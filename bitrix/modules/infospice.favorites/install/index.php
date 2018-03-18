<?

IncludeModuleLangFile(__FILE__);

Class infospice_favorites extends CModule {

	const MODULE_ID = 'infospice.favorites';

	var $MODULE_ID = 'infospice.favorites';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';
	var $MODULE_CODE = 'favorites';
	var $IBLOCK_ID = 0;
	var $SITES = array();
	var $USER_FIELD_CODE = 'UF_FAVORITES_POS';

	function __construct() {
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("infospice.favorites_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("infospice.favorites_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("infospice.favorites_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("infospice.favorites_PARTNER_URI");
	}

	function InstallDB($arParams = array()) {

		//RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CInfospiceFavorites', 'OnBuildGlobalMenu');

		//user field
		$userType = new CUserTypeEntity;
		$arFilter = array("FIELD_NAME" => $this->USER_FIELD_CODE);
		$rsData = $userType->GetList(array(), $arFilter);
		if ($arRes = $rsData->Fetch()) {
			$filed_name = $arRes["FIELD_NAME"];
		}
		if (empty($filed_name)) {
			$data = array(
				"ENTITY_ID"		 => "USER",
				"FIELD_NAME"	 => $this->USER_FIELD_CODE,
				"USER_TYPE_ID"	 => "string",
			);
			$userType->add($data);
		}

		$rsSites = CSite::GetList($by = "sort", $order = "desc", Array());
		while ($arSite = $rsSites->Fetch()) {
			if ($arSite['ACTIVE'] == 'Y') {
				$this->SITES[] = $arSite['LID'];
			}
		}

		//iblock
		global $USER;
		CModule::IncludeModule('iblock');

		if (!CIBlockType::GetByID($this->MODULE_CODE)->fetch()) {
			$obBlocktype = new CIBlockType;
			$arFields = Array(
				'ID'		 => $this->MODULE_CODE,
				'SECTIONS'	 => 'Y',
				'IN_RSS'	 => 'N',
				'SORT'		 => 100,
				'LANG'		 => Array(
					'en' => Array(
						'NAME'			 => $this->MODULE_CODE,
						'SECTION_NAME'	 => 'Sections',
						'ELEMENT_NAME'	 => 'Products',
					),
					'ru' => Array(
						'NAME'			 => $this->MODULE_NAME,
						'SECTION_NAME'	 => GetMessage("INFOSPICE_FAVORITES_RAZDELY"),
						'ELEMENT_NAME'	 => GetMessage("INFOSPICE_FAVORITES_ELEMENTY")
					)
				)
			);
			$res = $obBlocktype->Add($arFields);
		}

		if (!$arBlock = CIBlock::GetList(array(), array('TYPE' => $this->MODULE_CODE))->Fetch()) {
			$obIBlock = new CIBlock;
			$arFields = Array(
				"ACTIVE"		 => 'Y',
				"NAME"			 => $this->MODULE_NAME,
				"CODE"			 => $this->MODULE_CODE,
				"IBLOCK_TYPE_ID" => $this->MODULE_CODE,
				"SITE_ID"		 => $this->SITES,
				"GROUP_ID"		 => array(2 => 'R')
			);

			$this->IBLOCK_ID = $obIBlock->Add($arFields);
		} else {
			$this->IBLOCK_ID = $arBlock['ID'];
		}

		if (!CIBlockProperty::GetList(Array(), Array("IBLOCK_ID"	 => $this->IBLOCK_ID, 'CODE'		 => 'URL'))->Fetch()) {
			$arFields = array(
				"NAME"			 => GetMessage("INFOSPICE_FAVORITES_SSYLKA"),
				"ACTIVE"		 => "Y",
				"SORT"			 => "100",
				"CODE"			 => "URL",
				"PROPERTY_TYPE"	 => "S",
				"IBLOCK_ID"		 => $this->IBLOCK_ID
			);
			$obProperty = new CIBlockProperty;
			$propertyID = $obProperty->Add($arFields);
		}

		return true;
	}

	function UnInstallDB($arParams = array()) {
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CInfospiceFavorites', 'OnBuildGlobalMenu');
		CIBlockType::Delete($this->MODULE_CODE);
		return true;
	}

	function InstallEvents() {
		return true;
	}

	function UnInstallEvents() {
		return true;
	}

	function InstallFiles($arParams = array()) {
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
			if ($dir = opendir($p)) {
				while (false !== $item = readdir($dir)) {
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $item, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/' . self::MODULE_ID . '/admin/' . $item . '");?' . '>');
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/components')) {
			if ($dir = opendir($p)) {
				while (false !== $item = readdir($dir)) {
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p . '/' . $item, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/' . $item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}
		return true;
	}

	function UnInstallFiles() {
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
			if ($dir = opendir($p)) {
				while (false !== $item = readdir($dir)) {
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $item);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/components')) {
			if ($dir = opendir($p)) {
				while (false !== $item = readdir($dir)) {
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p . '/' . $item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0)) {
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/' . $item . '/' . $item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
		return true;
	}

	function DoInstall() {
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB();


		/* $step = IntVal($step);

		  if ($step < 2) {
		  $APPLICATION->IncludeAdminFile(GetMessage("infospice.favorites_MODULE_NAME"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/step1.php");
		  } else {
		  $this->InstallFiles();
		  $this->InstallDB(true);
		  $APPLICATION->IncludeAdminFile(GetMessage("infospice.favorites_MODULE_NAME"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/step2.php");
		  }

		  $this->InstallFiles();
		  $this->InstallDB(); */
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall() {
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}

}
?>
