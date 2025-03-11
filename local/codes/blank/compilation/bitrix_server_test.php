<?php
#####################################
define('LAST_MODIFIED','14.10.2015');
#####################################

ob_implicit_flush(true);

if (@$_REQUEST['debug']) 
{
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set("display_errors",1);
}
else
	error_reporting(0);

if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');

$image_file = "bitrix_test_image.gif";

$bTest = $_REQUEST['start_test'] ? true : false;

$lang=$_REQUEST['lang']?($_REQUEST['lang']=='ru'?'ru':'en'):(@preg_match('#ru#i',$_SERVER['HTTP_ACCEPT_LANGUAGE'])?'ru':'en');
if ($lang=='ru')
	header("Content-type:text/html; charset=windows-1251");

$M=array();
if ($lang=='en')
{
	$M['SELF_VERSION']='Test version';
	$M['SELF_VERSION_DESC']='Can be downloaded from <a href="http://www.bitrixsoft.com/download/scripts/bitrix_server_test.php">http://www.bitrixsoft.com/download/scripts/bitrix_server_test.php</a>';
	$M['SELF_OLD']='Outdated';
	$M['SELF_NEW']='Fresh';
	$M['TITLE']='Bitrix Site Manager Server Test';
	$M['TOP_MSG']='<font class="install">Server Test</font>&nbsp;<font class="bitrixtitle">Bitrix Site Manager</font>';
	$M['TOP_DESC']='Here you can find server configuration parameters required for correct management of the product';
	$M['TOP_SELECT_TYPE']='Select: ';
	$M['TOP_TAR1']=' Required settings';
	$M['TOP_TAR2']=' Recommended settings';
	$M['TOP_LINKS']='<br><a href="http://www.bitrixsoft.com/learning/course/index.php?COURSE_ID=22" target=_blank>Training course “Configuring Web Systems for Best Performance”</a><br><br>'; 
	$M['START_TEST']='Start testing';
	$M['CONF_GENERAL']='General';
	$M['CONF_FS']='File system'; 
	$M['SENDFILE']='send file';
	$M['CONF_EXT']='PHP extensions';
	$M['CONF_MYSQL']='MySQL configuration'; 
	$M['CONF_ADD']='Additional Information';
	$M['OPEN']='open';
	$M['COPY']='&nbsp;&copy; Bitrix Site Manager, 2001-';
	$M['SUPPORT_LINK']='<a 
	href="http://www.bitrixsoft.com">www.bitrixsoft.com</a>  |  <a 
	href="http://www.bitrixsoft.com/support/">Support</a>';
	$M['OPEN_RESULT']='Show test results';
	$M['LIMIT']='limit:';
	$M['TESTING']=' testing...';
	$M['DEL_TMP_TABLE']='Deleting test table';
	$M['TEST_MYSQL']='Test MySQL server';
	$M['SERVER_ANS']='Server response';
	$M['WRONG_ANS']='Wrong response';
	$M['NA']="Not determined";
	$M['WEB_SERVER']="Web-server version";
	$M['WEB_SERVER_DESC']=" Required: Apache 1.3.0 and higher or IIS 5.0 and higher";
	$M['SAPI']="PHP interface";
	$M['SAPI_DESC']="It's recommended to run PHP as the Apache module. It's faster than CGI and allows more flexible settings.";
	$M['PHP_VER']="PHP version";
	$M['PHP_VER_DESC']='Required version: 5.3 and higher'; 
	$M['SAFE_DESC']="Safe Mode is not supported";
	$M['TEST_SESS']="Sessions saving";
	$M['TEST_SESS_DESC']='Required for saving authorization';
	$M['TEST_SESS_UA']="Sessions saving without UserAgent";
	$M['TEST_SESS_UA_DESC']='Required for file upload plugin';
	$M['SHORT_TAG']="short_open_tag value"; 
	$M['SHORT_TAG_DESC']='short_open_tag=off is not supported'; 
	$M['MEM_LIMIT']="memory_limit value";
	$M['MEM_LIMIT_DESC']='Memory limit settings should be not less than 32M (64M for "Professional" and higher editions). It is recommended to disable unused PHP modules in php.ini file to increase the memory size available to applications.';
	$M['MEM_FACT']="Actual memory limit";
	$M['MEM_FACT_DESC']='Sometimes, actual memory limit differs from PHP settings';
	$M['SENDMAIL']="Email Sending";
	$M['SENDMAIL_DESC']='Attempt to call the mail() function'; 
	$M['MCRYPT_TEST']='Mcrypt module';
	$M['HASH_TEST']='Hash module';
	$M['MCRYPT_TEST_DESC']='Required for secure cloud backup';
	$M['SOCK_TEST']='Functions to work with sockets';
	$M['SOCK_TEST_DESC']='Required for work of SiteUpdate system';
	$M['SYSUPDATE']="SiteUpdate system";
	$M['SYSUPDATE_DESC']='Attempt to connect to the www.bitrixsoft.com 
	on port 80';
	$M['NO_CONNECT']='No connection';
	$M['HTTP_AUTH']="HTTP authorization";
	$M['1C_1']='Necessary for the integration with MS Outlook. Connecting to<b>'; 
	$M['1C_2']='</b> on <b>';
	$M['1C_3']='</b> port';
	$M['SET_TM']="Setting of set_time_limit";
	$M['SET_TM_DESC']='For correct work of the SiteUpdate system and system agents it is recommended to allow managing of the max_execution_time parameter value through the set_time_limit function in product scripts.'; 
	$M['TIME_TEST']="Execution time test";
	$M['TIME_TEST_CPU']="Execution time test with CPU load";
	$M['TIME_TEST_CPU_DESC']="";
	$M['TIME_TEST_DESC']='Attempt to execute the script for 60 seconds';
	$M['PHP_ACC']='PHP accelerator';
	$M['PHP_ACC_DESC']='PHP Accelerator is recommended (APC, XCache or any other except deprecated EAccelerator), it allows to greatly reduce the CPU load and PHP scripts execution time. It\'s desirable that the accelerator memory should be sufficient for commonly-used PHP pages.
	<br>If there is no PHP accelerator, analysis of <a 
	href="?phpinfo=Y">phpinfo()</a> is required';
	$M['CONFLICT']='conflict';
	$M['NOT_FOUND']='not found';
	$M['D_SPACE']="Disk space";
	$M['D_SPACE_DESC']='It is recommended to have not less than 50M for the Start Edition and 150M for the Enterprise Edition'; 
	$M['F_PERM']='Permissions for the current folder';
	$M['F_CREATE']='Folder creation';
	$M['F_CREATE_DESC']='Attempt to create a test folder';
	$M['F_NEW_PERM']='Permissions for the created folder';
	$M['F_DELETE']='Folder deletion';
	$M['FL_CREATE']='File creation';
	$M['FL_CREATE_D']='Attempt to create a test file';
	$M['FL_PERM']='Permissions for the created file';
	$M['FL_DEL']="File deletion";
	$M['FL_EXEC']="File execution (for the created file)";
	$M['FL_EXEC_D']='Sometimes, there are problems with executing files created with PHP';
	$M['NOT_TESTED']='not tested';
	$M['HTACCESS']='Processing .htaccess files';
	$M['HTACCESS_D']='Attempting to configure 404-error handling for a newly created folder'; 
	$M['FILE_UPL']='file_uploads value';
	$M['FILE_UPL_TEST']='File upload';
	$M['FILE_UPL_TEST_D']='Test upload of GIF image';
	$M['IMG']='Image';
	$M['IMG_D']='Image will be displayed after successful upload.';
	$M['EREGS']='PHP regular expressions';
	$M['PREG']='Perl regular expressions';
	$M['ZLIB_D']='Required for correct Compression module work and fast updates loading';
	$M['GDLIB']='Displaying graphs in the statistics and working with images';
	$M['GDLIB_D']='Required for CAPTCHA functionality'; 
	$M['SSL']='SSL support';
	$M['SSL_D']='Required for correct eStore module work with external payment systems plugins'; 
	$M['MBSTR']='mbstring support';
	$M['MBSTR_D']='Required for correct product work with UTF-8'; 
	$M['MYSQL']='MySQL functions';
	$M['MYSQL_D']='MySQL functions are mandatory';
	$M['MYSQL_CONNECT']='Connection to MySQL server';
	$M['MYSQL_VER']='MySQL server version';
	$M['MYSQL_REQ']='MySQL 5.0 and higher (No alpha or beta releases are allowed).'; 
	$M['MYSQL_REQ_D']='';
	$M['MYSQL_SELECT_DB']='Database selection';
	$M['INNODB']='InnoDB Support';
	$M['DB_TEST_TABLE']='Creating a test table';
	$M['INSERT']='Running Insert query';
	$M['INSERT_D']='Queries per second: if lower than 2000, it might indicate low DB performance';
	$M['DB_SELECT']='Database selection';
	$M['DB_CONNECT']='Connection to MySQL server';
	$M['MYSQL_TEST']='MySQL test';
	$M['SQL_MODE_DESC']='`STRICT*` modes are not supported';
	$M['DB_HOST']='DB Host';
	$M['DB_NAME']='DB name';
	$M['DB_USER']='User';
	$M['DB_PASS']='Password';
	$M['SHOW_ERR']='Display errors'; 
	$M['SHOW_ERR_D']='Turns on error displaying for this page and writes the file <a href="bitrix_server_test.log" target="_blank">bitrix_server_test.log</a>'; 
	$M['POST_MS']="post_max_size value";
	$M['ERROR']='Error';
	$M['YES']='Yes';
	$M['NO']='No';
	$M['FS_TIME']="Time to create 1000 files (sec)";
	$M['SEC']="sec.";
	$M['TIME']="Time";
	$M['FS_TIME_D']="";
	$M['SECURE_DBCONN']="";
	$M["LOADER_LOAD_CANT_OPEN_WRITE"] = "Cannot open file #FILE# for writing";
	$M["LOADER_NEW_VERSION"] = "Unable to load new version of this script";
}
else
{
	$M['SELF_VERSION']='Версия теста';
	$M['SELF_VERSION_DESC']='Можно скачать по ссылке: <a href="http://www.1c-bitrix.ru/download/scripts/bitrix_server_test.php">http://www.1c-bitrix.ru/download/scripts/bitrix_server_test.php</a>';
	$M['SELF_OLD']='Устарела';
	$M['SELF_NEW']='Актуальная';
	$M['TITLE']='Сервер-тест "Битрикс: Управление сайтом"';
	$M['TOP_MSG']='<font class="install">Сервер-тест </font>&nbsp;<font class="bitrixtitle">&quot;Битрикс: Управление сайтом&quot;</font>';
	$M['TOP_DESC']='Отображаются параметры конфигурации сервера, необходимые для использования всех возможностей программного продукта "Битрикс: Управление Сайтом"';
	$M['TOP_SELECT_TYPE']='Отметить требования конфигурации сервера для: ';
	$M['TOP_TAR1']='общего тарифа';
	$M['TOP_TAR2']='тарифа &quot;Битрикс&quot;';
	$M['TOP_LINKS']='
						<a href="http://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=32&CHAPTER_ID=1139">Учебный курс для хостеров</a><br>
						<a href="http://partners.1c-bitrix.ru/program/hosting.php">Информация по сертификации хостинга</a><br>
						<a href="http://partners.1c-bitrix.ru/program/hosting.php#tab-requirements-link">Требования по настройкам хостинга</a>';
	$M['START_TEST']='Начать тестирование';
	$M['CONF_GENERAL']='Общая конфигурация';
	$M['CONF_FS']='Файловая система';
	$M['SENDFILE']="Отправить файл";
	$M['CONF_EXT']='Расширения php';
	$M['CONF_MYSQL']='Конфигурация MySQL';
	$M['CONF_ADD']='Дополнительная информация';
	$M['OPEN']='открыть';
	$M['COPY']='&nbsp;&copy; &laquo;Битрикс&raquo;, 2001-';
	$M['SUPPORT_LINK']='<a href="http://www.bitrixsoft.ru">www.bitrixsoft.ru</a>  |  <a href="http://www.bitrixsoft.ru/support/">Техподдержка</a>';
	$M['OPEN_RESULT']='Открыть результат теста';
	$M['LIMIT']='лимит:';
	$M['TESTING']='тестируем...';
	$M['DEL_TMP_TABLE']="Удаление тестовой таблицы";
	$M['TEST_MYSQL']="Тестировать MySQL сервер";

	$M['SERVER_ANS']="Ответ сервера";
	$M['WRONG_ANS']='неверный ответ';
	$M['NA']="Не определено";
	$M['WEB_SERVER']="Версия веб-сервера";
	$M['WEB_SERVER_DESC']="Требуется Apache 1.3.0 и выше или IIS 5.0 и выше";
	$M['SAPI']="Интерфейс php";
	$M['SAPI_DESC']="Рекомендуется запускать PHP как модуль Apache, это быстрее чем CGI и даёт более гибкие настройки";
	$M['PHP_VER']="Версия php";
	$M['PHP_VER_DESC']='Требуется 5.3 и выше';
	$M['SAFE_DESC']="Режим safe_mode не поддерживается";
	$M['TEST_SESS']="Сохранение сессии";
	$M['TEST_SESS_DESC']='Необходимо для сохранения авторизации';
	$M['TEST_SESS_UA']="Сохранение сессий без UserAgent";
	$M['TEST_SESS_UA_DESC']='Необходимо для апплета множественной загрузки файлов и обмена с 1С';
	$M['SHORT_TAG']="Значение short_open_tag";
	$M['SHORT_TAG_DESC']='short_open_tag=off не поддерживается';
	$M['MEM_LIMIT']="Значение memory_limit";
	$M['MEM_LIMIT_DESC']='Ограничение памяти должно быть не ниже 32 Мб (64 Мб для старших редакций начиная с "Эксперта"). Неиспользуемые PHP модули в php.ini желательно отключить чтобы увеличить размер памяти, доступной для приложений.';
	$M['MEM_FACT']="Фактическое ограничение памяти";
	$M['MEM_FACT_DESC']='Иногда фактическое ограничение памяти может отличаться от установок php';
	$M['SENDMAIL']="Отправка почты";
	$M['SENDMAIL_DESC']='Попытка вызвать функцию mail()';
	$M['MCRYPT_TEST']='Модуль Mcrypt';
	$M['HASH_TEST']='Модуль Hash';
	$M['MCRYPT_TEST_DESC']='Требуется резервного копирования в облако';
	$M['SOCK_TEST']="Функции работы с сокетами";
	$M['SOCK_TEST_DESC']='Необходимы для работы системы обновлений';
	$M['SYSUPDATE']="Система обновлений";
	$M['SYSUPDATE_DESC']='Осуществляется попытка подключиться к серверу www.bitrixsoft.ru на порт 80';
	$M['NO_CONNECT']="нет подключения";
	$M['HTTP_AUTH']="HTTP авторизация";
	$M['1C_1']='Требуется для интеграцией с 1С и MS Outlook. Подключение к <b>';
	$M['1C_2']='</b> на <b>';
	$M['1C_3']='</b> порт';
	$M['SET_TM']="Установка set_time_limit";
	$M['SET_TM_DESC']='Для операций обновления продукта и работы агентов рекомендуется разрешать управление значением max_execution_time через функцию set_time_limit из продукта.';
	$M['TIME_TEST']="Тест на время";
	$M['TIME_TEST_CPU']="Тест на время с нагрузкой на процессор";
	$M['TIME_TEST_CPU_DESC']="В ряде случаев скрипты отключаются при превышении нагрузки на процессор";
	$M['TIME_TEST_DESC']='Попытка выполнять скрипт в течение 60 секунд';
	$M['PHP_ACC']="Акселератор php";
	$M['PHP_ACC_DESC']='Рекомендуется наличие акселератора PHP (APC, XCache или любого другого кроме устаревшего EAccelerator), это позволяет снизить нагрузку на CPU в несколько раз и уменьшить время выполнения PHP кода. Желательно, чтобы памяти акселератора было достаточно для размещения всех часто используемых PHP страниц. Рекомендуется установить фильтры, например (для eA): eaccelerator.filter  !*/help/* !*/admin/* !*/bitrix/*cache/* */bitrix/* */.*.php<br>Если акселератор не обнаружен, требуется анализ <a href="?phpinfo=Y">phpinfo()</a>';
	$M['CONFLICT']='конфликт';
	$M['NOT_FOUND']='не обнаружен';
	$M['D_SPACE']="Место на диске";
	$M['D_SPACE_DESC']='Не менее 50 Мб для редакции "Старт" и не менее 150 Мб для редакции "Бизнес"';
	$M['F_PERM']="Права на текущую папку";
	$M['F_CREATE']="Создание папки";
	$M['F_CREATE_DESC']='Попытка создать тестовую папку';
	$M['F_NEW_PERM']="Права на созданную папку";
	$M['F_DELETE']="Удаление папки";
	$M['FL_CREATE']="Создание файла";
	$M['FL_CREATE_D']='Попытка создать тестовый файл';
	$M['FL_PERM']="Права на созданный файл";
	$M['FL_DEL']="Удаление файла";
	$M['FL_EXEC']="Запуск созданного файла";
	$M['FL_EXEC_D']='В ряде случаев возникают проблемы при запуске файла, созданного средствами PHP';
	$M['NOT_TESTED']="не тестировалось";
	$M['HTACCESS']="Обработка .htaccess";
	$M['HTACCESS_D']='Осуществляется попытка настроить обработку 404-й ошибки во вновь созданной папке';
	$M['FILE_UPL']="Значение file_uploads";
	$M['FILE_UPL_TEST']="Загрузка файла";
	$M['FILE_UPL_TEST_D']='Тестовая загрузка картинки в формате GIF';
	$M['IMG']="Изображение";
	$M['IMG_D']='При успешной загрузке отображается картинка';
	$M['EREGS']="Регулярные выражения PHP";
	$M['PREG']="Регулярные выражения Perl";
	$M['ZLIB_D']='Требуется для работы модуля компрессии и быстрой загрузки обновлений';
	$M['GDLIB']='Отображение графиков в статистике, работа с изображениями';
	$M['GDLIB_D']='Необходима для работы CAPTCHA';
	$M['SSL']="Поддержка SSL";
	$M['SSL_D']='Необходима для работы интернет-магазина с подключением внешних платёжных систем';
	$M['MBSTR']="Поддержка mbstring";
	$M['MBSTR_D']='Необходима для работы продукта в кодировке UTF-8';
	$M['MYSQL']="Функции MySQL";
	$M['MYSQL_D']='Обязательнно наличие функций MySQL';
	$M['MYSQL_CONNECT']="Подключение к серверу MySQL";
	$M['MYSQL_VER']="Версия MySQL сервера";
	$M['MYSQL_REQ']='Минимальные требования: 5.0 и выше. Альфа и бета версии не допускаются.';
	$M['MYSQL_REQ_D']='';
	$M['MYSQL_SELECT_DB']="Выбор базы данных";
	$M['INNODB']="Поддержка InnoDB";
	$M['DB_TEST_TABLE']="Создание тестовой таблицы";
	$M['INSERT']="Выполнение запросов INSERT";
	$M['INSERT_D']='Число запросов в секунду: значение ниже 2000 может свидетельствовать о низкой производительности БД';
	$M['DB_SELECT']="Выбор базы данных";
	$M['DB_CONNECT']="Подключение к серверу MySQL";
	$M['MYSQL_TEST']="Тестирование MySQL";
	$M['SQL_MODE_DESC']='Режимы `STRICT*` не поддерживаются';
	$M['DB_HOST']="Хост БД";
	$M['DB_NAME']="Имя БД";
	$M['DB_USER']="Пользователь";
	$M['DB_PASS']="Пароль";
	$M['SHOW_ERR']="Включить отладку";
	$M['SHOW_ERR_D']='Включает отображение ошибок и пишет лог файл <a href="bitrix_server_test.log" target="_blank">bitrix_server_test.log</a>';
	$M['POST_MS']="Значение post_max_size";
	$M['ERROR']='Ошибка';
	$M['YES']="Да";
	$M['NO']="Нет";
	$M['TIME']="Время";
	$M['FS_TIME']="Время на создание 1000 файлов (сек)";
	$M['FS_TIME_D']="Нормальное время - до 2 секунд";
	$M['SEC']="сек.";
	$M['SECURE_DBCONN']="Есть файл конфигурации dbconn.php, но значения не показываются в целях безопасности";
	$M["LOADER_LOAD_CANT_OPEN_WRITE"] = "Не могу открыть файл #FILE# на запись";
	$M["LOADER_NEW_VERSION"] = "Доступна новая версия скрипта тестирования, но загрузить её не удалось";
}

if (@$_GET['auth_test']) 
{
	$remote_user = $_SERVER["REMOTE_USER"] ? $_SERVER["REMOTE_USER"] : $_SERVER["REDIRECT_REMOTE_USER"];
	$strTmp = base64_decode(substr($remote_user,6));
	if ($strTmp)
		list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $strTmp);
	
	if ($_SERVER['PHP_AUTH_USER']=='test_user' && $_SERVER['PHP_AUTH_PW']=='test_password') 
		die('SUCCESS');
	else
	{
		header('HTTP/1.x 401 Authorization required');
		header('WWW-Authenticate: Basic realm="Restricted area"');
		die('<h1>401 Authorization required</h1>');
	}
} 
elseif (@$_GET['session_test']) 
{
	session_start();
	if ($_SESSION['session_test']=='ok') 
		die('SUCCESS');
	else
		die('Fault');
} 
elseif (@$_GET['image']) 
{
	header("Content-type: image/gif");
	echo file_get_contents($image_file);
	@unlink($image_file);
	die();
} 
elseif (@$_GET['phpinfo']) 
{
	phpinfo();
	die();
} 
elseif (@$_GET['time_test']) 
{
	@set_time_limit(300);
	@ini_set('max_execution_time',300);
	$t=time();
	while(time()-$t < 60) 
	{
		if ($_GET['max_cpu'])
			date('Y-m-d H:i:s');
		else
			sleep(1);
	}
	die("SUCCESS");
} 
elseif (@$_GET['memory_test']) 
{
	$max=intval($_GET['max']);
	if (!$max) $max = 255;
	for($i=1;$i<=$max;$i++)
	       $a[]=str_repeat(chr($i),1024*1024); // 1 Mb
	die("SUCCESS");
}
elseif(@$_GET['killme']=='Y')
{
	unlink(__FILE__);
	echo file_exists(__FILE__)?'ERROR!':'OK';
	die();
}

session_start();


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- last modified <?php echo LAST_MODIFIED ?> -->
<html>
<head>
<title><?php echo GM('TITLE');?></title>
<meta http-equiv="Content-Type" content="text/html; charset=Windows-1251">
<style type="text/css">
	.text {font-family:Verdana,Arial, Helvetica, sans-serif; font-weight:normal; font-size:12px; color:#365069;}
	.error_text {font-family: Verdana,Arial, Helvetica, sans-serif; font-size:13px; color:#FF0000; font-weight:bold;}
	.warning_text {font-family: Verdana,Arial, Helvetica, sans-serif; font-size:13px; color:#990000; font-weight:bold;}
	.ok_text {font-family: Verdana,Arial,Helvetica,sans-serif; font-size:13px; color:#00FF00; font-weight:bold;}

	.tablehead, .tablehead1, .tablehead2, .tablehead3, .tablehead4, .tablehead5 {background-color:#C2DBED; padding:3px;}
	.tablehead1, .tablehead2, .tablehead3 {}
	.tablehead1 {}
	.tablehead3 {}
	.tablehead4, .tablehead5 {}
	.tablehead5 {}

	.tablebody, .tablebody1, .tablebody2, .tablebody3, .tablebody4 {background-color:#E2EFF7; padding:5px;}
	.tablebody1 {}
	.tablebody2 {}
	.tablebody3 {border-bottom:1px solid #C2DBED;}
	.tablebody4 {}

	.tablebodytext, .tableheadtext, .tablefieldtext {font-family:Verdana,Arial, Helvetica, sans-serif; font-size:12px;}
	.tableheadtext, .tablebodytext {font-family:Verdana,Arial, Helvetica, sans-serif;color:#000000}
	.tablefieldtext {font-family:Verdana,Arial, Helvetica, sans-serif;color:#365069;}

	INPUT.button {padding:2px; font-family:Tahoma; font-size:12px; cursor: pointer;}
	INPUT.typeinput {font-size:12px;}
	.typeselect {font-family:Verdana,Arial, Helvetica, sans-serif;font-size:12px;}

	h3 {font-family:Verdana,Arial, Helvetica, sans-serif; font-size:14px; font-weight: bold; color: #585858; margin-bottom: 5px;}


	.smalltext{font-family:Verdana, Arial, Helvetica, sans-serif; color:#365069; font-size:10px;}
	.version{font-family:Verdana, Arial, Helvetica, sans-serif; color:#FF9933; size:18px; font-weight:bold;}
	.bitrixtitle{font-family:Verdana, Arial, Helvetica, sans-serif; color:#4083B5; size:18px; font-weight:bold;}
	.install{font-family:Verdana, Arial, Helvetica, sans-serif; size:18px; font-weight:bold;}
	.head{font-family:Verdana, Arial, Helvetica, sans-serif; font-weight:bold; color:#365069; size:18px;}
	.headbitrix{font-family:Verdana, Arial, Helvetica, sans-serif; color:#365069; font-size:12px; font-weight:bold;}
	.title{font-family:Verdana, Arial, Helvetica, sans-serif; color:#365069; font-weight:bold; font-size:16px;}
	.menu{ background-color:#E6F1F9; font-family:Verdana, Arial, Helvetica, sans-serif; color:#B4C0D0; font-size:12px; padding-left:10px; padding-right:5px;}
	.menuact{background-color:#D8E8F4; font-family:Verdana, Arial, Helvetica, sans-serif; color:#365069; font-size:12px; padding-left:10px; padding-right:5px; font-weight:bold;}
	.text11 {font-family:Verdana,Arial, Helvetica, sans-serif; font-weight:normal; color:#365069; font-size:12px; margin-bottom: 5px;}
	
	a {color:#4182b6;}
	a:hover {color:#4182f6;}
</style>
</head>
<body link="#6C93AE" alink="#F1555A" vlink="#a4a4a4" style="margin-top:1px; margin-bottom:10px; margin-right:10px; margin-left:10px;" <?php echo $bTest ? ' onload="start_test()"' : ''?>>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<tr height="50">
		<td width="100%" align="center" ><?php echo GM('TOP_MSG');?></td>
	</tr>
</table>

<table width="100%"  border="0" cellspacing="1" cellpadding="0" bgcolor="#D5E7F3">
<tr>
	<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4FBFF">
		<tr>
			<td width="0%" bgcolor="#D5E7F3" rowspan="2"></td>
			<td width="100%" align="center" valign="top">
				<table width="100%"  border="0" cellspacing="0" cellpadding="0" style="border-top: 1px solid #B9D2E2; border-bottom: 1px solid #B9D2E2; border-left: 1px solid #B9D2E2; border-right: 1px solid #B9D2E2;">
				<tr>
					<td colspan=2 width="100%" bgcolor=#CFDFEF>
					<div align=center><font class="headbitrix">
					<?php echo GM('TOP_DESC'); ?>
	<br>
					<font class="text11">
					<?php echo GM('TOP_SELECT_TYPE');?>
				<?php if (@$_REQUEST['test']==1) echo "<b>".GM('TOP_TAR1')."</b>"; else echo "<a href=\"?test=1&lang=".$lang."\">".GM('TOP_TAR1')."</a>"; ?> /
				<?php if (@$_REQUEST['test']==2) echo "<b>".GM('TOP_TAR2')."</b>"; else echo "<a href=\"?test=2&lang=".$lang."\">".GM('TOP_TAR2')."</a>"; ?>
					</font></div>
					</font>
					</td>
				</tr>
				<tr>
					<td bgcolor=#CFDFEF>
	<div style="padding-left:20px; padding-right:20px;" align="left">
					<font class="text11">
					<?php echo GM('TOP_LINKS');?>
					</font></div>
					</td>
					<form method=GET>
					<td bgcolor=#CFDFEF align=center valign=middle width=50%>
						<input type=hidden name=debug value='<?php echo ($_REQUEST['debug']?'Y':'')?>'>
						<input type=hidden name=lang value="<?php echo $lang?>">
						<input type=hidden name=start_test value=Y>
						<input type=submit value="<?php echo GM('START_TEST')?>" <?php if($bTest) echo "disabled";?>>
					</td>
					</form>
				</tr>
				<tr>
					<td colspan=2 width="100%" height="619" valign="top" align="left" bgcolor="#DCE9F2">
						<div style="padding:20px;">
<?php // START Tests
######################################################################
?>
<div style="padding:5" align=center><font class=headbitrix><?php echo GM('CONF_GENERAL');?></font></div>
<?php
// Self version
debug(__LINE__);
$val = NULL;

// Check for updates
$strError = '';
if (!$_REQUEST['UPDATE_SUCCESS'])
{
	$this_script_name = basename(__FILE__);
	$bx_host = 'www.1c-bitrix.ru';
	$bx_url = '/download/files/scripts/'.$this_script_name;
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
					if ($str = file_get_contents('http://'.$bx_host.$bx_url))
					{
						if (file_put_contents(__FILE__, $str))
						{
							bx_accelerator_reset();
							echo '<script>document.location="?UPDATE_SUCCESS=Y";</script>';
							die();
						}
						else
							$strError = str_replace("#FILE#", $this_script_name, GM("LOADER_LOAD_CANT_OPEN_WRITE"));
					}
					else
						$strError = GM('LOADER_NEW_VERSION');
				}
				break;
			}
		}
		fclose($res);
	}
}

if ($strError)
	echo '<div style="color:red;padding:10px;border:2px solid red;text-align:center;background:#FFF">'.$strError.'</div>';

// Web server
debug(__LINE__);
$strSERVER_SOFTWARE = $_SERVER["SERVER_SOFTWARE"];
if (strlen($strSERVER_SOFTWARE)<=0)
	$strSERVER_SOFTWARE = $_SERVER["SERVER_SIGNATURE"];

$strSERVER_SOFTWARE = Trim($strSERVER_SOFTWARE);
if (@preg_match("#^([a-zA-Z-]+).*?([\d]+\.[\d]+(\.[\d]+)?)#i", $strSERVER_SOFTWARE, $arSERVER_SOFTWARE))
{
	$strWebServer = $arSERVER_SOFTWARE[1];
	$strWebServerVersion = $arSERVER_SOFTWARE[2];

	$val = $strWebServer." ".$strWebServerVersion;
} else {
	$val = GM("NA");
}

$pr=array(GM('WEB_SERVER'),GM('WEB_SERVER_DESC'),1);
show($pr,$val);

// CGI or not
debug(__LINE__);
$pr=array(GM('SAPI'),GM('SAPI_DESC'));
$sapi = strtolower(php_sapi_name());
show($pr,$sapi,$sapi=='cgi');

// PHP version
debug(__LINE__);
$pr=array(GM('PHP_VER'),GM('PHP_VER_DESC'),1);
show($pr,phpversion(),version_compare(phpversion(),'5.3.0','<'));

// Safe mode
debug(__LINE__);
$val=intval(ini_get("safe_mode"));
$pr=array("Safe mode",GM('SAFE_DESC'),1);
show($pr,$val,$val);

// short_open_tag
debug(__LINE__);
$val=intval(ini_get("short_open_tag"));
$pr=array(GM('SHORT_TAG'),GM('SHORT_TAG_DESC'),1);
show($pr,$val,!$val);

// Memory limit
debug(__LINE__);
$val=ini_get('memory_limit')?ini_get('memory_limit'):get_cfg_var("memory_limit");
$pr=array(GM('MEM_LIMIT'),GM('MEM_LIMIT_DESC'),1);
show($pr,$val);

// Test memory limit
debug(__LINE__);
$pr=array(GM('MEM_FACT'),GM('MEM_FACT_DESC'),1);
show($pr,"<div id=memory_limit><font color=gray>".GM('NOT_TESTED')."</font></div>");

// Mail()
debug(__LINE__);
$pr=array(GM('SENDMAIL'),GM('SENDMAIL_DESC'),1);
if ($bTest)
{
	$t = time();
	$val = mail("hosting_test@bitrix.ru","Bitrix server test","This is test message. Delete it.");
	$tt = time() - $t;
	show($pr,($val?GM('YES'):GM('NO')).($tt?" (".GM('TIME').": $tt ".GM('SEC').")":""),!$val||$tt>1);
}
else
	show($pr,'<font color=gray>'.GM('NOT_TESTED').'</font>');

// Mcrypt
debug(__LINE__);
$val = function_exists('mcrypt_encrypt');
$pr=array(GM('MCRYPT_TEST'),GM('MCRYPT_TEST_DESC'),1);
show($pr,$val,!$val);

// Hash
debug(__LINE__);
$val = function_exists('hash');
$pr=array(GM('HASH_TEST'),GM('MCRYPT_TEST_DESC'),1);
show($pr,$val,!$val);

// socket
debug(__LINE__);
$val = $socket = function_exists('fsockopen');
$pr=array(GM('SOCK_TEST'),GM('SOCK_TEST_DESC'),1);
show($pr,$val,!$val);

// Session data
debug(__LINE__);
$_SESSION['session_test'] = 'ok';
$pr=array(GM('TEST_SESS'),GM('TEST_SESS_DESC'),1);
show($pr,"<div id=session><font color=gray>".GM('NOT_TESTED')."</font></div>");
session_write_close();

// Session without UserAgent test: for upload applet
debug(__LINE__);
$ok = false;
$host = $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : 'localhost';
$port = $_SERVER['SERVER_PORT'] ? $_SERVER['SERVER_PORT'] : 80;

if ($bTest)
{
	$pr=array(GM('TEST_SESS_UA'),GM('TEST_SESS_UA_DESC'),1);
	if ($socket)
		$res = fsockopen(($port == 443 ? 'ssl://' : '').$host, $port, $errno, $errstr, 3);
	else
		$res = false;

	if($res) 
	{
		$strRequest = "GET ".dirname($_SERVER['PHP_SELF'])."/bitrix_server_test.php?session_test=Y HTTP/1.1\r\n";
		$strRequest.= "Host: ".$host."\r\n";
		$strRequest.= "Cookie: ".session_name()."=".session_id()."\r\n";
		$strRequest.= "\r\n";

		$strRes = getHttpResponse($res, $strRequest);
		fclose($res);

		if (trim($strRes) == "SUCCESS")
			$val = $ok = GM('YES');
		else
			$val = GM('NO');
	} 
	else
		$val=GM('NO_CONNECT');
	show($pr,$val,!$ok);
}

// Update system
debug(__LINE__);
$pr=array(GM('SYSUPDATE'),GM('SYSUPDATE_DESC'),1);
if ($bTest)
{
	$ok = 0;
	if ($socket)
		$res = fsockopen("www.bitrixsoft.com", "80", $errno, $errstr, 3);
	else
		$res = false;
	if($res) 
	{
		$strRequest = "POST /bitrix/updates/sysserver.php HTTP/1.1\r\n";
		$strRequest.= "User-Agent: BitrixSMUpdater\r\n";
		$strRequest.= "Accept: */*\r\n";
		$strRequest.= "Host: www.bitrixsoft.com\r\n";
		$strRequest.= "Accept-Language: en\r\n";
		$strRequest.= "Content-type: application/x-www-form-urlencoded\r\n";
		$strRequest.= "Content-length: 7\r\n\r\n";
		$strRequest.= "lang=en";
		$strRequest.= "\r\n";

		$strRes = getHttpResponse($res, $strRequest);
		fclose($res);

		if (strtolower(strip_tags($strRes)) == "license key is invalid")
			$val = $ok = 1;
		else
			$val = GM('WRONG_ANS')." <a href='javascript:alert(\"".addslashes($strRes)."\")' title='".GM('SERVER_ANS')."'>&gt;&gt;</a>";
	} 
	else
		$val=GM('NO_CONNECT');
	show($pr,$val,!$ok);
}
else
	show($pr,'<font color=gray>'.GM('NOT_TESTED').'</font>');

// HTTP Auth
debug(__LINE__);
$ok = false;
$pr=array(GM('HTTP_AUTH'),GM('1C_1').$host.GM('1C_2').$port.GM('1C_3'),2);
if ($bTest)
{
	if ($socket)
		$res = fsockopen(($port == 443 ? 'ssl://' : '').$host, $port, $errno, $errstr, 3);
	else
		$res = false;

	if($res) 
	{
		$url = parse_url($_SERVER['REQUEST_URI']);
		$strRequest = "GET ".$url['path']."?auth_test=Y HTTP/1.1\r\n";
		$strRequest.= "Host: ".$host."\r\n";
		$strRequest.= "Authorization: Basic dGVzdF91c2VyOnRlc3RfcGFzc3dvcmQ=\r\n";
		$strRequest.= "\r\n";

		$strRes = getHttpResponse($res, $strRequest);
		fclose($res);

		if (trim($strRes) == "SUCCESS")
		{
			$val = $ok = GM('YES');
			if ($_SERVER['REMOTE_USER'])
				$val .= ' ($_SERVER["REMOTE_USER"])';
			elseif ($_SERVER['REDIRECT_REMOTE_USER'])
				$val .= ' ($_SERVER["REDIRECT_REMOTE_USER"])';
		}
		else
			$val = GM('NO');
	} 
	else
		$val=GM('NO_CONNECT');
	show($pr,$val,!$ok);
}
else
	show($pr,'<font color=gray>'.GM('NOT_TESTED').'</font>');

// Set time limit
#debug(__LINE__);
#$pr=array(GM('SET_TM'),GM('SET_TM_DESC'),2);
#@set_time_limit(300);
#@ini_set('max_execution_time',300);
#$tl=(ini_get('max_execution_time')==300);
#show($pr,$tl,!$tl);

$pr=array(GM('TIME_TEST'),GM('TIME_TEST_DESC'),2);
show($pr,"<div id=time_test><font color=gray>".GM('NOT_TESTED')."</font></div>");

$pr=array(GM('TIME_TEST_CPU'),GM('TIME_TEST_CPU_DESC'),2);
show($pr,"<div id=time_test_cpu><font color=gray>".GM('NOT_TESTED')."</font></div>");

// Accelerator
debug(__LINE__);
$res = "";
$pr = array(GM('PHP_ACC'),GM('PHP_ACC_DESC'),2);
if ($val = function_exists("eaccelerator_info")) 
{
	$res = "EAccelerator";
	$val = false;
}
elseif($val = function_exists("accelerator_reset"))
{
	$res = 'Zend Accelerator <a href="http://dev.1c-bitrix.ru/community/blogs/howto/the-problem-of-performance-in-version-12.php" target=_blank>есть проблема</a>';
	$val = false;
}
elseif($val = function_exists("apc_fetch"))
	$res = "APC";
elseif($val = function_exists("xcache_get"))
	$res = "XCache";
elseif(($val = function_exists("opcache_reset")) && ini_get('opcache.enable'))
	$res = "OPcache";
show($pr,$res?GM('YES').' ('.$res.')':GM('NOT_FOUND'),!$val);

?>


<div style="padding:5" align=center><font class=headbitrix><?php echo GM('CONF_FS');?></font></div>
<?php


// Free space
debug(__LINE__);
$pr=array(GM('D_SPACE'),GM('D_SPACE_DESC'),1);
show($pr,intval(@disk_free_space($_SERVER["DOCUMENT_ROOT"])/1024/1024)." Mb");

// dirinfo
debug(__LINE__);
show(GM('F_PERM'),dirinfo("."));

// Folder create
debug(__LINE__);
$pr=array(GM('F_CREATE'),GM('F_CREATE_DESC'),1);
if ($bTest)
{
	$dir=create_tmp_folder();
	show($pr, $dir==false?GM('ERROR'):1, $dir==false);
}
else
	show($pr,'<font color=gray>'.GM('NOT_TESTED').'</font>');


if ($dir)
{
	// dirinfo
debug(__LINE__);
	show(GM('F_NEW_PERM'),dirinfo($dir));

	// Folder delete
debug(__LINE__);
	$val=rmdir($dir);
	show(array(GM('F_DELETE'),'',1), $val==false?GM('ERROR'):1, !$val);
}


// File create
debug(__LINE__);
$file = false;
$pr=array(GM('FL_CREATE'),GM('FL_CREATE_D'),1);
if ($bTest)
{
	$file=create_tmp_file();
	show($pr, $file==false?GM('ERROR'):1, $file==false);
}
else
	show($pr,'<font color=gray>'.GM('NOT_TESTED').'</font>');


if ($file) 
{
	// dirinfo
debug(__LINE__);
	show(GM('FL_PERM'),dirinfo($file));

	// File delete
debug(__LINE__);
	$del=unlink($file);
	show(array(GM('FL_DEL'),'',1), $val==false?GM('ERROR'):1, !$val);
}

// File exec
debug(__LINE__);
$pr=array(GM('FL_EXEC'),GM('FL_EXEC_D'),1);
$ok = false;
$host = $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : 'localhost';
$port = $_SERVER['SERVER_PORT'] ? $_SERVER['SERVER_PORT'] : 80;
$val = GM('NOT_TESTED');
if ($file && $bTest)
{
	$fn=$_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF'])."/bitrix_test_exec.php";
	$f=fopen($fn,"wb");
	$data='<?php 
	echo "SUCCESS";
	?>';
	fputs($f,$data);
	fclose($f);

	if ($socket)
		$res = fsockopen(($port == 443 ? 'ssl://' : '').$host, $port, $errno, $errstr, 3);
	else
		$res = false;

	if($res) 
	{
		$strRequest = "GET ".dirname($_SERVER['PHP_SELF'])."/bitrix_test_exec.php HTTP/1.1\r\n";
		$strRequest.= "Host: ".$host."\r\n";
		$strRequest.= "\r\n";

		$strRes = getHttpResponse($res, $strRequest);
		fclose($res);

		if (trim($strRes) == "SUCCESS")
			$val = $ok = GM('YES');
		else
			$val = GM('NO');
	} 
	else
		$val=GM('NO_CONNECT');
	show($pr,$val,!$ok);
	unlink($fn);
}
else
	show($pr,$val,!$ok);

// .htaccess
debug(__LINE__);
$ok = false;
$host = $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : 'localhost';
$port = $_SERVER['SERVER_PORT'] ? $_SERVER['SERVER_PORT'] : 80;

$pr=array(GM('HTACCESS'),GM('HTACCESS_D'),1);
if ($bTest && $file && $dir && prepare_htaccess_test()) 
{
	if ($socket)
		$res = fsockopen(($port == 443 ? 'ssl://' : '').$host, $port, $errno, $errstr, 3);
	else
		$res = false;

	if($res) 
	{
		$strRequest = "GET ".dirname($_SERVER['PHP_SELF'])."/bitrix_htaccess_test/test_file.php HTTP/1.1\r\n";
		$strRequest.= "Host: ".$host."\r\n";
		$strRequest.= "\r\n";

		$strRes = getHttpResponse($res, $strRequest);
		fclose($res);

		if (false!==strpos($strRes,"SUCCESS"))
			$val = $ok = GM('YES');
		else
			$val = GM('NO');
	} 
	else
		$val=GM('NO_CONNECT');
	show($pr,$val,!$ok);

	clear_htaccess_test();
} 
else 
	show($pr,GM('NOT_TESTED'),1);

// Filesystem benchmark
debug(__LINE__);
$pr=array(GM('FS_TIME'),GM('FS_TIME_D'));
if ($bTest && $file && $dir) 
{
	function xmktime()
	{
		list($usec, $sec) = explode(" ", microtime()); 
		return ((float)$usec + (float)$sec); 
	}

	$t = xmktime();
	$path = dirname(__FILE__).'/bx_fs_test'; 
	mkdir($path);
	$res = true;

	for($i=0;$i<1000;$i++)
	{
		if (!(($f = fopen($path.'/bx_test_file_'.$i,'wb')) && fwrite($f, '<?php #Hello, world! ?>') && fclose($f)))
		{
			$res = false;
			break;
		}
		include($path.'/bx_test_file_'.$i);
	}

	if ($res)
		for($i=0;$i<1000;$i++)
			if (!unlink($path.'/bx_test_file_'.$i))
			{
				$res = false;
				break;
			}
	rmdir($path);
	$time = round(xmktime()-$t,2);
	show($pr,$res ? $time : GM('ERROR'),$time>5);
}
else
	show($pr,GM('NOT_TESTED'),1);

// File uploads
debug(__LINE__);
$val=intval(ini_get('file_uploads'));
show(array(GM('FILE_UPL'),'',1),$val,!$val);


$tmp_name=@$_FILES['test_file']['tmp_name'];
$pr=array(GM('FILE_UPL_TEST'),GM('FILE_UPL_TEST_D'),1);

if (is_uploaded_file($tmp_name)) {
	$val=move_uploaded_file($tmp_name,$image_file);
	show($pr,$val==false?GM('ERROR'):1,$val==false);
	$pr=array(GM('IMG'),GM('IMG_D'));
	show($pr,"<img src=\"?image=Y\">");
} else {
	echo "<form method=post enctype=\"multipart/form-data\">
		<input type=hidden name=debug value='".($_REQUEST['debug']?'Y':'')."'>
		<input type=hidden name=lang value='".$lang."'>
		<input type=hidden name='test' value=\"".htmlspecialchars(@$_REQUEST['test'])."\">";
	show($pr,"<input type=file name=test_file>");
	echo"<div align=right><input type=submit value='".GM('SENDFILE')."'></div></form>";
}

?>
<div style="padding:5" align=center><font class=headbitrix><?php echo GM('CONF_EXT');?></font></div>
<?php


// Regex functions
debug(__LINE__);
$val=intval(function_exists("preg_match"));
$pr=array(GM('EREGS'),'',1);
show($pr,$val,!$val);

// Perl regex functions
debug(__LINE__);
$val=intval(function_exists("preg_match"));
$pr=array(GM('PREG'),'',1);
show($pr,$val,!$val);

// Zlib
debug(__LINE__);
$val=intval(extension_loaded('zlib') && function_exists("gzcompress"));
$pr=array("Zlib extension",GM('ZLIB_D'),1);
show($pr,$val,!$val);

// GD lib
debug(__LINE__);
$val=intval(function_exists("imagecreate"));
$pr=array("GD lib extension",GM('GDLIB'),1);
show($pr,$val,!$val);

// Free type
debug(__LINE__);
$val=intval(function_exists("imagettftext"));
$pr=array("Free Type extension",GM('GDLIB_D'),1);
show($pr,$val,!$val);

// SSL
debug(__LINE__);
$pr=array(GM('SSL'),GM('SSL_D'),2);
if ($bTest)
{
	$f=fsockopen("ssl://www.bitrixsoft.com",443, $errno, $errstr, 10); 
	$val = $f ? 1 : 0;
	show($pr,$val,!$val);
	@fclose($f);
}
else
	show($pr,'<font color=gray>'.GM('NOT_TESTED').'</font>');

// mbstring
debug(__LINE__);
$val=intval(function_exists("mb_substr"));
$pr=array(GM('MBSTR'),GM('MBSTR_D'),1);
show($pr,$val,!$val);

if ($val && $lang=='ru')
{
	$utf = false!==strpos(strtolower(ini_get('mbstring.internal_encoding')),'utf') && ini_get('mbstring.func_overload')==2;
	show(array("Включен режим UTF для mbstring"),$utf);

	/*
	$text0 = $utf ? "\xd0\xa2\xd0\xb5\xd0\xa1\xd1\x82" : 'ТеСт';
	$text1 = $utf ? "\xd1\x82\xd0\xb5\xd1\x81\xd1\x82" : 'тест';

	$res = strtolower($text0);
	$val = $res==$text1 || $res==$text0;
	show(array("Работа функции strtolower",'Тестируется функция strtolower для русских букв. Важно чтобы не было обратного преобразования: "ТеСт" -&gt; "ТЕСТ" вместо "тест"',1),$val,!$val);
	

	$l = strlen("\xd0\xa2");
	$val = $utf && $l==1 || !$utf && $l==2;
	show(array("Работа функции strlen",'',1),$val,!$val);
	*/
}


?>
<div style="padding:5" align=center><font class=headbitrix><?php echo GM('CONF_MYSQL');?></font></div>
<?php

// MySQL functions
debug(__LINE__);
$mysql=intval(function_exists("mysql_connect"));
$pr=array(GM('MYSQL'),GM('MYSQL_D'),1);
show($pr,$mysql,!$mysql);

//////////////////////
// MySQL benchmarking
debug(__LINE__);
$file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/dbconn.php';
if ($dbconn = file_exists($file))
	@include($file);

if ($mysql && ($bTest || $_REQUEST['mysql_test']))
{
	if (mysql_connect(@$_GET['host'],@$_GET['user'],@$_GET['pass']) || mysql_connect($DBHost,$DBLogin,$DBPassword))
	{
		show(array(GM('MYSQL_CONNECT'),'',1),1,0);
		$res=mysql_query("SELECT version()");
		$f=mysql_fetch_row($res);
		$pr=array(GM('MYSQL_VER'),GM('MYSQL_REQ'),1);
		show($pr,$f[0]);
		list($v1,$v2)=explode(".",$f[0]);

		$res=mysql_query("SHOW VARIABLES LIKE 'sql_mode'");
		while($f=mysql_fetch_row($res))
			show(array($f[0],GM('SQL_MODE_DESC'),1),"&nbsp;".$f[1],@preg_match("#strict#i",$f[1]));

		if ($v1 < 5)
			$warn=GM('MYSQL_REQ_D');
		else
			$warn='';
		$res=mysql_query("SHOW VARIABLES LIKE 'character\_set\_%'");
		while($f=mysql_fetch_row($res))
			show(array($f[0],$warn),$f[1]);

		if (mysql_select_db(@$_GET['dbname']) || $dbconn && mysql_select_db($DBName))
		{
			show(GM('MYSQL_SELECT_DB'),1,0);
			
			$name=create_tmp_table(true); // InnoDB
			$res=mysql_query("SHOW CREATE TABLE $name");
			$f=mysql_fetch_row($res);
			$val=preg_match("#ENGINE=InnoDB#",$f[1]);
			show(array(GM('INNODB'),'',2),$val,!$val);
			if ($name)
				mysql_query("DROP TABLE ".$name);
			
// Temporary table
debug(__LINE__);
			$name=create_tmp_table();
			if ($name)
			{
				show(array(GM('DB_TEST_TABLE'),'',1),1,0);

				
				$t1=microtime_float();
				$good=true;
// Insert 1000 rows 
debug(__LINE__);
				for($i=0;$i<1000;$i++)
				{
					if (!mysql_query("INSERT INTO ".$name." VALUES ('test1','test2','test3','test4')"))
					{
						$good=false;
						break;
					}
				}
				if ($good)
				{
					$t2=microtime_float();
					$pr=array(GM('INSERT'),GM('INSERT_D'));
					$tmp = round(1000/($t2-$t1));
					show($pr,$tmp." q/".GM('SEC'),$tmp<2000);
				}
				else
					show(GM("INSERT"),GM("ERROR"),1);
				$pr = array(GM('DEL_TMP_TABLE'),'',1);
				if (mysql_query("DROP TABLE ".$name))
					show($pr,1,0);
				else
					show($pr,0,1);
			} else
				show(GM('DB_TEST_TABLE'),GM('ERROR'),1);
		} else
			show(GM('DB_SELECT'),GM('ERROR'),1);
	} else
		show(GM('DB_CONNECT'),GM('ERROR'),1);
}
else
	show(GM('MYSQL_TEST'),'<font color=gray>'.GM('NOT_TESTED').'</font>');

echo "<form method=get>
	<input type=hidden name=lang value='".$lang."'>
	<input type=hidden name=debug value='".($_REQUEST['debug']?'Y':'')."'>
	<input type=hidden name='test' value=\"".htmlspecialchars(@$_REQUEST['test'])."\">";
@show(array(GM('DB_HOST'),$dbconn?GM('SECURE_DBCONN'):''),"<input name=host value=\"".($_GET['host']?htmlspecialchars(stripslashes($_GET['host'])):'localhost')."\">");
@show(GM('DB_NAME'),"<input name=dbname value=\"".htmlspecialchars(stripslashes($_GET['dbname']))."\">");
@show(GM('DB_USER'),"<input name=user value=\"".htmlspecialchars(stripslashes($_GET['user']))."\">");
@show(GM('DB_PASS'),"<input name=pass type=password value=\"".htmlspecialchars(stripslashes($_GET['pass']))."\">");
echo"<div align=right><input type=submit name=mysql_test value='".GM('TEST_MYSQL')."'></div></form>";




?>
<div style="padding:5" align=center><font class=headbitrix><?php echo GM('CONF_ADD');?></font></div>
<?php
// debug
$pr=array(GM('SHOW_ERR'),GM('SHOW_ERR_D'));
show($pr,"<a href=?debug=Y&lang=".$lang.">".GM('OPEN')."</a>");

// umask
$pr=array("umask",'');
show($pr,sprintf("%03o",umask()));

//post_max_size
$pr=array(GM('POST_MS'),'');
$val=ini_get("post_max_size");
show($pr,$val);

// Register globals
$val=intval(ini_get('register_globals'));
$pr=array("Register globals",'');
show($pr,$val);

// Display errors
$val=intval(ini_get('display_errors'));
show("Display errors",$val);

show("Server time",date("d.m.Y H:i"));
show("phpinfo()","<a href=?phpinfo=Y target=_blank>".GM('OPEN')."</a>");

show("Language","<a href='?lang=ru'>ru</a> / <a href='?lang=en'>en</a>");

show("Delete bitrix_server_test.php file","<a href='javascript:if(confirm(\"Delete?\"))document.location=\"?killme=Y\"'>delete</a>");


##############################################
function show($in_param,$value,$red='no') {
	if (is_array($in_param)) {
		$param=$in_param[0];
		$help=$in_param[1];
		$lvl=@$in_param[2];
	} else {
		$param=$in_param;
	}
	if ($red==1)
		$color='red';
	elseif (!$red)
		$color='green';
	else
		$color='#000000';

	if ($value=='1')
		$value=GM('YES');
	elseif ($value=='0')
		$value=GM('NO');

	if (@$lvl>0 && $lvl<=@$_REQUEST['test'])
		$bold='style="font-weight:bold"';
	else
		$bold='';

	@print("<table width=100% border=0 cellspacing=0 cellpadding=2>
		<tr> 
			<td nowrap align=right valign=top width=30% class=tablebody3>
				<font class=tablefieldtext $bold>$param:</font>
			</td>
			<td width=20% class=tablebody3 valign=top>
				<font class=tablebodytext style=\"color:$color\">
				$value
				</font>
			</td>
			<td class=tablebody3 valign=top><font class=smalltext>$help&nbsp;</font></td>
		</tr>
		</table>");
		
}

function dirinfo($dir) {
	if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
		$perm=substr(sprintf('%o', @fileperms($dir)), -4);
		$user=posix_getpwuid(fileowner($dir));
		$group=posix_getgrgid(filegroup($dir));
		return $perm." ".$user['name']." ".$group['name'];
	} else {
		return "N/A";
	}
}

function microtime_float() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function create_tmp_folder()
{
	$name=check_file_name(dirname(__FILE__).'/'.'bx_folder_test');
	mkdir($name);
	if (file_exists($name))
		return $name;
	else
		return false;
}

function create_tmp_file()
{
	$name=check_file_name(dirname(__FILE__).'/'.'bx_file_test');
	$f=fopen($name,'wb');
	if ($f)	fclose($f);
	if (file_exists($name))
		return $name;
	else
		return false;
}

function check_file_name($name) 
{
	if (file_exists($name))
		return check_file_name($name."_tmp");
	else
		return $name;
}

function create_tmp_table($innodb=false)
{
	$name='bx_test';
	while (true)
	{
		$name.='_tmp';
		$res=mysql_query("SHOW TABLES LIKE '".$name."'");

		if ($res)
		{
			if (!mysql_fetch_row($res))
			{
				if ($innodb && mysql_query("CREATE TABLE ".$name." (tst varchar(100), tst2 varchar(50), tst3 varchar(30), tst4 text) ENGINE=INNODB"))
					return $name;
				elseif (!$innodb && mysql_query("CREATE TABLE ".$name." (tst varchar(100), tst2 varchar(50), tst3 varchar(30), tst4 text) ENGINE=MYISAM"))
					return $name;
				else
					return false;
			}
		} else
			return false;
	}
}

function prepare_htaccess_test() 
{
	$path = dirname($_SERVER['PHP_SELF'])."/bitrix_htaccess_test";
	$dir = $_SERVER['DOCUMENT_ROOT'].$path;
	clear_htaccess_test();

	if (!mkdir($dir)) return;
	$f=fopen($dir."/.htaccess","wb");
	$str = "ErrorDocument 404 ".$path."/404.php\n".
	"<IfModule mod_rewrite.c>\n".
	"	RewriteEngine Off\n".
	"</IfModule>";
	fputs($f, $str);
	fclose($f);
	if (!file_exists($dir."/.htaccess")) return;

	$f=fopen($dir."/404.php","wb");
	$str = "<?php\n".
	"header(\"HTTP/1.1 200 OK\");\n".
	"echo 'SUCCESS'; ?>";
	fputs($f, $str);
	fclose($f);
	if (!file_exists($dir."/404.php")) return;

	return true;
}

function clear_htaccess_test()
{
	$dir = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF'])."/bitrix_htaccess_test";
	if (file_exists($dir)) 
	{
		unlink($dir."/.htaccess");
		unlink($dir."/404.php");
		rmdir($dir);
	}
}

function getHttpResponse($res, $strRequest)
{
	fputs($res, $strRequest);

	$bChunked = False;
	while (($line = fgets($res, 4096)) && $line != "\r\n")
	{
		if (@preg_match("/Transfer-Encoding: +chunked/i", $line))
			$bChunked = True;
		elseif (@preg_match("/Content-Length: +([0-9]+)/i", $line, $regs))
			$length = $regs[1];
	}

	$strRes = "";
	if ($bChunked)
	{
		$maxReadSize = 4096;

		$length = 0;
		$line = FGets($res, $maxReadSize);
		$line = StrToLower($line);

		$strChunkSize = "";
		$i = 0;
		while ($i < StrLen($line) && in_array($line[$i], array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f")))
		{
			$strChunkSize .= $line[$i];
			$i++;
		}

		$chunkSize = hexdec($strChunkSize);

		while ($chunkSize > 0)
		{
			$processedSize = 0;
			$readSize = (($chunkSize > $maxReadSize) ? $maxReadSize : $chunkSize);

			while ($readSize > 0 && $line = fread($res, $readSize))
			{
				$strRes .= $line;
				$processedSize += StrLen($line);
				$newSize = $chunkSize - $processedSize;
				$readSize = (($newSize > $maxReadSize) ? $maxReadSize : $newSize);
			}
			$length += $chunkSize;

			$line = FGets($res, $maxReadSize);

			$line = FGets($res, $maxReadSize);
			$line = StrToLower($line);

			$strChunkSize = "";
			$i = 0;
			while ($i < StrLen($line) && in_array($line[$i], array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f")))
			{
				$strChunkSize .= $line[$i];
				$i++;
			}
			$chunkSize = hexdec($strChunkSize);
		}
	}
	elseif ($length)
		$strRes = fread($res, $length);
	else
		while ($line = fread($res, 4096))
			$strRes .= $line;

	return $strRes;
}

function GM($code)
{
	global $M;
	return $M[$code];
}

function debug($line)
{
	static $f, $fail, $file;
	if (!$_REQUEST['debug'] || $fail)
		return;
	if (!$f)
	{
		if ($f = fopen(dirname(__FILE__).'/bitrix_server_test.log','wb'))
			$file = @file(__FILE__);
		else
			$fail = 1;
	}
	fwrite($f,date('H:i:s')."\t".$line."\t".trim($file[$line-2])."\n");
}
######################################################################?>
						</div>
					</td>
				</tr>
		<tr>
			<td align=left nowrap class=smalltext><?php echo GM('COPY');?><?php echo date("Y")?></td>
			<td align=right nowrap class=smalltext><?php echo GM('SUPPORT_LINK');?>&nbsp;
		</tr>
				</table>
		</table></td>
</tr>
</table>
</body>
<?php 
if ($bTest) 
{ 
?>
	<script language=JavaScript>
		var last_mem = 8;
		var max_success = 0;
		var memory_errors = 5;
		var absolute_max = 999;

		var tmr = 70;
		var tmr1 = 0;
		var time_test = document.getElementById('time_test');
		var time_test_cpu = document.getElementById('time_test_cpu');

		function NewXML()
		{
			if(window.XMLHttpRequest) {
				try {
					xml = new XMLHttpRequest();
				} catch(e) {
				}
			} else if(window.ActiveXObject) {
				try {
					xml = new ActiveXObject("Msxml2.XMLHTTP");
				} catch(e) {
					try {
						xml = new ActiveXObject("Microsoft.XMLHTTP");
					} catch(e) {
					}
				}
			}
			return xml;
		}

		function AjaxSend(xml, url, callback)
		{
			if (null!=callback)
				xml.onreadystatechange = function () 
				{ 
					if (xml.readyState == 4 || xml.readyState=="complete")
						callback(xml.responseText);
				}

			xml.open("GET", url, true);
			xml.send("");
		}

		function memory_test(max_mem)
		{
			xml = NewXML();
			callback = function(a)
			{
				if (a == 'SUCCESS')
				{
					max_success = last_mem;
					last_mem *= 2;
					if (last_mem > absolute_max)
						last_mem = parseInt((max_success + absolute_max)/2);

					if (max_success == last_mem)
					{
						memory_errors = 0;
						last_mem += 1;
					}

					if (max_success < 256)
					{
						document.getElementById('memory_limit').innerHTML = max_success + '...';
						memory_test(last_mem - 1);
					}
					else
						document.getElementById('memory_limit').innerHTML = '&gt;256';
				}
				else if (memory_errors > 0)
				{
					absolute_max = last_mem;
					last_mem = parseInt((max_success + last_mem)/2);
					memory_test(last_mem - 1);
					memory_errors--;
				}
				else
				{
					link = " <a href='?memory_test=Y&debug=Y&max=" + last_mem + "' target=_blank title='<?php echo GM('OPEN_RESULT')?>'>&gt;&gt;</a>";
					if (max_success== 0)
						res = '<font color=red>N/A</font>' + link;
					else
						res = max_success + link;

					document.getElementById('memory_limit').innerHTML = res;
				}
			}
			AjaxSend(xml, '?memory_test=Y&debug=Y&max=' + max_mem, callback);
		}

		function my_timer() 
		{
			tmr--;
			tmr1++;
			if (tmr < 1)
			{
				res = '<font color=red><?php echo GM('NO');?></font>';
				clearInterval(my_interval);
			}
			else 
				res = '<font color=gray><?php echo GM('TESTING');?> (' + tmr + ')</font>';
			time_test.innerHTML = res;
		}

		function start_test()
		{
			// time test
			xml = NewXML();
			callback = function(a) 
			{
				if (a == 'SUCCESS')
					res = '<font color=green><?php echo GM('YES');?></font>';
				else
					res = '<font color=red><?php echo GM('NO');?></font> (<?php echo GM('LIMIT');?> ' + tmr1 + ")  <a href='javascript:alert(\"" + escape(xml.responseText.substr(0,100)) + "\")' title='<?php echo GM('SERVER_ANS');?>'>&gt;&gt;</a>";
				time_test.innerHTML = res;
				clearInterval(my_interval);
			}
			AjaxSend(xml, "?time_test=Y", callback);
			
			// time test with max cpu
			xml = NewXML();
			callback = function(a) 
			{
				if (a == 'SUCCESS')
					res = '<font color=green><?php echo GM('YES');?></font>';
				else
					res = '<font color=red><?php echo GM('NO');?></font> ' + "<a href='javascript:alert(\"" + escape(a.substr(0,100))  + "\")' title='<?php echo GM('SERVER_ANS');?>'>&gt;&gt;</a>";
				time_test_cpu.innerHTML = res;
			}
			AjaxSend(xml, "?time_test=Y&max_cpu=Y",callback);

			my_interval = setInterval(my_timer, 1000);

			// session test
			xml = NewXML();
			callback = function(a)
			{
				if (a == 'SUCCESS')
					res = '<font color=green><?php echo GM('YES');?></font>';
				else
					res = '<font color=red><?php echo GM('NO');?></font>';

				document.getElementById('session').innerHTML = res;
			}
			AjaxSend(xml, '?session_test=Y',callback);
			
			// memory test
			memory_test(last_mem);
		}
	</script>
<?php 
}

// Finish
debug(__LINE__);

function bx_accelerator_reset()
{
        if(function_exists("accelerator_reset"))
                accelerator_reset();
        elseif(function_exists("wincache_refresh_if_changed"))
                wincache_refresh_if_changed();
}
?>
