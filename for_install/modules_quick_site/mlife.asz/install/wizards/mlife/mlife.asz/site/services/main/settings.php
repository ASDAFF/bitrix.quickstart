<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

COption::SetOptionString("fileman", "propstypes", serialize(array(
	"description"=>GetMessage("MLIFE_ASZ_MAIN_OPT_DESCRIPTION"), 
	"keywords"=>GetMessage("MLIFE_ASZ_MAIN_OPT_KEYWORDS"), 
	"title"=>GetMessage("MLIFE_ASZ_MAIN_OPT_TITLE"), 
	"keywords_inner"=>GetMessage("MLIFE_ASZ_MAIN_OPT_KEYWORDS_INNER")
)), false, $siteID);
COption::SetOptionInt("search", "suggest_save_days", 250);
COption::SetOptionString("search", "use_tf_cache", "Y");
COption::SetOptionString("search", "use_word_distance", "Y");
COption::SetOptionString("search", "use_social_rating", "Y");
COption::SetOptionString("iblock", "use_htmledit", "Y");
COption::SetOptionString("main", "captcha_registration", "N");
?>