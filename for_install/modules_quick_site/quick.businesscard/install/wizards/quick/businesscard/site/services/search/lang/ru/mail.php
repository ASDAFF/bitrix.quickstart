<?
//FEEDBACK_FORM

$MESS["FEEDBACK_FORM_NAME"] = "Отправка сообщения через форму обратной связи";
$MESS["FEEDBACK_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - дата создания сообщения
#TITLE# - тема сообщения
#NAME# - имя пользователя
#EMAIL# - Email пользователя
#PHONE# - телефон пользователя
#COMMENT# - комментарий";
$MESS["FEEDBACK_FORM_SUBJECT"] = "#SERVER_NAME#: Сообщение из формы обратной связи";
$MESS["FEEDBACK_FORM_MESSAGE"] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html;charset=#SITE_CHARSET#\"/>
	<style>
		body 
		{
			font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
			font-size: 14px;
			color: #000;
		}
	</style>
</head>
<body>
<table cellpadding=\"0\" cellspacing=\"0\" width=\"850\" style=\"background-color: #d1d1d1; border-radius: 2px; border:1px solid #d1d1d1; margin: 0 auto;\" border=\"1\" bordercolor=\"#d1d1d1\">
	<tr>
		<td height=\"83\" width=\"850\" bgcolor=\"#eaf3f5\" style=\"border: none; padding-top: 23px; padding-right: 17px; padding-bottom: 24px; padding-left: 17px;\">
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
				<tr>
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Информационное сообщение сайта &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Уважаемый администратор сайта.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">Пользователем оставлено сообщение.<br />
				<br />
				Дата:  <i>#DATE_ACTIVE_FROM#</i><br />
				Тема:  <b>#TITLE#</b><br />
				<br />
				Имя: <i>#NAME#</i><br /> 
				Email: <i>#EMAIL#</i><br />
				Телефон: <i>#PHONE#</i><br />
				Сообщение: <i>#COMMENT#</i><br />
				<br />
				<i>Это письмо сгенерировано автоматически. Пожалуйста не отвечайте на него.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// ORDER_FORM

$MESS["ORDER_FORM_NAME"] = "Уведомление о заказе товара";
$MESS["ORDER_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - дата заказа
#PRODUCT_NAME# - название товара
#NAME# - имя покупателя
#PHONE# - телефон
#EMAIL# - Email
#COMMENT# - комментарий";
$MESS["ORDER_FORM_SUBJECT"] = "#SERVER_NAME#: Заказ товара - #PRODUCT_NAME#";
$MESS["ORDER_FORM_MESSAGE"] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html;charset=#SITE_CHARSET#\"/>
	<style>
		body 
		{
			font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
			font-size: 14px;
			color: #000;
		}
	</style>
</head>
<body>
<table cellpadding=\"0\" cellspacing=\"0\" width=\"850\" style=\"background-color: #d1d1d1; border-radius: 2px; border:1px solid #d1d1d1; margin: 0 auto;\" border=\"1\" bordercolor=\"#d1d1d1\">
	<tr>
		<td height=\"83\" width=\"850\" bgcolor=\"#eaf3f5\" style=\"border: none; padding-top: 23px; padding-right: 17px; padding-bottom: 24px; padding-left: 17px;\">
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
				<tr>
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Информационное сообщение сайта &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Уважаемый администратор сайта.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">Покупателем оформлен заказ - <b>#PRODUCT_NAME#</b>.<br />
				<br />
				Дата:  <i>#DATE_ACTIVE_FROM#</i><br />
				Название: <i><b>#PRODUCT_NAME#</b></i><br />
				<br />
				Покупатель: <i>#NAME#</i><br /> 
				Телефон: <i><b>#PHONE#</b></i><br /> 
				Email: <i>#EMAIL#</i><br /> 
				Комментарий к заказу: <i>#COMMENT#</i><br />
				<br />
				<i>Это письмо сгенерировано автоматически. Пожалуйста не отвечайте на него.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// CALLBACK_FORM

$MESS["CALLBACK_FORM_NAME"] = "Уведомление об обратном звонке";
$MESS["CALLBACK_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - дата создания сообщения
#TITLE# - тема
#NAME# - имя пользователя
#PHONE# - телефон
#COMMENT# - комментарий";
$MESS["CALLBACK_FORM_SUBJECT"] = "#SERVER_NAME#: Запрос на обратный звонок - #PHONE#";
$MESS["CALLBACK_FORM_MESSAGE"] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html;charset=#SITE_CHARSET#\"/>
	<style>
		body 
		{
			font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
			font-size: 14px;
			color: #000;
		}
	</style>
</head>
<body>
<table cellpadding=\"0\" cellspacing=\"0\" width=\"850\" style=\"background-color: #d1d1d1; border-radius: 2px; border:1px solid #d1d1d1; margin: 0 auto;\" border=\"1\" bordercolor=\"#d1d1d1\">
	<tr>
		<td height=\"83\" width=\"850\" bgcolor=\"#eaf3f5\" style=\"border: none; padding-top: 23px; padding-right: 17px; padding-bottom: 24px; padding-left: 17px;\">
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
				<tr>
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Информационное сообщение сайта &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Уважаемый администратор сайта.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">Пользователь заказал обратный звонок.<br />
				<br />
				Дата:  <i>#DATE_ACTIVE_FROM#</i><br />
				Тема:  <b>#TITLE#</b><br />
				<br />
				Имя: <i>#NAME#</i><br /> 
				Телефон: <i>#PHONE#</i><br /> 
				Сообщение: <i>#COMMENT#</i><br />
				<br />
				<i>Это письмо сгенерировано автоматически. Пожалуйста не отвечайте на него.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// COMMENTS_FORM

$MESS["COMMENTS_FORM_NAME"] = "Уведомление о комментарии";
$MESS["COMMENTS_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - дата создания комментария
#TITLE# - раздел на сайте
#NAME# - имя пользователя
#EMAIL# - Email
#STARS# - рейтинг
#COMMENT# - комментарий";
$MESS["COMMENTS_FORM_SUBJECT"] = "#SERVER_NAME#: Комментарий на сайте";
$MESS["COMMENTS_FORM_MESSAGE"] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html;charset=#SITE_CHARSET#\"/>
	<style>
		body 
		{
			font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
			font-size: 14px;
			color: #000;
		}
	</style>
</head>
<body>
<table cellpadding=\"0\" cellspacing=\"0\" width=\"850\" style=\"background-color: #d1d1d1; border-radius: 2px; border:1px solid #d1d1d1; margin: 0 auto;\" border=\"1\" bordercolor=\"#d1d1d1\">
	<tr>
		<td height=\"83\" width=\"850\" bgcolor=\"#eaf3f5\" style=\"border: none; padding-top: 23px; padding-right: 17px; padding-bottom: 24px; padding-left: 17px;\">
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
				<tr>
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Информационное сообщение сайта &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Уважаемый администратор сайта.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">Пользователь оставил комментарий на сайте.<br />
				<br />
				Дата:  <i>#DATE_ACTIVE_FROM#</i><br />
				Раздел:  <b>#TITLE#</b><br />
				<br />
				Имя: <i>#NAME#</i><br /> 
				Email: <i>#EMAIL#</i><br />
				Рейтинг: <i>#STARS#</i><br />				
				Сообщение: <i>#COMMENT#</i><br />
				<br />
				<i>Это письмо сгенерировано автоматически. Пожалуйста не отвечайте на него.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// VACANCIES_FORM

$MESS["VACANCIES_FORM_NAME"] = "Отправка резюме";
$MESS["VACANCIES_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - дата создания сообщения
#TITLE# - вакансия
#NAME# - имя соискателя
#PHONE# - телефон
#EMAIL# - Email
#FILE# - файл
#COMMENT# - комментарий";
$MESS["VACANCIES_FORM_SUBJECT"] = "#SERVER_NAME#: Резюме соискателя";
$MESS["VACANCIES_FORM_MESSAGE"] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html;charset=#SITE_CHARSET#\"/>
	<style>
		body 
		{
			font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
			font-size: 14px;
			color: #000;
		}
	</style>
</head>
<body>
<table cellpadding=\"0\" cellspacing=\"0\" width=\"850\" style=\"background-color: #d1d1d1; border-radius: 2px; border:1px solid #d1d1d1; margin: 0 auto;\" border=\"1\" bordercolor=\"#d1d1d1\">
	<tr>
		<td height=\"83\" width=\"850\" bgcolor=\"#eaf3f5\" style=\"border: none; padding-top: 23px; padding-right: 17px; padding-bottom: 24px; padding-left: 17px;\">
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
				<tr>
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Информационное сообщение сайта &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Уважаемый администратор сайта.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">Соискателем отправлено резюме.<br />
				<br />
				Дата:  <i>#DATE_ACTIVE_FROM#</i><br />
				Вакансия: #TITLE#<br />
				Имя соискателя: <i>#NAME#</i><br /> 
				Телефон: <i>#PHONE#</i><br />
				Email: <i>#EMAIL#</i><br />
				Файл: <a href=\"#FILE#\" target=\"_blank\"><i>#FILE#</i></a><br /> 
				Комментарий: <i>#COMMENT#</i><br />
				<br />
				<i>Это письмо сгенерировано автоматически. Пожалуйста не отвечайте на него.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";
?>