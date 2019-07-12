<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контактная информация");
?> 
<p><b>Телефон:</b> #PHONE#
  <br />
 <b>Адрес:</b> #ADDRESS# </p>
 
 

<?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	"",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => "#EMAIL_TO#",
		"REQUIRED_FIELDS" => array(),
		"EVENT_MESSAGE_ID" => array()
	)
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>
