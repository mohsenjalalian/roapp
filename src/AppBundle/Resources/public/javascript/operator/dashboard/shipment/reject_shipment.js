/**
 * Created by msm on 11/14/16.
 */
$(document).ready(function () {
    $(".reject_shipment_button").on('click',function () {
        var shipmentId = $(this).val();
        $("#accept_reject_button").data("value",shipmentId);
        $('.small.modal')
            .modal('show')
        ;
    });
    $("#accept_reject_button").on('click',function () {
        var shipmentID = $(this).data('value');
        $.ajax({
            url: 'reject',
            data: {id:shipmentID},
            type: "POST",
            success: function (response) {
                $("#alert_msg_reject_shipment-by-operator").empty();
                $(".shipment_assign_list"+response).remove();
                $("#alert_msg_reject_shipment-by-operator").append('<p>سفارش مورد نظر با موفقیت لغو گردید.</p>').fadeIn().delay(3000).fadeOut();
                $("#alert_msg_reject_shipment-by-operator").css('display','block');
            }
        });
    })
});