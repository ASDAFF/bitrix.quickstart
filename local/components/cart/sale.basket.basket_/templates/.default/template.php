<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form">
<section class="b-detail">
<div class="b-detail-content">
    <div class="b-basket-button">
        <a class="b-catalog-sort__link b-catalog-sort__active" href="#" data-block="basket_items"><span>готовые к заказу</span></a>
        <a class="b-catalog-sort__link" href="#" data-block="basket_items_delay"><span>отложенные (<?=count($arResult["ITEMS"]["DelDelCanBuy"]);?>)</span></a>
    </div>
    <div id="basket_items">
    <? 
    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php"); ?>
    </div>    
    <div id="basket_items_delay"> 
    <?
    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delay.php");
    ?>
    </div>
</div>
</section>
</form> 