<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

class aspro_optimus extends CModule {
	const solutionName	= 'optimus';
	const partnerName = 'aspro';
	const moduleClass = 'COptimus';

	var $MODULE_ID = "aspro.optimus";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function aspro_optimus(){
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("SCOM_INSTALL_NAME_OPTIMUS"); 
		$this->MODULE_DESCRIPTION = GetMessage("SCOM_INSTALL_DESCRIPTION_OPTIMUS");
		$this->PARTNER_NAME = GetMessage("SPER_PARTNER");
		$this->PARTNER_URI = GetMessage("PARTNER_URI");
	}
	
	function checkValid(){
		return true;
	}

	function InstallDB($install_wizard = true){
		global $DB, $DBType, $APPLICATION;

		if(preg_match ( '/.bitrixlabs.ru/' , $_SERVER["HTTP_HOST"])){
			RegisterModuleDependences("main", "OnBeforeProlog", "aspro.optimus", "COptimus", "correctInstall");
		}

		RegisterModule("aspro.optimus"); 
		RegisterModuleDependences("main", "OnBeforeProlog", "aspro.optimus", "COptimus", "ShowPanel");
		
		return true;
	}

	function UnInstallDB($arParams = array()){
		global $DB, $DBType, $APPLICATION;
		
		UnRegisterModule("aspro.optimus");
		
		return true;
	}

	function InstallEvents(){
		RegisterModuleDependences("iblock", "OnAfterIBlockAdd", "aspro.optimus", "COptimusCache", "ClearTagIBlock");
		RegisterModuleDependences("iblock", "OnAfterIBlockUpdate", "aspro.optimus", "COptimusCache", "ClearTagIBlock");
		RegisterModuleDependences("iblock", "OnBeforeIBlockDelete", "aspro.optimus", "COptimusCache", "ClearTagIBlockBeforeDelete");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "aspro.optimus", "COptimusCache", "ClearTagIBlockElement");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", "aspro.optimus", "COptimusCache", "ClearTagIBlockElement");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", "aspro.optimus", "COptimusCache", "ClearTagIBlockElement");
		RegisterModuleDependences("iblock", "OnAfterIBlockSectionAdd", "aspro.optimus", "COptimusCache", "ClearTagIBlockSection");
		RegisterModuleDependences("iblock", "OnAfterIBlockSectionUpdate", "aspro.optimus", "COptimusCache", "ClearTagIBlockSection");
		RegisterModuleDependences("iblock", "OnBeforeIBlockSectionDelete", "aspro.optimus", "COptimusCache", "ClearTagIBlockSectionBeforeDelete");
		RegisterModuleDependences("main", "OnAfterUserUpdate", "aspro.optimus", "COptimusCache", "ClearTagByUser");

		RegisterModuleDependences("main", "OnBeforeUserRegister", "aspro.optimus", "COptimus", "OnBeforeUserUpdateHandler");
		RegisterModuleDependences("main", "OnBeforeUserAdd", "aspro.optimus", "COptimus", "OnBeforeUserUpdateHandler");
		RegisterModuleDependences("main", "OnBeforeUserUpdate", "aspro.optimus", "COptimus","OnBeforeUserUpdateHandler");
		RegisterModuleDependences("main", "OnEndBufferContent", "aspro.optimus", "COptimus", "InsertCounters");
		RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", "aspro.optimus", "COptimus", "clearBasketCacheHandler");
		RegisterModuleDependences("sale", "OnBasketAdd", "aspro.optimus", "COptimus", "clearBasketCacheHandler");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", "aspro.optimus", "COptimus", "DoIBlockAfterSave");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "aspro.optimus", "COptimus", "DoIBlockAfterSave");
		RegisterModuleDependences("catalog", "OnPriceAdd", "aspro.optimus", "COptimus", "DoIBlockAfterSave");
		RegisterModuleDependences("catalog", "OnPriceUpdate", "aspro.optimus", "COptimus", "DoIBlockAfterSave");
		RegisterModuleDependences("catalog", "OnProductUpdate", "aspro.optimus", "COptimus", "setStockProduct");
		RegisterModuleDependences("catalog", "OnProductAdd", "aspro.optimus", "COptimus", "setStockProduct");

		if(class_exists('\Bitrix\Main\EventManager')){
			$eventManager = \Bitrix\Main\EventManager::getInstance();
			$eventManager->registerEventHandler('sale', 'OnSaleOrderSaved', 'aspro.optimus', 'COptimus', 'BeforeSendEvent', 10);
		}
		
		return true;
	}

	function UnInstallEvents(){
		UnRegisterModuleDependences("iblock", "OnAfterIBlockAdd", "aspro.optimus", "COptimusCache", "ClearTagIBlock");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockUpdate", "aspro.optimus", "COptimusCache", "ClearTagIBlock");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockDelete", "aspro.optimus", "COptimusCache", "ClearTagIBlockBeforeDelete");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "aspro.optimus", "COptimusCache", "ClearTagIBlockElement");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", "aspro.optimus", "COptimusCache", "ClearTagIBlockElement");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", "aspro.optimus", "COptimusCache", "ClearTagIBlockElement");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockSectionAdd", "aspro.optimus", "COptimusCache", "ClearTagIBlockSection");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockSectionUpdate", "aspro.optimus", "COptimusCache", "ClearTagIBlockSection");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockSectionDelete", "aspro.optimus", "COptimusCache", "ClearTagIBlockSectionBeforeDelete");
		UnRegisterModuleDependences("main", "OnAfterUserUpdate", "aspro.optimus", "COptimusCache", "ClearTagByUser");

		UnRegisterModuleDependences("main", "OnBeforeUserRegister", "aspro.optimus", "COptimus", "OnBeforeUserUpdateHandler");
		UnRegisterModuleDependences("main", "OnBeforeUserAdd", "aspro.optimus", "COptimus", "OnBeforeUserUpdateHandler");
		UnRegisterModuleDependences("main", "OnBeforeUserUpdate", "aspro.optimus", "COptimus","OnBeforeUserUpdateHandler");
		UnRegisterModuleDependences("main", "OnBeforeProlog", "aspro.optimus", "COptimus", "ShowPanel");
		UnRegisterModuleDependences("main", "OnEndBufferContent", "aspro.optimus", "COptimus", "InsertCounters");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", "aspro.optimus", "COptimus", "clearBasketCacheHandler");
		UnRegisterModuleDependences("sale", "OnBasketAdd", "aspro.optimus", "COptimus", "clearBasketCacheHandler");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", "aspro.optimus", "COptimus", "DoIBlockAfterSave");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "aspro.optimus", "COptimus", "DoIBlockAfterSave");
		UnRegisterModuleDependences("catalog", "OnPriceAdd", "aspro.optimus", "COptimus", "DoIBlockAfterSave");
		UnRegisterModuleDependences("catalog", "OnPriceUpdate", "aspro.optimus", "COptimus", "DoIBlockAfterSave");
		UnRegisterModuleDependences("catalog", "OnProductUpdate", "aspro.optimus", "COptimus", "setStockProduct");
		UnRegisterModuleDependences("catalog", "OnProductAdd", "aspro.optimus", "COptimus", "setStockProduct");

		if(class_exists('\Bitrix\Main\EventManager')){
			$eventManager = \Bitrix\Main\EventManager::getInstance();
			$eventManager->unregisterEventHandler('sale', 'OnSaleOrderSaved', 'aspro.optimus', 'COptimus', 'BeforeSendEvent', 10);
		}
		
		return true;
	}
	
	function removeDirectory($dir){
		if($objs = glob($dir."/*")){
			foreach($objs as $obj){
				if(is_dir($obj)){
					COptimus::removeDirectory($obj);
				}
				else{
					if(!unlink($obj)){
						if(chmod($obj, 0777)){
							unlink($obj);
						}
					}
				}
			}
		}
		if(!rmdir($dir)){
			if(chmod($dir, 0777)){
				rmdir($dir);
			}
		}
	}

	function InstallFiles(){
		CopyDirFiles(__DIR__.'/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true);
		CopyDirFiles(__DIR__.'/css/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/css/'.self::partnerName.'.'.self::solutionName, true, true);
		CopyDirFiles(__DIR__.'/js/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.self::partnerName.'.'.self::solutionName, true, true);
		CopyDirFiles(__DIR__.'/images/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/'.self::partnerName.'.'.self::solutionName, true, true);
		CopyDirFiles(__DIR__.'/components/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components', true, true);
		CopyDirFiles(__DIR__.'/wizards/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards', true, true);

		$this->InstallGadget();

		if(preg_match('/.bitrixlabs.ru/', $_SERVER["HTTP_HOST"])){
			@set_time_limit(0);
			require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");
			CFileMan::DeleteEx(array('s1', '/bitrix/modules/'.$this->MODULE_ID.'/install/wizards'));
			CFileMan::DeleteEx(array('s1', '/bitrix/modules/'.$this->MODULE_ID.'/install/gadgets'));
		}

		return true;
	}

	function InstallPublic(){
	}

	function UnInstallFiles(){
		DeleteDirFiles(__DIR__.'/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		DeleteDirFilesEx('/bitrix/css/'.self::partnerName.'.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/js/'.self::partnerName.'.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/images/'.self::partnerName.'.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/wizards/'.self::partnerName.'/'.self::solutionName.'/');

		$this->UnInstallGadget();

		return true;
	}

	function InstallGadget(){
		CopyDirFiles(__DIR__.'/gadgets/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/gadgets/', true, true);

		$gadget_id = strtoupper(self::solutionName);
		$gdid = $gadget_id."@".rand();
		if(class_exists('CUserOptions')){
			$arUserOptions = CUserOptions::GetOption('intranet', '~gadgets_admin_index', false, false);
			if(is_array($arUserOptions) && isset($arUserOptions[0])){
				foreach($arUserOptions[0]['GADGETS'] as $tempid => $tempgadget){
					$p = strpos($tempid, '@');
					$gadget_id_tmp = ($p === false ? $tempid : substr($tempid, 0, $p));

					if($gadget_id_tmp == $gadget_id){
						return false;
					}
					if($tempgadget['COLUMN'] == 0){
						++$arUserOptions[0]['GADGETS'][$tempid]['ROW'];
					}
				}
				$arUserOptions[0]['GADGETS'][$gdid] = array('COLUMN' => 0, 'ROW' => 0);
				CUserOptions::SetOption('intranet', '~gadgets_admin_index', $arUserOptions, false, false);
			}
		}

		return true;
	}

	function UnInstallGadget(){
		$gadget_id = strtoupper(self::solutionName);
		if(class_exists('CUserOptions')){
			$arUserOptions = CUserOptions::GetOption('intranet', '~gadgets_admin_index', false, false);
			if(is_array($arUserOptions) && isset($arUserOptions[0])){
				foreach($arUserOptions[0]['GADGETS'] as $tempid => $tempgadget){
					$p = strpos($tempid, '@');
					$gadget_id_tmp = ($p === false ? $tempid : substr($tempid, 0, $p));

					if($gadget_id_tmp == $gadget_id){
						unset($arUserOptions[0]['GADGETS'][$tempid]);
					}
				}
				CUserOptions::SetOption('intranet', '~gadgets_admin_index', $arUserOptions, false, false);
			}
		}

		DeleteDirFilesEx('/bitrix/gadgets/'.self::partnerName.'/'.self::solutionName.'/');

		return true;
	}

	function DoInstall(){
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();
		$this->InstallPublic();

		$APPLICATION->IncludeAdminFile(GetMessage("SCOM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/aspro.optimus/install/step.php");
	}

	function DoUninstall(){
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		$APPLICATION->IncludeAdminFile(GetMessage("SCOM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/aspro.optimus/install/unstep.php");
	}
}
?>