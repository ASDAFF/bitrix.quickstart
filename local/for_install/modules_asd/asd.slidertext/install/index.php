<?
global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
include($PathInstall.'/version.php');

if (class_exists('asd_slidertext')) return;

class asd_slidertext extends CModule
{
	var $MODULE_ID = "asd.slidertext";
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

		$this->PARTNER_NAME = GetMessage("ASD_PARTNER_NAME");
		$this->PARTNER_URI = 'http://www.d-it.ru/solutions/components/slidertext/';

		$this->MODULE_NAME = GetMessage('ASD_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ASD_MODULE_DESCRIPTION');
	}

	function DoInstall()
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/asd.slidertext/install/components/',
					$_SERVER['DOCUMENT_ROOT'].'/bitrix/components/bitrix/', true, true);
		RegisterModule('asd.slidertext');
	}

	function DoUninstall()
	{
		UnRegisterModule('asd.slidertext');
	}
}
?>