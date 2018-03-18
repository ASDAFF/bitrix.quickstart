<?
global $MESS;

$MESS['ONLINEDENGI_PS_TITLE'] = "Оплата через сервис OnlineDengi";
$MESS['ONLINEDENGI_PS_DESCRIPTION'] = "Сервис OnlineDengi - крупнейший агрегатор игровых платежей в Росcии.";

$MESS['ONLINEDENGI_PROJECT_N'] = "Идентификатор проекта в системе OnlineDengi";
$MESS['ONLINEDENGI_PROJECT_D'] = "<br />Уточните в системе OnlineDengi [project]";

$MESS['ONLINEDENGI_SOURCE_N'] = "Идентификатор владельца внешней формы OnlineDengi";
$MESS['ONLINEDENGI_SOURCE_D'] = "<br />Уточните в системе OnlineDengi [source]";

$MESS['ONLINEDENGI_SECRET_KEY_N'] = "Секретный ключ";
$MESS['ONLINEDENGI_SECRET_KEY_D'] = "<br />Секретный ключ произвольного вида до 35 символов. Сообщите в систему OnlineDengi.";

$MESS['ONLINEDENGI_NICKNAME_N'] = "Идентификатор заказа";
$MESS['ONLINEDENGI_NICKNAME_D'] = "<br />[nickname]";

$MESS['ONLINEDENGI_NICK_EXTRA_N'] = "Дополнительные сведения о заказе";
$MESS['ONLINEDENGI_NICK_EXTRA_D'] = "<br />До 500 символов, если требуются [nick_extra]";

$MESS['ONLINEDENGI_MODE_TYPE_N'] = "Идентификатор способа платежа";
$MESS['ONLINEDENGI_MODE_TYPE_D'] = "<br />[mode_type]";

$MESS['ONLINEDENGI_COMMENT_N'] = "Комментарий к платежу";
$MESS['ONLINEDENGI_COMMENT_D'] = "<br />До 500 символов [comment]";

$MESS['ONLINEDENGI_AMOUNT_N'] = "Сумма платежа";
$MESS['ONLINEDENGI_AMOUNT_D'] = "<br />[amount]";

$MESS['ONLINEDENGI_ORDER_ID_N'] = "Идентификатор платежа";
$MESS['ONLINEDENGI_ORDER_ID_D'] = "<br />Если есть [order_id]";

$MESS['ONLINEDENGI_CONVERT_ROUND_UP_N'] = "При конвертации валют выполнять округление к большему";
$MESS['ONLINEDENGI_CONVERT_ROUND_UP_D'] = "<br />0 - арифметическое округление (например, 2.123 -> 2.12) <br /> 1 - округлять к большему (например, 2.123 -> 2.13)";

$MESS['ONLINEDENGI_WRONG_PAY_MODE_N'] = "Выполнять проводку оплаты заказа даже если фактически оплаченная сумма меньше выставленного счета";
$MESS['ONLINEDENGI_WRONG_PAY_MODE_D'] = "<br />Данная ситуация возможна при конвертации в валюту выбранного способа оплаты, в основном на малых суммах. <br /> -1 - проводку не выполнять, полученные средства записывать на пользовательский счет;<br />0 - проводку не выполнять;<br />1 - выполнять проводку;";

$MESS['ONLINEDENGI_OVERPAY_MODE_N'] = "Если сумма фактической оплаты больше выставленного счета, то лишние средства автоматически перевести на пользовательский счет:";
$MESS['ONLINEDENGI_OVERPAY_MODE_D'] = "0 - не переводить средства;<br />1 - выполнять перевод средств;";

$MESS['ONLINEDENGI_OVERPAY_STATUS_N'] = "Если сумма фактической оплаты больше выставленного счета, то заказ переводить в статус:";
$MESS['ONLINEDENGI_OVERPAY_STATUS_D'] = "<br />Укажите код (букву) статуса, если код не указан, то статус изменяться не будет. Перевод статуса создает почтовое событие. <a href=\"#HREF#\" target=\"_blank\">Доступные статусы</a>.";

$MESS['ONLINEDENGI_DEFICIT_PAY_STATUS_N'] = "Если сумма фактической оплаты меньше выставленного счета, то заказ переводить в статус:";
$MESS['ONLINEDENGI_DEFICIT_PAY_STATUS_D'] = "<br />Укажите код (букву) статуса, если код не указан, то статус изменяться не будет. Перевод статуса создает почтовое событие. <a href=\"#HREF#\" target=\"_blank\">Доступные статусы</a>.";

$MESS['ONLINEDENGI_ORDER_PAY_COMPONENT_TEMPLATE_N'] = "Имя шаблона вывода формы платежной системы";
$MESS['ONLINEDENGI_ORDER_PAY_COMPONENT_TEMPLATE_D'] = "Пусто - шаблон по умолчанию";

$MESS['ONLINEDENGI_AVAILABLE_PRE'] = "Использовать оплату через ";
$MESS['ONLINEDENGI_AVAILABLE_TYPE_D'] = "<br />1 - использовать, 0 - не использовать";

$MESS['ONLINEDENGI_MODE_TYPE_N'] = "Идентификатор способа платежа";
$MESS['ONLINEDENGI_MODE_TYPE_D'] = "<br />Если не задан, то выбор способа платежа будет предоставлен пользователю [mode_type]";

$MESS['ONLINEDENGI_PS_DESCRIPTION_RES'] = "<br /><br />Завершив настройку платежной системы, нажмите на кнопку <b>\"Применить\"</b> и воспользуйтесь <b><a href=\"#FILE_PATH#\">мастером</a> для получения адреса скрипта</b>, принимающего запрос на зачисление средств. Полученный адрес сообщите в систему OnlineDengi.";
