<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Заказать со склада");
?><strong> 
  <p align="center"><strong><font size="3">Производя мебель для склада, мы добиваемся значительных скидок на закупку материалов, что позволяет нам сформировать низкие цены на мебель при высоком качестве и дизайне.</font></strong></p>
 
  <p align="center"><font size="4" color="#003366">ВНИМАНИЕ! </font></p>
 
  <p align="center"><font size="4" color="#003366">Программа распродажи склада и образцов.</font></p>
 
  <p align="center"><font size="4" color="#003366"></font></p>
 
  <p align="center"><strong><font size="4" color="#003366">СКИДКА ДО 25% </font></strong></p>
 
  <br /><br /><br />
 
<?$APPLICATION->IncludeComponent("bitrix:main.feedback", "main", array(
	"USE_CAPTCHA" => "Y",
	"OK_TEXT" => "Спасибо, ваше сообщение принято.",
	"EMAIL_TO" => "mari1901@yandex.ru",
	"REQUIRED_FIELDS" => array(
		0 => "NAME",
		1 => "EMAIL",
		2 => "MESSAGE",
	),
	"EVENT_MESSAGE_ID" => array(
		0 => "5",
	)
	),
	false
);?> 
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>