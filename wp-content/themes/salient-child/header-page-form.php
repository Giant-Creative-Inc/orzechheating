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
<style>

.floating-form {
  overflow: auto;
  max-height: 100vh;
}

.floating-form .tool-tip {
    position: fixed;
    top: 103px;
    right: 340px;
    z-index: 999;
    background:#163d67;
    display: inline-flex;
    padding: 18px;
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    color: #fff;
    z-index: 9999999999;
    transition: all 0.5s ease;
    opacity: 0;
    transform: translateX(100%);
    z-index: -1 !important;
  }
  .floating-form .tool-tip.show {
    opacity: 1;
    transform: translateX(0%);
    z-index: 99 !important;
  }
  .floating-form .tool-tip:after {
    content: '';
    position: absolute;
    top: 50%;
    left: 99%;
    width: 0px;
    height: 0px;
    border-style: solid;
    border-width: 12.5px 0 12.5px 13px;
    border-color: transparent transparent transparent #163d67;
    transform: rotate(0deg) translateY(-50%);
  }
  .floating-form .tool-tip .h6 {
    font-weight: 600 !important;
    font-size: 16px !important;
    padding-bottom: 0!important;
    white-space: nowrap;
  }
.page-side-form .title-with-line {
  display: none;
}
.page-side-form .gform-theme--foundation .gform_fields {
  gap: 16px;
}
@media screen and (max-width: 1249px) {
  .page-side-form .form-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: rgba(1, 22, 29, 0.80);
    backdrop-filter: blur(11.4px);
    z-index: 999998; /* One layer below the form */
    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
  }
  .page-side-form .form-overlay.is-open {
    display: block;
  }
  .page-side-form aside {
    position: fixed;
    right: 0;
    top: 0;
    width: 320px; /* Adjust width as needed */
    height: 100vh;
    background: #ffffff; /* Example background */
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    z-index: 999999;
  }
  .page-side-form aside.is-open {
    transform: translateX(0);
  }
  .page-side-form .close-btn {
    position: absolute;
    top: 0px;
    right: 0;
    font-size: 40px;
    cursor: pointer;
    background: none;
    border: none;
    color: #01161D;
  }
  #openFloatingForm {
    background: #E31837;
    border: 1px solid #E31837;
    padding: 12px 10px 10px;
    width: 100%;
    font-family: "Poppins" !important;
    box-shadow: 0px 4px 11px 0px rgba(0, 0, 0, 0.17);
    color: #FFF;
    text-align: center;
    font-size: 16px;
    font-style: normal;
    font-weight: 600;
    text-transform: uppercase;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 99;
    border-radius: 0 !important;
  }
}
@media screen and (min-width: 1230px) {
  .page-side-form {
    display: flex;
  }
  .page-side-form  #header-outer {
    right: 320px !important;
    width:  auto !important;
  }
  .page-side-form  .form-overlay,
  .page-side-form  .close-btn {
    display: none;
  }
  .page-side-form  .main-content-form-page {
    overflow-x: hidden;
    width: calc(100% - 320px);
  }
  .page-side-form  aside {
    max-height: 100vh;
    display: block !important;
    transform: translateX(0%) !important;
    z-index: 1;
    width: 320px;
    position: fixed;
    top: 0;
    right: 0;
    border-left: 1px solid #1C1C1C;
    height: 100%;
    background: #FFF;
  }
}

.page-side-form .gform_wrapper {
  display: block !important;
  padding: 18px;
  background: #fff;
}
.page-side-form aside .wpb_text_column.wpb_content_element {
    border-left: 1px solid #1C1C1C;
    background: #E31837;
    color: #FFF;
    text-align: center;
    font-family: Roboto;
    font-size: 28px;
    font-style: normal;
    font-weight: 700;
    line-height: 1; /* 106.25% */
    text-transform: uppercase;
    padding: 35px 20px;
    margin: 0;
}
.page-side-form aside .wpb_text_column.wpb_content_element  p {
  padding-bottom: 0 !important;
}
.page-side-form aside p.gform_required_legend {
    display:  none;
}
.page-side-form aside h3.gsection_title {
    display: none;
}
.page-side-form aside .gform_wrapper input[type=color], .gform_wrapper input[type=date], .gform_wrapper input[type=datetime-local], .gform_wrapper input[type=datetime], .gform_wrapper input[type=email], .gform_wrapper input[type=month], .gform_wrapper input[type=number], .gform_wrapper input[type=password], .gform_wrapper input[type=search], .gform_wrapper input[type=tel], .gform_wrapper input[type=text], .gform_wrapper input[type=time], .gform_wrapper input[type=url], .gform_wrapper input[type=week], .gform_wrapper select, .gform_wrapper textarea {
    font-size: 15px;
    border-width: .5px;
    width: 100%;
    font-family: 'Poppins' !important;
    border-radius: 7.5px;
    border: 1px solid #5B5B5B;
    background-color: transparent;
    font-size: 14px;
    padding: 5px 24px !important;
    block-size:  auto !important;
}
.page-side-form aside .gform_wrapper .gfield-choice-input+label {
    color: #1C1C1C;
    font-family: Poppins;
    font-size: 14px;
    font-style: normal;
    font-weight: 400;
    line-height: 20px;
    margin-left: 6px;
}
.page-side-form aside .gform_wrapper .gfield_checkbox {
    display: flex !important;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px !important;
    flex-direction: row !important;
    margin-top: 5px;
}
.page-side-form aside .gform_wrapper .top_label .gfield_label {
  color: #1C1C1C;
  font-family: Roboto;
  font-size: 16px;
  font-style: normal;
  font-weight: 500;
  margin-top: 0 !important;
  margin-bottom: 3px !important;
}
.page-side-form aside .gform-theme--foundation .gfield textarea.small {
    min-block-size: 120px !important;
    block-size: 120px !important;
}
.page-side-form aside label.gform-field-label.gform-field-label--type-inline.gfield_consent_label {
    color: #1C1C1C;
    font-size: 14px;
    font-style: normal; 
    font-weight: 400;
    line-height: 1.4;
    display: inline-block;
}
.page-side-form aside .ginput_container.ginput_container_consent {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.page-side-form aside input#gform_submit_button_9 {
  border-radius: 12.04px;
  background: #E31837;
  border: 1px solid #E31837;
  padding: 16px 10px;
  width: 100%;
  font-family: "Poppins" !important;
  box-shadow: 0px 4px 11px 0px rgba(0, 0, 0, 0.17);
  color: #FFF;
  text-align: center;
  font-size: 20px;
  font-style: normal;
  font-weight: 400;
  margin: 8px 0 0;
  text-transform: uppercase;
}
.page-side-form aside input#gform_submit_button_9:hover {
  background: #fff;
  color: #E31837;
}
.page-side-form aside .gform-theme--foundation .gfield textarea.small {
  min-block-size: 45px !important;
  block-size: 45px !important;
  resize: vertical;
  box-sizing: border-box;
  overflow: auto;
}
.page-side-form aside .gform-theme--foundation .gfield textarea.small {
  min-block-size: 120px !important;
  block-size: 120px !important;
}
.page-side-form aside .gform-theme--foundation .gform_footer, .page-side-form aside  .gform-theme--foundation .gform_page_footer {
  margin-block-start: 0px !important;
}
.page-side-form aside .gform-theme--framework .gform_validation_errors .gform_submission_error {
  font-size: 14px !important;
}
.page-side-form aside .gform-theme--framework .gform_validation_errors {
  margin-bottom: 12px !important;
}
@media screen and (min-width: 1800px) {
  .page-side-form aside .gform_wrapper input[type=color], .gform_wrapper input[type=date], .gform_wrapper input[type=datetime-local], .gform_wrapper input[type=datetime], .gform_wrapper input[type=email], .gform_wrapper input[type=month], .gform_wrapper input[type=number], .gform_wrapper input[type=password], .gform_wrapper input[type=search], .gform_wrapper input[type=tel], .gform_wrapper input[type=text], .gform_wrapper input[type=time], .gform_wrapper input[type=url], .gform_wrapper input[type=week], .gform_wrapper select, .gform_wrapper textarea {
    font-size: 15px;
    padding: 6px 24px !important;
  }
  .page-side-form .gform_wrapper {
    padding: 24px;
  }
  .page-side-form .gform-theme--foundation .gform_fields {
    gap: 24px;
  }
}
@media screen and (max-height: 899px) {
  .page-side-form aside .gform-theme--foundation .gfield textarea.small {
    min-block-size: 50px !important;
    block-size: 50px !important;
  }
  .page-side-form aside .wpb_text_column.wpb_content_element {
    font-size: 24px;
    padding: 16px;
  }
  .page-side-form aside input#gform_submit_button_9 {
    padding: 14px 10px;
    font-size: 18px;
  }
}
@media screen and (max-height: 700px) {
  .page-side-form aside .gform-theme--foundation .gfield textarea.small {
    min-block-size: 50px !important;
    block-size: 50px !important;
  }
  .page-side-form aside .wpb_text_column.wpb_content_element {
    font-size: 20px;
    padding: 14px;
  }
  .page-side-form aside input#gform_submit_button_9 {
    padding: 12px 10px;
    font-size: 16px;
  }
  .page-side-form .gform_wrapper {
    padding: 12px 14px;
  }
}
@media screen and (min-height: 860px) {
  .page-side-form .gform-theme--foundation .gform_fields {
    gap: 24px;
  }
}

</style>
</head><?php

$nectar_header_options = nectar_get_header_variables();

?><body <?php body_class(); ?> <?php nectar_body_attributes(); ?>>
	
<div class="page-side-form">
<div class="main-content-form-page">
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
