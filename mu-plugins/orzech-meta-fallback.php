<?php
/**
 * Plugin Name: Orzech Meta Description Fallback
 * Description: Outputs an escaped meta description for specific commercial pages that are missing one. Intended as a staging fallback until SEO plugin fields are set. Skips output if a meta description already exists via an SEO plugin filter.
 * Version: 1.0.0
 * Author: GIANT Agent Factory (draft for human review)
 *
 * Safe by design: only emits an escaped <meta name="description"> tag on a fixed
 * allowlist of request paths. Does nothing on any other URL. Contains no secrets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowlist of path => meta description.
 * Paths are matched against the trimmed request path (no leading/trailing slash).
 */
function orzech_meta_fallback_map() {
	return array(
		'essential-furnace-buying-guide' => 'Compare furnace types, efficiency ratings and sizing before you buy. Orzech Heating & Cooling\'s furnace buying guide for London, ON homeowners.',
		'ultimate-ac-buying-guide'       => 'Choosing a new air conditioner? Learn about SEER ratings, sizing and costs in Orzech Heating & Cooling\'s AC buying guide for London, ON homes.',
		'lennox-ultimate-comfort-system' => 'Discover the Lennox Ultimate Comfort System for quiet, efficient home comfort. Installed and serviced by Orzech Heating & Cooling in London, ON.',
	);
}

function orzech_meta_fallback_current_path() {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path = parse_url( $uri, PHP_URL_PATH );
	if ( ! is_string( $path ) ) {
		return '';
	}
	return trim( $path, '/' );
}

add_action( 'wp_head', function () {
	// Do not run in admin or feeds.
	if ( is_admin() || is_feed() ) {
		return;
	}

	$map  = orzech_meta_fallback_map();
	$path = orzech_meta_fallback_current_path();

	if ( '' === $path || ! isset( $map[ $path ] ) ) {
		return;
	}

	$description = $map[ $path ];

	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}, 1 );
