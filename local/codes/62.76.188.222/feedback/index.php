<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Обратная связь");
?> 

    
    
    <?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	"",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => "kudinsasha@gmail.com",
		"REQUIRED_FIELDS" => array("NAME", "EMAIL", "MESSAGE"),
		"EVENT_MESSAGE_ID" => array("7")
	)
);?> 
    
    
    
    <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>