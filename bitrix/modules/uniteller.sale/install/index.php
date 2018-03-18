<?php
/**
 * Обязательный файл с описанием модуля, содержащий инсталлятор/деинсталлятор модуля.
 * @author r.smoliarenko
 * @author r.sarazhyn
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/prolog.php'); // пролог модуля

/**
 * Класс для инсталляции и деинсталляции модуля uniteller.sale.
 * @author r.smoliarenko
 * @author r.sarazhyn
 *
 */
class uniteller_sale extends CModule {
	// Обязательные свойства.
	/**
	 * Имя партнера - автора модуля.
	 * @var string
	 */
	var $PARTNER_NAME;
	/**
	 * URL партнера - автора модуля.
	 * @var string
	 */
	var $PARTNER_URI;
	/**
	 * Версия модуля.
	 * @var string
	 */
	var $MODULE_VERSION;
	/**
	 * Дата и время создания модуля.
	 * @var string
	 */
	var $MODULE_VERSION_DATE;
	/**
	 * Имя модуля.
	 * @var string
	 */
	var $MODULE_NAME;
	/**
	 * Описание модуля.
	 * @var string
	 */
	var $MODULE_DESCRIPTION;
	/**
	 * Массив с путями для инсталляции модуля.
	 * @var array
	 */
	var $aPaths;
	/**
	 * ID модуля.
	 * @var string
	 */
	var $MODULE_ID = 'uniteller.sale';

	/**
	 * Конструктор класса. Задаёт начальные значения свойствам.
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
	 * Устанавливает модуль.
	 */
	function DoInstall() {
		global $APPLICATION, $DB;

		$GLOBALS['errors'] = false;
		$this->errors = false;

		// Создаёт таблицу в БД.
		$DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/install/db/mysql/install.sql');

		// Копирует нужные файлы в нужные места.
		if (!CModule::IncludeModule($this->MODULE_ID)) {
			$this->InstallFiles();
			RegisterModule($this->MODULE_ID);

			// Создаёт агента
			CAgent::AddAgent('CUnitellerAgent::UnitellerAgent();', $this->MODULE_ID, 'Y', 60, '', 'Y', '', 0);
		}

		$GLOBALS['errors'] = $this->errors;

		// Показывает страницу с результатом установки модуля.
		$APPLICATION->IncludeAdminFile(GetMessage('UNITELLER.SALE_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/install/step_ok.php');
	}

	/**
	 * Удаляет модуль.
	 */
	function DoUninstall() {
		global $APPLICATION, $uninstall;

		if (isset($uninstall) && $uninstall == 'Y' && CModule::IncludeModule($this->MODULE_ID)) {
			$this->UnInstallFiles();

			// Удаляет агента
			CAgent::RemoveAgent('CUnitellerAgent::UnitellerAgent();', $this->MODULE_ID);
			UnRegisterModule($this->MODULE_ID);

			// Удаляет таблицу из БД, если пользователь решил удалить её.
			$this->UnInstallDB(array(
				'savedata' => $_REQUEST['savedata'],
			));
		} else {
			// Показывает страницу с настройками удаления модуля.
			$APPLICATION->IncludeAdminFile(GetMessage('UNITELLER.SALE_INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/uniteller.sale/install/unstep_ok.php');
		}
	}

	/**
	 * Копирует файлы модуля в нужные места.
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
	 * Удаляет файлы модуля отовсюду.
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
	 * Удаляет таблицу из БД.
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