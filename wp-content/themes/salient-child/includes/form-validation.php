<?php

// ---------------------------------------------------------------------------
// Gravity Forms — UTM field merge
// ---------------------------------------------------------------------------

add_filter( 'gform_field_value_utm_merged', 'gf_utm_combined' );
function gf_utm_combined( $value ) {
  return 'Source: '       . ( $_COOKIE['utm_source']         ?? '' ) . ',' . "\r\n" .
         'Medium: '       . ( $_COOKIE['utm_medium']         ?? '' ) . ',' . "\r\n" .
         'Campaign: '     . ( $_COOKIE['utm_campaign']       ?? '' ) . ',' . "\r\n" .
         'Term: '         . ( $_COOKIE['utm_term']           ?? '' ) . ',' . "\r\n" .
         'Content: '      . ( $_COOKIE['utm_content']        ?? '' ) . ',' . "\r\n" .
         'Url: '          . ( $_COOKIE['handl_url']          ?? '' ) . ',' . "\r\n" .
         'Landing Page: ' . ( $_COOKIE['handl_landing_page'] ?? '' );
}

// Merge address fields on form 6
add_action( 'gform_pre_submission_6', 'pre_submission_merge_fields' );
function pre_submission_merge_fields( $form ) {
  $_POST['input_24'] = ( $_POST['input_15'] ? $_POST['input_15'] . ', ' : '' ) .
                       ( $_POST['input_16'] ? $_POST['input_16'] . ', ' : '' ) .
                       ( $_POST['input_17'] ? $_POST['input_17'] . ' '  : '' ) .
                       ( $_POST['input_18'] ? $_POST['input_18']        : '' );
}

// ---------------------------------------------------------------------------
// Gravity Forms — Email validation (blocklist + MX + spam extensions)
// ---------------------------------------------------------------------------

add_filter( 'gform_field_validation', function ( $result, $value, $form, $field ) {
  if ( $field->type !== 'email' || empty( $value ) ) {
    return $result;
  }

  $email = sanitize_email( $value );
  if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
    $result['is_valid'] = false;
    $result['message']  = 'Please enter a valid email address.';
    return $result;
  }

  $email_domain    = substr( strrchr( $email, '@' ), 1 );
  $email_extension = strtolower( pathinfo( $email_domain, PATHINFO_EXTENSION ) );

  // Download / refresh disposable-email blocklist (cached 24h)
  $blocklist_url = 'https://raw.githubusercontent.com/disposable-email-domains/disposable-email-domains/master/disposable_email_blocklist.conf';
  $local_file    = wp_upload_dir()['basedir'] . '/spam-domain-list.txt';

  if ( ! file_exists( $local_file ) || time() - filemtime( $local_file ) > DAY_IN_SECONDS ) {
    $contents = file_get_contents( $blocklist_url );
    if ( $contents ) {
      file_put_contents( $local_file, $contents );
    }
  }

  $blocklist = file_exists( $local_file )
    ? file( $local_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES )
    : array();

  $spam_extensions = array( 'xyz', 'top', 'club', 'win', 'online', 'vip', 'ga', 'ml', 'tk' );

  if ( in_array( $email_domain, $blocklist ) ) {
    $result['is_valid'] = false;
    $result['message']  = 'Emails from this domain are not allowed.';
    return $result;
  }

  if ( in_array( $email_extension, $spam_extensions ) ) {
    $result['is_valid'] = false;
    $result['message']  = 'Email addresses with this domain extension are not allowed.';
    return $result;
  }

  if ( ! checkdnsrr( $email_domain, 'MX' ) ) {
    $result['is_valid'] = false;
    $result['message']  = 'The email domain does not exist.';
    return $result;
  }

  return $result;
}, 10, 4 );

// ---------------------------------------------------------------------------
// Gravity Forms — Phone validation (fake numbers + SpamCalls.net API)
// ---------------------------------------------------------------------------

add_filter( 'gform_field_validation', function ( $result, $value, $form, $field ) {
  if ( $field->type !== 'phone' ) {
    return $result;
  }

  $fake_numbers = array(
    '0000000000', '1111111111', '1234567890', '5555555555', '9999999999',
    '1231231234', '2025550168', '8005551212', '8888888888', '9009009000',
    '9998887777', '8769991234',
  );

  $phone = preg_replace( '/\D/', '', $value );

  if ( in_array( $phone, $fake_numbers ) ) {
    $result['is_valid'] = false;
    $result['message']  = 'Invalid or fake phone number detected.';
    return $result;
  }

  $response = wp_remote_get( "https://www.spamcalls.net/api/search?number={$phone}" );
  if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( isset( $body['score'] ) && $body['score'] > 50 ) {
      $result['is_valid'] = false;
      $result['message']  = 'This phone number has been reported as spam.';
    }
  }

  return $result;
}, 10, 4 );

// ---------------------------------------------------------------------------
// VPN / proxy detection via ProxyCheck.io (cached per day)
// ---------------------------------------------------------------------------

if ( ! defined( 'PROXYCHECK_CACHE_FILE' ) ) {
  define( 'PROXYCHECK_CACHE_FILE', WP_CONTENT_DIR . '/proxycheck_cache.json' );
}

function get_cached_proxy_data( $ip, $api_key ) {
  $today = date( 'Y-m-d' );
  $cache = array();

  if ( file_exists( PROXYCHECK_CACHE_FILE ) ) {
    $content = file_get_contents( PROXYCHECK_CACHE_FILE );
    $cache   = json_decode( $content, true );
    if ( ! is_array( $cache ) || ! isset( $cache['date'] ) || $cache['date'] !== $today ) {
      $cache = array( 'date' => $today, 'responses' => array() );
    }
  } else {
    $cache = array( 'date' => $today, 'responses' => array() );
  }

  if ( isset( $cache['responses'][ $ip ] ) ) {
    return $cache['responses'][ $ip ];
  }

  $response = wp_remote_get( "https://proxycheck.io/v2/{$ip}?key={$api_key}&vpn=1" );
  if ( is_wp_error( $response ) ) {
    return false;
  }

  $data   = json_decode( wp_remote_retrieve_body( $response ), true );
  $result = isset( $data[ $ip ] ) ? $data[ $ip ] : $data;

  $cache['responses'][ $ip ] = $result;
  file_put_contents( PROXYCHECK_CACHE_FILE, json_encode( $cache ) );

  return $result;
}

function is_vpn_ip( $ip ) {
  $api_key = '160301-54338s-0567zj-559n82';
  $result  = get_cached_proxy_data( $ip, $api_key );
  return ( $result && isset( $result['proxy'] ) && $result['proxy'] === 'yes' );
}

add_filter( 'gform_validation', 'check_vpn_with_proxycheck' );
function check_vpn_with_proxycheck( $validation_result ) {
  $ip = $_SERVER['REMOTE_ADDR'];

  if ( is_vpn_ip( $ip ) ) {
    foreach ( $validation_result['form']['fields'] as &$field ) {
      if ( $field->type === 'text' ) {
        $field->failed_validation  = true;
        $field->validation_message = 'Please turn off your VPN and try again.';
        break;
      }
    }
    $validation_result['is_valid'] = false;
  }

  return $validation_result;
}
