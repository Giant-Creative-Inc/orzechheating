<?php
/**
 * Task: task_mrjt9sllf7zxc1zfww — Missing meta descriptions on key commercial pages
 *
 * Server-side title + meta description for three commercial pages so the
 * fields render in HTML and are verifiable by a crawler / QA.
 *
 * SAFETY / ENABLEMENT NOTES (human review required before enabling):
 *  - Confirm the WP page slugs below match staging.
 *  - If an SEO plugin (Yoast/Rank Math/AIOSEO) already manages meta output,
 *    DO NOT enable this file; enter the values in the plugin fields instead
 *    to avoid duplicate <meta description> tags.
 *  - Load via the child theme functions include on the task branch only.
 *
 * Placement: wp-content/themes/orzech-child/inc/seo-meta-descriptions.php
 * and require_once it from the child theme functions file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slug => [ title, description ] map.
 */
function orzech_task_seo_meta_map() {
	return array(
		'essential-furnace-buying-guide' => array(
			'title'       => 'Furnace Buying Guide (London, ON) | Orzech Heating & Cooling',
			'description' => "Choosing a new furnace in London, ON? Compare efficiency, sizing, and cost with Orzech Heating & Cooling's essential furnace buying guide.",
		),
		'ultimate-ac-buying-guide' => array(
			'title'       => 'Air Conditioner Buying Guide (London, ON) | Orzech Heating & Cooling',
			'description' => "Not sure which AC to buy? Orzech Heating & Cooling's ultimate air conditioner buying guide covers sizing, SEER ratings, and pricing in London, ON.",
		),
		'lennox-ultimate-comfort-system' => array(
			'title'       => 'Lennox Ultimate Comfort System | Orzech Heating & Cooling',
			'description' => 'Discover the Lennox Ultimate Comfort System with Orzech Heating & Cooling in London, ON. Efficient, quiet heating and cooling for year-round comfort.',
		),
	);
}

/**
 * Return the meta entry for the currently rendered page, or null.
 */
function orzech_task_current_seo_entry() {
	if ( ! is_page() ) {
		return null;
	}

	$post = get_queried_object();
	if ( ! $post || empty( $post->post_name ) ) {
		return null;
	}

	$map  = orzech_task_seo_meta_map();
	$slug = $post->post_name;

	return isset( $map[ $slug ] ) ? $map[ $slug ] : null;
}

/**
 * Output the meta description in <head>.
 */
function orzech_task_output_meta_description() {
	$entry = orzech_task_current_seo_entry();
	if ( null === $entry ) {
		return;
	}

	printf(
		'<meta name="description" content="%s" />' . "\n",
		esc_attr( $entry['description'] )
	);
}
add_action( 'wp_head', 'orzech_task_output_meta_description', 1 );

/**
 * Override the document title for the mapped pages.
 */
function orzech_task_filter_title_parts( $parts ) {
	$entry = orzech_task_current_seo_entry();
	if ( null === $entry ) {
		return $parts;
	}

	$parts['title'] = $entry['title'];
	unset( $parts['tagline'] );

	return $parts;
}
add_filter( 'document_title_parts', 'orzech_task_filter_title_parts', 20 );
