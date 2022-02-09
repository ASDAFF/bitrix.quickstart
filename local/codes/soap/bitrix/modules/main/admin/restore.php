<?php
if (ini_get('short_open_tag') == 0)
	die('Error: short_open_tag parameter must be turned on in php.ini');
?><?
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

if (getenv('BITRIX_VA_VER'))
	define('VMBITRIX', 'defined');

if (version_compare(phpversion(),'5.3.0','<'))
	die('Error: PHP5.3 is required');

if(strpos($_SERVER['REQUEST_URI'], '/restore.php') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'].'/restore.php'))
	die('Error: this script must be started from Web Server\'s DOCUMENT ROOT');

if(isset($_SERVER["BX_PERSONAL_ROOT"]) && $_SERVER["BX_PERSONAL_ROOT"] <> "")
	define("BX_PERSONAL_ROOT", $_SERVER["BX_PERSONAL_ROOT"]);
else
	define("BX_PERSONAL_ROOT", "/bitrix");

if(!defined("START_EXEC_TIME"))
	define("START_EXEC_TIME", microtime(true));

define("STEP_TIME", defined('VMBITRIX') ? 30 : 15);
# define("DELAY", defined('VMBITRIX') ? 0 : 3); // reserved
# xdebug_start_trace();

if (function_exists('mb_internal_encoding'))
{
	switch (ini_get("mbstring.func_overload"))
	{
		case 0:
			$bUTF_serv = false;
		break;
		case 2:
			$bUTF_serv = mb_internal_encoding() == 'UTF-8';
		break;
		default:
			die('PHP parameter mbstring.func_overload='.ini_get("mbstring.func_overload").'. The only supported values are 0 or 2.');
		break;
	}
	mb_internal_encoding('ISO-8859-1');
}
else
	$bUTF_serv = false;

if (!function_exists('htmlspecialcharsbx'))
{
	function htmlspecialcharsbx($string, $flags = ENT_COMPAT)
	{
		//shitty function for php 5.4 where default encoding is UTF-8
		return htmlspecialchars($string, $flags, "ISO-8859-1");
	}
}


# http://bugs.php.net/bug.php?id=48886 - We have 2Gb file limit on Linux

#@set_time_limit(0);
ob_start();

if (@preg_match('#ru#i',$_SERVER['HTTP_ACCEPT_LANGUAGE']))
	$lang = 'ru';
elseif (@preg_match('#de#i',$_SERVER['HTTP_ACCEPT_LANGUAGE']))
	$lang = 'de';
if ($_REQUEST['lang'])
	$lang = $_REQUEST['lang'];
if (!in_array($lang,array('ru','en')))
	$lang = 'en';
define("LANG", $lang);
if (LANG=='ru' && !headers_sent())
	header("Content-type:text/html; charset=windows-1251");

$dbconn = $_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/dbconn.php";

$arc_name = $_REQUEST["arc_name"];
$mArr_ru =  array(
			"WINDOW_TITLE" => "Восстановление из резервной копии",
			"BACK" => "Назад",
			"BEGIN" => "
			<p>
			<ul>
			<li>Перейдите в административную панель своего сайта на страницу <b>Настройки &gt; Инструменты &gt; Резервное копирование</b>
			<li>Создайте полную резервную копию, которая будет включать <b>публичную часть</b>, <b>ядро</b> и <b>базу данных</b>
			</ul>
			<b>Документация:</b> <a href='http://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=35&LESSON_ID=2031' target='_blank'>http://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=35&LESSON_ID=2031</a>
			</p>
			",
			"ARC_DOWN" => "Скачать резервную копию с дальнего сайта",
			"ARC_DOWN_BITRIXCLOUD" => "Развернуть резервную копию из облака &quot;1С-Битрикс&quot;",
			"LICENSE_KEY" => "Ваш лицензионный ключ:",
			"ARC_LOCAL_NAME" => "Имя архива:",
			"DB_SELECT" => "Выберите дамп БД:",
			"DB_SETTINGS" => "Параметры подключения к базе данных",
			"DB_DEF" => "по умолчанию для выделенного сервера или виртуальной машины",
			"DB_ENV" => "восстановление в &quot;Битрикс: Веб-окружение&quot;",
			"DB_OTHER" => "установить значения вручную",
			"DB_SKIP" => "Пропустить",
			"DELETE_FILES" => "Удалить локальную резервную копию и служебные скрипты",
			"OR" => "ИЛИ",
			"ARC_DOWN_URL" => "Ссылка на архив:",
			"TITLE0" => "Подготовка архива",
			"TITLE1" => "Загрузка резервной копии",
			"TITLE_PROCESS1" => "Распаковка архива",
			"FILE_IS_ENC" => "Архив зашифрован, для продолжения распаковки необходимо ввести пароль (с учетом регистра и пробелов): ",
			"WRONG_PASS" => "Введенный пароль не верен",
			"ENC_KEY" => "Пароль: ",
			"TITLE_PROCESS2" => "Выполняется восстановление базы данных",
			"TITLE2" => "Восстановление базы данных",
			"SELECT_LANG" => "Выберите язык",
			"ARC_SKIP" => "Архив уже распакован",
			"ARC_SKIP_DESC" => "переход к восстановлению базы данных",
			"ARC_NAME" => "Архив загружен в корневую папку сервера",
			"ARC_DOWN_PROCESS" => "Загружается:",
			"ERR_LOAD_FILE_LIST" => "Ошибочный ответ от сервиса 1С-Битрикс",
			"ARC_LOCAL" => "Загрузить с локального диска",
			"ERR_NO_ARC" => "Не выбран архив для распаковки!",
			"ERR_NO_PARTS" => "Доступны не все части многотомного архива.<br>Общее число частей: ",
			"BUT_TEXT1" => "Далее",
			"BUT_TEXT_BACK" => "Назад",
			"DUMP_RETRY" => "Попробовать снова",
			"DUMP_NAME" => "Файл резервной копии базы:",
			"USER_NAME" => "Имя пользователя",
			"USER_PASS" => "Пароль",
			"BASE_NAME" => "Имя базы данных",
			"BASE_HOST" => "Сервер баз данных",
			"BASE_RESTORE" => "Восстановить",
			"ERR_NO_DUMP" => "Не выбран архив базы данных для восстановления!",
			"ERR_EXTRACT" => "Ошибка",
			"ERR_MSG" => "Ошибка!",
			"LICENSE_NOT_FOUND" => "Лицензионный ключ не найден",
			"SELECT_ARC" => "Выберите архив",
			"CNT_PARTS" => "частей",
			"ARC_LIST_EMPTY" => "Нет резервных копий, связанных с этим ключом",
			"ERR_UNKNOWN" => "Неизвестный ответ сервера",
			"ERR_UPLOAD" => "Не удалось загрузить файл на сервер",
			"ERR_DUMP_RESTORE" => "Ошибка восстановления базы данных",
			"ERR_DB_CONNECT" => "Ошибка соединения с базой данных",
			"ERR_CREATE_DB" => "Ошибка создания базы",
			"ERR_TAR_TAR" => "Присутствуют файлы с расширением tar.tar. Вместо них должны быть архивы с номерами: tar.1, tar.2 и т.д.",
			"FINISH" => "Операция выполнена успешно",
			"FINISH_MSG" => "Операция восстановления системы завершена.",
			"EXTRACT_FINISH_TITLE" => "Распаковка архива",
			"EXTRACT_FINISH_MSG" => "Распаковка архива завершена.",
			"BASE_CREATE_DB" => "Создать базу данных если не существует",
			"BASE_CLOUDS" => "Файлы из облачных хранилищ:",
			"BASE_CLOUDS_Y" => "сохранить локально",
			"BASE_CLOUDS_N" => "оставить в облаке",
			"EXTRACT_FINISH_DELL" => "Обязательно удалите скрипт restore.php и файл резервной копии из корневой директории сайта.",
			"EXTRACT_FULL_FINISH_DELL" => "Обязательно удалите скрипт restore.php, файл резервной копии из корневой директории сайта, а также дамп базы.",
			"BUT_DELL" => "Удалить",
			"FINISH_ERR_DELL" => "Не удалось удалить все временные файлы! Обязательно удалите их вручную.",
			"FINISH_ERR_DELL_TITLE" => "Ошибка удаления файлов!",
			"NO_READ_PERMS" => "Нет прав на чтение корневой папки сайта",
			"UTF8_ERROR1" => "Сайт работал в кодировке UTF-8. Конфигурация сервера не соответствует требованиям.<br>Для продолжения установите настройки PHP: mbstring.func_overload=2 и mbstring.internal_encoding=UTF-8.",
			"UTF8_ERROR2" => "Сайт работал в однобайтовой кодировке, а конфигурация сервера рассчитана на кодировку UTF-8.<br>Для продолжения установите настройки PHP: mbstring.func_overload=0 или mbstring.internal_encoding=ISO-8859-1.",
			"DOC_ROOT_WARN" => "Внимание! Во избежание проблем с доступом был переписан путь к корню сайта в настройках сайтов. Проверьте настройки сайтов.",
			"HTACCESS_WARN" => "Внимание! Файл .htaccess из архива был сохранен в корне сайта под именем .htaccess.restore, т.к. он может содержать директивы, недопустимые на данном сервере. В корне сайта создан .htaccess по умолчанию. Измените его вручную через FTP.",
			"HTACCESS_ERR_WARN" => "Внимание! Файл .htaccess из архива был сохранен в корне сайта под именем .htaccess.restore, т.к. он может содержать директивы, недопустимые на данном сервере. <br> Не удалось создать корне сайта .htaccess по умолчанию. Переименуйте файл .htaccess.restore в .htaccess через FTP.",

			"NOT_SAAS_ENV" => "Вы используете дистрибутив SaaS, он может быть развернут только на SaaS окружении",
			"NOT_SAAS_DISTR" => "Вы работаете на SaaS окружении, необходимо использовать редакцию SaaS",

'TAR_WRONG_BLOCK_SIZE' => 'Неверный размер блока: ',
'TAR_ERR_FORMAT' => 'Архив поврежден, ошибочный блок: ',
'TAR_EMPTY_FILE' => 'Пустое имя файла, ошибочный блок: ',
'TAR_ERR_CRC' => 'Ошибка контрольной суммы на файле: ',
'TAR_ERR_FOLDER_CREATE' => 'Не удалось создать папку: ',
'TAR_ERR_FILE_CREATE' => 'Не удалось создать файл: ',
'TAR_ERR_FILE_OPEN' => 'Не удалось открыть файл: ',
'TAR_ERR_FILE_SIZE' => 'Размер файла отличается: ',
'TAR_ERR_WRITE_HEADER' => 'Ошибка записи заголовка',
'TAR_PATH_TOO_LONG' => 'Слишком длинный путь: ',
'TAR_ERR_FILE_READ' => 'Ошибка чтения файла: ',
'TAR_ERR_FILE_WRITE' => 'Ошибка записи на файле: ',
'TAR_ERR_FILE_NO_ACCESS' => 'Нет доступа к файлу: ',
'TAR_NO_GZIP' => 'Не доступна функция gzopen',
		"ARC_DOWN_OK" => "Все части архива загружены",
			);

$mArr_en = array(
			"WINDOW_TITLE" => "Restoring",
			"BACK" => "Back",
			"BEGIN" => "
			<p>
			<ul>
			<li>Open Control Panel section of your old site and select <b>Settings &gt; Tools &gt; Backup</b>
			<li>Create full archive which contains <b>public site files</b>, <b>kernel files</b> and <b>database dump</b>
			</ul>
			<b>Documentation:</b> <a href='http://www.bitrixsoft.com/support/training/course/lesson.php?COURSE_ID=12&ID=441' target='_blank'>learning course</a>
			</p>
			",
			"ARC_DOWN" => "Download from remote server",
			"ARC_DOWN_BITRIXCLOUD" => "Restore the backup from the Bitrix Cloud",
			"LICENSE_KEY" => "Your license key:",
			"ARC_LOCAL_NAME" => "Archive name:",
			"DB_SELECT" => "Select Database Dump:",
			"DB_SETTINGS" => "Database settings",
			"DB_DEF" => "default values for Dedicated Server or Virtual Machine",
			"DB_ENV" => "restoring in Bitrix Environment",
			"DB_OTHER" => "custom database settings",
			"DB_SKIP" => "Skip",
			"DELETE_FILES" => "Delete archive and temporary scripts",
			"OR" => "OR",
			"ARC_DOWN_URL" => "Archive URL:",
			"TITLE0" => "Archive Creation",
			"TITLE1" => "Archive download",
			"TITLE_PROCESS1" => "Extracting an archive",
			"TITLE_PROCESS2" => "Restoring database...",
			"FILE_IS_ENC" => "Archive is encrypted. Enter password: ",
			"WRONG_PASS" => "Wrong password",
			"ENC_KEY" => "Password: ",
			"TITLE2" => "Database restore",
			"SELECT_LANG" => "Choose the language",
			"ARC_SKIP" => "Archive is already extracted",
			"ARC_SKIP_DESC" => "Starting database restore",
			"ARC_NAME" => "Archive is stored in document root folder",
			"ARC_DOWN_PROCESS" => "Downloading:",
			"ERR_LOAD_FILE_LIST" => "Wrong Bitrixsoft server response",
			"ARC_LOCAL" => "Upload from local disk",
			"ERR_NO_ARC" => "Archive for extracting is not specified!",
			"ERR_NO_PARTS" => "Some parts of the multivolume archive are missed.<br>Total number of parts: ",
			"BUT_TEXT1" => "Continue",
			"BUT_TEXT_BACK" => "Back",
			"DUMP_RETRY" => "Retry",
			"DUMP_NAME" => "Database dump file:",
			"USER_NAME" => "Database User Name",
			"USER_PASS" => "Password",
			"BASE_NAME" => "Database Name",
			"BASE_HOST" => "Database Host",
			"BASE_RESTORE" => "Restore",
			"ERR_NO_DUMP" => "Database dump file is not specified!",
			"ERR_EXTRACT" => "Error",
			"ERR_MSG" => "Error!",
			"LICENSE_NOT_FOUND" => "License not found",
			"SELECT_ARC" => "Select backup",
			"CNT_PARTS" => "parts",
			"ARC_LIST_EMPTY" => "Backup list is empty for current license key",
			"ERR_UNKNOWN" => "Unknown server response",
			"ERR_UPLOAD" => "Unable to upload file",
			"ERR_DUMP_RESTORE" => "Error restoring the database:",
			"ERR_DB_CONNECT" => "Error connecting the database:",
			"ERR_CREATE_DB" => "Error creating the database",
			"ERR_TAR_TAR" => "There are files with tar.tar extension presents. Should be tar.1, tar.2 and so on",
			"FINISH" => "Successfully completed",
			"FINISH_MSG" => "Restoring of the system was completed.",
			"EXTRACT_FINISH_TITLE" => "Archive extracting",
			"EXTRACT_FINISH_MSG" => "Archive extracting was completed.",
			"BASE_CREATE_DB" => "Create database",
			"BASE_CLOUDS" => "Cloud files:",
			"BASE_CLOUDS_Y" => "store locally",
			"BASE_CLOUDS_N" => "leave in the cloud",
			"EXTRACT_FINISH_DELL" => "Warning! You should delete restore.php script and backup copy file from the root folder of your site!",
			"EXTRACT_FULL_FINISH_DELL" => "Warning! You should delete restore.php script, backup copy file and database dump from the root folder of your site!",
			"BUT_DELL" => "Delete",
			"FINISH_ERR_DELL" => "Failed to delete temporary files! You should delete them manually",
			"FINISH_ERR_DELL_TITLE" => "Error deleting the files!",
			"NO_READ_PERMS" => "No permissions for reading Web Server root",
			"UTF8_ERROR1" => "Your server is not configured for UTF-8 encoding. Please set mbstring.func_overload=2 and mbstring.internal_encoding=UTF-8 to continue.",
			"UTF8_ERROR2" => "Your server is configured for UTF-8 encoding. Please set mbstring.func_overload=0 or mbstring.internal_encoding=ISO-8859-1 to continue.",
			"DOC_ROOT_WARN" => "Warning!  To prevent access problems the document root has been cleared in the site settings.",
			"HTACCESS_WARN" => "Warning! The file .htaccess was saved as .htaccess.restore, because it may contain directives which are not permitted on this server. Default .htaccess file has been created at the document root. Please modify it manually using FTP.",
			"HTACCESS_ERR_WARN" => "Warning! The file .htaccess was saved as .htaccess.restore, because it may contain directives which are not permitted on this server. There was an error in creating default .htaccess file. Please rename .htaccess.restore to .htaccess using FTP.",

'TAR_WRONG_BLOCK_SIZE' => 'Wrong Block size: ',
'TAR_ERR_FORMAT' => 'Archive is currupted, error block: ',
'TAR_EMPTY_FILE' => 'Empty filename, block: ',
'TAR_ERR_CRC' => 'Checksum error on file: ',
'TAR_ERR_FOLDER_CREATE' => 'Can\'t create folder: ',
'TAR_ERR_FILE_CREATE' => 'Can\'t create file: ',
'TAR_ERR_FILE_OPEN' => 'Can\'t open file: ',
'TAR_ERR_FILE_SIZE' => 'Filesize differs: ',
'TAR_ERR_WRITE_HEADER' => 'Error writing header',
'TAR_PATH_TOO_LONG' => 'Path is too long: ',
'TAR_ERR_FILE_READ' => 'Error reading file: ',
'TAR_ERR_FILE_WRITE' => 'Error adding file: ',
'TAR_ERR_FILE_NO_ACCESS' => 'No access to file: ',
'TAR_NO_GZIP' => 'PHP extension GZIP is not available',
		"ARC_DOWN_OK" => "All archive parts have been downloaded",
			);

	$MESS = array();
	if (LANG=="ru")
	{
		$MESS["LOADER_SUBTITLE1"] = "Загрузка резервной копии";
		$MESS["LOADER_SUBTITLE1_ERR"] = "Ошибка загрузки";
		$MESS["STATUS"] = "% выполнено...";
		$MESS["LOADER_MENU_UNPACK"] = "Распаковка файла";
		$MESS["LOADER_TITLE_LIST"] = "Выбор файла";
		$MESS["LOADER_TITLE_LOAD"] = "Загрузка файла на сайт";
		$MESS["LOADER_TITLE_UNPACK"] = "Распаковка файла";
		$MESS["LOADER_TITLE_LOG"] = "Отчет по загрузке";
		$MESS["LOADER_NEW_LOAD"] = "Загрузить";
		$MESS["LOADER_BACK_2LIST"] = "Вернуться в список файлов";
		$MESS["LOADER_LOG_ERRORS"] = "Загрузка резервной копии не удалась";
		$MESS["LOADER_NO_LOG"] = "Log-файл не найден";
		$MESS["LOADER_KB"] = "кб";
		$MESS["LOADER_LOAD_QUERY_SERVER"] = "Подключение к серверу...";
		$MESS["LOADER_LOAD_QUERY_DISTR"] = "Запрашиваю файл #DISTR#";
		$MESS["LOADER_LOAD_CONN2HOST"] = "Подключение к серверу #HOST#...";
		$MESS["LOADER_LOAD_NO_CONN2HOST"] = "Не могу соединиться с #HOST#:";
		$MESS["LOADER_LOAD_QUERY_FILE"] = "Запрашиваю файл...";
		$MESS["LOADER_LOAD_WAIT"] = "Ожидаю ответ...";
		$MESS["LOADER_LOAD_SERVER_ANSWER"] = "Ошибка загрузки. Сервер ответил: #ANS#";
		$MESS["LOADER_LOAD_SERVER_ANSWER1"] = "Ошибка загрузки. У вас нет прав на доступ к этому файлу. Сервер ответил: #ANS#";
		$MESS["LOADER_LOAD_NEED_RELOAD"] = "Ошибка загрузки. Докачка файла невозможна.";
		$MESS["LOADER_LOAD_NO_WRITE2FILE"] = "Не могу открыть файл #FILE# на запись";
		$MESS["LOADER_LOAD_LOAD_DISTR"] = "Загружаю файл #DISTR#";
		$MESS["LOADER_LOAD_ERR_SIZE"] = "Ошибка размера файла";
		$MESS["LOADER_LOAD_ERR_RENAME"] = "Не могу переименовать файл #FILE1# в файл #FILE2#";
		$MESS["LOADER_LOAD_CANT_OPEN_WRITE"] = "Не могу открыть файл #FILE# на запись";
		$MESS["LOADER_LOAD_CANT_REDIRECT"] = "Ошибочное перенаправление на адрес #URL#. Проверьте адрес для скачивания.";
		$MESS["LOADER_LOAD_CANT_OPEN_READ"] = "Не могу открыть файл #FILE# на чтение";
		$MESS["LOADER_LOAD_LOADING"] = "Загружаю файл... дождитесь окончания загрузки...";
		$MESS["LOADER_LOAD_FILE_SAVED"] = "Файл сохранен: #FILE# [#SIZE# байт]";
		$MESS["LOADER_UNPACK_ACTION"] = "Распаковываю файл... дождитесь окончания распаковки...";
		$MESS["LOADER_UNPACK_UNKNOWN"] = "Неизвестная ошибка. Повторите процесс еще раз или обратитесь в службу технической поддержки";
		$MESS["LOADER_UNPACK_SUCCESS"] = "Файл успешно распакован";
		$MESS["LOADER_UNPACK_ERRORS"] = "Файл распакован с ошибками";
		$MESS["LOADER_KEY_DEMO"] = "Демонстрационная версия";
		$MESS["LOADER_KEY_COMM"] = "Коммерческая версия";
		$MESS["UPDATE_SUCCESS"] = "Обновлено успешно. <a href='?'>Открыть</a>.";
		$MESS["LOADER_NEW_VERSION"] = "Доступна новая версия скрипта восстановления, но загрузить её не удалось";
	}
	else
	{
		$MESS["LOADER_SUBTITLE1"] = "Loading";
		$MESS["LOADER_SUBTITLE1_ERR"] = "Loading Error";
		$MESS["STATUS"] = "% done...";
		$MESS["LOADER_MENU_LIST"] = "Select package";
		$MESS["LOADER_MENU_UNPACK"] = "Unpack file";
		$MESS["LOADER_TITLE_LIST"] = "Select file";
		$MESS["LOADER_TITLE_LOAD"] = "Uploading file to the site";
		$MESS["LOADER_TITLE_UNPACK"] = "Unpack file";
		$MESS["LOADER_TITLE_LOG"] = "Upload report";
		$MESS["LOADER_NEW_ED"] = "package edition";
		$MESS["LOADER_NEW_AUTO"] = "automatically start unpacking after loading";
		$MESS["LOADER_NEW_STEPS"] = "load gradually with interval:";
		$MESS["LOADER_NEW_STEPS0"] = "unlimited";
		$MESS["LOADER_NEW_LOAD"] = "Download";
		$MESS["LOADER_BACK_2LIST"] = "Back to packages list";
		$MESS["LOADER_LOG_ERRORS"] = "Error occured";
		$MESS["LOADER_NO_LOG"] = "Log file not found";
		$MESS["LOADER_KB"] = "kb";
		$MESS["LOADER_LOAD_QUERY_SERVER"] = "Connecting server...";
		$MESS["LOADER_LOAD_QUERY_DISTR"] = "Requesting package #DISTR#";
		$MESS["LOADER_LOAD_CONN2HOST"] = "Connection to #HOST#...";
		$MESS["LOADER_LOAD_NO_CONN2HOST"] = "Cannot connect to #HOST#:";
		$MESS["LOADER_LOAD_QUERY_FILE"] = "Requesting file...";
		$MESS["LOADER_LOAD_WAIT"] = "Waiting for response...";
		$MESS["LOADER_LOAD_SERVER_ANSWER"] = "Error while downloading. Server reply was: #ANS#";
		$MESS["LOADER_LOAD_SERVER_ANSWER1"] = "Error while downloading. Your can not download this package. Server reply was: #ANS#";
		$MESS["LOADER_LOAD_NEED_RELOAD"] = "Error while downloading. Cannot resume download.";
		$MESS["LOADER_LOAD_NO_WRITE2FILE"] = "Cannot open file #FILE# for writing";
		$MESS["LOADER_LOAD_LOAD_DISTR"] = "Downloading package #DISTR#";
		$MESS["LOADER_LOAD_ERR_SIZE"] = "File size error";
		$MESS["LOADER_LOAD_ERR_RENAME"] = "Cannot rename file #FILE1# to #FILE2#";
		$MESS["LOADER_LOAD_CANT_OPEN_WRITE"] = "Cannot open file #FILE# for writing";
		$MESS["LOADER_LOAD_CANT_REDIRECT"] = "Wrong redirect to #URL#. Check download url.";
		$MESS["LOADER_LOAD_CANT_OPEN_READ"] = "Cannot open file #FILE# for reading";
		$MESS["LOADER_LOAD_LOADING"] = "Download in progress. Please wait...";
		$MESS["LOADER_LOAD_FILE_SAVED"] = "File saved: #FILE# [#SIZE# bytes]";
		$MESS["LOADER_UNPACK_ACTION"] = "Unpacking the package. Please wait...";
		$MESS["LOADER_UNPACK_UNKNOWN"] = "Unknown error occured. Please try again or consult the technical support service";
		$MESS["LOADER_UNPACK_SUCCESS"] = "The file successfully unpacked";
		$MESS["LOADER_UNPACK_ERRORS"] = "Errors occured while unpacking the file";
		$MESS["LOADER_KEY_DEMO"] = "Demo version";
		$MESS["LOADER_KEY_COMM"] = "Commercial version";
		$MESS["UPDATE_SUCCESS"] = "Successful update. <a href='?'>Open</a>.";
		$MESS["LOADER_NEW_VERSION"] = "Error occured while updating restore.php script!";
	}


$bSelectDumpStep = false;
if ($_REQUEST['source'] == 'dump')
	$bSelectDumpStep = true;

$Step = IntVal($_REQUEST["Step"]);

$strErrMsg = '';
if (!$Step)
{
	$this_script_name = basename(__FILE__);
	$bx_host = 'www.1c-bitrix.ru';
	$bx_url = '/download/files/scripts/'.$this_script_name;
	$form = '';

	// Check for updates
	$res = @fsockopen($bx_host, 80, $errno, $errstr, 3);

	if($res)
	{
		$strRequest = "HEAD ".$bx_url." HTTP/1.1\r\n";
		$strRequest.= "Host: ".$bx_host."\r\n";
		$strRequest.= "\r\n";

		fputs($res, $strRequest);

		while ($line = fgets($res, 4096))
		{
			if (@preg_match("/Content-Length: *([0-9]+)/i", $line, $regs))
			{
				if (filesize(__FILE__) != trim($regs[1]))
				{
					$tmp_name = $this_script_name.'.tmp';
					if (LoadFile('http://'.$bx_host.$bx_url, $tmp_name))
					{
						if (rename($_SERVER['DOCUMENT_ROOT'].'/'.$tmp_name,__FILE__))
						{
							bx_accelerator_reset();
							echo '<script>document.location="?lang='.LANG.'";</script>'.LoaderGetMessage('UPDATE_SUCCESS');
							die();
						}
						else
							$strErrMsg = str_replace("#FILE#", $this_script_name, LoaderGetMessage("LOADER_LOAD_CANT_OPEN_WRITE"));
					}
					else
						$strErrMsg = LoaderGetMessage('LOADER_NEW_VERSION');
				}
				break;
			}
		}
		fclose($res);
	}
}

if ($_REQUEST['LoadFileList'])
{
	$strLog = '';
	if (LoadFile("http://www.1c-bitrix.ru/buy_tmp/backup.php?license=".md5(trim($_REQUEST['license_key']))."&lang=".LANG."&action=get_info", $file = $_SERVER['DOCUMENT_ROOT'].'/file_list.xml') && ($str = file_get_contents($file)))
	{
		if (preg_match_all('/<file name="([^"]+)" size="([^"]+)".*?\\/>/', $str, $regs))
		{
			$arFiles = array();
			$arParts = array();
			foreach($regs[0] as $i => $wholeMatch)
			{
				$name = CTar::getFirstName($regs[1][$i]);
				$arFiles[$name] += $regs[2][$i];
				$arParts[$name]++;
			}
			krsort($arFiles);

			echo getMsg('SELECT_ARC').':&nbsp;<select name="bitrixcloud_backup">';
			foreach($arFiles as $name => $size)
				echo '<option value="'.htmlspecialcharsbx($name).'" '.($_REQUEST['bitrixcloud_backup'] == $name ? 'selected' : '').'>'.htmlspecialcharsbx($name).' ('.floor($size/1024/1024), ' Mb'.($arParts[$name] > 1 ? ', '.getMsg('CNT_PARTS').': '.$arParts[$name] : '').')</option>';
			echo '</select><br>';
			echo getMsg('ENC_KEY').'&nbsp;<input type="password" size=30 name="EncryptKey" autocomplete="off">';
		}
		else
		{
			if (strpos($str, '<files>') !== false) // valid answer
				$strErrMsg = getMsg('ARC_LIST_EMPTY');
			elseif (preg_match('#error#i',$str))
			{
				$code = strip_tags($str);
				if ($code == 'LICENSE_NOT_FOUND')
					$strErrMsg = getMsg('LICENSE_NOT_FOUND');
				else
					$strErrMsg = $code;
			}
			else
				$strErrMsg = getMsg('ERR_UNKNOWN');
			echo '<div style="color:red">'.getMsg('ERR_MSG').' '.$strErrMsg.'</div>';
		}
		unlink($file);
	}
	else
		echo '<div style="color:red">'.getMsg('ERR_LOAD_FILE_LIST').'</div><div style="text-align:left;color:#CCC">'.nl2br($strLog).'</div>';
	die();
}
elseif ($Step == 2 && !$bSelectDumpStep)
{
	if (is_array($_REQUEST['arHeaders']))
		$arHeaders = $_REQUEST['arHeaders'];
	else
		$arHeaders = array();

	$source = $_REQUEST['source'];
	if ($source == 'bitrixcloud')
	{
		$source = 'download';
		$strLog = '';
		if (LoadFile('http://www.1c-bitrix.ru/buy_tmp/backup.php?license='.md5($_REQUEST['license_key']).'&lang='.LANG.'&action=read_file&file_name='.urlencode($_REQUEST['bitrixcloud_backup']).'&check_word='.CTar::getCheckword($_REQUEST['EncryptKey']), $file = $_SERVER['DOCUMENT_ROOT'].'/file_info.xml') && ($str = file_get_contents($file)))
		{
			unlink($file);
//			echo htmlspecialcharsbx($str);

			$host = preg_match('#<host>([^<]+)</host>#i',$str,$regs) ? $regs[1] : false;
//			$port = preg_match('#<port>([^<]+)</port>#i',$str,$regs) ? $regs[1] : false;
			$path = preg_match('#<path>([^<]+)</path>#i',$str,$regs) ? $regs[1] : false;

			if (preg_match_all('/<header name="([^"]+)" value="([^"]+)".*?\\/>/', $str, $regs))
			{
				foreach($regs[0] as $i => $wholeMatch)
					$arHeaders[$regs[1][$i]] = $regs[2][$i];
			}


			if ($host && $path)
			{
				$_REQUEST['arc_down_url'] = $host.$path;
			}
			elseif (strpos($str, 'WRONG_FILE_NAME_OR_CHECKWORD') !== false)
				$strErrMsg = '<div style="color:red">'.getMsg('WRONG_PASS').'</div>';
			else
				$strErrMsg = '<div style="color:red">'.getMsg('ERR_LOAD_FILE_LIST').'</div>';
		}
		else
			$strErrMsg = '<div style="color:red">'.getMsg('ERR_LOAD_FILE_LIST').'</div><div style="text-align:left;color:#CCC">'.nl2br($strLog).'</div>';

		if (!$_REQUEST['try_next'] && $strErrMsg)
		{
			$text = $strErrMsg.
			getMsg('ENC_KEY').'<input type="password" size=30 name="EncryptKey" autocomplete="off" value="">'.
			'<input type="hidden" name="license_key" value="'.htmlspecialcharsbx($_REQUEST['license_key']).'">'.
			'<input type="hidden" name="source" value="bitrixcloud">'.
			'<input type="hidden" name="bitrixcloud_backup" value="'.htmlspecialcharsbx($_REQUEST['bitrixcloud_backup']).'">'.
			'<input type="hidden" name="Step" value="2">';
			$bottom .= '
			<input type="button" value="'.getMsg('BUT_TEXT_BACK').'" onClick="document.location=\'/restore.php?Step=1&lang='.LANG.'\'">
			<input type="button" id="start_button" value="'.getMsg("BUT_TEXT1", LANG).'" onClick="reloadPage(2, \''. LANG.'\')">';
			showMsg(getMsg('TITLE1'),$text,$bottom);
			die();
		}
	}

	if ($source == 'download')
	{
		$strUrl = $_REQUEST['arc_down_url'];

		if (!preg_match('#http://#',$strUrl))
			$strUrl = 'http://'.$strUrl;
		$arc_name = trim(basename($strUrl));

		$strLog = '';
		$status = '';

		if ($_REQUEST['continue'])
		{
			$res = LoadFile($strUrl, $_SERVER['DOCUMENT_ROOT'].'/'.$arc_name, $arHeaders);
		}
		else // начало закачки
		{
			$res = 2;
			SetCurrentProgress(0);
		}

		if ($res)
		{
			$text = getMsg('ARC_DOWN_PROCESS').' <b>'.htmlspecialcharsbx($arc_name).'</b>' . $status .
			'<input type=hidden name=Step value=2>'.
			'<input type=hidden name=continue value=Y>'.
			'<input type=hidden name="EncryptKey" value="'.htmlspecialcharsbx($_REQUEST['EncryptKey']).'">'.
			'<input type=hidden name="license_key" value="'.htmlspecialcharsbx($_REQUEST['license_key']).'">';

			if ($res == 2)
			{
				$text .= '<input type=hidden name=arc_down_url value="'.htmlspecialcharsbx($strUrl).'">';
				$text .= '<input type=hidden name=source value=download>';
				$text .= '<input type=hidden name="bitrixcloud_backup" value="'.htmlspecialcharsbx($_REQUEST['bitrixcloud_backup']).'">';
				foreach($arHeaders as $k=>$v)
					$text .= '<input type=hidden name="arHeaders['.htmlspecialcharsbx($k).']" value="'.htmlspecialcharsbx($v).'">';
			}
			else
			{
				$text .= '<input type=hidden name=try_next value=Y>';
				if (count($arHeaders)) // bitrixcloud
				{
					$text .= '<input type=hidden name=source value=bitrixcloud>';
					$text .= '<input type=hidden name="bitrixcloud_backup" value="'.htmlspecialcharsbx(CTar::getNextName($_REQUEST['bitrixcloud_backup'])).'">';
				}
				else
				{
					$text .= '<input type=hidden name=source value=download>';
					$text .= '<input type=hidden name=arc_down_url value="'.htmlspecialcharsbx(CTar::getNextName($strUrl)).'">';
				}
			}
		}
		elseif ($_REQUEST['try_next']) // пробовали новую часть
		{
			$text = getMsg('ARC_DOWN_OK').
			'<input type=hidden name=Step value=2>'.
			'<input type=hidden name="EncryptKey" value="'.htmlspecialcharsbx($_REQUEST['EncryptKey']).'">';

			if ($_REQUEST['bitrixcloud_backup'])
				$text .= '<input type=hidden name=arc_name value="'.htmlspecialcharsbx(CTar::getFirstName($_REQUEST['bitrixcloud_backup'])).'">';
			else
				$text .= '<input type=hidden name=arc_name value="'.htmlspecialcharsbx(CTar::getFirstName($arc_name)).'">';
		}
		else
		{
			if ($_REQUEST['source'] != 'bitrixcloud' && $replycode == 403 && count($arHeaders)) // Retry for bitrixcloud
			{
				$text = getMsg('ARC_DOWN_PROCESS').' <b>'.htmlspecialcharsbx($arc_name).'</b>' . $status .
					'<input type=hidden name=Step value=2>'.
					'<input type=hidden name=continue value=Y>'.
					'<input type=hidden name="EncryptKey" value="'.htmlspecialcharsbx($_REQUEST['EncryptKey']).'">'.
					'<input type=hidden name="license_key" value="'.htmlspecialcharsbx($_REQUEST['license_key']).'">';
				$text .= '<input type=hidden name=source value=bitrixcloud>';
				$text .= '<input type=hidden name="bitrixcloud_backup" value="'.htmlspecialcharsbx($_REQUEST['bitrixcloud_backup']).'">';

//				$text .= '<input type=hidden name=arc_down_url value="'.htmlspecialcharsbx($strUrl).'">';
			}
			else
			{
				$ar = array(
					'TITLE' => LoaderGetMessage('LOADER_SUBTITLE1_ERR'),
					'TEXT' => nl2br($strLog),
					'BOTTOM' => '<input type="button" value="'.getMsg('BUT_TEXT_BACK').'" onClick="document.location=\'/restore.php?Step=1&lang='.LANG.'\'"> '
				);
				html($ar);
				die();
			}
		}
		$bottom = '<input type="button" value="'.getMsg('BUT_TEXT_BACK').'" onClick="document.location=\'/restore.php?Step=1&lang='.LANG.'\'"> ';
		showMsg(LoaderGetMessage('LOADER_SUBTITLE1'),$text,$bottom);
		?><script>reloadPage(2, '<?= LANG?>', 1);</script><?
		die();
	}
	elseif($source == 'upload')
	{
		$tmp = $_FILES['archive'];
		$arc_name = $_REQUEST['arc_name'] = 'uploaded_archive.tar.gz';
		if (@move_uploaded_file($tmp['tmp_name'],$_SERVER['DOCUMENT_ROOT'].'/'.$arc_name))
		{
			$text =
			'<input type=hidden name=Step value=2>'.
			'<input type=hidden name=arc_name value="'.($arc_name).'">';
			showMsg(LoaderGetMessage('LOADER_SUBTITLE1'),$text);
			?><script>reloadPage(2, '<?= LANG?>', 1);</script><?
			die();
		}
		else
		{
			$ar = array(
				'TITLE' => getMsg('ERR_EXTRACT'),
				'TEXT' => getMsg('ERR_UPLOAD'),
				'BOTTOM' => '<input type="button" value="'.getMsg('BUT_TEXT_BACK').'" onClick="document.location=\'/restore.php?Step=1&lang='.LANG.'\'"> '
			);
			html($ar);
			die();
		}
	}
}
elseif($Step == 3)
{
	if ($_REQUEST['db_settings'] == 'skip')
		$Step++;
	else
	{
		$d_pos = (double) $_REQUEST["d_pos"];
		if ($d_pos < 0)
			$d_pos = 0;

		$oDB = new CDBRestore($_REQUEST["DBHost"], $_REQUEST["DBName"], $_REQUEST["DBLogin"], $_REQUEST["DBPassword"], $_REQUEST["dump_name"], $d_pos);
		$oDB->LocalCloud = $_REQUEST['LocalCloud'];

		if(!$oDB->Connect())
		{
			$strErrMsg = $oDB->getError();
			$Step = 2;
			$bSelectDumpStep = true;
		}
	}
}





if(!$Step)
{
	$ar = array(
		'TITLE' => getMsg("TITLE0", LANG),
		'TEXT' =>
			($strErrMsg ? '<div style="color:red;padding:10px;border:1px solid red">'.$strErrMsg.'</div>' : '').
			getMsg('BEGIN') .
			'<br>' .
			(file_exists($img = 'images/dump'.(LANG=='ru'?'_ru':'').'.png') ? '<img src="'.$img.'">' : ''),
		'BOTTOM' =>
		(defined('VMBITRIX') ? '<input type=button value="'.getMsg('BUT_TEXT_BACK').'" onClick="document.location=\'/\'"> ' : '').
		'<input type="button" value="'.getMsg("BUT_TEXT1", LANG).'" onClick="reloadPage(1,\''.LANG.'\')">'
	);
	html($ar);
}
elseif($Step == 1)
{
	$arc_down_url = $_REQUEST['arc_down_url'] ? $_REQUEST['arc_down_url'] : 'http://example.com/bitrix/backup/2012-05-02.18-27-04.s1.368e07e6.tar.gz';
	$local_arc_name = htmlspecialcharsbx(ltrim($_REQUEST['local_arc_name'],'/'));
	if ($_REQUEST['bitrixcloud_backup'])
	{
		@include($_SERVER['DOCUMENT_ROOT'].'/bitrix/license_key.php');
		$license_key = $LICENSE_KEY;
	}

	$option = getArcList();
	$ar = array(
		'TITLE' => getMsg("TITLE1", LANG),
		'TEXT' =>
				$local_arc_name
				?
				'<div class=t_div><input type=hidden name=arc_name value="'.$local_arc_name.'"> '.getMsg("ARC_LOCAL_NAME", LANG).' <b>'.$local_arc_name.'</div>'
				:
				($strErrMsg ? '<div style="color:red">'.$strErrMsg.'</div>' : '').
				'<input type="hidden" name="Step" value="2">'.
				'<div class=t_div>
					<label><input type=radio name=x_source onclick="div_show(0)" '.($_REQUEST['bitrixcloud_backup'] ? 'checked' : '').'>'.getMsg("ARC_DOWN_BITRIXCLOUD", LANG).'</label>
					<div id=div0 class="div-tool" style="display:none" align="right">
						<nobr>'.getMsg("LICENSE_KEY").'</nobr> <input name=license_key id=license_key size=30 value="'.htmlspecialcharsbx($license_key).'"> <input type="button" value=" OK " onclick="LoadFileList()"><br>
						<div id=file_list></div>
					</div>
				</div>
				<div class=t_div>
					<label><input type=radio name=x_source onclick="div_show(1)" '.($_REQUEST['arc_down_url'] ? 'checked' : '').'>'.getMsg("ARC_DOWN", LANG).'</label>
					<div id=div1 class="div-tool" style="display:none" align="right"><nobr>'.getMsg("ARC_DOWN_URL").'</nobr> <input name=arc_down_url size=40 value="'.htmlspecialcharsbx($arc_down_url).'"></div>
				</div>
				<div class=t_div>
					<label><input type=radio name=x_source onclick="div_show(2)">'. getMsg("ARC_LOCAL", LANG).'</label>
					<div id=div2 class="div-tool" style="display:none"><input type=file name=archive size=40></div>
				</div>
				'
				.(strlen($option) ?
				'<div class=t_div>
					<label><input type=radio name=x_source onclick="div_show(3)">'.getMsg("ARC_NAME", LANG).'</label>
					<div id=div3 class="div-tool" style="display:none">
						<select name="arc_name">'.$option.'</select>
					</div>'.
				'</div>'
				: '')
				.($option === false ? '<div style="color:red">'.getMsg('NO_READ_PERMS', LANG).'</div>' : '')
				.(count(getDumpList()) ?
				'<div class=t_div>'.
					'<label><input type=radio name=x_source onclick="div_show(4)">'.getMsg("ARC_SKIP", LANG).'</label>
					<div id=div4 class="div-tool" style="display:none;color:#999999">'.getMsg('ARC_SKIP_DESC').'</div>
				</div>' : '')
				,
		'BOTTOM' =>
		'<input type="button" value="'.getMsg('BUT_TEXT_BACK').'" onClick="document.location=\'/restore.php?Step=&lang='.LANG.'\'"> '.
		'<input type="button" id="start_button" value="'.getMsg("BUT_TEXT1", LANG).'" onClick="reloadPage(2,\''.LANG.'\')" '.($local_arc_name ? '' : 'disabled').'>'
	);
	html($ar);
	?>
	<script>
		function div_show(i)
		{
			document.getElementById('start_button').disabled = i == 0;
			for(j=0;j<=4;j++)
			{
				if (ob = document.getElementById('div' + j))
					ob.style.display = i == j ? 'block' : 'none';
			}

			arSources = [ 'bitrixcloud','download','upload','local','dump' ];
			strAdditionalParams = '&source=' + arSources[i]; // Если большой POST запрос очищается сервером, то данные GET сохранятся для дальнейшей обработки
		}

		function LoadFileList()
		{
			xml = new XMLHttpRequest(); // forget IE6

			xml.onreadystatechange = function ()
			{
				if (xml.readyState == 4)
				{
					str = xml.responseText;
					document.getElementById('file_list').innerHTML = str;
					document.getElementById('start_button').disabled = !/<select/.test(str);
				}
			}

			xml.open('POST', '/restore.php', true);
			query = 'LoadFileList=Y&bitrixcloud_backup=<?=htmlspecialcharsbx($_REQUEST['bitrixcloud_backup'])?>&license_key=' + document.getElementById('license_key').value;

			xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xml.send(query);
		}

		<? if ($_REQUEST['arc_down_url']) { ?>
			window.onload = div_show(1);
		<? } elseif ($_REQUEST['bitrixcloud_backup']) { ?>
			window.onload = function() {
				div_show(0);
				LoadFileList();
			}
		<? } ?>
	</script>
	<style type="text/css">
		.div-tool
		{
			border:1px solid #CCCCCC;
			padding:10px;
		}
		.t_div
		{
			padding:5px;
		}
	</style>
	<?
}
elseif($Step == 2)
{
	if(!$bSelectDumpStep)
	{
		$tar = new CTarRestore;
		$tar->path = $_SERVER['DOCUMENT_ROOT'];
		$tar->ReadBlockCurrent = intval($_REQUEST['ReadBlockCurrent']);
		$tar->EncryptKey = $_REQUEST['EncryptKey'];

		$bottom = '<input type="button" value="'.getMsg('BUT_TEXT_BACK').'" onClick="document.location=\'/restore.php?Step=1&lang='.LANG.'\'"> ';

		if ($rs = $tar->openRead($file1 = $file = $_SERVER['DOCUMENT_ROOT'].'/'.$arc_name))
		{
			$DataSize = intval($_REQUEST['DataSize']);
			$skip = '';

			if(!$DataSize) // first step
			{
				$Block = $tar->Block;
				if (!$ArchiveSize = $tar->getDataSize($file))
					$ArchiveSize = filesize($file) * 2; // for standard gzip files
				$DataSize = $ArchiveSize;

				while(file_exists($file1 = CTar::getNextName($file1)))
					$DataSize += $ArchiveSize;

				$r = true;
				SetCurrentProgress(0);

				if ($n = CTar::getLastNum($file))
				{
					for($i=1;$i<=$n;$i++)
					{
						if (!file_exists($file.'.'.$i))
						{
							$strErrMsg = getMsg('ERR_NO_PARTS').' <b>'.($n + 1).'</b>';
							$r = false;
							break;
						}
					}
				}

			}
			else
			{
				$Block = intval($_REQUEST['Block']);
				$skip = ' <input type=button value="'.getMsg('DB_SKIP').'" onClick="reloadPage(2, \''. LANG.'\')">';
				if ($r = $tar->SkipTo($Block))
				{
					while(($r = $tar->extractFile()) && haveTime());
				}
				$strErrMsg = implode('<br>',$tar->err);
			}

			if ($r === 0) // Finish
				$bSelectDumpStep = true;
			else
			{
				SetCurrentProgress(($tar->BlockHeader + $tar->ReadBlockCurrent) * 512,$DataSize, $red=false);

				$hidden = '<input type="hidden" name="Block" value="'.$tar->BlockHeader.'">'.
				'<input type="hidden" name="ReadBlockCurrent" value="'.$tar->ReadBlockCurrent.'">'.
				'<input type="hidden" name="EncryptKey" value="'.htmlspecialcharsbx($tar->EncryptKey).'">'.
				'<input type="hidden" name="DataSize" value="'.$DataSize.'">'.
				'<input type="hidden" name="arc_name" value="'.$arc_name.'">';
	
				if($r === false) // Error
					showMsg(getMsg("ERR_EXTRACT", LANG), $status.$hidden.'<div style="color:red">'.$strErrMsg.'</div>', $bottom.$skip);
				else
				{
					showMsg(getMsg('TITLE_PROCESS1'),$status.$hidden,$bottom);
					?><script>reloadPage(2, '<?= LANG?>', 1);</script><?
				}
			}
			$tar->close();
		}
		elseif ($tar->LastErrCode == 'ENC_KEY')
		{
			$text = ($tar->EncryptKey ? '<div style="color:red">'.getMsg('WRONG_PASS').'</div>' : '').
			getMsg('FILE_IS_ENC').
			'<input type="password" size=30 name="EncryptKey" autocomplete="off">'.
			'<input type="hidden" name="arc_name" value="'.$arc_name.'">'.
			'<input type="hidden" name="Step" value="2">';
			$bottom .= ' <input type="button" id="start_button" value="'.getMsg("BUT_TEXT1", LANG).'" onClick="reloadPage(2, \''. LANG.'\')">';
			showMsg(getMsg('TITLE_PROCESS1'),$text,$bottom);
		}
		else
			showMsg(getMsg("ERR_EXTRACT", LANG), getMsg('TAR_ERR_FILE_OPEN', LANG).' '.implode('<br>',$tar->err),$bottom);
	}

	if ($bSelectDumpStep)
	{
		if (file_exists($dbconn) && $strFile = file_get_contents($dbconn))
		{
			$bUTF_conf = preg_match('#^[ \t]*define\(.BX_UTF.+true\)#mi', $strFile);

			if ($bUTF_conf && !$bUTF_serv)
				$strErrMsg = getMsg('UTF8_ERROR1').'<br><br>'.$strErrMsg;
			elseif (!$bUTF_conf && $bUTF_serv)
				$strErrMsg = getMsg('UTF8_ERROR2').'<br><br>'.$strErrMsg;
		}

		if ($strErrMsg)
		{
				$ar = array(
					'TITLE' => getMsg("TITLE2", LANG),
					'TEXT' => '<div style="color:red">'.$strErrMsg.'</div>',
					'BOTTOM' =>
					'<input type="hidden" name="source" value="dump">'.
					'<input type="button" value="'.getMsg('BUT_TEXT_BACK').'"  onClick="reloadPage(1, \''. LANG.'\')"> '.
					'<input type="button" value="'.getMsg("DUMP_RETRY", LANG).'" onClick="reloadPage(2, \''. LANG.'\')"> '
				);
				html($ar);
		}
		else
		{
			$arDName = getDumpList();
			$strDName = '';
			foreach($arDName as $db)
				$strDName .= '<option value="'.htmlspecialcharsbx($db).'">'.htmlspecialcharsbx($db).'</option>';

			$DBHost = strlen($r = $_REQUEST["DBHost"]) ? $r : 'localhost'.(@file_exists($_SERVER['DOCUMENT_ROOT'].'/../BitrixEnv.exe') ? ':31006' : '');
			$DBLogin = strlen($r = $_REQUEST["DBLogin"]) ? $r : 'root';
			$DBPassword = $_REQUEST["DBPassword"];
			$DBName = strlen($r = $_REQUEST["DBName"]) ? $r : 'bitrix_'.(rand(11,99));
			$create_db = !$_REQUEST['DBLogin'] || $_REQUEST["create_db"] == "Y";

			if(count($arDName))
			{
				$ar = array(
					'TITLE' => getMsg("TITLE2", LANG),
					'TEXT' =>
						'<input type="hidden" name="arc_name" value="'.$arc_name.'">'.
						(count($arDName)>1 ? getMsg("DB_SELECT").' <select name="dump_name">'.$strDName.'</select>' : '<input type=hidden name=dump_name value="'.htmlspecialcharsbx($arDName[0]).'">').
						'<div style="border:1px solid #aeb8d7;padding:5px;margin-top:4px;margin-bottom:4px;">
						<div style="text-align:center;color:#aeb8d7;margin:4px"><b>'.getMsg("DB_SETTINGS", LANG).'</b></div>
						<table width=100% cellspacing=0 cellpadding=2 border=0>
						<tr><td align=right>'. getMsg("BASE_HOST", LANG).':</td><td><input autocomplete=off name="DBHost" value="'.htmlspecialcharsbx($DBHost).'"></td></tr>
						<tr><td align=right>'. getMsg("USER_NAME", LANG).':</td><td><input autocomplete=off name="DBLogin" value="'.htmlspecialcharsbx($DBLogin).'"></td></tr>
						<tr><td align=right>'. getMsg("USER_PASS", LANG).':</td><td><input type="password" autocomplete=off name="DBPassword" value="'.htmlspecialcharsbx($DBPassword).'"></td></tr>
						<tr><td align=right>'. getMsg("BASE_NAME", LANG).':</td><td><input autocomplete=off name="DBName" value="'.htmlspecialcharsbx($DBName).'"></td></tr>
						<tr><td align=right>'. getMsg("BASE_CREATE_DB", LANG).'</td><td><input type="checkbox" name="create_db" value="Y" '.($create_db ? 'checked' : '').'></td></tr>
						</table>
						</div>'.
						(
						file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/backup/clouds') ?
						'<div>'.getMsg("BASE_CLOUDS", LANG).'
							<select name="LocalCloud">
								<option value="Y">'.getMsg("BASE_CLOUDS_Y", LANG).'</option>
								<option value="">'.getMsg("BASE_CLOUDS_N", LANG).'</option>
							</select>
						</div>'
						:
						''
						)
					,
					'BOTTOM' =>
					'<input type="button" value="'.getMsg('BUT_TEXT_BACK').'" onClick="document.location=\'/restore.php?Step=1&lang='.LANG.'\'"> '.
					'<input type="button" value="'.getMsg("DB_SKIP", LANG).'" onClick="strAdditionalParams=\'&db_settings=skip\';reloadPage(3, \''. LANG.'\')"> '.
					'<input type="button" value="'.getMsg("BASE_RESTORE", LANG).'" onClick="reloadPage(3, \''. LANG.'\')">'
				);
				html($ar);
			}
			else
			{
				$strWarning = '';
				if (file_exists($_SERVER['DOCUMENT_ROOT'].'/.htaccess.restore'))
					$strWarning = '<div style="color:red">'.CreateDefaultHtaccessAndWarn().'</div>';

				$text =
				$strWarning.
				getMsg("EXTRACT_FINISH_MSG", LANG) . '
				<input type="hidden" name="arc_name" value="'.$arc_name.'">
				<input type="hidden" name="dump_name" value="'. htmlspecialcharsbx($_REQUEST["dump_name"]).'">';
				$bottom = '<input type="button" value="'.getMsg('BUT_TEXT_BACK').'" onClick="document.location=\'/restore.php?Step=1&lang='.LANG.'\'"> '.
				'<input type=button value="'.getMsg('DELETE_FILES').'" onClick="reloadPage(4)">';

				showMsg(getMsg("EXTRACT_FINISH_TITLE", LANG), $text, $bottom);
			}
		}
	}
}
elseif($Step == 3)
{
	$d_pos = (double) $_REQUEST["d_pos"];
	if ($d_pos < 0)
		$d_pos = 0;

	if (!isset($_REQUEST['d_pos'])) // start
	{
		if(!file_exists($dbconn))
		{
			if (!is_dir($dir = dirname($dbconn)))
				mkdir($dir, 0777, true);
			file_put_contents($dbconn, '<?'."\n".
				'define("DBPersistent", false);'."\n".
				'$DBType = "mysql";'."\n".
				'$DBHost = "";'."\n".
				'$DBLogin = "";'."\n".
				'$DBPassword = "";'."\n".
				'$DBName = "";'."\n".
				"\n".
				'$DBDebug = false;'."\n".
				'$DBDebugToFile = false;'."\n".
				'?>');
		}

		$arFile = file($dbconn);
		foreach($arFile as $line)
		{
			$line = str_replace("\r\n", "\n", $line);
			if (preg_match('#^[ \t]*\$(DBHost|DBLogin|DBPassword|DBName)#',$line,$regs))
			{
				$key = $regs[1];
				$line = '$'.$key.' = "'.str_replace('$','\$',addslashes($_REQUEST[$key])).'";'."\n";
			}
			$strFile .= $line;
		}

		if (defined('VMBITRIX') && !preg_match('#^[ \t]*define..BX_CRONTAB_SUPPORT#mi', $strFile))
			$strFile = '<'.'?define("BX_CRONTAB_SUPPORT", true);?'.'>'.$strFile;

		file_put_contents($dbconn, $strFile);

		SetCurrentProgress(0);
		$r = true;
	}
	else
		$r = $oDB->restore();

	$bottom = '<input type="button" value="'.getMsg('BUT_TEXT_BACK').'" onClick="document.location=\'/restore.php?Step=2&source=dump&lang='.LANG.'\'"> ';
	if($r && !$oDB->is_end())
	{
		$d_pos = $oDB->getPos();
		$oDB->close();
		$arc_name = $_REQUEST["arc_name"];
		SetCurrentProgress($d_pos,filesize($_SERVER['DOCUMENT_ROOT'].'/bitrix/backup/'.$_REQUEST['dump_name']));
		$text =
		$status . '
		<input type="hidden" name="arc_name" value="'.htmlspecialcharsbx($arc_name).'">
		<input type="hidden" name="dump_name" value="'. htmlspecialcharsbx($_REQUEST["dump_name"]).'">
		<input type="hidden" name="d_pos" value="'.$d_pos.'">
		<input type="hidden" name="DBLogin" value="'.htmlspecialcharsbx($_REQUEST["DBLogin"]).'">
		<input type="hidden" name="DBPassword" value="'. (strlen($_REQUEST["DBPassword"]) > 0 ? htmlspecialcharsbx($_REQUEST["DBPassword"]) : "").'">
		<input type="hidden" name="DBName" value="'. htmlspecialcharsbx($_REQUEST["DBName"]).'">
		<input type="hidden" name="DBHost" value="'. htmlspecialcharsbx($_REQUEST["DBHost"]).'">
		<input type="hidden" name="LocalCloud" value="'. ($_REQUEST["LocalCloud"] ? 'Y' : '').'">
		';
		showMsg(getMsg('TITLE_PROCESS2'),$text,$bottom);
		?><script>reloadPage(3, '<?= LANG?>', 1);</script><?
	}
	else
	{
		if($oDB->getError() != "")
			showMsg(getMsg("ERR_DUMP_RESTORE", LANG), '<div style="color:red">'.$oDB->getError().'</div>', $bottom);
		else
		{
			$strWarning = '';
			if ($rs = $oDB->Query('SELECT * FROM b_lang WHERE DOC_ROOT != "'.mysql_real_escape_string($_SERVER['DOCUMENT_ROOT']).'" AND DOC_ROOT IS NOT NULL AND DOC_ROOT != ""', $oDB->db_Conn))
			{
				if (mysql_fetch_assoc($rs))
				{
					$oDB->Query('UPDATE b_lang SET DOC_ROOT = "" ');
					$strWarning = '<div style="color:red">'.getMsg('DOC_ROOT_WARN').'</div><br>';
				}
			}

			if (file_exists($_SERVER['DOCUMENT_ROOT'].'/.htaccess.restore'))
				$strWarning .= '<div style="color:red">'.CreateDefaultHtaccessAndWarn().'</div>';

			$text = getMsg("FINISH_MSG", LANG).
			$strWarning.
			'<input type="hidden" name="arc_name" value="'.htmlspecialcharsbx($arc_name).'">
			<input type="hidden" name="dump_name" value="'. htmlspecialcharsbx($_REQUEST["dump_name"]).'">';
			$bottom = '<input type=button value="'.getMsg('DELETE_FILES').'" onClick="reloadPage(4)">';
			showMsg(getMsg("FINISH", LANG), $text, $bottom);
		}
	}
}
elseif($Step == 4)
{
	if ($_REQUEST['dump_name'])
	{
		@unlink($_SERVER["DOCUMENT_ROOT"]."/bitrix/backup/".$_REQUEST["dump_name"]);
		@unlink($_SERVER["DOCUMENT_ROOT"]."/bitrix/backup/".str_replace('.sql','_after_connect.sql',$_REQUEST["dump_name"]));
	}
	@unlink($_SERVER['DOCUMENT_ROOT'].'/bitrixsetup.php');
	$ok = unlink($_SERVER["DOCUMENT_ROOT"]."/restore.php");

	if($_REQUEST['arc_name'])
	{
		$ok = unlink($_SERVER["DOCUMENT_ROOT"]."/".$_REQUEST["arc_name"]) && $ok;
		$i = 0;
		while(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$_REQUEST['arc_name'].'.'.++$i))
			$ok = unlink($_SERVER['DOCUMENT_ROOT'].'/'.$_REQUEST['arc_name'].'.'.$i) && $ok;
	}

	foreach(array('cache','stack_cache','managed_cache') as $dir)
		DeleteDirRec($_SERVER['DOCUMENT_ROOT'].'/bitrix/'.$dir);

	if (!$ok)
		showMsg(getMsg("FINISH_ERR_DELL_TITLE", LANG), getMsg("FINISH_ERR_DELL", LANG));
	else
	{
		showMsg(getMsg("FINISH", LANG), getMsg("FINISH_MSG", LANG));
		?><script>window.setTimeout(function(){document.location="/";},2000);</script><?
	}
}

#################### END ############




class CDBRestore
{
	var $type = "";
	var $DBHost ="";
	var $DBName = "";
	var $DBLogin = "";
	var $DBPassword = "";
	var $DBdump = "";
	var $db_Conn = "";
	var $db_Error = "";
	var $f_end = false;
	var $start;
	var $d_pos;
	var $_dFile;


	function Query($sql)
	{
		return mysql_query($sql, $this->db_Conn);
	}

	function CDBRestore($DBHost, $DBName, $DBLogin, $DBPassword, $DBdump, $d_pos)
	{
		$this->DBHost = $DBHost;
		$this->DBLogin = $DBLogin;
		$this->DBPassword = $DBPassword;
		$this->DBName = $DBName;
		$this->DBdump = $_SERVER["DOCUMENT_ROOT"]."/bitrix/backup/".$DBdump;
		$this->d_pos = $d_pos;
	}

	//Соединяется с базой данных
	function Connect()
	{

		$this->type="MYSQL";
		if (!defined("DBPersistent")) define("DBPersistent",false);
		if (DBPersistent)
		{
			$this->db_Conn = @mysql_pconnect($this->DBHost, $this->DBLogin, $this->DBPassword);
		}
		else
		{
			$this->db_Conn = @mysql_connect($this->DBHost, $this->DBLogin, $this->DBPassword);
		}

		if(!($this->db_Conn))
		{
			if (DBPersistent) $s = "mysql_pconnect"; else $s = "mysql_connect";
			if(($str_err = mysql_error()) != "")
				$this->db_Error .= "<br><font color=#ff0000>Error! ".$s."('-', '-', '-')</font><br>".$str_err."<br>";
			return false;
		}

		mysql_query('SET FOREIGN_KEY_CHECKS = 0', $this->db_Conn);

		if(!@mysql_select_db($this->DBName, $this->db_Conn))
		{
			if (@$_REQUEST["create_db"]=="Y")
			{
				if(!@mysql_query("CREATE DATABASE `".mysql_real_escape_string($this->DBName)."`", $this->db_Conn))
				{
					$this->db_Error = getMsg("ERR_CREATE_DB", LANG).': '.mysql_error();
					return false;
				}
				@mysql_select_db($this->DBName, $this->db_Conn);
			}

			if(($str_err = mysql_error($this->db_Conn)) != "")
			{
				$this->db_Error = "<font color=#ff0000>Error! mysql_select_db($this->DBName)</font><br>".$str_err."<br>";
				return false;
			}
		}

		$after_file = str_replace('.sql','_after_connect.sql',$this->DBdump);
		if (file_exists($after_file))
		{
			$arSql = explode(';',file_get_contents($after_file));
			foreach($arSql as $sql)
			{
				$sql = str_replace('<DATABASE>', $this->DBName, $sql);
				mysql_query($sql, $this->db_Conn);
			}
		}

		return true;
	}

	function readSql()
	{
		$cache ="";

		while(!feof($this->_dFile) && (substr($cache, (strlen($cache)-2), 1) != ";"))
			$cache .= fgets($this->_dFile);

		if(!feof($this->_dFile))
			return $cache;
		else
		{
			$this->f_end = true;
			return false;
		}
	}

	function restore()
	{
		if (!$this->_dFile = fopen($this->DBdump, 'r'))
		{
			$this->db_Error = "Can't open file: ".$this->DBdump;
			return false;
		}

		if($this->d_pos > 0)
			fseek($this->_dFile, $this->d_pos);

		$sql = "";

		while(($sql = $this->readSql()) && haveTime())
		{
			if (defined('VMBITRIX')) // избавимся от MyISAM
			{
				if (preg_match('#^CREATE TABLE#i',$sql))
				{
					$sql = preg_replace('#ENGINE=MyISAM#i','',$sql);
					$sql = preg_replace('#TYPE=MyISAM#i','',$sql);
				}
			}

			$result = @mysql_query($sql, $this->db_Conn);

			if(!$result && mysql_errno() != 1062)
			{
				$this->db_Error .= mysql_error().'<br><br>'.htmlspecialcharsbx($sql);
				return false;
			}
			$sql = "";
		}
		mysql_query('SET FOREIGN_KEY_CHECKS = 1', $this->db_Conn);

		if($sql != "")
		{
			$result = @mysql_query($sql, $this->db_Conn);

			if(!$result)
			{
				$this->db_Error .= mysql_error().'<br><br>'.htmlspecialcharsbx($sql);
				return false;
			}
			$sql = "";
		}

		if ($this->LocalCloud && $this->f_end)
		{
			$i = '';
			while(file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/'.($name = 'clouds'.$i)))
				$i++;
			if (!file_exists($f = $_SERVER['DOCUMENT_ROOT'].'/upload'))
				mkdir($f);
			if (rename($_SERVER['DOCUMENT_ROOT'].'/bitrix/backup/clouds', $_SERVER['DOCUMENT_ROOT'].'/upload/'.$name))
			{
				$arFiles = scandir($_SERVER['DOCUMENT_ROOT'].'/upload/'.$name);
				foreach($arFiles as $file)
				{
					if ($id = intval($file))
						mysql_query('UPDATE b_file SET SUBDIR = CONCAT("'.$name.'/'.$id.'/", SUBDIR), HANDLER_ID=NULL WHERE HANDLER_ID ='.$id);
				}
			}
		}
		return true;
	}

	function getError()
	{
		return $this->db_Error;
	}

	function getPos()
	{
		if (is_resource($this->_dFile))
		{
			return @ftell($this->_dFile);
		}
	}

	function close()
	{
		unset($this->_dFile);
		return true;
	}

	function is_end()
	{
		return $this->f_end;
	}
}

function getDumpList()
{
	$arDump = array();
	if (is_dir($back_dir = $_SERVER["DOCUMENT_ROOT"]."/bitrix/backup"))
	{
		$handle = opendir($back_dir);
		while (false !== ($file = readdir($handle)))
		{
			if($file == "." || $file == "..")
				continue;

			if(is_dir($back_dir.'/'.$file))
				continue;

			if (strpos($file,'_after_connect.sql'))
				continue;

			if(substr($file, strlen($file) - 3, 3) == "sql")
				$arDump[] = $file;
		}
	}

	return $arDump;
}

function getMsg($str_index, $str_lang='')
{
	global $mArr_ru, $mArr_en;
	if(LANG == "ru")
		return $mArr_ru[$str_index];
	else
		return $mArr_en[$str_index];
}

function getArcList()
{
	$arc = "";
	global $strErrMsg;

	$handle = @opendir($_SERVER["DOCUMENT_ROOT"]);
	if (!$handle)
		return false;

	while (false !== ($file = @readdir($handle)))
	{
		if($file == "." || $file == "..")
			continue;

		if(is_dir($_SERVER["DOCUMENT_ROOT"]."/".$file))
			continue;

		if(preg_match('#\.(tar|enc)(\.gz)?$#',$file))
			$arc .= "<option value=\"$file\"> ".$file;

		if(substr($file, strlen($file) - 7, 7) == "tar.tar")
			$strErrMsg = getMsg('ERR_TAR_TAR');
	}

	return $arc;
}

function showMsg($title, $msg, $bottom='')
{
	$ar = array(
		'TITLE' => $title,
		'TEXT' => $msg,
		'BOTTOM' => $bottom

	);
	html($ar);
}

function html($ar)
{
?>
	<html>
	<head>
	<title><?=$ar['TITLE']?></title>
	</head>
	<body style="background:#4A507B">
	<style>
		td {font-family:Verdana;font-size:9pt}
	</style>
	<form name="restore" id="restore" action="restore.php" enctype="multipart/form-data" method="POST" onsubmit="this.action='restore.php?lang=<?=LANG?>&'+strAdditionalParams">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<script language="JavaScript">
		var strAdditionalParams = '';
		function reloadPage(val, lang, delay)
		{
			document.getElementById('restore').action='restore.php?lang=<?=LANG?>&Step=' + val + strAdditionalParams;
			if (null!=delay)
				window.setTimeout("document.getElementById('restore').submit()",1000);
			else
				document.getElementById('restore').submit();
		}
	</script>
	<table width=100% height=100%><tr><td align=center valign=middle>
	<table align="center" cellspacing=0 cellpadding=0 border=0 style="width:601px;height:387px">
		<tr>
			<td width=11><div style="background:#FFF url(<?=img('corner_top_left.gif')?>);width:11px;height:57px"></td>
			<td height=57 bgcolor="#FFFFFF" valign="middle">
				<table cellpadding=0 cellspacing=0 border=0 width=100%><tr>
					<td align=left style="font-size:14pt;color:#E11537;padding-left:25px"><?=$ar['TITLE']?></td>
					<td align=right>
						<?
						$arLang = array();
						foreach(array('en') as $l)
							$arLang[] = LANG == $l ? "<span style='color:grey'>$l</span>" : "<a href='?lang=$l' style='color:black'>$l</a>";
#						echo implode(' | ',$arLang);
						?>
					</td>
				</tr></table>

			</td>
			<td width=11><div style="background:#FFF url(<?=img('corner_top_right.gif')?>);width:11px;height:57px"></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF">&nbsp;</td>
			<td height=1 bgcolor="#FFFFFF"><hr size="1px" color="#D6D6D6"></td>
			<td bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF">&nbsp;</td>
			<td bgcolor="#FFFFFF" style="padding:10px;font-size:10pt" valign="<?=$ar['TEXT_ALIGN']?$ar['TEXT_ALIGN']:'top'?>"><?=$ar['TEXT']?></td>
			<td bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF">&nbsp;</td>
			<td bgcolor="#FFFFFF" style="padding:20x;font-size:10pt" valign="middle" align="right" height="40px"><?=$ar['BOTTOM']?></td>
			<td bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr>
			<td><div style="background:#FFF url(<?=img('corner_bottom_left.gif')?>);width:11;height:23"></td>
			<td height=23 bgcolor="#FFFFFF" background="<?=img('bottom_fill.gif')?>"></td>
			<td><div style="background:#FFF url(<?=img('corner_bottom_right.gif')?>);width:11;height:23"></td>
		</tr>
	</table>
	<div style="background:url(<?=img('logo_'.(LANG=='ru'?'':'en_').'installer.gif')?>); width:95; height:34">
	</td></tr></table>
	</form>
<?
}

function SetCurrentProgress($cur,$total=0,$red=true)
{
	global $status;
	if (!$total)
	{
		$total=100;
		$cur=0;
	}
	$val = intval($cur/$total*100);
	if ($val > 100)
		$val = 99;

	$status = '
	<div align=center style="padding:10px;font-size:18px">'.$val.'%</div>
	<table width=100% cellspacing=0 cellpadding=0 border=0 style="border:1px solid #D8D8D8">
	<tr>
		<td style="width:'.$val.'%;height:13px" bgcolor="'.($red?'#FF5647':'#54B4FF').'" background="'.img(($red?'red':'blue').'_progress.gif').'"></td>
		<td style="width:'.(100-$val).'%"></td>
	</tr>
	</table>';
}

function LoadFile($strRealUrl, $strFilename, $arHeaders = array())
{
	global $proxyaddr, $proxyport, $strUserAgent, $replycode;

	$iStartSize = 0;
	if (file_exists($strFilename.".tmp"))
		$iStartSize = filesize($strFilename.".tmp");

	$parsedurl = parse_url($strRealUrl);
	$strOriginalFile = basename($parsedurl['path']);

	do
	{
		SetCurrentStatus(str_replace("#DISTR#", $strRealUrl, LoaderGetMessage("LOADER_LOAD_QUERY_DISTR")));

		$lasturl = $strRealUrl;
		$redirection = "";

		$parsedurl = parse_url($strRealUrl);
		$useproxy = (($proxyaddr != "") && ($proxyport != ""));

		if (!$useproxy)
		{
			$host = $parsedurl["host"];
			$port = $parsedurl["port"];
			$hostname = $host;
		}
		else
		{
			$host = $proxyaddr;
			$port = $proxyport;
			$hostname = $parsedurl["host"];
		}
		SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_CONN2HOST")));

		$port = $port ? $port : "80";

		$sockethandle = @fsockopen($host, $port, $error_id, $error_msg, 10);
		if (!$sockethandle)
		{
			SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_NO_CONN2HOST"))." [".$error_id."] ".$error_msg);
			return false;
		}
		else
		{
			if (!$parsedurl["path"])
				$parsedurl["path"] = "/";

			$request = "";
			if (!$useproxy)
			{
				$request .= "GET ".$parsedurl["path"].($parsedurl["query"] ? '?'.$parsedurl["query"] : '')." HTTP/1.0\r\n";
				$request .= "Host: $hostname\r\n";
			}
			else
			{
				$request .= "GET ".$strRealUrl." HTTP/1.0\r\n";
				$request .= "Host: $hostname\r\n";
			}

			if ($strUserAgent != "")
				$request .= "User-Agent: $strUserAgent\r\n";

			foreach($arHeaders as $k => $v)
				$request .= $k.': '.$v."\r\n";

			$request .= "\r\n";

			fwrite($sockethandle, $request);

			$result = "";

			$replyheader = "";
			while (($result = fgets($sockethandle, 4096)) && $result!="\r\n")
			{
				$replyheader .= $result;
			}
			fclose($sockethandle);

			$ar_replyheader = explode("\r\n", $replyheader);

			$replyproto = "";
			$replyversion = "";
			$replycode = 0;
			$replymsg = "";
			if (preg_match("#([A-Z]{4})/([0-9.]{3}) ([0-9]{3})#", $ar_replyheader[0], $regs))
			{
				$replyproto = $regs[1];
				$replyversion = $regs[2];
				$replycode = IntVal($regs[3]);
				$replymsg = substr($ar_replyheader[0], strpos($ar_replyheader[0], $replycode) + strlen($replycode) + 1, strlen($ar_replyheader[0]) - strpos($ar_replyheader[0], $replycode) + 1);
			}

			if ($replycode!=200 && $replycode!=302)
			{
				if ($replycode==403)
					SetCurrentStatus(str_replace("#ANS#", $replycode." - ".$replymsg, LoaderGetMessage("LOADER_LOAD_SERVER_ANSWER1")));
				else
					SetCurrentStatus(str_replace("#ANS#", $replycode." - ".$replymsg, LoaderGetMessage("LOADER_LOAD_SERVER_ANSWER")));
				return false;
			}

			$strLocationUrl = "";
			$iNewRealSize = 0;
			$strAcceptRanges = "";
			foreach ($ar_replyheader as $i => $headerLine)
			{
				if (strpos($headerLine, "Location") !== false)
					$strLocationUrl = trim(substr($headerLine, strpos($headerLine, ":") + 1, strlen($headerLine) - strpos($headerLine, ":") + 1));
				elseif (strpos($headerLine, "Content-Length") !== false)
					$iNewRealSize = IntVal(Trim(substr($headerLine, strpos($headerLine, ":") + 1, strlen($headerLine) - strpos($headerLine, ":") + 1)));
				elseif (strpos($headerLine, "Accept-Ranges") !== false)
					$strAcceptRanges = Trim(substr($headerLine, strpos($headerLine, ":") + 1, strlen($headerLine) - strpos($headerLine, ":") + 1));
			}

			if (strlen($strLocationUrl)>0)
			{
				$redirection = $strLocationUrl;
				$redirected = true;
				if ((strpos($redirection, "http://")===false))
					$strRealUrl = dirname($lasturl)."/".$redirection;
				else
					$strRealUrl = $redirection;
			}

			if (strlen($strLocationUrl))
				$bRedirect = true;
			else
				break;
		}
	}
	while (true);

	if (strpos($strRealUrl, $strOriginalFile) === false)
	{
		SetCurrentStatus(str_replace("#URL#", htmlspecialcharsbx($strRealUrl), LoaderGetMessage("LOADER_LOAD_CANT_REDIRECT")));
		return false;
	}

	SetCurrentStatus(str_replace("#DISTR#", $strRealUrl, LoaderGetMessage("LOADER_LOAD_LOAD_DISTR")));

	$parsedurl = parse_url($strRealUrl);
	$useproxy = (($proxyaddr != "") && ($proxyport != ""));

	if (!$useproxy)
	{
		$host = $parsedurl["host"];
		$port = $parsedurl["port"];
		$hostname = $host;
	}
	else
	{
		$host = $proxyaddr;
		$port = $proxyport;
		$hostname = $parsedurl["host"];
	}

	$port = $port ? $port : "80";

	SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_CONN2HOST")));
	$sockethandle = @fsockopen($host, $port, $error_id, $error_msg, 10);
	if (!$sockethandle)
	{
		SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_NO_CONN2HOST"))." [".$error_id."] ".$error_msg);
		return false;
	}
	else
	{
		if (!$parsedurl["path"])
			$parsedurl["path"] = "/";

		SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_QUERY_FILE"));

		$request = "";
		if (!$useproxy)
		{
			$request .= "GET ".$parsedurl["path"].($parsedurl["query"] ? '?'.$parsedurl["query"] : '')." HTTP/1.0\r\n";
			$request .= "Host: $hostname\r\n";
		}
		else
		{
			$request .= "GET ".$strRealUrl." HTTP/1.0\r\n";
			$request .= "Host: $hostname\r\n";
		}

		if ($strUserAgent != "")
			$request .= "User-Agent: $strUserAgent\r\n";

		if ($iStartSize>0)
			$request .= "Range: bytes=".$iStartSize."-\r\n";

		foreach($arHeaders as $k => $v)
			$request .= $k.': '.$v."\r\n";

		$request .= "\r\n";

		fwrite($sockethandle, $request);

		$result = "";
		SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_WAIT"));

		$replyheader = "";
		while (($result = fgets($sockethandle, 4096)) && $result!="\r\n")
			$replyheader .= $result;

		$ar_replyheader = explode("\r\n", $replyheader);

		$replyproto = "";
		$replyversion = "";
		$replycode = 0;
		$replymsg = "";
		if (preg_match("#([A-Z]{4})/([0-9.]{3}) ([0-9]{3})#", $ar_replyheader[0], $regs))
		{
			$replyproto = $regs[1];
			$replyversion = $regs[2];
			$replycode = IntVal($regs[3]);
			$replymsg = substr($ar_replyheader[0], strpos($ar_replyheader[0], $replycode) + strlen($replycode) + 1, strlen($ar_replyheader[0]) - strpos($ar_replyheader[0], $replycode) + 1);
		}

		if ($replycode!=200 && $replycode!=302 && $replycode!=206)
		{
			SetCurrentStatus(str_replace("#ANS#", $replycode." - ".$replymsg, LoaderGetMessage("LOADER_LOAD_SERVER_ANSWER")));
			return false;
		}

		$strContentRange = "";
		$iContentLength = 0;
		$strAcceptRanges = "";
		foreach ($ar_replyheader as $i => $headerLine)
		{
			if (strpos($headerLine, "Content-Range") !== false)
				$strContentRange = trim(substr($headerLine, strpos($headerLine, ":") + 1, strlen($headerLine) - strpos($headerLine, ":") + 1));
			elseif (strpos($headerLine, "Content-Length") !== false)
				$iContentLength = doubleval(Trim(substr($headerLine, strpos($headerLine, ":") + 1, strlen($headerLine) - strpos($headerLine, ":") + 1)));
			elseif (strpos($headerLine, "Accept-Ranges") !== false)
				$strAcceptRanges = Trim(substr($headerLine, strpos($headerLine, ":") + 1, strlen($headerLine) - strpos($headerLine, ":") + 1));
		}

		$fh = fopen($strFilename.".tmp", "ab");
		if (!$fh)
		{
			SetCurrentStatus(str_replace("#FILE#", $strFilename.".tmp", LoaderGetMessage("LOADER_LOAD_CANT_OPEN_WRITE")));
			return false;
		}

		$bFinished = True;
		$downloadsize = (double) $iStartSize;
		SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_LOADING"));
		while (!feof($sockethandle))
		{
			if (!haveTime())
			{
				$bFinished = False;
				break;
			}

			$result = fread($sockethandle, 40960);
			$downloadsize += strlen($result);
			if ($result=="")
				break;

			fwrite($fh, $result);
		}
		SetCurrentProgress($downloadsize,$iNewRealSize);

		fclose($fh);
		fclose($sockethandle);

		if ($bFinished)
		{
			@unlink($strFilename);
			if (@rename($strFilename.".tmp", $strFilename))
			{
				SetCurrentStatus(str_replace("#SIZE#", $downloadsize, str_replace("#FILE#", $strFilename, LoaderGetMessage("LOADER_LOAD_FILE_SAVED"))));
				return 1;
			}
			else
			{
				SetCurrentStatus(str_replace("#FILE2#", $strFilename, str_replace("#FILE1#", $strFilename.".tmp", LoaderGetMessage("LOADER_LOAD_ERR_RENAME"))));
				return false;
			}
		}
		else
			return 2;
	}
}

function SetCurrentStatus($str)
{
	global $strLog;
	$strLog .= $str."\n";
}

function LoaderGetMessage($name)
{
	global $MESS;
	return $MESS[$name];
}

class CTar
{
	var $gzip;
	var $file;
	var $err = array();
	var $LastErrCode;
	var $res;
	var $Block = 0;
	var $BlockHeader;
	var $path;
	var $FileCount = 0;
	var $DirCount = 0;
	var $ReadBlockMax = 2000;
	var $ReadBlockCurrent = 0;
	var $header = null;
	var $ArchiveSizeLimit;
	const BX_EXTRA = 'BX0000';
	const BX_SIGNATURE = 'Bitrix Encrypted File';
	var $BufferSize;
	var $Buffer;
	var $dataSizeCache = array();

	##############
	# READ
	# {
	function openRead($file)
	{
		if (!isset($this->gzip) && (self::substr($file,-3)=='.gz' || self::substr($file,-4)=='.tgz'))
			$this->gzip = true;

		$this->BufferSize = 51200;

		if ($this->open($file, 'r'))
		{
			if ('' !== $str = $this->gzip ? gzread($this->res,512) : fread($this->res,512))
			{
				$data = unpack("a100empty/a90signature/a10version/a56tail/a256enc", $str);
				if (trim($data['signature']) != self::BX_SIGNATURE)
				{
					if (self::strlen($this->EncryptKey))
						$this->Error('Invalid encryption signature','ENC_SIGN');

					// Probably archive is not encrypted
					$this->gzip ? gzseek($this->res, 0) : fseek($this->res, 0);
					$this->EncryptKey = null;

					return $this->res;
				}

				if (($version = trim($data['version'])) != '1.0')
					return $this->Error('Unsupported archive version: '.$version, 'ENC_VER');

				$key = $this->getEncryptKey();
				$this->BlockHeader = $this->Block = 1;

				if (!$key || self::substr($str, 0, 256) != mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $data['enc'], MCRYPT_MODE_ECB, pack("a8",$key)))
					return $this->Error('Invalid encryption key', 'ENC_KEY');
			}
		}
		return $this->res;
	}

	function readBlock($bIgnoreOpenNextError = false)
	{
		if (!$this->Buffer)
		{
			$str = $this->gzip ? gzread($this->res, $this->BufferSize) : fread($this->res, $this->BufferSize);
			if ($str === '' && $this->openNext($bIgnoreOpenNextError))
				$str = $this->gzip ? gzread($this->res, $this->BufferSize) : fread($this->res, $this->BufferSize);
			if ($str !== '' && $key = $this->getEncryptKey())
				$str = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $str, MCRYPT_MODE_ECB, pack("a8",$key));
			$this->Buffer = $str;
		}

		$str = '';
		if ($this->Buffer)
		{
			$str = self::substr($this->Buffer, 0, 512);
			$this->Buffer = self::substr($this->Buffer, 512);
			$this->Block++;
		}

		return $str;
	}

	function SkipFile()
	{
		if ($this->Skip(ceil($this->header['size']/512)))
		{
			$this->header = null;
			return true;
		}
		return false;
	}

	function Skip($Block)
	{
		if ($Block == 0)
			return true;

		$this->Block += $Block;
		$toSkip = $Block * 512;

		if (self::strlen($this->Buffer) > $toSkip)
		{
			$this->Buffer = self::substr($this->Buffer, $toSkip);
			return true;
		}
		$this->Buffer = '';
		$NewPos = $this->Block * 512;

		if ($ArchiveSize = $this->getDataSize($file = self::getFirstName($this->file)))
		{
			while($NewPos > $ArchiveSize)
			{
				$file = $this->getNextName($file);
				$NewPos -= $ArchiveSize;
			}
		}

		if ($file != $this->file)
		{
			$this->close();
			if (!$this->open($file, $this->mode))
				return false;
		}

		if (0 === ($this->gzip ? gzseek($this->res, $NewPos) : fseek($this->res, $NewPos)))
			return true;
		return $this->Error('File seek error (file: '.$this->file.', position: '.$BackupPos.')');
	}

	function SkipTo($Block)
	{
		return $this->Skip($Block - $this->Block);
	}

	function readHeader($Long = false)
	{
		$str = '';
		while(trim($str) == '')
		{
			if (!($l = self::strlen($str = $this->readBlock($bIgnoreOpenNextError = true))))
				return 0; // finish
		}

		if (!$Long)
			$this->BlockHeader = $this->Block - 1;

		if ($l != 512)
			return $this->Error('Wrong block size: '.self::strlen($str).' (block '.$this->Block.')');


		$data = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1type/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155prefix", $str);
		$chk = $data['devmajor'].$data['devminor'];

		if (!is_numeric(trim($data['checksum'])) || $chk!='' && $chk!=0)
			return $this->Error('Archive is corrupted, wrong block: '.($this->Block-1));

		$header['filename'] = trim($data['prefix'].'/'.$data['filename'],'/');
		$header['mode'] = OctDec($data['mode']);
		$header['uid'] = OctDec($data['uid']);
		$header['gid'] = OctDec($data['gid']);
		$header['size'] = OctDec($data['size']);
		$header['mtime'] = OctDec($data['mtime']);
		$header['type'] = $data['type'];
//		$header['link'] = $data['link'];

		if (self::strpos($header['filename'],'./') === 0)
			$header['filename'] = self::substr($header['filename'], 2);

		if ($header['type']=='L') // Long header
		{
			$n = ceil($header['size']/512);
			for ($i = 0; $i < $n; $i++)
				$filename .= $this->readBlock();

			if (!is_array($header = $this->readHeader($Long = true)))
				return $this->Error('Wrong long header, block: '.$this->Block);
			$header['filename'] = self::substr($filename,0,self::strpos($filename,chr(0)));
		}
		
		if (self::strpos($header['filename'],'/') === 0) // trailing slash
			$header['type'] = 5; // Directory

		if ($header['type']=='5')
			$header['size'] = '';

		if ($header['filename']=='')
			return $this->Error('Filename is empty, wrong block: '.($this->Block-1));

		if (!$this->checkCRC($str, $data))
			return $this->Error('Checksum error on file: '.$header['filename']);

		$this->header = $header;

		return $header;
	}

	function checkCRC($str, $data)
	{
		$checksum = $this->checksum($str);
		$res = octdec($data['checksum']) == $checksum || $data['checksum']===0 && $checksum==256;
		return $res;
	}

	function extractFile()
	{
		if ($this->header === null)
		{
			if(($header = $this->readHeader()) === false || $header === 0 || $header === true)
			{
				if ($header === true && $this->SkipFile() === false)
					return false;
				return $header;
			}

			$this->lastPath = $f = $this->path.'/'.$header['filename'];
		
			if ($this->ReadBlockCurrent == 0)
			{
				if ($header['type']==5) // dir
				{
					if(!file_exists($f) && !self::xmkdir($f))
						return $this->ErrorAndSkip('Can\'t create folder: '.$f);
					//chmod($f, $header['mode']);
				}
				else // file
				{
					if (!self::xmkdir($dirname = dirname($f)))
						return $this->ErrorAndSkip('Can\'t create folder: '.$dirname);
					elseif (($rs = fopen($f, 'wb'))===false)
						return $this->ErrorAndSkip('Can\'t create file: '.$f);
				}
			}
			else
				return $this->Skip($this->ReadBlockCurrent);
		}
		else // файл уже частично распакован, продолжаем на том же хите
		{
			$header = $this->header;
			$this->lastPath = $f = $this->path.'/'.$header['filename'];
		}

		if ($header['type'] != 5) // пишем контент в файл 
		{
			if (!$rs)
			{
				if (($rs = fopen($f, 'ab'))===false)
					return $this->ErrorAndSkip('Can\'t open file: '.$f);
			}

			$i = 0;
			$FileBlockCount = ceil($header['size'] / 512);
			while(++$this->ReadBlockCurrent <= $FileBlockCount && ($contents = $this->readBlock()))
			{
				if ($this->ReadBlockCurrent == $FileBlockCount && ($chunk = $header['size'] % 512))
					$contents = self::substr($contents, 0, $chunk);

				fwrite($rs,$contents);

				if ($this->ReadBlockMax && ++$i >= $this->ReadBlockMax)
				{
					fclose($rs);
					return true; // Break
				}
			}
			fclose($rs);

			//chmod($f, $header['mode']);
			if (($s=filesize($f)) != $header['size'])
				return $this->Error('File size is wrong: '.$header['filename']).' (actual: '.$s.'  expected: '.$header['size'].')';
		}

		if ($this->header['type']==5)
			$this->DirCount++;
		else
			$this->FileCount++;

		$this->debug_header = $this->header;
		$this->BlockHeader = $this->Block;
		$this->ReadBlockCurrent = 0;
		$this->header = null;

		return true;
	}

	function openNext($bIgnoreOpenNextError)
	{
		if (file_exists($file = $this->getNextName()))
		{
			$this->close();
			return $this->open($file,$this->mode);
		}
		elseif (!$bIgnoreOpenNextError)
			return $this->Error("File doesn't exist: ".$file);
		return false;
	}

	function getLastNum($file)
	{
		$file = self::getFirstName($file);

		$f = fopen($file, 'rb');
		fseek($f, 12);
		if (fread($f, 2) == 'LN')
			$res = end(unpack('va',fread($f, 2)));
		else
			$res = false;
		fclose($f);
		return $res;
	}

	# }
	##############

	##############
	# WRITE 
	# {
	function openWrite($file)
	{
		if (!isset($this->gzip) && (self::substr($file,-3)=='.gz' || self::substr($file,-4)=='.tgz'))
			$this->gzip = true;

		$this->BufferSize = 51200;

		if (intval($this->ArchiveSizeLimit) <= 0)
			$this->ArchiveSizeLimit = 1024 * 1024 * 1024; // 1Gb


		$this->Block = 0;
		while(file_exists($file1 = $this->getNextName($file))) // находим последний архив
		{
			$this->Block += ceil($this->ArchiveSizeLimit / 512);
			$file = $file1;
		}

		$size = 0;
		if (file_exists($file) && !$size = $this->getDataSize($file))
			return $this->Error('Can\'t get data size: '.$file);

		$this->Block += $size / 512;
		if ($size >= $this->ArchiveSizeLimit) // если последний архив полон
		{
			$file = $file1;
			$size = 0;
		}
		$this->ArchiveSizeCurrent = $size;

		$res = $this->open($file, 'a');
		if ($res && $this->Block == 0 && ($key = $this->getEncryptKey())) // запишем служебный заголовок для зашифрованного архива
		{
			$enc = pack("a100a90a10a56",md5(uniqid(rand(), true)), self::BX_SIGNATURE, "1.0", "");
			$enc .= mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $enc, MCRYPT_MODE_ECB, pack("a8",$key));
			if (!($this->gzip ? gzwrite($this->res, $enc) : fwrite($this->res, $enc)))
				return $this->Error('Error writing to file');
			$this->Block = 1;
			$this->ArchiveSizeCurrent = 512;
		}
		return $res;
	}

	// создадим пустой gzip с экстра полем
	function createEmptyGzipExtra($file)
	{
		if (file_exists($file))
			return $this->Error('File already exists: '.$file);

		if (!($f = gzopen($file,'wb')))
			return $this->Error('Can\'t open file: '.$file);
		gzwrite($f,'');
		gzclose($f);

		$data = file_get_contents($file);

		if (!($f = fopen($file, 'w')))
			return $this->Error('Can\'t open file for writing: '.$file);

		$ar = unpack('A3bin0/A1FLG/A6bin1',self::substr($data,0,10));
		if ($ar['FLG'] != 0)
			return $this->Error('Error writing extra field: already exists');

		$EXTRA = "\x00\x00\x00\x00".self::BX_EXTRA; // 10 байт
		fwrite($f,$ar['bin0']."\x04".$ar['bin1'].chr(self::strlen($EXTRA))."\x00".$EXTRA.self::substr($data,10));
		fclose($f);
		return true;
	}

	function writeBlock($str)
	{
		$l = self::strlen($str);
		if ($l!=512)
			return $this->Error('Wrong block size: '.$l);

		if ($this->ArchiveSizeCurrent >= $this->ArchiveSizeLimit)
		{
			$file = $this->getNextName();
			$this->close();

			if (!$this->open($file,$this->mode))
				return false;

			$this->ArchiveSizeCurrent = 0;
		}

		$this->Buffer .= $str;

		$this->Block++;
		$this->ArchiveSizeCurrent += 512;

		if (self::strlen($this->Buffer) == $this->BufferSize)
			return $this->flushBuffer();

		return true;
	}

	function flushBuffer()
	{
		if (!$str = $this->Buffer)
			return true;
		$this->Buffer = '';

		if ($key = $this->getEncryptKey())
			$str = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $str, MCRYPT_MODE_ECB, pack("a8",$key));

		return $this->gzip ? gzwrite($this->res, $str) : fwrite($this->res, $str);
	}

	function writeHeader($ar)
	{
		$header0 = pack("a100a8a8a8a12a12", $ar['filename'], decoct($ar['mode']), decoct($ar['uid']), decoct($ar['gid']), decoct($ar['size']), decoct($ar['mtime']));
		$header1 = pack("a1a100a6a2a32a32a8a8a155", $ar['type'],'','','','','','', '', $ar['prefix']);

		$checksum = pack("a8",decoct($this->checksum($header0.'        '.$header1)));
		$header = pack("a512", $header0.$checksum.$header1);
		return $this->writeBlock($header) || $this->Error('Error writing header');
	}

	function addFile($f)
	{
		$f = str_replace('\\', '/', $f);
		$path = self::substr($f,self::strlen($this->path) + 1);
		if ($path == '')
			return true;
		if (self::strlen($path)>512)
			return $this->Error('Path is too long: '.$path);
		if (is_link($f) && !file_exists($f)) // broken link
			return true;

		if (!$ar = $this->getFileInfo($f))
			return false;

		if ($this->ReadBlockCurrent == 0) // read from start
		{
			if (self::strlen($path) > 100) // Long header
			{
				$ar0 = $ar;
				$ar0['type'] = 'L';
				$ar0['filename'] = '././@LongLink';
				$ar0['size'] = self::strlen($path);
				if (!$this->writeHeader($ar0))
					return $this->Error('Can\'t write header to file: '.$this->file);

				if (!$this->writeBlock(pack("a512",$path)))
					return $this->Error('Can\'t write to file: '.$this->file);

				$ar['filename'] = self::substr($path,0,100);
			}

			if (!$this->writeHeader($ar))
				return $this->Error('Can\'t write header to file: '.$this->file);
		}

		if ($ar['type'] == 0 && $ar['size'] > 0) // File
		{
			if (!($rs = fopen($f, 'rb')))
				return $this->Error('Error reading file: '.$f);

			if ($this->ReadBlockCurrent)
				fseek($rs, $this->ReadBlockCurrent * 512);

			$i = 0;
			while(!feof($rs) && ('' !== $str = fread($rs,512)))
			{
				$this->ReadBlockCurrent++;
				if (feof($rs))
					$str = pack("a512", $str);

				if (!$this->writeBlock($str))
				{
					fclose($rs);
					return $this->Error('Error processing file: '.$f);
				}

				if ($this->ReadBlockMax && ++$i >= $this->ReadBlockMax)
				{
					fclose($rs);
					return true;
				}
			}
			fclose($rs);
			$this->ReadBlockCurrent = 0;
		}
		return true;
	}

	# }
	##############

	##############
	# BASE 
	# {
	function open($file, $mode='r')
	{
		$this->file = $file;
		$this->mode = $mode;

		if (is_dir($file))
			return $this->Error('File is directory: '.$file);

		if ($this->EncryptKey && !function_exists('mcrypt_encrypt'))
			return $this->Error('Function &quot;mcrypt_encrypt&quot; is not available');
		
		if ($mode == 'r' && !file_exists($file))
			return $this->Error('File does not exist: '.$file);

		if ($this->gzip) 
		{
			if(!function_exists('gzopen'))
				return $this->Error('Function &quot;gzopen&quot; is not available');
			else
			{
				if ($mode == 'a' && !file_exists($file) && !$this->createEmptyGzipExtra($file))
					return false;
				$this->res = gzopen($file,$mode."b");
			}
		}
		else
			$this->res = fopen($file,$mode."b");

		return $this->res;
	}

	function close()
	{
		if ($this->mode == 'a')
			$this->flushBuffer();

		if ($this->gzip)
		{
			gzclose($this->res);

			if ($this->mode == 'a')
			{
				// добавим фактический размер всех несжатых данных в extra поле
				$f = fopen($this->file, 'rb+');
				fseek($f, 18);
				fwrite($f, pack("V", $this->ArchiveSizeCurrent));
				fclose($f);

				$this->dataSizeCache[$this->file] = $this->ArchiveSizeCurrent;

				// сохраним номер последней части в первый архив для многотомных архивов
				if (preg_match('#^(.+)\.([0-9]+)$#', $this->file, $regs))
				{
					$f = fopen($regs[1], 'rb+');
					fseek($f, 12);
					fwrite($f, 'LN'.pack("v",$regs[2]));
					fclose($f);
				}
			}
		}
		else
			fclose($this->res);
	}

	function getNextName($file = '')
	{
		if (!$file)
			$file = $this->file;

		static $CACHE;
		$c = &$CACHE[$file];

		if (!$c)
		{
			$l = strrpos($file, '.');
			$num = self::substr($file,$l+1);
			if (is_numeric($num))
				$file = self::substr($file,0,$l+1).++$num;
			else
				$file .= '.1';
			$c = $file;
		}
		return $c;
	}

	function checksum($s)
	{
		$chars = count_chars(self::substr($s,0,148).'        '.self::substr($s,156,356));
		$sum = 0;
		foreach($chars as $ch => $cnt)
			$sum += $ch*$cnt;
		return $sum;
	}

	function substr($s, $a, $b = null)
	{
		if (function_exists('mb_orig_substr'))
			return $b === null ? mb_orig_substr($s, $a) : mb_orig_substr($s, $a, $b);
		return $b === null ? substr($s, $a) : substr($s, $a, $b);
	}

	function strlen($s)
	{
		if (function_exists('mb_orig_strlen'))
			return mb_orig_strlen($s);
		return strlen($s);
	}

	function strpos($s, $a)
	{
		if (function_exists('mb_orig_strpos'))
			return mb_orig_strpos($s, $a);
		return strpos($s, $a);
	}

	function getDataSize($file)
	{
		$size = &$this->dataSizeCache[$file];
		if (!$size)
		{
			if (!file_exists($file))
				$size = false;
			else
			{
				if (preg_match('#\.gz(\.[0-9]+)?$#',$file))
				{
					$f = fopen($file, "rb");
					fseek($f, 16);
					if (fread($f, 2) == 'BX')
						$size = end(unpack("V", fread($f, 4)));
					else
					{
//						$this->Error('Wrong GZIP Extra Field');
						$size = false;
					}
					fclose($f);
				}
				else
					$size = filesize($file);
			}
		}

		return $size;
	}

	function Error($str = '', $code = '')
	{
		if ($code)
			$this->LastErrCode = $code;
		$this->err[] = $str;
		return false;
	}

	function ErrorAndSkip($str = '', $code = '')
	{
		$this->Error($str, $code);
		$this->SkipFile();
		if ($this->readHeader() === 0)
			$this->BlockHeader = $this->Block;
		return false;
	}

	function xmkdir($dir)
	{
		if (!file_exists($dir))
		{
			$upper_dir = dirname($dir);
			if (!file_exists($upper_dir) && !self::xmkdir($upper_dir))
				return $this->Error('Can\'t create folder: '.$upper_dir);

			return mkdir($dir);
		}

		return is_dir($dir);
	}

	function getEncryptKey()
	{
		if (!$this->EncryptKey)
			return false;
		static $key;
		if (!$key)
			$key = md5($this->EncryptKey);
		return $key;
	}

	function getFileInfo($f)
	{
		$f = str_replace('\\', '/', $f);
		$path = self::substr($f,self::strlen($this->path) + 1);

		$ar = array();

		if (is_dir($f))
		{
			$ar['type'] = 5;
			$path .= '/';
		}
		else
			$ar['type'] = 0;

		if (!$info = stat($f))
			return $this->Error('Can\'t get file info: '.$f);

		if ($info['size'] < 0)
			return $this->Error('File is too large: '.$f);

		$ar['mode'] = 0777 & $info['mode'];
		$ar['uid'] = $info['uid'];
		$ar['gid'] = $info['gid'];
		$ar['size'] = $ar['type']==5 ? 0 : $info['size'];
		$ar['mtime'] = $info['mtime'];
		$ar['filename'] = $path;

		return $ar;
	}

	function getCheckword($key)
	{
		return md5('BITRIXCLOUDSERVICE'.$key);
	}

	function getFirstName($file)
	{
		return preg_replace('#\.[0-9]+$#','',$file);
	}

	# }
	##############
}

class CTarRestore extends CTar
{
	function readHeader($Long = false)
	{
		$header = parent::readHeader($Long);
		if (is_array($header))
		{
			$dr = str_replace(array('/','\\'),'',$_SERVER['DOCUMENT_ROOT']);
			$f = str_replace(array('/','\\'),'',$this->path.'/'.$header['filename']);

			if ($f == $dr.'restore.php')
				return true;
			elseif ($f == $dr.'.htaccess')
				$header['filename'] .= '.restore';
			elseif ($f == $dr.'bitrix.config.php')
				return file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/.config.php') ? true : $this->Error('NOT_SAAS_ENV');
			elseif ($this->Block == 1 && file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/.config.php'))
				return $this->Error('NOT_SAAS_DISTR');
		}
		return $header;
	}
}

function haveTime()
{
	return microtime(true) - START_EXEC_TIME < STEP_TIME;
}

function img($name)
{
	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/images/'.$name))
		return '/images/'.$name;
	return 'http://www.1c-bitrix.ru/images/bitrix_setup/'.$name;
}

function bx_accelerator_reset()
{
	if(function_exists("accelerator_reset"))
		accelerator_reset();
	elseif(function_exists("wincache_refresh_if_changed"))
		wincache_refresh_if_changed();
}

function DeleteDirRec($path)
{
	if (file_exists($path) && $dir = opendir($path))
	{
		while(($item = readdir($dir)) !== false)
		{
			if ($item == '.' || $item == '..')
				continue;

			if (is_file($f = $path.'/'.$item))
				unlink($f);
			else
				DeleteDirRec($f);
		}
		closedir($dir);
		rmdir($path);
	}
}

function CreateDefaultHtaccessAndWarn()
{
	if (file_put_contents($_SERVER['DOCUMENT_ROOT'].'/.htaccess',
'Options -Indexes 
ErrorDocument 404 /404.php

<IfModule mod_php5.c>
	php_flag allow_call_time_pass_reference 1
	php_flag session.use_trans_sid off

	#php_value display_errors 1

	#php_value mbstring.internal_encoding UTF-8
</IfModule>

<IfModule mod_rewrite.c>
	Options +FollowSymLinks
	RewriteEngine On
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-l
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteCond %{REQUEST_FILENAME} !/bitrix/urlrewrite.php$
	RewriteRule ^(.*)$ /bitrix/urlrewrite.php [L]
	RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization}]
</IfModule>

<IfModule mod_dir.c>
	DirectoryIndex index.php index.html
</IfModule>

<IfModule mod_expires.c>
	ExpiresActive on
	ExpiresByType image/jpeg "access plus 3 day"
	ExpiresByType image/gif "access plus 3 day"
</IfModule>'))
		return getMsg('HTACCESS_WARN');
	else
		return getMsg('HTACCESS_ERR_WARN');
}
?>
