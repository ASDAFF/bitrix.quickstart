<?
IncludeModuleLangFile(__FILE__);

Class siteheart_siteheart extends CModule{
    
    var $MODULE_ID = "siteheart.siteheart";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function siteheart_siteheart(){

	$this->MODULE_ID = "siteheart.siteheart"; 
	$this->MODULE_NAME = GetMessage("SH_MODULE_NAME");
	$this->MODULE_DESCRIPTION = GetMessage("SH_MOD_DESCR");
	$this->PARTNER_NAME = 'siteheart';
	$this->PARTNER_URI = "http://siteheart.com";
	$arModuleVersion = array();
	$path = str_replace("\\", "/", __FILE__);
	$path = substr($path, 0, strlen($path) - strlen("/index.php"));
	include($path."/version.php");
	if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
	    $this->MODULE_VERSION = $arModuleVersion["VERSION"];
	    $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
	}

    }

    function DoInstall(){
	
	global $APPLICATION, $step;

	$step = IntVal($step);

	if (!$step){

	    $APPLICATION->IncludeAdminFile(
		GetMessage("SH_SETTINGS"),
		$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/siteheart.siteheart/install/step1.php"
	    );

	}else if ($step == 2){

	    $APPLICATION->IncludeAdminFile(
		GetMessage("SH_RESULT"),
		$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/siteheart.siteheart/install/step2.php"
	    );

	}
	
    }

    function DoUninstall(){
	
	UnRegisterModuleDependences("main", "OnPageStart", "siteheart.siteheart", "siteheartClass", "addScriptTag");
	COption::RemoveOption("siteheart.siteheart");
	UnRegisterModule("siteheart.siteheart");
	
    }
    
}
?>
