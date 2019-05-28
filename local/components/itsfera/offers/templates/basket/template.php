<?	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?
    if(empty($arResult['PRODUCTS'] )){
        return;
    }
?>
<div class="recently_viewed block js-fit">
    <div class="title"><?=$arParams["NAME"]?></div>
    <?
        $i = 0;
        foreach($arResult['PRODUCTS'] as $product){
            //$product = MHT\Product::byId($element['PRODUCT_ID']);
            echo $product->moreFields($element)->html('catalog', array(
                'tpl' => $this,
                'i' => $i++
            ));
            if($i >= 6) break;
        }
    ?>
</div>