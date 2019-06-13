<?
IncludeModuleLangFile(__FILE__);

Class api_feedbackex extends CModule
{
	const MODULE_ID = 'api.feedbackex';
	var $MODULE_ID = 'api.feedbackex';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $SITE_ID;
	var $strError  = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION      = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME         = GetMessage("AFEX_INSTALL_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("AFEX_INSTALL_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("AFEX_INSTALL_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("AFEX_INSTALL_PARTNER_URI");
	}

	function checkDependency()
	{
		$mainModuleValid = (defined('SM_VERSION') && version_compare(SM_VERSION, '15.5.1','>='));

		return $mainModuleValid;
	}


	function InstallDB($arParams = array())
	{
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		return true;
	}

	function InstallEvents()
	{
		//RegisterModuleDependences(self::MODULE_ID, 'OnBeforeEmailSend', self::MODULE_ID);
		//RegisterModuleDependences(self::MODULE_ID, 'OnAfterEmailSend', self::MODULE_ID);

		$siteId    = (isset($this->SITE_ID) && !empty($this->SITE_ID)) ? $this->SITE_ID : 's1';
		$eventType = new CEventType;
		$eventM    = new CEventMessage;

		$arEventTypeFields = array(
			0 => array(
				'LID'         => 'ru',
				'EVENT_NAME'  => GetMessage('AFEX_INSTALL_ET_EVENT_NAME'),
				'NAME'        => GetMessage('AFEX_INSTALL_ET_NAME'),
				'DESCRIPTION' => GetMessage('AFEX_INSTALL_ET_DESCRIPTION'),
			),
			1 => array(
				'LID'         => 'en',
				'EVENT_NAME'  => GetMessage('AFEX_INSTALL_ET_EVENT_NAME'),
				'NAME'        => GetMessage('AFEX_INSTALL_ET_NAME'),
				'DESCRIPTION' => GetMessage('AFEX_INSTALL_ET_DESCRIPTION'),
			),
		);
		foreach($arEventTypeFields as $arField)
		{
			$rsET = $eventType->GetByID($arField['EVENT_NAME'], $arField['LID']);
			$arET = $rsET->Fetch();

			if(!$arET)
				$eventType->Add($arField);
			else
				$eventType->Update(array('ID' => $arET['ID']), $arField);
		}
		unset($rsET, $arET, $arField);

		$arEventMessFields = array(
			0 => array(
				'ACTIVE'     => 'Y',
				'EVENT_NAME' => GetMessage('AFEX_INSTALL_ET_EVENT_NAME'),
				'LID'        => $siteId,
				'EMAIL_FROM' => GetMessage('AFEX_INSTALL_EM_EMAIL_FROM'),
				'EMAIL_TO'   => GetMessage('AFEX_INSTALL_EM_EMAIL_TO'),
				'BCC'        => GetMessage('AFEX_INSTALL_EM_BCC'),
				'SUBJECT'    => GetMessage('AFEX_INSTALL_EM_SUBJECT'),
				'BODY_TYPE'  => 'html',
				'MESSAGE'    => GetMessage('AFEX_INSTALL_EM_MESSAGE'),
			),
		);

		foreach($arEventMessFields as $arField)
		{
			$rsMess = $eventM->GetList($by = 'id', $order = 'asc', array(
				'SUBJECT' => $arField['SUBJECT'],
				'LID'     => $arField['LID'],
				'TYPE_ID' => GetMessage('AFEX_INSTALL_ET_EVENT_NAME'),
			));
			if(!$arMess = $rsMess->Fetch())
				$eventM->Add($arField);
		}
		unset($rsMess, $arMess, $arField);


		return true;
	}

	function UnInstallEvents()
	{
		//UnRegisterModuleDependences(self::MODULE_ID, 'OnBeforeEmailSend', self::MODULE_ID);
		//UnRegisterModuleDependences(self::MODULE_ID, 'OnAfterEmailSend', self::MODULE_ID);

		$eventType = new CEventType;
		$eventM    = new CEventMessage;

		$arFilter = array('TYPE_ID' => GetMessage('AFEX_INSTALL_ET_EVENT_NAME'));
		$rsET = $eventType->GetList($arFilter);
		while($arET = $rsET->Fetch())
		{
			$rsEM = $eventM->GetList($by="id", $order="asc", $arFilter);
			while($arEM = $rsEM->Fetch())
				$eventM->Delete($arEM['ID']);

			$eventType->Delete($arET['EVENT_NAME']);
		}


		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/components/api/feedbackex/");

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $step, $arSites;

		if (!$this->checkDependency())
		{
			$APPLICATION->IncludeAdminFile(GetMessage('AFEX_INSTALL_MODULE_NAME'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/error_dependency.php");

			return false;
		}

		$step     = intval($step);
		$ob_sites = CSite::GetList($by = "id", $order = "asc", array("ACTIVE" => "Y"));
		while($rs_site = $ob_sites->Fetch())
			$arSites[] = $rs_site;

		if(empty($arSites))
		{
			CAdminMessage::ShowMessage(GetMessage('AFEX_INSTALL_SITES_NOT_FOUND'));

			return false;
		}

		if($step < 2 && count($arSites) > 1)
			$APPLICATION->IncludeAdminFile(GetMessage("IBLOCK_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/step1.php");
		else
		{
			if(!empty($_REQUEST["API_SITE_ID"]) && is_array($_REQUEST["API_SITE_ID"]))
				$this->SITE_ID = $_REQUEST["API_SITE_ID"];
			else
				$this->SITE_ID[] = $arSites[0]['ID'];
		}

		$this->InstallFiles();
		$this->InstallDB();
		$this->InstallEvents();
		$this->InstallPublic();
		$this->InstallCore();

		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
	}

	function InstallCore(){
		require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/update_client_partner.php");

		$coreModule = 'api.core';
		if(!IsModuleInstalled($coreModule)) {
			$strError = '';
			if(!CUpdateClientPartner::LoadModuleNoDemand($coreModule, $strError, 'N', LANGUAGE_ID)) {
				CUpdateClientPartner::AddMessage2Log("exec CUpdateClientPartner::LoadModuleNoDemand api.core error");
			}
			else{
				if($oModule = CModule::CreateModuleObject($coreModule)) {
					if(!$oModule->IsInstalled()) {
						$oModule->DoInstall();
					}
				}
			}
		}

		if(IsModuleInstalled($coreModule)) {
			do {
				$result = CUpdateClientPartner::loadModule4Wizard($coreModule);
			}
			while($result == 'STP');
		}
	}

	function InstallPublic()
	{
		CopyDirFiles(
			 $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/public',
			 $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include',
			 true, true
		);
	}
}

?>