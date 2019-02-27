<?
IncludeModuleLangFile(__FILE__);

class parnas_khayrcomment extends CModule
{
	const MODULE_ID = 'parnas.khayrcomment';
	var $MODULE_ID = 'parnas.khayrcomment';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $errors;
	var $NEED_MAIN_VERSION = '14.0.0';
	var $NEED_MODULES = array('iblock');

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("KHAYR_COMMENT");
		$this->MODULE_DESCRIPTION = GetMessage("KHAYR_COMMENT_MODULE_DESC");
		$this->PARTNER_NAME = "KhayR (Parnas IT)";
		$this->PARTNER_URI = "http://parnas-it.com";
	}
	
	function DoInstall()
	{
		$this->errors = false;
		
		if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES))
			foreach ($this->NEED_MODULES as $module)
				if (!IsModuleInstalled($module))
					$this->errors[] = array('ERROR', GetMessage('KHAYR_MODULEINSTALLER_NEED_MODULES', array('#MODULE#' => $module)));

		if (strlen($this->NEED_MAIN_VERSION) <= 0 || version_compare(SM_VERSION, $this->NEED_MAIN_VERSION) >= 0)
		{
			$this->InstallFiles();
			$this->InstallDB();
			$this->InstallEvents();
			RegisterModule($this->MODULE_ID);
			$this->errors[] = array('OK', GetMessage('KHAYR_MODULEINSTALLER_INSTALL_OK'), GetMessage('KHAYR_MODULEINSTALLER_GOTO_SETTINGS_BUTTON'), '/bitrix/admin/settings.php', array('mid' => $this->MODULE_ID, 'mid_menu' => 1));
		}
		else
			$this->errors[] = array('ERROR', GetMessage('KHAYR_MODULEINSTALLER_NEED_RIGHT_VER', array('#NEED#' => $this->NEED_MAIN_VERSION)));
		
		$GLOBALS["errors"] = $this->errors;
		$GLOBALS["APPLICATION"]->IncludeAdminFile(GetMessage('KHAYR_COMMENT'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
	}

	function DoUninstall()
	{
		COption::RemoveOption($this->MODULE_ID);
		UnRegisterModule($this->MODULE_ID);
		$this->UnInstallEvents();
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
	
	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$this->MODULE_ID.'_'.$item,
					'<'.'?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$this->MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		if (is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components'))
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components', true, true);
		/*if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/component_templates'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/components/'.$item, true, true);
				}
				closedir($dir);
			}
		}*/
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$this->MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;
					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0))
					{
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
		return true;
	}

	function InstallDB()
	{
		return true;
	}

	function UnInstallDB()
	{
		return true;
	}

	function InstallEvents()
	{
		$arET = CEventType::GetByID("KHAYR_COMMENT_ADD", "ru")->Fetch();
		if (!$arET)
		{
			$obEventType = new CEventType;
			$obEventType->Add(array(
				"EVENT_NAME"    => "KHAYR_COMMENT_ADD",
				"SORT"			=> 150,
				"NAME"          => GetMessage("KHAYR_COMMENT_CEVENT_NAME"),
				"LID"       	=> "ru",
				"DESCRIPTION"   => GetMessage("KHAYR_COMMENT_CEVENT_DESCRIPTION")
			));
		}
		
		$rsMess = CEventMessage::GetList($by = "site_id", $order = "desc", Array("TYPE_ID" => "KHAYR_COMMENT_ADD"));
		if (!($arMess = $rsMess->Fetch()))
		{
			$arQuery = CSite::GetList($sort = "sort", $order = "desc", Array());
			while ($res = $arQuery->Fetch())
			{
				$sids[] = $res["ID"];
			}
			
			$arr = array(
				"ACTIVE"		=> "Y",
				"EVENT_NAME"	=> "KHAYR_COMMENT_ADD",
				"LID"			=> $sids,
				"EMAIL_FROM"	=> "#DEFAULT_EMAIL_FROM#",
				"EMAIL_TO"		=> "#EMAIL_TO#",
				"BCC"			=> "",
				"SUBJECT"		=> GetMessage("KHAYR_COMMENT_CEVENT_SUBJECT"),
				"BODY_TYPE"		=> "html",
				"MESSAGE"		=> GetMessage("KHAYR_COMMENT_CEVENT_MESSAGE")
			);
			$obTemplate = new CEventMessage;
			$obTemplate->Add($arr);
		}
		
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}
}
?>