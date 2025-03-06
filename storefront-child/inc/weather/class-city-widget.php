<?php

/**
 * Class CityTemperatureWidget
 *
 * This widget displays the temperature of a selected city using the CityHelper class
 */
class CityTemperatureWidget extends WP_Widget {
    /**
     * Constructor
     * Registers the widget with WordPress
     */
    public function __construct() {
        parent::__construct(
            'city_temperature_widget',
            'City Temperature',
            ['description' => 'Displays the temperature of a selected city.']
        );
    }

    /**
     * Outputs the widget content on the frontend
     *
     * @param array $args Widget arguments
     * @param array $instance Saved widget settings
     * @return void
     */
    public function widget($args, $instance) {
        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : 0;

        if ($city_id) {
            $temperature = CityHelper::get_city_temperature($city_id);
            echo $args['before_widget'];
            echo '<h3>' . get_the_title($city_id) . '</h3>';
            echo '<p>Temperature: ' . ($temperature !== null ? $temperature . '°C' : 'N/A') . '</p>';
            echo $args['after_widget'];
        }
    }

    /**
     * Outputs the widget settings form in the admin panel
     *
     * @param array $instance Previously saved widget settings
     * @return void
     */
    public function form($instance) {
        $cities = get_posts(['post_type' => 'cities', 'numberposts' => -1]);
        $selected_city = !empty($instance['city_id']) ? $instance['city_id'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('city_id'); ?>">Select City:</label>
            <select id="<?php echo $this->get_field_id('city_id'); ?>" name="<?php echo $this->get_field_name('city_id'); ?>">
                <?php foreach ($cities as $city) : ?>
                    <option value="<?php echo $city->ID; ?>" <?php selected($selected_city, $city->ID); ?>>
                        <?php echo esc_html($city->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    /**
     * Saves the widget settings
     *
     * @param array $new_instance New settings
     * @param array $old_instance Previous settings
     * @return array Updated settings
     */
    public function update($new_instance, $old_instance) {
        return ['city_id' => !empty($new_instance['city_id']) ? intval($new_instance['city_id']) : ''];
    }
}

/**
 * Registers the CityTemperatureWidget with WordPress
 *
 * @return void
 */
function register_city_temperature_widget() {
    register_widget('CityTemperatureWidget');
}

add_action('widgets_init', 'register_city_temperature_widget');