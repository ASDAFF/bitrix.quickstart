<?
IncludeModuleLangFile(__FILE__);

Class imaginweb_sms extends CModule{
	
	const MODULE_ID = "imaginweb.sms";
	var $MODULE_ID = "imaginweb.sms";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';
	var $MODULE_GROUP_RIGHTS = "Y";
	
	function __construct(){
		
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("imaginweb.sms_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("imaginweb.sms_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("imaginweb.sms_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("imaginweb.sms_PARTNER_URI");
	}
	
	function InstallDB($arParams = array()){
		
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		
		// Database tables creation
		if(!$DB->Query("SELECT 'x' FROM iwebsms_list_rubric WHERE 1=0", true)){
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".$DBType."/install.sql");
		}

		if($this->errors !== false){
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		else{
			RegisterModule(self::MODULE_ID);
			CModule::IncludeModule(self::MODULE_ID);
			
			RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", self::MODULE_ID, "CImaginwebSms", "OnSaleComponentOrderOneStepCompleteHandler");
			RegisterModuleDependences("sale", "OnSaleComponentOrderComplete", self::MODULE_ID, "CImaginwebSms", "OnSaleComponentOrderCompleteHandler");
			RegisterModuleDependences("sale", "OnSalePayOrder", self::MODULE_ID, "CImaginwebSms", "OnSalePayOrderHandler");
			RegisterModuleDependences("sale", "OnSaleDeliveryOrder", self::MODULE_ID, "CImaginwebSms", "OnSaleDeliveryOrderHandler");
			RegisterModuleDependences("sale", "OnSaleCancelOrder", self::MODULE_ID, "CImaginwebSms", "OnSaleCancelOrderHandler");
			RegisterModuleDependences("sale", "OnSaleStatusOrder", self::MODULE_ID, "CImaginwebSms", "OnSaleStatusOrderHandler");
			
			RegisterModuleDependences("main", "OnBeforeUserRegister", self::MODULE_ID, "CImaginwebSms", "OnBeforeUserRegisterHandler");
			RegisterModuleDependences("main", "OnBeforeUserSimpleRegister", self::MODULE_ID, "CImaginwebSms", "OnBeforeUserSimpleRegisterHandler");
			RegisterModuleDependences("main", "OnBeforeUserUpdate", self::MODULE_ID, "CImaginwebSms", "OnBeforeUserUpdateHandler");
			RegisterModuleDependences("main", "OnBeforeUserAdd", self::MODULE_ID, "CImaginwebSms", "OnBeforeUserAddHandler");
			
			RegisterModuleDependences("main", "OnBeforeLangDelete", self::MODULE_ID, "SMSCRubric", "OnBeforeLangDelete");
			RegisterModuleDependences("main", "OnUserDelete", self::MODULE_ID, "SMSCSubscription", "OnUserDelete");
			RegisterModuleDependences("main", "OnUserLogout", self::MODULE_ID, "SMSCSubscription", "OnUserLogout");
			RegisterModuleDependences("main", "OnGroupDelete", self::MODULE_ID, "SMSCPosting", "OnGroupDelete");
			
			CAgent::RemoveAgent("SMSCSubscription::CleanUp();", self::MODULE_ID);
            if(class_exists("CTimeZone")){
				CTimeZone::Disable();
            }
			CAgent::Add(
				array(
					"NAME" => "SMSCSubscription::CleanUp();",
					"MODULE_ID" => self::MODULE_ID,
					"ACTIVE" => "Y",
					"NEXT_EXEC" => date("d.m.Y H:i:s", mktime(3, 0, 0, date("m"), date("j") + 1, date("Y"))),
					"AGENT_INTERVAL" => 86400,
					"IS_PERIOD" => "Y"
				)
			);
            if(class_exists("CTimeZone")){
				CTimeZone::Enable();
            }
			
			$dbUserField = CUserTypeEntity::GetList(
				array(
					"SORT" => "ASC"
				),
				array(
					"FIELD_NAME" => "UF_IWEB_SMS_PASSWORD"
				)
			);
			if(!$dbUserField->Fetch()){
				$ob = new CUserTypeEntity();
				$arFields = array(
					"ENTITY_ID" => "USER",
					"FIELD_NAME" => "UF_IWEB_SMS_PASSWORD",
					"USER_TYPE_ID" => "string",
					"XML_ID" => "",
					"SORT" => 100,
					"MULTIPLE" => "N",
					"MANDATORY" => "N",
					"SHOW_FILTER" => "N",
					"SHOW_IN_LIST" => "Y",
					"EDIT_IN_LIST" => "Y",
					"IS_SEARCHABLE" => "N"
				);
				$FIELD_ID = $ob->Add($arFields);
			}
			
			return true;
		}
	}
	
	function UnInstallDB($arParams = array()){
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		if(!array_key_exists("save_tables", $arParams) || ($arParams["save_tables"] != "Y")){
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".$DBType."/uninstall.sql");
			$strSql = "SELECT ID FROM b_file WHERE MODULE_ID='".self::MODULE_ID."'";
			$rsFile = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			while($arFile = $rsFile->Fetch()){
				CFile::Delete($arFile["ID"]);
			}
		}
		
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", self::MODULE_ID, "CImaginwebSms", "OnSaleComponentOrderOneStepCompleteHandler");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderComplete", self::MODULE_ID, "CImaginwebSms", "OnSaleComponentOrderCompleteHandler");
		UnRegisterModuleDependences("sale", "OnSalePayOrder", self::MODULE_ID, "CImaginwebSms", "OnSalePayOrderHandler");
		UnRegisterModuleDependences("sale", "OnSaleDeliveryOrder", self::MODULE_ID, "CImaginwebSms", "OnSaleDeliveryOrderHandler");
		UnRegisterModuleDependences("sale", "OnSaleCancelOrder", self::MODULE_ID, "CImaginwebSms", "OnSaleCancelOrderHandler");
		UnRegisterModuleDependences("sale", "OnSaleStatusOrder", self::MODULE_ID, "CImaginwebSms", "OnSaleStatusOrderHandler");
		
		UnRegisterModuleDependences("main", "OnBeforeUserRegister", self::MODULE_ID, "CImaginwebSms", "OnBeforeUserRegisterHandler");
		UnRegisterModuleDependences("main", "OnBeforeUserSimpleRegister", self::MODULE_ID, "CImaginwebSms", "OnBeforeUserSimpleRegisterHandler");
		UnRegisterModuleDependences("main", "OnBeforeUserUpdate", self::MODULE_ID, "CImaginwebSms", "OnBeforeUserUpdateHandler");
		UnRegisterModuleDependences("main", "OnBeforeUserAdd", self::MODULE_ID, "CImaginwebSms", "OnBeforeUserAddHandler");

		UnRegisterModuleDependences("main", "OnBeforeLangDelete", self::MODULE_ID, "SMSCRubric", "OnBeforeLangDelete");
		UnRegisterModuleDependences("main", "OnUserDelete", self::MODULE_ID, "SMSCSubscription", "OnUserDelete");
		UnRegisterModuleDependences("main", "OnGroupDelete", self::MODULE_ID, "SMSCPosting", "OnGroupDelete");
		UnRegisterModuleDependences("main", "OnUserLogout", self::MODULE_ID, "SMSCSubscription", "OnUserLogout");
		
		UnRegisterModule(self::MODULE_ID);

		if($this->errors !== false){
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		return true;
	}
	
	function InstallEvents(){
		
		return true;
	}
	
	function UnInstallEvents(){
		
		return true;
	}
	
	function InstallFiles($arParams = array()){
		
		if($_ENV["COMPUTERNAME"] != "BX"){
			if(is_dir($p = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/admin")){
				if($dir = opendir($p)){
					while(false !== $item = readdir($dir)){
						if($item == '..' || $item == '.' || $item == 'menu.php'){
							continue;
						}
						file_put_contents($file = $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/".self::MODULE_ID."_".$item, '<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
					}
					closedir($dir);
				}
			}
			if(is_dir($p = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/components")){
				if($dir = opendir($p)){
					while(false !== $item = readdir($dir)){
						if($item == '..' || $item == '.'){
							continue;
						}
						CopyDirFiles($p."/".$item, $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/".$item, $ReWrite = true, $Recursive = true);
					}
					closedir($dir);
				}
			}
			
			//CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".self::MODULE_ID, false, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", false, true);
			//CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".self::MODULE_ID."/js/", true, true);
		}
		return true;
	}

	function UnInstallFiles(){
		
		if($_ENV["COMPUTERNAME"] != "BX"){
			if(is_dir($p = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/admin")){
				if($dir = opendir($p)){
					while(false !== $item = readdir($dir)){
						if($item == '..' || $item == '.' || $item == 'menu.php'){
							continue;
						}
						unlink($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/".self::MODULE_ID.'_'.$item);
					}
					closedir($dir);
				}
			}
			if(is_dir($p = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/components")){
				if($dir = opendir($p)){
					while(false !== $item = readdir($dir)){
						if($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item)){
							continue;
						}
						$dir0 = opendir($p0);
						while(false !== $item0 = readdir($dir0)){
							if($item0 == '..' || $item0 == '.'){
								continue;
							}	
							DeleteDirFilesEx("/bitrix/components/".$item."/".$item0);
						}
						closedir($dir0);
					}
					closedir($dir);
				}
			}
			
			//admin files
			//DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
			//css
			DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
			//icons
			DeleteDirFilesEx("/bitrix/themes/.default/icons/".self::MODULE_ID."/");
			//images
			DeleteDirFilesEx("/bitrix/images/".self::MODULE_ID."/");
			
		}
		return true;
	}

	function DoInstall(){
		
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		$POST_RIGHT = $APPLICATION->GetGroupRight(self::MODULE_ID);
		if($POST_RIGHT == "W"){
			
			$step = IntVal($step);
			if($step < 2){
				$APPLICATION->IncludeAdminFile(GetMessage("imaginweb.sms_inst_inst_title"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/inst1.php");
			}
			elseif($step == 2){
				if($this->InstallDB()){
					$this->InstallEvents();
					$this->InstallFiles();
				}
				$GLOBALS["errors"] = $this->errors;
				$APPLICATION->IncludeAdminFile(GetMessage("imaginweb.sms_inst_inst_title"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/inst2.php");
			}
		}
	}

	function DoUninstall(){
		
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step;
		$POST_RIGHT = $APPLICATION->GetGroupRight(self::MODULE_ID);
		if($POST_RIGHT == "W"){
			$step = IntVal($step);
			if($step < 2){
				$APPLICATION->IncludeAdminFile(GetMessage("imaginweb.sms_inst_uninst_title"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/uninst1.php");
			}
			elseif($step == 2){
				$this->UnInstallDB(array(
					"save_tables" => $_REQUEST["save_tables"],
				));
				$this->UnInstallEvents();
				$this->UnInstallFiles();
				$GLOBALS["errors"] = $this->errors;
				$APPLICATION->IncludeAdminFile(GetMessage("imaginweb.sms_inst_uninst_title"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/uninst2.php");
			}
		}
	}
	
}
?>
