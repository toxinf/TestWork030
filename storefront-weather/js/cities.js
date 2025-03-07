jQuery(document).ready(function ($) {
    let searchQuery = ''; // Global variable to store search query
    let currentPage = 1; // Current page
    let paginationBlock = $('#pagination'); // Pagination block

    function fetchCities(page = 1) {
        $.ajax({
            type: 'POST',
            url: citiesAjax.ajaxurl,
            data: {
                action: 'get_cities',
                page: page,
                search: searchQuery
            },
            success: function (response) {
                if (response.success) {
                    let tableBody = $('#cities-table tbody');
                    tableBody.empty();

                    if (response.data.cities.length > 0) {
                        $.each(response.data.cities, function (index, city) {
                            let row = `<tr>
                                <td>${city.city}</td>
                                <td>${city.country}</td>
                                <td>${city.temperature !== null ? city.temperature + '°C' : 'N/A'}</td>
                            </tr>`;
                            tableBody.append(row);
                        });

                        // Update pagination
                        updatePagination(response.data.total_pages, page);
                    } else {
                        tableBody.append('<tr><td colspan="3">No results found</td></tr>');
                        paginationBlock.empty(); // Remove pagination if no results
                    }
                }
            }
        });
    }

    function updatePagination(totalPages, activePage) {
        let paginationContainer = paginationBlock;
        paginationContainer.empty();

        if (totalPages > 1) {
            for (let i = 1; i <= totalPages; i++) {
                let button = $(`<button class="pagination-btn" data-page="${i}">${i}</button>`);
                if (i === activePage) {
                    button.addClass('active'); // Add class to the active button
                }
                paginationContainer.append(button);
            }
        }
    }

    // Search button click handler
    $('.city-search-button').on('click', function () {
        searchQuery = $('.city-search-input').val().trim();
        currentPage = 1; // Reset to the first page on new search
        fetchCities(currentPage);
    });

    // Enter key press handler in the search field
    $('.city-search-input').on('keypress', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            $('.city-search-button').click();
        }
    });

    // Click event handler for pagination buttons (event delegation)
    paginationBlock.on('click', '.pagination-btn', function () {
        currentPage = parseInt($(this).data('page'));
        fetchCities(currentPage);
    });

    // Load the first page on page load
    fetchCities(currentPage);
});
