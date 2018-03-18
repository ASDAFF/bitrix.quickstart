<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?>
<p><strong>Адрес:</strong> #COMPANY_ADRESS#</p>
<p><strong>Телефон:</strong> #COMPANY_PHONE#</p>
<p><strong>Контактный e-mail:</strong> #COMPANY_EMAIL#</p><br />
<p><strong>Также Вы можете связаться с нами, заполнив форму обратной связи ниже</strong></p><br />
<?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	"",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => "#COMPANY_EMAIL#",
		"REQUIRED_FIELDS" => array("EMAIL", "MESSAGE"),
		"EVENT_MESSAGE_ID" => array()
	),
false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>