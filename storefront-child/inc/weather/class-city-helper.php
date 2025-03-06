<?php

/**
 * Class CityHelper
 *
 * This class provides utility functions for retrieving city-related data
 */
class CityHelper {
    private static $api_key = 'YOUR_OPENWEATHERMAP_API_KEY'; // API key for OpenWeatherMap

    /**
     * Retrieves the current temperature of a city using OpenWeatherMap API
     *
     * @param int $city_id The ID of the city post
     * @return mixed|null The temperature in Celsius or null if unavailable
     */
    public static function get_city_temperature($city_id) {
        $latitude = get_post_meta($city_id, '_city_latitude', true);
        $longitude = get_post_meta($city_id, '_city_longitude', true);

        if (!$latitude || !$longitude) {
            return null;
        }

        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&units=metric&appid=" . self::$api_key;

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return isset($data['main']['temp']) ? $data['main']['temp'] : null;
    }
}