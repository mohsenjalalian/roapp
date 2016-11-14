/**
 * Created by msm on 11/14/16.
 */
$(document).ready(function () {
    $("#cancel_button").on('click',function () {
        $('.small.modal')
            .modal('show')
        ;
    });
    $("#accept_cancel_button").on('click',function () {
        var shipmentID = $("#shipment_id").val();
        $.ajax({
            url: 'cancel_shipment',
            data: {id:shipmentID},
            type: "POST",
            success: function (response) {
                $("#cancel_button").css("display","none");
            }
        });
    })
});
