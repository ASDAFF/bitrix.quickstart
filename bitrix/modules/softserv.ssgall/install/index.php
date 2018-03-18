<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

 Class softserv_ssgall extends CModule
 {
 	var $MODULE_ID = "softserv.ssgall";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'N';
 
 		function __construct()
		{
			 $arModuleVersion = array();
             $path = str_replace("\\", "/", __FILE__);
             $path = substr($path, 0, strlen($path) - strlen("/index.php"));
             include($path."/version.php");
			 
			  if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
                {
                   $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                   $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
                }
			   else
                {
                    $this->MODULE_VERSION = "1.0";
                    $this->MODULE_VERSION_DATE = "2011-12-19 15:00:00";
                }
		
		 		$this->MODULE_NAME = GetMessage("SOFTSERV_SSGALL_MODULE_NAME");
                $this->MODULE_DESCRIPTION = GetMessage("SOFTSERV_SSGALL_MODULE_DESCRIPTION");

                $this->PARTNER_NAME = GetMessage("SOFTSERV_PARTNER_NAME");
                $this->PARTNER_URI = "http://www.oz-softservice.ru/";
		}
		
		function DoInstall(){
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/softserv.ssgall/install/components/',
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/components/', true, true);
			
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/softserv.ssgall/install/js/',
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/js/', true, true);
			
			RegisterModule('softserv.ssgall');
		
		}
		
		function DoUninstall(){
			UnRegisterModule('softserv.ssgall');
			
			DeleteDirFilesEx("/bitrix/js/photoslider/");
			DeleteDirFilesEx("/bitrix/components/softserv/");
		
		} 
}
?>