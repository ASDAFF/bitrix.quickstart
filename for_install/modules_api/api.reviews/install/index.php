<?
use \Bitrix\Main\Application;
use \Bitrix\Main\SiteTable;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class api_reviews extends CModule
{
	var $MODULE_ID           = 'api.reviews';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = 'Y';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . '/version.php');
		$this->MODULE_VERSION      = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME         = GetMessage('ARII_MODULE_NAME');
		$this->MODULE_DESCRIPTION  = GetMessage('ARII_MODULE_DESC');

		$this->PARTNER_NAME = GetMessage('ARII_PARTNER_NAME');
		$this->PARTNER_URI  = GetMessage('ARII_PARTNER_URI');
	}

	function checkDependency()
	{
		$bMainValid = (defined('SM_VERSION') && version_compare(SM_VERSION, '16.00.00', '>='));
		$bPhpValid  = (defined('PHP_VERSION') && version_compare(PHP_VERSION, '5.3.0', '>='));

		return (bool)($bMainValid && $bPhpValid);
	}

	function DoInstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		if(!$this->checkDependency()) {
			//CEventLog::Log('ERROR','AFD_INSTALL_CHECK_DEPENDENCY',$this->MODULE_ID,'DoInstall()',Loc::getMessage('AFD_LOG_CHECK_DEPENDENCY'));
			$APPLICATION->IncludeAdminFile('', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/error_dependency.php');
			return false;
		}

		$this->InstallDB();
		$this->InstallFiles();
		$this->InstallEvents();
		$this->InstallCore();

		ModuleManager::registerModule($this->MODULE_ID);

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		$step = IntVal($step);
		if($step < 2)
			$APPLICATION->IncludeAdminFile(Loc::getMessage('IBLOCK_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/unstep1.php');
		else {
			$arParams = array(
				 'savedata' => $_REQUEST['savedata'],
			);

			$this->UnInstallDB($arParams);
			$this->UnInstallFiles();
			$this->UnInstallEvents($arParams);

			ModuleManager::unRegisterModule($this->MODULE_ID);
		}

		return true;
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

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$errors = null;
		if(!$DB->Query("SELECT 'x' FROM `api_reviews`", true))
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/install.sql');

		if(!empty($errors)) {
			$APPLICATION->ThrowException(implode('', $errors));
			return false;
		}

		$eventManager = EventManager::getInstance();
		$eventManager->registerEventHandler($this->MODULE_ID, 'onAfterReviewAdd', $this->MODULE_ID, '\Api\Reviews\Event', 'onAfterReviewAdd');

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$errors = null;
		if($arParams['savedata'] != 'Y') {
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/uninstall.sql');
			//$DB->Query('DELETE FROM `b_option` WHERE `MODULE_ID` = '".$this->MODULE_ID."'", true);
			//$DB->Query('DELETE FROM `b_event_log` WHERE `MODULE_ID` = '".$this->MODULE_ID."'", true);

			if(!empty($errors)) {
				$APPLICATION->ThrowException(implode('', $errors));
				return false;
			}
		}

		$eventManager = EventManager::getInstance();
		$eventManager->unRegisterEventHandler($this->MODULE_ID, 'onAfterReviewAdd', $this->MODULE_ID, '\Api\Reviews\Event', 'onAfterReviewAdd');

		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/". $this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/images", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/images/". $this->MODULE_ID, true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');

		DeleteDirFilesEx('/bitrix/components/api/reviews/');
		DeleteDirFilesEx('/bitrix/components/api/reviews.form/');
		DeleteDirFilesEx('/bitrix/components/api/reviews.list/');
		DeleteDirFilesEx('/bitrix/components/api/reviews.sort/');
		DeleteDirFilesEx('/bitrix/components/api/reviews.stat/');
		DeleteDirFilesEx('/bitrix/components/api/reviews.subscribe/');
		DeleteDirFilesEx('/bitrix/components/api/reviews.element.rating/');
		DeleteDirFilesEx('/bitrix/components/api/reviews.detail/');
		DeleteDirFilesEx('/bitrix/components/api/reviews.recent/');
		DeleteDirFilesEx('/bitrix/components/api/reviews.user/');
		DeleteDirFilesEx('/bitrix/components/api/reviews.filter/');

		DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID . '/');
		DeleteDirFilesEx('/bitrix/images/' . $this->MODULE_ID . '/');

		return true;
	}

	function InstallEvents()
	{
		//ѕолучим все €зыки сайта дл€ прив€зки типов
		$arLangs = array();
		$rsLangs = CLanguage::GetList($by = "lid", $order = "desc", Array("ACTIVE" => "Y"));
		while($row = $rsLangs->Fetch()) {
			$arLangs[] = $row['LID'];
		}

		//ƒобавл€ем типы почтовых событий
		$eventType   = new CEventType;
		$arEventType = (array)Loc::getMessage('ARII_EVENT_TYPE');
		foreach($arEventType as $arFields) {
			foreach($arLangs as $LID) {
				$arFields['LID'] = $LID;
				$eventType->Add($arFields);
			}
		}
		unset($arFields);


		//ѕолучим все активные сайты дл€ прив€зки к почтовым шаблонам
		$arSiteId = array();
		$rsSites  = \Bitrix\Main\SiteTable::getList(array(
			 'select' => array('LID'),
			 'filter' => array('=ACTIVE' => 'Y'),
		));
		while($row = $rsSites->fetch())
			$arSiteId[] = $row['LID'];


		//ƒобавл€ем почтовые шаблоны
		$eventM         = new CEventMessage;
		$arEventMessage = (array)Loc::getMessage('ARII_EVENT_MESSAGE');

		foreach($arEventMessage as $arFields) {
			$arFields['LID'] = $arSiteId;
			$eventM->Add($arFields);
		}

		return true;
	}

	function UnInstallEvents($arParams = array())
	{
		$eventType   = new CEventType;
		$eventM      = new CEventMessage;
		$arEventType = (array)Loc::getMessage('ARII_EVENT_TYPE');
		foreach($arEventType as $arFields) {

			$rsMess = $eventM->GetList($by = 'id', $order = 'desc', array('TYPE_ID' => $arFields['EVENT_NAME']));
			while($arEM = $rsMess->Fetch()) {
				$eventM->Delete($arEM['ID']);
			}

			$eventType->Delete($arFields['EVENT_NAME']);
		}
	}
}
?>