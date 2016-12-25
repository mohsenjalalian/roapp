/**
 * Created by mohsenjalalian on 12/24/16.
 */
$(document).ready(function() {
    $(".js-datepicker").pDatepicker(
        {
            timePicker: {
                enabled: true,
                showSeconds: true,
                showMeridian: true,
                scrollEnabled: true,
                showOn: 'button',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd-mm-yy',
                yearRange: "-0:+1"
            }
        }
    );
});
$(document).ready(function() {
    var $otherPhone = $('.other-phone');
    // When sport gets selected ...
    $otherPhone.on('keyup', function () {
        // ... retrieve the corresponding form.
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        var data = {};
        data[$otherPhone.attr('name')] = $otherPhone.val();
        data[$otherPhone.attr('name')] = $otherPhone.val();
        // Submit data via AJAX to the form's action path.
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: data,
            success: function (html) {
                // Replace current position field ...
                $('.select-address').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('.select-address')
                );
                // Position field now displays the appropriate positions.
            }
        });
    });
});

$(".calc_price_item").on('change', function () {
    var ownerAddressId = $('.owner-address').val();
    var otherAddressId = $('.select-address input:checked').val();
    var shipmentValue = 1000;
    var shipmentPickUpTime = $(".js-datepicker").val();
    $.ajax({
        url: 'calc_shipment_price',
        data: {
            ownerAddressId: ownerAddressId,
            otherAddressId: otherAddressId,
            shipmentValue: shipmentValue,
            shipmentPickUpTime: shipmentPickUpTime
        },
        type: "POST",
        success: function (response) {
            $("#calculate_price").html("<b>قیمت کل :</b>");
            $("#cost_show").html(response);
            $("#cost_show").css('display', 'inline');
            $(".price_send").prepend(
                '<input name="price_shipment" type="hidden" value="'+response+'">'
            );
                $('.creator_shipment').prop('disabled', false);
        }
    });
});