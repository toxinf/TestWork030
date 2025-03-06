jQuery(document).ready(function ($) {
    $('#city-search').on('keyup', function () {
        let searchQuery = $(this).val();

        $.ajax({
            type: 'POST',
            url: citiesAjax.ajaxurl,
            data: {
                action: 'search_cities',
                search: searchQuery
            },
            success: function (response) {
                if (response.success) {
                    let tableBody = $('#cities-table tbody');
                    tableBody.empty();

                    $.each(response.data, function (index, city) {
                        let row = `<tr>
                            <td>${city.city}</td>
                            <td>${city.country}</td>
                            <td>${city.temperature}</td>
                        </tr>`;
                        tableBody.append(row);
                    });
                }
            }
        });
    });
});