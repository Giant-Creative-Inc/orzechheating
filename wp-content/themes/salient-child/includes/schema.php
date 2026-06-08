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
    "https://www.linkedin.com/company/orzech-heating-and-cooling/?originalSubdomain=ca",
    "https://www.bbb.org/ca/on/london/profile/heating-and-air-conditioning/orzech-heating-cooling-inc-0187-1048562"
  ],
  "memberOf": [
    {"@type":"Organization","name":"Better Business Bureau","url":"http://www.bbb.org/"},
    {"@type":"Organization","name":"The Heating and Refrigeration Institute of Canada","url":"https://www.hrai.ca/"},
    {"@type":"Organization","name":"Government Licensing Bodies; TSSA","url":"https://www.tssa.org/en/index.as"}
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
