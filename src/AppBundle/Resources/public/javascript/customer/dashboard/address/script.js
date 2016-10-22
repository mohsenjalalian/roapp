function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: {lat: 35.6891975, lng: 51.388973599999986}
    });

    var marker = new google.maps.Marker({
        position: {lat: 35.6891975, lng: 51.388973599999986},
        map: map,
        title: 'تهران',
        draggable:true
    });

    var geocoder = new google.maps.Geocoder();


    $("#address").keyup(function(){
        geocodeAddress(geocoder, map, marker);
    });
}

function geocodeAddress(geocoder, resultsMap, marker) {
    var address = document.getElementById('address').value;

    geocoder.geocode({'address': address}, function(results, status) {
        if (status === 'OK') {
            resultsMap.setCenter(results[0].geometry.location);
            $('#address_latitude').val(results[0].geometry.location.lat());
            $('#address_longitude').val(results[0].geometry.location.lng());
            marker.setPosition({lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()});

            google.maps.event.addListener(marker, 'dragend', function (event) {
                $('#address_latitude').val(this.getPosition().lat());
                $('#address_longitude').val(this.getPosition().lng());
            });

        } else {

        }
    });
}


function editMap() {

    var lat = parseFloat($("#address").data('lat'));
    var lng = parseFloat($("#address").data('lng'));
    $("#address").val($("#address").data('addr'));

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: {lat: lat, lng: lng}
    });

    var marker = new google.maps.Marker({
        position: {lat: lat, lng: lng},
        map: map,
        title: 'تهران',
        draggable:true
    });

    var geocoder = new google.maps.Geocoder();


    $("#address").keyup(function(){
        geocodeAddress(geocoder, map, marker);
    });
}

$("#address").keyup(function(){
    $("#address_description").val($(this).val());
});