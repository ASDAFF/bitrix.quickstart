<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arResult["MENU_ITEMS"] = array();
$arResult["IBLOCK_LIST"] = array();

if($this->StartResultCache())
{
	if(!CModule::IncludeModule('iblock'))
	{
		$this->AbortResultCache();
		return array();
	}

	if(isset($arParams["IBLOCK_TYPE_ID"]))
	{
		$arFilter = array(
			"TYPE"=>$arParams["IBLOCK_TYPE_ID"],
			"SITE_ID"=>SITE_ID,
		);
	}
	else
	{
		if(!CModule::IncludeModule('catalog'))
		{
			$this->AbortResultCache();
			return array();
		}

		$dbRes = CCatalog::GetList(
			array(),
			array('LID' => SITE_ID)
		);

		$arFilter = array(
			"ID"=>array(),
			"SITE_ID"=>SITE_ID,
		);

		while($arRes = $dbRes->Fetch())
			$arFilter["ID"][] = $arRes["IBLOCK_ID"];
	}

	$dbIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'ID' => 'DESC'), $arFilter);
	$dbIBlock = new CIBlockResult($dbIBlock);

	while($arIBlock = $dbIBlock->GetNext())
	{
		if(defined("BX_COMP_MANAGED_CACHE"))
			$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$arIBlock["ID"]);

		if($arIBlock["ACTIVE"] == "Y")
			$arResult["IBLOCK_LIST"][$arIBlock['ID']] = $arIBlock;
	}

	if(defined("BX_COMP_MANAGED_CACHE"))
		$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_new");

	$this->EndResultCache();
}

foreach($arResult["IBLOCK_LIST"] as $arIBlock)
{
	$aMenuLinksExt = $APPLICATION->IncludeComponent("bitrix:menu.sections", "", array(
		"IS_SEF" => "Y",
		"SEF_BASE_URL" => "",
		"SECTION_PAGE_URL" => $arIBlock['SECTION_PAGE_URL'],
		"DETAIL_PAGE_URL" => $arIBlock['DETAIL_PAGE_URL'],
		"IBLOCK_TYPE" => $arIBlock['IBLOCK_TYPE_ID'],
		"IBLOCK_ID" => $arIBlock['ID'],
		"DEPTH_LEVEL" => "5",
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
	), false, Array('HIDE_ICONS' => 'Y'));

	if(count($arResult["IBLOCK_LIST"]) > 1)
	{
		foreach ($aMenuLinksExt as $key => $arItem)
			$aMenuLinksExt[$key][3]['DEPTH_LEVEL']++;

		$arResult["MENU_ITEMS"] = array_merge(
			$arResult["MENU_ITEMS"],
			array(
				array(
					$arIBlock['NAME'],
					$arIBlock["LIST_PAGE_URL"],
					array(),
					array(
						'FROM_IBLOCK' => true,
						'IS_PARENT' => count($aMenuLinksExt) > 0,
						'DEPTH_LEVEL' => 1,
						'IBLOCK_ROOT_ITEM' => true
					),
				),
			),
			$aMenuLinksExt
		);
	}
	else
	{
		$arResult["MENU_ITEMS"] = $aMenuLinksExt;
	}
}

return $arResult["MENU_ITEMS"];
?>