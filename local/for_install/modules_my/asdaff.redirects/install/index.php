<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));


Class asdaff_redirects extends CModule
{
    const MODULE_ID = 'asdaff.redirects';
	var $MODULE_ID = "asdaff.redirects";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function asdaff_redirects()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("SEO2_REDIRECT_TITLE");
		$this->MODULE_DESCRIPTION = GetMessage("SEO2_REDIRECT_DESC");
		$this->PARTNER_NAME = 'ASDAFF';
		$this->PARTNER_URI = 'https://asdaff.github.io/';
	}

	function DoInstall()
	{
		global $DB, $DBType, $APPLICATION;
        
        $this->installDB();
		$APPLICATION->IncludeAdminFile(GetMessage("INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $this->MODULE_ID . "/install/install.php");
	}

	function DoUninstall()
	{
		global $DB, $DBType, $APPLICATION, $step;
		
		$step = IntVal($step);
		if($step<2)
			$APPLICATION->IncludeAdminFile(GetMessage("UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $this->MODULE_ID . "/install/uninstall1.php");
		elseif($step == 2) 
			$APPLICATION->IncludeAdminFile(GetMessage("UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $this->MODULE_ID . "/install/uninstall.php");
	}
	
	function GetModuleRightList()
	{
		$arr = array(
			"reference_id" => array("D","R","W","C","F"),
			"reference" => array(
				"[D] ".GetMessage("SEO2_REDIRECT_DENIED"),
				"[R] ".GetMessage("SEO2_REDIRECT_READ"),
				"[W] ".GetMessage("SEO2_REDIRECT_WRITE"),
				"[C] ".GetMessage("SEO2_REDIRECT_ALBUM_CREATING"),
				"[F] ".GetMessage("SEO2_REDIRECT_FULL")
			)
		);
		return $arr;
	}
    
    function installDB() {
        global $DBType, $APPLICATION;
        
        $node_id = strlen($arParams["DATABASE"]) > 0? intval($arParams["DATABASE"]): false;

		if($node_id !== false)
			$DB = $GLOBALS["DB"]->GetDBNodeConnection($node_id);
		else
			$DB = $GLOBALS["DB"];
        
        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/asdaff.redirects/install/db/".strtolower($DBType)."/install.sql");
        if($this->errors !== false) {
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
    }
}
?>