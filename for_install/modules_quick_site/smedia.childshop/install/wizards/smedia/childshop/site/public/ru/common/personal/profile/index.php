<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Настройки пользователя");
?>
<?global $USER;
if ( !($USER->IsAuthorized()) ) {
    $APPLICATION->AuthForm('');
}
else{?>
   <?$APPLICATION->IncludeComponent("bitrix:main.profile", ".default", Array(
	"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
	),
	false);?>
<?}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>