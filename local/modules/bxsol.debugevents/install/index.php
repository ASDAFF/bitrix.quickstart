<?php

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

if (class_exists("bxsol_debugevents"))
{
	return;
}

Class bxsol_debugevents extends CModule
{

	var $MODULE_ID = 'bxsol.debugevents';
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;

	protected $moduleInstallPath;
	protected $bitrixAdminPath;

	public function __construct()
	{
		$arModuleVersion = array();

		include(__DIR__ . '/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		//fix some bug
		$this->PARTNER_NAME = 'BX Solutions';
		$this->PARTNER_NAME = GetMessage('BXSOL_PARTNER_NAME');
		$this->PARTNER_URI = 'http://bitrixsolutions.ru/';

		$this->MODULE_NAME = GetMessage('BXSOL_DEBUG_EVENTS_INSTALL_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('BXSOL_DEBUG_EVENTS_INSTALL_DESCR');

		$this->moduleInstallPath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install';
		$this->bitrixPath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix';
	}

	protected function GetInstallFiles()
	{
		return array(
			'/admin' => '/admin',
			'/js' => '/js',
			'/themes/.default' => '/themes/.default',
			'/themes/icons' => '/themes/.default/icons',
		);
	}

	public function InstallFiles()
	{

		foreach ($this->GetInstallFiles() as $source => $destination)
		{
			CopyDirFiles($this->moduleInstallPath . $source, $this->bitrixPath . $destination, true, true);
		}

		return true;
	}

	public function UnInstallFiles()
	{
		foreach ($this->GetInstallFiles() as $source => $destination)
		{

			if (is_dir($this->moduleInstallPath . $source))
			{
				$d = dir($this->moduleInstallPath . $source);

				while ($entry = $d->read())
				{
					if ($entry == '.' || $entry == '..')
					{
						continue;
					}

					if (is_dir($this->bitrixPath . $destination . '/' . $entry))
					{
						DeleteDirFilesEx('bitrix' . $destination . '/' . $entry);
					}
					elseif (is_file($this->bitrixPath . $destination . '/' . $entry))
					{
						@unlink($this->bitrixPath . $destination . '/' . $entry);
					}
				}
				$d->close();
			}

		}

		return true;
	}

	public function DoInstall()
	{
		$this->InstallFiles();
		RegisterModule($this->MODULE_ID);
		return true;
	}

	public function DoUninstall()
	{
		$this->UnInstallFiles();
		UnRegisterModule($this->MODULE_ID);
		return true;
	}

}
