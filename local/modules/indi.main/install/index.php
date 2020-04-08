<?php
/**
 * Individ module
 *
 * @category	Individ
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (class_exists('indi_main')) {
	return;
}

class indi_main extends CModule
{
	/**
	 * ID модуля
	 * @var string
	 */
	public $MODULE_ID = 'indi.main';

	/**
	 * Версия модуля
	 * @var string
	 */
	public $MODULE_VERSION = '';

	/**
	 * Дата выхода версии
	 * @var string
	 */
	public $MODULE_VERSION_DATE = '';

	/**
	 * Название модуля
	 * @var string
	 */
	public $MODULE_NAME = '';

	/**
	 * Описание модуля
	 * @var string
	 */
	public $MODULE_DESCRIPTION = '';

	/**
	 * Таблица стилей модуля
	 * @var string
	 */
	public $MODULE_CSS = '';

	/**
	 * Список обработчиков, устанавливаемых модулем
	 * @var array
	 */
	protected $eventHandlers = array();

	/**
	 * Конструктор модуля "Индивид"
	 *
	 * @return void
	 */
	public function __construct()
	{
		$version = include __DIR__ . '/version.php';

		$this->MODULE_VERSION = $version['VERSION'];
		$this->MODULE_VERSION_DATE = $version['VERSION_DATE'];

		$this->MODULE_NAME = Loc::getMessage('INDI_MAIN_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('INDI_MAIN_MODULE_DESC');

		$this->PARTNER_NAME = Loc::getMessage('INDI_MAIN_PARTNER_NAME');
		$this->PARTNER_URI = Loc::getMessage('INDI_MAIN_PARTNER_URI');

		$this->eventHandlers = array(
			array(
				'main',
				'OnPageStart',
				'\Indi\Main\Module',
				'onPageStart',
			),
			array(
				'main',
				'OnUserTypeBuildList',
				'\Indi\Main\UserField\User',
				'GetUserTypeDescription',
			),
			array(
				'iblock',
				'OnIBlockPropertyBuildList',
				'\Indi\Main\Iblock\Property\Image',
				'getUserTypeDescription',
			),
			array(
				'iblock',
				'OnIBlockPropertyBuildList',
				'\Indi\Main\Iblock\Property\TopSection',
				'getUserTypeDescription',
			),
			array(
				'search',
				'BeforeIndex',
				'\Indi\Main\Search',
				'onBeforeIndex',
			),
		);
	}

	/**
	 * Устанавливает данные модуля в БД
	 *
	 * @return boolean
	 */
	public function installDB()
	{
		global $DB;

		$DB->RunSQLBatch(__DIR__ . '/sql/install.sql');

		return true;
	}

	/**
	 * Удаляет таблицы модуля
	 *
	 * @return boolean
	 */
	public function unInstallDB()
	{
		global $DB;

		$DB->RunSQLBatch(__DIR__ . '/sql/uninstall.sql');

		return true;
	}

	/**
	 * Устанавливает события модуля
	 *
	 * @return boolean
	 */
	public function installEvents()
	{
		$eventManager = \Bitrix\Main\EventManager::getInstance();

		foreach($this->eventHandlers as $handler) {
			$eventManager->registerEventHandler(
				$handler[0],
				$handler[1],
				$this->MODULE_ID,
				$handler[2],
				$handler[3]
			);
		}

		return true;
	}

	/**
	 * Удаляет события модуля
	 *
	 * @return boolean
	 */
	public function unInstallEvents()
	{
		$eventManager = \Bitrix\Main\EventManager::getInstance();

		foreach($this->eventHandlers as $handler) {
			$eventManager->unRegisterEventHandler(
				$handler[0],
				$handler[1],
				$this->MODULE_ID,
				$handler[2],
				$handler[3]
			);
		}

		return true;
	}

	/**
	 * Устанавливает файлы модуля
	 *
	 * @return boolean
	 */
	public function installFiles($arParams = array())
	{
		$moduleDir = explode('/', __DIR__);
		array_pop($moduleDir);
		$moduleDir = implode('/', $moduleDir);

		$sourceRoot = $moduleDir . '/install/';
		$targetRoot = $_SERVER['DOCUMENT_ROOT'];

		$parts = array(
			'cron' => array(
				'target' => '/local/cron',
				'rewrite' => false,
			),
			'components' => array(
				'target' => '/local/components',
				'rewrite' => true,
			),
			'doc_root' => array(
				'target' => '',
				'rewrite' => false,
			),
			'php_interface' => array(
				'target' => '/bitrix/php_interface',
				'rewrite' => false,
			),
			'templates' => array(
				'target' => '/local/templates',
				'rewrite' => false,
			),
            'admin' => array(
				'target' => '/bitrix/admin',
				'rewrite' => false,
			),
            'js' => array(
                'target' => '/bitrix/js',
                'rewrite' => false,
            )

		);
		foreach ($parts as $dir => $config) {
			CopyDirFiles(
				$sourceRoot . $dir,
				$targetRoot . $config['target'],
				$config['rewrite'],
				true
			);
		}

		return true;
	}

	/**
	 * Удаляет файлы модуля
	 *
	 * @return boolean
	 */
	public function unInstallFiles()
	{
		return true;
	}

	/**
	 * Устанавливает модуль
	 *
	 * @return void
	 */
	public function DoInstall()
	{
		if ($this->installDB()
			&& $this->installEvents()
			&& $this->installFiles()
		) {
			\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
		}
	}

	/**
	 * Удаляет модуль
	 *
	 * @return void
	 */
	public function DoUninstall()
	{
		$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
		if ($request->getPost('confirm') != 'Y') {
			$view = new \Indi\Main\Mvc\View\Php('module/uninstall.php', array(
				'title' => Loc::getMessage('INDI_MAIN_UNINSTALL_NAME'),
				'question' => Loc::getMessage('INDI_MAIN_MODULE_UNINSTALL_QUESTION'),
				'yes' => Loc::getMessage('INDI_MAIN_MODULE_UNINSTALL_YES'),
				'no' => Loc::getMessage('INDI_MAIN_MODULE_UNINSTALL_NO'),
			));
			die($view->render());
		}

		if ($this->unInstallDB()
			&& $this->unInstallEvents()
			&& $this->unInstallFiles()
		) {
			\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
		}
	}
}