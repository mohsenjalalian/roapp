$isBusinessUnitDriver = $('#shipment-switch');
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
    $(window).on('load',function () {
        if($(".other-phone").val().length == 11) {
            var $otherPhone = $('.other-phone');
            // When sport gets selected ...
            $('.select-address').empty();
            // ... retrieve the corresponding form.
            shipmentForm = $('.panel');
            // Simulate form data, but only include the selected sport value.
            var data = {};
            data[$otherPhone.attr('name')] = $otherPhone.val();
            data[$otherPhone.attr('name')] = $otherPhone.val();
            // Submit data via AJAX to the form's action path.
            $.ajax({
                url: shipmentForm.attr('action'),
                type: 'POST',
                data: data,
                success: function (html) {
                    // Replace current position field ...
                    $('.select-address').replaceWith(
                        // ... with the returned one from the AJAX response.
                        $(html).find('.select-address')
                    );
                    var lastAddressSelected = $("#last_other_address_id_selected").html();
                    var lastPriceRegistered = $("#last_shipment_price_registered").html();
                    $("#cost_show").html(lastPriceRegistered);
                    $(".select-address input").filter(function(){return this.value== lastAddressSelected}).attr('checked', true);
                    $(".creator_shipment").attr('disabled', false);
                    // Position field now displays the appropriate positions.
                }
            });
        }
        // alert($("#last_driver_selected").html());
        if($("#last_driver_selected").html().length != 0 ) {
            var lastSelectedDriverId = $("#last_driver_selected").html();
            $isBusinessUnitDriver.prop('checked', true);
            $("#select-driver").prop('disabled',false);
            $("#select-driver").removeClass('disabled');
            var $form =  $('.panel');
            // Simulate form data, but only include the selected sport value.
            var data = {};
            data[$isBusinessUnitDriver.attr('name')] = $isBusinessUnitDriver.val();
            data['driverId'] = $("#last_driver_selected").html();

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: data,
                success: function (html) {
                    // Replace current position field ...
                    $('#restaurant_shipment_driver').replaceWith(
                        // ... with the returned one from the AJAX response.
                        $(html).find('#restaurant_shipment_driver')
                    );
                    var elem = $(".drivers label:contains('None')");
                    elem.empty();
                    elem.prepend('<input id="restaurant_shipment_driver_placeholder" name="restaurant_shipment[driver]" value="" checked="checked" type="radio">هیچکدام')
                    $("#restaurant_shipment_driver_30" ).prop('checked',true);
                    if ($(".drivers input:checked").val().length == 0) {
                        $("#cost_show").html("---");
                        $(".clac_price_section").css("display","block");
                        $('.creator_shipment').prop('disabled', true);
                    } else {
                        $(".clac_price_section").css("display","none");
                    }
                    // Position field now displays the appropriate positions.
                }
            });
        }
    });
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
                    $(".select-address input").first().attr('checked',true);
                    // Position field now displays the appropriate positions.
                }
            });
        }
    });
    // $isBusinessUnitDriver = $('#shipment-switch');
    $isBusinessUnitDriver.on('change', function (e) {
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        var data = {};
        data[$isBusinessUnitDriver.attr('name')] = $isBusinessUnitDriver.val();
        // Submit data via AJAX to the form's action path.
        if ($isBusinessUnitDriver.is(":checked")) {
            validate();
            // if ($(".drivers input:checked").val().length == 0) {
            //     console.log($(".drivers input:checked").val());
                // $('.creator_shipment').prop('disabled', true);
            // }

            // $('.creator_shipment').prop('disabled', true);
            // $("#cost_show").html("---");
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
                    if ($(".drivers input:checked").val().length == 0) {
                        $("#cost_show").html("---");
                        $(".clac_price_section").css("display","block");
                        $('.creator_shipment').prop('disabled', true);
                    }
                   var elem = $(".drivers label:contains('None')");
                   elem.empty();
                   elem.prepend('<input id="restaurant_shipment_driver_placeholder" name="restaurant_shipment[driver]" value="" checked="checked" type="radio">هیچکدام')
                    // Position field now displays the appropriate positions.
                }
            });
        } else  {
            // if (window.globalVar) {
                $(".clac_price_section").css("display","block");
                validate();
            // } else {
            //     $(".clac_price_section").css("display","block");
            //     $('.creator_shipment').prop('disabled', true);
            //
            // }
        }
    })
});
$('.businessUnit_driver_modal').on('hidden.bs.modal', function () {
    if($("#restaurant_shipment_driver input").is(":checked") && $(".drivers input:checked").val().length != 0 && $("#restaurant_shipment_value").val().length != 0 && $(".select-address").find(".empty-address").length == 0 && $(".other-phone").val().length == 11 ) {
        // validate();
        $(".clac_price_section").css("display","none");
        var useOwnerDriver = true;
        validate(useOwnerDriver);
        // $('.creator_shipment').prop('disabled', false);
    } else {
        $("#cost_show").html("---");
        $('.creator_shipment').prop('disabled', true);
    }
})

$(".calc_price_item, .select-address input").on('change', function () {
    validate();
});
function validate(useOwnerDriver) {
    useOwnerDriver = typeof useOwnerDriver !== 'undefined' ? useOwnerDriver : false;
    window.globalVar = false;
    var ownerAddressId = $('.owner-address').val();
    var otherAddressId = $('.select-address input:checked').val();
    var otherAddressLength = $('.select-address input').length;
    var shipmentValue = 1000;
    var shipmentPickUpTime = $(".duration").val();
    var restaurantShipmentValue = $("#restaurant_shipment_value").val()
    if (ownerAddressId.length !=0 && otherAddressLength !=0 && shipmentPickUpTime.length !=0 && restaurantShipmentValue.length !=0 && !$isBusinessUnitDriver.is(":checked")) {
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
        if (useOwnerDriver){
            $('.creator_shipment').prop('disabled', false);
        } else {
            if ($(".drivers input:checked").val().length != 0 && $isBusinessUnitDriver.is(":checked") && $("#restaurant_shipment_value").val().length != 0 && $(".select-address").find(".empty-address").length == 0 && $(".other-phone").val().length == 11){
                $(".clac_price_section").css("display","none");
                $('.creator_shipment').prop('disabled', false);
            } else {
                $("#cost_show").html("---");
                $('.creator_shipment').prop('disabled', true);
                window.globalVar = false;
            }
            // $("#cost_show").html("---");
            // $('.creator_shipment').prop('disabled', true);
            // window.globalVar = false;
        }
        // $("#cost_show").html("---");
        // $('.creator_shipment').prop('disabled', true);
        // window.globalVar = false;
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