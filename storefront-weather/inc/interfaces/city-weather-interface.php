<?php

/**
 * Interface for weather services.
 */
interface CityWeatherInterface {
    public function getTemperature($latitude, $longitude);
}