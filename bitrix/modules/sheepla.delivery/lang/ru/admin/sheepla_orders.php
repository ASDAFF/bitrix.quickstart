<?
if(LANG_CHARSET){ header('Content-Type: text/html; charset='.LANG_CHARSET); }
global $MESS;
$MESS ['SHEEPLA_ORDER_STATUS_NOTSENT'] = "Не отправлен";
$MESS ['SHEEPLA_ORDER_STATUS_SENT'] = "Отправлен";
$MESS ['SHEEPLA_ORDER_STATUS_UNKNOWN'] = "Неивестно";
$MESS ['SHEEPLA_NO_ORDERS'] = "Заказы еще не созданы";
$MESS ['SHEEPLA_ORDER_MARK_SEND'] = "Отправить";
$MESS ['SHEEPLA_ORDER_MARK_RESEND'] = "Повторно отправить";
$MESS ['SHEEPLA_ORDER_MARK_ERROR'] = "Пометить как ошибочный";
?>