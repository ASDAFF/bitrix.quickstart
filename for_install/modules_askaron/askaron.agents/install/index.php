<?
IncludeModuleLangFile(__FILE__);

if (class_exists('askaron_agents')) return;

class askaron_agents extends CModule
{  
	var $MODULE_ID = "askaron.agents";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'Y';
	// first modules '8.0.7', 2009-06-29
	// htmlspecialcharsbx was added in '11.5.9', 2012-09-13
	
	public $NEED_MAIN_VERSION = '8.0.7';
	public $NEED_MODULES = array();

	public $MY_DIR = "bitrix";
	
	public function __construct()
	{
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$dir = substr($path, 0, strlen($path) - strlen('/index.php'));
		include($dir.'/version.php');

		$check_last = "/local/modules/".$this->MODULE_ID."/install/index.php";
		$check_last_len = strlen($check_last);

		if ( substr($path, -$check_last_len) == $check_last )
		{
			$this->MY_DIR = "local";
		}
		
		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}
		
		// !Twice! Marketplace bug. 2013-03-13
		$this->PARTNER_NAME = "Askaron Systems";
		$this->PARTNER_NAME = GetMessage("ASKARON_AGENTS_PARTNER_NAME");
		$this->PARTNER_URI = 'http://askaron.ru/';

		$this->MODULE_NAME = GetMessage('ASKARON_AGENTS_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ASKARON_AGENTS_MODULE_DESCRIPTION');
	}

	public function DoInstall()
	{
		global $APPLICATION;

		global $askaron_agents_global_errors;
		$askaron_agents_global_errors = array();

		if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES))
			foreach ($this->NEED_MODULES as $module)
				if (!IsModuleInstalled($module))
					$askaron_agents_global_errors[] = GetMessage('ASKARON_AGENTS_NEED_MODULES', array('#MODULE#' => $module));
				
		if ( strlen($this->NEED_MAIN_VERSION) > 0  && version_compare(SM_VERSION, $this->NEED_MAIN_VERSION) < 0)
		{
			$askaron_agents_global_errors[] = GetMessage( 'ASKARON_AGENTS_NEED_RIGHT_VER', array('#NEED#' => $this->NEED_MAIN_VERSION) );
		}
		
		if ( count( $askaron_agents_global_errors ) == 0 )
		{
			RegisterModule("askaron.agents");
			RegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID, "CAskaronAgents", "OnPageStartHandler", "500");

			$this->InstallDB();
			$this->InstallFiles();			
		}
		
		$APPLICATION->IncludeAdminFile( GetMessage("ASKARON_AGENTS_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/".$this->MY_DIR."/modules/".$this->MODULE_ID."/install/step.php");
		return true;
	}

	public function DoUninstall()
	{
		global $APPLICATION;
		$step = intval($_REQUEST["step"]);
		
		if($step<2)
		{
			$APPLICATION->IncludeAdminFile( GetMessage("ASKARON_AGENTS_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/".$this->MY_DIR."/modules/".$this->MODULE_ID."/install/unstep1.php");			
		}
		elseif($step==2)
		{
			if ( isset($_REQUEST[ "check_agents" ]) && $_REQUEST[ "check_agents" ] == "Y" )
			{
				COption::SetOptionString("main", "check_agents", "Y" );
			}
			
			$this->UnInstallFiles();
			$this->UnInstallDB();

			UnRegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID, "CAskaronAgents", "OnPageStartHandler");
			UnRegisterModule('askaron.agents');
						
			$APPLICATION->IncludeAdminFile( GetMessage("ASKARON_AGENTS_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/".$this->MY_DIR."/modules/".$this->MODULE_ID."/install/unstep2.php");
		}
	}

	function InstallFiles($arParams = array())
	{
	}

	function UnInstallFiles( $arParams = array() )
	{
	}
	
	function InstallDB()
	{
		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		return true;
	}
}
?>