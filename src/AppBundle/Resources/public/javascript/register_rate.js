$(document).ready(function () {
    $('#rate_save').on('click', function (e) {
        e.preventDefault();
        var formData = new FormData($('form')[0]);
        var formURL = $("#register_rate_form").attr('action');
        $.ajax(
            {
                url: formURL,
                data: formData,
                processData: false,
                contentType: false,
                type: "POST",
                success: function (response) {
                    if (response == 200) {
                        $("#rate_box").empty()
                        $("#rate_box").append('<h4 class="well">امتیاز شما ثبت شد با تشکر</h4>').fadeIn().delay(3000).fadeOut();
                    }
                }
            });
    })

});