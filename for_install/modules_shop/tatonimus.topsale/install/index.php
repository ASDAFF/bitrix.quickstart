<?
IncludeModuleLangFile(__FILE__);

if(class_exists("tatonimus_topsale")) return;

class tatonimus_topsale extends CModule
{
	var $MODULE_ID = "tatonimus.topsale";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	var $errors;

	function tatonimus_topsale()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		
		$this->PARTNER_NAME = GetMessage("TTSML_COMPANY_NAME");
		$this->PARTNER_URI = GetMessage("TTSML_PARTNER_URI");
		$this->MODULE_NAME = GetMessage("TTSML_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("TTSML_INSTALL_DESCRIPTION");
	}
	
	function DoInstall()
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		
		$POST_RIGHT = $APPLICATION->GetGroupRight($this->MODULE_ID);

		if($POST_RIGHT == "W")
		{
            $this->InstallDB();

            $agentID = CAgent::AddAgent(
                'CTopsale::AgentRefresh();',
                $this->MODULE_ID,
                'Y',
                86400,
                '',
                'N'
            );

            $APPLICATION->IncludeAdminFile(GetMessage('TTSML_INST_INSTALL_TITLE'),$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step2.php");
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
				$APPLICATION->IncludeAdminFile(GetMessage('TTSML_INST_UNINSTA_TITLE'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php");
			}
			elseif($step==2)
			{
				$this->UnInstallDB(array(
					"save_tables" => $_REQUEST["save_tables"],
				));

                CAgent::RemoveModuleAgents($this->MODULE_ID);

				$GLOBALS["errors"] = $this->errors;
				
				$APPLICATION->IncludeAdminFile(GetMessage('TTSML_INST_UNINSTA_TITLE'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep2.php");
			}
		}
	}

	function InstallDB($arParams = array())
	{

		global $DB, $DBType, $APPLICATION;

		$this->errors = false;

		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/install.sql");

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

		if(!array_key_exists("save_tables", $arParams) || ($arParams["save_tables"] != "Y"))
		{
			//kick current user options
			COption::RemoveOption($this->MODULE_ID, "");
			//drop tables
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/uninstall.sql");
		}

		UnRegisterModule($this->MODULE_ID);

		if($this->errors !== false)
		{
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}

		return true;
	}

}
?>