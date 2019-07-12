/**
 * Created by anton on 06.05.14.
 */
$(document).ready(function () {

    $(document).on("submit", "form[name=quick-buy-form-element]", function (event) {

        if (window.product) {
            var colorId = product.currentColorId;
            var sizeId = product.currentSizeId;
            var productId = product.productId;

            $('form[name=quick-buy-form-element] input[name=colorId]').val(colorId);
            $('form[name=quick-buy-form-element] input[name=sizeId]').val(sizeId);
            $('form[name=quick-buy-form-element] input[name=productId]').val(productId);
        }

        $('form[name=quick-buy-form-element] input[name=url]').val(document.URL);

        $.post($(this).attr('action'), $(this).serialize(), function (data) {
            $('div.modal-scrollable div.OneClick').html(data);

        });
        return false;
    });

    UpdateBasketCatalog();
    $('form[name=quick-buy-form-element ] input[name=email]').val(window.JW_USER_EMAIL);

});

$(document).ready(function () {

    $(document).on("submit", "#quick-b-form", function (event) {

        if (window.product) {
            var colorId = product.currentColorId;
            var sizeId = product.currentSizeId;
            var productId = product.productId;

            $('#quick-b-form input[name=colorId]').val(colorId);
            $('#quick-b-form input[name=sizeId]').val(sizeId);
            $('#quick-b-form input[name=productId]').val(productId);
        } else {
            return false;
        }

        $('#quick-b-form input[name=url]').val(document.URL);

        $.post($(this).attr('action'), $(this).serialize(), function (data) {

            $("#fields").html(data);


        });
        return false;
    });

    //UpdateBasketCatalog();
    //$('form[name=quick-buy-form-element ] input[name=email]').val(window.JW_USER_EMAIL);

});
