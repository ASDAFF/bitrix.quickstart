<?
global $MESS; 
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php")); 
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php")); 
include($strPath2Lang."/install/version.php");
Class epir_comingsoon extends CModule{ 
	var $MODULE_ID = "asdaff.comingsoon";
	var $MODULE_VERSION; 
	var $MODULE_VERSION_DATE; 
	var $MODULE_NAME; 
	var $MODULE_DESCRIPTION;
	function epir_comingsoon(){

        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("CS_MODULE_NAME"); 
		$this->MODULE_DESCRIPTION = GetMessage("CS_MODULE_DESC"); 
        $this->PARTNER_NAME = GetMessage("CS_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("CS_PARTNER_URI");		
	}
	function DoInstall(){  
//		$sites_ids = array();
//		$sites_list = CSite::GetList($by="sort", $order="desc");
//		while ($site = $sites_list->Fetch())
//		{
//		  $sites_ids[] .= $site["LID"];
//		}
//		$file_name = 'site_closed.php';
//		foreach($sites_ids as $site_id){
//			$file_dir = $_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/'.$site_id.'/';
//			if(!opendir($file_dir)){
//				mkdir($file_dir);
//			}
//			copy($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/epir.comingsoon/'.$file_name, $file_dir.$file_name);
//		}
		$this->InstallFiles();
		RegisterModule("asdaff.comingsoon");

        RegisterModuleDependences("main", "OnBeforeProlog", "asdaff.comingsoon", "CComingsoon", "MyOnBeforePrologHandler");
	} 
	function InstallFiles()
	{
		global $DOCUMENT_ROOT;


		$ToDir = $DOCUMENT_ROOT."/bitrix/images/asdaff.comingsoon";
		CheckDirPath($ToDir);
		CopyDirFiles($DOCUMENT_ROOT."/bitrix/modules/asdaff.comingsoon/install/images", $ToDir);
		
		$ToDir = $DOCUMENT_ROOT."/bitrix/js/asdaff.comingsoon";
		CheckDirPath($ToDir);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/asdaff.comingsoon/install/js", $ToDir);
		
		$ToDir = $DOCUMENT_ROOT."/bitrix/themes/asdaff.comingsoon";
		CheckDirPath($ToDir);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/asdaff.comingsoon/install/themes", $ToDir);

		return true;
	}	
	function DoUninstall(){

        UnRegisterModuleDependences("main", "OnBeforeProlog", "asdaff.comingsoon", "CComingsoon", "MyOnBeforePrologHandler");

//		$sites_ids = array();
//		$sites_list = CSite::GetList($by="sort", $order="desc");
//		while ($site = $sites_list->Fetch())
//		{
//		  $sites_ids[] .= $site["LID"];
//		}
//		$file_name = 'site_closed.php';
//		foreach($sites_ids as $site_id){
//			$file_dir = $_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/'.$site_id.'/';
//			if(file_exists($file_dir.$file_name)){
//				unlink($file_dir.$file_name);
//			}
//			if(opendir($file_dir)){
//				rmdir($file_dir);
//			}
//		}
		$this->InstallFiles();
		UnRegisterModule("asdaff.comingsoon");
	} 
	function UnInstallFiles()
	{
		global $DOCUMENT_ROOT;

		DeleteDirFilesEx("/bitrix/images/asdaff.comingsoon/");
		DeleteDirFilesEx("/bitrix/js/asdaff.comingsoon/");
		DeleteDirFilesEx("/bitrix/themes/asdaff.comingsoon/");
			
		return true;
	} 
}
?>