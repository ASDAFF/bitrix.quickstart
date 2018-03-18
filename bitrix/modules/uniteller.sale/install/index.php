<?php
/**
 * ������������ ���� � ��������� ������, ���������� �����������/������������� ������.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/prolog.php'); // ������ ������

/**
 * ����� ��� ����������� � ������������� ������ uniteller.sale.
 * @author r.smoliarenko
 * @author r.sarazhyn
 *
 */
class uniteller_sale extends CModule {
	// ������������ ��������.
	/**
	 * ��� �������� - ������ ������.
	 * @var string
	 */
	var $PARTNER_NAME;
	/**
	 * URL �������� - ������ ������.
	 * @var string
	 */
	var $PARTNER_URI;
	/**
	 * ������ ������.
	 * @var string
	 */
	var $MODULE_VERSION;
	/**
	 * ���� � ����� �������� ������.
	 * @var string
	 */
	var $MODULE_VERSION_DATE;
	/**
	 * ��� ������.
	 * @var string
	 */
	var $MODULE_NAME;
	/**
	 * �������� ������.
	 * @var string
	 */
	var $MODULE_DESCRIPTION;
	/**
	 * ������ � ������ ��� ����������� ������.
	 * @var array
	 */
	var $aPaths;
	/**
	 * ID ������.
	 * @var string
	 */
	var $MODULE_ID = 'uniteller.sale';

	/**
	 * ����������� ������. ����� ��������� �������� ���������.
	 */
	function uniteller_sale() {
		$this->PARTNER_NAME = 'Uniteller';
		$this->PARTNER_URI = 'http://www.uniteller.ru';

		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path . '/version.php');

		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = GetMessage('UNITELLER.SALE_INSTALL_NAME');
		if (CModule::IncludeModule($this->MODULE_ID)) {
			$this->MODULE_DESCRIPTION = GetMessage('UNITELLER.SALE_INSTALL_DESCRIPTION');
		} else {
			$this->MODULE_DESCRIPTION = GetMessage('UNITELLER.SALE_PREINSTALL_DESCRIPTION');
		}
		$this->aPaths = array(
			'admin' => '/bitrix/admin',
			'components' => '/bitrix/components',
			'php_interface' => '/bitrix/php_interface',
			'templates' => '/bitrix/templates',
			'personal' => '/personal',
			'cron.bat' => '',
		);
	}

	/**
	 * ������������� ������.
	 */
	function DoInstall() {
		global $APPLICATION, $DB;

		$GLOBALS['errors'] = false;
		$this->errors = false;

		// ������ ������� � ��.
		$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/install/db/mysql/install.sql');

		// �������� ������ ����� � ������ �����.
		if (!CModule::IncludeModule($this->MODULE_ID)) {
			$this->InstallFiles();
			RegisterModule($this->MODULE_ID);

			// ������ ������
			CAgent::AddAgent('CUnitellerAgent::UnitellerAgent();', $this->MODULE_ID, 'Y', 60, '', 'Y', '', 0);
		}

		$GLOBALS['errors'] = $this->errors;

		// ���������� �������� � ����������� ��������� ������.
		$APPLICATION->IncludeAdminFile(GetMessage('UNITELLER.SALE_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/install/step_ok.php');
	}

	/**
	 * ������� ������.
	 */
	function DoUninstall() {
		global $APPLICATION, $uninstall;

		if (isset($uninstall) && $uninstall == 'Y' && CModule::IncludeModule($this->MODULE_ID)) {
			$this->UnInstallFiles();

			// ������� ������
			CAgent::RemoveAgent('CUnitellerAgent::UnitellerAgent();', $this->MODULE_ID);
			UnRegisterModule($this->MODULE_ID);

			// ������� ������� �� ��, ���� ������������ ����� ������� �.
			$this->UnInstallDB(array(
				'savedata' => $_REQUEST['savedata'],
			));
		} else {
			// ���������� �������� � ����������� �������� ������.
			$APPLICATION->IncludeAdminFile(GetMessage('UNITELLER.SALE_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/install/unstep_ok.php');
		}
	}

	/**
	 * �������� ����� ������ � ������ �����.
	 * @return boolean
	 */
	function InstallFiles() {
		$path_from = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/install/www';
		$path_to = $_SERVER['DOCUMENT_ROOT'];

		if (!CopyDirFiles($path_from . $this->aPaths['admin'], $path_to . $this->aPaths['admin'], true, true, false, '.svn')) {
			$this->errors = array(GetMessage('UNITELLER.SALE_INSTALL_ERROR'));
		}
		if (!CopyDirFiles($path_from . $this->aPaths['components'], $path_to . $this->aPaths['components'], true, true, false, '.svn')) {
			$this->errors = array(GetMessage('UNITELLER.SALE_INSTALL_ERROR'));
		}
		if (!CopyDirFiles($path_from . $this->aPaths['php_interface'], $path_to . $this->aPaths['php_interface'], true, true, false, '.svn')) {
			$this->errors = array(GetMessage('UNITELLER.SALE_INSTALL_ERROR'));
		}
		if (!CopyDirFiles($path_from . $this->aPaths['templates'], $path_to . $this->aPaths['templates'], true, true, false, '.svn')) {
			$this->errors = array(GetMessage('UNITELLER.SALE_INSTALL_ERROR'));
		}
		if (!CopyDirFiles($path_from . $this->aPaths['personal'], $path_to . $this->aPaths['personal'], true, true, false, '.svn')) {
			$this->errors = array(GetMessage('UNITELLER.SALE_INSTALL_ERROR'));
		}
		if (!CopyDirFiles($path_from . $this->aPaths['cron.bat'] . '/cron.bat', $path_to . $this->aPaths['cron.bat'] . '/cron.bat')) {
			$this->errors = array(GetMessage('UNITELLER.SALE_INSTALL_ERROR'));
		}

		return true;
	}

	/**
	 * ������� ����� ������ ��������.
	 * @return boolean
	 */
	function UnInstallFiles() {
		$path_from = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/install/www';
		$path_to = $_SERVER['DOCUMENT_ROOT'];

		DeleteDirFiles($path_from . $this->aPaths['admin'], $path_to . $this->aPaths['admin'], array('.svn'));

		DeleteDirFilesEx('/bitrix/components/bitrix/sale.personal.ordercheck');
		DeleteDirFilesEx('/bitrix/components/bitrix/sale.personal.ordercheck.cancel');
		DeleteDirFilesEx('/bitrix/components/bitrix/sale.personal.ordercheck.check');
		DeleteDirFilesEx('/bitrix/components/bitrix/sale.personal.ordercheck.detail');
		DeleteDirFilesEx('/bitrix/components/bitrix/sale.personal.ordercheck.list');

		DeleteDirFilesEx('/bitrix/php_interface/include/sale_payment/uniteller.sale');

		DeleteDirFilesEx('/bitrix/templates/.default/components/bitrix/sale.personal.ordercheck.cancel');
		DeleteDirFilesEx('/bitrix/templates/.default/components/bitrix/sale.personal.ordercheck.check');
		DeleteDirFilesEx('/bitrix/templates/.default/components/bitrix/sale.personal.ordercheck.detail');
		DeleteDirFilesEx('/bitrix/templates/.default/components/bitrix/sale.personal.ordercheck.list');

		DeleteDirFilesEx('/personal/ordercheck');

		DeleteDirFiles($path_from . $this->aPaths['cron.bat'], $path_to . $this->aPaths['cron.bat'], array('bitrix', 'personal', '.svn'));

		return true;
	}

	/**
	 * ������� ������� �� ��.
	 * @return boolean
	 */
	function UnInstallDB($arParams = Array()) {
		if (array_key_exists('savedata', $arParams) && $arParams['savedata'] != 'Y') {
			global $DB;
			$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/install/db/' . strtolower($DB->type) . '/uninstall.sql');
		}

		return true;
	}
}

?>