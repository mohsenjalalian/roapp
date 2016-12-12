$(document).ready(function () {
    $("#alert_msg_edit_driver_operator").css('display','none');
    $("#drive_edit_form_operator").on('submit', function (e) {
        e.preventDefault();
        var currentPasswordLength = $("#driver_currentPassword").val().length;
        var newPasswordLength = $("#driver_newPassword_first").val().length;
        var repeatedPasswordLength = $("#driver_newPassword_second").val().length;
        var formData = new FormData($('form')[1]);
        var formURL = $(this).attr("action");
        if (currentPasswordLength == 0 && newPasswordLength == 0 && repeatedPasswordLength == 0 ) {
            $.ajax({
                url: formURL,
                data: formData,
                processData: false,
                contentType: false,
                type: "POST",
                success: function (response) {
                  location.reload();
                }
            })
        } else {
            var newPassword = $("#driver_newPassword_first").val();
            var repeatedPassword = $("#driver_newPassword_second").val();
            $("#alert_msg_edit_driver_operator").empty();
            if (currentPasswordLength == 0) {
                $("#alert_msg_edit_driver_operator").append('<strong>خطا!!</strong><br><p>رمز فعلی نمی تواند خالی باشد</p>');
                $("#alert_msg_edit_driver_operator").css('display','block');
            } else if (newPasswordLength == 0) {
                $("#alert_msg_edit_driver_operator").append('<strong>خطا!!</strong><br><p>رمز عبور جدید نمی تواند خالی باشد</p>');
                $("#alert_msg_edit_driver_operator").css('display','block');
            } else if (repeatedPasswordLength == 0) {
                $("#alert_msg_edit_driver_operator").append('<strong>خطا!!</strong><br><p>تکرار رمز عبور جدید نمی تواند خالی باشد</p>');
                $("#alert_msg_edit_driver_operator").css('display','block');
            } else if (newPassword != repeatedPassword) {
                $("#alert_msg_edit_driver_operator").append('<strong>خطا!!</strong><br><p>رمز عبور جدید و تکرار آن باید یکی باشند</p>');
                $("#alert_msg_edit_driver_operator").css('display','block');
            } else {
                $.ajax({
                    url: formURL,
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: "POST",
                    success: function (response) {
                        location.reload();
                    }
                })
            }
        }
    })
});