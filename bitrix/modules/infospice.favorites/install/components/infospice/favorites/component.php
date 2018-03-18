<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();


if (!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if (strlen($arParams["IBLOCK_TYPE"]) <= 0)
	$arParams["IBLOCK_TYPE"] = "favorites";
CModule::IncludeModule('iblock');

$arParams["IBLOCK_ID"] = $this->GetFavoriteIBlockID();
$arResult['IBLOCK_ID'] = $arParams["IBLOCK_ID"];


if ($USER->IsAuthorized()) {
	$sectionID = $this->CheckUserSection();

	$arResult["ITEMS"] = array();

	$rsElement = CIBlockElement::GetList(array(), array(
				'IBLOCK_TYPE'	 => $arParams['IBLOCK_TYPE'],
				'IBLOCK_ID'		 => $arParams['IBLOCK_ID'],
				'SECTION_ID'	 => $sectionID), false, false, array('ID', 'NAME', 'PROPERTY_URL'));
	while ($arElement = $rsElement->Fetch()) {

		$arButtons = CIBlock::GetPanelButtons($arParams['IBLOCK_ID'], $arElement["ID"], 0, array(
					"SECTION_BUTTONS"	 => false,
					"SESSID"			 => false)
		);
		$arElement["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
		$arElement["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

		if ($this->IsFavoriteUrl($arElement['PROPERTY_URL_VALUE'])) {
			$arResult['CURRENT_PAGE_IS_FAVORITE'] = 'Y';
			$arElement['CURRENT_PAGE_IS_FAVORITE'] = 'Y';
		}

		$arResult["ITEMS"][] = $arElement;
	}
	$arGroups = $USER->GetUserGroupArray();

	foreach ($arGroups as $idGroup) {
		if (in_array($idGroup, $arParams['GROUPS'])) {
			$arResult['ACCESS'] = 'Y';
		}
	}

	$this->IncludeComponentTemplate();
}