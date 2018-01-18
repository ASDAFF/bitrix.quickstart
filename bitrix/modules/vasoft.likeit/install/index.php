<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

class vasoft_likeit extends CModule
{
	var $MODULE_ID = "vasoft.likeit";
	
	private static $arTables = array(
		'\Vasoft\Likeit\LikeTable'
	);
	private static $execlusionAdminFiles = array(
		'.',
		'..',
		'menu.php'
	);

	/**
	 * vasoft_likeit constructor.
	 */
	public function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__ . '/version.php');
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = Loc::getMessage('VASOFT_LIKEIT_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('VASOFT_LIKEIT_MODULE_DESCRIPTION');
		$this->PARTNER_NAME = 'VASoft';
		$this->PARTNER_URI = 'https://va-soft.ru/';

		$this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
		$this->MODULE_GROUP_RIGHTS = 'Y';
	}

	public function DoInstall()
	{
		global $APPLICATION;
		if (!\Bitrix\Main\Loader::includeModule('iblock')) {
			$APPLICATION->ThrowException(Loc::getMessage('VASOFT_LIKEIT_NEED_IBLOCK'));
		} elseif (self::isVersionD7()) {
			\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
			$this->installFiles();
			$this->installDB();
			$this->registerDependences();
		} else {
			$APPLICATION->ThrowException(Loc::getMessage("VASOFT_LIKEIT_NEED_D7"));
		}
	}

	public function DoUninstall()
	{
		global $APPLICATION;
		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		if ($request['step'] < 2) {
			$APPLICATION->IncludeAdminFile(Loc::getMessage("VASOFT_LIKEIT_MODULE_REMOVING"), self::GetPath() . '/install/unstep1.php');
		} elseif ($request['step'] == 2) {
			self::unRegisterDependences();
			self::unInstallFiles();
			if ($request['savedata'] != 'Y') {
				self::unInstallDB();
			} else {
				\Bitrix\Main\Loader::includeModule($this->MODULE_ID);
				\Vasoft\Likeit\LikeTable::dropIndexes();
			}
			\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
			$APPLICATION->IncludeAdminFile(Loc::getMessage("VASOFT_LIKEIT_MODULE_REMOVING"), self::GetPath() . '/install/unstep2.php');
		}
	}

	public static function isVersionD7()
	{
		return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
	}

	public function installDB()
	{
		\Bitrix\Main\Loader::includeModule($this->MODULE_ID);
		foreach (self::$arTables as $tableClass) {
			if (!Application::getConnection($tableClass::getConnectionName())->isTableExists(Base::getInstance($tableClass)->getDBTableName())) {
				Base::getInstance($tableClass)->createDbTable();
			}
		}
		\Vasoft\Likeit\LikeTable::createIndexes();
	}

	public function installFiles()
	{
		CopyDirFiles($this->GetPath().'/install/js',$_SERVER['DOCUMENT_ROOT'].'/bitrix/js',true,true);
		$path = $this->GetPath() . '/tools/';
		$pathDR = $this->GetPath(true) . '/tools/';
		if (Bitrix\Main\IO\Directory::isDirectoryExists($path)) {
			if ($dir = opendir($path)) {
				while (false !== $item = readdir($dir)) {
					if (in_array($item, self::$execlusionAdminFiles)) {
						continue;
					}
					$subName = str_replace('.', '_', $this->MODULE_ID);
					file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . $subName . '_' . $item, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."' . $pathDR . $item . '");?' . '>');
				}
				closedir($dir);
			}
		}
	}

	public function unInstallDB()
	{
		\Bitrix\Main\Loader::includeModule($this->MODULE_ID);
		foreach (self::$arTables as $tableClass) {
			Bitrix\Main\Application::getConnection($tableClass::getConnectionName())->queryExecute('drop table if exists ' . Base::getInstance($tableClass)->getDBTableName());
		}
		\Bitrix\Main\Config\Option::delete($this->MODULE_ID);
	}

	public function unInstallFiles()
	{
		\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/vasoft.likeit/');
		if (Bitrix\Main\IO\Directory::isDirectoryExists($path = self::GetPath() . '/tools')) {
			if ($dir = opendir($path)) {
				while (false !== $item = readdir($dir)) {
					if (in_array($item, self::$execlusionAdminFiles)) {
						continue;
					}
					$subName = str_replace('.', '_', $this->MODULE_ID);
					\Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . $subName . '_' . $item);
				}
				closedir($dir);
			}
		}
	}

	public function registerDependences()
	{
		if (\Bitrix\Main\Loader::includeModule($this->MODULE_ID) &&
			\Bitrix\Main\Loader::includeModule('iblock')
		) {
			/**
			 * @todo по готовности ядра переделать на события D7
			 * На момент разработки события не D7
			 */
			EventManager::getInstance()->registerEventHandler(
				'iblock',
				'OnBeforeIBlockElementDelete',
				$this->MODULE_ID,
				'Vasoft\Likeit\LikeTable', "onBeforeElementDeleteHandler");
		}
	}

	public function unRegisterDependences()
	{
		if (\Bitrix\Main\Loader::includeModule($this->MODULE_ID) &&
			\Bitrix\Main\Loader::includeModule('iblock')
		) {
			/**
			 * @todo по готовности ядра переделать на события D7
			 * На момент разработки события не D7
			 */
			EventManager::getInstance()->unRegisterEventHandler(
				'iblock',
				'OnBeforeIBlockElementDelete',
				$this->MODULE_ID,
				'Vasoft\Likeit\LikeTable', "onBeforeElementDeleteHandler");
		}
	}

	public static function GetPath($notDocumentRoot = false)
	{
		return ($notDocumentRoot)
			? preg_replace('#^(.*)\/(local|bitrix)\/modules#', '/$2/modules', dirname(__DIR__))
			: dirname(__DIR__);
	}
}