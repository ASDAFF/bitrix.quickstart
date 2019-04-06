<?
/**
 * Подключаем языковые константы инсталятора
 */
global $MESS;
IncludeModuleLangFile(__FILE__);

class uniplug_logviewer extends CModule {

	const MODULE_ID = 'uniplug.logviewer';
	var $MODULE_ID = 'uniplug.logviewer';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';


	/**
	 * Инициализация модуля для страницы 'Управление модулями'
	 */
	public function __construct() {
		/** @var array $arModuleVersion */
		include(dirname( __FILE__ ) . '/version.php');
		$this->MODULE_NAME           = GetMessage( 'UNIPLUG_LOGVIEWER_MODULE_NAME' );
		$this->MODULE_DESCRIPTION    = GetMessage( 'UNIPLUG_LOGVIEWER_MODULE_DESC' );
		$this->MODULE_VERSION      	 = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE   = $arModuleVersion['VERSION_DATE'];
		$this->PARTNER_NAME          = "UniPlug Ltd.";
		$this->PARTNER_URI           = "http://uniplug.ru/";
	}

	/**
	 * Устанавливаем модуль
	 */
	public function DoInstall() {
		if(!$this->InstallFiles() ) {
			return false;
		}
		RegisterModule( self::MODULE_ID );

		return true;
	}

	/**
	 * Удаляем модуль
	 */
	public function DoUninstall() {
		global $APPLICATION;
			UnRegisterModule( self::MODULE_ID );
		return true;
	}

	/**
	 * Копируем файлы административной части
	 *
	 * @return bool
	 */
	public function InstallFiles() {
		CopyDirFiles( __DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin' );
		CopyDirFiles( __DIR__ . '/themes', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes', true, true );
		return true;
	}

	/**
	 * Удаляем файлы административной части
	 *
	 * @return bool
	 */
	public function UnInstallFiles() {
		DeleteDirFiles( __DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin' );
		DeleteDirFiles( __DIR__ . '/themes', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes' );

		return true;
	}

}

