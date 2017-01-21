/**
 * Created by mohsenjalalian on 12/24/16.
 */
$(document).ready(function() {
    $(".js-datepicker").pDatepicker(
        {
            timePicker: {
                enabled: true,
                showSeconds: true,
                showMeridian: true,
                scrollEnabled: true,
                showOn: 'button',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd-mm-yy',
                yearRange: "-0:+1"
            }
        }
    );
});
$(document).ready(function() {
    var $otherPhone = $('.other-phone');
    // When sport gets selected ...
    $otherPhone.on('keyup', function () {
        $('.select-address').empty();
        if  ($otherPhone.val().length == 11) {
            // ... retrieve the corresponding form.
            var $form = $(this).closest('form');
            // Simulate form data, but only include the selected sport value.
            var data = {};
            data[$otherPhone.attr('name')] = $otherPhone.val();
            data[$otherPhone.attr('name')] = $otherPhone.val();
            // Submit data via AJAX to the form's action path.
            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                data: data,
                success: function (html) {
                    // Replace current position field ...
                    $('.select-address').replaceWith(
                        // ... with the returned one from the AJAX response.
                        $(html).find('.select-address')
                    );
                    // Position field now displays the appropriate positions.
                }
            });
        }
    });
    $isBusinessUnitDriver = $('#shipment-switch');
    $isBusinessUnitDriver.on('change', function (e) {
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        var data = {};
        data[$isBusinessUnitDriver.attr('name')] = $isBusinessUnitDriver.val();
        // Submit data via AJAX to the form's action path.
        if ($isBusinessUnitDriver.is(":checked")) {
            $('.creator_shipment').prop('disabled', true);
            $("#cost_show").html("---");
            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                data: data,
                success: function (html) {
                    // Replace current position field ...
                    $('#restaurant_shipment_driver').replaceWith(
                        // ... with the returned one from the AJAX response.
                        $(html).find('#restaurant_shipment_driver')
                    );
                    // Position field now displays the appropriate positions.
                }
            });
        } else  {
            if (window.globalVar) {
                validate();
            } else {
                $('.creator_shipment').prop('disabled', true);

            }
        }
    })
});
$('.businessUnit_driver_modal').on('hidden.bs.modal', function () {
    if($("#restaurant_shipment_driver input").is(":checked")) {
        validate();
        $('.creator_shipment').prop('disabled', false);
    } else {
        $("#cost_show").html("---");
        $('.creator_shipment').prop('disabled', true);
    }
})

$(".calc_price_item, .select-address input").on('change', function () {
    validate();
});
function validate() {
    window.globalVar = false;
    var ownerAddressId = $('.owner-address').val();
    var otherAddressId = $('.select-address input').val();
    var otherAddressLength = $('.select-address input').length;
    console.log(otherAddressId);
    var shipmentValue = 1000;
    var shipmentPickUpTime = $(".duration").val();
    var restaurantShipmentValue = $("#restaurant_shipment_value").val()
    if (ownerAddressId.length !=0 && otherAddressLength !=0 && shipmentPickUpTime.length !=0 && restaurantShipmentValue.length !=0) {
        $.ajax({
            url: 'calc_shipment_price',
            data: {
                ownerAddressId: ownerAddressId,
                otherAddressId: otherAddressId,
                shipmentValue: shipmentValue,
                shipmentPickUpTime: shipmentPickUpTime
            },
            type: "POST",
            success: function (response) {
                $("#calculate_price").html("<b>قیمت کل :</b>");
                $("#cost_show").html(response);
                $("#cost_show").css('display', 'inline');
                $(".price_send").prepend(
                    '<input name="price_shipment" type="hidden" value="' + response + '">'
                );
                $('.creator_shipment').prop('disabled', false);
                window.globalVar = true;
            },
            error : function () {
                $("#cost_show").html("---");
                $('.creator_shipment').prop('disabled', true);
                window.globalVar = false;
            }
        })
    } else {
        $("#cost_show").html("---");
        $('.creator_shipment').prop('disabled', true);
        window.globalVar = false;
    }
}
$('#mapModal').on('hidden.bs.modal', function () {
    $("#address").val("");
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: {lat: 35.6891975, lng: 51.388973599999986}
    });
    marker = new google.maps.Marker({
        position: {lat: 35.6891975, lng: 51.388973599999986},
        map: map,
        title: 'تهران',
        draggable:true
    });
});
$("#add_address").on('submit', function (event) {
    event.preventDefault();
    var phoneNumber = $("#restaurant_shipment_other_phone").val();
    $("#add_address #address").append('<input id="reciver_customer_mobile_number"  name="shipment_otherAddress_number" type="hidden" value="'+phoneNumber+'">');
    var fd = new FormData($('form')[2]);
    shipmentForm = $('.panel');
    var $otherPhone = $('.other-phone');
    var data = {};
    data[$otherPhone.attr('name')] = $otherPhone.val();
    var formURL = $(this).attr("action");
    $.ajax(
        {
            url: formURL,
            data:fd,
            processData: false,
            contentType: false,
            type: "POST",
            success: function (response) {
                $("#address").val("");
                $('#mapModal').modal('hide');

                $.ajax({
                    url: shipmentForm.attr('action'),
                    type: shipmentForm.attr('method'),
                    data: data,
                    success: function (html) {
                        // Replace current position field ...
                        $('.select-address').replaceWith(

                            // ... with the returned one from the AJAX response.
                            $(html).find('.select-address')
                        );
                        // Position field now displays the appropriate positions.
                        $("#restaurant_shipment_otherAddress:last-child label input").prop('checked', 'checked');
                    }
                });
            }
        });
});
$("#add-addr").on("click", function (event) {
    event.preventDefault();
});