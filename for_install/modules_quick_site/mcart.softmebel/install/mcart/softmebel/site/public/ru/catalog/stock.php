<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Продажа мебели со склада | готовые диваны со склада от производителя | Оптом | Санкт-Петербург");
$APPLICATION->SetTitle("Продажа мебели со склада в Петербурге");
?> 
<p align="left"><strong>Продажа мебели со склада</strong> позволяет нам экономить на аренде площадей в мебельных торговых центрах, что позволяет нам сформировать низкие цены на мебель при высоком качестве и дизайне. 
  <br />
 </p>
 При <b>продаже мебели со склада</b> вы можете рассчитывать на скидки до 25%<b> 
  <br />
 
  <br />
 </b>Позвоните или заполните форму обратной связи: 
<br />
 
<br />
 <b><?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	".default",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => "mebel@swmebel.ru",
		"REQUIRED_FIELDS" => array(0=>"NAME",1=>"EMAIL",2=>"MESSAGE",),
		"EVENT_MESSAGE_ID" => array()
	)
);?> 
  <br />
 
  <br />
</b> Также не забывайте, что мы открыли <a href="/contacts/6.php" >мебельный дисконт-центр</a>. <b>
  <br />
 
  <br />
 
  <p></p>
 </b><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>