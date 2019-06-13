<?

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Application;
use \Bitrix\Main\Loader;
use \Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

Class grain_flood extends CModule
{

	var $MODULE_ID = "grain.flood";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__."/version.php");

		$this->MODULE_ID = 'grain.flood';
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("GRAIN_FLOOD_MODULE_NAME"); 
		$this->MODULE_DESCRIPTION = Loc::getMessage("GRAIN_FLOOD_MODULE_DESC"); 
		$this->PARTNER_URI = GetMessage("GRAIN_FLOOD_PARTNER_URL"); // old GetMessage for marketplace compatibility
		$this->PARTNER_NAME = GetMessage("GRAIN_FLOOD_PARTNER_NAME"); // old GetMessage for marketplace compatibility

		//$this->MODULE_SORT = 1;
	}

	public function GetPath($notDocumentRoot=false)
	{
		if($notDocumentRoot)
			return str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__));
		else
			return dirname(__DIR__);
	}
	
	/*
	public function GetSelfBxRoot()
	{
		$rel_path = str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__));
		return substr($rel_path,0,6)=="/local" || substr($rel_path,0,5)=="local"?"local":"bitrix";
	}	
	*/

	function DoInstall() 
	{
		\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
		$this->InstallFiles();
		$this->InstallDB();
		$GLOBALS["APPLICATION"]->IncludeAdminFile(Loc::getMessage("GRAIN_FLOOD_INSTALL_TITLE"), $this->GetPath()."/install/step.php");
	}

	function DoUninstall()
	{
		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		if($request["step"]<2)
		{
			$GLOBALS["APPLICATION"]->IncludeAdminFile(Loc::getMessage("GRAIN_FLOOD_INSTALL_TITLE"), $this->GetPath()."/install/unstep1.php");
		}
		elseif($request["step"]==2)
		{
			$this->UnInstallFiles();

			$this->UnInstallDB(array(
				"savedata" => $_REQUEST["savedata"],
			));

			\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

			Option::delete($this->MODULE_ID);

			$GLOBALS["APPLICATION"]->IncludeAdminFile(Loc::getMessage("GRAIN_FLOOD_INSTALL_TITLE"), $this->GetPath()."/install/unstep2.php");
		}
	}

	function InstallDB()
	{
		Loader::includeModule($this->MODULE_ID);

		return true;
	}
	
	function UnInstallDB($arParams = Array())
	{
		Loader::includeModule($this->MODULE_ID);

		if($arParams["savedata"]=="Y")
			return;
	}

	function InstallFiles()
	{
		CopyDirFiles($this->GetPath()."/install/wizards", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards", true, true);
		return true;
	}	

	function UnInstallFiles()
	{
		Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"].'/bitrix/wizards/grain/iblock.flooding/');
		return true;
	}

} 

?>