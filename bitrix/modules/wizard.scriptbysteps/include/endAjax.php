<?if (!defined('HAS_WORK_WITH_COOKIE') || !HAS_WORK_WITH_COOKIE){
	//Правильно завершаем скрипт если не работаем с куки
	global $DB;
	$DB->Disconnect();
	\CMain::ForkActions();
	die();
}
else{
	//Правильно завершаем скрипт если работаем с куки через d7
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
}