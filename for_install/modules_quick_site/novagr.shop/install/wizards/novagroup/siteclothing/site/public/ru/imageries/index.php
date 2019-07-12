<?
if (isset($_REQUEST['CAJAX']) && $_REQUEST['CAJAX'] == 1) {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
} else
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
?>
<? include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . "include/catalog/inc.collections.php"); ?>


<?
if (isset($_REQUEST['FULL']) && $_REQUEST['FULL'] == 1) {

    ?>
    <div id="navi-chain"
         style="display:none;"><? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", array("START_FROM" => "0", "PATH" => "", "SITE_ID" => SITE_ID), false); ?></div>
    <div id="meta-title" style="display:none;"><? $APPLICATION->ShowTitle(); ?></div>
<?
}
?>

<?php

if (!defined("ERROR_404") && isset($_REQUEST['elmid'])) {

    $APPLICATION->IncludeComponent("novagroup:sale.viewed.product", "demoshop", array(
            "VIEWED_COUNT" => "4",
            "VIEWED_NAME" => "Y",
            "VIEWED_IMAGE" => "Y",
            "VIEWED_PRICE" => "Y",
            "VIEWED_CANBUY" => "Y",
            "EXCLUDE_ID" => $imagery,
            "VIEWED_CANBUSKET" => "Y",
            "VIEWED_IMG_HEIGHT" => "100",
            "VIEWED_IMG_WIDTH" => "100",
            "CATALOG_IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
            "IMAGERIES_IBLOCK_ID" => "#FASHION_IBLOCK_ID#",
            "BASKET_URL" => "/personal/basket.php",
            "ACTION_VARIABLE" => "action",
            "PRODUCT_ID_VARIABLE" => $_REQUEST['elmid'],
            "SET_TITLE" => "N"
        ),
        false
    );

}
?>
<?
if (isset($_REQUEST['CAJAX']) && $_REQUEST['CAJAX'] == 1) {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
} else
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>