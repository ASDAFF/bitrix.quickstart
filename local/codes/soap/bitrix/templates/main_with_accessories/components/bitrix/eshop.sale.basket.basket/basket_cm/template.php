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
	$arUrlTempl = Array(
		"delete" => $APPLICATION->GetCurPage()."?action=delete&id=#ID#",
		"shelve" => $APPLICATION->GetCurPage()."?action=shelve&id=#ID#",
		"add" => $APPLICATION->GetCurPage()."?action=add&id=#ID#",
	);
    ?>

    <div class="b-step-wrapper">
        <a href="#" class="b-step__link m-step__one active">Корзина</a>
        <span class="b-step__sep">&mdash;</span>
        <a href="#" class="b-step__link m-step__two">Информация  о покупателе</a>
        <span class="b-step__sep">&mdash;</span>
        <a href="#" class="b-step__link m-step__three">Доставка</a>
        <span class="b-step__sep">&mdash;</span>
        <a href="#" class="b-step__link m-step__four">Оплата</a>
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

            /*if ($arResult["ShowNotAvail"]=="Y")
            {
                include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_notavail.php");
            }*/

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
        ShowError($arResult["ERROR_MESSAGE"]);
?>