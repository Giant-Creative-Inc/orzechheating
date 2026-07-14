<?php

// Per-page custom schema from ACF field
add_action( 'wp_head', function () {
  if ( ! is_page() || ! get_field( 'service_schema' ) ) {
    return;
  }
  $schema = get_field( 'service_schema' );
  if ( ! empty( $schema ) ) {
    echo '<script type="application/ld+json">' . $schema . '</script>';
  }
} );

// Global HVACBusiness schema
add_action( 'wp_head', function () {
  ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "HVACBusiness",
  "@id": "https://orzechheating.ca/#localbusiness",
  "name": "Orzech Heating & Cooling",
  "legalName": "Orzech Heating & Cooling",
  "alternateName": "Orzech",
  "url": "https://orzechheating.ca/",
  "logo": "https://orzechheating.ca/wp-content/uploads/2024/04/OrzechLogo-Colour-1.svg",
  "image": "https://orzechheating.ca/wp-content/uploads/2021/06/Orzech-SocialPreview.jpeg",
  "telephone": "+1-226-799-4882",
  "email": "experts@orzechheating.ca",
  "priceRange": "$$",
  "slogan": "Committed To Comfort",
  "description": "As a local, family-owned and operated business, we have you covered for all your heating, cooling, and plumbing needs in London and the surrounding area.",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "15 Towerline Pl",
    "addressLocality": "London",
    "addressRegion": "ON",
    "postalCode": "N6E 2T3",
    "addressCountry": "CA"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 42.93469287520344,
    "longitude": -81.20090928994169
  },
  "openingHoursSpecification": [
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
      "opens": "00:00",
      "closes": "23:59"
    }
  ],
  "sameAs": [
    "https://www.facebook.com/orzechheatingandcooling/",
    "https://www.linkedin.com/company/orzech-heating-and-cooling/?originalSubdomain=ca"
  ],
  "memberOf": [
    {"@type":"Organization","name":"Better Business Bureau"},
    {"@type":"Organization","name":"The Heating, Refrigeration and Air Conditioning Institute of Canada","url":"https://www.hrai.ca/"},
    {"@type":"Organization","name":"Technical Standards and Safety Authority (TSSA)"}
  ],
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Orzech Heating & Cooling Services",
    "itemListElement": [
      {
        "@type": "OfferCatalog",
        "name": "Heating Services",
        "itemListElement": [
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Furnace Repair & Maintenance","url":"https://orzechheating.ca/heating/furnaces/furnace-repair/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Furnace Installation","url":"https://orzechheating.ca/heating/furnaces/furnace-installation/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Furnace Replacement","url":"https://orzechheating.ca/heating/furnaces/furnace-replacement/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Ductwork Installation","url":"https://orzechheating.ca/heating/furnaces/ductwork-installation/"}}
        ]
      },
      {
        "@type": "OfferCatalog",
        "name": "Air Conditioner Services",
        "itemListElement": [
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Air Conditioner Repair","url":"https://orzechheating.ca/cooling/air-conditioning/air-conditioner-repair/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Air Conditioner Installation","url":"https://orzechheating.ca/cooling/air-conditioning/air-conditioner-installation/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Air Conditioner Replacement","url":"https://orzechheating.ca/cooling/air-conditioning/air-conditioner-replacement/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Ductless AC Installation","url":"https://orzechheating.ca/cooling/air-conditioning/ductless-air-conditioner-installation/"}}
        ]
      },
      {
        "@type": "OfferCatalog",
        "name": "Plumbing Services",
        "itemListElement": [
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Plumbing Repair","url":"https://orzechheating.ca/plumbing/plumbing-repairs/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Water & Sewer Line Repair","url":"https://orzechheating.ca/plumbing/water-lines/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Drain Cleaning","url":"https://orzechheating.ca/plumbing/drain-cleaning/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Kitchen & Bathroom Plumbing","url":"https://orzechheating.ca/plumbing/kitchen-bathroom/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Sump Pump Installation & Repair","url":"https://orzechheating.ca/plumbing/sump-pumps/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Frozen Pipe Repair","url":"https://orzechheating.ca/plumbing/frozen-burst-pipe-repair-replacement/"}},
          {"@type":"Offer","itemOffered":{"@type":"Service","name":"Leaking Faucet & Pipe Repair","url":"https://orzechheating.ca/plumbing/leaking-pipes/"}}
        ]
      }
    ]
  }
}
</script>
  <?php
} );

/**
 * BreadcrumbList JSON-LD (Task task_mrk5khojjoudwy6mzo)
 *
 * Builds a site-wide BreadcrumbList from the current page's URL path so each
 * ListItem name and URL matches the visible page hierarchy. This adds ONLY
 * breadcrumb structured data; it does not modify the HVACBusiness or per-page
 * ACF schema above, and it adds no ratings/reviews.
 *
 * Behaviour:
 * - Runs only on singular pages/posts (not the front page or archives).
 * - Skips output when the trail has a single item (a lone "Home" node is not
 *   eligible for BreadcrumbList rich results).
 * - Derives readable names from a curated slug=>label map, with a title-cased
 *   fallback for any unmapped slug. Confirm labels against the visible trail
 *   on staging.
 */
function orzech_breadcrumb_label_map() {
  return array(
    'heating'                                  => 'Heating',
    'cooling'                                  => 'Cooling',
    'plumbing'                                 => 'Plumbing',
    'furnaces'                                 => 'Furnaces',
    'furnace-repair'                           => 'Furnace Repair',
    'furnace-installation'                     => 'Furnace Installation',
    'furnace-replacement'                      => 'Furnace Replacement',
    'ductwork-installation'                    => 'Ductwork Installation',
    'boilers'                                  => 'Boilers',
    'boiler-repair'                            => 'Boiler Repair',
    'boiler-installation'                      => 'Boiler Installation',
    'boiler-replacement'                       => 'Boiler Replacement',
    'boiler-services'                          => 'Boiler Services',
    'heat-pumps'                               => 'Heat Pumps',
    'gas-fireplaces'                           => 'Gas Fireplaces',
    'forced-air-furnaces'                      => 'Forced Air Furnaces',
    'air-conditioning'                         => 'Air Conditioning',
    'air-conditioner-repair'                   => 'Air Conditioner Repair',
    'air-conditioner-installation'             => 'Air Conditioner Installation',
    'air-conditioner-replacement'              => 'Air Conditioner Replacement',
    'central-air-conditioners'                 => 'Central Air Conditioners',
    'ductless-air-conditioners'                => 'Ductless Air Conditioners',
    'ductless-air-conditioner-installation'    => 'Ductless Air Conditioner Installation',
    'humidifiers'                              => 'Humidifiers',
    'thermostats'                              => 'Thermostats',
    'erv-hrv'                                  => 'ERV & HRV',
    'plumbing-repairs'                         => 'Plumbing Repairs',
    'water-lines'                              => 'Water & Sewer Lines',
    'drain-cleaning'                           => 'Drain Cleaning',
    'kitchen-bathroom'                         => 'Kitchen & Bathroom Plumbing',
    'sump-pumps'                               => 'Sump Pumps',
    'frozen-burst-pipe-repair-replacement'     => 'Frozen & Burst Pipe Repair',
    'leaking-pipes'                            => 'Leaking Pipes',
    'pool-heater'                              => 'Pool Heater',
    'refrigeration-installation'               => 'Refrigeration Installation',
    'refrigeration-repair'                     => 'Refrigeration Repair',
    'refrigeration-replacement'                => 'Refrigeration Replacement',
    'rooftop-hvac-installation'                => 'Rooftop HVAC Installation',
    'rooftop-hvac-repair'                      => 'Rooftop HVAC Repair',
    'rooftop-hvac-replacement'                 => 'Rooftop HVAC Replacement',
    'rooftop-services'                         => 'Rooftop Services',
    'emergency-hvac-repair'                    => 'Emergency HVAC Repair',
    'commercial'                               => 'Commercial',
    'products'                                 => 'Products',
    'lennox-products'                          => 'Lennox Products',
    'armstrong-air-products'                   => 'Armstrong Air Products',
    'about'                                    => 'About',
    'contact-us'                               => 'Contact Us',
    'financing'                                => 'Financing',
    'maintenance-plan'                         => 'Maintenance Plan',
    'faqs'                                     => 'FAQs',
    'reviews'                                  => 'Reviews',
    'news-and-blog'                            => 'News & Blog',
    'clean-home-initiative'                    => 'Clean Home Initiative',
    'furnace-and-air-conditioner-deals'        => 'Furnace & Air Conditioner Deals',
    'essential-furnace-buying-guide'           => 'Essential Furnace Buying Guide',
    'ultimate-ac-buying-guide'                 => 'Ultimate Air Conditioner Buying Guide',
    'lennox-ultimate-comfort-system'           => 'Lennox Ultimate Comfort System',
  );
}

function orzech_breadcrumb_label_for_slug( $slug ) {
  $map = orzech_breadcrumb_label_map();
  if ( isset( $map[ $slug ] ) ) {
    return $map[ $slug ];
  }
  // Fallback: turn "some-slug" into "Some Slug".
  $words = str_replace( array( '-', '_' ), ' ', $slug );
  return ucwords( $words );
}

add_action( 'wp_head', function () {
  if ( is_admin() || is_feed() ) {
    return;
  }

  // Only emit on singular content; skip the front page and archives.
  if ( is_front_page() || is_home() || ! ( is_page() || is_singular() ) ) {
    return;
  }

  $permalink = get_permalink();
  if ( ! $permalink ) {
    return;
  }

  $path = wp_parse_url( $permalink, PHP_URL_PATH );
  $path = is_string( $path ) ? trim( $path, '/' ) : '';

  $segments = array();
  if ( '' !== $path ) {
    $segments = array_values( array_filter( explode( '/', $path ), 'strlen' ) );
  }

  // Build the item list, always starting with Home.
  $items = array(
    array(
      'name' => 'Home',
      'url'  => trailingslashit( home_url( '/' ) ),
    ),
  );

  $cumulative = '';
  foreach ( $segments as $segment ) {
    $cumulative .= '/' . $segment;
    $items[] = array(
      'name' => orzech_breadcrumb_label_for_slug( $segment ),
      'url'  => trailingslashit( home_url( $cumulative ) ),
    );
  }

  // A single "Home" node is not an eligible breadcrumb trail.
  if ( count( $items ) < 2 ) {
    return;
  }

  $list_items = array();
  $position   = 1;
  foreach ( $items as $item ) {
    $list_items[] = array(
      '@type'    => 'ListItem',
      'position' => $position,
      'name'     => $item['name'],
      'item'     => $item['url'],
    );
    $position++;
  }

  $breadcrumb = array(
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    '@id'             => trailingslashit( $permalink ) . '#breadcrumb',
    'itemListElement' => $list_items,
  );

  echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
} );
