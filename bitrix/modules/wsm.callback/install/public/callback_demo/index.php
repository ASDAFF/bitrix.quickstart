<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Демонстрация заказа обратного звонка");
?>

<?$APPLICATION->IncludeComponent("wsm:callback", ".default", array(
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000"
	),
	false
);?>

<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>