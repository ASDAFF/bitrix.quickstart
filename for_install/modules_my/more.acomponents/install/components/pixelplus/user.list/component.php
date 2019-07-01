<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if (!CModule::IncludeModule('more.acomponents')) return;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))	$arParams["CACHE_TIME"] = 36000000;

if(strlen($arParams["USER_SORT_FIELD"])<=0)
	$arParams["USER_SORT_FIELD"] = "ID";

if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["USER_SORT_ORDER"]))
	 $arParams["USER_SORT_ORDER"] = "asc";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
	$arrFilter = array();
} else {
	global $$arParams["FILTER_NAME"];
	$arrFilter = ${$arParams["FILTER_NAME"]};
	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);

$arParams["PAGE_ELEMENT_COUNT"] = intval($arParams["PAGE_ELEMENT_COUNT"]);
if($arParams["PAGE_ELEMENT_COUNT"]<=0)
	$arParams["PAGE_ELEMENT_COUNT"]=20;
$arParams["LINE_ELEMENT_COUNT"] = intval($arParams["LINE_ELEMENT_COUNT"]);
if($arParams["LINE_ELEMENT_COUNT"]<=0)
	$arParams["LINE_ELEMENT_COUNT"]=3;

//Format these UF_ after select
if(!is_array($arParams["USER_USER_S_PROPERTIES"]))
	$arParams["USER_USER_S_PROPERTIES"] = array();	
foreach($arParams["USER_USER_S_PROPERTIES"] as $k=>$v)
	if($v==="" || !preg_match("/^UF_/", $v))
		unset($arParams["USER_USER_S_PROPERTIES"][$k]);

$arCheckParamsArray = Array(
	"SELECT_USER_IN_GROUPS", //user groups filter
	"USER_USER_S_FIELDS",	//Select these fields from database
	"USER_USER_F_FIELDS",	//Format these fields after select
	"USER_USER_F_PROPERTIES", //Select these UF_ from database
	"FORUM_USER_FIELDS", //Format these Forum Fields
);


foreach ($arCheckParamsArray as $paramkey) {
	if(!is_array($arParams[$paramkey])) $arParams[$paramkey] = array();
	
	if ($paramkey == "USER_USER_S_FIELDS") {
		if (!in_array("ID",$arParams[$paramkey])) {
			$arParams[$paramkey][] = "ID";
		}
	}
	
	foreach($arParams[$paramkey] as $k=>$v) {
		if($v==="") {
			unset($arParams[$paramkey][$k]);
		} else {
			if ($paramkey == "USER_USER_F_FIELDS") {
				if (!in_array($v,$arParams['USER_USER_S_FIELDS'])) {
					unset($arParams[$paramkey][$k]);
				}
			}
			if ($paramkey == "USER_USER_F_PROPERTIES") {
				if (!in_array($v,$arParams['USER_USER_S_PROPERTIES'])) {
					unset($arParams[$paramkey][$k]);
				}
			}
		}
	}
}

	
$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

$arNavParams = array(
	"nPageSize" => $arParams["PAGE_ELEMENT_COUNT"],
	"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	"bShowAll" => $arParams["PAGER_SHOW_ALL"],
);
$arNavigation = CDBResult::GetNavParams($arNavParams);
if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
	$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];

$arParams["CACHE_FILTER"]=$arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;

	
/*************************************************************************
			Work with cache
*************************************************************************/
if($this->StartResultCache(false, array($arrFilter, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $arNavigation))) {
	
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/version.php");
	$btrueUserGetList = CheckVersion(SM_VERSION,"11.0.13");
	
	if ($btrueUserGetList === true) {
		$arOrder = Array(
			$arParams["USER_SORT_FIELD"]=>$arParams["USER_SORT_ORDER"],
			$arParams["USER_SORT_FIELD_2"]=>$arParams["USER_SORT_ORDER_2"]
		);
	} else {
		$arOrder = $arParams["USER_SORT_FIELD"];
		$dummy = $arParams["USER_SORT_ORDER"];
	}

	
	$arFilter = array(
		"ACTIVE"=>"Y"
	);
	
	if (count($arParams["SELECT_USER_IN_GROUPS"]) > 0) {
		$arFilter["GROUPS_ID"] = $arParams["SELECT_USER_IN_GROUPS"];
	}
	if ($btrueUserGetList === true) {
		$arAPIParams['FIELDS'] = $arParams['USER_USER_S_FIELDS'];
	}
	if (count($arParams["USER_USER_S_PROPERTIES"]) > 0) {
		$arAPIParams['SELECT'] = $arParams["USER_USER_S_PROPERTIES"];
	}
	
	$arAPIParams['NAV_PARAMS'] = $arNavParams;
	
	
	//EXECUTE	
	$rsUsers = CUser::GetList($arOrder,$dummy,array_merge($arrFilter, $arFilter), $arAPIParams);
	$arResult["ITEMS"] = array();
	
	//format UF fields
	$obpxformatuf = new CPPFormatUF;
	$obpxformatuf->Init("USER");		
	
	
	while($arUser = $rsUsers->GetNext()) {
		$arUser["DISPLAY_FIELDS"] = array();
		if (count($arParams["USER_USER_F_FIELDS"]) > 0) {
			foreach ($arParams["USER_USER_F_FIELDS"] as $fid) {
				if ($arUser[$fid]) {
					if ($fid == "PERSONAL_PHOTO") {
						$arUser[$fid] = CFile::GetFileArray($arUser[$fid]);
						$arUser["DISPLAY_FIELDS"][$fid] = '<img src="'.$arUser[$fid]['SRC'].'"/>';
					} elseif ($fid == "PERSONAL_GENDER") {
						$arUser["DISPLAY_FIELDS"][$fid] = getMessage('CP_PPUL_USER_SEX_'.$arUser[$fid]);
					} elseif ($fid == "ACTIVE") {
						if ($arUser[$fid] == "Y") {
							$arUser["DISPLAY_FIELDS"][$fid] = getMessage('CP_PPUL_USER_YES');
						} else {
							$arUser["DISPLAY_FIELDS"][$fid] = getMessage('CP_PPUL_USER_NO');
						}
					} else {
						$arUser["DISPLAY_FIELDS"][$fid] = $arUser[$fid];
					}
				}
			}
		}
		
		if (count($arParams["USER_USER_F_PROPERTIES"])) {
			$obpxformatuf->SetFormatted($arParams["USER_USER_F_PROPERTIES"]);
			$arResult['USER_FIELDS'] = $obpxformatuf->GetEntityMeta();
		}
		$obpxformatuf->GetDispayFields($arUser);
				
		if ($arParams['USE_FORUM'] == "Y" && CModule::IncludeModule('forum')) {
			$arUser["FORUM"] = array();
			$arUser["DISPLAY_FORUM"] = array();
			$res = CForumUser::GetList(Array(), array("USER_ID"=>$arUser['ID']));
			if ($arUser["FORUM"] = $res->GetNext()) {
				if (count($arParams["FORUM_USER_FIELDS"])) {
					foreach ($arParams["FORUM_USER_FIELDS"] as $fid) {
						if ($arUser["FORUM"][$fid]) {
							if ($fid == "AVATAR") {
								$arUser["FORUM"][$fid] = CFile::GetFileArray($arUser["FORUM"][$fid]);
								$arUser["DISPLAY_FORUM"][$fid] = '<img src="'.$arUser["FORUM"][$fid]['SRC'].'"/>';
							} else {
								$arUser["DISPLAY_FORUM"][$fid] = $arUser[$fid];
							}
						}
					}
				}
			}
		}
		
		$arResult["ITEMS"][]=$arUser;
		$arResult["ELEMENTS"][] = $arUser["ID"];
	}

	$arResult["NAV_STRING"] = $rsUsers->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
	$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
	$arResult["NAV_RESULT"] = $rsUsers;

	
	$this->SetResultCacheKeys(array(
		"NAV_CACHED_DATA"
	));
	$this->IncludeComponentTemplate(); 
}
$this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);

?>