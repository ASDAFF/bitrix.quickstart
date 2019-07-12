<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход на сайт");
?>

<?if(!$USER->GetID()){?>
<?$APPLICATION->IncludeComponent("bitrix:system.auth.authorize", "template1", Array(
	
	),
	false
);?>
<?}else{?>
<p class="notetext">Вы зарегистрированы и успешно авторизовались.</p>
<p><a href="/">Вернуться на главную страницу</a></p>
<?}?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>