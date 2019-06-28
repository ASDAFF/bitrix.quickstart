<?
	$MESS['module.info.partner.name'] = 'Intec';
	$MESS['module.info.partner.url'] = 'http://www.intecweb.ru';
	$MESS['module.info.name'] = 'Старт SHOP';
	$MESS['module.info.description'] = 'Модуль, расширяющий возможности редакции "Старт" до интернет-магазина';

	$MESS['events.types.new_order.name'] = "Новый заказ";
	$MESS['events.types.new_order.description'] = "#ORDER_ID# - Номер заказа\r\n#ORDER_AMOUNT# - Сумма заказа\r\n#STARTSHOP_SHOP_EMAIL# - Электронная почта магазина из настроек сайта\r\n#STARTSHOP_CLIENT_EMAIL# - Электронная почта клиента, который сделал заказ\r\n";
	$MESS['events.types.new_order.template.subject'] = "Ваш заказ №#ORDER_ID# на сумму #ORDER_AMOUNT# успешно оформлен!";
	$MESS['events.types.new_order.template.message'] = "#SITE_NAME#. Ваш заказ №#ORDER_ID# на сумму #ORDER_AMOUNT#  успешно оформлен!";

	$MESS['events.types.new_order_admin.name'] = "Новый заказ";
	$MESS['events.types.new_order_admin.description'] = "#ORDER_ID# - Номер заказа\r\n#ORDER_AMOUNT# - Сумма заказа\r\n#STARTSHOP_SHOP_EMAIL# - Электронная почта магазина из настроек сайта\r\n";
	$MESS['events.types.new_order_admin.template.subject'] = "Новый заказ на сайте #SITE_NAME#.";
	$MESS['events.types.new_order_admin.template.message'] = "Новый заказ на сайте #SITE_NAME#, номер заказа #ORDER_ID#, сумма #ORDER_AMOUNT#";

	$MESS['events.types.pay_order_admin.name'] = "Заказ оплачен";
	$MESS['events.types.pay_order_admin.description'] = "#ORDER_ID# - Номер заказа\r\n#ORDER_AMOUNT# - Сумма заказа\r\n#STARTSHOP_SHOP_EMAIL# - Электронная почта магазина из настроек сайта\r\n";
	$MESS['events.types.pay_order_admin.template.subject'] = "Заказ №#ORDER_ID# оплачен!";
	$MESS['events.types.pay_order_admin.template.message'] = "Заказ №#ORDER_ID# на сайте #SITE_NAME# оплачен!";

	$MESS['module.install.form.caption'] = "Установка модуля \"Старт SHOP\"";
	$MESS['module.install.finish.form.caption'] = "Установка модуля \"Старт SHOP\" успешно завершена!";
	$MESS['module.uninstall.form.caption'] = "Удаление модуля \"Старт SHOP\"";
?>