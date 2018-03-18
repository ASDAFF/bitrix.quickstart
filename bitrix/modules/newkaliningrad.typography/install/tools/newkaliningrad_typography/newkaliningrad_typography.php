<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(CModule::IncludeModule('newkaliningrad.typography')){
	function utf2win($input){
		return iconv("UTF-16BE", "cp1251", pack("H4", $input[1]));
	}
	$_REQUEST['text'] = str_replace("#a#", "&", $_REQUEST['text']); 
	$_REQUEST['text'] = preg_replace_callback('!%u([\da-f]{4})!i', 'utf2win', $_REQUEST['text']);
	$_REQUEST['text'] = iconv('windows-1251', 'utf-8',  $_REQUEST['text']);

	$typography = new newkaliningrad_EMTypograph();
	$typography->set_text($_REQUEST['text']);
	$result = $typography->apply();
	if(!defined("BX_UTF")){
		$result = iconv('utf-8','windows-1251', $result);
	}
	print $result;
}