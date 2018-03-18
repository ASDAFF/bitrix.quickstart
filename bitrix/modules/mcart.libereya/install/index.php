<?
IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php");

Class mcart_libereya extends CModule
{
	const MODULE_ID = 'mcart.libereya';
	var $MODULE_ID = 'mcart.libereya'; 
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
		$this->MODULE_NAME = GetMessage("MCART_LIBEREYA_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MCART_LIBEREYA_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("MCART_LIBEREYA_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("MCART_LIBEREYA_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CMycompanyLibereya', 'OnBuildGlobalMenu');
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CMycompanyLibereya', 'OnBuildGlobalMenu');
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
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = true, $Recursive = true);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/files/addition_templates/'))
		{
			if ($dir = opendir($p))
			{
				
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = true, $Recursive = true);
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
	
	function InstallIblocks()
	{
		if (!$this->IncludeModule("iblock"))
		{
			die('no iblocks module connected!');
			return false;
		}	
		
		
		
		$iblocks = CIBlock::GetList(	Array(), 	Array(		
					'TYPE'			=> 'libereya_books',
					'SITE_ID'		=> $site_id, 		
					'ACTIVE'		=> 'Y', 				
					), false);
		while($ar_res = $iblocks->Fetch())
		{	
			$existed_iblocks[$ar_res['CODE']] = $ar_res['ID'];	
		}
		if(!empty($existed_iblocks['books']))
		{
			echo CAdminMessage::ShowMessage(GetMessage("MCART_LIBEREYA_BOOKS_EXISTS"));
			return false;
		}
		elseif(!empty($existed_iblocks['ganres']) || !empty($existed_iblocks['authors']))
		{
			echo CAdminMessage::ShowMessage(GetMessage("MCART_LIBEREYA_SECONDARY_IBLOCKS_EXISTS"));
			return false;
		}
		
		/*try to create libereya iblock_type*/
		$arFields = Array(	
			'ID'=>'libereya_books',	
			'SECTIONS'=>'Y',	
			'IN_RSS'=>'N',	
			'SORT'=>100,	
			'LANG'=> Array(		
					'en'=>Array(			
						'NAME'=>'Library',			
						'SECTION_NAME'=>'Sections',			
						'ELEMENT_NAME'=>'Books'			
						),
					'ru'=>Array(			
							'NAME'=>GetMessage("MCART_LIBEREYA_IBLOCK_TYPE_NAME_LIB"),			
							'SECTION_NAME'=>GetMessage("MCART_LIBEREYA_IBLOCK_TYPE_NAME_EL_NAME"),			
							'ELEMENT_NAME'=>GetMessage("MCART_LIBEREYA_IBLOCK_TYPE_NAME_SECTIONS")			
						)		
					)		
				);
			$obBlocktype = new CIBlockType;
			$res = $obBlocktype->Add($arFields);
		
			$site_id = "s1";// (!empty(SITE_ID)) ? SITE_ID : 

			//create iblocks authors, ganres, books
			$arFieldsAuthors = array(
				"SITE_ID" => $site_id,
				"ACTIVE" => "Y",
				"IBLOCK_TYPE_ID" => "libereya_books",
				"NAME" => GetMessage("MCART_LIBEREYA_IBLOCK_NAME_AUTHORS"),
				"CODE" => "authors",		
				"SORT" => "30",
				"GROUP_ID" => Array("2"=>"R", "3"=>"R")
			);
			$arFieldsGanres = array(
				"SITE_ID" => $site_id,
				"ACTIVE" => "Y",
				"IBLOCK_TYPE_ID" => "libereya_books",
				"NAME" => GetMessage("MCART_LIBEREYA_IBLOCK_NAME_GANRES"),
				"CODE" => "ganres",		
				"SORT" => "20",
				"GROUP_ID" => Array("2"=>"R", "3"=>"R")
			);
			$arFieldsBooks = array(
				"SITE_ID" => $site_id,
				"ACTIVE" => "Y",
				"IBLOCK_TYPE_ID" => "libereya_books",
				"NAME" => GetMessage("MCART_LIBEREYA_IBLOCK_NAME_BOOKS"),
				"CODE" => "books",		
				"SORT" => "10",
				"GROUP_ID" => Array("2"=>"W", "3"=>"W"),
			);

			$arFieldsBooks['FIELDS']['DETAIL_PICTURE'] = array(
					"IS_REQUIRED" => "N", 
					"DEFAULT_VALUE" => array(
						"SCALE" => "Y", 
						//"WIDTH" => "344",  
						"HEIGHT" => "522", 
						"IGNORE_ERRORS" => "Y",  
						"METHOD" => "resample",
						"COMPRESSION" => "95", 
					),
				);
			$arFieldsBooks['FIELDS']['PREVIEW_PICTURE'] = array(
					"IS_REQUIRED" => "N", 
					"DEFAULT_VALUE" => array(
						"SCALE" => "Y", 
						//"WIDTH" => "112",  
						"HEIGHT" => "156", 
						"IGNORE_ERRORS" => "Y",  
						"METHOD" => "resample",
						"COMPRESSION" => "95", 
						"FROM_DETAIL" => "Y",
					),
				);


			$ib = new CIBlock; 
			$authors_iblock_ID = (empty($existed_iblocks['authors']))
				? $ib->Add($arFieldsAuthors)
				: $existed_iblocks['authors'];
			$ganres_iblock_ID = (empty($existed_iblocks['ganres']))
				? $ib->Add($arFieldsGanres)
				: $existed_iblocks['ganres'];
			$books_iblock_ID = $ib->Add($arFieldsBooks);
			if(!$books_iblock_ID)
			{
				echo CAdminMessage::ShowMessage(GetMessage("MCART_LIBEREYA_ERROR_NO_BOOKS"));
				return false;
			}
			//create fields for books iblock

			$ibp = new CIBlockProperty;
			$arFields = Array( "NAME" => GetMessage("ML_IBLOCK_PROP_NAME_IS_NEW"), "ACTIVE" => "Y", "SORT" => "10", "CODE" => "IS_NEW", "LIST_TYPE" => "C", "PROPERTY_TYPE" => "L", "IBLOCK_ID" => $books_iblock_ID);
			$arFields["VALUES"][0] = Array(  "VALUE" => GetMessage("ML_IBLOCK_PROP_ENUM_YES"),  "DEF" => "N",  "SORT" => "100");
			$ibp->Add($arFields);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_GANRES"), "ACTIVE" => "Y",  "CODE" => "GANRES", "PROPERTY_TYPE" => "E", "MULTIPLE" => "Y", "LINK_IBLOCK_ID" => $ganres_iblock_ID, "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_AUTHORS"), "ACTIVE" => "Y",  "CODE" => "AUTHORS", "PROPERTY_TYPE" => "E", "MULTIPLE" => "Y", "LINK_IBLOCK_ID" => $authors_iblock_ID, "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);
			
			$arFields = Array( "NAME" => GetMessage("ML_IBLOCK_PROP_NAME_BOOK_TYPE"), "ACTIVE" => "Y", "SORT" => "20", "CODE" => "BOOK_TYPE", "PROPERTY_TYPE" => "L", "LIST_TYPE" => "C",  "IBLOCK_ID" => $books_iblock_ID);
			$arFields["VALUES"][0] = Array(  "VALUE" => GetMessage("ML_IBLOCK_PROP_NAME_BOOK_TYPE_PAPER"),  "DEF" => "N",  "SORT" => "100");
			$arFields["VALUES"][1] = Array(  "VALUE" => GetMessage("ML_IBLOCK_PROP_NAME_BOOK_TYPE_EL"),  "DEF" => "N",  "SORT" => "200");
			$ibp->Add($arFields);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_BOOK_FILE"), "SORT" => "200", "ACTIVE" => "Y",  "CODE" => "BOOK_FILE", "MULTIPLE" => "Y", "PROPERTY_TYPE" => "F", "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_RATING_AVG"), "ACTIVE" => "Y",  "CODE" => "RATING_AVG", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $books_iblock_ID);      
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_PUBLISHER"), "ACTIVE" => "Y",  "CODE" => "PUBLISHER", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $books_iblock_ID);      
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_SERIAL"), "ACTIVE" => "Y",  "CODE" => "SERIAL", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $books_iblock_ID);      
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_AGE_LIMITS"), "ACTIVE" => "Y",  "CODE" => "AGE_LIMITS", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $books_iblock_ID);      
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_BOOKING"), "ACTIVE" => "Y",  "CODE" => "BOOKING", "PROPERTY_TYPE" => "S", "USER_TYPE" => "UserID", "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);

			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_RETURN_MESSAGE"), "ACTIVE" => "Y",  "CODE" => "RETURN_MESSAGE", "MULTIPLE" => "Y", "PROPERTY_TYPE" => "S", "USER_TYPE" => "UserID", "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_READER"), "ACTIVE" => "Y",  "CODE" => "READER", "PROPERTY_TYPE" => "S", "USER_TYPE" => "UserID", "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);

			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_vote_count"), "ACTIVE" => "Y",  "CODE" => "vote_count", "PROPERTY_TYPE" => "N", "COL_COUNT" => "3", "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_vote_sum"), "ACTIVE" => "Y",  "CODE" => "vote_sum", "PROPERTY_TYPE" => "N", "COL_COUNT" => "3", "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_rating"), "ACTIVE" => "Y",  "CODE" => "rating", "PROPERTY_TYPE" => "N", "COL_COUNT" => "3", "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_FORUM_TOPIC_ID"), "ACTIVE" => "Y",  "CODE" => "FORUM_TOPIC_ID", "PROPERTY_TYPE" => "N", "COL_COUNT" => "3", "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_FORUM_MESSAGE_CNT"), "ACTIVE" => "Y",  "CODE" => "FORUM_MESSAGE_CNT", "PROPERTY_TYPE" => "N", "COL_COUNT" => "3", "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);
			
			$prop = Array("NAME" => GetMessage("ML_IBLOCK_PROP_NAME_BOOKING_TIME"), "SORT" => "500", "ACTIVE" => "Y",  "CODE" => "BOOKING_TIME", "USER_TYPE" => "DateTime", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $books_iblock_ID);
			$ibp->Add($prop);
			
			$books_propsIDs = array();
			$property_enums = CIBlockPropertyEnum::GetList(
					Array("DEF"=>"DESC", "SORT"=>"ASC"),
					Array("IBLOCK_ID"=>$books_iblock_ID));
			while($enum_fields = $property_enums->GetNext()){
				$books_propsIDs[$enum_fields['PROPERTY_CODE']][$enum_fields['VALUE']] = $enum_fields['ID'];
			}

			/*add books*/
			$el	= new CIBlockElement;
			
			/*add authors*/
			$authors = array();
			$ganres = array();
			$authors[] = $this->addIblockElement(GetMessage("ML_BOOK_AUTHORS_PUSHKIN"), $authors_iblock_ID);
			/*add ganres*/
			$ganres[] = $this->addIblockElement(GetMessage("ML_BOOK_GANRES_CLASSIC"), $ganres_iblock_ID);
			
			$arFields = array(
				'IBLOCK_ID'				=> $books_iblock_ID,
				'SORT'					=> "10",
				'ACTIVE'				=>  "Y",
				'NAME'					=> GetMessage('ML_BOOK_ATTRS_EVGENIY_ONEGIN'),
				'PREVIEW_TEXT'			=> GetMessage('ML_BOOK_ATTRS_ROMAN_V_STIHAH'),
				'DETAIL_TEXT'			=> GetMessage('ML_BOOK_ATTRS_ROMAN_V_STIHAH'),
				//'PREVIEW_PICTURE'		=> CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/files/images/onegin.jpg'),
				'DETAIL_PICTURE'		=> CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/files/images/onegin.jpg'),
			);
			$arFields["PROPERTY_VALUES"]['BOOK_TYPE'] = array("VALUE" => $books_propsIDs['BOOK_TYPE'][GetMessage("ML_IBLOCK_PROP_NAME_BOOK_TYPE_PAPER")]);
			
			$this->addBook($arFields, $authors, $ganres);
			
			/*add authors*/
			$authors = array();
			$ganres = array();
			$authors[] = $this->addIblockElement(GetMessage("ML_BOOK_AUTHORS_GOGOL"), $authors_iblock_ID);
			/*add ganres*/
			$ganres[] = $this->addIblockElement(GetMessage("ML_BOOK_GANRES_CLASSIC"), $ganres_iblock_ID);
			
			$arFields = array(
				'IBLOCK_ID'				=> $books_iblock_ID,
				'SORT'					=> "10",
				'ACTIVE'				=>  "Y",
				'NAME'					=> GetMessage('ML_BOOK_ATTRS_NOS'),
				'PREVIEW_TEXT'			=> GetMessage('ML_BOOK_ATTRS_POVEST'),
				'DETAIL_TEXT'			=> GetMessage('ML_BOOK_ATTRS_POVEST'),
				//'PREVIEW_PICTURE'		=> CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/files/images/nos.jpg'),
				'DETAIL_PICTURE'		=> CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/files/images/nos.jpg'),
			);
			$arFields["PROPERTY_VALUES"]['BOOK_TYPE'] = array("VALUE" => $books_propsIDs['BOOK_TYPE'][GetMessage("ML_IBLOCK_PROP_NAME_BOOK_TYPE_EL")]);
			$arFields["PROPERTY_VALUES"]['BOOK_FILE'] = array("VALUE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/files/books/nos_.pdf'),);
			
			$this->addBook($arFields, $authors, $ganres);
			
			/*add authors*/
			$authors = array();
			$ganres = array();
			$authors[] = $this->addIblockElement(GetMessage("ML_BOOK_ATTRS_DAN_BROWN"), $authors_iblock_ID);
			/*add ganres*/
			$ganres[] = $this->addIblockElement(GetMessage("ML_BOOK_GANRES_POEMS"), $ganres_iblock_ID);
			
			$arFields = array(
				'IBLOCK_ID'				=> $books_iblock_ID,
				'SORT'					=> "10",
				'ACTIVE'				=>  "Y",
				'NAME'					=> GetMessage("ML_BOOK_ATTRS_INFERNO"),
				'PREVIEW_TEXT'			=> GetMessage("ML_BOOK_ATTRS_ROMAN"),
				'DETAIL_TEXT'			=> GetMessage("ML_BOOK_ATTRS_ROMAN"),
				'DETAIL_PICTURE'		=> CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/files/images/den_braun.jpg'),
				//'PREVIEW_PICTURE'		=> CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/files/images/den_braun.jpg'),
				
			);
			$arFields["PROPERTY_VALUES"]['BOOK_TYPE'] = array("VALUE" => $books_propsIDs['BOOK_TYPE'][GetMessage("ML_IBLOCK_PROP_NAME_BOOK_TYPE_PAPER")]);
			
			$this->addBook($arFields, $authors, $ganres);
			
			
			/*add authors*/
			$authors = array();
			$ganres = array();
			$authors[] = $this->addIblockElement('Scott Chacon', $authors_iblock_ID);
			/*add ganres*/
			$ganres[] = $this->addIblockElement(GetMessage("ML_BOOK_GANRES_PROGRAMMING"), $ganres_iblock_ID);
			
			$arFields = array(
				'IBLOCK_ID'				=> $books_iblock_ID,
				'SORT'					=> "10",
				'ACTIVE'				=>  "Y",
				'NAME'					=> GetMessage('ML_BOOK_ATTRS_PRO_GIT'),
				'PREVIEW_TEXT'			=> GetMessage('ML_BOOK_ATTRS_PRO_GIT_TEXT'),
				'DETAIL_TEXT'			=> "",
				//'PREVIEW_PICTURE'		=> CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/files/images/progit.jpg'),
				'DETAIL_PICTURE'		=> CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/files/images/progit.jpg'),
			);
			$arFields["PROPERTY_VALUES"]['BOOK_TYPE'] = array("VALUE" => $books_propsIDs['BOOK_TYPE'][GetMessage("ML_IBLOCK_PROP_NAME_BOOK_TYPE_PAPER")]);
			
			$this->addBook($arFields, $authors, $ganres);
			
			
			
			
			//$books_iblock_ID = WizardServices::ImportIBlockFromXML($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/'.self::MODULE_ID.'/files/books_iblock.xml', 'books', 'libereya_books', $site_id);
			
			
			
			return true;	
	}

	function addIblockElement($name, $iblock_id)
	{
		static $elements;
		$el	= new CIBlockElement;
		
		$arFields = array(
				'IBLOCK_ID'				=> $iblock_id,
				'ACTIVE'				=>  "Y",
				'NAME'					=> $name,
			);
		/*name uniq checking*/
		if(!is_array($elements[$iblock_id]) || !$element_id = array_search($name, $elements[$iblock_id]))	
		{
			$element_id = $el->Add($arFields);
			$elements[$iblock_id][$element_id] = $name;
		}		
		 
		 return $element_id;
	}
	
	function addBook($arFields, $authors, $ganres)
	{
		$el	= new CIBlockElement;
		foreach($authors as $author)
			{
				$arFields['PROPERTY_VALUES']['AUTHORS'][] = array('VALUE' => $author); 
			}	
			foreach($ganres as $ganre)
			{
				$arFields['PROPERTY_VALUES']['GANRES'][] = array('VALUE' => $ganre); 
			}
		$el->Add($arFields, false, false, true);
	}
	
	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		if(!$this->InstallIblocks())
		{
			//$this->DoUninstall();
			//return false;
		}
		//$this->InstallDB();
		RegisterModule(self::MODULE_ID);
		
		$APPLICATION->IncludeAdminFile(GetMessage("MCART_LIBEREYA_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/step.php");
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		//$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}
?>
