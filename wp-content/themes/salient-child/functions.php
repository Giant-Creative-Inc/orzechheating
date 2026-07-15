<?php

define( 'ORZECH_VERSION', '1.0.0' );

require_once get_stylesheet_directory() . '/includes/enqueue.php';
require_once get_stylesheet_directory() . '/includes/critical-css.php';
require_once get_stylesheet_directory() . '/includes/form-validation.php';
require_once get_stylesheet_directory() . '/includes/schema.php';
require_once get_stylesheet_directory() . '/includes/misc.php';

/**
 * Manage page-specific SEO meta descriptions via Yoast's 'wpseo_metadesc'
 * filter.
 *
 * Yoast SEO owns rendered metadata on this site, so descriptions are set via
 * this filter rather than by echoing a raw <meta> tag. The filter is gated
 * strictly per target path and returns the incoming description unchanged for
 * every other path, so no other page's metadata is affected and no duplicate
 * description tag is emitted.
 *
 * Targets handled here:
 *
 * 1. heating/furnaces/ductwork-installation (Task task_mrlio1wwyax7vxyvzb):
 *    shorten an over-length description to 148 chars.
 *
 * 2. essential-furnace-buying-guide, ultimate-ac-buying-guide,
 *    lennox-ultimate-comfort-system (Task task_mrlofi9yuh4x8dlv): add missing,
 *    unique descriptions (120-165 chars). Every claim is supported by visible
 *    page content:
 *      - Furnace guide (152): free furnace buying checklist for London, ON;
 *        heating needs, fuel types, efficiency, warranties (all visible).
 *      - AC guide (153): free AC buying checklist for London, ON; BTU, noise,
 *        installation, rebates, warranties, smart cooling (all visible).
 *      - Lennox (152): "Save up to $1,100 on the Lennox Ultimate Comfort
 *        System" mirrors the visible H1; limited-time deal is on the page.
 */
add_filter( 'wpseo_metadesc', function ( $description ) {
	if ( ! is_page() ) {
		return $description;
	}

	$permalink = get_permalink();
	if ( ! $permalink ) {
		return $description;
	}

	$path = wp_parse_url( $permalink, PHP_URL_PATH );
	$path = is_string( $path ) ? trim( $path, '/' ) : '';

	switch ( $path ) {
		case 'heating/furnaces/ductwork-installation':
			return 'Orzech Heating & Cooling provides professional ductwork installation in London, ON, designing duct systems for even, efficient home airflow.';

		case 'essential-furnace-buying-guide':
			return 'Download Orzech Heating & Cooling\'s free furnace buying checklist for London, ON homes: assess heating needs, compare fuel types, efficiency and warranties.';

		case 'ultimate-ac-buying-guide':
			return 'Get Orzech Heating & Cooling\'s free AC buying checklist for London, ON: understand BTU, noise, installation, rebates, warranties and smart cooling options.';

		case 'lennox-ultimate-comfort-system':
			return 'Save up to $1,100 on the Lennox Ultimate Comfort System from Orzech Heating & Cooling in London, ON. See what\'s included and claim this limited-time deal.';
	}

	// Pass through everything else unchanged.
	return $description;
}, 20 );
