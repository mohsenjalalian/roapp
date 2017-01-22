var socket = io('http://localhost:4000');
var map;
var marker;
var line;
var show_map =$("#list-shipment-map-modal");
// var other_address_lat = show_map.data('lat');
// var other_address_lng = show_map.data('lng');
var currentStatus;
$(".tracking_shipment_button").on('click',function () {
    var token = $(this).data('token');
    // var other_address_lat = $(this).data('lat');
    // var other_address_lng = $(this).data('lng');
    // console.log(token);
    // console.log(other_address_lat);
    // console.log(other_address_lng);
    socket.emit('data', token);
    socket.on('chat message', function (msg) {
        var obj = JSON.parse(msg);
        newLat = obj.new_val.lat;
        newLng = obj.new_val.lng;
        oldLat = marker.position.lat();
        oldLng = marker.position.lng();

        // var origin1 = {lat: parseFloat(newLat), lng: parseFloat(newLng)};
        // var destinationB = {lat: parseFloat(other_address_lat), lng: parseFloat(other_address_lng)};
        // currentStatus = distanceMatrix(origin1,destinationB);
        // $(".google_distance td").html(currentStatus.Distance);
        // $(".google_time td").html(currentStatus.Duration);

        marker.setPosition(new google.maps.LatLng(newLat, newLng));
        map.panTo(new google.maps.LatLng(newLat, newLng));

    });
    $.ajax({
        url: "load_map",
        dataType: "json",
        data: {token: token},
        type: "POST",
        success: function (result) {

            lastLat = result[0].lastLat;
            lastLng = result[0].lastLng;

            // var origin1 = {lat: parseFloat(lastLat), lng: parseFloat(lastLng)};
            // var destinationB = {lat: parseFloat(other_address_lat), lng: parseFloat(other_address_lng)};
            // currentStatus = distanceMatrix(origin1, destinationB);
            // $(".google_distance td").html(currentStatus.Distance);
            // $(".google_time td").html(currentStatus.Duration);

            marker = new google.maps.Marker({
                position: new google.maps.LatLng(lastLat, lastLng),
                map: map,
                icon: icon
            });
            map.panTo(new google.maps.LatLng(lastLat, lastLng));
        }
    });
    $("#tracking_shipment_modal").modal("show");
});
