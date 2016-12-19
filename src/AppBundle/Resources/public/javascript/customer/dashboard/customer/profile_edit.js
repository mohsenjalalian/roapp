$(document).ready(function () {
    $("#alert_msg_edit_customer_profile").css('display','none');
    $("#profile_edit_form_customer").on('submit', function (e) {
        e.preventDefault();
        var currentPasswordLength = $("#customer_currentPassword").val().length;
        var newPasswordLength = $("#customer_newPassword_first").val().length;
        var repeatedPasswordLength = $("#customer_newPassword_second").val().length;
        var formData = new FormData($('form')[1]);
        var formURL = $(this).attr("action");
        // user doesn't want to change pass
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
        } else {  // user wants change password
            var newPassword = $("#customer_newPassword_first").val();
            var repeatedPassword = $("#customer_newPassword_second").val();
            $("#alert_msg_edit_customer_profile").empty();
            if (currentPasswordLength == 0) {
                $("#alert_msg_edit_customer_profile").append('<strong>خطا!!</strong><br><p>رمز فعلی نمی تواند خالی باشد</p>').fadeIn().delay(3000).fadeOut();
                $("#alert_msg_edit_customer_profile").css('display','block');
            } else if (newPasswordLength == 0) {
                $("#alert_msg_edit_customer_profile").append('<strong>خطا!!</strong><br><p>رمز عبور جدید نمی تواند خالی باشد</p>').fadeIn().delay(3000).fadeOut();
                $("#alert_msg_edit_customer_profile").css('display','block');
            } else if (repeatedPasswordLength == 0) {
                $("#alert_msg_edit_customer_profile").append('<strong>خطا!!</strong><br><p>تکرار رمز عبور جدید نمی تواند خالی باشد</p>').fadeIn().delay(3000).fadeOut();
                $("#alert_msg_edit_customer_profile").css('display','block');
            } else if (newPassword != repeatedPassword) {
                $("#alert_msg_edit_customer_profile").append('<strong>خطا!!</strong><br><p>رمز عبور جدید و تکرار آن باید یکی باشند</p>').fadeIn().delay(3000).fadeOut();
                $("#alert_msg_edit_customer_profile").css('display','block');
            } else {
                $.ajax({
                    url: formURL,
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: "POST",
                    success: function (response) {
                        // user entered wrong current password or no ??
                        if (response == "current password is wrong"){
                            $("#alert_msg_edit_customer_profile").append('<strong>خطا!!</strong><br><p>رمز فعلی وارد شده صحیح نمی باشد.</p>').fadeIn().delay(3000).fadeOut();
                            $("#alert_msg_edit_customer_profile").css('display','block');
                        } else {
                            location.reload();
                        }
                    }
                })
            }
        }
    })
});