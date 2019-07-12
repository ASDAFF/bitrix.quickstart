<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
$idSite = SITE_ID;
$newBasketNum = $_SESSION["SALE_BASKET_NUM_PRODUCTS"][$idSite] - 1;
$_SESSION["SALE_BASKET_NUM_PRODUCTS"][$idSite] = $newBasketNum;
?>
