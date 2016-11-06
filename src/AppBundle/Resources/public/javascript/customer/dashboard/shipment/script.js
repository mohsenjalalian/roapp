// add address whit modal for owner shipment
$(".address_modal").on('click',function () {
    $("#load_form_place").empty();
    $("#address").val("");
    var formName = $(this).attr ('name');
    if (formName == 'ownerFormModal') {
        $.ajax(
            {
                url: "load_owner_form",
                data: 1,
                processData: false,
                contentType: false,
                type: "POST",
                success: function (response) {
                    $("#load_form_place").html(response);
                    $("#add_address").on('submit', function (event) {
                        event.preventDefault();
                        var fd = new FormData($('form')[1]);
                        var formURL = $(this).attr("action");
                        $.ajax(
                            {
                                url: formURL,
                                data: fd,
                                processData: false,
                                contentType: false,
                                type: "POST",
                                success: function (response) {
                                    var res = JSON.parse(response);
                                    var arr = [];
                                    $.each(res, function (ind, val) {
                                        if (ind == 'description') {
                                            arr['desc'] = val;
                                        } else if (ind == 'cId') {
                                            arr['customerId'] = val;
                                        } else if (ind == 'isPublic') {
                                            arr['isPublic'] = val;
                                        }
                                        return arr;
                                    });
                                    var ci = arr['customerId'];
                                    var label = arr['desc'];
                                    $("#address_show_section").prepend(' <div style="margin-top:10px;margin-bottom: 10px"><input checked id="" type="radio"  name="publicAddress" value="' + ci + '"><span>' + label + '</span></div>')
                                    $("#address").val('');
                                    $("#address_isPublic").prop('checked', false);
                                    $('.ui.modal').modal('hide');
                                }
                            });
                    });
                }
            });
        // add address with modal for reciver shipment
    } else {
        var phoneNumber = $("#shipment_other").val();
        $.ajax(
            {
                url: "load_other_form",
                data: "owner=" + phoneNumber,
                processData: false,
                contentType: false,
                type: "GET",
                success: function (response) {
                    $("#load_form_place").html(response);
                    $("#add_address #address").append('<input id="reciver_customer_mobile_number"  name="shipment_otherAddress_number" type="hidden" value="'+phoneNumber+'">');
                    $("#add_address").on('submit', function (event) {
                        event.preventDefault();
                        var fd = new FormData($('form')[1]);
                        var formURL = $(this).attr("action");
                        $.ajax(
                            {
                                url: formURL,
                                data:fd,
                                processData: false,
                                contentType: false,
                                type: "POST",
                                success: function (response) {
                                    var res = JSON.parse(response);
                                    var arr = [];
                                    $.each(res, function (ind, val) {
                                        if (ind == 'description') {
                                            arr['desc'] = val;
                                        } else if (ind == 'cId') {
                                            arr['customerId'] = val;
                                        } else if (ind == 'isPublic') {
                                            arr['isPublic'] = val;
                                        }
                                        return arr;
                                    });
                                    var ci = arr['customerId'];
                                    var label = arr['desc'];
                                    $("#reciver_info_box").prepend(' <div style="margin-top:10px;margin-bottom: 10px"><input id="" type="radio" name="reciver_public_address" value="' + ci + '"><span>' + label + '</span></div>')
                                    $("#noAddress").css("display",'none');
                                    $("#address").val('');
                                    // $("#address_isPublic").prop('checked', false);
                                    $('.ui.modal').modal('hide');
                                }
                            });
                    });
                }
            });
    }
    // owner address modal initial for show
    $('#address_modal_form')
        .modal({
            inverted: true
        })
        .modal("show")
    ;
    google.maps.event.trigger(map,'resize');

});
$('.ui.accordion')
    .accordion();
// show reciver address(if address exist OR create address for that) when user enter phone number
$(document).ready(function () {
    $("#shipment_other").on('keyup',function () {
        $('a[name*=otherFormModal]').css("display","none");
        // if input box (where reciver's phone number entered by user) is empty
        if($(this).val().length === 0) {
            $('#show_reciver_address_btn').prop('checked', false);
            $('#show_reciver_address_btn').prop('disabled', true);
            $("#reciver_info_box").css("display","none");
            $("#noAddress").css("display",'none');
        }
            // if input box is fill
        else {
            $("#show_reciver_address_btn").prop('disabled', false);
            // if user click button for show addresses AND send ajax request for get addresses if exist
            if($('#show_reciver_address_btn').prop('checked')==true){
                $('a[name*=otherFormModal]').css("display","inline");
                var phoneNumber = $("#shipment_other").val();
                $("#reciver_info_box").empty();
                $.ajax({
                    url: 'get_customer_address',
                    data: {phoneNumber:phoneNumber},
                    type: "POST",
                    success: function (response) {
                        // if there is no number for this phone number
                        if (response == "there is no address"){
                            dataReady();
                            $("#noAddress").css("display",'block');
                        // if there is number for this phone number
                        } else {
                            var res = JSON.parse(response);
                            $.each(res, function (ind, val) {
                                $("#reciver_info_box").prepend(
                                    ' <div style="margin-top:10px">' +
                                    '<input  type="radio"  name="reciver_public_address"  value="' + ind + '">' +
                                    '<span>' + val + '</span>' +
                                    '</div>'
                                )
                            })
                        }
                    }
                });
                $("#reciver_info_box").css("display","inline");
                $("#noAddress").css("display",'none');
            }
        }
    });
    // show reciver address (if exist) when user click on switch button
    $("#show_reciver_address_btn").on('click',function () {
        $('a[name*=otherFormModal]').css("display","none");
        if($('#show_reciver_address_btn').prop('checked')==true) {
            var phoneNumber = $("#shipment_other").val();
            $("#reciver_info_box").empty();
            $.ajax({
                url: 'get_customer_address',
                data: {phoneNumber:phoneNumber},
                type: "POST",
                success: function (response) {
                    if (response == "there is no address"){
                        dataReady();
                        $("#noAddress").css("display",'block');
                        $('a[name*=otherFormModal]').css("display","inline");
                    } else {
                        var res = JSON.parse(response);
                        $.each(res, function (ind, val) {
                            $("#reciver_info_box").prepend(
                                ' <div style="margin-top:10px">' +
                                '<input type="radio"  name="reciver_public_address"  value="' + ind + '">' +
                                '<span>' + val + '</span>' +
                                '</div>'
                            )
                        });
                        $("#noAddress").css("display",'none');
                        $('a[name*=otherFormModal]').css("display","inline");
                    }
                }
            });
            $("#reciver_info_box").css("display","inline");
        }
        else {
            $("#reciver_info_box").css("display","none");
            $("#noAddress").css("display",'none');
        }
    });
    // reciver address modal initial for show
    $("#reciver_modal_link").on('click',function () {
        $('#reciver_modal_form')
            .modal({
                inverted: true
            })
            .modal("show")
        ;
        google.maps.event.trigger(map,'resize')
    });
});
// claculate price with ajax method section
$(".calc_price_item").on('change', function () {
    $(".price_send").empty();
    if (dataReady()) {
        var ownerAddressId = $('input[name=publicAddress]:checked').val();
        var otherAddressId = $('input[name=reciver_public_address]:checked').val();
        var shipmentValue = $("#shipment_value").val();
        var shipmentPickUpTime = $("#shipment_pickUpTime").val();
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
                    '<input name="price_shipment" type="hidden" value="'+response+'">'
                );
                $('.creator_shipment').prop('disabled', false);
            }
        });
    } else {
        $("#cost_show").html("---")
        $("#cost_show").css('display', 'inline');
        $('.creator_shipment').prop('disabled', true);
    }
});
function isExistAddress() {
    var phoneNumber = $("#shipment_other").val();
    $.ajax({
        url: 'get_customer_address',
        data: {phoneNumber: phoneNumber},
        type: "POST",
        success: function (response) {
            if(response == "there is no address") {
                window.globalVar = "no" ;
            } else {
                window.globalVar = "yes" ;
            }
        }
    });

    return globalVar;
}
function dataReady() {
    if ($("#shipment_other").val().length == 0) {
        $('input[name=reciver_public_address]:checked').val("");
        return false;
    }
    if (isExistAddress() == "no"){
        if (!$('input[name=reciver_public_address]:checked').val()) {
            $('input[name=reciver_public_address]:checked').val("");
            return false;
        }
    }
    if ($('#show_reciver_address_btn').prop('checked')==false) {
        $('input[name=reciver_public_address]:checked').val("");
        return false;
    }
    var ownerAddressId = $('input[name=publicAddress]:checked').val();
    var otherAddressId = $('input[name=reciver_public_address]:checked').val();
    var shipmentValue = $("#shipment_value").val();
    var shipmentPickUpTime = $("#shipment_pickUpTime").val();
    if (ownerAddressId.length != 0 && otherAddressId.length != 0 && shipmentValue.length !=0 && shipmentPickUpTime.length != 0) {
        return true;
    } else {
        return false;
    }
}


