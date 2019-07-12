<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
CModule::IncludeModule('iblock');
$arUrlTempl = Array(
    "delete" => $APPLICATION->GetCurPage() . "?action=delete&id=#ID#",
    "shelve" => $APPLICATION->GetCurPage() . "?action=shelve&id=#ID#",
    "add" => $APPLICATION->GetCurPage() . "?action=add&id=#ID#",
);
?>
<form method="post" action="<?=POST_FORM_ACTION_URI ?>" name="basket_form">
    <?
   include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/basket_items.php");

    if(count($arResult["ITEMS"]["nAnCanBuy"])>0)
    include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/basket_items_notavail.php");
    ?>
</form>