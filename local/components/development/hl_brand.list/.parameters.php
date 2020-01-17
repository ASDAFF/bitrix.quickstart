<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$arSorts = array("ASC"=>GetMessage("HLLIST_COMPONENT_DESC_ASC"), "DESC"=>GetMessage("HLLIST_COMPONENT_DESC_DESC"));

// list HL-блоков
CModule::IncludeModule('highloadblock');
CModule::IncludeModule('iblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Localization\Loc ;


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
    array(),
    LANGUAGE_ID
);
foreach ($arUserFields as $uf_field){
    $uf_fields[$uf_field["FIELD_NAME"]]="[".$uf_field["ID"]."] ".$uf_field['EDIT_FORM_LABEL']."( ".$uf_field['FIELD_NAME']." )";

    if(($uf_field['USER_TYPE_ID'] == 'string' && $uf_field['MULTIPLE']=='N') || ($uf_field['USER_TYPE_ID'] == 'double' && $uf_field['MULTIPLE']=='N')){
        $arSortFields[$uf_field["FIELD_NAME"]]="[".$uf_field["ID"]."] ".$uf_field['EDIT_FORM_LABEL']."( ".$uf_field['FIELD_NAME']." )";
    }
}

$arComponentParameters = array(
	'GROUPS' => array(
        'SEF_GROUP' => array(
            'NAME' => GetMessage('SEF_GROUP'),
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
		'DETAIL_URL' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('HLLIST_COMPONENT_DETAIL_URL_PARAM'),
			'TYPE' => 'TEXT',
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
        "FIELD_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("BLOCK_PROPERTY"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $uf_fields,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_BY1" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("HLLIST_COMPONENT_IBORD1"),
            "TYPE" => "LIST",
            "DEFAULT" => "ID",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_ORDER1" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("HLLIST_COMPONENT_IBBY1"),
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ),
		'ROWS_PER_PAGE' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('HLLIST_COMPONENT_ROWS_PER_PAGE_PARAM'),
			'TYPE' => 'TEXT'
		),
		'FILTER_NAME' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('HLLIST_COMPONENT_FILTER_NAME_PARAM'),
			'TYPE' => 'TEXT',
            'DEFAULT' => 'brandFilter',
		),
		'CHECK_PERMISSIONS' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('HLLIST_COMPONENT_CHECK_PERMISSIONS_PARAM'),
			'TYPE' => 'CHECKBOX',
		),
        'SEF_MODE_HL' => array(
            'PARENT' => 'SEF_GROUP',
            'NAME' => GetMessage('HLLIST_COMPONENT_SEF_MODE_HL'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            "REFRESH" => "Y",
        ),
        "CACHE_TIME" => array('DEFAULT' => '360000',),
	),
);

CIBlockParameters::AddPagerSettings(
    $arComponentParameters,
    GetMessage("T_IBLOCK_DESC_PAGER_NEWS"), //$pager_title
    false, //$bDescNumbering
    true, //$bShowAllParam
    false, //$bBaseLink
    $arCurrentValues["PAGER_BASE_LINK_ENABLE"]==="Y" //$bBaseLinkEnabled
);
if ($arCurrentValues['SEF_MODE_HL']=='Y')
{
    $arComponentParameters['PARAMETERS']['SEF_MODE_PARAM'] = array(
        'NAME' => GetMessage('SEF_PARAMS'),
        "PARENT" => "SEF_GROUP",
        'TYPE' => 'STRING',
        'DEFAULT' => '/brand/#UF_XML_ID#/'
    );
}