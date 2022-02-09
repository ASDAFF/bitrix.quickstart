<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("blog"))
{
	ShowError(GetMessage("BLOG_MODULE_NOT_INSTALL"));
	return;
}
if (!CModule::IncludeModule("socialnetwork"))
{
	ShowError(GetMessage("SONET_MODULE_NOT_INSTALL"));
	return;
}

$arParams["MESSAGE_COUNT"] = IntVal($arParams["MESSAGE_COUNT"])>0 ? IntVal($arParams["MESSAGE_COUNT"]): 10;

if ($arParams["CACHE_TYPE"] == "Y" || ($arParams["CACHE_TYPE"] == "A" && COption::GetOptionString("main", "component_cache_on", "Y") == "Y"))
	$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
else
	$arParams["CACHE_TIME"] = 0;	

if (strtolower($arParams["TYPE"]) == "rss1")
	$arResult["TYPE"] = "RSS .92";
if (strtolower($arParams["TYPE"]) == "rss2")
	$arResult["TYPE"] = "RSS 2.0";
if (strtolower($arParams["TYPE"]) == "atom")
	$arResult["TYPE"] = "Atom .03";

if(strLen($arParams["PAGE_VAR"])<=0)
	$arParams["PAGE_VAR"] = "page";
if(strLen($arParams["POST_VAR"])<=0)
	$arParams["POST_VAR"] = "id";
if(strLen($arParams["USER_VAR"])<=0)
	$arParams["USER_VAR"] = "id";
	
$arParams["GROUP_ID"] = IntVal($arParams["GROUP_ID"]);

$arParams["PATH_TO_POST"] = trim($arParams["PATH_TO_POST"]);
if(strlen($arParams["PATH_TO_POST"])<=0)
	$arParams["PATH_TO_POST"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=post&".$arParams["BLOG_VAR"]."=#blog#&".$arParams["POST_VAR"]."=#post_id#");

$arParams["PATH_TO_USER"] = trim($arParams["PATH_TO_USER"]);
if(strlen($arParams["PATH_TO_USER"])<=0)
	$arParams["PATH_TO_USER"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?".$arParams["PAGE_VAR"]."=user&".$arParams["USER_VAR"]."=#user_id#");
//----------------
$cacheSoNet = new CPHPCache;
$cache_idSoNet = "blog_sonet_".SITE_ID;
$cache_pathSoNet = "/".SITE_ID."/blog/sonet/";
$arAvBlog = Array();



	if(CModule::IncludeModule("socialnetwork") && IntVal($arParams["SOCNET_GROUP_ID"]) <= 0 && IntVal($arParams["USER_ID"]) <= 0)
	{
		unset($arFilter[">PERMS"]);
		$cacheSoNet = new CPHPCache;
		$cache_idSoNet = "blog_sonet_".SITE_ID;
		$cache_pathSoNet = "/".SITE_ID."/blog/sonet/";

		if ($arParams["CACHE_TIME"] > 0 && $cacheSoNet->InitCache($arParams["CACHE_TIME"], $cache_idSoNet, $cache_pathSoNet))
		{
			$Vars = $cacheSoNet->GetVars();
			$arAvBlog = $Vars["arAvBlog"];
			CBitrixComponentTemplate::ApplyCachedData($Vars["templateCachedData"]);	
			$cacheSoNet->Output();
		}
		else
		{
			if ($arParams["CACHE_TIME"] > 0)
				$cacheSoNet->StartDataCache($arParams["CACHE_TIME"], $cache_idSoNet, $cache_pathSoNet);

			$arAvBlog = Array();

			$arFilterTmp = Array("ACTIVE" => "Y", "GROUP_SITE_ID" => SITE_ID);
			if(IntVal($arParams["GROUP_ID"]) > 0)
				$arFilterTmp["GROUP_ID"] = $arParams["GROUP_ID"];
			
			$dbBlog = CBlog::GetList(Array(), $arFilterTmp);
			while($arBlog = $dbBlog->Fetch())
			{
				if(IntVal($arBlog["SOCNET_GROUP_ID"]) > 0)
				{
					$featureOperationPerms = CSocNetFeaturesPerms::GetOperationPerm(SONET_ENTITY_GROUP, $arBlog["SOCNET_GROUP_ID"], "blog", "view_post");
					if ($featureOperationPerms == SONET_ROLES_ALL)
						$arAvBlog[] = $arBlog["ID"];
				}
				else
				{
					$featureOperationPerms = CSocNetFeaturesPerms::GetOperationPerm(SONET_ENTITY_USER, $arBlog["OWNER_ID"], "blog", "view_post");
					if ($featureOperationPerms == SONET_RELATIONS_TYPE_ALL)
						$arAvBlog[] = $arBlog["ID"];
				}
			}
			if ($arParams["CACHE_TIME"] > 0)
				$cacheSoNet->EndDataCache(array("templateCachedData" => $this->GetTemplateCachedData(), "arAvBlog" => $arAvBlog));
		}
	}
	
//-------------------
$cache = new CPHPCache; 
$cache_id = "blog_rss_sonet_out_".serialize($arParams);
$cache_path = "/".SITE_ID."/blog/rss_sonet/".strtolower($arResult["TYPE"])."/";

$APPLICATION->RestartBuffer();
header("Content-Type: text/xml");
header("Pragma: no-cache");

if ($arParams["CACHE_TIME"] > 0 && $cache->InitCache($arParams["CACHE_TIME"], $cache_id, $cache_path))
{
	$cache->Output();
}
else
{
	if ($textRSS = CBlog::BuildRSSAll(0, $arResult["TYPE"], $arParams["MESSAGE_COUNT"], SITE_ID, $arParams["PATH_TO_POST"], $arParams["PATH_TO_USER"], $arAvBlog, Array("GROUP_BLOG_POST" => $arParams["PATH_TO_GROUP_BLOG_POST"], "BLOG_POST" => $arParams["PATH_TO_POST"])))
	{
		if ($arParams["CACHE_TIME"] > 0)
			$cache->StartDataCache($arParams["CACHE_TIME"], $cache_id, $cache_path);

		echo $textRSS;

		if ($arParams["CACHE_TIME"] > 0)
			$cache->EndDataCache(array());
	}
}
die();
?>