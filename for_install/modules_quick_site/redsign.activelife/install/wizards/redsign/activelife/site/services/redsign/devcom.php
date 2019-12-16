<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$module_id = 'redsign.devcom';

if($obModule = CModule::CreateModuleObject($module_id)){
	if(!$obModule->IsInstalled()){
		$obModule->InstallDB();
		$obModule->InstallEvents();
		$obModule->InstallOptions();
		$obModule->InstallFiles();
		$obModule->InstallPublic();
	}
}