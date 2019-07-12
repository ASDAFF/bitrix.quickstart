<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контактная информация");
?>
<h2>Контактная информация</h2>
<?$APPLICATION->IncludeComponent('prmedia:minimarket.map.salepoints', '', array()); ?>
<h2>Задать вопрос</h2>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback", 
	"", 
	array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => "#FEEDBACK_EMAIL#",
		"REQUIRED_FIELDS" => array(
			0 => "NAME",
			1 => "EMAIL",
			2 => "MESSAGE",
		),
		"EVENT_MESSAGE_ID" => array(
		)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>