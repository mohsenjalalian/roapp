$(document).ready(function () {
    $('#forgot-password-link').click(function () {
        $('#password-reset-form').fadeIn(400);
        return false;
    });
    $('#password-reset-form .close').click(function () {
        $('#password-reset-form').fadeOut(400);
        return false;
    });
});