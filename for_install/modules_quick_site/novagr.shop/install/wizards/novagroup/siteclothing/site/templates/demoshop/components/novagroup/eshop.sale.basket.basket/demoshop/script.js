function addQuantity(quantinyLimit, quantityId, message) {
    var curValue = $("#QUANTITY_" + quantityId).val();

    if (curValue < quantinyLimit) {
        var newValue = ++curValue;
        $("#QUANTITY_" + quantityId).val(newValue);
        // ставим тултип если больше нет остатков
        if (newValue >= quantinyLimit) {
            $("#addQuantity_" + quantityId).attr("rel", "tooltip").attr("data-placement", "top").attr("data-original-title", message);
            $("[rel=tooltip]").tooltip({});
        }
    }
}
function minusQuantity(quantinyLimit, quantityId) {
    var curValue = $("#QUANTITY_" + quantityId).val();
    if (curValue > 1) {
        var newValue = --curValue;
        $("#QUANTITY_" + quantityId).val(newValue);
        $("#addQuantity_" + quantityId).removeAttr("rel").removeAttr("data-placement").removeAttr("data-original-title");
    }
}