<?
/**
* Виджет групп Вконтакте
*/

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule('iblock');
$APPLICATION->AddHeadScript('http://vk.com/js/api/openapi.js');

if(substr($arParams["LINK"], 0, 6) == "public") {
	$arResult["LINK"] = substr($arParams["LINK"], 6);
} else {

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.vk.com/method/groups.getById?gids=".$arParams["LINK"]);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	$getGroupXML = curl_exec($ch);
	curl_close($ch);
	//curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
	$arGroupXML = json_decode($getGroupXML);
	$arResult["LINK"] = $arGroupXML->response[0]->gid;
}

$this->IncludeComponentTemplate($componentPage);
?>
