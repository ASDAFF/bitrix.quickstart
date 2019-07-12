<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>
<?$APPLICATION->IncludeComponent("v1rt.personal:contacts", "", Array(), false);?>
<?$APPLICATION->IncludeComponent("v1rt.personal:feedback", "contacts.v2", Array(), false);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>