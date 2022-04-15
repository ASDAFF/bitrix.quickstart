<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$requiredModules = array('highloadblock');
foreach ($requiredModules as $requiredModule)
{
	if (!CModule::IncludeModule($requiredModule))
	{
		ShowError(GetMessage("F_NO_MODULE"));
		return 0;
	}
}

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

// hlblock info
$hlblock_id = $arParams['BLOCK_ID'];
if (empty($hlblock_id))
{
	ShowError(GetMessage('HLBLOCK_LIST_NO_ID'));
	return 0;
}
if (
    isset($arParams['FILTER_NAME']) &&
    !empty($arParams['FILTER_NAME']) &&
    preg_match('/^[A-Za-z_][A-Za-z01-9_]*$/', $arParams['FILTER_NAME']))
{
    if($_REQUEST['alf']){
        global ${$arParams['FILTER_NAME']};
        $filter = ${$arParams['FILTER_NAME']} = array( $arParams["FIELD_CODE"] => $_REQUEST['alf'].'%');
    }

    if(is_array($filter))
        $additionalCacheID = implode("", $filter);
}
if ($this->StartResultCache($arParams["CACHE_TIME"], $additionalCacheID, $cachePath = False))
{
    $hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
    if (empty($hlblock)) {
        ShowError(GetMessage('HLBLOCK_LIST_404'));
        return 0;
    }

// check rights
    if (isset($arParams['CHECK_PERMISSIONS']) && $arParams['CHECK_PERMISSIONS'] == 'Y' && !$USER->isAdmin()) {
        $operations = HL\HighloadBlockRightsTable::getOperationsName($hlblock_id);
        if (empty($operations)) {
            ShowError(GetMessage('HLBLOCK_LIST_404'));
            return 0;
        }
    }

    $entity = HL\HighloadBlockTable::compileEntity($hlblock);


// sort
    $sort_id = $arParams['FIELD_CODE'];
    $sort_type = $arParams['SORT_ORDER1'];

// start query
    $mainQuery = new Entity\Query($entity);

    // uf info
    $arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields('HLBLOCK_' . $arParams['BLOCK_ID']);

    $new_select[] = $arParams['FIELD_CODE'];

    $new_select[] = "ID";
    $mainQuery->setSelect($new_select);
    $mainQuery->setOrder(array($sort_id => $sort_type));

    $result = $mainQuery->exec();
    $result = new CDBResult($result);

// build results
    $rows = array();
    $tableColumns = array();
    while ($row = $result->fetch()) {
        $rows[] = $row;
    }

    $arResult['rows'] = $rows;
//    $arResult['fields'] = $fields;

    foreach ($arResult['rows'] as $key => $val){
        $alf[] = mb_substr($val[$arParams["FIELD_CODE"]], 0, 1);
    }

    // clear array
    $alf = array_filter($alf, function($element) {
        return !empty($element);
    });
    $arResult['alf'] = array_unique($alf);

    if($_REQUEST['alf'] && !empty($_REQUEST['alf']) && !in_array($_REQUEST['alf'], $arResult['alf']))
    $this->AbortResultCache();

$this->IncludeComponentTemplate();
}