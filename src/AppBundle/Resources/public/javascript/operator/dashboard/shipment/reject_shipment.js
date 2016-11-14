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
                $(".shipment_assign_list"+response).remove();
            }
        });
    })
});