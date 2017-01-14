/**
 * Created by mohsenjalalian on 11/14/16.
 */
var socket = io('http://localhost:4000');
var map;
var marker;
var line;


// function initMap() {
//     var tehran = {lat: 35.78819000, lng: 51.45983810};
//     map = new google.maps.Map(document.getElementById('show-shipment-map'), {
//         zoom: 16,
//         center: tehran
//     });
//     marker = new google.maps.Marker({
//         position: tehran,
//         map: map
//     });
// }
$(document).ready(function () {
    var token = $("#show-shipment-map").data('token');
    socket.emit('data', token);
    socket.on('chat message', function(msg){
        var obj = JSON.parse(msg);
        newLat = obj.new_val.lat;
        newLng = obj.new_val.lng;

        oldLat = marker.position.lat();
        oldLng = marker.position.lng();

        marker.setPosition( new google.maps.LatLng( newLat, newLng) );
        map.panTo( new google.maps.LatLng( newLat, newLng) );

    });
    $.ajax({
        url: "load_map",
        dataType: "json",
        data: {token: token},
        type: "POST",
        success: function (result) {

            lastLat = result[0].lastLat;
            lastLng = result[0].lastLng;

            marker = new google.maps.Marker({
                position: new google.maps.LatLng(lastLat, lastLng),
                map: map,
                icon: icon
            });

            map.panTo( new google.maps.LatLng( lastLat, lastLng) );
        }
    })
});