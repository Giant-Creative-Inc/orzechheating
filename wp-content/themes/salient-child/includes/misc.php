<?php

// Make nav items with empty href accessible
add_filter( 'nav_menu_link_attributes', 'accessible_menu_items', 10, 4 );
function accessible_menu_items( $atts, $item, $args, $depth ) {
  $empty_href   = ( ! isset( $atts['href'] ) || $atts['href'] === '#' || $atts['href'] === '' );
  $has_children = ( is_array( $item->classes ) && in_array( 'menu-item-has-children', $item->classes ) );

  if ( $empty_href && $has_children ) {
    unset( $atts['href'] );
    $atts['aria-disabled'] = 'true';
    $atts['role']          = 'link';
  }

  return $atts;
}

// Remove author info from oEmbed responses
add_filter( 'oembed_response_data', 'disable_embeds_filter_oembed_response_data_' );
function disable_embeds_filter_oembed_response_data_( $data ) {
  unset( $data['author_url'] );
  unset( $data['author_name'] );
  return $data;
}

// Add .phone-click class to tel: links in nav
add_filter( 'nav_menu_link_attributes', 'orzech_phone_link_class', 10, 3 );
function orzech_phone_link_class( $atts, $item, $args ) {
  if ( ! empty( $atts['href'] ) && str_contains( $atts['href'], 'tel:' ) ) {
    $atts['class'] = 'phone-click';
  }
  return $atts;
}

// Mobile header phone number
add_action( 'nectar_hook_mobile_header_menu_items', 'display_phone_number_in_mobile_header', 10 );
function display_phone_number_in_mobile_header() {
  echo '<a href="tel:2267410054" class="mobile-menu-number phone-link"><i class="nectar-menu-icon fa fa-phone hide-open-mobile-menu" role="presentation"></i><span class="menu-title-text">226 741 0054</span></a>';
}
