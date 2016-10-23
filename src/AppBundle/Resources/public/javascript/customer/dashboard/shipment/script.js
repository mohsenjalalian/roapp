$("#address_modal").on('click',function () {
    $('#address_modal_form')
        .modal({
            inverted: true
        })
        .modal("show")
    ;
});

$("#add_address").submit(function (event) {
    event.preventDefault();
    var fd = new FormData($('form')[1]);
    var formURL = $(this).attr("action");
    $.ajax(
        {
            url : formURL,
            data : fd,
            processData: false,
            contentType: false,
            type: "POST",
            success:function(response) {
               var res = JSON.parse(response);
                var arr = [];
                $.each(res,function (ind,val) {
                    if (ind == 'description'){
                        arr['desc'] = val;
                    } else if(ind == 'cId') {
                        arr['customerId'] = val;
                    } else if(ind == 'isPublic') {
                        arr['isPublic'] = val;
                    }
                    return arr;
                });
                var ci = arr['customerId'];
                var label = arr['desc'];
                $("#address_show_section").prepend(' <div style="margin-top:10px;margin-bottom: 10px"><input checked id="" type="radio"  name="publicAddress" value="'+ci+'"><span>'+label+'</span></div>')
                $("#pac-input").val('');
                $("#address_isPublic").prop('checked', false);
                $('.ui.modal').modal('hide');
            }
        });
});
$('.ui.accordion')
    .accordion();