function shelveInit() {
    $("a[data-action=addToShelve]").unbind("click");
    $("a[data-action=addToShelve]").click(function () {
        var isAdded = $(this).hasClass("ADDED_SHELVE_PRODUCT");
        var elemid = $("#SHELVE_PRODUCT").attr("data-elem-id");
        $.post(JAVASCRIPT_SITE_DIR + "include/ajax/add2basket.php", "ADD2BASKET=1&elemId=" + elemid + "&act=addToShelve", function (data) {
            $("#box-shelve, #box-shelve .message-demo, .DIV_SHELVE_PRODUCT").hide();
            if (data.status == 'OK') {
                if (isAdded)
                    $("#box-shelve, #box-shelve .added-success, .DELETED_SHELVE_PRODUCT").show();
                else
                    $("#box-shelve, #box-shelve .deleted-success, .ADDED_SHELVE_PRODUCT").show();
            } else {
                if (isAdded)
                    $("#box-shelve, #box-shelve .added-error, .ADDED_SHELVE_PRODUCT").show();
                else
                    $("#box-shelve, #box-shelve .deleted-error, .DELETED_SHELVE_PRODUCT").show();
            }
            setTimeout(function () {
                $("#box-shelve").hide();
            }, 2500);
        }, "JSON")
        return false;
    });
}

$(document).ready(function () {
    shelveInit();
});
$(document).ajaxComplete(function () {
    shelveInit();
});