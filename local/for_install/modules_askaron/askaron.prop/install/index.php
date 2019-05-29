<? if(class_exists('askaron.prop'))
{
	return;
}

IncludeModuleLangFile(__FILE__);

class askaron_prop extends CModule
{
	var $MODULE_ID = "askaron.prop";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_URI;
	public $PARTNER_NAME;


	public function askaron_prop()
	{
		$arModuleVersion = array();

		$dir = str_replace('\\', '/', __DIR__);
		include($dir . '/version.php');

		if(is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}
		else
		{
			$this->MODULE_VERSION = GetMessage('ASKARON_PROP_MODULE_VERSION');
			$this->MODULE_VERSION_DATE = GetMessage('ASKARON_PROP_MODULE_VERSION_DATE');
		}

		$this->MODULE_NAME = GetMessage('ASKARON_PROP_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ASKARON_PROP_MODULE_DESCRIPTION');
		$this->PARTNER_URI = GetMessage('ASKARON_PROP_MODULE_PARTNER_URL');
		$this->PARTNER_NAME = GetMessage('ASKARON_PROP_MODULE_PARTNER_NAME');
	}

	public function DoInstall()
	{
		$this->InstallModule();
	}

	protected function InstallModule()
	{
		RegisterModule($this->MODULE_ID);

		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Askaron\Prop\Store', 'GetPropertyDescription');
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Askaron\Prop\Price', 'GetPropertyDescription');
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Askaron\Prop\Group', 'GetPropertyDescription');
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Askaron\Prop\Iblock', 'GetPropertyDescription');
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Askaron\Prop\IblockProperty', 'GetPropertyDescription');

		RegisterModuleDependences('catalog', 'OnGroupUpdate', $this->MODULE_ID, '\Askaron\Prop\Price', 'OnGroupUpdate');
		RegisterModuleDependences('catalog', 'OnGroupDelete', $this->MODULE_ID, '\Askaron\Prop\Price', 'OnGroupDelete');
		RegisterModuleDependences('catalog', 'OnGroupAdd', $this->MODULE_ID, '\Askaron\Prop\Price', 'OnGroupAdd');

		RegisterModuleDependences('catalog', 'OnCatalogStoreUpdate', $this->MODULE_ID, '\Askaron\Prop\Store', 'OnCatalogStoreUpdate');
		RegisterModuleDependences('catalog', 'OnCatalogStoreDelete', $this->MODULE_ID, '\Askaron\Prop\Store', 'OnCatalogStoreDelete');
		RegisterModuleDependences('catalog', 'OnCatalogStoreAdd', $this->MODULE_ID, '\Askaron\Prop\Store', 'OnCatalogStoreAdd');

		RegisterModuleDependences('main', 'OnAfterGroupUpdate', $this->MODULE_ID, '\Askaron\Prop\Group', 'OnGroupUpdate');
		RegisterModuleDependences('main', 'OnGroupDelete', $this->MODULE_ID, '\Askaron\Prop\Group', 'OnGroupDelete');
		RegisterModuleDependences('main', 'OnAfterGroupAdd', $this->MODULE_ID, '\Askaron\Prop\Group', 'OnGroupAdd');

		RegisterModuleDependences('iblock', 'OnAfterIBlockUpdate', $this->MODULE_ID, '\Askaron\Prop\Iblock', 'OnIblockUpdate');
		RegisterModuleDependences('iblock', 'OnIBlockDelete', $this->MODULE_ID, '\Askaron\Prop\Iblock', 'OnIblockDelete');
		RegisterModuleDependences('iblock', 'OnAfterIBlockAdd', $this->MODULE_ID, '\Askaron\Prop\Iblock', 'OnIblockAdd');

		RegisterModuleDependences('iblock', 'OnBeforeIBlockPropertyAdd', $this->MODULE_ID, '\Askaron\Prop\IblockProperty', 'OnBeforeIBlockPropertyAdd');
		RegisterModuleDependences('iblock', 'OnBeforeIBlockPropertyUpdate', $this->MODULE_ID, '\Askaron\Prop\IblockProperty', 'OnBeforeIBlockPropertyUpdate');
		RegisterModuleDependences('iblock', 'OnBeforeIBlockPropertyDelete', $this->MODULE_ID, '\Askaron\Prop\IblockProperty', 'OnBeforeIBlockPropertyDelete');

		return true;
	}

	public function DoUninstall()
	{
		$this->UnInstallModule();
	}

	protected function UnInstallModule()
	{
		UnRegisterModule($this->MODULE_ID);

		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Askaron\Prop\Store', 'GetPropertyDescription');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Askaron\Prop\Price', 'GetPropertyDescription');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Askaron\Prop\Group', 'GetPropertyDescription');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Askaron\Prop\Iblock', 'GetPropertyDescription');
		UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Askaron\Prop\IblockPropperty', 'GetPropertyDescription');

		UnRegisterModuleDependences('catalog', 'OnGroupUpdate', $this->MODULE_ID, '\Askaron\Prop\Price', 'OnGroupUpdate');
		UnRegisterModuleDependences('catalog', 'OnGroupDelete', $this->MODULE_ID, '\Askaron\Prop\Price', 'OnGroupDelete');
		UnRegisterModuleDependences('catalog', 'OnGroupAdd', $this->MODULE_ID, '\Askaron\Prop\Price', 'OnGroupAdd');

		UnRegisterModuleDependences('catalog', 'OnCatalogStoreUpdate', $this->MODULE_ID, '\Askaron\Prop\Store', 'OnCatalogStoreUpdate');
		UnRegisterModuleDependences('catalog', 'OnCatalogStoreDelete', $this->MODULE_ID, '\Askaron\Prop\Store', 'OnCatalogStoreDelete');
		UnRegisterModuleDependences('catalog', 'OnCatalogStoreAdd', $this->MODULE_ID, '\Askaron\Prop\Store', 'OnCatalogStoreAdd');

		UnRegisterModuleDependences('main', 'OnAfterGroupUpdate', $this->MODULE_ID, '\Askaron\Prop\Group', 'OnGroupUpdate');
		UnRegisterModuleDependences('main', 'OnGroupDelete', $this->MODULE_ID, '\Askaron\Prop\Group', 'OnGroupDelete');
		UnRegisterModuleDependences('main', 'OnAfterGroupAdd', $this->MODULE_ID, '\Askaron\Prop\Group', 'OnGroupAdd');

		UnRegisterModuleDependences('iblock', 'OnAfterIBlockUpdate', $this->MODULE_ID, '\Askaron\Prop\Iblock', 'OnIblockUpdate');
		UnRegisterModuleDependences('iblock', 'OnIBlockDelete', $this->MODULE_ID, '\Askaron\Prop\Iblock', 'OnIblockDelete');
		UnRegisterModuleDependences('iblock', 'OnAfterIBlockAdd', $this->MODULE_ID, '\Askaron\Prop\Iblock', 'OnIblockAdd');

		UnRegisterModuleDependences('iblock', 'OnBeforeIBlockPropertyAdd', $this->MODULE_ID, '\Askaron\Prop\IblockProperty', 'OnBeforeIBlockPropertyAdd');
		UnRegisterModuleDependences('iblock', 'OnBeforeIBlockPropertyUpdate', $this->MODULE_ID, '\Askaron\Prop\IblockProperty', 'OnBeforeIBlockPropertyUpdate');
		UnRegisterModuleDependences('iblock', 'OnBeforeIBlockPropertyDelete', $this->MODULE_ID, '\Askaron\Prop\IblockProperty', 'OnBeforeIBlockPropertyDelete');

		return true;
	}
}