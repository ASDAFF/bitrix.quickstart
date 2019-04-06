<?

define('NS_SITE_ID', COption::GetOptionString('zebrus.resize', 'NS_SITE_ID', 's1'));
define('URLREWRITER_CONDITION', '#^/resize/([0-9]+)x([0-9]+)x([0-9]+)x([0-9]+)x([0-9]+)/(.*)#');
define('URLREWRITER_FILE_PATH', '/zebrus_resize.php');



define('ZEBRUS_CHACHE_IMG_PATH', $_SERVER["DOCUMENT_ROOT"].'/bitrix/cache/zebrus_resize/'); 
CheckDirPath(ZEBRUS_CHACHE_IMG_PATH); 



?>