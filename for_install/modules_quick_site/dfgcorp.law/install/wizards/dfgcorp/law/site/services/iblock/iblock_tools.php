<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if(!CModule::IncludeModule('iblock'))
	return;

//Library and language
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/".strtolower($GLOBALS["DB"]->type)."/cml2.php");
__IncludeLang(GetLangFileName(dirname(__FILE__)."/lang/", "/".basename(__FILE__)));

//Set options which will overwrite defaults
COption::SetOptionString("iblock", "use_htmledit", "Y");
COption::SetOptionString("iblock", "combined_list_mode", "Y");

//Copy public files with "on the fly" translation
function DEMO_IBlock_CopyFiles($source, $target, $bReWriteAdditionalFiles = false, $search = false, $replace = false)
{
	if(is_array($source))
	{
		$source_base = $source[0];
		$source_abs = $source_base.$source[1];
		$source = $source[1];
	}
	else
	{
		$source_base = dirname(__FILE__);
		$source_abs = $source_base.$source;
	}
	$target_abs = $_SERVER['DOCUMENT_ROOT'].$target;

	if(file_exists($source_abs) && is_dir($source_abs))
	{
		//Create target directory
		CheckDirPath($target_abs);
		$dh = opendir($source_abs);
		//Read the source
		while($file = readdir($dh))
		{
			if($file == "." || $file == "..")
				continue;
			$target_file = $target_abs.$file;
			if($bReWriteAdditionalFiles || !file_exists($target_file))
			{
				//Here we will write public data
				$source_file = $source_abs.$file;
				if(is_dir($source_file))
					continue;
				$fh = fopen($source_file, "rb");
				$php_source = fread($fh, filesize($source_file));
				fclose($fh);
				//Replace real IDs
				if(is_array($search) && is_array($replace))
				{
					$php_source = str_replace($search, $replace, $php_source);
				}
				//Parse GetMessage("MESSAGE_ID") with double quotes
				if(preg_match_all('/GetMessage\("(.*?)"\)/', $php_source, $matches))
				{
					//Include LANGUAGE_ID file
					__IncludeLang(GetLangFileName($source_base."/lang/", $source.$file));
					//Substite the stuff
					foreach($matches[0] as $i => $text)
					{
						$php_source = str_replace(
							$text,
							'"'.GetMessage($matches[1][$i]).'"',
							$php_source
						);
					}
				}
				//Parse GetMessage('MESSAGE_ID') with single quotes
				//embedded html
				if(preg_match_all('/GetMessage\(\'(.*?)\'\)/', $php_source, $matches))
				{
					//Include LANGUAGE_ID file
					__IncludeLang(GetLangFileName($source_base."/lang/", $source.$file));
					//Substite the stuff
					foreach($matches[0] as $i => $text)
					{
						$php_source = str_replace(
							$text,
							GetMessage($matches[1][$i]),
							$php_source
						);
					}
				}
				//Write to the destination directory
				$fh = fopen($target_file, "wb");
				fwrite($fh, $php_source);
				fclose($fh);
				@chmod($target_file, BX_FILE_PERMISSIONS);
			}
		}
	}
}

//Add left menu
function DEMO_IBlock_AddMenuItem($menuFile, $menuItem, $site_id = "s1")
{
	if(CModule::IncludeModule('fileman'))
	{
		$arResult = CFileMan::GetMenuArray($_SERVER["DOCUMENT_ROOT"].$menuFile);
		$arMenuItems = $arResult["aMenuLinks"];
		$menuTemplate = $arResult["sMenuTemplate"];

		$bFound = false;
		foreach($arMenuItems as $item)
			if($item[1] == $menuItem[1])
				$bFound = true;

		if(!$bFound)
		{
			$arMenuItems[] = $menuItem;
			CFileMan::SaveMenu(Array($site_id, $menuFile), $arMenuItems, $menuTemplate);
		}
	}
}

//Import XML File
function DEMO_IBlock_ImportXML($xml_dir, $file, $site_id, $xml_force = false, $workflow = false)
{
	if(strlen($file) <= 0)
		return false;
	//$xml_dir = $_SERVER['DOCUMENT_ROOT']."/bitrix/wizards/bitrix/demo/modules/iblock/xml/".LANGUAGE_ID;
	if(file_exists($xml_dir) && is_dir($xml_dir))
	{
		if(is_file($xml_dir."/".$file))
		{
			if(preg_match("/^(\d\d\d)_([a-z]+)_(.+)_([a-z]{2})\\.xml$/", $file, $match))
			{
				$documentRoot = rtrim(str_replace(Array("\\\\", "//"), Array("\\", "/"), $_SERVER["DOCUMENT_ROOT"]), "\\/");
				$arFile = array(
					"DIR" => substr($xml_dir, strlen($documentRoot)),
					"NAME" => $file,
					"TYPE" => $match[2],
					"XML_ID" => $match[3],
					"LANG" => $match[4],
				);
				if($arFile["TYPE"] == "FUTURE")
					$arFile["TYPE"] = "xmlcatalog";
				//Check if iblock exists
				$obIBlock = new CIBlock;
				$rsIBlock = $obIBlock->GetList(array(), array("XML_ID" => $arFile["XML_ID"]));
				$arIBlock = $rsIBlock->Fetch();
				if(!$arIBlock || ($xml_force === true))
				{
					//Create iblock type
					DEMO_IBlock_CreateType(array($arFile["TYPE"]));

					ImportXMLFile($arFile["DIR"]."/".$arFile["NAME"], $arFile["TYPE"], array($site_id), "N", "N", false, false, true, true, true);

					$rsIBlock = $obIBlock->GetList(array(), array("XML_ID" => $arFile["XML_ID"]));
					$arIBlock = $rsIBlock->Fetch();
					if(is_array($arIBlock))
					{
						$obIBlock = new CIBlock;

						if($workflow === "bizproc")
						{
							if(IsModuleInstalled('bizproc'))
								$obIBlock->Update($arIBlock["ID"], array("WORKFLOW" => "N", "BIZPROC" => "Y"));
							else
								$obIBlock->Update($arIBlock["ID"], array("WORKFLOW" => "Y", "BIZPROC" => "N"));
						}
						elseif($workflow)
							$obIBlock->Update($arIBlock["ID"], array("WORKFLOW" => "Y"));
					}
				}
				if($arIBlock["ID"])
				{
					$obIBlock = new CIBlock;
					$obIBlock->SetPermission($arIBlock["ID"], array(1=>"X",2=>"R"));
				}
				return $arIBlock["ID"];
			}
		}
	}
	return false;
}

function DEMO_IBlock_CreateType($IBLOCK_TYPES = false)
{
	if(!is_array($IBLOCK_TYPES))
		$IBLOCK_TYPES = array("news", "services", "photo", "books", "articles", "paid", "xmlcatalog");

	$arTypes = array(
		"news" => array(
			"ID" => "news",
			"SECTIONS" => "Y",
			"IN_RSS" => "N",
			"SORT" => 10,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("DEMO_IBLOCK_TYPE_NEWS_NAME"),
					"SECTION_NAME" => GetMessage("DEMO_IBLOCK_TYPE_NEWS_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("DEMO_IBLOCK_TYPE_NEWS_ELEMENT_NAME"),
				),
			),
		),
		"articles" => array(
			"ID" => "articles",
			"SECTIONS" => "Y",
			"IN_RSS" => "N",
			"SORT" => 20,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("DEMO_IBLOCK_TYPE_ARTICLES_NAME"),
					"SECTION_NAME" => GetMessage("DEMO_IBLOCK_TYPE_ARTICLES_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("DEMO_IBLOCK_TYPE_ARTICLES_ELEMENT_NAME"),
				),
			),
		),
		"photo" => array(
			"ID" => "photo",
			"SECTIONS" => "Y",
			"IN_RSS" => "N",
			"SORT" => 40,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("DEMO_IBLOCK_TYPE_PHOTO_NAME"),
					"SECTION_NAME" => GetMessage("DEMO_IBLOCK_TYPE_PHOTO_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("DEMO_IBLOCK_TYPE_PHOTO_ELEMENT_NAME"),
				),
			),
		),
		"services" => array(
			"ID" => "services",
			"SECTIONS" => "Y",
			"IN_RSS" => "N",
			"SORT" => 50,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("DEMO_IBLOCK_TYPE_SERVICES_NAME"),
					"SECTION_NAME" => GetMessage("DEMO_IBLOCK_TYPE_SERVICES_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("DEMO_IBLOCK_TYPE_SERVICES_ELEMENT_NAME"),
				),
			),
		),
		"books" => array(
			"ID" => "books",
			"SECTIONS" => "Y",
			"IN_RSS" => "N",
			"SORT" => 60,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("DEMO_IBLOCK_TYPE_BOOKS_NAME"),
					"SECTION_NAME" => GetMessage("DEMO_IBLOCK_TYPE_BOOKS_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("DEMO_IBLOCK_TYPE_BOOKS_ELEMENT_NAME"),
				),
			),
		),
		"paid" => array(
			"ID" => "paid",
			"SECTIONS" => "N",
			"IN_RSS" => "N",
			"SORT" => 70,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("DEMO_IBLOCK_TYPE_PAID_NAME"),
					"SECTION_NAME" => GetMessage("DEMO_IBLOCK_TYPE_PAID_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("DEMO_IBLOCK_TYPE_PAID_ELEMENT_NAME"),
				),
			),
		),
		"xmlcatalog" => array(
			"ID" => "xmlcatalog",
			"SECTIONS" => "Y",
			"IN_RSS" => "N",
			"SORT" => 80,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("DEMO_IBLOCK_TYPE_XMLCATALOG_NAME"),
					"SECTION_NAME" => GetMessage("DEMO_IBLOCK_TYPE_XMLCATALOG_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("DEMO_IBLOCK_TYPE_XMLCATALOG_ELEMENT_NAME"),
				),
			),
		),
		"lists" => array(
			"ID" => "lists",
			"SECTIONS" => "Y",
			"IN_RSS" => "N",
			"SORT" => 90,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("DEMO_IBLOCK_TYPE_LISTS_NAME"),
					"SECTION_NAME" => GetMessage("DEMO_IBLOCK_TYPE_LISTS_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("DEMO_IBLOCK_TYPE_LISTS_ELEMENT_NAME"),
				),
			),
		),
	);

	foreach($IBLOCK_TYPES as $TYPE_ID)
	{
		if(array_key_exists($TYPE_ID, $arTypes))
		{
			$obType = new CIBlockType;
			$arFields = $arTypes[$TYPE_ID];
			$rsType = $obType->GetList(array(),array("=ID" => $arFields["ID"]));
			if($arType = $rsType->Fetch())
				continue;
			else
				$obType->Add($arFields);
		}
	}
}

//Add group or return existent
function DEMO_IBlock_AddUserGroup($id, $name, $description)
{
	$rsGroup = CGroup::GetList($by = "c_sort", $order = "asc", array(
		"STRING_ID_EXACT_MATCH" => "Y",
		"STRING_ID" => $id,
	));
	if($arGroup = $rsGroup->Fetch())
	{
		$result = $arGroup["ID"];
	}
	else
	{
		$obGroup = new CGroup;
		$result = $obGroup->Add(array(
			"ACTIVE" => "Y",
			"C_SORT" => 500,
			"NAME" => $name,
			"DESCRIPTION" => $description,
			"STRING_ID" => $id,
		));
	}
	return $result;
}

function DEMO_IBlock_EditFormLayout($IBLOCK_ID, $arFormOptions)
{
	$arTabs = array();
	foreach($arFormOptions as $tab_id => $arTab)
	{
		$arFields = array($tab_id."--#--".$arTab["TITLE"]);
		foreach($arTab["FIELDS"] as $FIELD_ID => $FIELD_LABEL)
			$arFields[] = $FIELD_ID."--#--".$FIELD_LABEL;
		$arTabs[] = implode("--,--", $arFields);
	}
	$s = implode("--;--", $arTabs);
	CUserOptions::SetOption("form", "form_element_".$IBLOCK_ID, array("tabs"  => $s), true);
}

?>