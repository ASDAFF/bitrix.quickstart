<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/fairytale.tpic/classes/general/CTPic.php');

IncludeModuleLangFile(__FILE__);
Class fairytale_tpic extends CModule
{
	const MODULE_ID = 'fairytale.tpic';
	var $MODULE_ID = 'fairytale.tpic'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';
	var $fileInitPath = '';
	var $includeModuleCode = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("fairytale.tpic_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("fairytale.tpic_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("fairytale.tpic_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("fairytale.tpic_PARTNER_URI");
		
		$this->fileInitPath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/init.php';
		$this->includeModuleCode = 'CModule::IncludeModule(\'' . self::MODULE_ID . '\');';
	}

	function InstallDB($arParams = array())
	{
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		return true;
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
	
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step, $errors;
		
		// Create directory
		CheckDirPath(
			str_replace(
				array('\\', '//'), 
				array('/', '/'),
				$_SERVER['DOCUMENT_ROOT'] . ft\CTpic::PATH
			)
		);
		
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/js/' . self::MODULE_ID, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . self::MODULE_ID, true, true);
		
		if(!file_exists($this->fileInitPath)) {
			$initFileContent = '<?' . $this->includeModuleCode . '?>';
			if(!$APPLICATION->SaveFileContent($this->fileInitPath, $initFileContent)) {
				return false;
			}
		} else {
			$curInputFileContent = trim(file_get_contents($this->fileInitPath));
			if(!preg_match('/' . str_replace(array('(', ')'), array('\(', '\)'), $this->includeModuleCode) . '/i', $curInputFileContent)) {
				//$curInputFileContent = trim($curInputFileContent);
				if(substr($curInputFileContent, -2, 2) == '?>') {
					$curInputFileContent .= '<?' . $this->includeModuleCode . '?>';
				} else {
					$curInputFileContent .= $this->includeModuleCode;
				}
				RewriteFile($this->fileInitPath, $curInputFileContent);
			}
		}
			
		return true;
	}

	function UnInstallFiles($arParams = array())
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step, $errors;
		
		if(file_exists($this->fileInitPath)) {
			$curInputFileContent = trim(file_get_contents($this->fileInitPath));
			if(preg_match('/<\?' . str_replace(array('(', ')'), array('\(', '\)'), $this->includeModuleCode) . '\?>/i', $curInputFileContent)) {
				$curInputFileContent = str_replace('<?' . $this->includeModuleCode . '?>', '', $curInputFileContent);
				RewriteFile($this->fileInitPath, $curInputFileContent);
			} elseif(preg_match('/' . str_replace(array('(', ')'), array('\(', '\)'), $this->includeModuleCode) . '/i', $curInputFileContent)) {
				$curInputFileContent = str_replace($this->includeModuleCode, '', $curInputFileContent);
				RewriteFile($this->fileInitPath, $curInputFileContent);
			}
		}
		
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/js')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item)) {
                        continue;
                    }

                    $dir0 = opendir($p0);

                    while (false !== $item0 = readdir($dir0)) {
                        if ($item0 == '..' || $item0 == '.') {
                            continue;
                        }

                        DeleteDirFilesEx('/bitrix/js/'.$item.'/'.$item0);
                    }

                    closedir($dir0);
                }

                closedir($dir);
            }
        }
	
		if($arParams['deleteDirectory'] == 'Y') {

			// Delete directory
			DeleteDirFilesEx(
				str_replace(
					array('\\', '//'), 
					array('/', '/'),
					ft\CTpic::PATH
				)
			);
		
		}
		
		return true;
	}

	function DoInstall()
	{
		global $DB, $DOCUMENT_ROOT, $APPLICATION, $step, $errors;

		$FORM_RIGHT = $APPLICATION->GetGroupRight(self::MODULE_ID);
		if ($FORM_RIGHT >= 'W') {
			
			$step = IntVal($step);
			if($step < 2) {
			
				$APPLICATION->IncludeAdminFile(
					GetMessage(self::MODULE_ID . '_INSTALL_TITLE'),
					$_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/step1.php'
				);
				
			} elseif($step == 2) {
				RegisterModule(self::MODULE_ID);
				$this->InstallFiles();
				$this->InstallDB();
				$APPLICATION->IncludeAdminFile(GetMessage(self::MODULE_ID . '_INSTALL_TITLE'), $DOCUMENT_ROOT . '/bitrix/modules/' . self::MODULE_ID . '/install/step2.php');
				
			}
		
		}

	}

	function DoUninstall()
	{
	
		global $DB, $APPLICATION, $step, $errors;

		$FORM_RIGHT = $APPLICATION->GetGroupRight(self::MODULE_ID);
		if ($FORM_RIGHT >= 'W')
		{
			$step = IntVal($step);
			if($step < 2) {
			
				$APPLICATION->IncludeAdminFile(GetMessage(self::MODULE_ID . '_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/unstep1.php');
				
			} elseif($step == 2) {
			
				$errors = false;
				UnRegisterModule(self::MODULE_ID);
				$this->UnInstallFiles(array('deleteDirectory' => $_REQUEST['deleteDirectory']));
				$this->UnInstallDB();
				$APPLICATION->IncludeAdminFile(GetMessage(self::MODULE_ID . '_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/unstep2.php');
			}
		}
	
	}
}
?>
