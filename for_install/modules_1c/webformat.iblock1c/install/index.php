<?php
IncludeModuleLangFile(__FILE__);

if(class_exists('webformat_iblock1c')){return;}

Class webformat_iblock1c extends CModule{
	const MODULE_ID = 'webformat.iblock1c';

	var $MODULE_ID = 'webformat.iblock1c';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	private $langPrefix;
	private $phpRoot;

	function __construct(){
		$arModuleVersion = array();
		$this->MODULE_ID = 'webformat.iblock1c';
		$this->langPrefix = strtoupper(__CLASS__).'_';

		include(dirname(__FILE__)."/version.php");
		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)){
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_NAME = GetMessage('WEBFORMAT_IBLOCK1C_PARTNER_NAME'); // $this->langPrefix can't be passed as a prefix to the GetMessage() function. This is a bitrix "magic"
		$this->PARTNER_URI = 'http://www.webformat.ru';
		$this->MODULE_GROUP_RIGHTS = 'Y';

		$this->MODULE_NAME = GetMessage($this->langPrefix.'MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage($this->langPrefix.'MODULE_DESCRIPTION');

		$this->phpRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/';
	}

	function DoInstall(){
		if(!IsModuleInstalled(self::MODULE_ID)){
			RegisterModule(self::MODULE_ID);
		}
	}

	function DoUninstall(){
        if(IsModuleInstalled(self::MODULE_ID)){
		    UnRegisterModule(self::MODULE_ID);
        }
	}

}