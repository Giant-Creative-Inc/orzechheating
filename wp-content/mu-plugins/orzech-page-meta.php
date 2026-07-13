<?php
/**
 * Plugin Name: Orzech Page Meta (Task task_mrjt9sllf7zxc1zfww)
 * Description: Adds titles and meta descriptions to key commercial pages that are missing them. Defers to an active SEO plugin (Yoast/RankMath) to avoid duplicate meta tags; only fills gaps otherwise.
 * Author: GIANT Agent Factory
 * Version: 1.0.0
 *
 * Reviewed via draft PR; deploys to staging first. Production requires human approval.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slug => meta map for the affected commercial pages.
 * Keys are matched against the request path (trailing slashes ignored).
 */
function orzech_page_meta_map() {
	return array(
		'essential-furnace-buying-guide' => array(
			'title'       => 'Furnace Buying Guide (London, ON) | Orzech Heating & Cooling',
			'description' => 'Choosing a new furnace in London, ON? Compare furnace types, efficiency ratings, sizing and installation costs in our essential furnace buying guide from Orzech Heating & Cooling.',
		),
		'ultimate-ac-buying-guide'       => array(
			'title'       => 'Air Conditioner Buying Guide | Orzech Heating & Cooling London, ON',
			'description' => 'Plan your new AC with confidence. Our ultimate air conditioner buying guide covers SEER ratings, sizing, energy savings and installation for London, ON homeowners.',
		),
		'lennox-ultimate-comfort-system' => array(
			'title'       => 'Lennox Ultimate Comfort System | Orzech Heating & Cooling',
			'description' => 'Discover the Lennox Ultimate Comfort System for whole-home comfort and efficiency. Expert installation across London, ON from Orzech Heating & Cooling.',
		),
	);
}

/**
 * Determine which mapped page (if any) matches the current request.
 *
 * @return array|null
 */
function orzech_current_page_meta() {
	$map = orzech_page_meta_map();

	$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	$path = trim( (string) $path, '/' );

	// Also try to match by queried object slug for reliability.
	$slug = '';
	if ( is_page() || is_singular() ) {
		$obj = get_queried_object();
		if ( $obj && isset( $obj->post_name ) ) {
			$slug = $obj->post_name;
		}
	}

	foreach ( $map as $key => $meta ) {
		if ( $key === $slug || $key === $path || ( '' !== $path && substr( $path, -strlen( $key ) ) === $key ) ) {
			return $meta;
		}
	}

	return null;
}

/**
 * Is a known SEO plugin active and handling meta output?
 */
function orzech_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' );
}

/**
 * Override the document title for mapped pages.
 */
add_filter(
	'pre_get_document_title',
	function ( $title ) {
		if ( orzech_seo_plugin_active() ) {
			return $title; // Let the SEO plugin own titles.
		}
		$meta = orzech_current_page_meta();
		if ( $meta && ! empty( $meta['title'] ) ) {
			return $meta['title'];
		}
		return $title;
	},
	20
);

/**
 * Print the meta description into the head for mapped pages.
 * Skipped when an SEO plugin is active to prevent duplicate tags.
 */
add_action(
	'wp_head',
	function () {
		if ( orzech_seo_plugin_active() ) {
			return;
		}
		$meta = orzech_current_page_meta();
		if ( $meta && ! empty( $meta['description'] ) ) {
			echo '<meta name="description" content="' . esc_attr( $meta['description'] ) . '" />' . "\n";
		}
	},
	1
);

/**
 * If an SEO plugin IS active, feed it our descriptions only when it has none set,
 * so the value renders without creating duplicates.
 */
add_filter(
	'wpseo_metadesc',
	function ( $desc ) {
		if ( ! empty( $desc ) ) {
			return $desc;
		}
		$meta = orzech_current_page_meta();
		return ( $meta && ! empty( $meta['description'] ) ) ? $meta['description'] : $desc;
	},
	20
);

add_filter(
	'rank_math/frontend/description',
	function ( $desc ) {
		if ( ! empty( $desc ) ) {
			return $desc;
		}
		$meta = orzech_current_page_meta();
		return ( $meta && ! empty( $meta['description'] ) ) ? $meta['description'] : $desc;
	},
	20
);
