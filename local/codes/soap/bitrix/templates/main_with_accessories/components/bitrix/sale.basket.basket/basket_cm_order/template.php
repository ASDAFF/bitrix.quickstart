<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/style.css">
<?
    if (StrLen($arResult["ERROR_MESSAGE"])<=0)
    {
        if(is_array($arResult["WARNING_MESSAGE"]) && !empty($arResult["WARNING_MESSAGE"]))
        {
            foreach($arResult["WARNING_MESSAGE"] as $v)
            {
                echo ShowError($v);
            }
        } 
    ?>

<div class="b-step-wrapper">
    <a href="#" class="b-step__link m-step__one">Корзина</a>
    <span class="b-step__sep">&mdash;</span>
    <a href="#" class="b-step__link m-step__two <?=($arResult['STEP'] == 2)? 'active': '';?>">Информация  о покупателе</a>
    <span class="b-step__sep">&mdash;</span>
    <a href="#" class="b-step__link m-step__three <?=($arResult['STEP'] == 3)? 'active': '';?>">Доставка</a>
    <span class="b-step__sep">&mdash;</span>
    <a href="#" class="b-step__link m-step__four <?=($arResult['STEP'] == 4)? 'active': '';?>">Оплата</a>
    </div>
 
 
 
    <div class="b-cart__list">
    <form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form">
        <?
            if ($arResult["ShowReady"]=="Y")
            {
                include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
            }

            if ($arResult["ShowDelay"]=="Y")
            {
                include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delay.php");
            }

            if ($arResult["ShowNotAvail"]=="Y")
            {
                include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_notavail.php");
            }

            if ($arResult["ShowSubscribe"] == "Y")
            {
                include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribe.php");
            }

        ?>
    </form>
    </div>
    <?
    }
    else
    if (!isset($_REQUEST["ORDER_ID"]))
        ShowError($arResult["ERROR_MESSAGE"]);
    
    
      if (StrLen($arResult["ERROR_MESSAGE"])<=0)
    {
?>
<div class="b-step-wrapper">
    <a href="#" class="b-step__link m-step__one">Корзина</a>
    <span class="b-step__sep">&mdash;</span>
    <a href="#" class="b-step__link m-step__two <?=($arResult['STEP'] == 2)? 'active': '';?>">Информация  о покупателе</a>
    <span class="b-step__sep">&mdash;</span>
    <a href="#" class="b-step__link m-step__three <?=($arResult['STEP'] == 3)? 'active': '';?>">Доставка</a>
    <span class="b-step__sep">&mdash;</span>
    <a href="#" class="b-step__link m-step__four <?=($arResult['STEP'] == 4)? 'active': '';?>">Оплата</a>
    </div>
 
<?}?>