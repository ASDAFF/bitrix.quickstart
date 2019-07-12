<?
$MESS["MF_EVENT_NAME"] = "Sending a message using a feedback form";
$MESS["MF_EVENT_DESCRIPTION"] = "#AUTHOR# - Message author
#AUTHOR_EMAIL# - Author's e-mail address
#TEXT# - Message text
#EMAIL_FROM# - Sender's e-mail address
#EMAIL_TO# - Recipient's e-mail address";
$MESS["MF_EVENT_SUBJECT"] = "#SITE_NAME#: A feedback form message";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Notification from &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Dear e-shop administrator</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">A message has been sent to you from the feedback form.<br />
				<br />		
					Sent by: <i>#AUTHOR#</i><br />
					Sender's e-mail: <i>#AUTHOR_EMAIL#</i><br /> 
					Message text: <i>#TEXT#</i><br />
				<br />
				<i>This notification has been generated automatically. Please do not respond.</i>
			</p>
		</td>
	</tr>
	<tr>
		<td height=\"40px\" width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;\">
			<p style=\"border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;\">With respect,<br />administration <a href=\"http://#SERVER_NAME#\" style=\"color:#2e6eb6;\">Internet-shop</a><br />
				E-mail: <a href=\"mailto:#DEFAULT_EMAIL_FROM#\" style=\"color:#2e6eb6;\">#DEFAULT_EMAIL_FROM#</a>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";

$MESS["SALE_QUICKLY_ORDER_NAME"] = "Notice on the order in 1 cliques";

$MESS["SALE_QUICKLY_ORDER_DESC"] = "#ELEMENT_ID# - ID the goods
#ELEMENT_NAME# - Goods name
#DETAIL_PAGE# - Reference to the detailed description of the goods
#SECTION_NAME# - Name of section of the goods
#SECTION_PAGE_URL# - Reference to goods section
#EDIT_URL# - Reference to the order form
#NAME# - Name of the buyer
#ARTNUMBER# - Goods article
#PHONE# - Phone of the buyer
#COLOR# - Colour of the goods
#SIZE# - Size of the goods
#QUANTITY# - Quantity of the goods ";

$MESS["SALE_QUICKLY_ORDER_SUBJECT"] = "Buy in 1 click: #ELEMENT_NAME# #SIZE# #COLOR# - #ARTNUMBER#";

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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">&laquo;Buy in 1 click&raquo;. Card order.</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Dear Manager online store!</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">
				Buyer <b>#NAME#</b> , phone <b>#PHONE#</b> ordered from section <i>&laquo;<a href=\"http://#SERVER_NAME##SECTION_PAGE_URL#\">#SECTION_NAME#</a>&raquo;</i> the following goods:
				<br />
					<p style=\"font-weight:bold; font-size:14px;\"><a href=\"http://#SERVER_NAME##DETAIL_PAGE#\">#ELEMENT_NAME#</a></p>
					Code: <i>#ARTNUMBER#</i><br />
					Size: <i>#SIZE#</i><br /> 
					Color: <i>#COLOR#</i><br /> 
					Number: <i>#QUANTITY# pcs</i>
				<br /><br />

				<i>If this letter is displayed correctly, click <a href=\"http://#SERVER_NAME##EDIT_URL#\">SHOW</a>.</i><br />
				<i>This letter is generated automatically. Please do not respond.</i>
			</p>
		</td>
	</tr>
	<tr>
		<td height=\"40px\" width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;\">
			<p style=\"border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;\">With respect,<br />administration <a href=\"http://#SERVER_NAME#\" style=\"color:#2e6eb6;\">Internet-shop</a><br />
				E-mail: <a href=\"mailto:#SALE_EMAIL#\" style=\"color:#2e6eb6;\">#SALE_EMAIL#</a>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";

$MESS["SALE_NEW_ORDER_DESC"] = "#ORDER_ID# - Order code
#ORDER_DATE# - Order date
#PATH_TO_PERSONAL_AUTH# - Reference of instant authorisation
#PATH_TO_PERSONAL# - Reference to personal section
#ORDER_USER# - Customer
#PRICE# - Order sum
#EMAIL# - E-Mail the customer
#BCC# - E-Mail the latent copy
#ORDER_LIST# - Order structure
#SALE_EMAIL# - E-Mail department of sales";

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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">You decorated order in the store &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Dear #ORDER_USER#,</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">Your order number #ORDER_ID# from #ORDER_DATE# adopted.<br />
			<br />
				Cost of the order: #PRICE#.<br />
				<br />
				The composition of the order:<br />
				#ORDER_LIST#<br />
				<br />
				You can follow the progress of your order (at what stage of implementation it is), entering in Your <a href=\"http://#SERVER_NAME##PATH_TO_PERSONAL#\">Personal section</a> of the website &laquo;#SITE_NAME#&raquo;. <br />
				<br />
				Please note that to enter this section, You can click on the link instant authorization <a href=\"http://#SERVER_NAME##PATH_TO_PERSONAL_AUTH#\"><nobr>http://#SERVER_NAME##PATH_TO_PERSONAL_AUTH#</nobr></a>. After login, you can set your password on <a href=\"http://#SERVER_NAME##PATH_TO_PERSONAL#\">Personal section of the website</a>.<br />
				<br />
				In order to cancel the order, use the cancellation of the order, which is available in Your personal section of the website &laquo;#SITE_NAME#&raquo;.<br />
				<br />
				Please, appeal to site administration &laquo;#SITE_NAME#&raquo; be SURE to include Your order number - #ORDER_ID#.<br />
				<br />
				Thank you for your purchase!<br />
			</p>
		</td>
	</tr>
	<tr>
		<td height=\"40px\" width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;\">
			<p style=\"border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;\">With respect,<br />administration <a href=\"http://#SERVER_NAME#\" style=\"color:#2e6eb6;\">Internet-shop</a><br />
				E-mail: <a href=\"mailto:#SALE_EMAIL#\" style=\"color:#2e6eb6;\">#SALE_EMAIL#</a>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


$MESS["SALE_CHEAP_ORDER_NAME"] = "Notification message is Found cheaper";

$MESS["SALE_CHEAP_ORDER_DESC"] = "#ELEMENT_ID# - item ID
#ELEMENT_NAME# - name of the product
#DETAIL_PAGE# - link to detailed product description
#SECTION_NAME# - is the partition name of the goods
#SECTION_PAGE_URL# - link to the category of goods
#EDIT_URL# - link to the message form
#NAME# - is the name of the buyer
#PHONE# - phone buyer
#URL# - link to similar goods
#COMMENT# - comment to the goods";

$MESS["SALE_CHEAP_ORDER_SUBJECT"] = "Find cheaper: #ELEMENT_NAME#";

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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">&laquo;Find cheaper?&raquo;. Card message similar goods.</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Dear Manager of the online store!</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">
				The buyer <b>#NAME#</b> , phone <b>#PHONE#</b> have found a cheaper product <a href=\"http://#SERVER_NAME##DETAIL_PAGE#\">#ELEMENT_NAME#</a> from section <i>&laquo;<a href=\"http://#SERVER_NAME##SECTION_PAGE_URL#\">#SECTION_NAME#</a>&raquo;</i>:
				<br />				
					Link to similar goods: <i><a href=\"#URL#\">#URL#</a></i><br />
					Comment: <i>#COMMENT#</i><br /> 
				<br /><br />

				<i>If this letter is displayed correctly, click <a href=\"http://#SERVER_NAME##EDIT_URL#\">SHOW</a>.</i><br />
				<i>This letter is generated automatically. Please do not respond.</i>
			</p>
		</td>
	</tr>
	<tr>
		<td height=\"40px\" width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;\">
			<p style=\"border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;\">Sincerely,<br />administration <a href=\"http://#SERVER_NAME#\" style=\"color:#2e6eb6;\">Internet-shop</a><br />
				E-mail: <a href=\"mailto:#SALE_EMAIL#\" style=\"color:#2e6eb6;\">#SALE_EMAIL#</a>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";
?>