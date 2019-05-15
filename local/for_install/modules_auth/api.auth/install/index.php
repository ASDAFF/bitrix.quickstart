<?

use Bitrix\Main\ModuleManager,
	 Bitrix\Main\EventManager,
	 Bitrix\Main\Localization\Loc;


Loc::loadMessages(__FILE__);

Class api_auth extends CModule
{
	var $MODULE_ID           = 'api.auth';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION      = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME         = GetMessage("api.auth_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("api.auth_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.auth_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("api.auth_PARTNER_URI");
	}

	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;

		$errors = null;
		if(!$DB->Query("SELECT 'x' FROM `api_auth_settings`", true))
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/install.sql');

		if(!empty($errors)) {
			$APPLICATION->ThrowException(implode('', $errors));
			return false;
		}

		$eventManager = EventManager::getInstance();
		$eventManager->registerEventHandler('main', 'OnUserTypeBuildList', $this->MODULE_ID, '\Api\Auth\UserType\Location', 'getUserTypeDescription');

		return true;
	}

	function UnInstallDB()
	{
		global $DB, $DBType, $APPLICATION;

		$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/uninstall.sql');
		//$DB->Query('DELETE FROM `b_option` WHERE `MODULE_ID` = '".$this->MODULE_ID."'", true);
		//$DB->Query('DELETE FROM `b_event_log` WHERE `MODULE_ID` = '".$this->MODULE_ID."'", true);

		if(!empty($errors)) {
			$APPLICATION->ThrowException(implode('', $errors));
			return false;
		}

		$eventManager = EventManager::getInstance();
		$eventManager->unRegisterEventHandler('main', 'OnUserTypeBuildList', $this->MODULE_ID, '\Api\Auth\UserType\Location', 'getUserTypeDescription');

		return true;
	}

	function InstallEvents()
	{
		//Получим все языки сайта для привязки типов
		$arLangs = array();
		$rsLangs = CLanguage::GetList($by = "lid", $order = "desc", Array("ACTIVE" => "Y"));
		while($row = $rsLangs->Fetch()) {
			$arLangs[] = $row['LID'];
		}

		//Добавляем типы почтовых событий
		$eventType   = new CEventType;
		$arEventType = (array)Loc::getMessage('API_AUTH_INSTALL_EVENT_TYPE');
		foreach($arEventType as $arFields) {
			foreach($arLangs as $LID) {
				$arFields['LID'] = $LID;
				$eventType->Add($arFields);
			}
		}
		unset($arFields);


		//Получим все активные сайты для привязки к почтовым шаблонам
		$arSiteId = array();
		$rsSites  = \Bitrix\Main\SiteTable::getList(array(
			 'select' => array('LID'),
			 'filter' => array('=ACTIVE' => 'Y'),
		));
		while($row = $rsSites->fetch())
			$arSiteId[] = $row['LID'];


		//Добавляем почтовые шаблоны
		$eventM         = new CEventMessage;
		$arEventMessage = (array)Loc::getMessage('API_AUTH_INSTALL_EVENT_MESSAGE');

		foreach($arEventMessage as $arFields) {
			$arFields['LID'] = $arSiteId;
			$eventM->Add($arFields);
		}

		return true;
	}

	function UnInstallEvents()
	{
		global $DB;

		$eventM      = new CEventMessage;
		$eventType   = new CEventType;
		if($arEventType = (array)Loc::getMessage('API_AUTH_INSTALL_EVENT_TYPE')){

			foreach($arEventType as $arFields) {
				$typeId = $arFields['EVENT_NAME'];
				$rsMess = $eventM->GetList($by = 'id', $order = 'asc', array('TYPE_ID' => $typeId));
				while($arMess = $rsMess->Fetch()) {
					$DB->StartTransaction();
					if(!$eventM->Delete(intval($arMess['ID']))) {
						$DB->Rollback();
						//$strError.=GetMessage("DELETE_ERROR");
					}
					else
						$DB->Commit();
				}

				$eventType->Delete($typeId);
			}

		}

		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/images/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/' . $this->MODULE_ID, true, true);

		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/bitrix/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/bitrix', true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');

		DeleteDirFilesEx('/bitrix/css/' . $this->MODULE_ID . '/');
		DeleteDirFilesEx('/bitrix/images/' . $this->MODULE_ID . '/');

		DeleteDirFilesEx('/bitrix/components/api/auth/');
		DeleteDirFilesEx('/bitrix/components/api/auth.ajax/');
		DeleteDirFilesEx('/bitrix/components/api/auth.change/');
		DeleteDirFilesEx('/bitrix/components/api/auth.confirm/');
		DeleteDirFilesEx('/bitrix/components/api/auth.login/');
		DeleteDirFilesEx('/bitrix/components/api/auth.register/');
		DeleteDirFilesEx('/bitrix/components/api/auth.restore/');
		DeleteDirFilesEx('/bitrix/components/api/auth.profile/');

		return true;
	}

	function InstallPublic()
	{
		$path_from = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/public';
		$path_to   = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/.default/components/bitrix';

		CopyDirFiles($path_from, $path_to, true, true);
	}

	function UnInstallPublic()
	{
		DeleteDirFilesEx('/bitrix/templates/.default/components/bitrix/system.auth.authorize/');
	}

	function DoInstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$this->InstallFiles();
		$this->InstallDB();
		$this->InstallEvents();
		$this->InstallPublic();
		$this->InstallCore();

		ModuleManager::registerModule($this->MODULE_ID);

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		$this->UnInstallPublic();

		ModuleManager::unRegisterModule($this->MODULE_ID);

		return true;
	}


	function InstallCore()
	{
		require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/update_client_partner.php");

		$coreModule = 'api.core';
		if(!IsModuleInstalled($coreModule)) {
			$strError = '';
			if(!CUpdateClientPartner::LoadModuleNoDemand($coreModule, $strError, 'N', LANGUAGE_ID)) {
				CUpdateClientPartner::AddMessage2Log("exec CUpdateClientPartner::LoadModuleNoDemand api.core error");
			}
			else {
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
}

?>