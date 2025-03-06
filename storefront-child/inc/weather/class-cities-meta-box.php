<?php

/**
 * Class CitiesMetaBox
 *
 * This class creates a meta box in the WordPress admin panel for adding latitude and longitude coordinates to city posts
 */
class CitiesMetaBox {

    /**
     * Constructor.
     * Hooks into WordPress to add and save the meta box
     */
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta_box']);
    }

    /**
     * Adds a meta box to the "cities" post type
     *
     * @return void
     */
    public function add_meta_box() {
        add_meta_box(
            'city_coordinates', // Meta box ID
            'City Coordinates', // Title
            [$this, 'render_meta_box'], // Callback function to display fields
            'cities', // Post type
            'side', // Context (location on the editing screen)
            'default' // Priority
        );
    }

    /**
     * Renders the meta box fields in the WordPress admin panel
     *
     * @param WP_Post $post The current post object
     * @return void
     */
    public function render_meta_box($post) {
        // Retrieve existing latitude and longitude values
        $latitude = get_post_meta($post->ID, '_city_latitude', true);
        $longitude = get_post_meta($post->ID, '_city_longitude', true);
        ?>
        <p>
            <label for="city_latitude">Latitude:</label>
            <input type="text" id="city_latitude" name="city_latitude" value="<?php echo esc_attr($latitude); ?>" style="width:100%;">
        </p>
        <p>
            <label for="city_longitude">Longitude:</label>
            <input type="text" id="city_longitude" name="city_longitude" value="<?php echo esc_attr($longitude); ?>" style="width:100%;">
        </p>
        <?php
    }

    /**
     * Saves the meta box data when the post is saved
     *
     * @param int $post_id The ID of the post being saved
     * @return void
     */
    public function save_meta_box($post_id) {
        // Check if latitude is set and save it
        if (isset($_POST['city_latitude'])) {
            update_post_meta($post_id, '_city_latitude', sanitize_text_field($_POST['city_latitude']));
        }
        // Check if longitude is set and save it
        if (isset($_POST['city_longitude'])) {
            update_post_meta($post_id, '_city_longitude', sanitize_text_field($_POST['city_longitude']));
        }
    }
}

// Instantiate the class to register the meta box
new CitiesMetaBox();