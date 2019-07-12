<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
    
	COption::SetOptionInt("search", "suggest_save_days", 250);
	COption::SetOptionInt("search", "use_tf_cache", "Y");
	
	COption::SetOptionInt("iblock", "use_htmledit", "Y");
?>