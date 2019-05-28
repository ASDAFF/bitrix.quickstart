<?

/** @global CMain $APPLICATION */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Application;

require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/admitad.tracking/lib/vendor/autoload.php');
Loc::loadMessages(__FILE__);

Class admitad_tracking extends CModule
{
	var $exclusionAdminFiles;
	var $MODULE_ID = 'admitad.tracking';

	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__ . "/version.php");

		$this->exclusionAdminFiles = array(
			'..',
			'.',
			'menu.php',
		);

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("ADMITAD_TRACKING_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("ADMITAD_TRACKING_MODULE_DESCRIPTION");

		$this->PARTNER_NAME = Loc::getMessage("ADMITAD_TRACKING_MODULE_PARTNER_NAME");
		$this->PARTNER_URI = Loc::getMessage("ADMITAD_TRACKING_MODULE_PARTNER_URI");

		$this->MODULE_SORT = 1;
		$this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
		$this->MODULE_GROUP_RIGHTS = "Y";
	}

	public function GetPath($notDocumentRoot = false)
	{
		if ($notDocumentRoot) {
			return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
		} else {
			return dirname(__DIR__);
		}
	}

	public function isVersionD7()
	{
		return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
	}

	function InstallDB()
	{
		global $APPLICATION, $arResult;
		if (!\Admitad\Tracking\Admitad\AdmitadOrder::addOrderUidProperty()
			|| !\Admitad\Tracking\Admitad\AdmitadOrder::addUserUidProperty()
			|| !\Admitad\Tracking\Admitad\AdmitadOrder::addUserUidLifeTimeProperty()
		) {
			$arResult['errCode'] = 'ADMITAD_TRACKING_MASTER_STEP2_INSTALL_DB_ERROR';
			$APPLICATION->IncludeAdminFile(Loc::getMessage("ADMITAD_TRACKING_INSTALL_TITLE"), $this->GetPath() . "/install/step2.php");

			return;
		}
	}

	function UnInstallDB()
	{
		Option::delete($this->MODULE_ID);

		return true;
	}

	function InstallEvents()
	{
		\Bitrix\Main\EventManager::getInstance()->registerEventHandler('main', 'OnBeforeProlog', $this->MODULE_ID, '\Admitad\Tracking\Event', 'OnBeforeProlog');
		\Bitrix\Main\EventManager::getInstance()->registerEventHandler('sale', 'OnSaleOrderSaved', $this->MODULE_ID, '\Admitad\Tracking\Event', 'OnSaleOrderSaved');
	}

	function UnInstallEvents()
	{
		\Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler('main', 'OnBeforeProlog', $this->MODULE_ID, '\Admitad\Tracking\Event', 'OnBeforeProlog');
		\Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler('sale', 'OnSaleOrderSaved', $this->MODULE_ID, '\Admitad\Tracking\Event', 'OnSaleOrderSaved');
	}

	function InstallFiles()
	{
		CopyDirFiles(
			Bitrix\Main\Application::getDocumentRoot() . "/bitrix/modules/" . $this->MODULE_ID . "/install/public/admitad",
			Bitrix\Main\Application::getDocumentRoot() . "/admitad",
			true, true
		);

		\Admitad\Tracking\Admitad\AdmitadRevision::addRevisionPathRule();

		return true;
	}

	function UnInstallFiles()
	{

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $arResult;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		$step = intval($request['step']);

		Loader::registerAutoLoadClasses($this->MODULE_ID, array(
			\Admitad\Tracking\Admitad\Admitad::class         => 'lib/admitad/admitad.php',
			\Admitad\Tracking\Admitad\AdmitadOrder::class    => 'lib/admitad/admitadorder.php',
			\Admitad\Tracking\Admitad\AdmitadRevision::class => 'lib/admitad/admitadrevision.php',
			\Admitad\Tracking\Event::class                   => 'lib/event.php',
		));

		if ($this->isVersionD7()) {

			if ($step <= 1) {

				$errors = $this->checkRequirements();
				$APPLICATION->IncludeAdminFile(Loc::getMessage("ADMITAD_TRACKING_INSTALL_TITLE"), $this->GetPath() . "/install/step1.php");
			} elseif ($step == 2) {

				if ($this->checkRequirements()) {
					$APPLICATION->IncludeAdminFile(Loc::getMessage("ADMITAD_TRACKING_INSTALL_TITLE"), $this->GetPath() . "/install/step1.php");

					return;
				}

				$arResult['PARAM_NAME'] = \Admitad\Tracking\Admitad\Admitad::PARAM_NAME;
				$APPLICATION->IncludeAdminFile(Loc::getMessage("ADMITAD_TRACKING_INSTALL_TITLE"), $this->GetPath() . "/install/step2.php");

				return;
			} elseif ($step == 3) {

				$arResult['PARAM_NAME'] = \Admitad\Tracking\Admitad\Admitad::PARAM_NAME;
				$arResult = array_merge($arResult, [
					'CLIENT_ID'     => $request['client_id'],
					'CLIENT_SECRET' => $request['client_secret'],
					'PARAM_NAME'    => $request['param_name'],
				]);
				if (!empty($request['client_id']) and !empty($request['client_secret']) and !empty($request['param_name'])) {
					try {
						$admitad = new \Admitad\Tracking\Admitad\Admitad();
						$admitad
							->setClientId($request['client_id'])
							->setClientSecret($request['client_secret'])
							->setParamName($request['param_name']);
						$response = $admitad->authorizeClient();
					} catch (Exception $e) {
						$arResult['errCode'] = 'ADMITAD_TRACKING_MASTER_STEP2_AUTH_ERROR';
						$APPLICATION->IncludeAdminFile(Loc::getMessage("ADMITAD_TRACKING_INSTALL_TITLE"), $this->GetPath() . "/install/step2.php");

						return;
					}
				} else {
					$arResult['errCode'] = 'ADMITAD_TRACKING_MASTER_STEP2_EMPTY_DATA_ERROR';
					$APPLICATION->IncludeAdminFile(Loc::getMessage("ADMITAD_TRACKING_INSTALL_TITLE"), $this->GetPath() . "/install/step2.php");

					return;
				}

				$this->InstallFiles();
				$this->InstallEvents();
				$this->InstallDB();
				ModuleManager::registerModule($this->MODULE_ID);
				$APPLICATION->IncludeAdminFile(Loc::getMessage("ADMITAD_TRACKING_INSTALL_TITLE"), $this->GetPath() . "/install/step3.php");
			}

		} else {
			$APPLICATION->ThrowException("ADMITAD_TRACKING_INSTALL_ERROR_VERSION");
		}

		$APPLICATION->IncludeAdminFile(Loc::getMessage("ADMITAD_TRACKING_INSTALL_TITLE"), $this->GetPath() . "/install/step1.php");
	}

	function DoUninstall()
	{
		global $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();


		if ($request["step"] < 2) {
			$APPLICATION->IncludeAdminFile(Loc::getMessage("ADMITAD_TRACKING_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep1.php");
		} elseif ($request["step"] == 2) {
			$this->UnInstallFiles();
			$this->UnInstallEvents();

			if ($request["savedata"] != "Y") {
				$this->UnInstallDB();
			}

			ModuleManager::unRegisterModule($this->MODULE_ID);

			$APPLICATION->IncludeAdminFile(Loc::getMessage("ADMITAD_TRACKING_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep2.php");
		}
	}

	function checkRequirements()
	{
		global $arResult;

		$error = false;

		$arResult['checks']['d7']['title'] = Loc::getMessage("ADMITAD_TRACKING_D7_VERSION_TITLE");
		if (!CheckVersion(ModuleManager::getVersion('main'), '14.00.00')) {
			$arResult['checks']['d7']['text'] = Loc::getMessage("ADMITAD_TRACKING_D7_VERSION_ERROR");
			$arResult['checks']['d7']['status'] = 'ERROR';
			$error = true;
		} else {
			$arResult['checks']['d7']['text'] = Loc::getMessage("ADMITAD_TRACKING_D7_VERSION_SUCCESS");
			$arResult['checks']['d7']['status'] = 'SUCCESS';
		}

		$arResult['checks']['sale']['title'] = Loc::getMessage("ADMITAD_TRACKING_SALE_VERSION_TITLE");
		if (!ModuleManager::isModuleInstalled('sale')) {
			$arResult['checks']['sale']['text'] = Loc::getMessage("ADMITAD_TRACKING_SALE_VERSION_ERROR");
			$arResult['checks']['sale']['status'] = 'ERROR';
			$error = true;
		} else {
			$arResult['checks']['sale']['text'] = Loc::getMessage("ADMITAD_TRACKING_SALE_VERSION_SUCCESS");
			$arResult['checks']['sale']['status'] = 'SUCCESS';
		}

		$arResult['checks']['php']['title'] = Loc::getMessage("ADMITAD_TRACKING_PHP_TITLE");
		if (!version_compare(PHP_VERSION, '5.6.0', '>=')) {
			$arResult['checks']['php']['text'] = Loc::getMessage("ADMITAD_TRACKING_PHP_ERROR");
			$arResult['checks']['php']['status'] = 'ERROR';
			$error = true;
		} else {
			$arResult['checks']['php']['text'] = Loc::getMessage("ADMITAD_TRACKING_PHP_SUCCESS");
			$arResult['checks']['php']['status'] = 'SUCCESS';
		}

		$arResult['checks']['curl']['title'] = Loc::getMessage("ADMITAD_TRACKING_CURL_TITLE");
		if (!in_array('curl', get_loaded_extensions())) {
			$arResult['checks']['curl']['text'] = Loc::getMessage("ADMITAD_TRACKING_CURL_ERROR");
			$arResult['checks']['curl']['status'] = 'ERROR';
			$error = true;
		} else {
			$arResult['checks']['curl']['text'] = Loc::getMessage("ADMITAD_TRACKING_CURL_SUCCESS");
			$arResult['checks']['curl']['status'] = 'SUCCESS';
		}

		return $error;
	}

	function GetModuleRightList()
	{
		return array(
			"reference_id" => array("D", "K", "S", "W"),
			"reference"    => array(
				"[D] " . Loc::getMessage("ADMITAD_TRACKING_DENIED"),
				"[K] " . Loc::getMessage("ADMITAD_TRACKING_READ_COMPONENT"),
				"[S] " . Loc::getMessage("ADMITAD_TRACKING_WRITE_SETTINGS"),
				"[W] " . Loc::getMessage("ADMITAD_TRACKING_FULL"),
			),
		);
	}
}

?>