<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock")) return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

	
$arResizeTypes = Array(
    "BX_RESIZE_IMAGE_EXACT" => getMessage('CP_PXSD_RES_1'), //масштабирует в прямоугольник $arSize без сохранения пропорций;
    "BX_RESIZE_IMAGE_PROPORTIONAL" => getMessage('CP_PXSD_RES_2'), // масштабирует с сохранением пропорций, размер ограничивается $arSize;
    "BX_RESIZE_IMAGE_PROPORTIONAL_ALT" => getMessage('CP_PXSD_RES_3'), // масштабирует с сохранением пропорций, размер ограничивается $arSize, улучшенна обработка вертикальных картинок.
);	
	

$arProperty_UF = array();
$arSProperty_LNS = array();
$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION");
foreach($arUserFields as $FIELD_NAME=>$arUserField) {
	$arProperty_UF[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME;
	if($arUserField["USER_TYPE"]["BASE_TYPE"]=="string")
		$arSProperty_LNS[$FIELD_NAME] = $arProperty_UF[$FIELD_NAME];
}

$arSectionFields = Array(
	"ID" => getMessage("CP_PXSD_SECTION_FIELD_ID"),
	"NAME" => getMessage("CP_PXSD_SECTION_FIELD_NAME"),
	"CODE" => getMessage("CP_PXSD_SECTION_FIELD_CODE"),
	"XML_ID" => getMessage("CP_PXSD_SECTION_FIELD_XML_ID"),
	"IBLOCK_ID" => getMessage("CP_PXSD_SECTION_FIELD_IBLOCK_ID"),
	"IBLOCK_SECTION_ID" => getMessage("CP_PXSD_SECTION_FIELD_IBLOCK_SECTION_ID"),
	"TIMESTAMP" => getMessage("CP_PXSD_SECTION_FIELD_TIMESTAMP"),
	"SORT" => getMessage("CP_PXSD_SECTION_FIELD_SORT"),
	"ACTIVE" => getMessage("CP_PXSD_SECTION_FIELD_ACTIVE"),
	//"GLOBAL_ACTIVE" => getMessage("CP_PXSD_SECTION_FIELD_GLOBAL_ACTIVE"),
	"PICTURE" => getMessage("CP_PXSD_SECTION_FIELD_PICTURE"),
	"DESCRIPTION" => getMessage("CP_PXSD_SECTION_FIELD_DESCRIPTION"),
	"LEFT_MARGIN" => getMessage("CP_PXSD_SECTION_FIELD_LEFT_MARGIN"),
	"RIGHT_MARGIN" => getMessage("CP_PXSD_SECTION_FIELD_RIGHT_MARGIN"),
	"DEPTH_LEVEL" => getMessage("CP_PXSD_SECTION_FIELD_DEPTH_LEVEL"),
	//"SEARCHABLE_CONTENT" => getMessage("CP_PXSD_SECTION_FIELD_SEARCHABLE_CONTENT"),
	//"SECTION_PAGE_URL" => getMessage("CP_PXSD_SECTION_FIELD_SECTION_PAGE_URL"),
	"MODIFIED_BY" => getMessage("CP_PXSD_SECTION_FIELD_MODIFIED_BY"),
	"DATE_CREATE" => getMessage("CP_PXSD_SECTION_FIELD_DATE_CREATE"),
	"CREATED_BY" => getMessage("CP_PXSD_SECTION_FIELD_CREATED_BY"),
	"DETAIL_PICTURE" => getMessage("CP_PXSD_SECTION_FIELD_DETAIL_PICTURE")
);

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"AJAX_MODE" => array(),
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_PXSD_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_PXSD_IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"SHOW_ONLY_ACTIVE" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_PXSD_SHOW_ONLY_ACTIVE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y"
		),
		"SECTION_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_PXSD_IBLOCK_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["SECTION_ID"]}',
		),
		"SECTION_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_PXSD_IBLOCK_SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"SECTION_S_FIELDS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXSD_SECTION_S_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arSectionFields
		),
		"SECTION_F_FIELDS" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXSD_SECTION_F_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => Array()
		),
		"SECTION_S_PROPERTIES" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXSD_SECTION_S_PROPERTIES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arProperty_UF,
			"REFRESH" => "Y"
		),
		"SECTION_F_PROPERTIES" =>array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXSD_SECTION_F_PROPERTIES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => Array()
		),
		"SECTION_URL" => CIBlockParameters::GetPathTemplateParam(
			"SECTION",
			"SECTION_URL",
			GetMessage("CP_PXSD_IBLOCK_SECTION_URL"),
			"",
			"URL_TEMPLATES"
		),
		"META_KEYWORDS" =>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_PXSD_DESC_KEYWORDS"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => array_merge(Array("-"=>" "), $arSProperty_LNS),
		),
		"META_DESCRIPTION" =>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_PXSD_DESC_DESCRIPTION"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => array_merge(Array("-"=>" "), $arSProperty_LNS),
		),
		"BROWSER_TITLE" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_PXSD_BROWSER_TITLE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"DEFAULT" => "-",
			"VALUES" => array_merge(Array("-"=>" ", "NAME" => GetMessage("CP_PXSD_SECTION_FIELD_NAME")), $arSProperty_LNS),
		),
		"ADD_SECTIONS_CHAIN" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_PXSD_ADD_SECTIONS_CHAIN"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y"
		),
		"GET_PATH" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_PXSD_GET_PATH"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"SET_TITLE" => Array(),
		"SET_STATUS_404" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CP_PXSD_SET_STATUS_404"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
	),
);
CIBlockParameters::AddPagerSettings($arComponentParameters, GetMessage("T_IBLOCK_DESC_PAGER_CATALOG"), true, true);

if ($arCurrentValues["ADD_SECTIONS_CHAIN"] == "Y") unset($arComponentParameters['PARAMETERS']['GET_PATH']);

$arComponentParameters['PARAMETERS']['SECTION_S_FIELDS']['REFRESH'] = "Y";

//Поля раздела для вывода
$bunsetuserffields = true;
if (count($arCurrentValues['SECTION_S_FIELDS'])) {	
	$arUserFieldsF = Array();
	foreach ($arCurrentValues['SECTION_S_FIELDS'] as $k=>$v) {
		if($v!=="" && $arSectionFields[$v]) {
			$arUserFieldsF[$v] = $arSectionFields[$v];
		}
	}
	if (count($arUserFieldsF)) {
		$bunsetuserffields = false;
		$arComponentParameters["PARAMETERS"]["SECTION_F_FIELDS"] = array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXSD_SECTION_F_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arUserFieldsF,
			"SORT"=>10
		);
	}
}

if (count($arCurrentValues['SECTION_F_FIELDS'])) {
	foreach (Array("PICTURE","DETAIL_PICTURE") as $key) {
		if (in_array($key,$arCurrentValues['SECTION_F_FIELDS'])) {
			$arComponentParameters["GROUPS"]["PXR_RG_".$key]['NAME'] = GetMessage("CP_PXSD_RESIZE_C")." ".$key;
			
			$arComponentParameters["PARAMETERS"]["PXR_".$key."_C"] = array(
				"PARENT" => "PXR_RG_".$key,
				"NAME" => GetMessage("CP_PXSD_RESIZE_C"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
				"REFRESH" => "Y"
			);
			if ($arCurrentValues["PXR_".$key."_C"] == "Y") {
				$arComponentParameters["PARAMETERS"]["PXR_".$key."_W"] = array(
					"PARENT" => "PXR_RG_".$key,
					"NAME" => GetMessage("CP_PXSD_RESIZE_WIDTH"),
					"TYPE" => "STRING",
				);
				$arComponentParameters["PARAMETERS"]["PXR_".$key."_H"] = array(
					"PARENT" => "PXR_RG_".$key,
					"NAME" => GetMessage("CP_PXSD_RESIZE_HEIGHT"),
					"TYPE" => "STRING",
				);
				$arComponentParameters["PARAMETERS"]["PXR_".$key."_RS"] = array(
					"PARENT" => "PXR_RG_".$key,
					"NAME" => GetMessage("CP_PXSD_RESIZE_RETSIZE"),
					"TYPE" => "CHECKBOX",
				);
				$arComponentParameters["PARAMETERS"]["PXR_".$key."_RT"] = array(
					"PARENT" => "PXR_RG_".$key,
					"NAME" => GetMessage("CP_PXSD_RESIZE_TYPE"),
					"TYPE" => "LIST",
					"VALUES" => $arResizeTypes,  
					"DEFAULT" => "BX_RESIZE_IMAGE_EXACT"
				);
			}		
		}
	}
}	

if ($bunsetuserffields === true) unset($arComponentParameters["PARAMETERS"]["SECTION_F_FIELDS"]);


//Свойства раздела для вывода
$bunsetuserfproperties = true;
if (count($arCurrentValues['SECTION_S_PROPERTIES'])) {
	$arUserPropertiesF = Array();
	foreach ($arCurrentValues['SECTION_S_PROPERTIES'] as $k=>$v) {
		if($v!=="" && $arProperty_UF[$v]) {
			$arUserPropertiesF[$v] = $arProperty_UF[$v];
		}
	}
	if (count($arUserPropertiesF)) {
		$bunsetuserfproperties = false;
		$arComponentParameters["PARAMETERS"]["SECTION_F_PROPERTIES"] = array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_PXSD_SECTION_F_FIELDS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arUserPropertiesF,
			"SORT"=>10
		);
	}
}

?>