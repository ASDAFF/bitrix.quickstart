<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

COption::SetOptionString("main", "captcha_registration", "N");
COption::SetOptionString("iblock", "use_htmledit", "Y");
COption::SetOptionString("sale", "SHOP_SITE_".WIZARD_SITE_ID, WIZARD_SITE_ID);
COption::SetOptionString("fileman", "propstypes", serialize(array("description"=>GetMessage("MAIN_OPT_DESCRIPTION"), "keywords"=>GetMessage("MAIN_OPT_KEYWORDS"), "title"=>GetMessage("MAIN_OPT_TITLE"), "keywords_inner"=>GetMessage("MAIN_OPT_KEYWORDS_INNER"), "viewed_show"=>GetMessage("MAIN_OPT_VIEWED_SHOW"))), false, WIZARD_SITE_ID);
COption::SetOptionInt("search", "suggest_save_days", 250);
COption::SetOptionString("search", "use_tf_cache", "Y");
COption::SetOptionString("search", "use_word_distance", "Y");
COption::SetOptionString("search", "use_social_rating", "Y");

// social auth services
if (COption::GetOptionString("socialservices", "auth_services") == ""){
	$bRu = (LANGUAGE_ID == 'ru');
	$arServices = array(
		"VKontakte" => "Y",  
		"MyMailRu" => "Y",
		"Twitter" => "Y",
		"Facebook" => "Y",
		"Livejournal" => "Y",
		"YandexOpenID" => ($bRu? "Y":"N"),
		"Rambler" => ($bRu? "Y":"N"),
		"MailRuOpenID" => ($bRu? "Y":"N"),
		"Liveinternet" => ($bRu? "Y":"N"),
		"Blogger" => "N",
		"OpenID" => "Y",
		"LiveID" => "N",
	);
	COption::SetOptionString("socialservices", "auth_services", serialize($arServices));
}

COption::SetOptionString("socialservices", "auth_services", serialize($arServices));
COption::SetOptionString("aspro.optimus", "WIZARD_SITE_ID", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "SITE_INSTALLED", "Y", GetMessage("SHOP_INSTALLED"), WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "USE_FILTERS", "Y", "", WIZARD_SITE_ID);

// subscribe to products - set active notify for sites
$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);
if($arNotify){
	foreach($arNotify as $siteID => $notify){
		if($siteID == WIZARD_SITE_ID){
			$arNotify[$siteID]['use'] = 'Y';
			$arNotify[$siteID]['del_after'] = $arNotify[$siteID]['del_after'] > 0 ? $arNotify[$siteID]['del_after'] : 30;
		}
	}
	COption::SetOptionString("sale", "subscribe_prod", serialize($arNotify), "");
}

// get DB charset
$sql='SHOW VARIABLES LIKE "character_set_database";';
if(method_exists('\Bitrix\Main\Application', 'getConnection')){
	$db=\Bitrix\Main\Application::getConnection();
	$arResult = $db->query($sql)->fetch();
	$isUTF8 = $arResult['Value'] == 'utf8';
}elseif(defined("BX_USE_MYSQLI") && BX_USE_MYSQLI === true){
	if($result = @mysqli_query($sql)){
		$arResult = mysql_fetch_row($result);
		$isUTF8 = $arResult[1] == 'utf8';
	}
}elseif($result = @mysql_query($sql)){
	$arResult = mysql_fetch_row($result);
	$isUTF8 = $arResult[1] == 'utf8';
}

// new options
COption::SetOptionString("aspro.optimus", "BASKET", "FLY", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "STORES_SOURCE", "IBLOCK", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "TYPE_SKU", "TYPE_1", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "MAX_DEPTH_MENU", "4", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "TYPE_VIEW_FILTER", "HORIZONTAL", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "SHOW_BASKET_ONADDTOCART", "Y", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "USE_PRODUCT_QUANTITY_LIST", "Y", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "USE_PRODUCT_QUANTITY_DETAIL", "Y", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "BUYNOPRICEGGOODS", "NOTHING", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "BUYMISSINGGOODS", "ADD", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "EXPRESSION_ORDER_BUTTON", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Под заказ') : 'Под заказ'), "", WIZARD_SITE_ID);

$DefaultGroupID = 0;
$rsGroups = CGroup::GetList($by = "id", $order = "asc", array("ACTIVE" => "Y"));
while($arItem = $rsGroups->Fetch()){
	if($arItem["ANONYMOUS"] == "Y"){
		$DefaultGroupID = $arItem["ID"];
		break;
	}
}

COption::SetOptionString("aspro.optimus", "SHOW_QUANTITY_FOR_GROUPS", $DefaultGroupID, "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "SHOW_QUANTITY_COUNT_FOR_GROUPS", $DefaultGroupID, "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "EXPRESSION_FOR_EXISTS", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Есть в наличии') : 'Есть в наличии'), "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "EXPRESSION_FOR_NOTEXISTS", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Нет в наличии') : 'Нет в наличии'), "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "USE_WORD_EXPRESSION", "Y", "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "MAX_AMOUNT", 10, "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "MIN_AMOUNT", 2, "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "EXPRESSION_FOR_MIN", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Мало') : 'Мало'), "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "EXPRESSION_FOR_MID", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Достаточно') : 'Достаточно'), "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "EXPRESSION_FOR_MAX", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Много') : 'Много'), "", WIZARD_SITE_ID);
COption::SetOptionString("aspro.optimus", "MIN_ORDER_PRICE_TEXT", ($isUTF8 ? iconv('CP1251', 'UTF-8', '<b>Минимальная сумма заказа #PRICE#</b><br/>Пожалуйста, добавьте еще товаров в корзину') : '<b>Минимальная сумма заказа #PRICE#</b><br/>Пожалуйста, добавьте еще товаров в корзину'), "", WIZARD_SITE_ID);

// enable composite
if(class_exists("CHTMLPagesCache")){
	if(method_exists("CHTMLPagesCache", "GetOptions")){
		if($arHTMLCacheOptions = CHTMLPagesCache::GetOptions()){
			if($arHTMLCacheOptions["COMPOSITE"] !== "Y"){
				$arDomains = array();
				
				$arSites = array();
				$dbRes = CSite::GetList($by="sort", $order="desc", array("ACTIVE" => "Y"));
				while($item = $dbRes->Fetch()){
					$arSites[$item["LID"]] = $item;
				}
				
				if($arSites){
					foreach($arSites as $arSite){
						if(strlen($serverName = trim($arSite["SERVER_NAME"], " \t\n\r"))){
							$arDomains[$serverName] = $serverName;
						}
						if(strlen($arSite["DOMAINS"])){
							foreach(explode("\n", $arSite["DOMAINS"]) as $domain){
								if(strlen($domain = trim($domain, " \t\n\r"))){
									$arDomains[$domain] = $domain;
								}
							}
						}
					}
				}
				
				if(!$arDomains){
					$arDomains[$_SERVER["SERVER_NAME"]] = $_SERVER["SERVER_NAME"];
				}
				
				if(!$arHTMLCacheOptions["GROUPS"]){
					$arHTMLCacheOptions["GROUPS"] = array();
				}
				$rsGroups = CGroup::GetList(($by="id"), ($order="asc"), array());
				while($arGroup = $rsGroups->Fetch()){
					if($arGroup["ID"] > 2){
						if(in_array($arGroup["STRING_ID"], array("RATING_VOTE_AUTHORITY", "RATING_VOTE")) && !in_array($arGroup["ID"], $arHTMLCacheOptions["GROUPS"])){
							$arHTMLCacheOptions["GROUPS"][] = $arGroup["ID"];
						}
					}
				}
				
				$arHTMLCacheOptions["COMPOSITE"] = "Y";
				$arHTMLCacheOptions["DOMAINS"] = array_merge((array)$arHTMLCacheOptions["DOMAINS"], (array)$arDomains);
				CHTMLPagesCache::SetEnabled(true);
				CHTMLPagesCache::SetOptions($arHTMLCacheOptions);
				bx_accelerator_reset();
			}
		}
	}
}
?>