<?php
/**
* The template for displaying the footer.
*
* @package Salient WordPress Theme
* @version 12.2
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nectar_options = get_nectar_theme_options();
$header_format  = ( !empty($nectar_options['header_format']) ) ? $nectar_options['header_format'] : 'default';

nectar_hook_before_footer_open();

?>

<div id="footer-outer" <?php nectar_footer_attributes(); ?>>
	
	<?php
	
	nectar_hook_after_footer_open();
	
	get_template_part( 'includes/partials/footer/call-to-action' );
	
	get_template_part( 'includes/partials/footer/main-widgets' );
	
	get_template_part( 'includes/partials/footer/copyright-bar' );
	
	?>
	
</div><!--/footer-outer-->

<?php

nectar_hook_before_outer_wrap_close();

get_template_part( 'includes/partials/footer/off-canvas-navigation' );

?>

</div> <!--/ajax-content-wrap-->

<?php
	
	// Boxed theme option closing div.
	if ( ! empty( $nectar_options['boxed_layout'] ) && 
	'1' === $nectar_options['boxed_layout'] && 
	'left-header' !== $header_format ) {

		echo '</div><!--/boxed closing div-->'; 
	}
  ?>

  
<?php
	
	get_template_part( 'includes/partials/footer/back-to-top' );
	
	nectar_hook_after_wp_footer();
	nectar_hook_before_body_close();
?>
</div>
<div id="formOverlay" class="form-overlay" role="dialog" aria-label="Close form" aria-hidden="true"></div>
<aside id="floatingForm" class="hidden">
    <button id="closeFloatingForm" class="close-btn" aria-label="Close form">×</button>
    <div class="floating-form">
        <?php echo do_shortcode('[nectar_global_section id="4077"]'); ?>
        <!-- <div class="tool-tip"><p class="h6">Contact Us for a FREE Quote</p></div>
        <?php //echo do_shortcode('[gravityform id="9" title="false" description="false" ajax="true"]'); ?> -->
    </div>
</aside>
<button id="openFloatingForm" class="hidden">GET A QUOTE</button>
</div>
<?php
	wp_footer();
?>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const openBtn = document.getElementById("openFloatingForm");
    const closeBtn = document.getElementById("closeFloatingForm");
    const formOverlay = document.getElementById("formOverlay");
    const floatingForm = document.getElementById("floatingForm");
    const quoteLinks = document.querySelectorAll('a[href*="#get-a-quote"], button[href*="#get-a-quote"]');
    let initialInnerHeight = window.innerHeight;

    function checkScreenSize() {
      const currentHeight = window.innerHeight;

      // If height shrank but width stayed the same, likely keyboard opened — ignore
      const heightDiff = Math.abs(currentHeight - initialInnerHeight);
      if (heightDiff > 0 ) return;

      if (window.innerWidth >= 1250) {
        closeForm();
      }

      // Update initial height in case of real resize
      initialInnerHeight = currentHeight;
    }

    // Function to open the form
    function openForm(event) {
        if (event) event.preventDefault(); // Prevent default anchor behavior
        floatingForm.classList.add("is-open");
        formOverlay.classList.add("is-open");
        closeBtn.style.display = "block"; // Show close button
        openBtn.style.display = "none"; // Hide open button
        floatingForm.setAttribute("aria-hidden", "false");
        closeBtn.focus();
    }

    // Function to close the form
    function closeForm() {
        floatingForm.classList.remove("is-open");
        formOverlay.classList.remove("is-open");
        closeBtn.style.display = "none"; // Hide close button
        openBtn.style.display = "block"; // Show open button
        floatingForm.setAttribute("aria-hidden", "true");
        openBtn.focus();
    }

    openBtn.addEventListener("click", openForm);
    closeBtn.addEventListener("click", closeForm);
    // Only close when clicking outside the form
    formOverlay.addEventListener("click", function(event) {
      if (event.target === formOverlay) {
        closeForm();
      }
    });

    // Close with Escape key
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && floatingForm.classList.contains("is-open")) {
            closeForm();
        }
    });

    // Attach event listeners to all #get-a-quote links for mobile
    quoteLinks.forEach(link => {
        link.addEventListener("click", function (event) {
            if (window.innerWidth <= 1249) {
                openForm(event);
            } else {
                event.preventDefault();
                // Show tooltip for 5 seconds
                const toolTip = document.querySelector('.floating-form .tool-tip');
                if (toolTip) {
                    toolTip.classList.add('show');
                    setTimeout(() => {
                        toolTip.classList.remove('show');
                    }, 5000);
                }
                // Focus input field
                const inputField = document.getElementById('input_9_1');
                if (inputField) {
                    inputField.focus();
                }
            }
        });
    });

    // Run the screen size check on load and resize
    checkScreenSize();
    window.addEventListener("resize", checkScreenSize);
});

jQuery("select").on("change", function () {
  if (jQuery(this).val()) {
    return jQuery(this).addClass("has-val");
  } else {
    return jQuery(this).removeClass("has-val");
  }
});
jQuery(document).ready(function() {
  jQuery(function() {
    jQuery('.gfield--type-email input').keyup(function ()  {
      if(jQuery(".gfield--type-email input").val().length > 5) {
        var email_val = jQuery(this).val();
        let pattern = /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/
        if (email_val.match(pattern)) {
          jQuery('.gfield--type-email').removeClass("gfield_error");
          jQuery( "#custom_error" ).remove();
        } else {
          jQuery('.gfield--type-email').addClass("gfield_error");
          if(!jQuery( "#custom_error" ).length) {
            jQuery('<div id="custom_error" class="gfield_description validation_message gfield_validation_message">The email address entered is invalid, please check the formatting (e.g. email@domain.com).</div>').insertAfter( ".gfield--type-email > .ginput_container" );
          }
        }
      }
    })
    if(jQuery( ".gfield--type-phone input" ).length) {
      console.log("hi")
      jQuery(".gfield--type-phone input").attr('maxlength','14');
    }
  });
  if ( jQuery( '.gform_wrapper' ).length ) {
    //GRAVITY FORMS - MATERIAL DSIGN - ADD FOCUS TO LABEL
    jQuery(document).on("focusin", 'form input, form textarea, form select', function() {
      jQuery(this).parent().siblings('label').addClass('focused');
    });

    jQuery(document).on("focusout", 'form input, form textarea, form select', function() {
      var input = jQuery(this);
      if( input.val().length === 0 ){
        input.parent().siblings('label').removeClass('focused');
      }
    });
    jQuery('.gform_wrapper input, .gform_wrapper textarea, .gform_wrapper select').each(function(){
      if( jQuery(this).val().length !== 0 ){
        jQuery(this).parent().siblings('label').addClass('focused');
      }
    });
  }
});
</script>
</style>
</body>
</html>