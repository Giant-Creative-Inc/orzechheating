<?php
/**
 * Orzech child theme functions.
 *
 * NOTE FOR REVIEWER: If functions.php already exists in the child theme,
 * merge only the require_once line below into the existing file rather than
 * replacing it. Confirm the child theme directory slug matches 'orzech-child'.
 *
 * @package Orzech_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Enqueue parent + child theme styles.
add_action(
	'wp_enqueue_scripts',
	function () {
		$parent = get_template_directory_uri() . '/style.css';
		wp_enqueue_style( 'orzech-parent-style', $parent, array(), null );
		wp_enqueue_style(
			'orzech-child-style',
			get_stylesheet_uri(),
			array( 'orzech-parent-style' ),
			wp_get_theme()->get( 'Version' )
		);
	}
);

// Serve /llms.txt as plain text (GridPane/WordPress hybrid fallback).
require_once get_stylesheet_directory() . '/inc/llms-txt-route.php';
