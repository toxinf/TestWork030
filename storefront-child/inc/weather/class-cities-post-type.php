<?php

/**
 * Class CitiesPostType
 *
 * This class registers a custom post type "Cities" in WordPress
 */
class CitiesPostType {
    /**
     * Constructor
     * Hooks into WordPress to register the custom post type
     */
    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
    }

    /**
     * Registers the "Cities" custom post type
     *
     * @return void
     */
    public function register_post_type() {
        $labels = [
            'name'          => 'Cities', // Plural name
            'singular_name' => 'City', // Singular name
            'menu_name'     => 'Cities', // Menu label in admin panel
            'add_new'       => 'Add New City', // Add new button text
            'edit_item'     => 'Edit City', // Edit item text
            'view_item'     => 'View City', // View item text
            'all_items'     => 'All Cities', // All items text
        ];

        $args = [
            'labels'        => $labels, // Labels for the post type
            'public'        => true, // Make it publicly accessible
            'menu_icon'     => 'dashicons-location-alt', // Icon in the admin menu
            'supports'      => ['title', 'editor', 'thumbnail'], // Supported features
            'has_archive'   => true, // Enable archive page
            'rewrite'       => ['slug' => 'cities'], // URL rewrite settings
        ];

        // Register the post type with the given arguments
        register_post_type('cities', $args);
    }
}

// Instantiate the class to register the custom post type
new CitiesPostType();