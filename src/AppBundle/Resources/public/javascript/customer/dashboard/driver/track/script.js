/**
 * Created by mohsenjalalian on 11/14/16.
 */
var socket = io('http://localhost:5000');
$(".tracking-element").each(function (index, item) {
    token = $(item).data('track');
    socket.emit('data', token);

    socket.on('chat message', function (msg) {
        var tracking_token = msg.tracking_token;
        var location = JSON.parse(msg.location);
        newLat = location.new_val.lat;
        newLng = location.new_val.lng;

        if (markers.indexOf(tracking_token) == -1) {
            marker [tracking_token] = new google.maps.Marker({
                position: new google.maps.LatLng(newLat, newLng),
                map: map,
                icon: icon
            });
        } else {
            marker[tracking_token].setPosition( new google.maps.LatLng( newLat, newLng) );
            map.panTo( new google.maps.LatLng( newLat, newLng ) );
        }

        markers.push(tracking_token);

    })
});

$(document).ready(function () {
    $.ajax({
        url: "init_map",
        dataType: "json",
        data: {test: 'test'},
        type: "POST",
        success: function (result) {
            console.log(result);
        }
    })
});