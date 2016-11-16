/**
 * Created by msm on 11/14/16.
 */
$(document).ready(function () {
    $("#cancel_button").on('click',function () {
        $('#cancel_modal_customer')
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
                $("#shipment_status_customer").html("کنسل");
                $("#fail_button_customer").css("display","none");
                $("#cancel_button").css("display","none");
                $("#edit_button_customer_shipment").css("display","none");
                $("#valid_code_form_section").remove();
            }
        });
    })
});
