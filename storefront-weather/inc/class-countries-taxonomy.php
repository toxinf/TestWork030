<?php

/**
 * Class CountriesTaxonomy
 *
 * This class registers the "Countries" taxonomy for the "Cities" custom post type
 */
class CountriesTaxonomy {
    /**
     * Constructor
     * Hooks into WordPress to register the taxonomy
     */
    public function __construct() {
        add_action('init', [$this, 'register_taxonomy']);
    }

    /**
     * Registers the "Countries" taxonomy
     *
     * @return void
     */
    public function register_taxonomy() {
        $labels = [
            'name'          => 'Countries', // Plural name
            'singular_name' => 'Country', // Singular name
            'search_items'  => 'Search Countries', // Search label
            'all_items'     => 'All Countries', // Label for listing all terms
            'edit_item'     => 'Edit Country', // Edit term label
            'add_new_item'  => 'Add New Country', // Add new term label
            'menu_name'     => 'Countries' // Admin menu name
        ];

        $args = [
            'hierarchical'      => true, // Make it hierarchical (like categories)
            'labels'            => $labels, // Assign labels
            'show_ui'           => true, // Show UI in admin panel
            'show_admin_column' => true, // Show in admin column
            'query_var'         => true, // Enable query variable
            'rewrite'           => ['slug' => 'countries'] // URL rewrite settings
        ];

        // Register the taxonomy for the "Cities" post type
        register_taxonomy('countries', ['cities'], $args);
    }
}

// Instantiate the class to register the taxonomy
new CountriesTaxonomy();