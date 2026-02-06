$(document).ready(function() {
    $('#province_id').select2({ placeholder: 'Select Province(s)' });
    $('#city_id').select2({ placeholder: 'Select City/Cities' });

    function filterCities() {
        var selectedProvinces = $('#province_id').val();
        var allCities = JSON.parse($('#city_id').attr('data-cities'));
        var filtered = [];
        if (selectedProvinces && selectedProvinces.length > 0) {
            filtered = allCities.filter(function(city) {
                return selectedProvinces.includes(city.province_id.toString());
            });
        } else {
            filtered = allCities;
        }
        var citySelect = $('#city_id');
        var selectedCities = citySelect.val() || [];
        citySelect.empty();
        citySelect.append('<option value="">All Cities</option>');
        filtered.forEach(function(city) {
            var selected = selectedCities.includes(city.id.toString()) ? 'selected' : '';
            citySelect.append('<option value="' + city.id + '" ' + selected + '>' + city.name + '</option>');
        });
        citySelect.trigger('change.select2');
    }

    $('#province_id').on('change', filterCities);
    filterCities();
});
