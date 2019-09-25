<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

error_reporting(0);
header('Content-Type: text/html; charset=utf-8');
 
$result_bool = false;
 
if(
	isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
) {
	
	$id = intval(htmlspecialchars($_REQUEST["id"]));
	$type = htmlspecialchars($_REQUEST["type"]);
	$list = htmlspecialchars($_REQUEST["list"]);
	
	if(!CModule::IncludeModule("iblock"))
		return;

	if($id > 0)
	{
		$res = CIBlockElement::GetByID($id);
		$arElement = $res->GetNext();
	}
	
	switch($type)
	{
		case 'compare_add': 
			$_SESSION[$list][$arElement['IBLOCK_ID']]["ITEMS"][$arElement['ID']] = $arElement;
			$result_bool = true;
		break;
		case 'compare_del': 
			unset($_SESSION[$list][$arElement['IBLOCK_ID']]["ITEMS"][$arElement['ID']]);
			$result_bool = true;
		break;
	}
}

if($result_bool) echo 'SUCCESS';
			else echo 'ERROR';
?>