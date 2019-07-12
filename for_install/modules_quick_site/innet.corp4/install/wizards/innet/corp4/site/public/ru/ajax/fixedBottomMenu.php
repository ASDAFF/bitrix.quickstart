<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
if ($_REQUEST["class"] == 'true'){
    $_SESSION["INNET_FIXED_BOTTOM_MENU"] = '';
} else {
    $_SESSION["INNET_FIXED_BOTTOM_MENU"] = 'not-active';
}
?>