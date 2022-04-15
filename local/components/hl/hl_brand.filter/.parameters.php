<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
$arSorts = array("ASC"=>GetMessage("HLLIST_COMPONENT_DESC_ASC"), "DESC"=>GetMessage("HLLIST_COMPONENT_DESC_DESC"));
$arSortFields = array(
    "ID"=>GetMessage("HLLIST_COMPONENT_DESC_FID"),
    "UF_NAME"=>GetMessage("HLLIST_COMPONENT_DESC_FNAME"),
    "UF_SORT"=>GetMessage("HLLIST_COMPONENT_DESC_FSORT"),
);
// list HL
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
    if($uf_field["USER_TYPE_ID"]=='string')
    $uf_fields[$uf_field["FIELD_NAME"]]="[".$uf_field["ID"]."] ".$uf_field['EDIT_FORM_LABEL'];
}
$arTemplateInfo = CComponentUtil::GetTemplatesList('bitrix:main.pagenavigation');
if (empty($arTemplateInfo))
{
    $arComponentParameters["PARAMETERS"]["PAGER_TEMPLATE"] = Array(
        "PARENT" => "PAGER_SETTINGS",
        "NAME" => Loc::getMessage("T_IBLOCK_DESC_PAGER_TEMPLATE"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    );
}
else {
    sortByColumn($arTemplateInfo, array('TEMPLATE' => SORT_ASC, 'NAME' => SORT_ASC));
    $arTemplateList = array();
    $arSiteTemplateList = array(
        '.default' => Loc::getMessage('T_IBLOCK_DESC_PAGER_TEMPLATE_SITE_DEFAULT'),
    );
    $arTemplateID = array();
    foreach ($arTemplateInfo as &$template) {
        if ('' != $template["TEMPLATE"] && '.default' != $template["TEMPLATE"])
            $arTemplateID[] = $template["TEMPLATE"];
        if (!isset($template['TITLE']))
            $template['TITLE'] = $template['NAME'];
    }
    unset($template);

    if (!empty($arTemplateID)) {
        $rsSiteTemplates = CSiteTemplate::GetList(
            array(),
            array("ID" => $arTemplateID),
            array()
        );
        while ($arSitetemplate = $rsSiteTemplates->Fetch()) {
            $arSiteTemplateList[$arSitetemplate['ID']] = $arSitetemplate['NAME'];
        }
    }

    foreach ($arTemplateInfo as &$template) {
        if (isset($arHiddenTemplates[$template['NAME']]))
            continue;
        $strDescr = $template["TITLE"] . ' (' . ('' != $template["TEMPLATE"] && '' != $arSiteTemplateList[$template["TEMPLATE"]] ? $arSiteTemplateList[$template["TEMPLATE"]] : Loc::getMessage("HLLIST_COMPONENT_PAGER_TEMPLATE_SYSTEM")) . ')';
        $arTemplateList[$template['NAME']] = $strDescr;
    }
    unset($template);
}

$arComponentParameters = array(
	'GROUPS' => array(
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
        "FIELD_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("BLOCK_PROPERTY"),
            "TYPE" => "LIST",
            "VALUES" => $uf_fields,
            "ADDITIONAL_VALUES" => "N",
        ),
        "SORT_ORDER1" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("HLLIST_COMPONENT_IBBY1"),
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
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
        "CACHE_TIME" => array('DEFAULT' => '360000',),
	),
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
