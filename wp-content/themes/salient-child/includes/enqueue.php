<?php

add_action( 'wp_enqueue_scripts', 'salient_child_enqueue_styles', 100 );

function salient_child_enqueue_styles() {
  $nectar_theme_version = nectar_get_theme_version();

  // Google Fonts — Poppins & Roboto used throughout style.css
  wp_enqueue_style(
    'orzech-google-fonts',
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&family=Roboto:wght@500;700;900&display=swap',
    array(),
    null
  );

  // Main stylesheet (full salient-child CSS — preserved as-is)
  wp_enqueue_style(
    'orzech-child-style',
    get_stylesheet_directory_uri() . '/style.css',
    array(),
    $nectar_theme_version
  );

  // Compiled SCSS output — enqueued only when the file exists (after `gulp build`)
  $main_min = get_stylesheet_directory() . '/assets/css/style.min.css';
  if ( file_exists( $main_min ) ) {
    wp_enqueue_style(
      'orzech-main-style',
      get_stylesheet_directory_uri() . '/assets/css/style.min.css',
      array( 'orzech-child-style' ),
      filemtime( $main_min )
    );
  }

  // Accessibility & hero JS (from salient-child)
  wp_enqueue_script(
    'orzech-custom-js',
    get_stylesheet_directory_uri() . '/custom.js',
    array( 'jquery' ),
    $nectar_theme_version,
    true
  );

  // Mobile signature pad on sales-form page
  if ( is_page( 'sales-form' ) ) {
    wp_enqueue_script(
      'touch-punch',
      'https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js',
      array( 'jquery', 'jquery-ui-core' ),
      '0.2.3',
      true
    );
  }

  // Inter font — maintenance-plan & lennox-ultimate-comfort-system pages
  if ( is_page( array( 'maintenance-plan', 'lennox-ultimate-comfort-system' ) ) ) {
    wp_enqueue_style(
      'orzech-inter-font',
      'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap',
      array(),
      null
    );
  }

  // Maintenance plan page — dedicated CSS
  if ( is_page( 'maintenance-plan' ) ) {
    $mp_css = get_stylesheet_directory() . '/css/maintenance-plan.css';
    wp_enqueue_style(
      'orzech-maintenance-plan',
      get_stylesheet_directory_uri() . '/css/maintenance-plan.css',
      array(),
      file_exists( $mp_css ) ? filemtime( $mp_css ) : '1.0.0'
    );
  }

  // Auto-load compiled page-specific CSS: create assets/scss/pages/{slug}.scss → it loads automatically
  if ( is_front_page() ) {
    $slug = 'home';
  } elseif ( is_singular( 'page' ) ) {
    $slug = get_post_field( 'post_name', get_the_ID() );
  } else {
    $slug = null;
  }

  if ( $slug ) {
    $page_css = get_stylesheet_directory() . "/assets/css/{$slug}.min.css";
    if ( file_exists( $page_css ) ) {
      wp_enqueue_style(
        "orzech-{$slug}-style",
        get_stylesheet_directory_uri() . "/assets/css/{$slug}.min.css",
        array(),
        filemtime( $page_css )
      );
    }
  }

  // Blog index and single posts
  if ( is_home() || is_singular( 'post' ) ) {
    $blog_css = get_stylesheet_directory() . '/assets/css/blog.min.css';
    if ( file_exists( $blog_css ) ) {
      wp_enqueue_style(
        'orzech-blog-style',
        get_stylesheet_directory_uri() . '/assets/css/blog.min.css',
        array(),
        filemtime( $blog_css )
      );
    }
  }

  if ( is_rtl() ) {
    wp_enqueue_style( 'salient-rtl', get_template_directory_uri() . '/rtl.css', array(), '1', 'screen' );
  }
}

// Remove cf-edge-cache header on sales-form page
add_action( 'shutdown', function () {
  if ( is_page( 'sales-form' ) ) {
    header_remove( 'cf-edge-cache' );
  }
} );
