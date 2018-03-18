<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 * Внимание!!!
 * Содержимое файла onlinedengi_fail.php перезаписывается.
 * Для кастомизации данной платежной системы создайте файл с другим именем.
 * Этот файл используется обработчиком платежной системы OnlineDengi. Не удалять.
 *
 */
 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>

	<h1>Платеж не выполнен</h1>
	
	<p>Если эта ошибка повторится вновь, обратитесь в <a href="http://www.onlinedengi.ru/faq.php" target="_blank">службу поддержки Деньги Онлайн</a> 
	по e-mail: <a href="mailto:support@onlinedengi.ru">support@onlinedengi.ru</a> или телефону 8-800-200-03-20.</p>
	
</p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>