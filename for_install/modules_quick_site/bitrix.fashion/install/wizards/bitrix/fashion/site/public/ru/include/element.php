<?
CModule::IncludeModuleEx('bitrix.fashion');

$isPriceShow = true;
foreach ($arResult["OFFERS_COMPACT"] as $color => $arr) {
    foreach ($arr["SIZES"] as $key => $size) {?>
        <div id="<?=$color . '-' . $size["PRODUCT_ID"]?>" class="<?if($isPriceShow && $color == $defaultColor){$isPriceShow = false; echo "show ";}else{echo "hide ";}?>block price<?=($size["PRICE"] != $size["DISCOUNT"] ? ' new' : '')?>" itemprop="offerDetails" itemscope itemtype="http://schema.org/Offer">
            <?if ($size["PRICE"] != $size["DISCOUNT"]) {?>
            <p class="oldprice"><?=CSiteFashionStore::formatMoney($size["PRICE"])?> <span class="rub">Ð</span></p>
            <?} else {?>
            <p>&nbsp;</p>
            <?}?>
            <form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
            <p class="current-price">
                <span itemprop="price" class="item-price"><?=CSiteFashionStore::formatMoney($size["DISCOUNT"])?></span>&nbsp;<span class="rub">Ð</span>
                <span class="times">X</span>
                <input type="text" name="<?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>" class="item-quantity" value="1" />
                <span class="count"> <?=GetMessage("CT_BCE_QUANTITY")?></span>
                <input type="submit" class="add-to-cart" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" value="<?=GetMessage("CT_BCE_CATALOG_ADD")?>" />
                <input type="hidden" name="<?=$arParams["ACTION_VARIABLE"]?>" value="BUY">
                <input class="h_id" type="hidden" name="<?=$arParams["PRODUCT_ID_VARIABLE"]?>" value="<?=$size["PRODUCT_ID"]?>">
            </p>
            <meta itemprop="currency" content="RUB" />
            </form>
        </div>
    <?}
}?>
<script>
    $(".add-to-cart").click(function(){
        cur_pr = $(this).parents('.current-price');
        itemID = cur_pr.find('.h_id').val();
        q = cur_pr.find('.item-quantity');
        if(itemID>0&&q.val()>0){
            $.post('/ajax/index.php', {id: itemID, q: q.val() }, function(data) {$('#top-cart').html(data);q.val('1')});
        }

        $("#cart-confirm h3 strong").html("<?=$arResult['NAME']?>");
        $("#cart-image").attr("src",$("#thumbs li:visible:first img").attr("src"));
        $('#cart-color').empty();
        $('#color li.selected a').clone().appendTo('#cart-color');
        $('#cart-color a').attr('href', 'javascript:void()');
        $('#cart-color img').css('width', 24).css('height', 16);
        $("#cart-size").text($("#size li.selected span").text());
        $("#cart-price").text(cur_pr.find('.item-price').text());
        $("#cart-quantity").text(q.val());
        $("#cart-overall").text(parseInt(cur_pr.find('.item-price').text())*parseInt($("#cart-quantity").text()));
        $("#cart-confirm").show().css("top",$(window).scrollTop()+($(window).height() - $("#cart-confirm").height())/2);
        $("#overlay").show();

        return false;
    })
</script>