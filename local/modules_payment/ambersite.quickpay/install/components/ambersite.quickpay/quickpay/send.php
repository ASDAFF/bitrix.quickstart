<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");  
global $MESS;
$strPath2Lang = str_replace('\\', '/', __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/send.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/send.php"));

if(CModule::IncludeModule('ambersite.quickpay')) {
	echo QuickPay::Send($_REQUEST);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>