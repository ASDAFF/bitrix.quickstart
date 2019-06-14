<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 */

$strPath2Lang = str_replace('\\', '/', __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
@include(GetLangFileName($strPath2Lang.'/lang/', '/install/index.php'));
IncludeModuleLangFile($strPath2Lang.'/install/index.php');
global $MESS;
define('ONLINEDENGI_PAYMENT_MODULE_ID', 'rarusspb.onlinedengi');

Class rarusspb_onlinedengi extends CModule {
        var $MODULE_ID = 'rarusspb.onlinedengi';
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
		var $errors;
		var $sPath2UserPSFiles;
		var $PARTNER_NAME;
		var $PARTNER_URI;
		
        function rarusspb_onlinedengi() {
				
				$strPath2Lang = str_replace('\\', '/', __FILE__);
				$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
				require($strPath2Lang.'/install/version.php');
                $this->errors = array();
                $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
                $this->MODULE_NAME = GetMessage('MODULE_NAME');
                $this->MODULE_DESCRIPTION = GetMessage('MODULE_DESCRIPTION');
				$this->PARTNER_NAME = "Деньги Онлайн";
				$this->PARTNER_URI = "http://www.onlinedengi.ru/";
				$sPath2UserPSFiles = COption::GetOptionString('sale', 'path2user_ps_files', BX_PERSONAL_ROOT.'/php_interface/include/sale_payment/');
				$sPath2UserPSFiles = '/'.trim(trim($sPath2UserPSFiles), '/'); // слэш в конце не нужен
				$this->sPath2UserPSFiles = str_replace('//', '/', $sPath2UserPSFiles);
        }

        function DoInstall() {
        	$GLOBALS['mErrors'] = false;
                $GLOBALS['step'] = intval($GLOBALS['step']);
                $iIncludeStepFile = 1;
                switch($GLOBALS['step']) {
                	default:
                		$iIncludeStepFile = 1;
        	        	$this->CheckRequiredModules();
                	break;
                	case 2:
	                	$iIncludeStepFile = 2;
						if($this->InstallDB()) {
							RegisterModule('rarusspb.onlinedengi');
							rarusspb_onlinedengi::InstallFiles();
							if(empty($this->errors)) {
								rarusspb_onlinedengi::InstallModuleDependences();
								rarusspb_onlinedengi::InstallDefaultOptions();
							} else {
								rarusspb_onlinedengi::UnInstallFiles();
								UnRegisterModule('rarusspb.onlinedengi');
							}
						}
					break;
                }
		if($iIncludeStepFile) {
	        	$GLOBALS['mErrors'] = $this->errors;
	        	$GLOBALS['sPath2UserPSFiles'] = $this->sPath2UserPSFiles;
        	        $GLOBALS['APPLICATION']->IncludeAdminFile(GetMessage('INSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/step'.$iIncludeStepFile.'.php');
		}
        }

        function DoUninstall() {
        	$GLOBALS['mErrors'] = false;
                $GLOBALS['step'] = intval($GLOBALS['step']);
        	$GLOBALS['sPath2UserPSFiles'] = $this->sPath2UserPSFiles;

		if(!$this->CheckUnistall()) {
			$GLOBALS['step'] = 1;
		}
		if($GLOBALS['step'] < 2) {
        		$GLOBALS['mErrors'] = $this->errors;
	                $GLOBALS['APPLICATION']->IncludeAdminFile(GetMessage('UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/unstep1.php');
		} else {
			$this->UnInstallDB(
				array(
					'savedata' => $_REQUEST['savedata']
				)
			);
	                // удаляем файлы
			rarusspb_onlinedengi::UnInstallFiles(
				array(
					'savefiles' => $_REQUEST['savefiles']
				)
			);

        		$GLOBALS['mErrors'] = $this->errors;

			$GLOBALS['CACHE_MANAGER']->CleanAll();
			$GLOBALS['stackCacheManager']->CleanAll();
                	$GLOBALS['APPLICATION']->IncludeAdminFile(GetMessage('UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/unstep2.php');
                }
        }

	function CheckRequiredModules() {
		if(!IsModuleInstalled('sale')) {
			$this->errors['ERR_SALE_MODULE_REQUIRED'] = GetMessage('ERR_SALE_MODULE_REQUIRED');
			$GLOBALS['APPLICATION']->ThrowException(implode('', $this->errors));
			return false;
		}
		return true;
	}

	function CheckUnistall() {
		$bResult = true;
		if(CModule::IncludeModule('sale')) {
			$rsItems = CSalePaySystemAction::GetList(array(), array('ACTION_FILE' => $this->sPath2UserPSFiles.'/onlinedengi_payment'));
			if($rsItems->SelectedRowsCount() > 0) {
				$this->errors['ERR_UNINSTALL_PAYMENT_USED'] = GetMessage('ERR_UNINSTALL_PAYMENT_USED');
				$bResult = false;
			}
		}
		return $bResult;
	}

	function InstallDB() {
		if(!empty($this->errors)) {
			$GLOBALS['APPLICATION']->ThrowException(implode('', $this->errors));
			return false;
		}
		return true;
	}

	function UnInstallDB($arParams = array()) {
       		rarusspb_onlinedengi::UnInstallOptions();
		rarusspb_onlinedengi::UnInstallModuleDependences();
		UnRegisterModule('rarusspb.onlinedengi');
		return true;
	}

	function InstallFiles() {
		// обработчик платежной системы
		$bRewriteFiles = isset($_REQUEST['rewrite_files']) && $_REQUEST['rewrite_files'] == 'Y';
		$bSuccess = CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/sale_payment/', $_SERVER['DOCUMENT_ROOT'].$this->sPath2UserPSFiles, $bRewriteFiles, true);
		if($bSuccess === false) {
			$this->errors['ERR_COPY_PHP_INTERFACE_PAY_SYTEM_FILES'] = GetMessage('ERR_COPY_PHP_INTERFACE_PAY_SYTEM_FILES', array('#FILE_PATH#' => $this->sPath2UserPSFiles));
		}
		if(empty($this->errors)) {
			// module imges
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/images', $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/rarusspb_onlinedengi/', true, true);
			// themes
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/themes/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes', true, true);
			// tools
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/tools', $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools', true, true);
			// wizards
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/wizards', $_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards', true, true);
			// components
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/components', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components', true, true);
		}
	}

	function UnInstallFiles($arParams = array()) {
		// module imges
		DeleteDirFilesEx('/bitrix/images/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/');
		// themes
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/themes/.default/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/.default');
		// public tools
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.ONLINEDENGI_PAYMENT_MODULE_ID.'/install/tools/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools');
		// wizards
		DeleteDirFilesEx(BX_PERSONAL_ROOT.'/wizards/onlinedengi_payment');
		// components
		DeleteDirFilesEx(BX_PERSONAL_ROOT.'/components/onlinedengi_payment');

		// обработчики
		if(!array_key_exists('savefiles', $arParams) || ($arParams['savefiles'] != 'Y')) {
			DeleteDirFilesEx($this->sPath2UserPSFiles.'/onlinedengi_payment');
		}
	}

	function InstallDefaultOptions() {
                // запишем опции по умолчанию (используются триггеры)
		if(CModule::IncludeModule('rarusspb.onlinedengi')) {
		}
	}
	
        function UnInstallOptions() {
                // удалим опции модуля (чтобы сработали триггеры и удалили все связи)
        }

        function UnInstallTriggerModuleDependences() {
        }
        
        function InstallModuleDependences() {
		// автоподключение модуля
		// RegisterModuleDependences('main', 'OnBeforeProlog', 'rarusspb_onlinedengi', '', '', 10);
        }

        function UnInstallModuleDependences() {
		// автоподключение модуля
		// UnRegisterModuleDependences('main', 'OnBeforeProlog', 'rarusspb_onlinedengi', '', '');
        }
}
