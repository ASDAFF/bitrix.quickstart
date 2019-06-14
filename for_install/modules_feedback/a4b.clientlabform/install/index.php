<?
IncludeModuleLangFile(__FILE__);



class a4b_clientlabform extends CModule
{
	const MODULE_ID = 'a4b.clientlabform';
	var  $IBLOCK_TYPE = 'clientlab_form';
	var  $IBLOCK_CODE = 'clientlab_form';
	var $MODULE_ID = 'a4b.clientlabform';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $errors;
	var $NEED_MAIN_VERSION = '14.0.0';
	var $NEED_MODULES = array('iblock');

	var $SITE_IDS = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("CLIENTLAB_FORMS_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("CLIENTLAB_FORMS_DESC");
		$this->PARTNER_NAME = GetMessage("CLIENTLAB_FORMS_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("CLIENTLAB_FORMS_PARTNER_URI");
	}


	public function AddIblockType($arFieldsIBT){
		global $DB;
		CModule::IncludeModule("iblock");

		$iblockType = $arFieldsIBT["ID"];
		$db_iblock_type = CIBlockType::GetList(Array("SORT" => "ASC"), Array("ID" => $iblockType));

		if (!$ar_iblock_type = $db_iblock_type->Fetch()){
			$obBlocktype = new CIBlockType;
			$DB->StartTransaction();
			$resIBT = $obBlocktype->Add($arFieldsIBT);
			if (!$resIBT){
				$DB->Rollback();
				echo 'Error: '.$obBlocktype->LAST_ERROR.'';
				die();
			}else{
				$DB->Commit();
			}
		}else{
			//return false;
			return $ar_iblock_type;
		}

		return $resIBT;
	}

	public function AddIblock($arFieldsIB){
		CModule::IncludeModule("iblock");

		$iblockCode = $arFieldsIB["CODE"];
		$iblockType = $arFieldsIB["TYPE"];

		$arFieldsIB['NAME'] = GetMessage("CLIENTLAB_FORMS_IBLOCK_NAME_RU");

		$ib = new CIBlock;
		$iblockID = false;

		$resIBE = CIBlock::GetList(Array(), Array('TYPE' => $iblockType, "CODE" => $iblockCode));

		$ar_resIBE = $resIBE->Fetch();
		//var_dump($ar_resIBE);
		if ($ar_resIBE){
			return false;
		}else{

			//var_dump($arFieldsIB);
			$ID = $ib->Add($arFieldsIB);
			$iblockID = $ID;
		}

		return $iblockID;
	}


	public function createIblocks(){
		$arFieldsForType = Array(
			'ID' => $this->IBLOCK_TYPE,
			'CODE' => $this->IBLOCK_CODE,
			'NAME'=> GetMessage("CLIENTLAB_FORMS_IBLOCK_NAME_RU"),
			'SECTIONS' => 'N',
			'IN_RSS' => 'N',
			'SORT' => 500,
			'LANG' => Array(
				'en' => Array(
					'NAME' =>  GetMessage("CLIENTLAB_FORMS_IBLOCK_NAME_EN"),
				),
				'ru' => Array(
					'NAME' => GetMessage("CLIENTLAB_FORMS_IBLOCK_NAME_RU"),
				)
			)
		);

		$arQuery = CSite::GetList($sort = "sort", $order = "desc", Array());
		while ($res = $arQuery->Fetch())
		{
			$sids[] = $res["ID"];
		}

		$SITE_IDS = $sids;

		if ($this->AddIblockType($arFieldsForType)!=''){
			$arFieldsForIblock = Array(
				"ACTIVE" => "Y",
				"NAME" => GetMessage("CLIENTLAB_IBLOCK_NAME"),
				"CODE" => $arFieldsForType["CODE"],
				"IBLOCK_TYPE_ID" => $arFieldsForType["ID"],
				"SITE_ID" => $sids,
				"GROUP_ID" => Array("3" => "W"),
				"FIELDS" => Array(
					"CODE" => Array(
						"IS_REQUIRED" => "N",
						"DEFAULT_VALUE" => Array(
							"TRANS_CASE" => "L",
							"UNIQUE" => "N",
							"TRANSLITERATION" => "Y",
							"TRANS_SPACE" => "-",
							"TRANS_OTHER" => "-"
						)
					)
				)
			);

			$iblockID = $this->AddIblock($arFieldsForIblock);

			if ($iblockID){
				COption::SetOptionString(
					self::MODULE_ID,
					"iblock_id",
					$iblockID,
					false,
					false
				);

				COption::SetOptionString(
					self::MODULE_ID,
					"user_id",
					CUser::GetID(),
					false,
					false
				);

			}else{
			}

		}else{
			CAdminMessage::ShowMessage(Array(
				"TYPE" => "ERROR",
				"MESSAGE" => GetMessage("CLIENTLAB_IBLOCK_CREATE_ERR"),
				"DETAILS" => "",
				"HTML" => true
			));
		}
	}

	function DoInstall()
	{
		$this->errors = false;
		
		if (is_array($this->NEED_MODULES) && !empty($this->NEED_MODULES))
			foreach ($this->NEED_MODULES as $module)
				if (!IsModuleInstalled($module))
					$this->errors[] = array('ERROR', GetMessage('CLIENTLAB_FORMS_INSTALLER_NEED_MODULES', array('#MODULE#' => $module)));

		if (strlen($this->NEED_MAIN_VERSION) <= 0 || version_compare(SM_VERSION, $this->NEED_MAIN_VERSION) >= 0)
		{
			$this->InstallFiles();
			$this->InstallDB();
			$this->InstallEvents();
			RegisterModule($this->MODULE_ID);
			$this->errors[] = array('OK', GetMessage('CLIENTLAB_FORMS_INSTALLER_INSTALL_OK'), GetMessage('CLIENTLAB_FORMS_INSTALLER_GOTO_SETTINGS_BUTTON'), '/bitrix/admin/settings.php', array('mid' => $this->MODULE_ID, 'mid_menu' => 1));
		}
		else
			$this->errors[] = array('ERROR', GetMessage('CLIENTLAB_FORMS_INSTALLER_NEED_RIGHT_VER', array('#NEED#' => $this->NEED_MAIN_VERSION)));
		
		$GLOBALS["errors"] = $this->errors;
		$GLOBALS["APPLICATION"]->IncludeAdminFile(GetMessage('CLIENTLAB_FORMS_NAME'), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
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
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/component_templates'))
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
		}
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


		/* EMAIL EVENTS */
		$arET = CEventType::GetByID("CLIENTLAB_FORM_ADD", "ru")->Fetch();
        $eID = '';
		if (!$arET)
		{
			$obEventType = new CEventType;
			$eID = $obEventType->Add(array(
					"EVENT_NAME"    => "CLIENTLAB_FORM_ADD",
					"SORT"			=> 150,
					"NAME"          => GetMessage("CLIENTLAB_FORMS_MAIL_EVENT_NAME"),
					"LID"       	=> "ru",
					"DESCRIPTION"   => GetMessage("CLIENTLAB_FORMS_MAIL_EVENT_DESCRIPTION")
				));

			COption::SetOptionString(
				self::MODULE_ID,
				"email_event",
				$eID,
				false,
				false
			);
		}

		//$rsMess = CEventMessage::GetList($by = "site_id", $order = "desc", Array("TYPE_ID" => "CLIENTLAB_COMMENT_ADD"));
		if ($eID)
		{
			$arQuery = CSite::GetList($sort = "sort", $order = "desc", Array());
			while ($res = $arQuery->Fetch())
			{
				$sids[] = $res["ID"];
			}
			
			$arr = array(
				"ACTIVE"		=> "Y",
				"EVENT_NAME"	=> "CLIENTLAB_FORM_ADD",
				"LID"			=> $sids[0],
				"EMAIL_FROM"	=> "#DEFAULT_EMAIL_FROM#",
				"EMAIL_TO"		=> "#EMAIL_TO#",
				"BCC"			=> "",
				"SUBJECT"		=> GetMessage("CLIENTLAB_FORMS_MAIL_EVENT_SUBJECT"),
				"BODY_TYPE"		=> "html",
				"MESSAGE"		=> GetMessage("CLIENTLAB_FORMS_MAIL_EVENT_MESSAGE")
			);
			$obTemplate = new CEventMessage;
			$tId = $obTemplate->Add($arr);

			COption::SetOptionString(
				self::MODULE_ID,
				"email_template",
				$tId,
				false,
				false
			);
		}

		/* CREATE IBLOCK */
		$this->createIblocks();


		RegisterModuleDependences("main", "OnPanelCreate", self::MODULE_ID, "ClientlabForm", "insertAdminScripts", 1);
		RegisterModuleDependences("main", "OnPageStart", self::MODULE_ID, "ClientlabForm", "insertGlobalScripts", 1);

		return true;
	}


	function UnInstallEvents()
	{
		UnRegisterModuleDependences("main", "OnPanelCreate", self::MODULE_ID, "ClientlabForm", "insertAdminScripts");
		UnRegisterModuleDependences("main", "OnPageStart", self::MODULE_ID, "ClientlabForm", "insertGlobalScripts");

		return true;
	}
}
?>