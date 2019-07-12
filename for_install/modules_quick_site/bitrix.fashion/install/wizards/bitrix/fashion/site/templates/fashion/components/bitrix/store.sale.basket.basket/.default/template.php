<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//echo "<pre>"; print_r($arResult); echo "</pre>";
if (StrLen($arResult["ERROR_MESSAGE"])<=0) {
    $arUrlTempl = Array(
        "delete" => $APPLICATION->GetCurPage()."?action=delete&id=#ID#",
        "shelve" => $APPLICATION->GetCurPage()."?action=shelve&id=#ID#",
        "add" => $APPLICATION->GetCurPage()."?action=add&id=#ID#",
    );
?>

<div class="step">
    <?=GetMessage("SALE_STEP_1")?> <span class="sep">&nbsp;</span> <span class="current"><?=GetMessage("SALE_STEP_2")?></span>
</div>

<form id="cart-form" method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form">
<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delay.php");?>
</form>
<?} else {
    ShowNote($arResult["ERROR_MESSAGE"]);
}?>