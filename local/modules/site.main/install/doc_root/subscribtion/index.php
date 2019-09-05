<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подписка");

if($_REQUEST["unsubscribe"] != "yes")
	LocalRedirect('/');	
?>

<?$APPLICATION->IncludeComponent(
	"site:subscribtion",
	"",
	Array(
		"SET_TITLE" => "Y",
	)
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>