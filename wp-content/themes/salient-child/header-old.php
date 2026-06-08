<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php
	
	$nectar_options = get_nectar_theme_options();
	
	nectar_meta_viewport();
	
	// Shortcut icon fallback.
	if ( ! empty( $nectar_options['favicon'] ) && ! empty( $nectar_options['favicon']['url'] ) ) {
		echo '<link rel="shortcut icon" href="'. esc_url( nectar_options_img( $nectar_options['favicon'] ) ) .'" />';
	}
	
	wp_head();
	
?>
</head><?php

$nectar_header_options = nectar_get_header_variables();

?><body <?php body_class(); ?> <?php nectar_body_attributes(); ?>>
	
	<?php
	
	nectar_hook_after_body_open();
	
	nectar_hook_before_header_nav();
	
	// Boxed theme option opening div.
	if ( $nectar_header_options['n_boxed_style'] ) {
		echo '<div id="boxed">';
	}
	
	get_template_part( 'includes/partials/header/header-space' );
	
	?>
	<div id="header-outer" <?php nectar_header_nav_attributes(); ?>>
		<!--<div class="clean-home-initiativebanner" style="
			    padding: 10px;
    text-align: center;
    font-size: 14px !important;
    font-weight: 600;
    line-height: 1.4 !important;
    background: #fff;">
			<a href="https://orzechheating.ca/products/heat-pumps/" style="color: #2867b1;font-weight: 600;">Save up to $8,000 with the Home Efficiency Rebate on select heat pumps. Learn more.</a>
		</div>-->
		<?php
		
		get_template_part( 'includes/partials/header/secondary-navigation' );
		
		if ('ascend' !== $nectar_header_options['theme_skin'] && 
			  'left-header' !== $nectar_header_options['header_format']) {
			get_template_part( 'includes/header-search' );
		}
		
		get_template_part( 'includes/partials/header/header-menu' );
		
		
		?>
		
	</div>
	<?php
	
	if ( ! empty( $nectar_options['enable-cart'] ) && '1' === $nectar_options['enable-cart'] ) {
		get_template_part( 'includes/partials/header/woo-slide-in-cart' );
	}
	
	if ( 'ascend' === $nectar_header_options['theme_skin'] || 
		   'left-header' === $nectar_header_options['header_format'] && 
		   'false' !== $nectar_header_options['header_search'] ) {
		get_template_part( 'includes/header-search' ); 
	}
	
  get_template_part( 'includes/partials/footer/body-border' );
  
	?>
	<div id="ajax-content-wrap">
<?php
		
		nectar_hook_after_outer_wrap_open();
