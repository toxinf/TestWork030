<?php

/**
 * Service class for retrieving city weather data from OpenWeather API.
 */
class OpenWeatherService implements CityWeatherInterface {
    private $api_key;
    private $http_client;

    public function __construct($api_key, $http_client = null) {
        $this->api_key = $api_key;
        $this->http_client = $http_client ?: 'wp_remote_get';
    }

    public function getTemperature($latitude, $longitude) {
        if (empty($this->api_key) || $this->api_key === 'YOUR_OPENWEATHERMAP_API_KEY') {
            return rand(-10, 35); // Simulating temperature if API key is missing
        }

        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&units=metric&appid=" . $this->api_key;

        $response = call_user_func($this->http_client, $url);
        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return isset($data['main']['temp']) ? $data['main']['temp'] : null;
    }
}