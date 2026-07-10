<?php
/**
 * Plugin Name: GIANT Agent Factory llms.txt
 * Description: Serves the generated llms.txt file for AI assistants.
 */

add_action('template_redirect', function () {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if ($path !== '/llms.txt') {
        return;
    }

    $content = '# Orzech Heating & Cooling

> Orzech Heating & Cooling is an HVAC and plumbing company serving London, Ontario. Services include furnace repair and installation, air conditioning repair and installation, heat pumps, plumbing repairs, and commercial rooftop HVAC.

## Services

- [Furnace Repair](https://orzechheating.ca/heating/furnaces/furnace-repair/)
- [Furnace Installation](https://orzechheating.ca/heating/furnaces/furnace-installation/)
- [Air Conditioner Repair](https://orzechheating.ca/cooling/air-conditioning/air-conditioner-repair/)
- [Air Conditioner Installation](https://orzechheating.ca/cooling/air-conditioning/air-conditioner-installation/)
- [Heat Pumps](https://orzechheating.ca/products/heat-pumps/)
- [Plumbing Repairs](https://orzechheating.ca/plumbing/plumbing-repairs/)
- [Commercial Rooftop HVAC Repair](https://orzechheating.ca/commercial/rooftop-services/rooftop-hvac-repair/)

## Company

- [About](https://orzechheating.ca/about/)
- [Get a Quote](https://orzechheating.ca/get-a-quote/)
- [Home](https://orzechheating.ca/)

## Contact

Request service or a quote through the Get a Quote page: https://orzechheating.ca/get-a-quote/
';

    status_header(200);
    header('Content-Type: text/plain; charset=UTF-8');
    header('X-Robots-Tag: noindex');
    echo $content;
    exit;
});
