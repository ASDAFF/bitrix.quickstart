<?
IncludeModuleLangFile(__FILE__);
Class webmechanic_landing extends CModule
{
	const MODULE_ID = 'webmechanic.landing';
	var $MODULE_ID = 'webmechanic.landing'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $IBLOCKS;
	var $DEFAULT_REGIONS;

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("webmechanic.landing_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("webmechanic.landing_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("webmechanic.landing_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("webmechanic.landing_PARTNER_URI");

		$this->DEFAULT_REGIONS = array(
			GetMessage('webmechanic_landing_msk'),
			GetMessage('webmechanic_landing_spb')
		);
	}

	function InstallDB($arParams = array())
	{
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CWebmechanicLanding', 'OnBuildGlobalMenu');

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CWebmechanicLanding', 'OnBuildGlobalMenu');

		if (CModule::IncludeModule('iblock')) {

        	$obIBlockType = new CIBlockType;

        	foreach ($this->IBLOCKS as $iblock) {

            	//$obIBlockType->Delete($iblock['ID']);
        	}
            
        }

		return true;
	}

	function initOptions() 
	{
		include $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/options.php';

		foreach ($arOptions as $option => $value) {

            $val = $value['DEFAULT'];

            if($value['TYPE'] == 'CHECKBOX' && $val != 'Y')
                $val = 'N';
            elseif($value['TYPE'] == 'MSELECT')
                $val = serialize($value['VALUES']['REFERENCE_ID']);

            COption::SetOptionString(self::MODULE_ID, $option, $val);

		}

		COption::SetOptionString(self::MODULE_ID, "WEBMECHANIC_CREDIT_REGION", serialize($this->DEFAULT_REGIONS));
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}

		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/wizards'))
		{
			CopyDirFiles(
	        	$_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/wizards',
	        	$_SERVER["DOCUMENT_ROOT"] . "/bitrix/wizards", true, true
	        );

		}
        
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		
		if (is_dir($p = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/wizards/webmechanic"))
		{
			//DeleteDirFilesEx($p);	
		}

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled(self::MODULE_ID))
		{
			$this->InstallDB();
			$this->InstallFiles();

			$this->initOptions();

			RegisterModule(self::MODULE_ID);

			$APPLICATION->IncludeAdminFile(GetMessage('webmechanic_landing_install')." ".self::MODULE_ID , $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/".self::MODULE_ID."/install/step.php");

		}

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION;
		
		$this->UnInstallDB();
		$this->UnInstallFiles();

		UnRegisterModule(self::MODULE_ID);
		$APPLICATION->IncludeAdminFile(GetMessage('webmechanic_landing_uninstall')." ".self::MODULE_ID, $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/".self::MODULE_ID."/install/unstep.php");
	}
}
?>
