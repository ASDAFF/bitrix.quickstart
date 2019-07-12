<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult["TAGS_CHAIN"] = array();

$arResult["ELEMENTS"] = array();
$arResult["CATEGORIES"] = array();
$arResult["ITEM_IDS"] = array();

foreach($arResult["SEARCH"] as $i=>$arItem){
	if(substr($arItem["ITEM_ID"],0,1)!="S"){
		$arResult["ELEMENTS"][$arItem["ITEM_ID"]] = $arItem["ITEM_ID"];
		$arResult["ITEM_IDS"][] = $arItem["ITEM_ID"];
	}else{
		$arResult["CATEGORIES"][$arItem["ITEM_ID"]] = $arItem;
	}
}

if (!empty($arResult["ELEMENTS"]) && CModule::IncludeModule("iblock"))
{

	$obParser = new CTextParser;

	$arResult["PRICES"] = array();

	$arSelect = array(
		"ID",
		"IBLOCK_ID",
		"PREVIEW_TEXT",
		"PREVIEW_PICTURE",
		"DETAIL_PICTURE",
	);
	$arFilter = array(
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
	);
	$arFilter["=ID"] = $arResult["ELEMENTS"];
	$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while($arElement = $rsElements->Fetch())
	{
		$arResult["ELEMENTS"][$arElement["ID"]] = $arElement;
	}
}

foreach($arResult["SEARCH"] as $i=>$arItem)
{
	switch($arItem["MODULE_ID"])
	{
		case "iblock":
			if(array_key_exists($arItem["ITEM_ID"], $arResult["ELEMENTS"]))
			{
				$arElement = &$arResult["ELEMENTS"][$arItem["ITEM_ID"]];

					if ($arElement["PREVIEW_PICTURE"] > 0)
						$arElement["PICTURE"] = CFile::ResizeImageGet($arElement["PREVIEW_PICTURE"], array("width"=>100, "height"=>100), BX_RESIZE_IMAGE_PROPORTIONAL, true);
					elseif ($arElement["DETAIL_PICTURE"] > 0)
						$arElement["PICTURE"] = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"], array("width"=>100, "height"=>100), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			}
			break;
	}

}

if (!empty($arResult["ELEMENTS"]) && CModule::IncludeModule("mlife.asz"))
{
//получаем типы цен для групп текущего пользователя
$arGroups = $USER->GetUserGroupArray();

if(is_array($arGroups)){
	$priceTip = \Mlife\Asz\CurencyFunc::getPriceForGroup($arGroups,SITE_ID);
}else{
	$priceTip = \Mlife\Asz\CurencyFunc::getPriceForGroup();
}
//типы цен из настроек компонента
if(is_array($arParams["PRICE_CODE"])){
	$newArPrice = array();
	foreach($priceTip as $key=>$p_id){
		if(in_array($p_id,$arParams["PRICE_CODE"])) $newArPrice[] = $p_id;
	}
	$priceTip = $newArPrice;
}

//получаем цены
$arResult["PRICE"] = \Mlife\Asz\CurencyFunc::getPriceBase($priceTip,$arResult["ITEM_IDS"],SITE_ID);
}
?>