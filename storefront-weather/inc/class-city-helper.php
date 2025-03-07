<?php

/**
 * Class CityHelper
 *
 * This class provides utility functions for retrieving city-related data
 */
class CityHelper {
    private $weather_service;
    private $cache_service;

    /**
     * Constructor to initialize the weather and cache services
     *
     * @param CityWeatherInterface $weather_service
     * @param CacheService $cache_service
     */
    public function __construct(CityWeatherInterface $weather_service, CacheService $cache_service) {
        $this->weather_service = $weather_service;
        $this->cache_service = $cache_service;
    }

    /**
     * Fetches the temperature for a given city ID.
     *
     * @param int $city_id The ID of the city.
     * @return float|null The temperature or null if coordinates are missing.
     */
    public function fetch_city_temperature($city_id) {
        // Retrieve latitude and longitude from city metadata
        $latitude = get_post_meta($city_id, '_city_latitude', true);
        $longitude = get_post_meta($city_id, '_city_longitude', true);

        // Return null if coordinates are not available
        if (!$latitude || !$longitude) {
            return null;
        }

        // Fetch temperature using the weather service
        return $this->weather_service->getTemperature($latitude, $longitude);
    }

    /**
     * Gets the temperature for a given city, using cache when available.
     *
     * @param int $city_id The ID of the city.
     * @return float|null The temperature or null if not available.
     */
    public function get_city_temperature($city_id) {
        // Generate a cache key based on the city ID
        $cache_key = "city_temp_{$city_id}";

        // Attempt to get the temperature from cache
        $temperature = $this->cache_service->get($cache_key);

        // If cache is empty, fetch the temperature and update cache
        if ($temperature === false) {
            $temperature = $this->fetch_city_temperature($city_id);

            // Store temperature in cache if available
            if ($temperature !== null) {
                $this->cache_service->set($cache_key, $temperature);
            }
        }

        return $temperature;
    }
}