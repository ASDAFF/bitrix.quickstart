<?php
$arFields["ID"]= <<<EOD
41
EOD;
$arFields["EVENT_NAME"]= <<<EOD
SALE_ORDER_TRACKING_NUMBER
EOD;
$arFields["ACTIVE"]= <<<EOD
Y
EOD;
$arFields["LID"]= <<<EOD
s2
EOD;
$arFields["SITE_ID"]= <<<EOD
s2
EOD;
$arFields["EMAIL_FROM"]= <<<EOD
#SALE_EMAIL#
EOD;
$arFields["EMAIL_TO"]= <<<EOD
#EMAIL#
EOD;
$arFields["SUBJECT"]= <<<EOD
Номер идентификатора отправления вашего заказа на сайте #SITE_NAME#
EOD;
$arFields["MESSAGE"]= <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
				<head>
					<meta http-equiv="Content-Type" content="text/html;charset=windows-1251"/>
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
				<table cellpadding="0" cellspacing="0" width="850" style="background-color: #d1d1d1; border-radius: 2px; border:1px solid #d1d1d1; margin: 0 auto;" border="1" bordercolor="#d1d1d1">
					<tr>
						<td height="83" width="850" bgcolor="#eaf3f5" style="border: none; padding-top: 23px; padding-right: 17px; padding-bottom: 24px; padding-left: 17px;">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<td bgcolor="#ffffff" height="75" style="font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;">Номер идентификатора отправления вашего заказа на сайте #SITE_NAME#</td>
								</tr>
								<tr>
									<td bgcolor="#bad3df" height="11"></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="850" bgcolor="#f7f7f7" valign="top" style="border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;">
							<p style="margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;">Уважаемый #ORDER_USER#,</p>
							<p style="margin-top: 0; margin-bottom: 20px; line-height: 20px;">Произошла почтовая отправка заказа N #ORDER_ID# от #ORDER_DATE#.<br />
<br />
Номер идентификатора отправления: #ORDER_TRACKING_NUMBER#.<br />
<br />
Для получения подробной информации по заказу пройдите на сайт http://#SERVER_NAME#/personal/order/detail/#ORDER_ID#/<br />
<br />
E-mail: #SALE_EMAIL#<br />
</p>
						</td>
					</tr>
					<tr>
						<td height="40px" width="850" bgcolor="#f7f7f7" valign="top" style="border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;">
							<p style="border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;">С уважением,<br />администрация <a href="http://#SERVER_NAME#" style="color:#2e6eb6;">Интернет-магазина</a><br />
								E-mail: <a href="mailto:#SALE_EMAIL#" style="color:#2e6eb6;">#SALE_EMAIL#</a>
							</p>
						</td>
					</tr>
				</table>
				</body>
				</html>
EOD;
$arFields["BODY_TYPE"]= <<<EOD
html
EOD;
$arFields["BCC"]= <<<EOD
#BCC#
EOD;
$arFields["REPLY_TO"]= <<<EOD

EOD;
$arFields["CC"]= <<<EOD

EOD;
$arFields["IN_REPLY_TO"]= <<<EOD

EOD;
$arFields["PRIORITY"]= <<<EOD

EOD;
$arFields["FIELD1_NAME"]= <<<EOD

EOD;
$arFields["FIELD1_VALUE"]= <<<EOD

EOD;
$arFields["FIELD2_NAME"]= <<<EOD

EOD;
$arFields["FIELD2_VALUE"]= <<<EOD

EOD;
$arFields["TIMESTAMP_X"]= <<<EOD
06.12.2013 12:54:46
EOD;
$arFields["EVENT_TYPE"]= <<<EOD
[ SALE_ORDER_TRACKING_NUMBER ] Уведомление об изменении идентификатора почтового отправления
EOD;
