var map;
var marker;
var icon = $('#show-shipment-map').data('icon');

function initMap() {
    var tehran = {lat: 35.78819000, lng: 51.45983810};
    map = new google.maps.Map(document.getElementById('show-shipment-map'), {
        zoom: 16,
        center: tehran
    });
    distanceMatrix(tehran, tehran);
}

// var origin1 = {lat: lat, lng: lng};
// var destinationB = {lat: 50.087, lng: 14.421};
function distanceMatrix(origin1, destinationB) {
    console.log(origin1);
    console.log(destinationB);
    var service = new google.maps.DistanceMatrixService;
    service.getDistanceMatrix({
        origins: [origin1],
        destinations: [destinationB],
        travelMode: 'DRIVING',
        unitSystem: google.maps.UnitSystem.METRIC,
        avoidHighways: false,
        avoidTolls: false
    }, function(response, status) {
        if (status !== 'OK') {
            alert('Error was: ' + status);
        } else {
            var originList = response.originAddresses;
            var destinationList = response.destinationAddresses;
            for (var i = 0; i < originList.length; i++) {
                var results = response.rows[i].elements;

                for (var j = 0; j < results.length; j++) {

                    console.log(results[j].distance.text);
                    console.log(results[j].duration.text);
                }
            }

        }
    });
}