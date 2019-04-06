<?
IncludeModuleLangFile(__FILE__);
Class kaycom_oneplaceseo extends CModule
{
	const MODULE_ID = 'kaycom.oneplaceseo';
	var $MODULE_ID = 'kaycom.oneplaceseo'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("kaycom.oneplaceseo_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("kaycom.oneplaceseo_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("kaycom.oneplaceseo_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("kaycom.oneplaceseo_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CKaycomOneplaceseo', 'OnBuildGlobalMenu');
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CKaycomOneplaceseo', 'OnBuildGlobalMenu');
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

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
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

	function DoInstall()
	{
		global $APPLICATION;
		if(!CModule::IncludeModule('iblock')) return false;
		
		//получаем тип инфоблока
    	$iblockType = CIBlockType::GetList(
    		array(),
    		array(
    			"ID" => "kaycom_ONEPLACESEO"
    		)
    	);
		//создаем, если такого нет
    	if(!$iblockType = $iblockType->GetNext()){
			$iT = new CIBlockType();
    		$iblockType["ID"] =  $iT->Add(
    			array(
    				"ID" 		=> "kaycom_ONEPLACESEO",
    				"SECTIONS" 	=> "N",
					"LANG" => array(
						"ru" => array(
							'NAME' => GetMessage("kaycom.oneplaceseo_IBLOCK_TYPE_NAME"),
						)
					)
    			)
    		);
    	}
    	
		//если не создался, выходим
    	if(!$iblockType["ID"]){
    		return false;
    	}
		//получаем список всех сайтов
    	$arSites = array();
    	$rsSites = CSite::GetList($by="sort", $order="desc", Array("ACTIVE" => "Y"));
		while ($arSite = $rsSites->Fetch()){
			$arSites[] = $arSite["ID"]; 
		}
    	
		//пытаемся найти инфоблок в нашем типе инфоблока
    	$iblocks = CIBlock::GetList(
    		array(),
    		array(
    			"TYPE" => "kaycom_ONEPLACESEO",
    			"CODE" => "kaycom_ONEPLACESEO"
    		)
    	);
		//если нет - создаем инфоблок с заданным набором полей
    	if(!$iblock = $iblocks->GetNext()){
			$ib = new CIBlock();
			$iblock["ID"] = $ib->Add(
				array(
					"ACTIVE" => "Y",
					"NAME" => GetMessage("kaycom.oneplaceseo_IBLOCK_TYPE_NAME"),
					"CODE" => "kaycom_ONEPLACESEO",
					"LIST_PAGE_URL" => "",
					"DETAIL_PAGE_URL" => "",
					"IBLOCK_TYPE_ID" => "kaycom_ONEPLACESEO",
					"SITE_ID" => $arSites,
					"SORT" => 10,
					"WORKFLOW" => "N",
					"EDIT_FILE_AFTER" => (strpos (SM_VERSION, '12')!==false ? "/bitrix/modules/".$this->MODULE_ID."/admin/iblock_element_edit.php" : "") // TODO: добавить файл редактирования для 11 версии
				)
			);
			
			$ibp = new CIBlockProperty;
			
			foreach(array("TITLE", "KEYWORDS", "DESCRIPTION") as $PROP_CODE){
				$ibp->Add(
					Array(
						"NAME" => GetMessage("kaycom.oneplaceseo_PROP_".$PROP_CODE),
						"ACTIVE" => "Y",
						"SORT" => 10 * $cnt++,
						"CODE" => $PROP_CODE,
						"PROPERTY_TYPE" => "S",
						"MULTIPLE" => "N",
						"IBLOCK_ID" => $iblock["ID"]
					)
				);
			}
		}
		
		
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
		
		
		
		RegisterModuleDependences("main", "OnEpilog", "kaycom.oneplaceseo",  "CKaycomOneplaceseo", "onPageLoad");
	}

	function DoUninstall()
	{
		if(!CModule::IncludeModule('iblock')) return false;
		
		global $APPLICATION, $step, $errors;

		$step = IntVal($step);
		if($step<2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("FORM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep1.php");
		}
		elseif($step==2)
		{
			$errors = false;
			if($_REQUEST["SAVE_IBLOCK"]!="Y"){
				CIBlockType::Delete("kaycom_ONEPLACESEO");
			}
			UnRegisterModule(self::MODULE_ID);
			$this->UnInstallDB();
			$this->UnInstallFiles();	
			UnRegisterModuleDependences("main", "OnEpilog", "kaycom.oneplaceseo",  "CKaycomOneplaceseo", "onPageLoad");
			
			$APPLICATION->IncludeAdminFile(GetMessage("FORM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/unstep2.php");
		}
	}
}





















?>
