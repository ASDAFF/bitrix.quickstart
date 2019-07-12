<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="adbasket mainDivOneClick">
    <!-- <a id="btnsel" class="btnsel" href="/catalog/wl-dresses/dress_olivegrey/?action=ADD2BASKET&amp;id=572"><i class="icon-plus"></i> ???????? ? ???????</a>-->
    <div class="oneClick">
        <a id="btnclick" class="" href="#oneClick"
           data-toggle="modal"><?= GetMessage('BUY_ONE_CLICK') ?></a>

    </div>
    <div aria-hidden="false" role="dialog" tabindex="-1"
         class="modal hide fade autorize in OneClick" id="oneClick">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h3><?= GetMessage('BUY_ONE_CLICK') ?></h3>
        </div>
        <div class="modal-body">
            <form autocomplete="on" action="<?= SITE_DIR ?>include/catalog/element/quick-buy.php" name="quick-buy-form-element"
                  method="post">
                <input type="hidden" name="colorId" value="0">
                <input type="hidden" name="sizeId" value="0">
                <input type="hidden" name="productId" value="0">
                <input type="hidden" name="url" value="">
                <div>
                    <div class="login">
                        <div class="name">
                            E-mail
                        </div>
                        <div class="value">
                            <input type="text" autocomplete="on" value="<?= $arParams['USER_EMAIL'] ?>" maxlength="50"
                                   name="email">
                        </div>
                        <div class="name"><?= GetMessage('PHONE') ?>: <span class="starrequired"> * </span></div>
                        <div class="value"><input type="text" value="" name="phone" size="30"></div>
                    </div>

                </div>
                <div class="sub-div-n">
                    <input type="submit" value="<?=GetMessage('BUY')?>" class="btn">
                </div>
            </form>
            <div class="clear"></div>

        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function(){

        $(document).on("submit", "form[name=quick-buy-form-element]", function(event){

            if (window.product) {
                var colorId = product.currentColorId;
                var sizeId = product.currentSizeId;
                var productId = product.productId;

                $('form[name=quick-buy-form-element] input[name=colorId]').val(colorId);
                $('form[name=quick-buy-form-element] input[name=sizeId]').val(sizeId);
                $('form[name=quick-buy-form-element] input[name=productId]').val(productId);
            }

            $('form[name=quick-buy-form-element] input[name=url]').val(document.URL);

            $.post($(this).attr('action'), $(this).serialize(), function(data) {
                $('div.modal-scrollable div.OneClick').html(data);

            });
            return false;
        });

        UpdateBasketCatalog();
        $('form[name=quick-buy-form-element ] input[name=email]').val(window.JW_USER_EMAIL);

    });

</script>
