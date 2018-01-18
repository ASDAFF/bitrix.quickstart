<?
	Class dragon_limitlessyoutube extends CModule
	{
		var $MODULE_ID = "dragon.limitlessyoutube";
		var $MODULE_NAME;
		var $PARTNER_NAME = 'DragoN';

		function dragon_limitlessyoutube()
		{
			$arModuleVersion = array();

			$path = str_replace("\\", "/", __FILE__);
			$path = substr($path, 0, strlen($path) - strlen("/index.php"));
			include($path."/version.php");

			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
			$this->PARTNER_NAME = "DragoN";
			$this->PARTNER_URI = "http://l1mitless.ru/";

			$this->MODULE_NAME = "YouTube HTML5 player, L1mitless";
			$this->MODULE_DESCRIPTION = "YouTube HTML5 Player";
		}

		function InstallFiles()
		{
			CopyDirFiles(
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/dragon.limitlessyoutube/install/components/",
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/components",
					true, true
			);

			return true;
		}

		function UnInstallFiles()
		{
			return true;
		}

		function InstallEvents()
		{
			RegisterModule("dragon.limitlessyoutube");
			return true;
		}

		function UnInstallEvents()
		{
			UnRegisterModule("dragon.limitlessyoutube");
			return true;
		}

		function DoInstall()
		{
			if (!IsModuleInstalled("dragon.limitlessyoutube"))
			{
				$this->InstallEvents();
				$this->InstallFiles();
			}
		}

		function DoUninstall()
		{
			$this->UnInstallEvents();
			$this->UnInstallFiles();

		}
	}
?>