<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (CModule::IncludeModule("search"))
	CSearch::ReIndexAll(Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));
