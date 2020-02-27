<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$arResult = array();

if ($this->StartResultCache()) {
	$this->IncludeComponentTemplate();
}