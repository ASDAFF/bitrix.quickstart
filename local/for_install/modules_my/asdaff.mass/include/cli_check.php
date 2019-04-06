<?
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../..');
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
define('NO_KEEP_STATISTIC', true); 
define('NOT_CHECK_PERMISSIONS', true);
define('PUBLIC_AJAX_MODE', true); 
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule('asdaff.mass');
$APPLICATION->RestartBuffer();
print CUtil::translit(GetMessage('WDA_CLI_CHECK_RUS'), LANGUAGE_ID, array(
	'max_len' => 100,
	'change_case' => '',
	'replace_space' => '_',
	'replace_other' => '_',
	'delete_repeat_replace' => true,
));
die();
?>