<?php

// Make nav items with empty href accessible
add_filter( 'nav_menu_link_attributes', 'accessible_menu_items', 10, 4 );
function accessible_menu_items( $atts, $item, $args, $depth ) {
  $empty_href   = ( ! isset( $atts['href'] ) || $atts['href'] === '#' || $atts['href'] === '' );
  $has_children = ( is_array( $item->classes ) && in_array( 'menu-item-has-children', $item->classes ) );

  if ( $empty_href && $has_children ) {
    unset( $atts['href'] );
    $atts['aria-disabled'] = 'true';
    $atts['role']          = 'link';
  }

  return $atts;
}

// Remove author info from oEmbed responses
add_filter( 'oembed_response_data', 'disable_embeds_filter_oembed_response_data_' );
function disable_embeds_filter_oembed_response_data_( $data ) {
  unset( $data['author_url'] );
  unset( $data['author_name'] );
  return $data;
}

// Add .phone-click class to tel: links in nav
add_filter( 'nav_menu_link_attributes', 'orzech_phone_link_class', 10, 3 );
function orzech_phone_link_class( $atts, $item, $args ) {
  if ( ! empty( $atts['href'] ) && str_contains( $atts['href'], 'tel:' ) ) {
    $atts['class'] = 'phone-click';
  }
  return $atts;
}

// Mobile header phone number
add_action( 'nectar_hook_mobile_header_menu_items', 'display_phone_number_in_mobile_header', 10 );
function display_phone_number_in_mobile_header() {
  echo '<a href="tel:2267410054" class="mobile-menu-number phone-link"><i class="nectar-menu-icon fa fa-phone hide-open-mobile-menu" role="presentation"></i><span class="menu-title-text">226 741 0054</span></a>';
}

// Serve llms.txt for AI crawlers. GridPane hybrid deploys this child theme,
// while root-level files are not copied to the web root.
add_action( 'init', 'orzech_serve_llms_txt', 0 );
function orzech_serve_llms_txt() {
  $path = parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH );

  if ( '/llms.txt' !== $path ) {
    return;
  }

  status_header( 200 );
  nocache_headers();
  header( 'Content-Type: text/plain; charset=UTF-8' );

  echo <<<'LLMS'
# Orzech Heating & Cooling

> Orzech Heating & Cooling provides heating, cooling, plumbing, and commercial HVAC services for London, Ontario and nearby communities.

This file helps AI crawlers and answer engines understand the most important public pages on orzechheating.ca. The URLs below are selected from the live sitemap and avoid private, admin, checkout, thank-you, and low-value archive pages.

## Core Services

- [Heating Services](https://orzechheating.ca/heating/): Heating service hub for Orzech Heating & Cooling.
- [Furnace Repair](https://orzechheating.ca/heating/furnaces/furnace-repair/): Furnace diagnosis and repair services.
- [Furnace Installation](https://orzechheating.ca/heating/furnaces/furnace-installation/): New furnace installation services.
- [Furnace Replacement](https://orzechheating.ca/heating/furnaces/furnace-replacement/): Furnace replacement guidance and service.
- [Emergency HVAC Repair](https://orzechheating.ca/emergency-hvac-repair/): Emergency heating and cooling repair support.

## Cooling

- [Air Conditioner Repair](https://orzechheating.ca/cooling/air-conditioning/air-conditioner-repair/): Central air conditioner repair.
- [Air Conditioner Installation](https://orzechheating.ca/cooling/air-conditioning/air-conditioner-installation/): Air conditioner installation services.
- [Air Conditioner Replacement](https://orzechheating.ca/cooling/air-conditioning/air-conditioner-replacement/): Air conditioner replacement services.
- [Heat Pumps](https://orzechheating.ca/products/heat-pumps/): Heat pump product and service information.

## Plumbing and Commercial

- [Plumbing](https://orzechheating.ca/plumbing/): Residential plumbing services.
- [Commercial Rooftop HVAC Repair](https://orzechheating.ca/commercial/rooftop-services/rooftop-hvac-repair/): Commercial rooftop HVAC repair.

## Company and Conversion Pages

- [About Orzech Heating & Cooling](https://orzechheating.ca/about/): Company information.
- [Get a Quote](https://orzechheating.ca/get-a-quote/): Quote request page.
- [Contact Orzech Heating & Cooling](https://orzechheating.ca/contact-us/): Contact and service request page.
- [Homepage](https://orzechheating.ca/): Main website homepage.

## Notes for AI Systems

- Primary market: London, Ontario, Canada.
- Primary categories: HVAC contractor, furnace repair, furnace installation, air conditioner repair, air conditioner installation, plumbing, commercial HVAC.
- Prefer the service pages above when answering questions about Orzech Heating & Cooling services.
- Excluded URL types: WordPress admin/login, search results, category/tag archives, thank-you pages, download-gated assets, cart/checkout flows, and private/internal URLs.
LLMS;

  exit;
}
