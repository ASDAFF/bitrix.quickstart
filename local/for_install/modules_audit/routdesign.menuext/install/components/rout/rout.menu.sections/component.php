<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
if(!is_array($arParams["IBLOCK_ID"]))
	$arParams["IBLOCK_ID"] = Array();
foreach($arParams["IBLOCK_ID"] as $k => $v)
	if($v === "")
		unset($arParams["IBLOCK_ID"][$k]);

if(!is_array($arParams["IBLOCK_TYPE"]))
	$arParams["IBLOCK_TYPE"] = Array();
foreach($arParams["IBLOCK_TYPE"] as $k => $v)
	if($v === "")
		unset($arParams["IBLOCK_TYPE"][$k]);

$arParams['ONLY_SECTIONS'] = $arParams['ONLY_SECTIONS']=='Y';
$arParams['INCLUDE_ELEMENTS'] = $arParams['INCLUDE_ELEMENTS']=='Y';

$arParams["DEPTH_LEVEL"] = intval($arParams["DEPTH_LEVEL"]);
if($arParams['ONLY_SECTIONS'] && $arParams["DEPTH_LEVEL"] <= 0)
	$arParams["DEPTH_LEVEL"] = 1;

$arParams["IS_SEF"] = "N";
$arParams["DETAIL_PAGE_URL"] = "";

$arResult["SECTIONS"] = array();
$arResult["ELEMENT_LINKS"] = array();
$arResult["TYPES"] = array();

if($this->StartResultCache())
{
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
	}
	else
	{
		$depthLvl = 0;
		$arData = Array(
			"IBLOCK_TYPES" => Array(),
			"IBLOCKS" => Array(),
		);
		if(count($arParams["IBLOCK_TYPE"]) > 0) {
			$depthLvl++;
			$rsIblocksType = CIBlockType::GetList(
				Array('SORT' => "ASC"),
				Array(
					'=ID' => $arParams["IBLOCK_TYPE"],
				)
			);
			while($arIblockType = $rsIblocksType->Fetch()) {
				if($arIBType = CIBlockType::GetByIDLang($arIblockType["ID"], LANG)) {
					$arIBType['URL'] = $arParams['CATEGORY_'.$arIBType['IBLOCK_TYPE_ID'].'_LINK'] = CIBlock::ReplaceDetailUrl(
						$arParams['CATEGORY_'.$arIBType['IBLOCK_TYPE_ID'].'_LINK'],
						Array(
							"IBLOCK_TYPE_ID" => $arIBType['IBLOCK_TYPE_ID'],
						)
					);
					$arData['IBLOCK_TYPES'][$arIBType['IBLOCK_TYPE_ID']] = Array(
						"NAME" => $arIBType['NAME'],
						"ID" => $arIBType['IBLOCK_TYPE_ID'],
						"URL" => $arIBType['URL'],
						"DEPTH_LEVEL" => $depthLvl,
					);
					$arResult["TYPES"][$arIBType['IBLOCK_TYPE_ID']] = $arIBType['IBLOCK_TYPE_ID'];
				}
			}
			$depthLvl++;
		}
		if(count($arParams["IBLOCK_ID"]) > 0) {
			if(count($arParams["IBLOCK_TYPE"]) <= 0 && !$arParams['ONLY_SECTIONS'])
				$depthLvl++;
			$rsIblocks = CIBlock::GetList(
				Array('SORT' => "ASC"),
				Array(
					'ID' => $arParams["IBLOCK_ID"],
					'SITE_ID' => SITE_ID,
					'ACTIVE' => 'Y',
					'CHECK_PERMISSIONS' => 'N',
				), false
			);
			while($arIblock = $rsIblocks->Fetch()) {
				$arIblock['URL'] = CIBlock::ReplaceDetailUrl(
					$arIblock['LIST_PAGE_URL'],
					Array(
						"IBLOCK_TYPE_ID" => $arIblock['IBLOCK_TYPE_ID'],
						"IBLOCK_CODE" => $arIblock['CODE'],
						"IBLOCK_ID" => $arIblock['ID'],
						"IBLOCK_EXTERNAL_ID" => $arIblock['EXTERNAL_ID'],
					)
				);
				if(count($arParams["IBLOCK_TYPE"]) <= 0 && $arParams['CATEGORY_'.$arIblock['ID'].'_IS_SEF'] === "Y") {
					$arParams['CATEGORY_'.$arIblock['ID'].'_LINK'] = CIBlock::ReplaceDetailUrl(
						$arParams['~CATEGORY_'.$arIblock['ID'].'_LINK'],
						Array(
							"IBLOCK_TYPE_ID" => $arIblock['IBLOCK_TYPE_ID'],
							"IBLOCK_CODE" => $arIblock['CODE'],
							"IBLOCK_ID" => $arIblock['ID'],
							"IBLOCK_EXTERNAL_ID" => $arIblock['EXTERNAL_ID'],
						)
					);
					$arResult["TYPES"][$arIblock['ID']] = $arIblock['ID'];
				}
				if(!$arParams['ONLY_SECTIONS']) {
					$arData['IBLOCKS'][$arIblock['IBLOCK_TYPE_ID']][] = Array(
						"NAME" => $arIblock['NAME'],
						"ID" => $arIblock['ID'],
						"URL" => $arIblock['URL'],
						"DEPTH_LEVEL" => $depthLvl,
					);
				}
				if($arParams["DEPTH_LEVEL"] > 0) {
					$arFilter = array(
						"IBLOCK_ID" => $arIblock["ID"],
						"GLOBAL_ACTIVE" => "Y",
						"IBLOCK_ACTIVE" => "Y",
						"<="."DEPTH_LEVEL" => $arParams["DEPTH_LEVEL"],
					);
					$arOrder = array("left_margin" => "asc");
					$rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, array(
						"ID",
						"DEPTH_LEVEL",
						"NAME",
						"SECTION_PAGE_URL",
					));
					if($arParams['CATEGORY_'.(count($arParams["IBLOCK_TYPE"]) <= 0 ? $arIblock['ID'] : $arIblock['IBLOCK_TYPE_ID']).'_IS_SEF'] === "Y")
						$rsSections->SetUrlTemplates("", $arParams['CATEGORY_'.(count($arParams["IBLOCK_TYPE"]) <= 0 ? $arIblock['ID'] : $arIblock['IBLOCK_TYPE_ID']).'_LINK'].$arParams["CATEGORY_".(count($arParams["IBLOCK_TYPE"]) <= 0 ? $arIblock['ID'] : $arIblock['IBLOCK_TYPE_ID'])."_SECTION_LINK"]);
					while($arSection = $rsSections->GetNext()) {
						$arData['IBLOCKS'][$arIblock['IBLOCK_TYPE_ID']][] = Array(
							"NAME" => $arSection['NAME'],
							"ID" => $arSection['ID'],
							"URL" => $arSection['SECTION_PAGE_URL'],
							"DEPTH_LEVEL" => $arSection['DEPTH_LEVEL'] + $depthLvl,
						);
					}
				}
			}
		}
		
		if(count($arData['IBLOCK_TYPES']) > 0) {
			foreach($arData['IBLOCK_TYPES'] as $key => $type) {
				$arResult["SECTIONS"][] = $type;
				if(array_key_exists($key, $arData['IBLOCKS'])) {
					$arResult["SECTIONS"] = array_merge($arResult["SECTIONS"], $arData['IBLOCKS'][$key]);
				}
			}
		} else {
			foreach($arData['IBLOCKS'] as $key => $ar)
				$arResult["SECTIONS"] = array_values($ar);
		}
		
		$this->EndResultCache();
	}
}

//In "SEF" mode we'll try to parse URL and get ELEMENT_ID from it
foreach($arResult["TYPES"] as $a) {
	if($arParams['CATEGORY_'.$a.'_IS_SEF'] === 'Y') {
		$componentPage = CComponentEngine::ParseComponentPath(
			$arParams['CATEGORY_'.$a.'_LINK'],
			array(
				"section" => $arParams["CATEGORY_".$a."_SECTION_LINK"],
				"detail" => $arParams["CATEGORY_".$a."_DETAIL_LINK"],
			),
			$arVariables
		);
		if($componentPage === "detail")
		{
			CComponentEngine::InitComponentVariables(
				$componentPage,
				array("SECTION_ID", "ELEMENT_ID"),
				array(
					"section" => array("SECTION_ID" => "SECTION_ID"),
					"detail" => array("SECTION_ID" => "SECTION_ID", "ELEMENT_ID" => "ELEMENT_ID"),
				),
				$arVariables
			);
			$arParams["ELEMENT_ID"] = intval($arVariables["ELEMENT_ID"]);
			$arParams["IS_SEF"] = "Y";
			$arParams["DETAIL_PAGE_URL"] = $arParams["CATEGORY_".$a."_DETAIL_LINK"];
		}
		
	}
}

if(($arParams["ELEMENT_ID"] > 0) && (intval($arVariables["SECTION_ID"]) <= 0 && strlen(trim($arVariables["SECTION_CODE"])) <= 0) && CModule::IncludeModule("iblock"))
{
	$arSelect = array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
	$arFilter = array(
		"ID" => $arParams["ELEMENT_ID"],
		"ACTIVE" => "Y",
	);
	$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	if(($arParams["IS_SEF"] === "Y") && (strlen($arParams["DETAIL_PAGE_URL"]) > 0))
		$rsElements->SetUrlTemplates($arParams["SEF_BASE_URL"].$arParams["DETAIL_PAGE_URL"]);
	while($arElement = $rsElements->GetNext())
	{
		$arResult["ELEMENT_LINKS"][$arElement["IBLOCK_SECTION_ID"]][] = $arElement["~DETAIL_PAGE_URL"];
	}
}

$aMenuLinksNew = array();
$menuIndex = 0;
$previousDepthLevel = 1;
foreach($arResult["SECTIONS"] as $arSection)
{
	if($menuIndex > 0)
		$aMenuLinksNew[$menuIndex - 1][3]["IS_PARENT"] = $arSection["DEPTH_LEVEL"] > $previousDepthLevel;
	$previousDepthLevel = $arSection["DEPTH_LEVEL"];

	$aMenuLinksNew[$menuIndex++] = array(
		htmlspecialchars($arSection["NAME"]),
		$arSection["URL"],
		$arResult["ELEMENT_LINKS"][$arSection["ID"]],
		array(
			"FROM_IBLOCK" => true,
			"IS_PARENT" => false,
			"DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
		),
	);
}

return $aMenuLinksNew;
?>