<?
global $MESS;
$PathInstall = str_replace('\\', '/', __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
IncludeModuleLangFile($PathInstall.'/install.php');
include($PathInstall.'/version.php');


if (class_exists('askaron_ibvote')) return;

class askaron_ibvote extends CModule
{
	var $MODULE_ID = "askaron.ibvote";
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'Y';
	
	// system for partners modules is avaible since '8.0.7', 2009-06-29
	public $NEED_MAIN_VERSION = '8.0.7';
	//public $NEED_MAIN_VERSION = '8.5.2';
	public $NEED_MODULES = array('iblock');

	public function askaron_ibvote()
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

		$this->PARTNER_NAME = GetMessage("ASKARON_IBVOTE_PARTNER_NAME");
		$this->PARTNER_URI = 'http://askaron.ru/';

		$this->MODULE_NAME = GetMessage('ASKARON_IBVOTE_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ASKARON_IBVOTE_MODULE_DESCRIPTION');
	}

	public function DoInstall()
	{
		global $DB;
		
		if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES))
			foreach ($this->NEED_MODULES as $module)
				if (!IsModuleInstalled($module))
					$this->ShowForm('ERROR', GetMessage('ASKARON_IBVOTE_NEED_MODULES', array('#MODULE#' => $module)));

		if (strlen($this->NEED_MAIN_VERSION)<=0 || version_compare(SM_VERSION, $this->NEED_MAIN_VERSION)>=0)
		{
			
			if ( strtolower($DB->type) == 'mysql' )
			{
				if ($this->InstallDB())
				{
					RegisterModule("askaron.ibvote");
					CModule::IncludeModule("askaron.ibvote");
					$this->InstallFiles();
					
					//global $CACHE_MANAGER;
					//$CACHE_MANAGER->CleanDir("fileman_component_tree_array");

					$this->ShowForm('OK', GetMessage('MOD_INST_OK'));
				}
				else
				{
					$this->ShowForm('ERROR', GetMessage('ASKARON_IBVOTE_INSTALL_TABLE_ERROR'));
				}
			}
			else
			{
				$this->ShowForm('ERROR', GetMessage('ASKARON_IBVOTE_ONLY_MYSQL_ERROR'));
			}
		}
		else
			$this->ShowForm('ERROR', GetMessage('ASKARON_IBVOTE_NEED_RIGHT_VER', array('#NEED#' => $this->NEED_MAIN_VERSION)));
	}

	public function DoUninstall()
	{
		global $DB, $APPLICATION, $step, $errors;
		
		$RIGHT = $APPLICATION->GetGroupRight("askaron.ibvote");
		if ($RIGHT>="W")
		{
			$step = IntVal($step);
			if($step<2)
			{
				$APPLICATION->IncludeAdminFile(GetMessage("ASKARON_IBVOTE_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/install/unstep1.php");
			}
			elseif($step==2)
			{
				$errors = false;

				if ( $this->UnInstallDB( array(	"savedata" => $_REQUEST["savedata"]	) ) )
				{
					$this->UnInstallFiles( array( "savedata" => $_REQUEST["savedata"] ) );

					//global $CACHE_MANAGER;
					//$CACHE_MANAGER->CleanDir("fileman_component_tree_array");
					
					UnRegisterModule('askaron.ibvote');
				}
				//$this->ShowForm('OK', GetMessage('MOD_UNINST_OK'));				
				$APPLICATION->IncludeAdminFile(GetMessage("ASKARON_IBVOTE_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/install/unstep2.php");
			}
		}		
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/images/",	$_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".$this->MODULE_ID."/", true, true);

		// Included code samples
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/samples/",	$_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/askaron.include/samples/", true, true);
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
		DeleteDirFilesEx("/bitrix/themes/.default/icons/askaron.ibvote/");//icons
		DeleteDirFilesEx("/bitrix/themes/.default/start_menu/askaron.ibvote/");//start menu
		DeleteDirFilesEx("/bitrix/images/".$this->MODULE_ID."/");//images
		
		// Included code samples
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/samples/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/askaron.include/samples");
	}

	function InstallDB()
	{
		global $APPLICATION, $DB, $errors;
		
		if (!$DB->Query("SELECT 'x' FROM b_askaron_ibvote_event", true)) $EMPTY = "Y"; else $EMPTY = "N";

		$errors = false;
		
		if ($EMPTY=="Y")
		{
			$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/install/db/".strtolower($DB->type)."/install.sql");
		}
		
		if (!empty($errors))
		{
			//print_r($errors);
			
			$APPLICATION->ThrowException(implode("", $errors)); 
			return false;
		}
		
		return true;
			
	}	
	
	function UnInstallDB($arParams = Array())
	{		
		
		global $APPLICATION, $DB, $errors;
		
		if(!array_key_exists("savedata", $arParams) || $arParams["savedata"] != "Y")
		{
			$errors = false;
			// delete whole base
			$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/install/db/".strtolower($DB->type)."/uninstall.sql");
			
			if (!empty($errors))
			{
				$APPLICATION->ThrowException(implode("", $errors)); 
				return false;
			}			
		}

		return true;
	}	
	
	private function ShowForm($type, $message, $buttonName='')
	{
		$keys = array_keys($GLOBALS);
		for($i=0; $i<count($keys); $i++)
			if($keys[$i]!='i' && $keys[$i]!='GLOBALS' && $keys[$i]!='strTitle' && $keys[$i]!='filepath')
				global ${$keys[$i]};

		$PathInstall = str_replace('\\', '/', __FILE__);
		$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen('/index.php'));
		IncludeModuleLangFile($PathInstall.'/install.php');

		$APPLICATION->SetTitle(GetMessage('ASKARON_IBVOTE_MODULE_NAME'));
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
		echo CAdminMessage::ShowMessage(array('MESSAGE' => $message, 'TYPE' => $type));
		?>
		<form action="<?= $APPLICATION->GetCurPage()?>" method="get">
		<p>
			<input type="hidden" name="lang" value="<?=LANG?>" />
			<input type="submit" value="<?= strlen($buttonName) ? $buttonName : GetMessage('MOD_BACK')?>" />
		</p>
		</form>
		<?
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
		die();
	}
}
?>