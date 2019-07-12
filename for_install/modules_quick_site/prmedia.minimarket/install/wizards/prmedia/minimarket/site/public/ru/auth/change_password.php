<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Изменение пароля");
?><h1><?= $APPLICATION->ShowTitle(false); ?></h1>
<?$APPLICATION->IncludeComponent('bitrix:system.auth.changepasswd', false, array()); ?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>