<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

if(class_exists("elipseart_siteposition")) return;
Class elipseart_siteposition extends CModule
{
	var $MODULE_ID = "elipseart.siteposition";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function elipseart_siteposition()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("SPER_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SPER_INSTALL_DESCRIPTION");
		
		$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
		$this->PARTNER_URI = "http://www.elipseart.ru/";
	}

	function InstallDB($install_wizard = true)
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		if(!$DB->Query("SELECT 'x' FROM b_ea_siteposition_position", true))
			$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/elipseart.siteposition/install/db/".$DBType."/install.sql");

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("", $this->errors));
			return false;
		}
		
		RegisterModule("elipseart.siteposition");
		
		CAgent::AddAgent("CEASitePositionUpdate::Update();", "elipseart.siteposition", "N", 600);
		
		if(require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/elipseart.siteposition/include.php"))
		{
			CEASitePositionRegion::Update();
			CEASitePositionHost::UpdateSiteHost();
			CEASitePositionSearchSystem::UpdateSearchSystem();
		}
		
		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		
		if(!array_key_exists("savedata", $arParams) || ($arParams["savedata"] != "Y"))
			$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/elipseart.siteposition/install/db/".$DBType."/uninstall.sql");
		
		if(!array_key_exists("saveoption", $arParams) || ($arParams["saveoption"] != "Y"))
			COption::RemoveOption("elipseart.siteposition");
		
		CAgent::RemoveAgent("CEASitePositionUpdate::Update();","elipseart.siteposition");
		UnRegisterModule("elipseart.siteposition");
		
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/elipseart.siteposition/install/admin/",
			$_SERVER["DOCUMENT_ROOT"].BX_ROOT."/admin/",
			true, true
		);
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/elipseart.siteposition/install/public/bitrix/",
			$_SERVER["DOCUMENT_ROOT"].BX_ROOT."/",
			true, true
		);
		
		return true;
	}

	function UnInstallFiles($arParams = Array())
	{
		global $DB, $APPLICATION;
		
		DeleteDirFilesEx(BX_ROOT."/admin/elipseart.siteposition.position.php");
		DeleteDirFilesEx(BX_ROOT."/admin/elipseart.siteposition.position_dynamic_graph.php");
		DeleteDirFilesEx(BX_ROOT."/admin/elipseart.siteposition.keyword.php");
		DeleteDirFilesEx(BX_ROOT."/admin/elipseart.siteposition.keyword_edit.php");
		DeleteDirFilesEx(BX_ROOT."/admin/elipseart.siteposition.stat.php");
		DeleteDirFilesEx(BX_ROOT."/admin/elipseart.siteposition.stat_dynamic_graph.php");
		
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/install/public/bitrix/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
		DeleteDirFilesEx("/bitrix/themes/.default/icons/elipseart.siteposition/");
		DeleteDirFilesEx("/bitrix/images/elipseart.siteposition/");
		DeleteDirFilesEx("/bitrix/js/elipseart.siteposition/");
		
		return true;
	}

	function DoInstall()
	{
		global $DB, $APPLICATION, $step, $DBType;
		
		if($DBType == "mysql")
		{
			$this->InstallFiles();
			$this->InstallDB(false);
			$this->InstallEvents();
			$GLOBALS["errors"] = $this->errors;
			
			$APPLICATION->IncludeAdminFile(GetMessage("SPER_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/elipseart.siteposition/install/step.php");
		}
		else
		{
				$APPLICATION->IncludeAdminFile(GetMessage("SPER_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/elipseart.iblockform/install/step1.php");
		}
	}

	function DoUninstall()
	{
		global $DB, $APPLICATION, $step, $obModule;
		$step = IntVal($step);

		if($step < 2)
			$APPLICATION->IncludeAdminFile(GetMessage("SPER_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/install/unstep1.php");
		elseif($step == 2)
		{
			
			$this->UnInstallDB(array(
				"savedata" => $_REQUEST["savedata"],
				"saveoption" => $_REQUEST["saveoption"],
			));
			$this->UnInstallFiles(array(
				"savedata" => $_REQUEST["savedata"],
				"saveoption" => $_REQUEST["saveoption"],
			));
			$this->UnInstallEvents();
			$obModule = $this;
			$APPLICATION->IncludeAdminFile(GetMessage("SPER_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/elipseart.siteposition/install/unstep2.php");
			
		}
	}

	function GetModuleRightList()
	{ 
		$arr = array( 
			"reference_id" => array("D","R","W"), 
			"reference" => array( 
				GetMessage("RIGHT_D"), 
				GetMessage("RIGHT_R"), 
				GetMessage("RIGHT_W"), 
			),
		); 
		return $arr; 
	}


}
?>