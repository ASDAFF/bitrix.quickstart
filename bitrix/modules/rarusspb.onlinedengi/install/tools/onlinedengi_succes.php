<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 * Внимание!!!
 * Содержимое файла onlinedengi_succes.php перезаписывается.
 * Для кастомизации данной платежной системы создайте файл с другим именем.
 * Этот файл используется обработчиком платежной системы OnlineDengi. Не удалять.
 *
 */
 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>

	<h1>Платеж успешно выполнен</h1>
	
	<p>Спасибо, что Вы выбрали сервис <a href="http://www.onlinedengi.ru" target="_blank">Деньги Онлайн</a></p>
	<p>По вопросам работы сервиса пожалуйста обращайтесь по e-mail: <a href="mailto:support@onlinedengi.ru">support@onlinedengi.ru</a> или телефону 8-800-200-03-20.</p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>