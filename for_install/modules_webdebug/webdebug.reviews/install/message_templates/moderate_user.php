<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=<?=SITE_CHARSET;?>">
	<title>Вы оставили отзыв на сайте #SERVER_NAME#</title>
</head>
<body style="color:#000; font-family:'Helvetica Neue', 'Helvetica', 'Arial', sans-serif; font-size:14px;">
	<table cellpadding="0" cellspacing="0" width="750" style="background-color: #d1d1d1; border-radius: 2px; border:1px solid #d1d1d1; margin: 0 auto;" border="1">
		<tr>
			<td height="83" width="100%" bgcolor="#eaf3f5" style="border: none; border-bottom:1px solid #d1d1d1; padding-top: 23px; padding-right: 17px; padding-bottom: 24px; padding-left: 17px;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid #d1d1d1;">
					<tr>
						<td bgcolor="#ffffff" height="75" style="font-weight: bold; text-align: center; font-size: 22px; color: #0b3961;">Вы оставили отзыв на сайте #SERVER_NAME#</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width="100%" bgcolor="#f7f7f7" valign="top" style="border: none; padding-top: 0; padding-right: 24px; padding-bottom: 16px; padding-left: 24px;">
				<p style="margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 17px;">Уважаемый #USER_NAME#,</p>
				<p>Ваш отзыв отправлен администрации сайта. После прохождения проверки он будет опубликован на сайте <a href="http://#SERVER_NAME##TARGET_URL#" style="color:#2e6eb6;">на той же странице</a>, на которой Вы написали отзыв.</p>
				<p>Благодарим за Ваше мнение!</p>
				<p>Текст Вашего отзыва:<br>#USER_REVIEW#</p>
			</td>
		</tr>
		<tr>
			<td height="40px" width="100%" bgcolor="#f7f7f7" valign="top" style="border:none; border-top:1px solid #d1d1d1; padding:0 24px 20px;">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
					<tr>
						<td width="60%" valign="top" style="padding-top:20px;">
							С уважением,<br>администрация сайта <a href="http://#SERVER_NAME#" style="color:#2e6eb6;">#SITE_NAME#</a>.
						</td>
						<td width="40%" align="right" valign="top" style="padding-top:20px;">
							E-mail: <a href="mailto:#DEFAULT_EMAIL_FROM#" style="color:#2e6eb6;">#DEFAULT_EMAIL_FROM#</a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>