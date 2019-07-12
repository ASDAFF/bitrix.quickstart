<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Настройки пользователя");
?>
<div id="contactfaq" class="content contenttext">
<?$APPLICATION->IncludeComponent("bitrix:main.profile", "profile", Array(
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	),
	false
);?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>