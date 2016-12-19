$('#shipment-switch').on('click', function () {
    if  ($(this).prop('checked')==true) {
        $('#select-driver').removeAttr("disabled");
        $("#select-driver").removeClass("disabled");
    } else {
        $('#select-driver').attr("disabled", "true");
        $("#select-driver").addClass("disabled");
    }
});