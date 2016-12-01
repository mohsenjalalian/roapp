$(document).ready(function () {
    $("#pay_confirm_button_operator").unbind('click').bind('click',function () {
        var paymentId = $("#payment_id_operator").val();
        $.ajax({
            url: 'pay_confirm',
            data: {paymentId:paymentId},
            type: "POST",
            success: function (response) {
                var result = JSON.parse(response);
                if(result) {
                    $("#pay_confirm_button_operator").remove();
                    $("#pay_confirm_section").prepend('<h4 style="color: green"><i style="color: green;font-size: large" class="checkmark icon"></i> <span>تایید شده</span> </h4>');
                }
            }
        })
    })
});