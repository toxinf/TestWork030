jQuery(document).ready(function ($) {
    // Pagination button click handler
    $('.pagination-btn').on('click', function () {
        let page = $(this).data('page');

        $.ajax({
            type: 'POST',
            url: citiesAjax.ajaxurl,
            data: {
                action: 'get_cities',
                page: page
            },
            success: function (response) {
                if (response.success) {
                    let tableBody = $('#cities-table tbody');
                    tableBody.empty();

                    $.each(response.data.cities, function (index, city) {
                        let row = `<tr>
                            <td>${city.city}</td>
                            <td>${city.country}</td>
                            <td>${city.temperature}</td>
                        </tr>`;
                        tableBody.append(row);
                    });

                    // Update the active pagination button
                    $('.pagination-btn').removeClass('active');
                    $('.pagination-btn[data-page="' + page + '"]').addClass('active');
                }
            }
        });
    });
});
