<?php

/**
 * Register and render the Orzech finance calculator WPBakery element.
 */

add_action( 'vc_before_init', 'orzech_register_finance_calculator_element' );

function orzech_register_finance_calculator_element() {
	if ( ! function_exists( 'vc_map' ) ) {
		return;
	}

	vc_map(
		array(
			'name'        => __( 'Orzech Finance Calculator', 'salient-child' ),
			'base'        => 'orzech_finance_calculator',
			'description' => __( 'Interactive estimated HVAC payment calculator.', 'salient-child' ),
			'category'    => __( 'Orzech', 'salient-child' ),
			'icon'        => 'icon-wpb-ui-separator',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Default project cost', 'salient-child' ),
					'param_name'  => 'default_amount',
					'value'       => '3400',
					'description' => __( 'Amount shown when the calculator loads.', 'salient-child' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Minimum project cost', 'salient-child' ),
					'param_name' => 'minimum_amount',
					'value'      => '1000',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Maximum project cost', 'salient-child' ),
					'param_name' => 'maximum_amount',
					'value'      => '100000',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Slider increment', 'salient-child' ),
					'param_name' => 'amount_step',
					'value'      => '100',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Project cost presets', 'salient-child' ),
					'param_name'  => 'presets',
					'value'       => 'HVAC Repair|1000,Furnace/AC Replacement|6000,Furnace + AC Combo|12000,Full System + Ductwork|18000',
					'description' => __( 'Comma-separated Label|Amount pairs. Leave blank to hide presets.', 'salient-child' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Example APR', 'salient-child' ),
					'param_name'  => 'apr',
					'value'       => '9.99',
					'description' => __( 'Annual percentage rate used for illustrative estimates.', 'salient-child' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Amortization terms', 'salient-child' ),
					'param_name'  => 'terms',
					'value'       => '12,24,36,48,60,72,84,96,108,120,132,144,156,168,180,240',
					'description' => __( 'Comma-separated number of months.', 'salient-child' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Default amortization', 'salient-child' ),
					'param_name' => 'default_term',
					'value'      => '180',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Apply button URL', 'salient-child' ),
					'param_name' => 'apply_url',
					'value'      => 'https://www.financeit.ca/en/direct/payment-plan/YT0yNzc0NzUmbD0mcD1lOTJfTjBCLWsxMjdVbWhHZE16c25BJnM9MCZ2PTE=/apply?slug=Z0kYPw',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Extra CSS class', 'salient-child' ),
					'param_name' => 'el_class',
				),
			),
		)
	);
}

add_shortcode( 'orzech_finance_calculator', 'orzech_render_finance_calculator' );

function orzech_render_finance_calculator( $atts ) {
	$atts = shortcode_atts(
		array(
			'default_amount' => '3400',
			'minimum_amount' => '1000',
			'maximum_amount' => '100000',
			'amount_step'     => '100',
			'presets'         => 'HVAC Repair|1000,Furnace/AC Replacement|6000,Furnace + AC Combo|12000,Full System + Ductwork|18000',
			'apr'             => '9.99',
			'terms'           => '12,24,36,48,60,72,84,96,108,120,132,144,156,168,180,240',
			'default_term'    => '180',
			'apply_url'       => 'https://www.financeit.ca/en/direct/payment-plan/YT0yNzc0NzUmbD0mcD1lOTJfTjBCLWsxMjdVbWhHZE16c25BJnM9MCZ2PTE=/apply?slug=Z0kYPw',
			'el_class'        => '',
		),
		$atts,
		'orzech_finance_calculator'
	);

	$minimum_amount = max( 1, (float) $atts['minimum_amount'] );
	$maximum_amount = max( $minimum_amount, (float) $atts['maximum_amount'] );
	$amount_step     = max( 1, (float) $atts['amount_step'] );
	$default_amount  = min( $maximum_amount, max( $minimum_amount, (float) $atts['default_amount'] ) );
	$apr             = max( 0, (float) $atts['apr'] );
	$terms           = array_values(
		array_unique(
			array_filter(
				array_map( 'absint', explode( ',', $atts['terms'] ) )
			)
		)
	);
	$presets         = array();

	foreach ( explode( ',', $atts['presets'] ) as $preset ) {
		$parts  = array_map( 'trim', explode( '|', $preset, 2 ) );
		$label  = isset( $parts[0] ) ? sanitize_text_field( $parts[0] ) : '';
		$amount = isset( $parts[1] ) ? (float) $parts[1] : 0;

		if ( '' !== $label && $amount >= $minimum_amount && $amount <= $maximum_amount ) {
			$presets[] = array(
				'label'  => $label,
				'amount' => $amount,
			);
		}
	}

	if ( empty( $terms ) ) {
		$terms = array( 120 );
	}

	$default_term = absint( $atts['default_term'] );
	if ( ! in_array( $default_term, $terms, true ) ) {
		$default_term = $terms[0];
	}

	$monthly_rate   = $apr / 100 / 12;
	$monthly_payment = 0.0 === $monthly_rate
		? $default_amount / $default_term
		: $default_amount * $monthly_rate / ( 1 - pow( 1 + $monthly_rate, -$default_term ) );
	$daily_payment  = $monthly_payment * 12 / 365;
	$instance_id    = wp_unique_id( 'orzech-finance-calculator-' );
	$classes        = trim( 'orzech-finance-calculator ' . sanitize_html_class( $atts['el_class'] ) );
	$script_path    = get_stylesheet_directory() . '/assets/js/main.js';

	wp_enqueue_script(
		'orzech-finance-calculator',
		get_stylesheet_directory_uri() . '/assets/js/main.js',
		array(),
		file_exists( $script_path ) ? filemtime( $script_path ) : ORZECH_VERSION,
		true
	);

	ob_start();
	?>
	<div
		class="<?php echo esc_attr( $classes ); ?>"
		data-finance-calculator
		data-apr="<?php echo esc_attr( $apr ); ?>"
	>
		<section class="orzech-finance-calculator__controls" aria-labelledby="<?php echo esc_attr( $instance_id ); ?>-title">
			<header class="orzech-finance-calculator__header">
				<p class="orzech-finance-calculator__eyebrow">Payment Calculator</p>
				<h2 class="orzech-finance-calculator__title" id="<?php echo esc_attr( $instance_id ); ?>-title">Build your payment.</h2>
			</header>

			<div class="orzech-finance-calculator__amount-block">
				<label for="<?php echo esc_attr( $instance_id ); ?>-amount">Estimated project cost</label>
				<p class="orzech-finance-calculator__amount"><span data-finance-amount>$<?php echo esc_html( number_format_i18n( $default_amount, 0 ) ); ?></span> <small>CAD</small></p>
			</div>

			<input
				class="orzech-finance-calculator__range"
				id="<?php echo esc_attr( $instance_id ); ?>-amount"
				type="range"
				min="<?php echo esc_attr( $minimum_amount ); ?>"
				max="<?php echo esc_attr( $maximum_amount ); ?>"
				step="<?php echo esc_attr( $amount_step ); ?>"
				value="<?php echo esc_attr( $default_amount ); ?>"
				data-finance-range
			>

			<?php if ( ! empty( $presets ) ) : ?>
				<div class="orzech-finance-calculator__presets" aria-label="Common project cost estimates">
					<?php foreach ( $presets as $preset ) : ?>
						<button type="button" data-finance-preset="<?php echo esc_attr( $preset['amount'] ); ?>" aria-pressed="false">
							<span><?php echo esc_html( $preset['label'] ); ?></span>
							<strong>$<?php echo esc_html( number_format_i18n( $preset['amount'], 0 ) ); ?></strong>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="orzech-finance-calculator__field">
				<label for="<?php echo esc_attr( $instance_id ); ?>-term">Amortization term:<sup>1</sup></label>
				<select id="<?php echo esc_attr( $instance_id ); ?>-term" data-finance-term>
					<?php foreach ( $terms as $term ) : ?>
						<option value="<?php echo esc_attr( $term ); ?>" <?php selected( $default_term, $term ); ?>><?php echo esc_html( $term ); ?> months</option>
					<?php endforeach; ?>
				</select>
			</div>

			<ul class="orzech-finance-calculator__benefits" aria-label="Financing benefits">
				<li><strong>Early Payoff</strong> - Pay off anytime without penalties</li>
				<li><strong>No Hidden Fees</strong> - Clear terms, no surprises along the way</li>
			</ul>
		</section>

		<section class="orzech-finance-calculator__summary" aria-label="Estimated payment summary" aria-live="polite">
			<div class="orzech-finance-calculator__results">
				<div>
					<p>Estimated daily payment</p>
					<p class="orzech-finance-calculator__daily"><strong data-finance-daily>$<?php echo esc_html( number_format_i18n( $daily_payment, 2 ) ); ?></strong><span>/day</span></p>
					<p class="orzech-finance-calculator__mobile-monthly">Roughly <strong data-finance-monthly>$<?php echo esc_html( number_format_i18n( $monthly_payment, 2 ) ); ?></strong><span>/month</span></p>
				</div>
				<hr>
				<div>
					<p><span class="orzech-finance-calculator__monthly-label-desktop">Roughly per month</span><span class="orzech-finance-calculator__monthly-label-mobile">Roughly</span></p>
					<p class="orzech-finance-calculator__monthly"><strong data-finance-monthly>$<?php echo esc_html( number_format_i18n( $monthly_payment, 2 ) ); ?></strong><span>/month</span></p>
				</div>
			</div>

			<div class="orzech-finance-calculator__actions">
				<div class="nectar-cta border_radius_1000px alignment_tablet_default alignment_phone_default display_tablet_inherit display_phone_inherit font_size_desktop_16px" data-color="extra-color-2" data-using-bg="true" data-style="basic" data-display="block" data-alignment="left" data-text-color="custom" style="--nectar-text-color: #FFFFFF; --nectar-button-color: var(--nectar-extra-color-2); --nectar-icon-gap: 10px;">
					<span style="color: #FFFFFF;" class="nectar-button-type">
						<span class="link_wrap" style="padding-top: 16px; padding-right: 32px; padding-bottom: 16px; padding-left: 32px;">
							<a target="_blank" rel="noopener noreferrer" class="link_text" role="button" href="<?php echo esc_url( $atts['apply_url'] ); ?>" aria-label="Opens in a new tab"><span class="text">Apply now</span><span class="screen-reader-text"> (opens in a new tab)</span></a>
						</span>
					</span>
				</div>
				<div class="orzech-finance-calculator__fine-print">
					<p>Pre-qualifying is a soft check. It will not affect your credit score.</p>
					<p>1 - Estimate at <?php echo esc_html( number_format_i18n( $apr, 2 ) ); ?>% APR on approved credit. Subject to change and may vary upon approval. For illustrative purposes only. Not a final offer.</p>
				</div>
			</div>
		</section>
	</div>
	<?php
	return ob_get_clean();
}
