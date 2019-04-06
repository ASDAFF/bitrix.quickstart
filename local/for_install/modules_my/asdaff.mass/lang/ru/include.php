<?
$MESS['IBLOCK_FIELD_ACTIVE_DATE'] = 'Активность по дате';
$MESS['IBLOCK_FIELD_SECTION_GLOBAL_ACTIVE'] = 'Активность разделов-родителей';

$MESS['WDA_GROUP_FIELDS'] = 'Поля инфоблока';
$MESS['WDA_GROUP_PROPERTIES'] = 'Свойства инфоблока';
$MESS['WDA_GROUP_PRICES'] = 'Цены каталога';
$MESS['WDA_GROUP_CATALOG'] = 'Свойства товара';

$MESS['WDA_Y'] = 'Да';
$MESS['WDA_N'] = 'Нет';

$MESS['WDA_EQ'] = 'равно';
$MESS['WDA_NOT'] = 'не равно';
$MESS['WDA_GT'] = 'больше';
$MESS['WDA_GTE'] = 'больше или равно';
$MESS['WDA_LT'] = 'меньше';
$MESS['WDA_LTE'] = 'меньше или равно';
$MESS['WDA_ISS'] = 'задано';
$MESS['WDA_NISS'] = 'не задано';
$MESS['WDA_CON'] = 'содержит';
$MESS['WDA_NCON'] = 'не содержит';
$MESS['WDA_BEG'] = 'начинается с';
$MESS['WDA_NBEG'] = 'не начинается с';
$MESS['WDA_END'] = 'оканчивается на';
$MESS['WDA_NEND'] = 'не оканчивается на';
$MESS['WDA_GT_DATE'] = 'позднее чем';
$MESS['WDA_LT_DATE'] = 'ранее чем';

$MESS['WDA_GROUP_GENERAL'] = 'Общее';
$MESS['WDA_GROUP_IMAGES'] = 'Изображения';
$MESS['WDA_GROUP_PRICES'] = 'Цены';
$MESS['WDA_GROUP_OTHERS'] = 'Другое';

$MESS['WDA_CATALOG_QUANTITY'] = 'Остаток (общий)';
$MESS['WDA_CATALOG_WEIGHT'] = 'Вес';
$MESS['WDA_CATALOG_AVAILABLE'] = 'Доступность к покупке';
$MESS['WDA_CATALOG_PURCHASING_PRICE'] = 'Закупочная цена';
$MESS['WDA_CATALOG_VAT_EMPTY'] = '--- не выбрано ---';

// Email
$MESS['WDA_EVENT_TYPE_NAME'] = 'Уведомление об уcпешном завершении (из планировщика Cron)';
$MESS['WDA_EVENT_TYPE_DESCRIPTION'] = '';
$MESS['WDA_EVENT_MESSAGE_SUBJECT'] = '#SERVER_NAME#: Модуль выполнил назначенное задание.';
$MESS['WDA_EVENT_MESSAGE_BODY'] = 'Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Модуль успешно выполнил назначенное задание «#PROFILE_NAME#» (профиль ##PROFILE_ID#) на сайте «#SITE_NAME#» (#SERVER_NAME#).

Выполненное действие: #ACTION#

Количество обрабатываемых элементов: #COUNT_ALL#
Успешно обработано: #COUNT_SUCCESS#
Обработано с ошибками: #COUNT_FAILED#

Дата и время начала процесса: #DATETIME_START#
Дата и время завершения процесса: #DATETIME_FINISH#

Сообщение сгенерировано автоматически.
';

// Cli check
$MESS['WDA_CLI_CHECK_RUS'] = 'Проверка';
$MESS['WDA_CLI_CHECK_ENG'] = 'Proverka';
$MESS['WDA_CLI_CHECK_TITLE'] = 'Для регулярного запуска процесса (через планировщик) необходима дополнительная настройка!';
$MESS['WDA_CLI_CHECK_CONTENT'] = 'Планировщик настроен некорректно с точки зрения кодировки: в нем используется не та кодировка, которая используется на сайте. По этой причине при запуске из-под планировщика будут проблемы с русскими символами, например, "Текст1" будет обрабатываться как "___1" (это относится к названиям, описаниям, символьным кодам и всему остальному). Необходимо в папке модуля (/bitrix/modules/asdaff.mass/) создать файл php.ini c необходимыми настройками (в частности, настройками mbstring) и указывать его в команде планировщика через параметр "-c". В результате команда для планировщика будет иметь примерно такой вид:<br/><code>/usr/bin/php -c /path/to/php.ini -f /path/to/cron.php<br/><i><b><span style="color:green">При этом ручной запуск кнопкой (на странице модуля) Вы можете выполнять прямо сейчас, без дополнительной настройки.</span></b></i>';
?>