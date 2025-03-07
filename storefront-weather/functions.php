<?php

/**
 * Storefront Weather Child Theme Functions
 *
 * This file contains functions and hooks for the Storefront Weather child theme.
 * It includes custom post types, taxonomies, widgets, and helper classes.
 *
 * @package storefront-weather
 */
function storefront_weather_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', ['parent-style']);
}
add_action('wp_enqueue_scripts', 'storefront_weather_enqueue_styles');

$weather_classes = [
    'class-cities-post-type.php',
    'class-cities-meta-box.php',
    'class-countries-taxonomy.php',
    'class-city-widget.php',
    'class-city-helper.php',
    'class-cities-table.php',
    'interfaces/city-weather-interface.php',
    'services/cache-service.php',
    'services/weather/class-open-weather.php',
];

foreach ($weather_classes as $file) {
    require_once get_stylesheet_directory() . '/inc/' . $file;
}

function initialize_city_services() {
    global $weather_service, $cache_service, $city_helper;

    $weather_service = new OpenWeatherService('');
    $cache_service = new CacheService();
    $city_helper = new CityHelper($weather_service, $cache_service);
}
add_action('init', 'initialize_city_services');