<?
	$MESS['module.info.partner.name'] = 'Intec';
	$MESS['module.info.partner.url'] = 'http://www.intecweb.ru';
	$MESS['module.info.name'] = 'Старт SHOP';
	$MESS['module.info.description'] = 'Модуль, расширяющий возможности редакции "Старт" до интернет-магазина';

	$MESS['events.types.new_order.name'] = "Новый заказ";
	$MESS['events.types.new_order.description'] = "#ORDER_ID# - Номер заказа\r\n#ORDER_AMOUNT# - Сумма заказа\r\n#STARTSHOP_SHOP_EMAIL# - Электронная почта магазина из настроек сайта\r\n#STARTSHOP_CLIENT_EMAIL# - Электронная почта клиента, который сделал заказ\r\n#STARTSHOP_ORDER_LIST# - Состав заказа\r\n#ORDER_DELIVERY# - стоимость доставки\r\n#ORDER_PAYMENT# - способ оплаты\r\n";
	$MESS['events.types.new_order.template.subject'] = "Ваш заказ №#ORDER_ID# на сумму #ORDER_AMOUNT# успешно оформлен!";
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
								<td bgcolor="#ffffff" height="75" style="font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;">Оформлен заказ в магазине #SITE_NAME#</td>
							</tr>
							<tr>
								<td bgcolor="#bad3df" height="11"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="850" bgcolor="#f7f7f7" valign="top" style="border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;">
						<p style="margin-top: 0; margin-bottom: 20px; line-height: 20px;">Ваш заказ №#ORDER_ID# успешно оформлен.<br />
			<br />
			Стоимость заказа: #ORDER_AMOUNT#.<br />
			<br />
			Стоимость доставки: #ORDER_DELIVERY#.<br />
			<br />
			Способ оплаты: #ORDER_PAYMENT#.<br />
			<br />
			Состав заказа:<br />
			#STARTSHOP_ORDER_LIST#<br />
			<br />
					</td>
				</tr>
			</table>
			</body>
			</html>';

	$MESS['events.types.new_order_admin.name'] = "Новый заказ";
	$MESS['events.types.new_order_admin.description'] = "#ORDER_ID# - Номер заказа\r\n#ORDER_AMOUNT# - Сумма заказа\r\n#STARTSHOP_SHOP_EMAIL# - Электронная почта магазина из настроек сайта\r\n#STARTSHOP_ORDER_LIST# - Состав заказа\r\n#STARTSHOP_ORDER_PROPERTY# - Свойства заказа\r\n#ORDER_DELIVERY# - стоимость доставки\r\n#ORDER_PAYMENT# - способ оплаты\r\n";
	$MESS['events.types.new_order_admin.template.subject'] = "Новый заказ на сайте #SITE_NAME#.";
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
								<td bgcolor="#ffffff" height="75" style="font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;">Оформлен заказ в магазине #SITE_NAME#</td>
							</tr>
							<tr>
								<td bgcolor="#bad3df" height="11"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="850" bgcolor="#f7f7f7" valign="top" style="border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;">
						<p style="margin-top: 0; margin-bottom: 20px; line-height: 20px;">Номер заказа #ORDER_ID#.<br />
			<br />
			Стоимость заказа: #ORDER_AMOUNT#.<br />
			<br />
			Состав заказа:<br />
			#STARTSHOP_ORDER_LIST#<br />
			<br />
			Стоимость доставки: #ORDER_DELIVERY#.<br />
			<br />
			Способ оплаты: #ORDER_PAYMENT#.<br />
			<br />
			Свойства заказа:<br />
			#STARTSHOP_ORDER_PROPERTY#<br />
			<br />

					</td>
				</tr>
			</table>
			</body>
			</html>';

	$MESS['events.types.pay_order_admin.name'] = "Заказ оплачен";
	$MESS['events.types.pay_order_admin.description'] = "#ORDER_ID# - Номер заказа\r\n#ORDER_AMOUNT# - Сумма заказа\r\n#STARTSHOP_SHOP_EMAIL# - Электронная почта магазина из настроек сайта\r\n";
	$MESS['events.types.pay_order_admin.template.subject'] = "Заказ №#ORDER_ID# оплачен!";
	$MESS['events.types.pay_order_admin.template.message'] = "Заказ №#ORDER_ID# на сайте #SITE_NAME# оплачен!";

	$MESS['module.install.form.caption'] = "Установка модуля \"Старт SHOP\"";
	$MESS['module.install.finish.form.caption'] = "Установка модуля \"Старт SHOP\" успешно завершена!";
	$MESS['module.uninstall.form.caption'] = "Удаление модуля \"Старт SHOP\"";
?>