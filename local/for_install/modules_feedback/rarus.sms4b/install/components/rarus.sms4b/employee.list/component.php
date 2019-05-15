<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('intranet')) return;

$bSoNet = CModule::IncludeModule('socialnetwork');

$arParams['FILTER_NAME'] = 
		(strlen($arParams["FILTER_NAME"])<=0 || !ereg("^[A-Za-z_][A-Za-z01-9_]*$", $arParams["FILTER_NAME"])) ? 
		'find_' : $arParams['FILTER_NAME'];

$arParams['USERS_PER_PAGE'] = intval($arParams['USERS_PER_PAGE']);
$arParams['USERS_PER_PAGE'] = $arParams['USERS_PER_PAGE'] > 0 ? $arParams['USERS_PER_PAGE'] : 10;
$arParams['NAV_TITLE'] = $arParams['NAV_TITLE'] ? $arParams['NAV_TITLE'] : GetMessage('INTR_ISL_PARAM_NAV_TITLE_DEFAULT');

InitBVar($arParams['FILTER_1C_USERS']);
InitBVar($arParams['FILTER_SECTION_CURONLY']);

InitBVar($arParams['SHOW_NAV_TOP']);
InitBVar($arParams['SHOW_NAV_BOTTOM']);

InitBVar($arParams['SHOW_UNFILTERED_LIST']);

$arParams['DETAIL_URL'] = COption::GetOptionString('intranet', 'search_user_url', '/user/#ID#/');

// prepare list filter
$arFilter = array('ACTIVE' => 'Y');

if ($arParams['FILTER_1C_USERS'] == 'Y')
	$arFilter['UF_1C'] = 1;

$cnt_start = count($arFilter);

if ($GLOBALS[$arParams['FILTER_NAME'].'_UF_DEPARTMENT'])
	$arFilter['UF_DEPARTMENT'] = 
		$arParams['FILTER_SECTION_CURONLY'] == 'N'
		? CIntranetUtils::GetIBlockSectionChildren($GLOBALS[$arParams['FILTER_NAME'].'_UF_DEPARTMENT'])
		: array($GLOBALS[$arParams['FILTER_NAME'].'_UF_DEPARTMENT']);

if ($GLOBALS[$arParams['FILTER_NAME'].'_POST'])
	$arFilter['WORK_POSITION'] = $GLOBALS[$arParams['FILTER_NAME'].'_POST'];
if ($GLOBALS[$arParams['FILTER_NAME'].'_EMAIL'])
	$arFilter['EMAIL'] = $GLOBALS[$arParams['FILTER_NAME'].'_EMAIL'];

if ($GLOBALS[$arParams['FILTER_NAME'].'_FIO'])
	$arFilter['NAME'] = $GLOBALS[$arParams['FILTER_NAME'].'_FIO'];

if ($GLOBALS[$arParams['FILTER_NAME'].'_KEYWORDS'])
	$arFilter['KEYWORDS'] = $GLOBALS[$arParams['FILTER_NAME'].'_KEYWORDS'];

if ($GLOBALS[$arParams['FILTER_NAME'].'_LAST_NAME'])
{
	$arFilter['LAST_NAME'] = $GLOBALS[$arParams['FILTER_NAME'].'_LAST_NAME'];
	$arFilter['LAST_NAME_EXACT_MATCH'] = 'Y';
}

if ($arParams['SHOW_UNFILTERED_LIST'] == 'N' && !$bExcel && $cnt_start == count($arFilter))
{
	$arResult['EMPTY_UNFILTERED_LIST'] = 'Y';
	$this->IncludeComponentTemplate();
	return;
}

$arResult['FILTER_VALUES'] = $arFilter;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

//get users list
$obUser = new CUser();

if ($arParams['HIDE_USERS'] == 'Y')
{
	$defUserProperty = COption::GetOptionString('rarus.sms4b', 'user_property_phone', '', SITE_ID);
	$arFilter[$defUserProperty] = true;
}
$dbUsers = $obUser->GetList(($sort_by = 'last_name'), ($sort_dir = 'asc'), $arFilter, array('SELECT' => array('UF_*')));
$dbUsers->NavStart($arParams['USERS_PER_PAGE'],false);

$arResult['USERS'] = array();
$arDepCache = array();
$arDepCacheValue = array();

while ($arUser = $dbUsers->Fetch())
{
	if ($arUser['PERSONAL_PHOTO'])
	{
		$arUser['PERSONAL_PHOTO'] = CFile::GetPath($arUser['PERSONAL_PHOTO']);
	}
	
	$arDep = array();
	if (is_array($arUser['UF_DEPARTMENT']) && count($arUser['UF_DEPARTMENT']) > 0)
	{
		$arNewDep = array_diff($arUser['UF_DEPARTMENT'], $arDepCache);
		
		if (count($arNewDep) > 0)
		{
			$dbRes = CIBlockSection::GetList(array('SORT' => 'ASC'), array('ID' => $arNewDep));
			while ($arSect = $dbRes->Fetch())
			{
				$arDepCache[] = $arSect['ID']; 
				$arDepCacheValue[$arSect['ID']] = $arSect['NAME'];
			}
		}
		
		foreach ($arUser['UF_DEPARTMENT'] as $key => $sect)
		{
			$arDep[$sect] = $arDepCacheValue[$sect];
		}
	}
	
	$arUser['UF_DEPARTMENT'] = $arDep;
	$arResult['USERS'][] = $arUser;
}


$arResult["USERS_NAV"] = $dbUsers->GetPageNavStringEx($navComponentObject, $arParams["NAV_TITLE"]);

$this->IncludeComponentTemplate();
?>