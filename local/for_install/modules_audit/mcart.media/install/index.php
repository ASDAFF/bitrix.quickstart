<?
IncludeModuleLangFile( __FILE__);

Class mcart_media extends CModule
{
	var $MODULE_ID = "mcart.media";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";
	
	
	function mcart_media() 
	{
		$arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }else{
            $this->MODULE_VERSION=MS_MODULE_VERSION;
            $this->MODULE_VERSION_DATE=MS_MODULE_VERSION_DATE;
        }

        $this->MODULE_NAME = GetMessage('MM_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('MM_MODULE_DESCRIPTION');
        
        $this->PARTNER_NAME = GetMessage("MM_PARTNER_NAME");
        $this->PARTNER_URI  = "http://mcart.ru/";
	}
	
	
	
	
	function DoInstall()
	{
				
		if (!IsModuleInstalled("mcart.media"))
		{
		
			global $APPLICATION;
			if (!CModule::IncludeModule('iblock'))
			{$APPLICATION->ThrowException(GetMessage("MODULE_IBLOCK_NOT_INSTALLED"));
			
			return false;
			}
			$iblock_id	= 	$this->AddIB(GetMessage('MM_IBLOCK_NAME'), 'medialibrary'); 	
			if (!$iblock_id)
			return false;
			$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.media/prolog.php", "w");
				fwrite($handle, '<?define("MEDIALIBRARY_IBLOCK_ID", '.$iblock_id.');?>');
				fclose($handle);
			
			$this->AddUserFieldByMediacollection($iblock_id);
			
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
			
		}
		return true;
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		$this->UnInstallEvents();
		$this->UnInstallFiles();
		
		return true;
	}

	
	function InstallDB() {
		
		
			RegisterModule("mcart.media");	
			return true;
		}
				
	
	
	function UnInstallDB()
	{
		UnRegisterModule("mcart.media");
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
	{CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.media/install/images",  $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/mcart.media", true, True);
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.media/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
	return true;
	}
	
	function UnInstallFiles()
	{	DeleteDirFilesEx("/bitrix/images/mcart.media/");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.media/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		return true;
	}	

	//==================================================
	function FillIBlocks($iblock_id)
	{
	$arFields=array(
	"0" => Array(	
	'IBLOCK_ID' => $iblock_id,
    'NAME'=> 'MEDIA_ID',
    'ACTIVE' => 'Y',
    'CODE' => 'MEDIA_ID',
    'PROPERTY_TYPE' => 'N'
	),
	"1" => array(
	'IBLOCK_ID' => $iblock_id,
	'NAME' => 'REAL_PICTURE',
    'ACTIVE' => 'Y',
    'CODE' => 'REAL_PICTURE',
    'PROPERTY_TYPE' => 'F'
	),
	
	
	);
	
	$ibp = new CIBlockProperty;
	for ($key = 0, $size = count($arFields); $key < $size; $key++){
		$PropID = $ibp->Add($arFields[$key]);}
	}
	
	function AddIB($name, $code){
	
		$str_type = GetMessage("MM_IBLOCK_TYPE_EN");
		$str_type_ru =GetMessage("MM_IBLOCK_TYPE_RU");
		$db_iblock_type =CIBlockType::GetByID($str_type);
		if (!$db_iblock_type->Fetch())
			{$arFields = Array(
				'ID'=>$str_type,
				'SECTIONS'=>'Y',
				'IN_RSS'=>'N',
				'SORT'=>100,
				'LANG'=>Array(
				'en'=>Array(
					'NAME'=>$str_type),
				'ru'=>Array(
					'NAME'=>$str_type_ru)
				)
			);

		$obBlocktype = new CIBlockType;
		if (!$res = $obBlocktype->Add($arFields))
			{echo "error:".$obBlocktype->LAST_ERROR;
			return false;}
			
			}
		
		$ib = new CIBlock;
		$arFields = Array(
		"ACTIVE" => "Y",
		"NAME" => $name,
		"CODE" => $code,
		"IBLOCK_TYPE_ID" => $str_type,
		"SITE_ID" => Array('s1') ,
		'WORKFLOW' => 'N');
		
		if (!$ID = $ib->Add($arFields))
		{
			echo "error iblock create:".$ib->LAST_ERROR;
			return false;
		}
		else
			{
				$this-> FillIBlocks($ID);
				return $ID;
			}
		}
	
		function AddUserFieldByMediacollection($iblock_id)
		{
			$oUserTypeEntity    = new CUserTypeEntity();
 
			$aUserFields    = array(
				'ENTITY_ID'         => 'IBLOCK_'.$iblock_id.'_SECTION',
				'FIELD_NAME'        => 'UF_COLLECTION',
				'USER_TYPE_ID'      => 'double',
				'XML_ID'            => 'XML_UF_COLLECTION',
				'SORT'              => 10,
				'MULTIPLE'          => 'N',
				'MANDATORY'         => 'N',
				'SHOW_FILTER'       => 'N',
				'SHOW_IN_LIST'      => '',
				'EDIT_IN_LIST'      => '',
				'IS_SEARCHABLE'     => 'N',
				'SETTINGS'          => array(
					),
				'EDIT_FORM_LABEL'   => array(
					'ru'    =>GetMessage('MM_UF_COLLECTION_NAME'),
					'en'    => 'id collection', 
				),
				'LIST_COLUMN_LABEL' => array(
					'ru'    => GetMessage('MM_UF_COLLECTION_NAME'),
					'en'    => 'id collection', 
				),
				'LIST_FILTER_LABEL' => array(
					'ru'    => GetMessage('MM_UF_COLLECTION_NAME'),
					'en'    => 'id collection',
			)
			);
			 
			$iUserFieldId   = $oUserTypeEntity->Add( $aUserFields ); // int
		}

} //end class
	?>	