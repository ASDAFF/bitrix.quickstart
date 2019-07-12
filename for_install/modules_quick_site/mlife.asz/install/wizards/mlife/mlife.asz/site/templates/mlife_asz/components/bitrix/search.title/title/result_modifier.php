<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arResult["ITEM_IDS"] = array();

$PREVIEW_WIDTH = intval($arParams["PREVIEW_WIDTH"]);
if ($PREVIEW_WIDTH <= 0)
	$PREVIEW_WIDTH = 75;

$PREVIEW_HEIGHT = intval($arParams["PREVIEW_HEIGHT"]);
if ($PREVIEW_HEIGHT <= 0)
	$PREVIEW_HEIGHT = 75;

$arParams["PRICE_VAT_INCLUDE"] = $arParams["PRICE_VAT_INCLUDE"] !== "N";

$arResult["ELEMENTS"] = array();
$arResult["SEARCH"] = array();

foreach($arResult["CATEGORIES"] as $category_id => $arCategory)
{
	foreach($arCategory["ITEMS"] as $i => $arItem)
	{
		if(isset($arItem["ITEM_ID"]))
		{
		$arResult["ITEM_IDS"][] = $arItem["ITEM_ID"];
			$arResult["SEARCH"][] = &$arResult["CATEGORIES"][$category_id]["ITEMS"][$i];
			if (
				$arItem["MODULE_ID"] == "iblock"
				&& substr($arItem["ITEM_ID"], 0, 1) !== "S"
			)
			{
				$arResult["ELEMENTS"][$arItem["ITEM_ID"]] = $arItem["ITEM_ID"];
			}
		}
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
		if($arParams["PREVIEW_TRUNCATE_LEN"] > 0)
			$arElement["PREVIEW_TEXT"] = $obParser->html_cut($arElement["PREVIEW_TEXT"], $arParams["PREVIEW_TRUNCATE_LEN"]);

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

				if ($arParams["SHOW_PREVIEW"] == "Y")
				{
					if ($arElement["PREVIEW_PICTURE"] > 0)
						$arElement["PICTURE"] = CFile::ResizeImageGet($arElement["PREVIEW_PICTURE"], array("width"=>$PREVIEW_WIDTH, "height"=>$PREVIEW_HEIGHT), BX_RESIZE_IMAGE_EXACT, true);
					elseif ($arElement["DETAIL_PICTURE"] > 0)
						$arElement["PICTURE"] = CFile::ResizeImageGet($arElement["DETAIL_PICTURE"], array("width"=>$PREVIEW_WIDTH, "height"=>$PREVIEW_HEIGHT), BX_RESIZE_IMAGE_EXACT, true);
				}
			}
			break;
	}

	$arResult["SEARCH"][$i]["ICON"] = true;
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
//print_r($arResult["PRICE"]);

?>