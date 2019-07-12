<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();$this->setFrameMode(true);
?>
<div class="adbasket mainDivOneClick">

    <div aria-hidden="false" role="dialog" tabindex="-1"
         class="modal hide fade autorize in OneClick" id="oneClickCart">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h3><?= GetMessage('BUY_ONE_CLICK') ?></h3>
        </div>
        <div class="modal-body">
            <form autocomplete="on" action="<?= SITE_DIR ?>include/catalog/cabinet/quick-buy.php" name="quick-buy-form"
                  method="post">
                <input type="hidden" name="CAJAX" value="1">
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

        $(document).on("submit", "form[name=quick-buy-form]", function(event){

            $('form[name=quick-buy-form]:visible input[name=url]').val(document.URL);
            $.post($('form[name=quick-buy-form]:visible').attr('action'), $('form[name=quick-buy-form]:visible').serialize(), function (data) {
                $('div.modal-scrollable div.OneClick').html(data);

            });
            return false;
        });
        $('form[name=quick-buy-form] input[name=email]').val(window.JW_USER_EMAIL);
    });


</script>
<??>