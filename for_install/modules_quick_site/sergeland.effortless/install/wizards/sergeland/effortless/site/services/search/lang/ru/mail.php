<?
//FEEDBACK_FORM

$MESS["FEEDBACK_FORM_NAME"] = "�������� ��������� ����� ����� �������� �����";
$MESS["FEEDBACK_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - ���� �������� ���������
#TITLE# - ���� ���������
#NAME# - ��� ������������
#EMAIL# - Email ������������
#PHONE# - ������� ������������
#COMMENT# - �����������";
$MESS["FEEDBACK_FORM_SUBJECT"] = "#SERVER_NAME#: ��������� �� ����� �������� �����";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">�������������� ��������� ����� &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">��������� ������������� �����.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">������������� ��������� ���������.<br />
				<br />
				����:  <i>#DATE_ACTIVE_FROM#</i><br />
				����:  <b>#TITLE#</b><br />
				<br />
				���: <i>#NAME#</i><br /> 
				Email: <i>#EMAIL#</i><br />
				�������: <i>#PHONE#</i><br />
				���������: <i>#COMMENT#</i><br />
				<br />
				<i>��� ������ ������������� �������������. ���������� �� ��������� �� ����.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// ORDER_FORM

$MESS["ORDER_FORM_NAME"] = "����������� � ������ ������";
$MESS["ORDER_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - ���� ������
#PRODUCT_NAME# - �������� ������
#NAME# - ��� ����������
#PHONE# - �������
#EMAIL# - Email
#COMMENT# - �����������";
$MESS["ORDER_FORM_SUBJECT"] = "#SERVER_NAME#: ����� ������ - #PRODUCT_NAME#";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">�������������� ��������� ����� &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">��������� ������������� �����.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">����������� �������� ����� - <b>#PRODUCT_NAME#</b>.<br />
				<br />
				����:  <i>#DATE_ACTIVE_FROM#</i><br />
				��������: <i><b>#PRODUCT_NAME#</b></i><br />
				<br />
				����������: <i>#NAME#</i><br /> 
				�������: <i><b>#PHONE#</b></i><br /> 
				Email: <i>#EMAIL#</i><br /> 
				����������� � ������: <i>#COMMENT#</i><br />
				<br />
				<i>��� ������ ������������� �������������. ���������� �� ��������� �� ����.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// CALLBACK_FORM

$MESS["CALLBACK_FORM_NAME"] = "����������� �� �������� ������";
$MESS["CALLBACK_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - ���� �������� ���������
#TITLE# - ����
#NAME# - ��� ������������
#PHONE# - �������
#COMMENT# - �����������";
$MESS["CALLBACK_FORM_SUBJECT"] = "#SERVER_NAME#: ������ �� �������� ������ - #PHONE#";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">�������������� ��������� ����� &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">��������� ������������� �����.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">������������ ������� �������� ������.<br />
				<br />
				����:  <i>#DATE_ACTIVE_FROM#</i><br />
				����:  <b>#TITLE#</b><br />
				<br />
				���: <i>#NAME#</i><br /> 
				�������: <i>#PHONE#</i><br /> 
				���������: <i>#COMMENT#</i><br />
				<br />
				<i>��� ������ ������������� �������������. ���������� �� ��������� �� ����.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// COMMENTS_FORM

$MESS["COMMENTS_FORM_NAME"] = "����������� � �����������";
$MESS["COMMENTS_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - ���� �������� �����������
#TITLE# - ������ �� �����
#NAME# - ��� ������������
#EMAIL# - Email
#STARS# - �������
#COMMENT# - �����������";
$MESS["COMMENTS_FORM_SUBJECT"] = "#SERVER_NAME#: ����������� �� �����";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">�������������� ��������� ����� &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">��������� ������������� �����.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">������������ ������� ����������� �� �����.<br />
				<br />
				����:  <i>#DATE_ACTIVE_FROM#</i><br />
				������:  <b>#TITLE#</b><br />
				<br />
				���: <i>#NAME#</i><br /> 
				Email: <i>#EMAIL#</i><br />
				�������: <i>#STARS#</i><br />				
				���������: <i>#COMMENT#</i><br />
				<br />
				<i>��� ������ ������������� �������������. ���������� �� ��������� �� ����.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";


// VACANCIES_FORM

$MESS["VACANCIES_FORM_NAME"] = "�������� ������";
$MESS["VACANCIES_FORM_DESCRIPTION"] = "#DATE_ACTIVE_FROM# - ���� �������� ���������
#TITLE# - ��������
#NAME# - ��� ����������
#PHONE# - �������
#EMAIL# - Email
#FILE# - ����
#COMMENT# - �����������";
$MESS["VACANCIES_FORM_SUBJECT"] = "#SERVER_NAME#: ������ ����������";
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
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">�������������� ��������� ����� &laquo;#SITE_NAME#&raquo;</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">��������� ������������� �����.</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">����������� ���������� ������.<br />
				<br />
				����:  <i>#DATE_ACTIVE_FROM#</i><br />
				��������: #TITLE#<br />
				��� ����������: <i>#NAME#</i><br /> 
				�������: <i>#PHONE#</i><br />
				Email: <i>#EMAIL#</i><br />
				����: <a href=\"#FILE#\" target=\"_blank\"><i>#FILE#</i></a><br /> 
				�����������: <i>#COMMENT#</i><br />
				<br />
				<i>��� ������ ������������� �������������. ���������� �� ��������� �� ����.</i>
			</p>
		</td>
	</tr>
</table>
</body>
</html>";
?>