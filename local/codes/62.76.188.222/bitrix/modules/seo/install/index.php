<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));

IncludeModuleLangFile($PathInstall."/install.php");

if(class_exists("seo")) return;

Class seo extends CModule
{
	var $MODULE_ID = "seo";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	function seo()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		else
		{
			$this->MODULE_VERSION = SEO_VERSION;
			$this->MODULE_VERSION_DATE = SEO_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("SEO_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SEO_MODULE_DESCRIPTION");
	}

	function DoInstall()
	{
		$this->InstallFiles();
		$this->InstallDB();
		$GLOBALS['APPLICATION']->IncludeAdminFile(GetMessage("SEO_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seo/install/step1.php"); 
	}
	
	function InstallDB()
	{
		global $DB, $APPLICATION;
	
		$this->errors = false;
		if(!$DB->Query("SELECT 'x' FROM b_seo_keywords", true))
			$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/seo/install/db/".strtolower($DB->type)."/install.sql");

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("", $this->errors));
			return false;
		} 
		
		RegisterModule("seo");
		
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seo/install/tasks/install.php"); 
		
		RegisterModuleDependences('main', 'OnPanelCreate', 'seo', 'CSeoEventHandlers', 'SeoOnPanelCreate');

		if (COption::GetOptionString('seo', 'searchers_list', '') == '' && CModule::IncludeModule('statistic'))
		{
			$arFilter = array('ACTIVE' => 'Y', 'NAME' => 'Google|MSN|Bing', 'NAME_EXACT_MATCH' => 'Y');
			if (COption::GetOptionString('main', 'vendor') == '1c_bitrix')
				$arFilter['NAME'] .= '|Yandex';
			
			$strSearchers = '';
			$dbRes = CSearcher::GetList($by = 's_id', $order = 'asc', $arFilter, $is_filtered);
			while ($arRes = $dbRes->Fetch())
			{
				$strSearchers .= ($strSearchers == '' ? '' : ',').$arRes['ID'];
			}
			
			COption::SetOptionString('seo', 'searchers_list', $strSearchers);
		}

		return true;
	}
	
	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seo/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seo/install/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seo/install/images/seo", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/seo", true);
		
		return true;
	}

	function InstallEvents()
	{
		return true;
	}
	
	function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION, $step;
		$step = IntVal($step);
		if($step<2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("SEO_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/seo/install/unstep1.php");
		}
		elseif($step==2)
		{
			$this->UnInstallDB(array(
				"savedata" => $_REQUEST["savedata"],
			));
			$this->UnInstallFiles();
			$APPLICATION->IncludeAdminFile(GetMessage("SEO_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/seo/install/unstep2.php");
		}
	}
	
	function UnInstallDB($arParams = Array())
	{
		global $APPLICATION, $DB, $errors;
		
		$this->errors = false;
		
		if (!$arParams['savedata'])
			$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/seo/install/db/".strtolower($DB->type)."/uninstall.sql");

		if(is_array($this->errors))
			$arSQLErrors = array_merge($arSQLErrors, $this->errors);

		if(!empty($arSQLErrors))
		{
			$this->errors = $arSQLErrors;
			$APPLICATION->ThrowException(implode("", $arSQLErrors));
			return false;
		} 
		
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seo/install/tasks/uninstall.php");
		
		UnRegisterModuleDependences('main', 'OnPanelCreate', 'seo');
		//COption::RemoveOption("seo");
		
		UnRegisterModule("seo");

		return true;
	}
	
	function UnInstallFiles($arParams = array())
	{
		global $DB;
		
		// Delete files
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seo/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seo/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/seo/install/images/seo", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/seo");
		
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}
	
	function GetModuleRightList()
	{
		global $MESS;
		$arr = array(
			"reference_id" => array("D","R","W"),
			"reference" => array(
				"[D] ".GetMessage("SEO_DENIED"),
				"[R] ".GetMessage("SEO_OPENED"),
				"[W] ".GetMessage("SEO_FULL"))
			);
		return $arr;
	}
}
?>