<?
use Bitrix\Main\Application;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Mail;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


Class api_orderstatus extends CModule
{
	const MODULE_ID = 'api.orderstatus';
	var $MODULE_ID = 'api.orderstatus';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError  = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION      = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME         = GetMessage("AOS_MODULE_NAME");
		$this->MODULE_DESCRIPTION  = GetMessage("AOS_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("AOS_PARTNER_NAME");
		$this->PARTNER_URI  = GetMessage("AOS_PARTNER_URI");
	}

	function checkDependency()
	{
		$info = CModule::CreateModuleObject('sale');

		if(!$info)
			return false;

		$bSaleValid     = version_compare($info->MODULE_VERSION, '15.00.00', '>=');
		$bSaleInstalled = \Bitrix\Main\ModuleManager::isModuleInstalled('sale');
		$bMainValid     = (defined('SM_VERSION') && version_compare(SM_VERSION, '15.00.00', '>='));


		return (bool)($bSaleValid && $bMainValid && $bSaleInstalled);
	}

	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$eventManager = EventManager::getInstance();

		$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/install.sql');

		if(!empty($errors)) {
			$APPLICATION->ThrowException(implode("", $errors));
			return false;
		}

		RegisterModule(self::MODULE_ID);

		$eventManager->registerEventHandler('main', 'OnAdminTabControlBegin', $this->MODULE_ID, 'CApiOrderStatus', 'initForm');
		$eventManager->registerEventHandler('main', 'OnBeforeEventAdd', $this->MODULE_ID, 'CApiOrderStatus', 'OnBeforeEventAdd');
		$eventManager->registerEventHandler('main', 'OnBeforeEventSend', $this->MODULE_ID, 'CApiOrderStatus', 'OnBeforeEventSend');

		$eventManager->registerEventHandler('sale', 'OnOrderStatusSendEmail', $this->MODULE_ID, 'CApiOrderStatus', 'OnOrderStatusSendEmail');
		$eventManager->registerEventHandler('sale', 'OnOrderNewSendEmail', $this->MODULE_ID, 'CApiOrderStatus', 'OnOrderNewSendEmail');

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$eventManager = EventManager::getInstance();

		$errors = null;
		if(array_key_exists("savedata", $arParams) && $arParams["savedata"] != "Y") {
			$errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/db/' . $DBType . '/uninstall.sql');
			DeleteDirFilesEx('/upload/api.orderstatus/');
			DeleteDirFilesEx('/upload/api_orderstatus/');

			$DB->Query("DELETE FROM `b_file` WHERE `MODULE_ID` = '" . $this->MODULE_ID . "'", true);
			$DB->Query("DELETE FROM `b_event` WHERE `EVENT_NAME` = 'API_ORDERSTATUS' AND `SUCCESS_EXEC` = 'Y'", true);
			//$DB->Query("DELETE FROM `b_option` WHERE `MODULE_ID` = '".$this->MODULE_ID."'", true);
			//$DB->Query("DELETE FROM `b_event_log` WHERE `MODULE_ID` = '".$this->MODULE_ID."'", true);

			if(!empty($errors)) {
				$APPLICATION->ThrowException(implode("", $errors));
				return false;
			}
		}

		$eventManager->unRegisterEventHandler('main', 'OnAdminTabControlBegin', $this->MODULE_ID, 'CApiOrderStatus', 'initForm');
		$eventManager->unRegisterEventHandler('main', 'OnBeforeEventAdd', $this->MODULE_ID, 'CApiOrderStatus', 'OnBeforeEventAdd');
		$eventManager->unRegisterEventHandler('main', 'OnBeforeEventSend', $this->MODULE_ID, 'CApiOrderStatus', 'OnBeforeEventSend');

		$eventManager->unRegisterEventHandler('sale', 'OnOrderStatusSendEmail', $this->MODULE_ID, 'CApiOrderStatus', 'OnOrderStatusSendEmail');
		$eventManager->unRegisterEventHandler('sale', 'OnOrderNewSendEmail', $this->MODULE_ID, 'CApiOrderStatus', 'OnOrderNewSendEmail');

		UnRegisterModule(self::MODULE_ID);

		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		$arFilter = array('EVENT_NAME' => 'API_ORDERSTATUS');

		$arET = Mail\Internal\EventTypeTable::getList(array(
			 'filter' => $arFilter,
		))->fetchAll();

		if($arET) {
			$arEM = Mail\Internal\EventMessageTable::getList(array(
				 'filter' => $arFilter,
			))->fetchAll();

			if($arEM) {
				foreach($arEM as $arMess) {
					Mail\Internal\EventMessageTable::delete($arMess['ID']);
				}
			}

			if($arET) {
				foreach($arET as $arType) {
					Mail\Internal\EventTypeTable::delete($arType['ID']);
				}
			}
		}

		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/" . $this->MODULE_ID, true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx('/bitrix/components/api/orderstatus.block.header/');
		DeleteDirFilesEx('/bitrix/components/api/orderstatus.block.buyer/');
		DeleteDirFilesEx('/bitrix/components/api/orderstatus.block.shipment/');
		DeleteDirFilesEx('/bitrix/components/api/orderstatus.block.payment/');
		DeleteDirFilesEx('/bitrix/components/api/orderstatus.block.basket/');
		DeleteDirFilesEx('/bitrix/components/api/orderstatus.block.footer/');
		DeleteDirFilesEx('/bitrix/components/api/orderstatus.block.finance/');
		DeleteDirFilesEx('/bitrix/components/api/orderstatus.block.total/');
		DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID . '/');

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");

		return true;
	}

	function InstallDefaults()
	{
		$connection = Application::getConnection();
		$sqlHelper  = $connection->getSqlHelper();

		Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/api.orderstatus/tools/defaults.php');

		//1) AOS_TD_INSTALL_DEFAULTS
		$arOptions = $connection->query('SELECT COUNT(*) CNT FROM api_orderstatus_option')->fetch();
		if(!$arOptions['CNT']) {
			$dbSite = SiteTable::getList(array(
				 'select' => array('*'),
				 'filter' => array('ACTIVE' => 'Y'),
			));

			while($arSite = $dbSite->fetch()) {
				$arDefOptions = Loc::getMessage('AOS_TD_INSTALL_DEFAULTS');

				if($arSite['SITE_NAME'])
					$arDefOptions['SALE_NAME'] = $arSite['SITE_NAME'];

				if($arSite['SERVER_NAME'])
					$arDefOptions['SALE_URL'] = (CMain::IsHTTPS() ? "https://" : "http://") . $arSite['SERVER_NAME'];

				if($arSite['EMAIL'])
					$arDefOptions['SALE_EMAIL'] = $arSite['EMAIL'];

				foreach($arDefOptions as $key => $val) {
					$sql = "INSERT INTO `api_orderstatus_option` 
								SET `NAME` = '" . $sqlHelper->forSql($key) . "', 
									 `VALUE` = '" . $sqlHelper->forSql($val) . "', 
									 `SITE_ID` = '" . $sqlHelper->forSql($arSite['LID']) . "'";

					$connection->query($sql);
				}
			}
		}

		//2) AOS_TD_INSTALL_DEFAULTS_GATEWAY
		if($connection->isTableExists('api_orderstatus_sms_gateway')){
			if($arSmsGateway = (array)Loc::getMessage('AOS_TD_INSTALL_DEFAULTS_GATEWAY')){
				foreach($arSmsGateway as $code=>$values){
					$sql = "SELECT * FROM `api_orderstatus_sms_gateway` WHERE `NAME`='". trim($code) ."'";
					$rsGateway = $connection->query($sql);
					if(!$rsGateway->fetch()){
						$sql = "INSERT INTO `api_orderstatus_sms_gateway` (`NAME`, `ACTIVE`, `SORT`, `PARAMS`, `DATE_MODIFY`, `MODIFIED_BY`) VALUES {$values}";
						$connection->query($sql);
					}
				}
			}
		}
	}

	function DoInstall()
	{
		global $APPLICATION;

		if(!$this->checkDependency()) {
			$APPLICATION->IncludeAdminFile(GetMessage('AOS_MODULE_NAME'), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/error_dependency.php");

			return false;
		}

		$this->InstallDB();
		$this->InstallFiles();
		$this->InstallEvents();
		$this->InstallDefaults();
	}

	function DoUninstall()
	{

		global $APPLICATION, $step;

		$step = intval($step);
		if($step < 2)
			$APPLICATION->IncludeAdminFile(GetMessage("IBLOCK_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/unstep1.php");
		else {
			$arParams = array(
				 "savedata" => $_REQUEST["savedata"],
			);

			$this->UnInstallDB($arParams);
			$this->UnInstallFiles();
			$this->UnInstallEvents();
		}
	}
}

?>