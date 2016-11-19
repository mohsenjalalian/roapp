$(document).ready(function () {
   $("#send_code_button").unbind('submit').bind('submit', function (e) {
       e.preventDefault();
       var fd = new FormData($('form')[1]);
       var formURL = $(this).attr("action");
       $("#not_valid_code").remove();
       $.ajax(
           {
               url: formURL,
               data: fd,
               processData: false,
               contentType: false,
               type: "POST",
               success: function (response){
                   var result = JSON.parse(response);
                   if (result) {
                       $("#valid_code_form_section").empty();
                       $("#valid_code_form_section").append("<div class='ui compact segment' style='margin-top: 10px;'><h5 style='color: green;'>کد معتبر می باشد</h5></div>").show().delay(5000).fadeOut();
                   } else {
                       $("#valid_code_form_section").append("<div id='not_valid_code' class='ui compact segment' style='margin-top: 10px;'><h5 style='color: red;'>کد معتبر نمی باشد</h5></div>")
                       $("#not_valid_code").show().delay(2000).fadeOut();
                   }
               }
           });
   })
});