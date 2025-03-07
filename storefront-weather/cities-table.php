<?php
/*
 * Template Name: Cities Table
 *
 * This template displays a paginated table of cities along with their respective temperatures.
 */

get_header();
do_action('before_cities_table');

global $city_helper;

// Instantiate the CitiesTable class to fetch city data
$cities_table = new CitiesTable();

// Retrieve the first page of cities (10 per page by default)

$first_page_cities = $cities_table->get_paginated_cities();

// Get the total number of cities in the database

$total_cities = $cities_table->get_total_cities();

// Calculate the total number of pages needed for pagination (assuming 10 cities per page)

$total_pages = ceil($total_cities / 10);
?>

    <div class="cities-table-container">
        <h2>List of Cities with Temperature</h2>

        <!-- Input field for filtering cities by name -->
        <div class="city-search">
            <input type="text" class="city-search-input" placeholder="Search for a city...">
            <button class="city-search-button">Search</button>
        </div>


        <table id="cities-table">
            <thead>
            <tr>
                <th>City</th>
                <th>Country</th>
                <th>Temperature (°C)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($first_page_cities as $city) :
                // Retrieve the current temperature for the city
                $temperature = $city_helper->get_city_temperature($city->ID);
                ?>
                <tr>
                    <td><?php echo esc_html($city->city); ?></td>
                    <td><?php echo esc_html($city->country); ?></td>
                    <td><?php echo $temperature !== null ? $temperature . '°C' : 'N/A'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination controls -->
        <div id="pagination" class="pagination-wrapper">
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <button class="pagination-btn" data-page="<?php echo $i; ?>"><?php echo $i; ?></button>
            <?php endfor; ?>
        </div>

    </div>

<?php do_action('after_cities_table');
get_footer();
