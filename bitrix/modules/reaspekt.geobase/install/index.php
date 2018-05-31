<?
/**
 * Company developer: REASPEKT
 * Developer: adel yusupov
 * Site: http://www.reaspekt.ru
 * E-mail: adel@rreaspekt_geobase_citieseaspekt.ru
 * @copyright (c) 2016 REASPEKT
 */
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config as Conf;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;
use Bitrix\Main\EventManager; 

use Bitrix\Highloadblock as HL;

Loc::loadMessages(__FILE__);

Class reaspekt_geobase extends CModule {
	
	var $nameCompany = "reaspekt";
	var $pathResourcesCompany = "local";
    var $pathCompany = "bitrix";
    
    var $exclusionAdminFiles;
    
	var $MODULE_ID = "reaspekt.geobase";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
    
	function __construct() {
		
		$arModuleVersion = array();
		
		include(__DIR__."/version.php");
        
        //Исключения
        $this->exclusionAdminFiles=array(
            '..',
            '.',
        );

        $this->MODULE_ID = "reaspekt.geobase";
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("REASPEKT_GEOBASE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("REASPEKT_GEOBASE_MODULE_DESC");

		$this->PARTNER_NAME = Loc::getMessage("REASPEKT_GEOBASE_PARTNER_NAME");
		$this->PARTNER_URI = Loc::getMessage("REASPEKT_GEOBASE_PARTNER_URI");

        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS='N';
        $this->MODULE_GROUP_RIGHTS = "N";
		
		if (!\Bitrix\Main\Loader::includeModule("highloadblock")) {
			$APPLICATION->ThrowException("Please install module <a href='/bitrix/admin/module_admin.php?lang=ru'>highloadblock</a>");
			return false;
		}
	}
	
	//Определяем место размещения модуля
    public function GetPath($notDocumentRoot=false)
    {
        if($notDocumentRoot)
            return str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__));
        else
            return dirname(__DIR__);
    }
	
	//Проверяем что система поддерживает D7
    public function isVersionD7() {
        return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
    }
	
	function DoInstall() {
		
		//global $DB, $APPLICATION, $step;
		global $DB, $APPLICATION;
		
        if ($this->isVersionD7()) {
			
			$context = Application::getInstance()->getContext();
			$request = $context->getRequest();
			
			$step = IntVal($request['step']);
			
			if ($step == 2) {
				
				//Устанавливаем файлы
				$this->InstallFiles();
				
				if ($request["LOAD_DATA"] != "Y"){
					$step = 3;
				}
			}
			

			if ($step < 2) {
				$GLOBALS["install_step"] = 1;
				
				$APPLICATION->IncludeAdminFile(
					Loc::getMessage("REASPEKT_GEOBASE_INSTALL_TITLE"),
					$this->GetPath() . "/install/step1.php"
				);
				
			} elseif ($step == 2) { // ipgeobase
				$GLOBALS["install_step"]	= 2;
				
				$APPLICATION->IncludeAdminFile(
					Loc::getMessage("REASPEKT_GEOBASE_INSTALL_TITLE"),
					$this->GetPath() . "/install/step2.php"
				);
				
				
			} elseif ($step == 3) { // end
				if ($this->InstallDB()) {
					$GLOBALS["install_step"] = 3;
					
					$APPLICATION->IncludeAdminFile(
						Loc::getMessage("REASPEKT_GEOBASE_INSTALL_TITLE"),
						$this->GetPath() . "/install/step3.php"
					);
				}
			}
			
		} else {
            $APPLICATION->ThrowException(Loc::getMessage("REASPEKT_GEOBASE_INSTALL_ERROR_VERSION"));
			
			$APPLICATION->IncludeAdminFile(
				Loc::getMessage("REASPEKT_GEOBASE_INSTALL_TITLE"),
				$this->GetPath() . "/install/step.php"
			);
        }
	}
	
	function DoUninstall() {
		global $DB, $APPLICATION;
		
		$context = Application::getInstance()->getContext();
        $request = $context->getRequest();
		
		if ($request["step"] < 2) {
			$APPLICATION->IncludeAdminFile(
				Loc::getMessage("REASPEKT_GEOBASE_UNINSTALL_TITLE"), 
				$this->GetPath() . "/install/unstep1.php"
			);
		} elseif ($request["step"] == 2) {
			
			if($request["savedata"] != "Y"){
				
				$this->UnInstallDB(array(
					"savedata" => $request["savedata"],
				));
			}
			
			$this->UnInstallFiles();
			
            EventManager::getInstance()->unRegisterEventHandler(
                "main",
                "OnProlog",
                $this->MODULE_ID,
                "ReaspGeoBaseLoad",
                "OnPrologHandler"
            ); 
            
			\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
			
			$APPLICATION->IncludeAdminFile(
				Loc::getMessage("REASPEKT_GEOBASE_UNINSTALL_TITLE"), 
				$this->GetPath() . "/install/unstep2.php"
			);
		}
	}
	
	function InstallDB(){
		
		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
			
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;

		if($this->errors !== false){
			$APPLICATION->ThrowException(implode("", $this->errors));
			return false;
		}
		//Регистрация модуля в системе
		\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
        
        EventManager::getInstance()->registerEventHandler(
            "main",
            "OnProlog",
            $this->MODULE_ID,
            "ReaspGeoBaseLoad",
            "OnPrologHandler"
        ); 
        
		if ($request['LOAD_DATA'] == "N"){
			Option::set($this->MODULE_ID, "reaspekt_set_local_sql", "not_using");
		}
        
		return true;
	}
	
	function UnInstallDB($arParams = array()){
		global $DB, $DBType, $APPLICATION;
		
		if (!$arParams['savedata']){
			$requestHL = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('order' => array('NAME'), 'filter' => array("TABLE_NAME" => array("reaspekt_geobase_cities","reaspekt_geobase_codeip"))));
			
			while ($rowHL = $requestHL->fetch()){
				if ($DB->TableExists($rowHL["TABLE_NAME"])) {
					HL\HighloadBlockTable::delete($rowHL["ID"]);
				}
			}
		}
		
		Option::delete($this->MODULE_ID);
	}
	
	function InstallFiles() {
		//Создаем папку 'geobase' в /upload/ туда будем загружать файлы с данными об IP адресах и городах
		if (!\Bitrix\Main\IO\Directory::isDirectoryExists($_SERVER["DOCUMENT_ROOT"] . "/upload/".$this->nameCompany . "/geobase/")) {
			if(!defined("BX_DIR_PERMISSIONS"))
				mkdir($_SERVER["DOCUMENT_ROOT"] . "/upload/" . $this->nameCompany . "/geobase/", 0755, true);
			else
				mkdir($_SERVER["DOCUMENT_ROOT"] . "/upload/" . $this->nameCompany . "/geobase/", BX_DIR_PERMISSIONS, true);
		}
        
        //Путь до папки /install/css в модуле
		$pathComponents = $this->GetPath() . "/install/css";
		
		//Проверяем сущетвует ли папка
		if(\Bitrix\Main\IO\Directory::isDirectoryExists($pathComponents))
			CopyDirFiles($pathComponents, $_SERVER["DOCUMENT_ROOT"] . "/".$this->pathResourcesCompany . "/css", true, true);
        else
            throw new \Bitrix\Main\IO\InvalidPathException($pathComponents);
		
        //Путь до папки /install/js в модуле
		$pathComponents = $this->GetPath() . "/install/js";
		
		//Проверяем сущетвует ли папка
		if(\Bitrix\Main\IO\Directory::isDirectoryExists($pathComponents))
			CopyDirFiles($pathComponents, $_SERVER["DOCUMENT_ROOT"] . "/" .$this->pathResourcesCompany . "/js", true, true);
        else
            throw new \Bitrix\Main\IO\InvalidPathException($pathComponents);
		
		//Путь до папки /install/components в модуле
		$pathComponents = $this->GetPath() . "/install/components";
		
		//Проверяем сущетвует ли папка
		if(\Bitrix\Main\IO\Directory::isDirectoryExists($pathComponents))
			CopyDirFiles($pathComponents, $_SERVER["DOCUMENT_ROOT"] . "/" . $this->pathResourcesCompany . "/components", true, true);
        else
            throw new \Bitrix\Main\IO\InvalidPathException($pathComponents);
		
		$path = $this->GetPath().'/admin';
		
		//Проверяем сущетвует ли папка
		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path)) {
			
            //если есть файлы для копирования
            CopyDirFiles($this->GetPath() . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"); 
			
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->exclusionAdminFiles))
                        continue;
					
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $item,
                        '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . $this->pathCompany . '/modules/' . $this->MODULE_ID . '/admin/' . $item . '");?' . '>');
                }
                closedir($dir);
            }
        }
		
		return true;
	}
	
	function UnInstallFiles(){
		
		\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/' . $this->pathResourcesCompany . '/css/' . $this->nameCompany . '/reaspekt.geobase/');
		\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/' . $this->pathResourcesCompany . '/js/' . $this->nameCompany . '/reaspekt.geobase/');
		\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/' . $this->pathResourcesCompany . '/components/' . $this->nameCompany . '/reaspekt.geoip/');
        \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/upload/' . $this->nameCompany . '/geobase/');

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/install/admin/', $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin');
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->exclusionAdminFiles))
                        continue;
                    
                    \Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $item);
                }
                closedir($dir);
            }
        }
		
		return true;
	}
}