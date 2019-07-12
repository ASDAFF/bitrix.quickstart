<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

COption::SetOptionString("sale", "SHOP_SITE_".WIZARD_SITE_ID, WIZARD_SITE_ID);
COption::SetOptionString("fileman", "propstypes", serialize(array("description"=>GetMessage("MAIN_OPT_DESCRIPTION"), "keywords"=>GetMessage("MAIN_OPT_KEYWORDS"), "title"=>GetMessage("MAIN_OPT_TITLE"), "keywords_inner"=>GetMessage("MAIN_OPT_KEYWORDS_INNER"))), false, $siteID);
COption::SetOptionInt("search", "suggest_save_days", 250);
COption::SetOptionString("search", "use_tf_cache", "Y");
COption::SetOptionString("search", "use_word_distance", "Y");
COption::SetOptionString("search", "use_social_rating", "Y");
COption::SetOptionString("iblock", "use_htmledit", "Y");

COption::SetOptionString("main", "new_user_registration", "Y");
COption::SetOptionString("main", "captcha_registration", "Y");
COption::SetOptionString("main", "new_user_email_uniq_check", "Y");
COption::SetOptionString("main", "stable_versions_only", "Y");

//socialservices
if (COption::GetOptionString("socialservices", "auth_services") == "")
{

	$arServices = array(
		"VKontakte" => "Y",  
		"MyMailRu" => "N",
		"Twitter" => "Y",
		"Facebook" => "Y",
		"Livejournal" => "Y",
		"YandexOpenID" => "N",
		"Rambler" => "N",
		"MailRuOpenID" => "N",
		"Liveinternet" => "N",
		"Blogger" => "N",
		"OpenID" => "N",
		"LiveID" => "N",
	);
	COption::SetOptionString("socialservices", "auth_services", serialize($arServices));
}


?>