var map;
var marker;
var icon = $('#list-shipment-map-modal').data('icon');
function initMap() {
    var tehran = {lat: 35.78819000, lng: 51.45983810};
    map = new google.maps.Map(document.getElementById('list-shipment-map-modal'), {
        zoom: 16,
        center: tehran
    });
// $("#tracking_shipment_modal").modal("");
    // google.maps.event.trigger(map, 'resize');
}
$('#tracking_shipment_modal').on('shown.bs.modal', function () {
    google.maps.event.trigger(map, "resize");
});

// var origin1 = {lat: lat, lng: lng};
// var destinationB = {lat: 50.087, lng: 14.421};
// function distanceMatrix(origin1, destinationB) {
//     // console.log(origin1);
//     // console.log(destinationB);
//     var service = new google.maps.DistanceMatrixService;
//     service.getDistanceMatrix({
//         origins: [origin1],
//         destinations: [destinationB],
//         travelMode: 'DRIVING',
//         unitSystem: google.maps.UnitSystem.METRIC,
//         avoidHighways: false,
//         avoidTolls: false
//     }, function(response, status) {
//         if (status !== 'OK') {
//             current = false;
//
//             return current;
//             // alert('Error was: ' + status);
//         } else {
//             var originList = response.originAddresses;
//             var destinationList = response.destinationAddresses;
//             for (var i = 0; i < originList.length; i++) {
//                 var results = response.rows[i].elements;
//
//                 for (var j = 0; j < results.length; j++) {
//                     // console.log(results[j].distance.text);
//                     // console.log(results[j].duration.text);
//
//                     current =  {
//                         Distance: results[j].distance.text,
//                         Duration: results[j].duration.text
//                     };
//
//                     return current;
//
//                 }
//             }
//         }
//     });
//     return current;
// }