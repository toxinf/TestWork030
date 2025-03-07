<?php

/**
 * Class CitiesTable
 *
 * This class handles AJAX-based pagination for displaying cities in a table
 */
class CitiesTable {
    private $per_page = 10; // Number of cities per page

    /**
     * Constructor
     * Hooks into WordPress to enqueue scripts and register AJAX handlers.
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_get_cities', [$this, 'get_cities_ajax']);
        add_action('wp_ajax_nopriv_get_cities', [$this, 'get_cities_ajax']);
    }

    /**
     * Enqueues JavaScript files for AJAX-based pagination and search.
     */
    public function enqueue_scripts() {
        if (!is_admin() && is_page() && is_page_template('cities-table.php')) {
            $this->enqueue_script('cities', 'js/cities.js');
            wp_localize_script('cities', 'citiesAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);
        }
    }

    /**
     * Helper function to enqueue scripts with versioning.
     *
     * @param string $handle Script handle.
     * @param string $path Relative path to the script file.
     */
    private function enqueue_script($handle, $path) {
        wp_enqueue_script(
            $handle,
            get_stylesheet_directory_uri() . '/' . $path,
            ['jquery'],
            filemtime(get_stylesheet_directory() . '/' . $path),
            true
        );
    }

    /**
     * Retrieves a paginated list of cities, optionally filtered by search term.
     *
     * @param int $page The page number to fetch.
     * @param string|null $search_term Search term to filter cities.
     * @return array List of cities with country names.
     */
    public function get_paginated_cities($page = 1, $search_term = null) {
        global $wpdb;

        $offset = ($page - 1) * $this->per_page;
        $search_condition = "";
        $params = [$this->per_page, $offset];

        if ($search_term) {
            $search_condition = "AND (p.post_title LIKE %s OR t.name LIKE %s)";
            array_unshift($params, '%' . $search_term . '%', '%' . $search_term . '%');
        }

        $query = $wpdb->prepare("
            SELECT p.ID, p.post_title AS city, t.name AS country
            FROM {$wpdb->prefix}posts p
            LEFT JOIN {$wpdb->prefix}term_relationships tr ON (p.ID = tr.object_id)
            LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'countries')
            LEFT JOIN {$wpdb->prefix}terms t ON (tt.term_id = t.term_id)
            WHERE p.post_type = 'cities' AND p.post_status = 'publish' 
            $search_condition
            ORDER BY t.name ASC, p.post_title ASC
            LIMIT %d OFFSET %d
        ", ...$params);

        return $wpdb->get_results($query);
    }

    /**
     * Retrieves the total number of cities, optionally filtered by search term.
     *
     * @param string|null $search_term Search term to filter the count.
     * @return int Total number of cities.
     */
    public function get_total_cities($search_term = null) {
        global $wpdb;

        $search_condition = "";
        $params = [];

        if ($search_term) {
            $search_condition = "AND (p.post_title LIKE %s OR t.name LIKE %s)";
            $params = ['%' . $search_term . '%', '%' . $search_term . '%'];
        }

        $query = $wpdb->prepare("
            SELECT COUNT(*)
            FROM {$wpdb->prefix}posts p
            LEFT JOIN {$wpdb->prefix}term_relationships tr ON (p.ID = tr.object_id)
            LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'countries')
            LEFT JOIN {$wpdb->prefix}terms t ON (tt.term_id = t.term_id)
            WHERE p.post_type = 'cities' AND p.post_status = 'publish' 
            $search_condition
        ", ...$params);

        return (int) $wpdb->get_var($query);
    }

    /**
     * Handles AJAX requests to fetch paginated cities with optional search.
     */
    public function get_cities_ajax() {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : null;

        $cities = $this->get_paginated_cities($page, $search_term);
        $total_cities = $this->get_total_cities($search_term);
        $total_pages = ceil($total_cities / $this->per_page);

        $response = [
            'cities' => $this->format_cities($cities),
            'total_pages' => $total_pages
        ];

        wp_send_json_success($response);
    }

    /**
     * Formats city data for JSON response.
     *
     * @param array $cities List of city objects.
     * @return array Formatted city data.
     */
    private function format_cities($cities) {
        global $city_helper;
        $result = [];

        foreach ($cities as $city) {
            $temperature = $city_helper->get_city_temperature($city->ID);
            $result[] = [
                'city' => esc_html($city->city),
                'country' => esc_html($city->country),
                'temperature' => $temperature
            ];
        }

        return $result;
    }
}

new CitiesTable();
