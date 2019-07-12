<?
IncludeModuleLangFile(__FILE__);

$MODULE_ID = basename(dirname(__FILE__));


require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$MODULE_ID.'/classes/general/CModuleOptions.php';
//require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$MODULE_ID.'/classes/general/Mobile_Detect.php';

Class CWebmechanicLanding 
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
			return;
	}
}
?>
