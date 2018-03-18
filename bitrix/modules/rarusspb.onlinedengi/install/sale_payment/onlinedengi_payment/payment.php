<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 * Внимание!!!
 * Содержимое папки onlinedengi_payment перезаписывается.
 * Для кастомизации данной платежной системы создайте папку с другим именем.
 * Этот файл используется обработчиком платежной системы OnlineDengi. Не удалять.
 *
 */

if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/rarusspb.onlinedengi/payment/onlinedengi_payment/payment.php')) {
	include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/rarusspb.onlinedengi/payment/onlinedengi_payment/payment.php');
}
