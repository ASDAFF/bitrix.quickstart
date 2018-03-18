<?
global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
include($PathInstall.'/version.php');

if (class_exists('mcart_extramail')) return;

class mcart_extramail extends CModule
{
	var $MODULE_ID = "mcart.extramail";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'N';

	public function __construct()
	{
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_NAME = GetMessage('MCART_PARTNER_NAME');
		$this->PARTNER_URI = 'MCART_PARTNER_URI';

		$this->MODULE_NAME = GetMessage('MCART_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('MCART_MODULE_DESCRIPTION');
	}

	
	
	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("mcart.extramail"))
		{
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
			
		}
		return true;
	}

	function DoUninstall()
	{
		$this->UnInstallFiles();
		$this->UnInstallDB();
		$this->UnInstallEvents();
		
		
		return true;
	}
	
	
		function InstallDB() {

		
		RegisterModule("mcart.extramail");	
		return true;
	
			
	}
	
	function UnInstallDB()
	{
		
		UnRegisterModule("mcart.extramail");
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
	
	if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/init.php',
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.extramail/classes/general/cmoduleextramail.php");?'.'>', FILE_APPEND);
					break;
				}
				closedir($dir);
			}
		}
	return true;
	}
	
	function UnInstallFiles()
	{	
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/init.php'))
			{
				$file = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/init.php', 'r');
				$text = fread($file, filesize($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/init.php'));
				fclose($file);
				$file = fopen($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/init.php', 'w');
				fwrite($file, str_replace('<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.extramail/classes/general/cmoduleextramail.php");?>', '', $text));
				fclose($file);
			}
		return true;
	}
	
	

}
?>