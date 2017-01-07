$(document).ready(function () {
    $(".cancel_shipment_button").on('click',function () {
        var shipmentId = $(this).val();
        $("#accept_cancel_button").data("value",shipmentId);
        $('#cancel_modal_customer')
            .modal('show')
        ;
    });
    $("#accept_cancel_button").on('click',function () {
        var shipmentID = $(this).data('value');
        $.ajax({
            url: 'cancel_shipment',
            data: {id:shipmentID},
            type: "POST",
            success: function (response) {
                if (response == 400) {
                    $("#alert_msg_cancel_shipment-by-customer").empty();
                    $("#alert_msg_cancel_shipment-by-customer").append('<p>خطا لطفا دوباره تلاش کنید.</p>').fadeIn().delay(3000).fadeOut();
                    $("#alert_msg_cancel_shipment-by-customer").css('display', 'block');
                }
                else {
                    var result = JSON.parse(response);
                    $("#alert_msg_cancel_shipment-by-customer").empty();
                    $(".customer_shipment_status" + result).html("کنسل شده توسط مشتری");
                    $(".customer_shipment_cancel_btn" + result).remove();
                    $("#alert_msg_cancel_shipment-by-customer").append('<p>سفارش مورد نظر با موفقیت کنسل شد.</p>').fadeIn().delay(3000).fadeOut();
                    $("#alert_msg_cancel_shipment-by-customer").css('display', 'block');
                }
            }
        });
    })
});
