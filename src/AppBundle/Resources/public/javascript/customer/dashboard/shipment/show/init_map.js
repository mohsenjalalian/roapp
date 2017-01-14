var map;
var marker;
var icon = $('#show-shipment-map').data('icon');

function initMap() {
    var tehran = {lat: 35.78819000, lng: 51.45983810};
    map = new google.maps.Map(document.getElementById('show-shipment-map'), {
        zoom: 16,
        center: tehran
    });
}