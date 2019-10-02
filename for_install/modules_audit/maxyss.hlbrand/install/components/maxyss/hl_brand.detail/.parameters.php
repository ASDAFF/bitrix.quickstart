<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
CModule::IncludeModule('highloadblock');
use Bitrix\Highloadblock as HL;

$hlblock = HL\HighloadBlockTable::getList()->fetchAll();
if(!$hlblock){
    throw new \Exception('[04072017.1331.1]');
}
foreach ($hlblock as $hllist)
{
    $hllists[$hllist["ID"]]="[".$hllist["ID"]."] ".$hllist["NAME"];
}
global $USER_FIELD_MANAGER;
$arUserFields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData(
    'HLBLOCK_'.(isset($arCurrentValues["BLOCK_ID"])?$arCurrentValues["BLOCK_ID"]:$arCurrentValues["ID"]),
    $row,
    LANGUAGE_ID
);
foreach ($arUserFields as $uf_field){
    $uf_fields[$uf_field["FIELD_NAME"]]="[".$uf_field["ID"]."] ".$uf_field['EDIT_FORM_LABEL'];
}


$arComponentParameters = array(
	'GROUPS' => array(
        'FILTER' => array(
            'NAME' => GetMessage('HLVIEW_COMPONENT_FILTER'),
            'SORT' => 210
        ),
	),
	'PARAMETERS' => array(
        "BLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage('HLLIST_COMPONENT_BLOCK_ID_PARAM'),
            "TYPE" => "LIST",
            "VALUES" => $hllists,
            "DEFAULT" => '={$_REQUEST["ID"]}',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ),
        'SEF_MODE' => array(),
        'SEF_RULE' => array(
            'VALUES' => array(
                "ELEMENT_CODE" => array(
                    "TEXT" => GetMessage("HLVIEW_COMPONENT_BLOCK_XML_ID"),
                    "TEMPLATE" => "#UF_XML_ID#",
                    "PARAMETER_LINK" => "UF_XML_ID",
                    "PARAMETER_VALUE" => '={$_REQUEST["UF_XML_ID"]}',
                ),
                "ELEMENT_ID" => array(
                    "TEXT" => GetMessage("HLVIEW_COMPONENT_BLOCK_ID"),
                    "TEMPLATE" => "#ID#",
                    "PARAMETER_LINK" => "ID",
                    "PARAMETER_VALUE" => '={$_REQUEST["ID"]}',
                ),
                "ELEMENT_NAME" => array(
                    "TEXT" => GetMessage("HLVIEW_COMPONENT_BLOCK_NAME"),
                    "TEMPLATE" => "#UF_NAME#",
                    "PARAMETER_LINK" => "UF_NAME",
                    "PARAMETER_VALUE" => '={$_REQUEST["UF_NAME"]}',
                ),
            ),
            'DEFAULT' => '/brand/#UF_XML_ID#/'
        ),
        'ROW_KEY' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('HLVIEW_COMPONENT_KEY_PARAM'),
			'TYPE' => 'TEXT',
			'DEFAULT' => 'ID'
		),
		'ROW_ID' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('HLVIEW_COMPONENT_ID_PARAM'),
			'TYPE' => 'TEXT',
			'DEFAULT' => '={$_REQUEST[\'ID\']}'
		),
		'LIST_URL' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('HLVIEW_COMPONENT_LIST_URL_PARAM'),
			'TYPE' => 'TEXT'
		),
        "FIELD_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("HLVIEW_COMPONENT_BLOCK_PROPERTY"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $uf_fields,
            "ADDITIONAL_VALUES" => "Y",
        ),
		'CHECK_PERMISSIONS' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('HLVIEW_COMPONENT_CHECK_PERMISSIONS_PARAM'),
			'TYPE' => 'CHECKBOX',
		),
        "SET_TITLE_HL" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("HLVIEW_COMPONENT_SET_TITLE"),
            "TYPE" => "CHECKBOX",
            "REFRESH" => "Y",
            "DEFAULT" => "N",
        ),
        "TITLE_HL" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("HLVIEW_COMPONENT_TITLE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "DEFAULT" => "-",
            "VALUES" => $uf_fields,
            "HIDDEN" => (isset($arCurrentValues['SET_TITLE_HL']) && $arCurrentValues['SET_TITLE_HL'] == 'N' ? 'Y' : 'N')
        ),
        "SET_BROWSER_TITLE" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("HLVIEW_COMPONENT_SET_BROWSER_TITLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y"
        ),
        "BROWSER_TITLE" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("HLVIEW_COMPONENT_BROWSER_TITLE"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "DEFAULT" => "-",
            "VALUES" => $uf_fields,
            "HIDDEN" => (isset($arCurrentValues['SET_BROWSER_TITLE']) && $arCurrentValues['SET_BROWSER_TITLE'] == 'N' ? 'Y' : 'N')
        ),
        "SET_META_KEYWORDS" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("HLVIEW_COMPONENT_SET_META_KEYWORDS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y",
        ),
        "META_KEYWORDS" =>array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("HLVIEW_COMPONENT_KEYWORDS"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "DEFAULT" => "-",
            "VALUES" => $uf_fields,
            "HIDDEN" => (isset($arCurrentValues['SET_META_KEYWORDS']) && $arCurrentValues['SET_META_KEYWORDS'] == 'N' ? 'Y' : 'N')
        ),
        "SET_META_DESCRIPTION" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("HLVIEW_COMPONENT_SET_META_DESCRIPTION"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
            "REFRESH" => "Y"
        ),
        "META_DESCRIPTION" =>array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("HLVIEW_COMPONENT_DESCRIPTION"),
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "DEFAULT" => "-",
            "VALUES" => $uf_fields,
            "HIDDEN" => (isset($arCurrentValues['SET_META_DESCRIPTION']) && $arCurrentValues['SET_META_DESCRIPTION'] == 'N' ? 'Y' : 'N')
        ),
        'FILTER_NAME' => array(
            'PARENT' => 'FILTER',
            'NAME' => GetMessage('HLVIEW_COMPONENT_FILTER_NAME_PARAM'),
            'TYPE' => 'TEXT',
            'DEFAULT' => 'brandFilter',
        ),
        'FILTER_CODE' => array(
            'PARENT' => 'FILTER',
            'NAME' => GetMessage('HLVIEW_COMPONENT_FILTER_CODE_PARAM'),
            'TYPE' => 'TEXT',
            'DEFAULT' => 'BRAND_REF',
        ),
        "CACHE_TIME" => array('DEFAULT' => '360000',),
	)
);