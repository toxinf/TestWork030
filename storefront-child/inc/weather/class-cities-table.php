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
     * Hooks into WordPress to enqueue scripts and register AJAX handlers
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_get_cities', [$this, 'get_cities_ajax']);
        add_action('wp_ajax_nopriv_get_cities', [$this, 'get_cities_ajax']);
    }

    /**
     * Enqueues the JavaScript file for AJAX-based pagination
     *
     * @return void
     */
    public function enqueue_scripts() {
        if (is_page_template('templates/cities-table.php')) {
            wp_enqueue_script('cities-pagination', get_stylesheet_directory_uri() . '/inc/weather/cities-pagination.js', ['jquery'], null, true);
            wp_localize_script('cities-pagination', 'citiesAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);
        }
    }

    /**
     * Retrieves a paginated list of cities
     *
     * @param int $page The page number to fetch
     * @return array List of cities with country names
     */
    public function get_paginated_cities($page = 1) {
        global $wpdb;

        $offset = ($page - 1) * $this->per_page;

        $query = $wpdb->prepare("
            SELECT p.ID, p.post_title AS city, t.name AS country
            FROM {$wpdb->prefix}posts p
            LEFT JOIN {$wpdb->prefix}term_relationships tr ON (p.ID = tr.object_id)
            LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'countries')
            LEFT JOIN {$wpdb->prefix}terms t ON (tt.term_id = t.term_id)
            WHERE p.post_type = 'cities' AND p.post_status = 'publish'
            ORDER BY t.name ASC, p.post_title ASC
            LIMIT %d OFFSET %d
        ", $this->per_page, $offset);

        return $wpdb->get_results($query);
    }

    /**
     * Handles AJAX requests to fetch paginated cities
     *
     * @return void
     */
    public function get_cities_ajax() {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;

        $cities = $this->get_paginated_cities($page);
        $total_cities = $this->get_total_cities();
        $total_pages = ceil($total_cities / $this->per_page);

        $response = [
            'cities' => [],
            'total_pages' => $total_pages
        ];

        foreach ($cities as $city) {
            $temperature = CityHelper::get_city_temperature($city->ID);
            $response['cities'][] = [
                'city' => esc_html($city->city),
                'country' => esc_html($city->country),
                'temperature' => $temperature !== null ? $temperature . '°C' : 'N/A'
            ];
        }

        wp_send_json_success($response);
    }


    /**
     * Retrieves the total number of published city posts
     *
     * @return mixed Total number of cities
     */
    public function get_total_cities() {
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'cities' AND post_status = 'publish'");
    }
}

new CitiesTable();
