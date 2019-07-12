<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Доставка детских товаров по городу Благовещенску. Обратная связь");
$APPLICATION->SetTitle("Обратная связь");
?> 
<div align="center"> 
  <div align="center"> </div>
 
  <div align="left"><font size="3"><b><font face="Tahoma">Форма обратной связи</font></b></font> 
    <br />
   </div>
 
  <div align="left"> 
    <br />
  Напишите нам письмо, мы обязательно вам ответим!
    <br />
  
    <br />
   </div>
 
  <div align="left"> <?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	"",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => COption::GetOptionString("main","email_from"),
		"REQUIRED_FIELDS" => array("NAME","EMAIL","MESSAGE"),
		"EVENT_MESSAGE_ID" => array("5")
	)
);?></div>
 </div>
 
<br />
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>