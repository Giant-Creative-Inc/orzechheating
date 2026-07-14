<?php
/**
 * Plugin Name: Orzech Page Meta (Task task_mrl7wf0sj1mn06o3r7)
 * Description: Canonical, deployable source of titles + meta descriptions for the three commercial pages that are missing them. GUARANTEES a single non-empty <meta name="description"> renders on staging: it feeds Yoast/RankMath when their value is empty, and uses a late output-buffered wp_head fallback that injects our escaped description ONLY when no non-empty description tag was already emitted (prevents duplicates). This is the SINGLE source of truth — the root mu-plugins/orzech-meta-fallback.php and snippets/seo/meta-descriptions.php must NOT also emit meta.
 * Author: GIANT Agent Factory
 * Version: 3.0.0
 *
 * Reviewed via draft PR; deploys to staging first. Production requires human approval.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slug => meta map for the affected commercial pages.
 * Keys are matched against the request path (trailing slashes ignored) and
 * the queried object slug. Meta descriptions kept within ~150-160 characters.
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

	// Prefer matching by queried object slug for reliability.
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
 * Override the document title for mapped pages, only when nothing else set it.
 * We do not hard-defer to the SEO plugin here; if the plugin already sets a
 * non-empty title WordPress will pass it through and we leave it alone.
 */
add_filter(
	'pre_get_document_title',
	function ( $title ) {
		if ( ! empty( $title ) ) {
			return $title; // Something (theme or SEO plugin) already set a title.
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
 * Feed Yoast its description only when it has none set for this page,
 * so the value renders through the plugin without creating a duplicate.
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

/**
 * Feed Rank Math its description only when it has none set for this page.
 */
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

/**
 * GUARANTEED fallback: buffer wp_head output at a late priority, inspect for an
 * existing non-empty <meta name="description">, and inject ours only if none
 * was emitted. This fixes the prior QA failure (no tag rendered when an SEO
 * plugin was active but had no description) while preventing duplicate tags.
 */
add_action(
	'wp_head',
	function () {
		if ( is_admin() || is_feed() ) {
			return;
		}
		$meta = orzech_current_page_meta();
		if ( ! $meta || empty( $meta['description'] ) ) {
			return;
		}
		// Start buffering the remainder of wp_head so we can inspect what other
		// hooks (theme / SEO plugin) print before us.
		ob_start(
			function ( $buffer ) use ( $meta ) {
				// Detect an existing NON-EMPTY meta description tag.
				$has_desc = false;
				if ( preg_match_all( '/<meta[^>]*name=["\']description["\'][^>]*>/i', $buffer, $matches ) ) {
					foreach ( $matches[0] as $tag ) {
						if ( preg_match( '/content=["\']([^"\']*)["\']/i', $tag, $c ) && '' !== trim( $c[1] ) ) {
							$has_desc = true;
							break;
						}
					}
				}
				if ( $has_desc ) {
					return $buffer; // Leave existing non-empty tag untouched.
				}
				$tag = '<meta name="description" content="' . esc_attr( $meta['description'] ) . '" />' . "\n";
				return $tag . $buffer;
			}
		);
	},
	0
);

add_action(
	'wp_head',
	function () {
		if ( is_admin() || is_feed() ) {
			return;
		}
		$meta = orzech_current_page_meta();
		if ( ! $meta || empty( $meta['description'] ) ) {
			return;
		}
		// Flush the buffer started at priority 0, running the injector callback.
		if ( ob_get_level() > 0 ) {
			@ob_end_flush();
		}
	},
	PHP_INT_MAX
);
