<?
	$MESS['module.info.partner.name'] = 'Intec';
	$MESS['module.info.partner.url'] = 'http://www.intecweb.ru';
	$MESS['module.info.name'] = '����� SHOP';
	$MESS['module.info.description'] = '������, ����������� ����������� �������� "�����" �� ��������-��������';

	$MESS['events.types.new_order.name'] = "����� �����";
	$MESS['events.types.new_order.description'] = "#ORDER_ID# - ����� ������\r\n#ORDER_AMOUNT# - ����� ������\r\n#STARTSHOP_SHOP_EMAIL# - ����������� ����� �������� �� �������� �����\r\n#STARTSHOP_CLIENT_EMAIL# - ����������� ����� �������, ������� ������ �����\r\n#STARTSHOP_ORDER_LIST# - ������ ������\r\n#ORDER_DELIVERY# - ��������� ��������\r\n#ORDER_PAYMENT# - ������ ������\r\n";
	$MESS['events.types.new_order.template.subject'] = "��� ����� �#ORDER_ID# �� ����� #ORDER_AMOUNT# ������� ��������!";
	$MESS['events.types.new_order.template.message'] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
			<head>
				<meta http-equiv="Content-Type" content="text/html;charset=windows-1251"/>
				<style>
					body
					{
						font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;
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
								<td bgcolor="#ffffff" height="75" style="font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;">�������� ����� � �������� #SITE_NAME#</td>
							</tr>
							<tr>
								<td bgcolor="#bad3df" height="11"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="850" bgcolor="#f7f7f7" valign="top" style="border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;">
						<p style="margin-top: 0; margin-bottom: 20px; line-height: 20px;">��� ����� �#ORDER_ID# ������� ��������.<br />
			<br />
			��������� ������: #ORDER_AMOUNT#.<br />
			<br />
			��������� ��������: #ORDER_DELIVERY#.<br />
			<br />
			������ ������: #ORDER_PAYMENT#.<br />
			<br />
			������ ������:<br />
			#STARTSHOP_ORDER_LIST#<br />
			<br />
					</td>
				</tr>
			</table>
			</body>
			</html>';

	$MESS['events.types.new_order_admin.name'] = "����� �����";
	$MESS['events.types.new_order_admin.description'] = "#ORDER_ID# - ����� ������\r\n#ORDER_AMOUNT# - ����� ������\r\n#STARTSHOP_SHOP_EMAIL# - ����������� ����� �������� �� �������� �����\r\n#STARTSHOP_ORDER_LIST# - ������ ������\r\n#STARTSHOP_ORDER_PROPERTY# - �������� ������\r\n#ORDER_DELIVERY# - ��������� ��������\r\n#ORDER_PAYMENT# - ������ ������\r\n";
	$MESS['events.types.new_order_admin.template.subject'] = "����� ����� �� ����� #SITE_NAME#.";
	$MESS['events.types.new_order_admin.template.message'] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
			<head>
				<meta http-equiv="Content-Type" content="text/html;charset=windows-1251"/>
				<style>
					body
					{
						font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif;
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
								<td bgcolor="#ffffff" height="75" style="font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;">�������� ����� � �������� #SITE_NAME#</td>
							</tr>
							<tr>
								<td bgcolor="#bad3df" height="11"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="850" bgcolor="#f7f7f7" valign="top" style="border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;">
						<p style="margin-top: 0; margin-bottom: 20px; line-height: 20px;">����� ������ #ORDER_ID#.<br />
			<br />
			��������� ������: #ORDER_AMOUNT#.<br />
			<br />
			������ ������:<br />
			#STARTSHOP_ORDER_LIST#<br />
			<br />
			��������� ��������: #ORDER_DELIVERY#.<br />
			<br />
			������ ������: #ORDER_PAYMENT#.<br />
			<br />
			�������� ������:<br />
			#STARTSHOP_ORDER_PROPERTY#<br />
			<br />

					</td>
				</tr>
			</table>
			</body>
			</html>';

	$MESS['events.types.pay_order_admin.name'] = "����� �������";
	$MESS['events.types.pay_order_admin.description'] = "#ORDER_ID# - ����� ������\r\n#ORDER_AMOUNT# - ����� ������\r\n#STARTSHOP_SHOP_EMAIL# - ����������� ����� �������� �� �������� �����\r\n";
	$MESS['events.types.pay_order_admin.template.subject'] = "����� �#ORDER_ID# �������!";
	$MESS['events.types.pay_order_admin.template.message'] = "����� �#ORDER_ID# �� ����� #SITE_NAME# �������!";

	$MESS['module.install.form.caption'] = "��������� ������ \"����� SHOP\"";
	$MESS['module.install.finish.form.caption'] = "��������� ������ \"����� SHOP\" ������� ���������!";
	$MESS['module.uninstall.form.caption'] = "�������� ������ \"����� SHOP\"";
?>