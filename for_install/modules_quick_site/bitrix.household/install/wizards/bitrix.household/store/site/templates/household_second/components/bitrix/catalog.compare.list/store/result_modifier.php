<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if ($_REQUEST['ajax_compare'])
	$GLOBALS['APPLICATION']->RestartBuffer();
else
	ob_start();
?>