<?
IncludeModuleLangFile(__FILE__);

if(class_exists("tatonimus_abc")) return;

class tatonimus_abc extends CModule
{
	var $MODULE_ID = "tatonimus.abc";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	var $errors;

	function tatonimus_abc()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		
		$this->PARTNER_NAME = GetMessage("TABC_COMPANY_NAME");
		$this->PARTNER_URI = GetMessage("TABC_PARTNER_URI");
		$this->MODULE_NAME = GetMessage("TABC_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("TABC_INSTALL_DESCRIPTION");
	}
	
	function DoInstall()
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		
		$POST_RIGHT = $APPLICATION->GetGroupRight($this->MODULE_ID);

		if($POST_RIGHT == "W")
		{
			$step = intval($step);
			if($step<2)
			{
				$APPLICATION->IncludeAdminFile(GetMessage('TABC_INST_INSTALL_TITLE'),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
			}
			elseif($step==2)
			{
				$this->InstallDB();
				$this->InstallFiles();
				
				$APPLICATION->IncludeAdminFile(GetMessage('TABC_INST_INSTALL_TITLE'),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step2.php");
			}
		}
	}
	
	function DoUninstall()
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;

		$POST_RIGHT = $APPLICATION->GetGroupRight($this->MODULE_ID);
		if($POST_RIGHT == "W")
		{
			$step = IntVal($step);
			if($step<2)
			{
				$APPLICATION->IncludeAdminFile(GetMessage('TABC_INST_UNINSTA_TITLE'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php");
			}
			elseif($step==2)
			{
				$this->UnInstallDB(array(
					"save_tables" => $_REQUEST["save_tables"],
				));
				
				$this->UnInstallFiles($_REQUEST);
				
				$GLOBALS["errors"] = $this->errors;
				
				$APPLICATION->IncludeAdminFile(GetMessage('TABC_INST_UNINSTA_TITLE'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep2.php");
			}
		}
	}

	function InstallDB()
	{

		global $DB, $DBType, $APPLICATION;

		$this->errors = false;

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		else
		{
			RegisterModule($this->MODULE_ID);
			return true;
		}
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		UnRegisterModule($this->MODULE_ID);

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}

		return true;
	}


	function InstallFiles($arParams = array())
	{
		global $APPLICATION;
		if($_REQUEST["INSTALL_COMPONENTS"] == "Y")
		{
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", false, true);
		}
		if($_REQUEST["INSTALL_DEMO"] == "Y")
		{
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/{$this->MODULE_ID}/install/public", $_SERVER["DOCUMENT_ROOT"]."/tatonimus_abc", false, true);

            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/tatonimus_abc/index.php");
            $file = str_replace('#TABC_DEMO_TITLE_PAGE#', GetMessage('TABC_DEMO_TITLE_PAGE'), $file);
            file_put_contents($_SERVER["DOCUMENT_ROOT"]."/tatonimus_abc/index.php", $file);
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/tatonimus_abc/.section.php");
            $file = str_replace('#TABC_DEMO_TITLE_PAGE#', GetMessage('TABC_DEMO_TITLE_PAGE'), $file);
            file_put_contents($_SERVER["DOCUMENT_ROOT"]."/tatonimus_abc/.section.php", $file);
		}
		return true;
	}


	function UnInstallFiles()
	{
		//COMPONENTS
		if($_REQUEST["SAVE_COMPONENTS"] != "Y")
		{
			DeleteDirFilesEx("/bitrix/components/{$this->MODULE_ID}");
		}
		//delete demo public part
		if($_REQUEST["SAVE_DEMO"] != "Y")
		{
			DeleteDirFilesEx("/tatonimus_abc");
		}

		return true;
	}

}
?>