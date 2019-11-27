<?

global $MESS;

$strPath2Lang = str_replace("\\", "/", __FILE__);

$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);

include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));



class byteeightlab_sitemap extends CModule{

	var $MODULE_ID = "byteeightlab.sitemap";

	var $MODULE_VERSION;

	var $MODULE_VERSION_DATE;

	var $MODULE_NAME;

	var $MODULE_DESCRIPTION;

	var $MODULE_GROUP_RIGHTS = "Y";



	function byteeightlab_sitemap(){

		$arModuleVersion = array();



		$path = str_replace("\\", "/", __FILE__);

		$path = substr($path, 0, strlen($path) - strlen("/index.php"));

		include($path."/version.php");



		if(is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){

			$this->MODULE_VERSION = $arModuleVersion["VERSION"];

			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		}



		$this->MODULE_NAME = GetMessage("BEL_MODULE_NAME");

		$this->MODULE_DESCRIPTION = GetMessage("BEL_MODULE_DESCRIPTION");

		$this->PARTNER_NAME = GetMessage("BEL_PARTNER_NAME"); 

		$this->PARTNER_URI = GetMessage("BEL_PARTNER_URI");

	}



	function DoInstall(){

		global $DOCUMENT_ROOT, $APPLICATION, $errors;

		$errors = false;

		$FM_RIGHT = $APPLICATION->GetGroupRight("byteeightlab.sitemap");

		if($FM_RIGHT!="D"){

			$this->InstallFiles();

			$this->InstallDB();

			$APPLICATION->IncludeAdminFile(GetMessage("BEL_INSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/step.php");

		}

	}



	function InstallFiles(){

		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin",$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/public",$_SERVER["DOCUMENT_ROOT"]."/");

		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes",$_SERVER["DOCUMENT_ROOT"]."/bitrix/themes",true,true);      

		return true;

	}

  

	function InstallDB(){

		global $DB;    

		RegisterModule($this->MODULE_ID);    

		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/tasks/install.php");

		return true;

	} 

  

	function DoUninstall(){

		global $DOCUMENT_ROOT, $APPLICATION;

		$FM_RIGHT = $APPLICATION->GetGroupRight("byteeightlab.sitemap");

		if($FM_RIGHT!="D"){

			$this->UnInstallFiles();

			$this->UnInstallDB();

			$APPLICATION->IncludeAdminFile(GetMessage("BEL_UNINSTALL_TITLE"), $DOCUMENT_ROOT."/bitrix/modules/".$this->MODULE_ID."/install/unstep.php");

		}

	}

  

	function UnInstallFiles(){

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/public", $_SERVER["DOCUMENT_ROOT"]."/");

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes");   

		return true;

	}



	function UnInstallDB(){

		global $DB;

		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/tasks/uninstall.php");

		UnRegisterModule($this->MODULE_ID);

		return true;

	}	

	

	function GetModuleRightList(){

		global $MESS;

		$arr = array(

			"reference_id" => array("D","R","W"),

			"reference" => array(

				"[D] ".GetMessage("BEL_DENIED"),

				"[L] ".GetMessage("BEL_VIEW_STATS"),

				"[R] ".GetMessage("BEL_VIEW_SETTINGS"),

				"[W] ".GetMessage("BEL_EDIT_SETTINGS"))

			);

		return $arr;

	}

}

?>