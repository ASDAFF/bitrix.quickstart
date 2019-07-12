<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if(!CModule::IncludeModule('iblock'))
	return;

//Library and language
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/".strtolower($GLOBALS["DB"]->type)."/cml2.php");
$package->IncludeWizardLang('scripts/'.basename(__FILE__));
__IncludeLang(GetLangFileName(dirname(__FILE__)."/../lang/", "/".basename(__FILE__)));

//Set options which will overwrite defaults
//COption::SetOptionString("iblock", "use_htmledit", "Y");
//COption::SetOptionString("iblock", "combined_list_mode", "Y");


//Copy public files with "on the fly" translation
function ArealCopyFiles($source, $target, $bReWriteAdditionalFiles = false, $search = false, $replace = false)
{
	$source_abs = $source;
	$target_abs = $target;
	if (file_exists($source_abs)) {
		if(is_dir($source_abs))
		{
			//Create target directory
			CheckDirPath($target_abs);
			$dh = opendir($source_abs);
			//Read the source
			while($file = readdir($dh))
			{
				if($file == "." || $file == "..")
					continue;
					
				$source_file = realpath($source_abs.DIRECTORY_SEPARATOR.$file);
				$target_file = str_replace(array("/\\","\\/","//","\\\\"),"/",$target_abs.DIRECTORY_SEPARATOR.$file);
				ArealCopyFiles($source_file, $target_file, $bReWriteAdditionalFiles, $search, $replace);
			}
			closedir($dh);
		}
		else
		{
			$source_file = realpath($source_abs);
			$target_file = str_replace(array("/\\","\\/","//","\\\\"),"/",$target_abs);

			$fh = fopen($source_file, "rb");
			$php_source = fread($fh, filesize($source_file));
			fclose($fh);
			//Replace real IDs
			if(is_array($search) && is_array($replace))
			{
				$php_source = str_replace($search, $replace, $php_source);
			}
			//Write to the destination directory
			if ($bok = RewriteFile($target_file,$php_source)) {
				chmod($target_file, BX_FILE_PERMISSIONS);
			}
		}
	}
}

//Copy public files with "on the fly" translation
function ArealMagic($source, $target, $replace = null)
{
	$source_abs = $source;
	$target_abs = $target;
	if (file_exists($source_abs)) {
		if(is_dir($source_abs))
		{
			//Create target directory
			CheckDirPath($target_abs);
			$dh = opendir($source_abs);
			//Read the source
			while($file = readdir($dh))
			{
				if($file == "." || $file == "..")
					continue;
					
				$source_file = realpath($source_abs.DIRECTORY_SEPARATOR.$file);
				$target_file = str_replace(array("/\\","\\/","//","\\\\"),"/",$target_abs.DIRECTORY_SEPARATOR.$file);
				ArealMagic($source_file, $target_file, $replace);
			}
			closedir($dh);
		}
		else
		{
			$source_file = realpath($source_abs);
			$target_file = str_replace(array("/\\","\\/","//","\\\\"),"/",$target_abs);

			$fh = fopen($source_file, "rb");
			$php_source = fread($fh, filesize($source_file));
			fclose($fh);
			//Replace macroses, if any
			if(is_array($replace) and !empty($replace))
			{
				$srch = array_keys($replace); foreach ($srch as $n => $s) {$srch[$n] = '#'.trim($s,'#').'#';}
				$rplc = array_values($replace);
				$php_source = str_replace($srch, $rplc, $php_source);
			}
			//Write to the destination directory
			RewriteFile($target_file,$php_source);
			chmod($target_file, BX_FILE_PERMISSIONS);
		}
	}
}

// Replace recursively
function SimplestArealMagic($target, $replace = null, $exclude = null)
{
	clearstatcache();
	$target_abs = $target;
//	AddMessage2Log('START process '.$target_file,'iblock');
	if (file_exists($target_abs) and !is_null($replace)) {
		if(is_dir($target_abs))
		{
			//Create target directory
			//CheckDirPath($target_abs);
			$dh = opendir($target_abs);
			//Read the source
			while($file = readdir($dh))
			{
				if($file == "." || $file == "..")
					continue;
				
				$target_file = realpath($target_abs.DIRECTORY_SEPARATOR.$file);
//				AddMessage2Log('Dive into '.$target_file,'iblock');
				SimplestArealMagic($target_file, $replace, $exclude);
			}
			closedir($dh);
		}
		elseif (is_file($target_abs))
		{
			if (is_string($exclude)) $exclude = array($exclude);
			elseif (!is_array($exclude)) $exclude = array();
			if (!empty($exclude))
			{
				foreach ($exclude as $pattern) {
					if (preg_match($pattern, $target_abs)) return;
				}
			}
			//$source_file = realpath($source_abs);
			$target_file = realpath($target_abs);

			$fh = fopen($target_file, "rb");
			$php_source = fread($fh, filesize($target_file));
			fclose($fh);
			//Replace macroses, if any
			$srch = array_keys($replace); foreach ($srch as $n => $s) {$srch[$n] = '#'.trim($s,'#').'#';}
			$rplc = array_values($replace);
			$php_source = str_replace($srch, $rplc, $php_source);

			//Write to the destination directory
			$ok = RewriteFile($target_file,$php_source);
			chmod($target_file, BX_FILE_PERMISSIONS);
//			AddMessage2Log('replace for '.$target_file.' is '.$ok,'iblock');
		}
	}
}

function SimpleArealMagic($filePath, $arReplace)
{

	clearstatcache();

	if ((!is_dir($filePath) && !is_file($filePath)) || !is_array($arReplace))
		return;

	if ($handle = @opendir($filePath))
	{
		while (($file = readdir($handle)) !== false)
		{
			if ($file == "." || $file == "..") continue;

			if (is_dir($filePath."/".$file))
			{
				SimpleArealMagic(str_replace(array("/\\","\\/","//","\\\\"),"/",$filePath.DIRECTORY_SEPARATOR.$file), $arReplace);
			}
			elseif (is_file($filePath."/".$file))
			{
/*
				if (!is_writable($filePath."/".$file) || !is_array($arReplace))
					return;
echo 'not writable file '.$file;
*/
				@chmod($filePath."/".$file, BX_FILE_PERMISSIONS);

				if (!$handleFile = @fopen($filePath."/".$file, "rb"))
					return;
echo 'cant read file '.$file;
				$content = @fread($handleFile, filesize($filePath."/".$file));
				@fclose($handleFile);

				$handleFile = false;
				if (!$handleFile = @fopen($filePath."/".$file, "wb"))
					return;
echo 'cant write file '.$file;

				if (flock($handleFile, LOCK_EX))
				{
					$arSearch = Array();
					$arValue = Array();

					foreach ($arReplace as $search => $replace)
					{
						if ($skipSharp)
							$arSearch[] = $search;
						else
							$arSearch[] = "#".$search."#";

						$arValue[] = $replace;
					}

					$content = str_replace($arSearch, $arValue, $content);
					//Write to the destination directory
					RewriteFile($target_file,$php_source);
					chmod($target_file, BX_FILE_PERMISSIONS);
					@fwrite($handleFile, $content);
					@flock($handleFile, LOCK_UN);
				}
				@fclose($handleFile);

			}
		}
		@closedir($handle);

	}
}

//Import XML File
function Areal_IBlock_ImportXML($file, $site_id, $params = array(), $workflow = false)
{
	AddMessage2Log(__FILE__.':'.__LINE__."\n".'Args:'."\n"
	.'$file: '.print_r($file,1)."\n"
	.'$site_id: '.print_r($site_id,1)."\n"
	.'$params: '.print_r($params,1)."\n"
	.'$workflow: '.print_r($workflow,1)."\n"
	,'iblock');
	// file exists
	if(!file_exists($file) or !is_file($file)) {
		AddMessage2Log(__FILE__.':'.__LINE__."\n".'Not such file! '.$file,'iblock');
		return false;
	}

	$xml_dir = dirname($file);
	$xml_name = basename($file);

	$documentRoot = rtrim(str_replace(Array("\\\\", "//"), Array("\\", "/"), $_SERVER["DOCUMENT_ROOT"]), "\\/");

	if(!empty($params))
		$arFile = array(
			"DIR" => substr($xml_dir, strlen($documentRoot)),
			"NAME" => $xml_name,
			"TYPE" => $params['type'],
			"XML_ID" => $params['code'],
			"LANG" => LANGUAGE_ID,
		);
	elseif(preg_match("/^([a-z]+)_(.+)_([a-z]{2})\\.xml$/", $file, $match))
		$arFile = array(
			"DIR" => substr($xml_dir, strlen($documentRoot)),
			"NAME" => $xml_name,
			"TYPE" => $match[2],
			"XML_ID" => $match[3],
			"LANG" => $match[4],
		);
	else {// incorrect args
		AddMessage2Log(__FILE__.':'.__LINE__."\n".'incorrect args','iblock');
		return false;
	}

	AddMessage2Log(__FILE__.':'.__LINE__."\n".'$arFile:'."\n".print_r($arFile,1),'iblock');
	//Check if iblock exists
	$rsIBlock = CIBlock::GetList(array(), array("TYPE" => $arFile["TYPE"],"CODE" => $arFile["XML_ID"]));
	$arIBlock = $rsIBlock->Fetch();
	AddMessage2Log('$arIBlock:'.print_r($arIBlock,1),'iblock');
	if (!$arIBlock) {// if not found iblock - try to import it

		$error_message = ImportXMLFile($arFile["DIR"]."/".$arFile["NAME"], $arFile["TYPE"], array($site_id), "N", "N",false,false,false,true);
		if ($error_message !== true) {
			AddMessage2Log(__FILE__.':'.__LINE__."\n".'Error in iblock importing '.$arFile["NAME"].'. ImportXMLFile:'.$error_message,'iblock');
			return false;
		}
		AddMessage2Log(__FILE__.':'.__LINE__.":".' IBlock successfully imported with ImportXMLFile','iblock');

		// get Iblock ID
		$rsIBlock = CIBlock::GetList(array(), array("TYPE" => $arFile["TYPE"],"CODE" => $arFile["XML_ID"]));
		if (!$arIBlock = $rsIBlock->Fetch()) {
			AddMessage2Log(__FILE__.':'.__LINE__."\n".'Iblock '.$arFile["NAME"].' not found','iblock');
			return false;
		}
		

	}
	$rsSites = CIBlock::GetSite($arIBlock["ID"]);
	while ($arSite = $rsSites->Fetch()) {
		$sites[] = $arSite['LID'];
	}
	
	if ( ! in_array($site_id,$sites)) {
		$sites = array_merge($sites, (array)$site_id);
		$new_site['SITE_ID'] = $sites;
	}
	
	if($workflow && $arIBlock) {
		$new_site['WORKFLOW'] = 'Y';
	}
	
	if ( isset($new_site) and !empty($new_site)) {
		$obIBlock = new CIBlock;
		$obIBlock->Update($arIBlock["ID"], $new_site);
	}

	return $arIBlock["ID"];
}

/*function Areal_IBlock_CreateType($IBLOCK_TYPES = false)
{
	if(!is_array($IBLOCK_TYPES))
		$IBLOCK_TYPES = array("article","lease","notice","realtor","vacancy");

	$arTypes = array(
		"article" => array(
			"ID" => "article",
			"SECTIONS" => "Y",
			"IN_RSS" => "Y",
			"SORT" => 500,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("AREAL_IBLOCK_TYPE_ARTICLE_NAME"),
					"SECTION_NAME" => GetMessage("AREAL_IBLOCK_TYPE_ARTICLE_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("AREAL_IBLOCK_TYPE_ARTICLE_ELEMENT_NAME"),
				),
			),
		),
		"lease" => array(
			"ID" => "lease",
			"SECTIONS" => "Y",
			"IN_RSS" => "Y",
			"SORT" => 500,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("AREAL_IBLOCK_TYPE_LEASE_NAME"),
					"SECTION_NAME" => GetMessage("AREAL_IBLOCK_TYPE_LEASE_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("AREAL_IBLOCK_TYPE_LEASE_ELEMENT_NAME"),
				),
			),
		),
		"notice" => array(
			"ID" => "notice",
			"SECTIONS" => "Y",
			"IN_RSS" => "Y",
			"SORT" => 500,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("AREAL_IBLOCK_TYPE_NOTICE_NAME"),
					"SECTION_NAME" => GetMessage("AREAL_IBLOCK_TYPE_NOTICE_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("AREAL_IBLOCK_TYPE_NOTICE_ELEMENT_NAME"),
				),
			),
		),
		"realtor" => array(
			"ID" => "realtor",
			"SECTIONS" => "Y",
			"IN_RSS" => "Y",
			"SORT" => 500,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("AREAL_IBLOCK_TYPE_REALTOR_NAME"),
					"SECTION_NAME" => GetMessage("AREAL_IBLOCK_TYPE_REALTOR_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("AREAL_IBLOCK_TYPE_REALTOR_ELEMENT_NAME"),
				),
			),
		),
		"vacancy" => array(
			"ID" => "vacancy",
			"SECTIONS" => "Y",
			"IN_RSS" => "Y",
			"SORT" => 500,
			"LANG" => array(
				LANGUAGE_ID => array(
					"NAME" => GetMessage("AREAL_IBLOCK_TYPE_VACANCY_NAME"),
					"SECTION_NAME" => GetMessage("AREAL_IBLOCK_TYPE_VACANCY_SECTION_NAME"),
					"ELEMENT_NAME" => GetMessage("AREAL_IBLOCK_TYPE_VACANCY_ELEMENT_NAME"),
				),
			),
		),
	);
	$err = array();
	foreach($IBLOCK_TYPES as $TYPE_ID)
	{
		if(array_key_exists($TYPE_ID, $arTypes))
		{
			$obType = new CIBlockType;
			$arFields = $arTypes[$TYPE_ID];
			$rsType = $obType->GetList(array(),array("=ID" => $arFields["ID"]));
			if($arType = $rsType->Fetch())
				continue;
			else {
				$o = $obType->Add($arFields);
				if (!$o) $err[] = $obType->LAST_ERROR;
			}
				
		}
	}
	return $err;
}*/

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