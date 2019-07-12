<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<form method="post" id="quick-b-form" action="<?= SITE_DIR ?>include/catalog/element/quick-buy-landing.php">
    <input type="hidden" name="colorId" value="0">
    <input type="hidden" name="sizeId" value="0">
    <input type="hidden" name="productId" value="0">
    <input type="hidden" name="url" value="">


    <div id="fields">
    <?
    include('form.php');
    ?>
    </div>
    <div class="actual-price">
        <div><span class="old-price" id="old-price"></span></div>
        <div><span class="discount" id="sum"></span>
            <span class="icon-arrow-right"></span>
            <span id="buy-btn-span">
            <input class="btn bt3 bt-lan" type="submit" value="<?=GetMessage('BUY_NOW')?>">
            </span>
        </div>
        <div class="clear"></div>
        <div style="display: none;" id="buy-popup">
            <div id="message-demo" class="message-demo"></div>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(document)
        .ready(
        function () {

            $('#quick-b-form')
                .validate(
                {
                    rules: {

                        name: {
                            minlength: 2,
                            required: true
                        },
                        email: {
                            minlength: 2,
                            required: true
                        },
                        phone: {
                            minlength: 2,
                            required: true
                        }

                    },
                    highlight: function (element) {
                        $(
                            element)
                            .closest(
                                '.control-group')
                            .removeClass(
                                'success')
                            .addClass(
                                'error');
                    },
                    success: function (element) {
                        element
                            .text(
                                'OK!')
                            .addClass(
                                'valid')
                            .closest(
                                '.control-group')
                            .removeClass(
                                'error')
                            .addClass(
                                'success');
                    }
                });
        }); // end document.ready
</script>