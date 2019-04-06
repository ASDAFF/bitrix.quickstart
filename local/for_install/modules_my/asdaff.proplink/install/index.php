<?
IncludeModuleLangFile(__FILE__);

Class asdaff_proplink extends CModule {

	var $MODULE_ID = 'asdaff.proplink';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	public function __construct() {
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path . '/version.php');

		$this->MODULE_VERSION      = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

		$this->MODULE_NAME         = GetMessage($this->MODULE_ID . '_MODULE_NAME');
		$this->MODULE_DESCRIPTION  = GetMessage($this->MODULE_ID . '_MODULE_DESC');

		$this->PARTNER_NAME        = GetMessage('asdaff.proplink_PARTNER_NAME');
		$this->PARTNER_URI         = GetMessage('asdaff.proplink_PARTNER_URI');
	}

	public function InstallDB($arParams = array()){

		global $DB, $DBType;

		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/" . $this->MODULE_ID . "/install/db/" . $DBType . "/install.sql");

		return true;
	}

	public function UnInstallDB($arParams = array()){

		global $DB, $DBType;

		$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/" . $this->MODULE_ID . "/install/db/" . $DBType . "/uninstall.sql");

		return true;
	}

	public function InstallEvents(){
		return true;
	}

	public function UnInstallEvents() {
		return true;
	}

	public function InstallFiles($arParams = array()){
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', TRUE, TRUE);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/' . $this->MODULE_ID . '/install/tools/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools', TRUE, TRUE);

		CopyDirFiles(
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/' . $this->MODULE_ID . '/install/js',
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/js/' . $this->MODULE_ID . '/',
			$rewrite = TRUE,
			$recursive = TRUE
		);

		CopyDirFiles(
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/' . $this->MODULE_ID . '/install/panel',
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/panel/' . $this->MODULE_ID . '/',
			$rewrite = TRUE,
			$recursive = TRUE
		);

		return true;
	}

	public function UnInstallFiles() {
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/' . $this->MODULE_ID . '/install/tools/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools');

		DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID);
		DeleteDirFilesEx('/bitrix/panel/' . $this->MODULE_ID);
		return true;
	}

	public function DoInstall() {

		global $APPLICATION;

		RegisterModule($this->MODULE_ID);
		$this->InstallFiles();
		$this->InstallDB();
	}

	public function DoUninstall() {

		global $APPLICATION, $step;

		UnRegisterModule($this->MODULE_ID);
		$this->UnInstallFiles();
		$this->UnInstallDB();
	}
}
?>
