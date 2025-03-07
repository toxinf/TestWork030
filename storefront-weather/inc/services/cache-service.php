<?php

/**
 * Handles caching for city temperature.
 */
class CacheService {
    private $cache_ttl;

    /**
     * @param $cache_ttl
     */
    public function __construct($cache_ttl = 1800) {
        $this->cache_ttl = $cache_ttl;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key) {
        return get_transient($key);
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    public function set($key, $value) {
        set_transient($key, $value, $this->cache_ttl);
    }
}