<?php
/**
 * Serve /llms.txt as plain text on WordPress/GridPane hybrid setups.
 *
 * If Nginx already serves the static root llms.txt file, this route is never
 * reached and is harmless. If WordPress intercepts the request, this returns
 * the raw file content with a text/plain content type and HTTP 200.
 *
 * @package Orzech_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detect a request for /llms.txt and stream the root llms.txt file.
 */
function orzech_serve_llms_txt() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path        = wp_parse_url( $request_uri, PHP_URL_PATH );

	if ( '/llms.txt' !== untrailingslashit( (string) $path ) ) {
		return;
	}

	$file = ABSPATH . 'llms.txt';

	if ( ! file_exists( $file ) || ! is_readable( $file ) ) {
		return; // Let WordPress handle it normally if the file is missing.
	}

	$contents = file_get_contents( $file );

	if ( false === $contents ) {
		return;
	}

	status_header( 200 );
	nocache_headers();
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo $contents; // Raw plain-text file; not HTML output.
	exit;
}
add_action( 'template_redirect', 'orzech_serve_llms_txt', 0 );
