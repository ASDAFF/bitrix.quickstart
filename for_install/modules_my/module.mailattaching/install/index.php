<?
//global $MESS;
IncludeModuleLangFile(__FILE__);

Class module_mailattaching extends CModule {
	var $MODULE_ID = "module.mailattaching";
	var $sModulePrefix = 'MODULE_MAILATTACHING_';
	var $sComponentsDir = 'mailattaching';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;
	var $errors;

	function __construct() {
		$this->errors = array();
		$arPathInfo = pathinfo(str_replace('\\', '/', __FILE__));
		include($arPathInfo['dirname'].'/version.php');

		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

		$this->MODULE_NAME = $this->GetMessage('MODULE_NAME');
		$this->MODULE_DESCRIPTION = $this->GetMessage('MODULE_DESCRIPTION');
		
		// обход тупняка парсера в форме загрузки решения
		$this->PARTNER_NAME = GetMessage("PARTNER_NAME");

		$this->PARTNER_NAME = $this->GetMessage('PARTNER_NAME');
		$this->PARTNER_URI = 'https://asdaff.github.io/';
	}

	public function GetMessage($sMessageCode, $arReplace = false) {
		return GetMessage($this->sModulePrefix.$sMessageCode, $arReplace);
	}

	public function DoInstall() {
		

		$GLOBALS['mErrors'] = false;
		$GLOBALS['step'] = intval($GLOBALS['step']);
		$GLOBALS['_INSTALLING_MODULE_OBJ_'] =& $this;
		$iIncludeStepFile = 1;
		switch($GLOBALS['step']) {
			default:
				$iIncludeStepFile = 1;
				$this->CheckRequiredModules();
			break;
			case 2:
				$iIncludeStepFile = 2;
				if($this->InstallDB()) {
					RegisterModule($this->MODULE_ID);
					$this->InstallFiles();
					if(empty($this->errors)) {
						$this->InstallModuleDependences();
						$this->InstallDefaultOptions();
					} else {
						$this->UnInstallFiles();
						UnRegisterModule($this->MODULE_ID);
					}
				}
			break;
		}
		if($iIncludeStepFile) {
			$GLOBALS['mErrors'] = $this->errors;
			$GLOBALS['sPath2UserPSFiles'] = $this->sPath2UserPSFiles;
			$GLOBALS['APPLICATION']->IncludeAdminFile($this->GetMessage('INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/step'.$iIncludeStepFile.'.php');
		}
	}

	public function DoUninstall() {
		$GLOBALS['mErrors'] = false;
		$GLOBALS['step'] = intval($GLOBALS['step']);
		$GLOBALS['sPath2UserPSFiles'] = $this->sPath2UserPSFiles;
		$GLOBALS['_INSTALLING_MODULE_OBJ_'] = $this;

		if(!$this->CheckUnistall()) {
			$GLOBALS['step'] = 1;
		}
		if($GLOBALS['step'] < 2) {
			$GLOBALS['mErrors'] = $this->errors;
			$GLOBALS['APPLICATION']->IncludeAdminFile($this->GetMessage('UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/unstep1.php');
		} else {
			$this->UnInstallDB(
				array(
					'savedata' => $_REQUEST['savedata']
				)
			);
			// удаляем файлы
			$this->UnInstallFiles(
				array(
					'savefiles' => $_REQUEST['savefiles']
				)
			);

			$GLOBALS['mErrors'] = $this->errors;

			$GLOBALS['CACHE_MANAGER']->CleanAll();
			$GLOBALS['stackCacheManager']->CleanAll();
			$GLOBALS['APPLICATION']->IncludeAdminFile($this->GetMessage('UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/unstep2.php');
		}
	}

	public function CheckRequiredModules() {
		return true;
	}

	public function CheckUnistall() {
		$bResult = true;
		return $bResult;
	}

	public function InstallDB() {
		
		if(!empty($this->errors)) {
			$GLOBALS['APPLICATION']->ThrowException(implode('', $this->errors));
			return false;
		}
		return true;
	}

	public function UnInstallDB($arParams = array()) {
		$this->UnInstallOptions();
		$this->UnInstallModuleDependences();
		UnRegisterModule($this->MODULE_ID);
		return true;
	}

	public function InstallFiles() {
	}

	public function UnInstallFiles($arParams = array()) {
	}

	public function InstallDefaultOptions() {
		// запишем опции по умолчанию (используются триггеры)
		if(CModule::IncludeModule($this->MODULE_ID)) {
		}
	}
	
	public function UnInstallOptions() {
		// удалим опции модуля (чтобы сработали триггеры и удалили все связи)
	}

	public function UnInstallTriggerModuleDependences() {
	}
	
	public function InstallModuleDependences() {
		// автоподключение модуля
		RegisterModuleDependences('main', 'OnBeforeProlog', $this->MODULE_ID, '', '', 1);
		RegisterModuleDependences('main', 'OnAdminTabControlBegin', $this->MODULE_ID, 'CModuleMailAttachingAdmin', 'AddEditFormTab', 5000);

	}

	public function UnInstallModuleDependences() {
		// удаление автоподключения модуля
		UnRegisterModuleDependences('main', 'OnBeforeProlog', $this->MODULE_ID, '', '');
		UnRegisterModuleDependences('main', 'OnAdminTabControlBegin', $this->MODULE_ID, 'CModuleMailAttachingAdmin', 'AddEditFormTab');
	}
}
?>



































































































