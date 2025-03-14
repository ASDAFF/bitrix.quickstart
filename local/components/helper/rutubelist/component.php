<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$module_message = false;
//$MODULE_ID = "helper.rutubelist";
//switch(CModule::IncludeModuleEx($MODULE_ID)) {
//    case 0: die(GetMessage("HELPER_RUTUBE_MODULE_NOT_INSTALL"));
//    case 1: break;
//    case 2: $module_message = GetMessage("HELPER_RUTUBE_MODULE_DEMO"); break;
//    case 3: die(GetMessage("HELPER_RUTUBE_MODULE_DEMO_EXPIRED"));
//}
if(!isset($arParams["CACHE_TIME"])) {
	$arParams["CACHE_TIME"] = 3600;
}

if($arParams["JQUERY_ON"]=="Y") {
	CUtil::InitJSCore(array('jquery'));
}

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";
if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
{
	$arNavParams = array(
		"nPageSize" => $arParams["VIDEO_COUNT"],
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
}
else
{
	$arNavParams = array(
		"nTopCount" => $arParams["VIDEO_COUNT"],
	);
	$arNavigation = false;
}



if($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()),$arNavigation)))
{


	if($arParams["TIP"] == "CHANNEL") {
           $arr=CImaginRutube::getRutubeChannelDataXml($arParams["PLAYLISTS_CODE"], $arParams["RUTUBE_VIDEO_COUNT"], $arParams["CACHE_TIME_OUT"]);
	} 
	if($arParams["TIP"] == "PLST") {
 			$arr=CImaginRutube::getRutubePlaylistDataXml($arParams["PLAYLISTS_CODE"], $arParams["RUTUBE_VIDEO_COUNT"], $arParams["CACHE_TIME_OUT"]);
	}
if (!empty($arr)) {
			$arr = array_slice($arr, 0, $arParams["RUTUBE_VIDEO_COUNT"]);
            if(is_array($arr) && count($arr)>0){
            $rs = new CDBResult;
            $rs->InitFromArray($arr);
            $rs->NavStart($arParams["VIDEO_COUNT"], $arParams["PAGER_SHOW_ALL"], false);
      		while($arVideo = $rs->GetNext())
    		{
    		  $arResult["VIDEO"][] = $arVideo;
            }
    		$arResult["NAV_STRING"] = $rs->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
    		$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
    		$arResult["NAV_RESULT"] = $rs;
        }
        else $arResult["VIDEO"]=array();

    $arResult["MESSAGE"] = $module_message;    
	$this->SetResultCacheKeys(array(
		"VIDEO",
        "NAV_CACHED_DATA"
	));
}
	$this->IncludeComponentTemplate();


}
?>