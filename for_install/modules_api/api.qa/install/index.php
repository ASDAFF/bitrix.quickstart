<?
use Bitrix\Main\ModuleManager,
	 Bitrix\Main\EventManager,
	 Bitrix\Main\Localization\Loc;


Loc::loadMessages(__FILE__);

Class api_qa extends CModule
{
	var $MODULE_ID           = 'api.qa';
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
		$this->MODULE_NAME         = GetMessage("api.qa_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("api.qa_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("api.qa_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("api.qa_PARTNER_URI");
	}

	function DoInstall()
	{
		global $APPLICATION;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$this->InstallFiles();
		$this->InstallDB();
		$this->InstallEvents();

		ModuleManager::registerModule($this->MODULE_ID);

		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/images/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/'. $this->MODULE_ID, true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/css/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css/'. $this->MODULE_ID, true, true);

		return true;
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;

		$errors = null;
		if(!$DB->Query("SELECT 'x' FROM `api_qa_question`", true))
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/install.sql');

		if(!empty($errors)) {
			$APPLICATION->ThrowException(implode('', $errors));
			return false;
		}

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
		$arEventType = (array)Loc::getMessage('AQAII_EVENT_TYPE');
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
		$arEventMessage = (array)Loc::getMessage('AQAII_EVENT_MESSAGE');

		foreach($arEventMessage as $arFields) {
			$arFields['LID'] = $arSiteId;
			$eventM->Add($arFields);
		}

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		if($APPLICATION->GetGroupRight('main') < 'W')
			return false;

		$step = intval($step);
		if($step < 2)
			$APPLICATION->IncludeAdminFile(Loc::getMessage('IBLOCK_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/unstep1.php');
		else {
			$arParams = array(
				 'savedata' => $_REQUEST['savedata'],
			);

			$this->UnInstallDB($arParams);
			$this->UnInstallFiles();
			$this->UnInstallEvents();

			ModuleManager::unRegisterModule($this->MODULE_ID);
		}

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

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/');

		DeleteDirFilesEx('/bitrix/css/' . $this->MODULE_ID . '/');
		DeleteDirFilesEx('/bitrix/images/' . $this->MODULE_ID . '/');

		DeleteDirFilesEx('/bitrix/components/api/qa.list/');
		DeleteDirFilesEx('/bitrix/components/api/qa.recent/');

		return true;
	}

	function UnInstallEvents($arParams = array())
	{
		$eventType   = new CEventType;
		$eventM      = new CEventMessage;
		$arEventType = (array)Loc::getMessage('AQAII_EVENT_TYPE');
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