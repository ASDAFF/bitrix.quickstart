<?
#################################################
#        Company developer: IPOL
#        Developer: Nikta Egorov
#        Site: http://www.ipol.com
#        E-mail: om-sv2@mail.ru
#        Copyright (c) 2006-2012 IPOL
#################################################
?>
<?
IncludeModuleLangFile(__FILE__); 

if(class_exists("ipol_sdek")) 
    return;
	
Class ipol_sdek extends CModule{
    var $MODULE_ID = "ipol.sdek";
    var $MODULE_NAME;
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "N";
        var $errors;

	function ipol_sdek(){
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("IPOLSDEK_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("IPOLSDEK_INSTALL_DESCRIPTION");
        
        $this->PARTNER_NAME = "Ipol";
        $this->PARTNER_URI = "http://www.ipolh.com";
	}

	protected function getDB(){
		return array('ipol_sdek'=>'Orders','ipol_sdekcities'=>'Cities','ipol_sdeklogs'=>'Auth');
	}
	
	function InstallDB(){
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		$arDB = $this->getDB();

		foreach($arDB as $name => $path)
			if(!$DB->Query("SELECT 'x' FROM ".$name, true)){
				$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$this->MODULE_ID."/install/db/mysql/install".$path.".sql");
				if($this->errors !== false){
					$APPLICATION->ThrowException(implode("", $this->errors));
					return false;
				}
			}

		return true;
	}

	function UnInstallDB(){
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		$arDB = $this->getDB();

		foreach($arDB as $name => $path){
			$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/".$this->MODULE_ID."/install/db/mysql/unInstall".$path.".sql");
			if(!empty($this->errors)){
				$APPLICATION->ThrowException(implode("", $this->errors));
				return false;
			}
		}

		return true;
	}

	function InstallEvents(){
		//события устанавливаются в файле /classes/general/sdekhelper.php функция auth
		return true;
	}
	function UnInstallEvents() {
		UnRegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "sdekdriver", "onEpilog"); // заполнение заявки		
		UnRegisterModuleDependences("main", "OnEndBufferContent", $this->MODULE_ID, "CDeliverySDEK", "onBufferContent"); // сохранение города и пункта при перезагрузке
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", $this->MODULE_ID, "CDeliverySDEK", "pickupLoader");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepProcess", $this->MODULE_ID, "CDeliverySDEK", "loadComponent");
		UnRegisterModuleDependences("main", "OnAdminListDisplay", $this->MODULE_ID, "sdekdriver", "displayActPrint"); // печати
		UnRegisterModuleDependences("main", "OnBeforeProlog", $this->MODULE_ID, "sdekdriver", "OnBeforePrologHandler");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", $this->MODULE_ID, "sdekdriver", "orderCreate"); // создание заказа
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepPaySystem", $this->MODULE_ID, "CDeliverySDEK", "checkNalD2P");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", $this->MODULE_ID, "CDeliverySDEK", "checkNalP2D");
		return true;
	}

	function InstallFiles(){
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".$this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/", true, true);
		//файл доставки копируется в  файле /classes/general/imlhelper.php функция auth
		// $fileOfActs = $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID."/printActs.php";
		// if(file_exists($fileOfActs) && LANG_CHARSET === 'UTF-8')
			// file_put_contents($fileOfActs,$GLOBALS['APPLICATION']->ConvertCharset(file_get_contents($fileOfActs),'windows-1251','UTF-8'));
		return true;
	}
	function UnInstallFiles(){
		DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID);
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.$this->MODULE_ID))
			DeleteDirFilesEx("/bitrix/tools/".$this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/images/".$this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_delivery/delivery_sdek.php");
		DeleteDirFilesEx("/bitrix/components/ipol/ipol.sdekPickup");
		DeleteDirFilesEx("/upload/".$this->MODULE_ID);
		$arrayOfFiles=scandir($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/ipol');
		$flagForDelete=true;
		foreach($arrayOfFiles as $element){
			if(strlen($element)>2)
				$flagForDelete=false;
		}
		if($flagForDelete)
			DeleteDirFilesEx("/bitrix/components/ipol");
		return true;
	}
	
    function DoInstall(){
        global $DB, $APPLICATION, $step;
		$this->errors = false;
		
		$this->InstallDB();
		$this->InstallEvents();
		$this->InstallFiles();
		
		RegisterModule($this->MODULE_ID);
		
        $APPLICATION->IncludeAdminFile(GetMessage("IPOLSDEK_INSTALL"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
    }

    function DoUninstall(){
        global $DB, $APPLICATION, $step;
		$this->errors = false;
		
		COption::SetOptionString($this->MODULE_ID,'logSDEK','');
		COption::SetOptionString($this->MODULE_ID,'pasSDEK','');
		COption::SetOptionString($this->MODULE_ID,'logged',false);
		 
		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		
		CAgent::RemoveModuleAgents('ipol.sdek');
		
		UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(GetMessage("IPOLSDEK_DEL"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php");
    }
}
?>
