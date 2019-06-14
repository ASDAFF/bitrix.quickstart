<?
IncludeModuleLangFile(__FILE__);

Class mlife_smsservices extends CModule
{
        var $MODULE_ID = "mlife.smsservices";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;

        function mlife_smsservices() {
				$path = str_replace("\\", "/", __FILE__);
				$path = substr($path, 0, strlen($path) - strlen("/index.php"));
				include($path."/version.php");
				
				$this->MODULE_VERSION = $arModuleVersion["VERSION"];
				$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
				$this->PARTNER_NAME = 'MLife Media';
				$this->PARTNER_URI = 'http://mlife-media.by/';
				$this->MODULE_NAME = GetMessage("MLIFESS_MODULE_NAME");
				$this->MODULE_DESCRIPTION = GetMessage("MLIFESS_MODULE_DESC");
				
				if(GetMessage("MLIFESS_PARTNER_NAME")){
					$this->PARTNER_NAME = GetMessage("MLIFESS_PARTNER_NAME");
				}
				if(GetMessage("MLIFESS_PARTNER_URI")){
					$this->PARTNER_URI = GetMessage("MLIFESS_PARTNER_URI");
				}
				
			return true;
        }

        function DoInstall() {
			
			CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin",true,true);
			
			CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/css",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/css",true,true);
			
			RegisterModule($this->MODULE_ID);
			$this->createTable();
			$this->createAgents();
			
			$eventManager = \Bitrix\Main\EventManager::getInstance();
			$eventManager->registerEventHandlerCompatible('main', 'OnAdminTabControlBegin', $this->MODULE_ID, '\Mlife\Smsservices\Events', 'OnAdminTabControlBegin');
			
			RegisterModuleDependences("sale", "OnSaleStatusOrder", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleStatusOrderHandler");
			RegisterModuleDependences("sale", "OnSaleCancelOrder", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleCancelOrderHandler");
			RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleComponentOrderOneStepCompleteHandler");
			RegisterModuleDependences("sale", "OnSaleComponentOrderComplete", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleComponentOrderOneStepCompleteHandler");
			RegisterModuleDependences("sale", "OnSaleCancelOrder", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleCancelOrderHandler");
			RegisterModuleDependences("sale", "OnSaleDeliveryOrder", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleDeliveryOrderHandler");
			
			//Редирект на настройки приложения
			LocalRedirect('/bitrix/admin/settings.php?lang=ru&mid='.$this->MODULE_ID.'&mid_menu=1');
        }

        function DoUninstall() {
			//Удаление файлов визуальной части админ панели
			DeleteDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
			
			DeleteDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/css",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/css");
			
			$this->deleteTable();
			$this->deleteAgents();
			
			//UnRegisterModuleDependences("sale", "OnSaleStatusOrder", $this->MODULE_ID, "CmlifeCmsServicesHandlers", "OnSaleStatusOrderHandler");
			//UnRegisterModuleDependences("sale", "OnSaleCancelOrder", $this->MODULE_ID, "CmlifeCmsServicesHandlers", "OnSaleCancelOrderHandler");
			//UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", $this->MODULE_ID, "CmlifeCmsServicesHandlers", "OnSaleComponentOrderOneStepCompleteHandler");
			//UnRegisterModuleDependences("sale", "OnSaleComponentOrderComplete", $this->MODULE_ID, "CmlifeCmsServicesHandlers", "OnSaleComponentOrderOneStepCompleteHandler");
			//UnRegisterModuleDependences("sale", "OnSaleCancelOrder", $this->MODULE_ID, "CmlifeCmsServicesHandlers", "OnSaleCancelOrderHandler");
			//UnRegisterModuleDependences("sale", "OnSaleDeliveryOrder", $this->MODULE_ID, "CmlifeCmsServicesHandlers", "OnSaleDeliveryOrderHandler");
			
			UnRegisterModuleDependences("main", "OnAdminTabControlBegin", $this->MODULE_ID, '\Mlife\Smsservices\Events', "OnAdminTabControlBegin");
			
			UnRegisterModuleDependences("sale", "OnSaleStatusOrder", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleStatusOrderHandler");
			UnRegisterModuleDependences("sale", "OnSaleCancelOrder", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleCancelOrderHandler");
			UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleComponentOrderOneStepCompleteHandler");
			UnRegisterModuleDependences("sale", "OnSaleComponentOrderComplete", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleComponentOrderOneStepCompleteHandler");
			UnRegisterModuleDependences("sale", "OnSaleCancelOrder", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleCancelOrderHandler");
			UnRegisterModuleDependences("sale", "OnSaleDeliveryOrder", $this->MODULE_ID, "\\Mlife\\Smsservices\\Handlers", "OnSaleDeliveryOrderHandler");
			
			\Bitrix\Main\Loader::includeModule("mlife.smsservices");
			\Mlife\Smsservices\EventlistTable::removeAllEvent();
			
			UnRegisterModule($this->MODULE_ID);
        }
	
	function createTable() {
		global $DB;
		$sql = "
		CREATE TABLE IF NOT EXISTS `mlife_smsservices_list` (
		  `ID` int(18) NOT NULL AUTO_INCREMENT,
		  `PROVIDER` varchar(50) DEFAULT NULL,
		  `SMSID` varchar(100) DEFAULT NULL,
		  `SENDER` varchar(50) DEFAULT NULL,
		  `PHONE` varchar(20) DEFAULT NULL,
		  `TIME` int(11) NOT NULL,
		  `TIME_ST` int(11) NOT NULL,
		  `MEWSS` varchar(2655) NOT NULL,
		  `PRIM` varchar(655) DEFAULT NULL,
		  `STATUS` int(2) NOT NULL DEFAULT '0',
		  `EVENT` varchar(100) NULL DEFAULT 'DEFAULT',
		  `EVENT_NAME` varchar(100) NULL DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) AUTO_INCREMENT=1 ;
		";
		if(strtolower($DB->type)=="mysql") $res = $DB->Query($sql);
		$sql = "
		CREATE TABLE IF NOT EXISTS `mlife_smsservices_eventlist` (
		`ID` int(9) NOT NULL AUTO_INCREMENT,
		`SITE_ID` varchar(10) NOT NULL,
		`SENDER` varchar(50) NULL,
		`EVENT` varchar(50) NOT NULL,
		`NAME` varchar(255) NOT NULL,
		`TEMPLATE` varchar(2500) NULL,
		`PARAMS` varchar(6255) NULL,
		`ACTIVE` varchar(1) NOT NULL DEFAULT 'N',
		 PRIMARY KEY (`ID`)
		);
		";
		if(strtolower($DB->type)=="mysql") $res = $DB->Query($sql);
		//ALTER TABLE  `mlife_smsservices_list` CHANGE  `MEWSS`  `MEWSS` VARCHAR( 2655 ) NOT NULL
	}
	
	function deleteTable () {
		global $DB;
		//$sql = 'DROP TABLE IF EXISTS `b_mlife_smsservices_list`';
		$sql = 'DROP TABLE IF EXISTS `mlife_smsservices_list`';
		$res = $DB->Query($sql);
		$sql = 'DROP TABLE IF EXISTS `mlife_smsservices_eventlist`';
		$res = $DB->Query($sql);
	}
	
	function createAgents() {
		CAgent::AddAgent(
		"\\Mlife\\Smsservices\\Agent::statusSms();",
		$this->MODULE_ID,
		"N",
		600);
		CAgent::AddAgent(
		"\\Mlife\\Smsservices\\Agent::turnSms();",
		$this->MODULE_ID,
		"N",
		300);
	}
	
	function deleteAgents() {
		//CAgent::RemoveAgent("CMlifeSmsServicesAgentStatusSms();", "mlife.smsservices");
		//CAgent::RemoveAgent("CMlifeSmsServicesAgentTurnSms();", "mlife.smsservices");
		CAgent::RemoveAgent("\\Mlife\\Smsservices\\Agent::turnSms();", $this->MODULE_ID);
		CAgent::RemoveAgent("\\Mlife\\Smsservices\\Agent::statusSms();", $this->MODULE_ID);
	}
}

?>

