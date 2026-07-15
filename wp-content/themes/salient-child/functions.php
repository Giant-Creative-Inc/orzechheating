<?php

define( 'ORZECH_VERSION', '1.0.0' );

require_once get_stylesheet_directory() . '/includes/enqueue.php';
require_once get_stylesheet_directory() . '/includes/critical-css.php';
require_once get_stylesheet_directory() . '/includes/form-validation.php';
require_once get_stylesheet_directory() . '/includes/schema.php';
require_once get_stylesheet_directory() . '/includes/misc.php';

/**
 * Shorten the meta description on /heating/furnaces/ductwork-installation/
 * (Task task_mrlio1wwyax7vxyvzb).
 *
 * Yoast SEO owns rendered metadata on this site, so the description is set via
 * the 'wpseo_metadesc' filter rather than by echoing a raw <meta> tag. The
 * filter is gated strictly to the single target page and returns the incoming
 * description unchanged for every other path, so no other page's metadata is
 * affected.
 *
 * Before/after record:
 *   BEFORE (169 chars, observed on staging + production):
 *     "Professional ductwork cleaning & installation in London Ontario. Remove
 *      dust, mold & improve airflow\u2014fast service, free inspections, energy
 *      savings. Orzech HVAC experts!"
 *   AFTER (148 chars):
 *     "Orzech Heating & Cooling provides professional ductwork installation in
 *      London, ON, designing duct systems for even, efficient home airflow."
 *
 * The revised copy is supported by the visible page (H1 "Ductwork Installation
 * London Ontario") and invents no offers, savings, speed, or guarantees.
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

	// Gate strictly to the single target page; pass through everything else.
	if ( 'heating/furnaces/ductwork-installation' !== $path ) {
		return $description;
	}

	return 'Orzech Heating & Cooling provides professional ductwork installation in London, ON, designing duct systems for even, efficient home airflow.';
}, 20 );
