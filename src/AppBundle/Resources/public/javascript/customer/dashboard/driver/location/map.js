/**
 * Created by mohsenjalalian on 11/14/16.
 */
var map;
var marker = [];
var markers = [];
var line;
var icon = $('.tracking-element').data('icon');

function initMap() {
    var tehran = {lat: 35.78819000, lng: 51.45983810};
    map = new google.maps.Map(document.getElementById('location-shipment-map'), {
        zoom: 11,
        center: tehran
    });
}