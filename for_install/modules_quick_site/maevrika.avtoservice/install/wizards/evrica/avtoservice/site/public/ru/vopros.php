<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Форма обратной связи");
?>Вы можете задать записаться на прием к &quot;врачу&quot; или задать свой вопрос по телефону 8(495) 225-58-90  или заполните форму обратной связи. 
<br />
<font color="#ff0000"><strong>ВНИМАНИЕ!</strong> </font>Если Вы хотите записаться на шиномонтажные или иные виды работ, <strong>обязательно укажите</strong> в теле письма <strong>свой номер телефона</strong> для потверждения заказа. В противном случае заказ не будет принят к исполнению. 
<br />

<br />
Поля отмеченные &quot;*&quot; обязательны для заполнения 
<br />

<br />
 <?$APPLICATION->IncludeComponent(
	"bitrix:main.feedback",
	".default",
	Array(
		"USE_CAPTCHA" => "Y",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"EMAIL_TO" => "",
		"REQUIRED_FIELDS" => array("NAME", "EMAIL", "MESSAGE"),
		"EVENT_MESSAGE_ID" => array()
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>