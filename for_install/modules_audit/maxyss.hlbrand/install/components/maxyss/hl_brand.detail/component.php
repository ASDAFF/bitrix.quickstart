<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
$requiredModules = array('highloadblock');
foreach ($requiredModules as $requiredModule)
{
	if (!\Bitrix\Main\Loader::includeModule($requiredModule))
	{
		ShowError(GetMessage('F_NO_MODULE'));
		return 0;
	}
}
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

global $USER_FIELD_MANAGER;

$arResult['ERROR']  = '';

// hlblock info
$hlblock_id = $arParams['BLOCK_ID'];
// field for URL
$code = explode('#', $arParams['SEF_RULE']);
$code = explode('#', $code[1]);
$code_str = "#".$code[0]."#";

if($arParams["SEF_MODE"]=='Y') {
    $additionalCacheID = $_REQUEST[$code[0]];
}else{
    $additionalCacheID = array();
}
// clear the array of empty values
$new_select = array_filter($arParams["FIELD_CODE"], function ($element) {
    return !empty($element);
});

if(empty($new_select)){
    ShowError(GetMessage('HLBLOCK_LIST_NO_PROP'));
    return 0;
}
if(empty($hlblock_id)){
    ShowError(GetMessage('HLBLOCK_VIEW_NO_ID'));
    return 0;
}

if (!empty($hlblock_id) && !empty($new_select))
{
//	$arResult['ERROR'] = GetMessage('HLBLOCK_VIEW_NO_ID');
//}
//else {
    if ($this->StartResultCache(false, $additionalCacheID, false)) {
        $hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
        if (empty($hlblock)) {
            $arResult['ERROR'] = GetMessage('HLBLOCK_VIEW_404');
        }


// check rights
        if (isset($arParams['CHECK_PERMISSIONS']) && $arParams['CHECK_PERMISSIONS'] == 'Y' && !$USER->isAdmin()) {
            $operations = HL\HighloadBlockRightsTable::getOperationsName($hlblock_id);
            if (empty($operations)) {
                $arResult['ERROR'] = GetMessage('HLBLOCK_VIEW_404');
            }
        }

        if ($arResult['ERROR'] == '') {
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);

            if ((!isset($arParams['ROW_KEY']) || trim($arParams['ROW_KEY']) == '') && ($arParams['SEF_MODE' != "Y"])) {

                $arParams['ROW_KEY'] = 'ID';
            }
            // row data
            $main_query = new Entity\Query($entity);


            // clear the array of nonexistent values
            $arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields('HLBLOCK_' . $arParams['BLOCK_ID']);
            foreach ($arUserFields as $uf_field) {
                $uf_fields[] = $uf_field["FIELD_NAME"];
            }
            $new_select = array_intersect($new_select, $uf_fields);


            $new_select_display = $new_select;

            $new_select[] = $code[0];
            $new_select[] = "ID";
            $new_select[] = "UF_XML_ID";

            if ($arParams['TITLE_HL'] && $arParams["SET_TITLE_HL"] == "Y")
                $new_select[] = $arParams['TITLE_HL'];
            if ($arParams['BROWSER_TITLE'] && $arParams["SET_BROWSER_TITLE"] == "Y")
                $new_select[] = $arParams['BROWSER_TITLE'];
            if ($arParams['META_KEYWORDS'] && $arParams["SET_META_KEYWORDS"] == "Y")
                $new_select[] = $arParams['META_KEYWORDS'];
            if ($arParams['META_DESCRIPTION'] && $arParams["SET_META_DESCRIPTION"] == "Y")
                $new_select[] = $arParams['META_DESCRIPTION'];
            $new_select = array_unique($new_select);


            $new_select = array_filter($new_select, function ($element) {
                return !empty($element);
            });
            $main_query->setSelect($new_select);

            if ($arParams['SEF_MODE'] == "N") {
                $main_query->setFilter(array('=' . trim($arParams['ROW_KEY']) => $arParams['ROW_ID']));
            } else {
                 $main_query->setFilter(array('='.$code[0] => $_REQUEST[$code[0]]));
            }

            $result = $main_query->exec();
            $result = new CDBResult($result);
            $row = $result->Fetch();

            if (empty($row))
                $this->AbortResultCache();

            $fields = $USER_FIELD_MANAGER->getUserFieldsWithReadyData(
                'HLBLOCK_' . $hlblock['ID'],
                $row,
                LANGUAGE_ID
            );

            foreach ($new_select_display as $key => $value) {
                $row['DISPLAY'][$value]['NAME'] = $fields[$value]['LIST_COLUMN_LABEL'];
                $row['DISPLAY'][$value]['VALUE'] = $fields[$value]['VALUE'];
                $row['DISPLAY'][$value]['TYPE'] = $arUserFields[$value]['USER_TYPE']['BASE_TYPE'];
                $row['DISPLAY'][$value]['MULTIPLE'] = $fields[$value]['MULTIPLE'];
            }

            if (empty($row)) {
                $arResult['ERROR'] = GetMessage('HLBLOCK_VIEW_NO_ROW');
            }

            $arResult['fields'] = $fields;
            $arResult['row'] = $row;
            $arResult["META_TAGS"]["TITLE"] = $row[$arParams['TITLE_HL']];
            $arResult["META_TAGS"]["BROWSER_TITLE"] = $row[$arParams['BROWSER_TITLE']];
            $arResult["META_TAGS"]["KEYWORDS"] = $row[$arParams['META_KEYWORDS']];
            $arResult["META_TAGS"]["DESCRIPTION"] = $row[$arParams['META_DESCRIPTION']];

        }
        $this->IncludeComponentTemplate();
    }
}

if($arParams["SET_TITLE_HL"]==="Y")
    $APPLICATION->SetTitle($arResult["META_TAGS"]["TITLE"]);

if ($arParams["SET_BROWSER_TITLE"] === 'Y')
{
    if ($arResult["META_TAGS"]["BROWSER_TITLE"] !== '')
        $APPLICATION->SetPageProperty("title", $arResult["META_TAGS"]["BROWSER_TITLE"]);
}

if ($arParams["SET_META_KEYWORDS"] === 'Y')
{
    if ($arResult["META_TAGS"]["KEYWORDS"] !== '')
        $APPLICATION->SetPageProperty("keywords", $arResult["META_TAGS"]["KEYWORDS"]);
}

if ($arParams["SET_META_DESCRIPTION"] === 'Y')
{
    if ($arResult["META_TAGS"]["DESCRIPTION"] !== '')
        $APPLICATION->SetPageProperty("description", $arResult["META_TAGS"]["DESCRIPTION"]);
}
$arParams['FILTER_CODE'] = ($arParams['FILTER_CODE'])? $arParams['FILTER_CODE'] : 'BRAND_REF';
if($arResult['row']['UF_XML_ID'] && (isset($arParams['FILTER_NAME']) && !empty($arParams["FILTER_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))) {
    global ${$arParams['FILTER_NAME']};
    ${$arParams['FILTER_NAME']} = array('PROPERTY_'.$arParams['FILTER_CODE'] => $arResult['row']['UF_XML_ID']);
}
