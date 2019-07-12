<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Настройки пользователя");?>

<?$APPLICATION->IncludeComponent("bitrix:main.profile", "", Array(
	"SET_TITLE" => "Y",
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>