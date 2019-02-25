<?
global $MESS;
$strPath2Lang = str_replace('\\', '/', __FILE__);

$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));
include($strPath2Lang."/install/version.php");

Class yenisite_3dtags extends CModule 																								// <------------ HERE------------- CLASS NAME MUST BE CHANGED
{
        var $MODULE_ID = 'yenisite.3dtags';																							
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
		
		
        function yenisite_3dtags()																												// <------------ HERE------------- CONSTRUCTOR NAME MUST BE CHANGED
        {        		
                $arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->PARTNER_NAME = "yenisite";
		$this->PARTNER_URI = "http://www.yenisite.ru/";
		$this->MODULE_NAME = GetMessage("MODULE_NAME"); 												// <------------ HERE------------- LANG MUST BE CHANGED
		$this->MODULE_DESCRIPTION = GetMessage("MODULE_DESC"); 									// <------------ HERE------------- LANG MUST BE CHANGED

		return true;
        }

		
        function DoInstall(){		
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/yenisite.3dtags/install/template-components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/.default/components/bitrix/", true, true);  
		RegisterModule($this->MODULE_ID);
        }

        function DoUninstall(){           
		self::removeDirRec($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/.default/components/bitrix/search.page/3d_tags/");
		UnRegisterModule($this->MODULE_ID);
        }


	function removeDirRec($dir)
	{
		if ($objs = glob($dir."/*")) {
			foreach($objs as $obj) {
				is_dir($obj) ? removeDirRec($obj) : unlink($obj);
			}
		}

		rmdir($dir);
	}

}

?>