<?
//FEEDBACK_FORM

$MESS["MF_EVENT_NAME"] = "Send a message via the feedback form";
$MESS["MF_EVENT_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - date of message creation
#TITLE# - subject of the message
#NAME# - username
#EMAIL# - user's Email address
#PHONE# - user's phone
#COMMENT# - comment";
$MESS["MF_EVENT_SUBJECT"] = "#SERVER_NAME#: Message from the feedback form";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Informational message site &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Dear site administrator.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">A user left a message.<br />
				<br />
				Date: <i>#DATE_ACTIVE_FROM#</i><br />
				Topic: <b>#TITLE#</b><br />
				<br />
				Name: <i>#NAME#</i><br /> 
				Email: <i>#EMAIL#</i><br />
				Phone: <i>#PHONE#</i><br />				
				Message: <i>#COMMENT#</i><br />
				<br />
				<i>This letter is generated automatically. Please do not respond.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// ORDER_FORM

$MESS["ORDER_FORM_NAME"] = "Notice of order";
$MESS["ORDER_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - order date
#PRODUCT_NAME# - name of the product
#NAME# - name of the buyer
#PHONE# - phone
#EMAIL# - Email
#COMMENT# - comment";
$MESS["ORDER_FORM_SUBJECT"] = "#SERVER_NAME#: Order item - #PRODUCT_NAME#";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Informational message site &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Dear site administrator.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">The buyer submits an order - <b>#PRODUCT_NAME#</b>.<br />
				<br />
				Date: <i>#DATE_ACTIVE_FROM#</i><br />
				Title: <i><b>#PRODUCT_NAME#</b></i><br />
				<br />
				Buyer: <i>#NAME#</i><br /> 
				Phone: <i><b>#PHONE#</b></i><br /> 
				Email: <i>#EMAIL#</i><br /> 
				The comment to the order: <i>#COMMENT#</i><br />
				<br />
				<i>This letter is generated automatically. Please do not respond.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// CALLBACK_FORM

$MESS["CALLBACK_FORM_NAME"] = "Notice the reverse call";
$MESS["CALLBACK_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - date of message creation
#TITLE# - theme
#NAME# - username
#PHONE# - phone
#COMMENT# - comment";
$MESS["CALLBACK_FORM_SUBJECT"] = "#SERVER_NAME#: Request a callback - #PHONE#";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Informational message site &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Dear site administrator.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">The user request a call back.<br />
				<br />
				Date: <i>#DATE_ACTIVE_FROM#</i><br />
				Theme: <b>#TITLE#</b><br />
				<br />
				Name: <i>#NAME#</i><br /> 
				Phone: <i>#PHONE#</i><br /> 
				Message: <i>#COMMENT#</i><br />
				<br />
				<i>This letter is generated automatically. Please do not respond.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// COMMENTS_FORM

$MESS["COMMENTS_FORM_NAME"] = "Notification about comments";
$MESS["COMMENTS_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - date of the comment
#TITLE# - section on the website
#NAME# - username
#EMAIL# - Email
#STARS# - Stars
#COMMENT# - comment";
$MESS["COMMENTS_FORM_SUBJECT"] = "#SERVER_NAME#: Comment on the website";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Informational message site &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Dear site administrator.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">A user left a comment to the goods - <b>#PRODUCT_NAME#</b>.<br />
				<br />
				Date: <i>#DATE_ACTIVE_FROM#</i><br />
				Category: <b>#TITLE#</b><br />
				<br />
				Name: <i>#NAME#</i><br /> 
				Email: <i>#EMAIL#</i><br />
				Stars: <i>#STARS#</i><br />
				Message: <i>#COMMENT#</i><br />
				<br />
				<i>This letter is generated automatically. Please do not respond.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// VACANCIES_FORM

$MESS["VACANCIES_FORM_NAME"] = "Send resume";
$MESS["VACANCIES_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - date of message creation
#TITLE# - vacancy
#NAME# - name of the applicant
#PHONE# - phone
#EMAIL# - Email
#FILE# - file
#COMMENT# - comment";
$MESS["VACANCIES_FORM_SUBJECT"] = "#SERVER_NAME#: Summary of the applicant";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Informational message site &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Dear site administrator.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">The applicant sent the resume.<br />
				<br />
				Date: <i>#DATE_ACTIVE_FROM#</i><br />
				Vacancy: #TITLE#<br />
				Applicant name: <i>#NAME#</i><br /> 
				Phone: <i>#PHONE#</i><br />
				Email: <i>#EMAIL#</i><br />
				File: <a href=\"#FILE#\" target=\"_blank\"><i>#FILE#</i></a><br /> 
				Comment: <i>#COMMENT#</i><br />
				<br />
				<i>This letter is generated automatically. Please do not respond.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// SUBSCRIBE_FORM

$MESS["SUBSCRIBE_FORM_NAME"] = "News subscription";
$MESS["SUBSCRIBE_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - date creation
#NAME# - name
#EMAIL# - Email";
$MESS["SUBSCRIBE_FORM_SUBJECT"] = "#SERVER_NAME#: newsletter Subscription";
$MESS["SUBSCRIBE_FORM_MESSAGE"] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Informational message site &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Dear site administrator.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">The user has subscribed to the news.<br />
				<br />
				Date: <i>#DATE_ACTIVE_FROM#</i><br />
				Name: <i>#NAME#</i><br /> 
				Email: <i>#EMAIL#</i><br />
				<br />
				<i>This letter is generated automatically. Please do not respond.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";
?>