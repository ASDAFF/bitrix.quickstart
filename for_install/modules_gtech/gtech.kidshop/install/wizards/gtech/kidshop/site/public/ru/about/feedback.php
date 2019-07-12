<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Обратная связь с менеджерами Интернет-магазина \"Капитошка\"");
$APPLICATION->SetTitle("Обратная связь");
?> 
<div align="left"> 
  <div> </div>
 <font size="3"><b><font face="Tahoma">Форма обратной связи</font></b></font> 
  <br />

  <br />
Напишите нам письмо, мы вам обязательно ответим!
  <br />
 
  <br />
 <?$APPLICATION->IncludeComponent("bitrix:main.feedback", ".default", array(
	"USE_CAPTCHA" => "Y",
	"OK_TEXT" => "Спасибо, ваше сообщение принято.",
	"EMAIL_TO" => COption::GetOptionString("main","email_from"),
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
);?></div>
 
<div align="left"> 
  <br />
 </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>