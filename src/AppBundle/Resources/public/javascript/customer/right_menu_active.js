$(document).ready(function () {
    resetColors();
    var currentUrl = window.location.href;
    if (currentUrl.indexOf('shipment') >= 0) {
        $("#item1 a span").css("color", "white");
        $("#item1 a i").css("color", "#1D89CF");
    } else if (currentUrl.indexOf('period_invoice') >= 0){
        $("#item2 a span").css("color", "white");
        $("#item2 a i").css("color", "#1D89CF");
    }else if (currentUrl.indexOf('subscription_invoice') >= 0){
        $("#item3 a span").css("color", "white");
        $("#item3 a i").css("color", "#1D89CF");
    }else if (currentUrl.indexOf('address') >= 0){
        $("#item4 a span").css("color", "white");
        $("#item4 a i").css("color", "#1D89CF");
    }else if (currentUrl.indexOf('payment') >= 0){
        $("#item5 a span").css("color", "white");
        $("#item5 a i").css("color", "#1D89CF");
    }else if (currentUrl.indexOf('driver') >= 0){
        $("#item6 a span").css("color", "white");
        $("#item6 a i").css("color", "#1D89CF");
    }else if (currentUrl.indexOf('businessunit') >= 0){
        $("#item8 a span").css("color", "white");
        $("#item8 a i").css("color", "#1D89CF");
    } else if (currentUrl.indexOf('customer') >= 0 && currentUrl.indexOf('edit') >= 0){
        $("#item7 a span").css("color", "white");
        $("#item7 a i").css("color", "#1D89CF");
    }else {
        console.log('nothing');
    }
});

function resetColors() {
    $("#item1 a span").css("color", "");
    $("#item1 a i").css("color", "");
    $("#item2 a span").css("color", "");
    $("#item2 a i").css("color", "");
    $("#item3 a span").css("color", "");
    $("#item3 a i").css("color", "");
    $("#item4 a span").css("color", "");
    $("#item4 a i").css("color", "");
    $("#item5 a span").css("color", "");
    $("#item5 a i").css("color", "");
    $("#item6 a span").css("color", "");
    $("#item6 a i").css("color", "");
    $("#item7 a span").css("color", "");
    $("#item7 a i").css("color", "");
    $("#item8 a span").css("color", "");
    $("#item8 a i").css("color", "");
}
