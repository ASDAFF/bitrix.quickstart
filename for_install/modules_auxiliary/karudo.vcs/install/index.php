<?
global $MESS;
$strPath2Module = str_replace("\\", "/", __FILE__);
$strPath2Module = substr($strPath2Module, 0, strlen($strPath2Module) - strlen("/install/index.php"));
include(GetLangFileName($strPath2Module . "/lang/", "/install/index.php"));
include_once($strPath2Module . '/config.php');

class karudo_vcs extends CModule
{
	var $MODULE_ID = "karudo.vcs";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	var $errors = false;

	public function karudo_vcs() {
		$this->__construct();
	}

	public function __construct() {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path . "/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("SCOM_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SCOM_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("SPER_PARTNER");
		$this->PARTNER_URI = GetMessage("PARTNER_URI");
	}

	public function InstallDB($install_wizard = true) {
		global $DB, $APPLICATION;

		$this->errors = $DB->RunSQLBatch(dirname(__FILE__) . '/db/mysql/install.sql');
		if($this->errors !== false) {
			$APPLICATION->ThrowException(implode("", $this->errors));
			return false;
		}

		RegisterModule($this->MODULE_ID);
		$this->SetupDrivers();
		return true;
	}

	public function UnInstallDB($arParams = false) {
		global $DB;
		$DB->RunSQLBatch(dirname(__FILE__) . '/db/mysql/uninstall.sql');

		UnRegisterModule($this->MODULE_ID);
		COption::RemoveOption($this->MODULE_ID);
		return true;
	}

	public function InstallFiles() {
		CopyDirFiles(
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . CVCSConfig::MODULE_ID .'/install/admin',
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin',
			true);
		CopyDirFiles(
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . CVCSConfig::MODULE_ID .'/install/tools',
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools',
			true,
			true);
		CopyDirFiles(
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . CVCSConfig::MODULE_ID .'/install/js',
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/js',
			true,
			true);
		CopyDirFiles(
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . CVCSConfig::MODULE_ID .'/install/themes',
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes',
			true,
			true);

		return true;
	}

	public function UnInstallFiles() {
		DeleteDirFiles(
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . CVCSConfig::MODULE_ID .'/install/admin',
			$_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
		DeleteDirFilesEx('/bitrix/tools/'.CVCSConfig::MODULE_ID);
		DeleteDirFilesEx('/bitrix/js/'.CVCSConfig::MODULE_ID);
		DeleteDirFilesEx('/bitrix/themes/.default/'.CVCSConfig::MODULE_ID);
		return true;
	}

	public function DoInstall() {
		if ($this->InstallDB(false)) {


			$this->InstallFiles();
		}
	}

	public function DoUninstall() {
		if ($this->UnInstallDB()) {
			$this->UnInstallFiles();
		}
	}

	public function SetupDrivers() {
		global $DB;
		$arExts = array('.php', '.js', '.css', '.htaccess', '.xml', '.html', '.htm');

		$arDrivers = array(
			'files_kernel' => array(
				'name' => GetMessage('VCS_DRIVERNAME_KERNEL'),
			)
		);
		$arDriversOptions = array(
			'files_kernel' => array(
				'is_full_path' => 1,
				'site' => '',
				'doc_root' => $_SERVER['DOCUMENT_ROOT'] . '/bitrix',
				'extensions' => $arExts,
				'included_dirs' => array(
					'/templates',
					'/components',
					'/php_interface',
				),
				'excluded_dirs' => array(
					'/modules/',
					'/components/bitrix/',
					'/php_interface/dbconn.php',
				),
			),
		);

		$kernel_doc_root = '';

		$rs = CSite::GetList($b, $o);
		while ($arr = $rs->Fetch()) {
			if (empty($kernel_doc_root)) {
				$kernel_doc_root = $arr['ABS_DOC_ROOT'] . '/bitrix';
			}
			if ($arr['DEF'] == 'Y') {
				$kernel_doc_root = $arr['ABS_DOC_ROOT'] . '/bitrix';
			}

			$code = 'files_site_' . $arr['LID'];

			$arDriversOptions[$code] = array(
				'is_full_path' => 0,
				'site' => $arr['LID'],
				'doc_root' => '/',
				'extensions' => $arExts,
				'included_dirs' => array(),
				'excluded_dirs' => array(
					'/upload/',
					'/bitrix/',
				),
			);

			$arDrivers[$code] = array(
				'name' => GetMessage('VCS_DRIVERNAME_SITE', array(
					'#NAME#' => $arr['SITE_NAME'],
					'#CODE#' => $arr['LID']
				))
			);
		}

		if (!empty($kernel_doc_root)) {
			$arDriversOptions['files_kernel']['doc_root'] = $kernel_doc_root;
		}

		foreach ($arDriversOptions as $code => $arSettings) {
			$DB->Add(CVCSConfig::TBL_DRIVERS, array(
				'DRIVER_CODE' => $code,
				'NAME' => $arDrivers[$code]['name'],
				'ACTIVE' => 1,
				'SETTINGS' => serialize($arSettings),
				'~TIMESTAMP_X' => $DB->GetNowFunction(),
			));
		}

	}
}
?>