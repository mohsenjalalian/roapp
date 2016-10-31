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
                                    $("#reciver_info_box").prepend(' <div style="margin-top:10px;margin-bottom: 10px"><input checked id="" type="radio"  name="reciver_public_address" value="' + ci + '"><span>' + label + '</span></div>')
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
$(document).ready(function () {
    $("#shipment_other").on('keyup',function () {
        $('a[name*=otherFormModal]').css("display","none");
        if($(this).val().length === 0) {
            $('#show_reciver_address_btn').prop('checked', false);
            $('#show_reciver_address_btn').prop('disabled', true);
            $("#reciver_info_box").css("display","none");
            $("#noAddress").css("display",'none');
        }
        else {
            $("#show_reciver_address_btn").prop('disabled', false);
            if($('#show_reciver_address_btn').prop('checked')==true){
                $('a[name*=otherFormModal]').css("display","inline");
                var phoneNumber = $("#shipment_other").val();
                $("#reciver_info_box").empty();
                $.ajax({
                    url: 'get_customer_address',
                    data: {phoneNumber:phoneNumber},
                    type: "POST",
                    success: function (response) {
                        if (response == "there is no address"){
                            $("#noAddress").css("display",'block');
                        } else {
                            var res = JSON.parse(response);
                            $.each(res, function (ind, val) {
                                $("#reciver_info_box").prepend(
                                    ' <div style="margin-top:10px">' +
                                    '<input type="radio"  name="reciver_public_address"  value="' + ind + '">' +
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

    $("#show_reciver_address_btn").on('click',function () {
        $('a[name*=otherFormModal]').css("display","none");
        if($('#show_reciver_address_btn').prop('checked')==true) {
            var phoneNumber = $("#shipment_other").val();
            // var l = window.location;
            // var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];
            // console.log(base_url+"/web/app_dev.php/customer/dashboard/");
            $("#reciver_info_box").empty();
            $.ajax({
                url: 'get_customer_address',
                data: {phoneNumber:phoneNumber},
                type: "POST",
                success: function (response) {
                    if (response == "there is no address"){
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
// claculate price when click on calc button
$("#calculate_price").on('click',function (e) {
    e.preventDefault();
    var ownerAddressId = $('input[name=publicAddress]:checked').val();
    var otherAddressId = $('input[name=reciver_public_address]:checked').val();
    var shipmentValue = $("#shipment_value").val();
    var shipmentPickUpTime = $("#shipment_pickUpTime").val();

    $.ajax({
        url: 'calc_shipment_price',
        data: {
            ownerAddressId:ownerAddressId,
            otherAddressId:otherAddressId,
            shipmentValue:shipmentValue,
            shipmentPickUpTime:shipmentPickUpTime
        },
        type: "POST",
        success: function (response){
            $("#calculate_price").html("قیمت کل :");
            $("#cost_show").html(response);
            $("#cost_show").css('display','inline');
            $('.creator_shipment').prop('disabled',false);
        }
    });
 // $('.creator_shipment').prop('disabled',false);
});

