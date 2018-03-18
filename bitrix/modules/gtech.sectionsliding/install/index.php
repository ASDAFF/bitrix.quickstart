<?
global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
include($PathInstall.'/version.php');

if (class_exists('gtech_sectionsliding')) return;

class gtech_sectionsliding extends CModule
{
	var $MODULE_ID = "gtech.sectionsliding";
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

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($path.'/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->PARTNER_NAME = GetMessage("GTECH_PARTNER_NAME");
		$this->PARTNER_URI = 'http://g-tech.su/';

		$this->MODULE_NAME = GetMessage('GTECH_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('GTECH_MODULE_DESCRIPTION');
	}

	function DoInstall()
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/gtech.sectionsliding/install/components/',
					$_SERVER['DOCUMENT_ROOT'].'/bitrix/components/', true, true);
		RegisterModule('gtech.sectionsliding');
	}

	function DoUninstall()
	{
		UnRegisterModule('gtech.sectionsliding');
	}
}
?>
