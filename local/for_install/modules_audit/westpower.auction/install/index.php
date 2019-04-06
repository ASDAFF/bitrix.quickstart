<?
IncludeModuleLangFile(__FILE__);
Class westpower_auction extends CModule
{
	const MODULE_ID = 'westpower.auction';
	var $MODULE_ID = 'westpower.auction'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';
	var $errors = false;

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		
		if(!$DB->Query("SELECT 'x' FROM b_auction WHERE 1=0", true)) {
			$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".$DBType."/install.sql");
		}
		if($this->errors !== false) {
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		
		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".$DBType."/uninstall.sql");

		if($this->errors !== false) {
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		
		return true;
	}

	function InstallFiles($arParams = array())
	{
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
		
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/admin', $_SERVER['DOCUMENT_ROOT']."/bitrix/admin", true, true);
		
		return true;
	}

	function InstallIblock($CATALOG_ID, $SITE_ID)
	{
		if ($CATALOG_ID <= 0)
			return false;
		
		global $DB;
		CModule::IncludeModule('iblock');
		
		$IBLOCK_TYPE = "auction";
		
		$DB->StartTransaction();
		$arFields = array(
			'ID'=>$IBLOCK_TYPE,	
			'SECTIONS'=>'Y',
			'SORT'=>500,	
			'LANG'=>array(
				'ru'=>array(			
					'NAME'=>GetMessage('BAY_IBLOCK_TYPE'),			
					'SECTION_NAME'=>GetMessage('BAY_IBLOCK_TYPE_SECTIONS'),	
					'ELEMENT_NAME'=>GetMessage('BAY_IBLOCK_TYPE_ELEMENT'),
				),
				'en'=>array(			
					'NAME'=>'Products of auction',			
					'SECTION_NAME'=>'Sections',			
					'ELEMENT_NAME'=>'Products',
				)		
			)
		);
		$obBlocktype = new CIBlockType;
		$res = $obBlocktype->Add($arFields);
		if ($res)
		{
			$ib = new CIBlock;
			$arFields = array(
				"ACTIVE"=>"Y",
				"NAME" => GetMessage('BAY_IBLOCK_NAME'),
				"CODE" => "auction-product",
				"IBLOCK_TYPE_ID" => $IBLOCK_TYPE,	
				"SITE_ID" => array($SITE_ID),
				"GROUP_ID" => array("2"=>"W"),
				"INDEX_ELEMENT"=>"N",
				"WF_TYPE"=>"N",
				"FIELDS"=>array(
					"ACTIVE_FROM"=>array("IS_REQUIRED"=>"Y", "DEFAULT_VALUE"=>"=now"),
					"ACTIVE_TO"=>array("IS_REQUIRED"=>"Y"),
				),
				"SECTION_PAGE_URL"=>"#SITE_DIR#/".$IBLOCK_TYPE."/?SECTION_ID=#SECTION_ID#",
				"DETAIL_PAGE_URL"=>"#SITE_DIR#/".$IBLOCK_TYPE."/detail.php?ELEMENT_ID=#ELEMENT_ID#",
			);
			$ID = $ib->Add($arFields);
					
			$arFields = array(
				"ACTIVE"=>"Y",
				"NAME" => GetMessage('BAY_IBLOCK_USER'),
				"CODE" => "auction-user-bets",
				"IBLOCK_TYPE_ID" => $IBLOCK_TYPE,	
				"SITE_ID" => array($SITE_ID),
				"GROUP_ID" => array("2"=>"W"),
				"INDEX_ELEMENT"=>"N",
				"WF_TYPE"=>"N",
			);
			$IBLOCK_USER_ID = $ib->Add($arFields);
			
			if ($ID > 0 && $IBLOCK_USER_ID > 0)
			{
				$arFields = array(
					array(
						"NAME" => GetMessage('BAY_PROPS_PRICE'),
						"ACTIVE" => "Y",
						"SORT" => "10",
						"CODE" => "PRICE_BEGIN",
						"IBLOCK_ID" => $ID,
						"PROPERTY_TYPE" => "S",
						"IS_REQUIRED"=>"Y",
					),
					array(
						"NAME" => GetMessage('BAY_PROPS_FOOT'),
						"ACTIVE" => "Y",
						"SORT" => "20",
						"CODE" => "BETS",
						"IBLOCK_ID" => $ID,
						"PROPERTY_TYPE" => "N",
						"IS_REQUIRED"=>"Y",
					),
					array(
						"NAME" => GetMessage('BAY_PROPS_CATALOG'),
						"ACTIVE" => "Y",
						"SORT" => "30",
						"CODE" => "PRODUCTS",
						"IBLOCK_ID" => $ID,
						"PROPERTY_TYPE" => "E",
						"LINK_IBLOCK_ID"=>$CATALOG_ID,
						"MULTIPLE"=>"N",
						"IS_REQUIRED"=>"Y",
					)
				);
				$ibp = new CIBlockProperty;
				foreach ($arFields as $arField)
					$PropID = $ibp->Add($arField);
					
				//user props
				$arFields = array(
					array(
						"NAME" => GetMessage('BAY_PROPS_PRODUCT_ID'),
						"ACTIVE" => "Y",
						"SORT" => "10",
						"CODE" => "PRODUCT_ID",
						"IBLOCK_ID" => $IBLOCK_USER_ID,
						"PROPERTY_TYPE" => "E",
						"LINK_IBLOCK_ID"=>$CATALOG_ID,
						"MULTIPLE"=>"N",
						"IS_REQUIRED"=>"Y",
					),
					array(
						"NAME" => GetMessage('BAY_PROPS_AUCTION_ID'),
						"ACTIVE" => "Y",
						"SORT" => "20",
						"CODE" => "AUCTION_ID",
						"IBLOCK_ID" => $IBLOCK_USER_ID,
						"PROPERTY_TYPE" => "E",
						"LINK_IBLOCK_ID"=>$ID,
						"MULTIPLE"=>"N",
						"IS_REQUIRED"=>"Y",
					),
					array(
						"NAME" => GetMessage('BAY_PROPS_BETS'),
						"ACTIVE" => "Y",
						"SORT" => "20",
						"CODE" => "USER_BETS",
						"IBLOCK_ID" => $IBLOCK_USER_ID,
						"PROPERTY_TYPE" => "N",
						"LINK_IBLOCK_ID"=>"",
						"MULTIPLE"=>"N",
						"IS_REQUIRED"=>"Y",
					)
				);
				foreach ($arFields as $arField)
					$PropID = $ibp->Add($arField);
				
				$DB->Commit();
				
				if ($IBLOCK_USER_ID > 0 && $ID > 0)
				{
					$arProperty = array();
					$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $ID));
					while($arProp = $dbProperty->Fetch())
						$arProperty[$arProp["CODE"]] = $arProp["ID"];
				
					CUserOptions::SetOption(
						"list", 
						"tbl_iblock_list_".md5($IBLOCK_TYPE.".".$ID), 
						array ( 
							'columns' => 'ID,NAME,ACTIVE,SORT,DATE_ACTIVE_FROM,DATE_ACTIVE_TO,PROPERTY_'.$arProperty["PRICE_BEGIN"].',PROPERTY_'.$arProperty["BETS"].'', 
							'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20'
							)
					);
					
					CUserOptions::SetOption("form", "form_element_".$ID, 
					array ( 
						'tabs' => 'edit1--#--'.GetMessage("BAY_PAGE_TAB").'--,--ACTIVE--#--'.GetMessage("BAY_PAGE_ACTIVE").'--,--SORT--#--'.GetMessage("BAY_PAGE_SORT").'--,--ACTIVE_FROM--#--'.GetMessage("BAY_PAGE_DATE_BEGIN").'--,--ACTIVE_TO--#--'.GetMessage("BAY_PAGE_DATE_END").'--,--NAME--#--'.GetMessage("BAY_PAGE_USER_BETS").'--,--PROPERTY_'.$arProperty["PRICE_BEGIN"].'--#--'.GetMessage("BAY_PAGE_BAGIN_PRICE").'--,--PROPERTY_'.$arProperty["BETS"].'--#--'.GetMessage("BAY_PAGE_FOOT_BETS").'--,--PROPERTY_'.$arProperty["PRODUCTS"].'--#--'.GetMessage("BAY_PAGE_PRODUCTS").'--;--',
						)
					);
					
					$arProperty = array();
					$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $IBLOCK_USER_ID));
					while($arProp = $dbProperty->Fetch())
						$arProperty[$arProp["CODE"]] = $arProp["ID"];
				
					CUserOptions::SetOption(
						"list", 
						"tbl_iblock_list_".md5($IBLOCK_TYPE.".".$IBLOCK_USER_ID), 
						array ( 
							'columns' => 'ID,NAME,ACTIVE,DATE_CREATE,CREATED_USER_NAME,PROPERTY_'.$arProperty["AUCTION_ID"].',PROPERTY_'.$arProperty["PRODUCT_ID"].',PROPERTY_'.$arProperty["USER_BETS"].'', 
							'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20'
							)
					);
					
					CUserOptions::SetOption("form", "form_element_".$IBLOCK_USER_ID, 
					array ( 
						'tabs' => 'edit1--#--'.GetMessage("BAY_USER_TAB").'--,--ACTIVE--#--'.GetMessage("BAY_PAGE_ACTIVE").'--,--SORT--#--'.GetMessage("BAY_PAGE_SORT").'--,--NAME--#--'.GetMessage("BAY_USER_BETS_NAME").'--,--PROPERTY_'.$arProperty["AUCTION_ID"].'--#--'.GetMessage("BAY_USER_AUCTION_ID").'--,--PROPERTY_'.$arProperty["PRODUCT_ID"].'--#--'.GetMessage("BAY_USER_PRODUCT_ID").'--,--PROPERTY_'.$arProperty["USER_BETS"].'--#--'.GetMessage("BAY_USER_USER_BETS").'--;--',
						)
					);
				}
			}
			else
			{
				$DB->Rollback();
				return false;
			}
		}
		else
		{
			$DB->Rollback();
			return false;
		}
		
		return true;
	}
	
	function UnInstallFiles()
	{
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
		global $APPLICATION, $DOCUMENT_ROOT, $step, $SITE_ID, $CATALOG_ID;
		
		$step = intval($step);
		
		if ($step < 2)
			$APPLICATION->IncludeAdminFile(GetMessage('BAY_CATALOG'), $DOCUMENT_ROOT."/bitrix/modules/".self::MODULE_ID."/install/step1.php");			
		elseif ($step >= 2)
		{
			$this->InstallIblock(intval($CATALOG_ID), trim($SITE_ID));
			$this->InstallFiles();
			$this->InstallDB();
			RegisterModule(self::MODULE_ID);
		}
	}

	function DoUninstall()
	{
		global $APPLICATION;
		
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallFiles();
		$this->UnInstallDB();
	}
}
?>
