<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (WIZARD_IS_RERUN)
	return;

if(!CModule::IncludeModule("fileman"))
	return;

$APPLICATION->SetGroupRight("fileman", WIZARD_EDITORS_GROUP, "F");

COption::SetOptionString('fileman', 'default_edit_groups', WIZARD_EDITORS_GROUP);
?>