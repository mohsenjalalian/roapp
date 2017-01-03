/**
 * Created by mohsenjalalian on 11/14/16.
 */
var socket = io('http://localhost:5000');
$(".tracking-element").each(function (index, item) {
    token = $(item).data('track');
    socket.emit('data', token);

    socket.on('chat message', function (msg) {
        var location = JSON.parse(msg.location);
        newLat = location.new_val.lat;
        newLng = location.new_val.lng;

        marker = new google.maps.Marker({
            position: new google.maps.LatLng(newLat, newLng),
            map: map
        });
    })
});