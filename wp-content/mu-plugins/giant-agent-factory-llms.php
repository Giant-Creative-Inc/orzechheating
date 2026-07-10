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

> Orzech Heating & Cooling is a HVAC and plumbing contractor serving London, Ontario and surrounding areas. We provide furnace repair and installation, air conditioning repair and installation, heat pumps, plumbing, and commercial HVAC services.

Location: London, Ontario, Canada
Website: https://orzechheating.ca

## Services

- [Furnace Repair](https://orzechheating.ca/furnace-repair/): Repair services for residential and commercial furnaces.
- [Furnace Installation](https://orzechheating.ca/furnace-installation/): New furnace supply and installation.
- [AC Repair](https://orzechheating.ca/ac-repair/): Air conditioning diagnostics and repair.
- [AC Installation](https://orzechheating.ca/ac-installation/): New air conditioner supply and installation.
- [Heat Pumps](https://orzechheating.ca/heat-pumps/): Heat pump consultation, supply, and installation.
- [Plumbing](https://orzechheating.ca/plumbing/): Residential and commercial plumbing services.
- [Commercial HVAC](https://orzechheating.ca/commercial-hvac/): Heating and cooling solutions for commercial properties.

## Key Pages

- [Home](https://orzechheating.ca/): Overview of Orzech Heating & Cooling services.
- [Services](https://orzechheating.ca/services/): Full list of HVAC and plumbing services.
- [About](https://orzechheating.ca/about/): Company background and service area.
- [Contact](https://orzechheating.ca/contact/): Phone, service requests, and booking information.

## Notes

- Service area: London, Ontario and surrounding communities.
- For appointments and quotes, use the Contact page.
';

    status_header(200);
    header('Content-Type: text/plain; charset=UTF-8');
    header('X-Robots-Tag: noindex');
    echo $content;
    exit;
});
