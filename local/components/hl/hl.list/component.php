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
$additionalCacheID=array();

// hlblock info
$hlblock_id = $arParams['BLOCK_ID'];
if (empty($hlblock_id))
{
	ShowError(GetMessage('HLBLOCK_LIST_NO_ID'));
	return 0;
}
// clear the array of empty values
$new_select = array_filter($arParams["FIELD_CODE"], function($element) {
    return !empty($element);
});
if(empty($new_select))
{
    ShowError(GetMessage('HLBLOCK_LIST_NO_PROP'));
    return 0;
}

//pager


$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]=="Y";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]=="Y";

$arParams["CHECK_PERMISSIONS"] = $arParams["CHECK_PERMISSIONS"]!="N";

if (isset($arParams['ROWS_PER_PAGE']) && $arParams['ROWS_PER_PAGE']>0)
{
    $perPage = intval($arParams['ROWS_PER_PAGE']);
}
else
{
    $arParams['ROWS_PER_PAGE'] = 0;
}

if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
    $arNavParams = array(
        "nPageSize" => $arParams['ROWS_PER_PAGE'],
        "bShowAll" => $arParams["PAGER_SHOW_ALL"],
    );
    $arNavigation = CDBResult::GetNavParams($arNavParams);
}
else
{
    $arNavParams = array(
        "nTopCount" => $arParams['ROWS_PER_PAGE'],
    );
    $arNavigation = false;
}

if (empty($arParams["PAGER_PARAMS_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PAGER_PARAMS_NAME"]))
{
    $pagerParameters = array();
}
else
{
    $pagerParameters = $GLOBALS[$arParams["PAGER_PARAMS_NAME"]];
    if (!is_array($pagerParameters))
        $pagerParameters = array();
}
//pager

$FilterCacheID = array();
if (
    isset($arParams['FILTER_NAME']) &&
    !empty($arParams['FILTER_NAME']) &&
    preg_match('/^[A-Za-z_][A-Za-z01-9_]*$/', $arParams['FILTER_NAME']))
{
    global ${$arParams['FILTER_NAME']};
    $filter = ${$arParams['FILTER_NAME']};

    if(is_array($filter))
        $FilterCacheID = $filter;
}

$CacheID = array_merge($arNavigation, $FilterCacheID, $pagerParameters);
$additionalCacheID = implode("", $CacheID);
if ($this->StartResultCache($arParams["CACHE_TIME"], $additionalCacheID, $cachePath = False))
{
        // Requesting data and filling $arResult

    $hlblock = HL\HighloadBlockTable::getById($hlblock_id)->fetch();
    if (empty($hlblock))
    {
        ShowError(GetMessage('HLBLOCK_LIST_404'));
        return 0;
    }

    // check rights
    if (isset($arParams['CHECK_PERMISSIONS']) && $arParams['CHECK_PERMISSIONS'] == 'Y' && !$USER->isAdmin())
    {
        $operations = HL\HighloadBlockRightsTable::getOperationsName($hlblock_id);
        if (empty($operations))
        {
            ShowError(GetMessage('HLBLOCK_LIST_404'));
            return 0;
        }
    }

    $entity = HL\HighloadBlockTable::compileEntity($hlblock);

    // uf info
    $fields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('HLBLOCK_'.$hlblock['ID'], 0, LANGUAGE_ID);

    // sort
    $sort_id = $arParams['SORT_BY1'];
    $sort_type = $arParams['SORT_ORDER1'];
    if (!empty($_GET['sort_id']) && (isset($fields[$_GET['sort_id']])))
    {
        $sort_id = $_GET['sort_id'];
    }
    if (!empty($_GET['sort_type']) && in_array($_GET['sort_type'], array('ASC', 'DESC'), true))
    {
        $sort_type = $_GET['sort_type'];
    }

    // start query
    $mainQuery = new Entity\Query($entity);

    // clear the array of nonexistent value
    $arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields('HLBLOCK_'.$arParams['BLOCK_ID']);
    foreach ($arUserFields as $uf_field){
        $uf_fields[]=$uf_field["FIELD_NAME"];
    }
    $new_select = array_intersect($new_select, $uf_fields);

    // field for URL
    $code = explode('#', $arParams['SEF_MODE_PARAM']);
    $code = explode('#', $code[1]);
    $code_str = "#".$code[0]."#";
    $new_select_display = $new_select;

    $new_select[] = $code[0];
    $new_select[] = "ID";
    $new_select[] = $arParams['SORT_BY1'];
    $new_select = array_unique($new_select);

    if(!empty($arParams['ROW_ID']))
    $new_select[] = $arParams['ROW_ID'];
    $new_select = array_filter($new_select, function($element) {
        return !empty($element);
    });

    $mainQuery->setSelect($new_select);
    $mainQuery->setOrder(array($sort_id => $sort_type));

        if (is_array($filter))
        {
            $mainQuery->setFilter($filter);
        }

    $result = $mainQuery->exec();
    $result = new CDBResult($result);
    if($perPage) {
        $result->NavStart($perPage, $arParams["PAGER_SHOW_ALL"]);
        $result->nPageWindow = 5;
        $arResult['NAV_STRING'] = $result->GetPageNavStringEx(
            $navComponentObject,
            $arParams["PAGER_TITLE"],
            $arParams["PAGER_TEMPLATE"],
            $arParams["PAGER_SHOW_ALWAYS"],
            $this
        );
        $arResult["NAV_CACHED_DATA"] = null;
        $arResult["NAV_RESULT"] = $result;
    }
    // build results
    $rows = array();
    $flag_cach=false;

    while ($row = $result->fetch())
    {
       $flag_cach=true;
        foreach ($new_select_display as $value){
            $row['DISPLAY'][$value]['VALUE'] = $row[$value];
            $row['DISPLAY'][$value]['TYPE'] = $arUserFields[$value]['USER_TYPE']['BASE_TYPE'];

        }
        if($arParams['SEF_MODE_HL']=='Y'){
            $row['DETAIL_URL'] = str_replace($code_str, $row[$code[0]], $arParams['SEF_MODE_PARAM']);
        }else{
            $row['DETAIL_URL'] = $arParams['DETAIL_URL'].'?'.$arParams['ROW_KEY'].'='.$row[$arParams['ROW_ID']];
        }
        $rows[] = $row;
    }
    if ($flag_cach===false) { $this->AbortResultCache(); }
    $arResult['rows'] = $rows;
    $arResult['fields'] = $fields;
    $arResult['sort_id'] = $sort_id;
    $arResult['sort_type'] = $sort_type;

$this->IncludeComponentTemplate();
}
