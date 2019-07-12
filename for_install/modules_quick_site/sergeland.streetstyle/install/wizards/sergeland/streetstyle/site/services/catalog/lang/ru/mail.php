<?
$MESS["MF_EVENT_NAME"] = "Отправка сообщения через форму обратной связи";
$MESS["MF_EVENT_DESCRIPTION"] = "#AUTHOR# - Автор сообщения
#AUTHOR_EMAIL# - Email автора сообщения
#TEXT# - Текст сообщения
#EMAIL_FROM# - Email отправителя письма
#EMAIL_TO# - Email получателя письма";
$MESS["MF_EVENT_SUBJECT"] = "#SITE_NAME#: Сообщение из формы обратной связи";
$MESS["MF_EVENT_MESSAGE"] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
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
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Уважаемый администратор магазина</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">Вам было отправлено сообщение через форму обратной связи<br />
				<br />		
					Автор: <i>#AUTHOR#</i><br />
					E-mail автора: <i>#AUTHOR_EMAIL#</i><br /> 
					Текст сообщения: <i>#TEXT#</i><br />
				<br />
				<i>Это письмо сгенерировано автоматически. Пожалуйста не отвечайте на него.</i>
			</p>
		</td>
	</tr>
	<tr>
		<td height=\"40px\" width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;\">
			<p style=\"border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;\">С уважением,<br />администрация <a href=\"http://#SERVER_NAME#\" style=\"color:#2e6eb6;\">Интернет-магазина</a><br />
				E-mail: <a href=\"mailto:#DEFAULT_EMAIL_FROM#\" style=\"color:#2e6eb6;\">#DEFAULT_EMAIL_FROM#</a>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";

$MESS["SALE_QUICKLY_ORDER_NAME"] = "Уведомление о заказе в 1 клик";

$MESS["SALE_QUICKLY_ORDER_DESC"] = "#ELEMENT_ID# - ID товара
#ELEMENT_NAME# - название товара
#DETAIL_PAGE# - ссылка на детальное описание товара
#SECTION_NAME#	- название раздела товара
#SECTION_PAGE_URL# - ссылка на раздел товара
#EDIT_URL# - ссылка на форму заказа
#NAME# - имя покупателя
#ARTNUMBER# - артикул товара
#PHONE#	- телефон покупателя
#COLOR# - цвет товара
#SIZE#	- размер товара
#QUANTITY# - количество товара";

$MESS["SALE_QUICKLY_ORDER_SUBJECT"] = "Купить в 1 клик: #ELEMENT_NAME# #SIZE# #COLOR# - #ARTNUMBER#";

$MESS["SALE_QUICKLY_ORDER_MESSAGE"] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">&laquo;Купить в 1 клик&raquo;. Карточка заказа товара.</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Уважаемый менеджер интернет-магазина!</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">
				Покупатель <b>#NAME#</b> , тел. <b>#PHONE#</b> заказал из раздела <i>&laquo;<a href=\"http://#SERVER_NAME##SECTION_PAGE_URL#\">#SECTION_NAME#</a>&raquo;</i> следующий товар:
				<br />
					<p style=\"font-weight:bold; font-size:14px;\"><a href=\"http://#SERVER_NAME##DETAIL_PAGE#\">#ELEMENT_NAME#</a></p>
					Артикул: <i>#ARTNUMBER#</i><br />
					Размер: <i>#SIZE#</i><br /> 
					Цвет: <i>#COLOR#</i><br /> 
					Количество: <i>#QUANTITY# шт.</i>
				<br /><br />

				<i>Если это письмо отображается с ошибками, нажмите <a href=\"http://#SERVER_NAME##EDIT_URL#\">ПОСМОТРЕТЬ</a>.</i><br />
				<i>Это письмо сгенерировано автоматически.Пожалуйста не отвечайте на него.</i>
			</p>
		</td>
	</tr>
	<tr>
		<td height=\"40px\" width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;\">
			<p style=\"border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;\">С уважением,<br />администрация <a href=\"http://#SERVER_NAME#\" style=\"color:#2e6eb6;\">Интернет-магазина</a><br />
				E-mail: <a href=\"mailto:#SALE_EMAIL#\" style=\"color:#2e6eb6;\">#SALE_EMAIL#</a>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";

$MESS["SALE_NEW_ORDER_DESC"] = "#ORDER_ID# - код заказа
#ORDER_DATE# - дата заказа
#PATH_TO_PERSONAL_AUTH# - ссылка мгновенной авторизации
#PATH_TO_PERSONAL# - ссылка на персональный раздел
#ORDER_USER# - заказчик
#PRICE# - сумма заказа
#EMAIL# - E-Mail заказчика
#BCC# - E-Mail скрытой копии
#ORDER_LIST# - состав заказа
#SALE_EMAIL# - E-Mail отдела продаж";

$MESS["SALE_NEW_ORDER_MESSAGE"] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Вами оформлен заказ в магазине &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Уважаемый #ORDER_USER#,</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">Ваш заказ номер #ORDER_ID# от #ORDER_DATE# принят.<br />
				<br />
				Стоимость заказа: #PRICE#.<br />
				<br />
				Состав заказа:<br />
				#ORDER_LIST#<br />
				<br />
				Вы можете следить за выполнением своего заказа (на какой стадии выполнения он находится), войдя в Ваш <a href=\"http://#SERVER_NAME##PATH_TO_PERSONAL#\">Персональный раздел</a> сайта &laquo;#SITE_NAME#&raquo;. <br />
				<br />
				Обратите внимание, что для входа в этот раздел Вы можете перейти по ссылке мгновенной авторизации <a href=\"http://#SERVER_NAME##PATH_TO_PERSONAL_AUTH#\"><nobr>http://#SERVER_NAME##PATH_TO_PERSONAL_AUTH#</nobr></a>. После авторизации, вы сможете задать свой пароль на <a href=\"http://#SERVER_NAME##PATH_TO_PERSONAL#\">Персональный раздел сайта</a>.<br />
				<br />
				Для того, чтобы аннулировать заказ, воспользуйтесь функцией отмены заказа, которая доступна в Вашем персональном разделе сайта &laquo;#SITE_NAME#&raquo;.<br />
				<br />
				Пожалуйста, при обращении к администрации сайта &laquo;#SITE_NAME#&raquo; ОБЯЗАТЕЛЬНО указывайте номер Вашего заказа - #ORDER_ID#.<br />
				<br />
				Спасибо за покупку!<br />
			</p>
		</td>
	</tr>
	<tr>
		<td height=\"40px\" width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;\">
			<p style=\"border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;\">С уважением,<br />администрация <a href=\"http://#SERVER_NAME#\" style=\"color:#2e6eb6;\">Интернет-магазина</a><br />
				E-mail: <a href=\"mailto:#SALE_EMAIL#\" style=\"color:#2e6eb6;\">#SALE_EMAIL#</a>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


$MESS["SALE_CHEAP_ORDER_NAME"] = "Уведомление о сообщении - Нашли дешевле";

$MESS["SALE_CHEAP_ORDER_DESC"] = "#ELEMENT_ID# - ID товара
#ELEMENT_NAME# - название товара
#DETAIL_PAGE# - ссылка на детальное описание товара
#SECTION_NAME#	- название раздела товара
#SECTION_PAGE_URL# - ссылка на раздел товара
#EDIT_URL# - ссылка на форму сообщения
#NAME# - имя покупателя
#PHONE#	- телефон покупателя
#URL# - ссылка на аналогичный товар
#COMMENT# - комментарий к товару";

$MESS["SALE_CHEAP_ORDER_SUBJECT"] = "Нашли дешевле: #ELEMENT_NAME#";

$MESS["SALE_CHEAP_ORDER_MESSAGE"] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">&laquo;Нашли дешевле?&raquo;. Карточка сообщения об аналогичном товаре.</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Уважаемый менеджер интернет-магазина!</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">
				Покупатель <b>#NAME#</b> , тел. <b>#PHONE#</b> нашел дешевле товар <a href=\"http://#SERVER_NAME##DETAIL_PAGE#\">#ELEMENT_NAME#</a> из раздела <i>&laquo;<a href=\"http://#SERVER_NAME##SECTION_PAGE_URL#\">#SECTION_NAME#</a>&raquo;</i>:
				<br />				
					Ссылка на аналогичный товар: <i><a href=\"#URL#\">#URL#</a></i><br />
					Комментарий: <i>#COMMENT#</i><br /> 
				<br /><br />

				<i>Если это письмо отображается с ошибками, нажмите <a href=\"http://#SERVER_NAME##EDIT_URL#\">ПОСМОТРЕТЬ</a>.</i><br />
				<i>Это письмо сгенерировано автоматически. Пожалуйста не отвечайте на него.</i>
			</p>
		</td>
	</tr>
	<tr>
		<td height=\"40px\" width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;\">
			<p style=\"border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;\">С уважением,<br />администрация <a href=\"http://#SERVER_NAME#\" style=\"color:#2e6eb6;\">Интернет-магазина</a><br />
				E-mail: <a href=\"mailto:#SALE_EMAIL#\" style=\"color:#2e6eb6;\">#SALE_EMAIL#</a>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";
?>