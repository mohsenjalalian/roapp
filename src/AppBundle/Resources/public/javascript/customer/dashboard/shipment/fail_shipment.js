$(document).ready(function () {
    $("#fail_button_customer").on('click',function () {
        $('#fail_modal_customer')
            .modal('show')
        ;
    });
    $("#accept_fail_button_customer").on('click',function () {
        var shipmentId = $("#shipment_id").val();
        var failReason = $("#fail_customer_reason").val()
        $.ajax({
            url: 'fail_shipment',
            data: {id:shipmentId,reason:failReason},
            type: "POST",
            success: function (response) {
                $("#shipment_status_customer").html("لغو");
                $("#fail_button_customer").css("display","none");
                $("#cancel_button").css("display","none");
                $("#edit_button_customer_shipment").css("display","none");
            }
        });
    })
});
