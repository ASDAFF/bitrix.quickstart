<?
global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
include($PathInstall.'/version.php');

if (class_exists('zamit_callback')) return;

class zamit_callback extends CModule
{
	var $MODULE_ID = "zamit.callback";
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

		$this->PARTNER_NAME = GetMessage("ZAM_IT_PARTNER_NAME");
		$this->PARTNER_URI = 'http://www.zam-it.ru/solutions/';

		$this->MODULE_NAME = GetMessage('ZAM_IT_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ZAM_IT_MODULE_DESCRIPTION');
	}

	function DoInstall()
	{
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/zamit.callback/install/components/',
					$_SERVER['DOCUMENT_ROOT'].'/bitrix/components/zam-it/', true, true);
		RegisterModule('zamit.callback');
		$this->InstallEvents();
	}

	function DoUninstall()
	{
		UnRegisterModule('zamit.callback');
	}

	function InstallEvents()
	{		$arEventTypes = Array();
		$langs = CLanguage::GetList(($b=""), ($o=""));
		while($language = $langs->Fetch())
		{
			$lid = $language["LID"];
			IncludeModuleLangFile(__FILE__, $lid);

			$arEventTypes[] = Array(
				"LID" => $lid,
				"EVENT_NAME" => "CALLBACK_FORM",
				"NAME" => GetMessage("CF_EVENT_NAME"),
				"DESCRIPTION" => GetMessage("CF_EVENT_DESCRIPTION"),
				"SORT" => 150
			);
		}

		$type = new CEventType;
		foreach ($arEventTypes as $arEventType)
			$type->Add($arEventType);

		IncludeModuleLangFile(__FILE__);

		$arMessages = Array();
		$arMessages[] = Array(
			"EVENT_NAME" => "CALLBACK_FORM",
			"LID" => "s1",
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL_TO#",
			"SUBJECT" => GetMessage("CF_EVENT_SUBJECT"),
			"MESSAGE" => GetMessage("CF_EVENT_MESSAGE")
		);

		$message = new CEventMessage;
		foreach ($arMessages as $arMessage)
			$message->Add($arMessage);

		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}
}
?>