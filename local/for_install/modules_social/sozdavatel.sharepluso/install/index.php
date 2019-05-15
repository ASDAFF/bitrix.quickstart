<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class sozdavatel_sharepluso extends CModule
{
	var $MODULE_ID = "sozdavatel.sharepluso";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $PARTNER_NAME;
	var $PARTNER_URI;

	function sozdavatel_sharepluso() 
    {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		
		$this->MODULE_NAME = GetMessage("SZD_SHAREPLUSO_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("SZD_SHAREPLUSO_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("SZD_SHAREPLUSO_PARTNER_NAME");
		$this->PARTNER_URI = 'http://sozdavatel.ru';
	}


	function InstallDB($install_wizard = true)
	{
		RegisterModule("sozdavatel.sharepluso");		
		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		UnRegisterModule("sozdavatel.sharepluso");
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
	
	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sozdavatel.sharepluso/install/components/sozdavatel", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/sozdavatel", true, true);
		return true;
	}

	function InstallPublic()
	{
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/sozdavatel/sharepluso");		
		return true;
		
	}

	function DoInstall()
	{
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();
		$this->InstallPublic();
        $this->SendInstallInfo();
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		
	}
    
    function SendInstallInfo ()
    {
		global $USER;
		global $APPLICATION;
		$email = $USER->GetEmail();
		$first_name = $APPLICATION->ConvertCharset($USER->GetFirstName(), SITE_CHARSET, "windows-1251");
		$last_name = $APPLICATION->ConvertCharset($USER->GetLastName(), SITE_CHARSET, "windows-1251");
		$time = time();
		$url = $_SERVER["SERVER_NAME"];
		$module_install = true;
		$module_id = "sozdavatel.sharepluso";
		$socket = fsockopen('www.sozdavatel.ru', 80, $errno, $errstr, 10);
		if($socket) 
        {
            $data = "first_name=".urlencode($first_name)."&last_name=".urlencode($last_name)."&time=".urlencode($time)."&url=".urlencode($url)."&module_install=".urlencode($module_install)."&module_id=".urlencode($module_id)."&email=".urlencode($email);
            fwrite($socket, "POST /mp/index.php HTTP/1.1\r\n");
            fwrite($socket, "Host: www.sozdavatel.ru\r\n");
            fwrite($socket,"Content-type: application/x-www-form-urlencoded\r\n");
            fwrite($socket,"Content-length:".strlen($data)."\r\n");
            fwrite($socket,"Accept:*/*\r\n");
            fwrite($socket,"User-agent:Opera 10.00\r\n");
            fwrite($socket,"Connection:Close\r\n");
            fwrite($socket,"\r\n");
            fwrite($socket,$data."\r\n");
            fwrite($socket,"\r\n");
            $answer = '';
            while(!feof($socket)){$answer.= fgets($socket, 4096);}
            fclose($socket);
        }
    }
    
}

?>