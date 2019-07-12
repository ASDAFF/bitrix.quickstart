<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Восстановление пароля");
?>

<h1>Восстановление пароля</h1>
<div id="pwdrecovery" class="content contenttext">

<?$APPLICATION->IncludeComponent("bitrix:system.auth.forgotpasswd", "template1", Array(
	
	),
	false
);?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

