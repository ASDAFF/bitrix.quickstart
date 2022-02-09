<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/*************************************************************************
	Processing of received parameters
*************************************************************************/

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["INTERVAL"] = intval($arParams["INTERVAL"]);

if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

if(!is_array($arParams["SITE_LIST"]))
	$arParams["SITE_LIST"] = array();

if($arParams["ELEMENT_ACTION"]!="N" && $arParams["ELEMENT_ACTION"]!="A")
	$arParams["ELEMENT_ACTION"] = "D";
if($arParams["SECTION_ACTION"]!="N" && $arParams["SECTION_ACTION"]!="A")
	$arParams["SECTION_ACTION"] = "D";

$arParams["FILE_SIZE_LIMIT"] = intval($arParams["FILE_SIZE_LIMIT"]);
if($arParams["FILE_SIZE_LIMIT"] < 1)
	$arParams["FILE_SIZE_LIMIT"] = 200*1024; //200KB

$arParams["USE_CRC"] = $arParams["USE_CRC"]!="N";
$arParams["USE_ZIP"] = $arParams["USE_ZIP"]!="N";
$arParams["USE_OFFERS"] = $arParams["USE_OFFERS"]=="Y";
$arParams["USE_IBLOCK_TYPE_ID"] = $arParams["USE_IBLOCK_TYPE_ID"]=="Y";
$arParams["USE_IBLOCK_PICTURE_SETTINGS"] = $arParams["USE_IBLOCK_PICTURE_SETTINGS"]=="Y";
$arParams["SKIP_ROOT_SECTION"] = $arParams["SKIP_ROOT_SECTION"]=="Y";

if($arParams["USE_IBLOCK_PICTURE_SETTINGS"])
{
	$preview = true;
	$detail = true;
}
else
{
	$arParams["GENERATE_PREVIEW"] = $arParams["GENERATE_PREVIEW"]!="N";
	if($arParams["GENERATE_PREVIEW"])
	{
		$preview = array(
			intval($arParams["PREVIEW_WIDTH"]) > 1? intval($arParams["PREVIEW_WIDTH"]): 100,
			intval($arParams["PREVIEW_HEIGHT"]) > 1? intval($arParams["PREVIEW_HEIGHT"]): 100,
		);
	}
	else
	{
		$preview = false;
	}

	$arParams["DETAIL_RESIZE"] = $arParams["DETAIL_RESIZE"]!="N";
	if($arParams["DETAIL_RESIZE"])
	{
		$detail = array(
			intval($arParams["DETAIL_WIDTH"]) > 1? intval($arParams["DETAIL_WIDTH"]): 300,
			intval($arParams["DETAIL_HEIGHT"]) > 1? intval($arParams["DETAIL_HEIGHT"]): 300,
		);
	}
	else
	{
		$detail = false;
	}
}

$arParams["TRANSLIT_MAX_LEN"] = intval($arParams["TRANSLIT_MAX_LEN"]);
if($arParams["TRANSLIT_MAX_LEN"] <= 0)
	$arParams["TRANSLIT_MAX_LEN"] = 100;
if(!array_key_exists("TRANSLIT_CHANGE_CASE", $arParams))
	$arParams["TRANSLIT_CHANGE_CASE"] = 'L'; // 'L' - toLower, 'U' - toUpper, false - do not change
if(!array_key_exists("TRANSLIT_REPLACE_SPACE", $arParams))
	$arParams["TRANSLIT_REPLACE_SPACE"] = '_';
if(!array_key_exists("TRANSLIT_REPLACE_OTHER", $arParams))
	$arParams["TRANSLIT_REPLACE_OTHER"] = '_';
$arParams["TRANSLIT_DELETE_REPEAT_REPLACE"] = $arParams["TRANSLIT_DELETE_REPEAT_REPLACE"] !== "N";

$arTranslitParams = array(
	"max_len" => $arParams["TRANSLIT_MAX_LEN"],
	"change_case" => $arParams["TRANSLIT_CHANGE_CASE"],
	"replace_space" => $arParams["TRANSLIT_REPLACE_SPACE"],
	"replace_other" => $arParams["TRANSLIT_REPLACE_OTHER"],
	"delete_repeat_replace" => $arParams["TRANSLIT_DELETE_REPEAT_REPLACE"],
);

$arParams["TRANSLIT_ON_ADD"] = $arParams["TRANSLIT_ON_ADD"] === "Y";
$arParams["TRANSLIT_ON_UPDATE"] = $arParams["TRANSLIT_ON_UPDATE"] === "Y";

if($arParams["INTERVAL"] <= 0)
	@set_time_limit(0);

$start_time = time();

$bUSER_HAVE_ACCESS = false;
if(isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$bUSER_HAVE_ACCESS = $GLOBALS["USER"]->IsAdmin();
	if(!$bUSER_HAVE_ACCESS)
	{
		$arUserGroupArray = $GLOBALS["USER"]->GetUserGroupArray();
		foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
		{
			if(in_array($PERM, $arUserGroupArray))
			{
				$bUSER_HAVE_ACCESS = true;
				break;
			}
		}
	}
}

$bDesignMode = $GLOBALS["APPLICATION"]->GetShowIncludeAreas()
		&& !isset($_GET["mode"])
		&& is_object($GLOBALS["USER"])
		&& $GLOBALS["USER"]->IsAdmin();

if(!$bDesignMode)
{
	if(!isset($_GET["mode"]))
		return;
	$APPLICATION->RestartBuffer();
	header("Pragma: no-cache");
}

$DIR_NAME = "";

ob_start();

if($_GET["mode"] == "checkauth" && $USER->IsAuthorized())
{
	if(
		(COption::GetOptionString("main", "use_session_id_ttl", "N") == "Y")
		&& (COption::GetOptionInt("main", "session_id_ttl", 0) > 0)
		&& !defined("BX_SESSION_ID_CHANGE")
	)
	{
		echo "failure\n",GetMessage("CC_BSC1_ERROR_SESSION_ID_CHANGE");
	}
	else
	{
		echo "success\n";
		echo session_name()."\n";
		echo session_id() ."\n";
	}
}
elseif(!$USER->IsAuthorized())
{
	echo "failure\n",GetMessage("CC_BSC1_ERROR_AUTHORIZE");
}
elseif(!$bUSER_HAVE_ACCESS)
{
	echo "failure\n",GetMessage("CC_BSC1_PERMISSION_DENIED");
}
elseif(!CModule::IncludeModule('iblock'))
{
	echo "failure\n",GetMessage("CC_BSC1_ERROR_MODULE");
}
else
{
	//We have to strongly check all about file names at server side
	$DIR_NAME = "/".COption::GetOptionString("main", "upload_dir", "upload")."/1c_catalog";
	$ABS_FILE_NAME = false;
	$WORK_DIR_NAME = false;
	if(isset($_GET["filename"]) && (strlen($_GET["filename"])>0))
	{
		//This check for 1c server on linux
		$filename = preg_replace("#^(/tmp/|upload/1c/webdata)#", "", $_GET["filename"]);
		$filename = trim(str_replace("\\", "/", trim($filename)), "/");

		$io = CBXVirtualIo::GetInstance();
		$bBadFile = HasScriptExtension($filename)
			|| IsFileUnsafe($filename)
			|| !$io->ValidatePathString("/".$filename)
		;

		if(!$bBadFile)
		{
			$FILE_NAME = rel2abs($_SERVER["DOCUMENT_ROOT"].$DIR_NAME, "/".$filename);
			if((strlen($FILE_NAME) > 1) && ($FILE_NAME === "/".$filename))
			{
				$ABS_FILE_NAME = $_SERVER["DOCUMENT_ROOT"].$DIR_NAME.$FILE_NAME;
				$WORK_DIR_NAME = substr($ABS_FILE_NAME, 0, strrpos($ABS_FILE_NAME, "/")+1);
			}
		}
	}

	if(($_GET["mode"] == "file") && $ABS_FILE_NAME)
	{
		//Read http data
		if(function_exists("file_get_contents"))
			$DATA = file_get_contents("php://input");
		elseif(isset($GLOBALS["HTTP_RAW_POST_DATA"]))
			$DATA = &$GLOBALS["HTTP_RAW_POST_DATA"];
		else
			$DATA = false;

		$DATA_LEN = defined("BX_UTF")? mb_strlen($DATA, 'latin1'): strlen($DATA);
		//And save it the file
		if($DATA_LEN > 0)
		{
			CheckDirPath($ABS_FILE_NAME);
			if($fp = fopen($ABS_FILE_NAME, "ab"))
			{
				$result = fwrite($fp, $DATA);
				if($result === $DATA_LEN)
				{
					echo "success\n";
					if($_SESSION["BX_CML2_IMPORT"]["zip"])
						$_SESSION["BX_CML2_IMPORT"]["zip"] = $ABS_FILE_NAME;
				}
				else
				{
					echo "failure\n",GetMessage("CC_BSC1_ERROR_FILE_WRITE", array("#FILE_NAME#"=>$FILE_NAME));
				}
			}
			else
			{
				echo "failure\n",GetMessage("CC_BSC1_ERROR_FILE_OPEN", array("#FILE_NAME#"=>$FILE_NAME));
			}
		}
		else
		{
			echo "failure\n",GetMessage("CC_BSC1_ERROR_HTTP_READ");
		}
	}
	elseif(($_GET["mode"] == "import") && $_SESSION["BX_CML2_IMPORT"]["zip"])
	{
		if(!array_key_exists("last_zip_entry", $_SESSION["BX_CML2_IMPORT"]))
			$_SESSION["BX_CML2_IMPORT"]["last_zip_entry"] = "";

		$result = CIBlockXMLFile::UnZip($_SESSION["BX_CML2_IMPORT"]["zip"], $_SESSION["BX_CML2_IMPORT"]["last_zip_entry"]);
		if($result===false)
		{
			echo "failure\n",GetMessage("CC_BSC1_ZIP_ERROR");
		}
		elseif($result===true)
		{
			$_SESSION["BX_CML2_IMPORT"]["zip"] = false;
			echo "progress\n".GetMessage("CC_BSC1_ZIP_DONE");
		}
		else
		{
			$_SESSION["BX_CML2_IMPORT"]["last_zip_entry"] = $result;
			echo "progress\n".GetMessage("CC_BSC1_ZIP_PROGRESS");
		}
	}
	elseif(($_GET["mode"] == "import") && $ABS_FILE_NAME)
	{
		$NS = &$_SESSION["BX_CML2_IMPORT"]["NS"];
		$strError = "";
		$strMessage = "";

		if($NS["STEP"] < 1)
		{
			CIBlockXMLFile::DropTemporaryTables();
			$strMessage = GetMessage("CC_BSC1_TABLES_DROPPED");
			$NS["STEP"] = 1;
		}
		elseif($NS["STEP"] == 1)
		{
			if(CIBlockXMLFile::CreateTemporaryTables())
			{
				$strMessage = GetMessage("CC_BSC1_TABLES_CREATED");
				$NS["STEP"] = 2;
			}
			else
			{
				$strError = GetMessage("CC_BSC1_TABLE_CREATE_ERROR");
			}
		}
		elseif($NS["STEP"] == 2)
		{
			$fp = fopen($ABS_FILE_NAME, "rb");
			$total = filesize($ABS_FILE_NAME);

			if(($total > 0) && is_resource($fp))
			{
				$obXMLFile = new CIBlockXMLFile;
				if($obXMLFile->ReadXMLToDatabase($fp, $NS, $arParams["INTERVAL"]))
				{
					$NS["STEP"] = 3;
					$strMessage = GetMessage("CC_BSC1_FILE_READ");
				}
				else
				{
					$strMessage = GetMessage("CC_BSC1_FILE_PROGRESS", array("#PERCENT#"=>$total > 0? round($obXMLFile->GetFilePosition()/$total*100, 2): 0));
				}
				fclose($fp);
			}
			else
			{
				$strError = GetMessage("CC_BSC1_FILE_ERROR");
			}
		}
		elseif($NS["STEP"] == 3)
		{
			if(CIBlockXMLFile::IndexTemporaryTables())
			{
				$strMessage = GetMessage("CC_BSC1_INDEX_CREATED");
				$NS["STEP"] = 4;
			}
			else
				$strError = GetMessage("CC_BSC1_INDEX_CREATE_ERROR");
		}
		elseif($NS["STEP"] == 4)
		{
			$obCatalog = new CIBlockCMLImport;
			$obCatalog->InitEx($NS, array(
				"files_dir" => $WORK_DIR_NAME,
				"use_crc" => $arParams["USE_CRC"],
				"preview" => $preview,
				"detail" => $detail,
				"use_offers" => $arParams["USE_OFFERS"],
				"use_iblock_type_id" => $arParams["USE_IBLOCK_TYPE_ID"],
				"translit_on_add" => $arParams["TRANSLIT_ON_ADD"],
				"translit_on_update" => $arParams["TRANSLIT_ON_UPDATE"],
				"translit_params" => $arTranslitParams,
				"skip_root_section" => $arParams["SKIP_ROOT_SECTION"],
			));
			$result = $obCatalog->ImportMetaData(1, $arParams["IBLOCK_TYPE"], $arParams["SITE_LIST"]);
			if($result === true)
			{
				$strMessage = GetMessage("CC_BSC1_METADATA_IMPORTED");
				$NS["STEP"] = 5;
			}
			else
			{
				$strError = GetMessage("CC_BSC1_METADATA_ERROR").implode("\n", $result);
			}
		}
		elseif($NS["STEP"] == 5)
		{
			$obCatalog = new CIBlockCMLImport;
			$obCatalog->InitEx($NS, array(
				"files_dir" => $WORK_DIR_NAME,
				"use_crc" => $arParams["USE_CRC"],
				"preview" => $preview,
				"detail" => $detail,
				"use_offers" => $arParams["USE_OFFERS"],
				"use_iblock_type_id" => $arParams["USE_IBLOCK_TYPE_ID"],
				"translit_on_add" => $arParams["TRANSLIT_ON_ADD"],
				"translit_on_update" => $arParams["TRANSLIT_ON_UPDATE"],
				"translit_params" => $arTranslitParams,
				"skip_root_section" => $arParams["SKIP_ROOT_SECTION"],
			));
			$result = $obCatalog->ImportSections();
			$strMessage = GetMessage("CC_BSC1_SECTIONS_IMPORTED");
			$NS["STEP"] = 6;
		}
		elseif($NS["STEP"] == 6)
		{
			$obCatalog = new CIBlockCMLImport;
			$obCatalog->InitEx($NS, array(
				"files_dir" => $WORK_DIR_NAME,
				"use_crc" => $arParams["USE_CRC"],
				"preview" => $preview,
				"detail" => $detail,
				"use_offers" => $arParams["USE_OFFERS"],
				"use_iblock_type_id" => $arParams["USE_IBLOCK_TYPE_ID"],
				"translit_on_add" => $arParams["TRANSLIT_ON_ADD"],
				"translit_on_update" => $arParams["TRANSLIT_ON_UPDATE"],
				"translit_params" => $arTranslitParams,
				"skip_root_section" => $arParams["SKIP_ROOT_SECTION"],
			));
			$result = $obCatalog->DeactivateSections($arParams["SECTION_ACTION"]);
			$obCatalog->SectionsResort();
			$strMessage = GetMessage("CC_BSC1_SECTION_DEA_DONE");
			$NS["STEP"] = 7;
		}
		elseif($NS["STEP"] == 7)
		{
			if(($NS["DONE"]["ALL"] <= 0) && $NS["XML_ELEMENTS_PARENT"])
			{
				$rs = $DB->Query("select count(*) C from b_xml_tree where PARENT_ID = ".intval($NS["XML_ELEMENTS_PARENT"]));
				$ar = $rs->Fetch();
				$NS["DONE"]["ALL"] = $ar["C"];
			}

			$obCatalog = new CIBlockCMLImport;
			$obCatalog->InitEx($NS, array(
				"files_dir" => $WORK_DIR_NAME,
				"use_crc" => $arParams["USE_CRC"],
				"preview" => $preview,
				"detail" => $detail,
				"use_offers" => $arParams["USE_OFFERS"],
				"use_iblock_type_id" => $arParams["USE_IBLOCK_TYPE_ID"],
				"translit_on_add" => $arParams["TRANSLIT_ON_ADD"],
				"translit_on_update" => $arParams["TRANSLIT_ON_UPDATE"],
				"translit_params" => $arTranslitParams,
				"skip_root_section" => $arParams["SKIP_ROOT_SECTION"],
			));
			$obCatalog->ReadCatalogData($_SESSION["BX_CML2_IMPORT"]["SECTION_MAP"], $_SESSION["BX_CML2_IMPORT"]["PRICES_MAP"]);
			$result = $obCatalog->ImportElements($start_time, $arParams["INTERVAL"]);

			$counter = 0;
			foreach($result as $key=>$value)
			{
				$NS["DONE"][$key] += $value;
				$counter+=$value;
			}

			if(!$counter)
			{
				$strMessage = GetMessage("CC_BSC1_DONE");
				$NS["STEP"] = 8;
			}
			elseif(strlen($obCatalog->LAST_ERROR))
			{
				$strError = $obCatalog->LAST_ERROR;
			}
			else
			{
				$strMessage = GetMessage("CC_BSC1_PROGRESS", array("#TOTAL#"=>$NS["DONE"]["ALL"],"#DONE#"=>intval($NS["DONE"]["CRC"])));
			}
		}
		elseif($NS["STEP"] == 8)
		{
			$obCatalog = new CIBlockCMLImport;
			$obCatalog->Init($NS);
			$result = $obCatalog->DeactivateElement($arParams["ELEMENT_ACTION"], $start_time, $arParams["INTERVAL"]);

			$counter = 0;
			foreach($result as $key=>$value)
			{
				$NS["DONE"][$key] += $value;
				$counter+=$value;
			}

			if(!$counter)
			{
				$strMessage = GetMessage("CC_BSC1_DEA_DONE");
				$NS["STEP"] = 9;
			}
			else
			{
				$strMessage = GetMessage("CC_BSC1_PROGRESS", array("#TOTAL#"=>$NS["DONE"]["ALL"],"#DONE#"=>$NS["DONE"]["NON"]));
			}
		}
		else
		{
			$NS["STEP"]++;
		}

		if($strError)
		{
			echo "failure\n";
			echo str_replace("<br>", "", $strError);
		}
		elseif($NS["STEP"] < 10)
		{
			echo "progress\n",$strMessage;
		}
		else
		{
			foreach(GetModuleEvents("catalog", "OnSuccessCatalogImport1C", true) as $arEvent)
				ExecuteModuleEventEx($arEvent);

			echo "success\n",GetMessage("CC_BSC1_IMPORT_SUCCESS");
			$_SESSION["BX_CML2_IMPORT"] = array(
				"zip" => $_SESSION["BX_CML2_IMPORT"]["zip"], //save from prev load
				"NS" => array(
					"STEP" => 0,
				),
				"SECTION_MAP" => false,
				"PRICES_MAP" => false,
			);
		}
	}
	elseif($_GET["mode"]=="init")
	{
		DeleteDirFilesEx($DIR_NAME);
		CheckDirPath($_SERVER["DOCUMENT_ROOT"].$DIR_NAME."/");
		if(!is_dir($_SERVER["DOCUMENT_ROOT"].$DIR_NAME))
		{
			echo "failure\n",GetMessage("CC_BSC1_ERROR_INIT");
		}
		else
		{
			$_SESSION["BX_CML2_IMPORT"] = array(
				"zip" => $arParams["USE_ZIP"] && function_exists("zip_open"),
				"NS" => array(
					"STEP" => 0,
				),
				"SECTION_MAP" => false,
				"PRICES_MAP" => false,
			);
			echo "zip=".($_SESSION["BX_CML2_IMPORT"]["zip"]? "yes": "no")."\n";
			echo "file_limit=".$arParams["FILE_SIZE_LIMIT"]."\n";
		}
	}
	else
	{
		echo "failure\n",GetMessage("CC_BSC1_ERROR_UNKNOWN_COMMAND");
	}
}

$contents = ob_get_contents();
ob_end_clean();

if($DIR_NAME != "")
{
	$ht_name = $_SERVER["DOCUMENT_ROOT"].$DIR_NAME."/.htaccess";
	file_put_contents($ht_name, "Deny from All");
	@chmod($ht_name, BX_FILE_PERMISSIONS);
}

if(!$bDesignMode)
{
	if(toUpper(LANG_CHARSET) != "WINDOWS-1251")
		$contents = $APPLICATION->ConvertCharset($contents, LANG_CHARSET, "windows-1251");
	header("Content-Type: text/html; charset=windows-1251");

	echo $contents;
	die();
}
else
{
	$this->IncludeComponentLang(".parameters.php");
	$arAction = array(
		"N" => GetMessage("CC_BCI1_NONE"),
		"A" => GetMessage("CC_BCI1_DEACTIVATE"),
		"D" => GetMessage("CC_BCI1_DELETE"),
	);

	if(
		(COption::GetOptionString("main", "use_session_id_ttl", "N") == "Y")
		&& (COption::GetOptionInt("main", "session_id_ttl", 0) > 0)
		&& !defined("BX_SESSION_ID_CHANGE")
	)
		ShowError(GetMessage("CC_BSC1_ERROR_SESSION_ID_CHANGE"));
	?><table class="data-table">
	<tr><td><?echo GetMessage("CC_BCI1_IBLOCK_TYPE")?></td><td><?echo $arParams["IBLOCK_TYPE"]?></td></tr>
	<tr><td><?echo GetMessage("CC_BCI1_SITE_LIST")?></td><td><?echo implode(", ", $arParams["SITE_LIST"])?></td></tr>
	<tr><td><?echo GetMessage("CC_BCI1_INTERVAL")?></td><td><?echo $arParams["INTERVAL"]?></td></tr>
	<tr><td><?echo GetMessage("CC_BCI1_ELEMENT_ACTION")?></td><td><?echo $arAction[$arParams["ELEMENT_ACTION"]]?></td></tr>
	<tr><td><?echo GetMessage("CC_BCI1_SECTION_ACTION")?></td><td><?echo $arAction[$arParams["SECTION_ACTION"]]?></td></tr>
	<tr><td><?echo GetMessage("CC_BCI1_FILE_SIZE_LIMIT")?></td><td><?echo $arParams["FILE_SIZE_LIMIT"]?></td></tr>
	<tr><td><?echo GetMessage("CC_BCI1_USE_CRC")?></td><td><?echo $arParams["USE_CRC"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	<tr><td><?echo GetMessage("CC_BCI1_USE_ZIP")?></td><td><?echo $arParams["USE_ZIP"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	<tr><td><?echo GetMessage("CC_BCI1_USE_IBLOCK_PICTURE_SETTINGS")?></td><td><?echo $arParams["USE_IBLOCK_PICTURE_SETTINGS"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	<?if(!$arParams["USE_IBLOCK_PICTURE_SETTINGS"]):?>
		<tr><td><?echo GetMessage("CC_BCI1_GENERATE_PREVIEW")?></td><td><?echo $arParams["GENERATE_PREVIEW"]? GetMessage("MAIN_YES")." ".$arParams["PREVIEW_WIDTH"]."x".$arParams["PREVIEW_HEIGHT"]: GetMessage("MAIN_NO")?></td></tr>
		<tr><td><?echo GetMessage("CC_BCI1_DETAIL_RESIZE")?></td><td><?echo $arParams["DETAIL_RESIZE"]? GetMessage("MAIN_YES")." ".$arParams["DETAIL_WIDTH"]."x".$arParams["DETAIL_HEIGHT"]: GetMessage("MAIN_NO")?></td></tr>
	<?endif?>
	<tr><td><?echo GetMessage("CC_BCI1_TRANSLIT_ON_ADD")?></td><td><?echo $arParams["TRANSLIT_ON_ADD"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	<tr><td><?echo GetMessage("CC_BCI1_TRANSLIT_ON_UPDATE")?></td><td><?echo $arParams["TRANSLIT_ON_UPDATE"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	<?if($arParams["TRANSLIT_ON_ADD"] || $arParams["TRANSLIT_ON_UPDATE"]):?>
		<tr><td><?echo GetMessage("CC_BCI1_TRANSLIT_MAX_LEN")?></td><td><?echo $arParams["TRANSLIT_MAX_LEN"]?></td></tr>
		<tr><td><?echo GetMessage("CC_BCI1_TRANSLIT_CHANGE_CASE")?></td><td><?
			if($arParams["TRANSLIT_CHANGE_CASE"] == "L" || $arParams["TRANSLIT_CHANGE_CASE"] == "l")
				echo GetMessage("CC_BCI1_TRANSLIT_CHANGE_CASE_LOWER");
			elseif($arParams["TRANSLIT_CHANGE_CASE"] == "U" || $arParams["TRANSLIT_CHANGE_CASE"] == "u")
				echo GetMessage("CC_BCI1_TRANSLIT_CHANGE_CASE_UPPER");
			else
				echo GetMessage("CC_BCI1_TRANSLIT_CHANGE_CASE_PRESERVE");
		?></td></tr>
		<tr><td><?echo GetMessage("CC_BCI1_TRANSLIT_REPLACE_SPACE")?></td><td><?echo $arParams["TRANSLIT_REPLACE_SPACE"]?></td></tr>
		<tr><td><?echo GetMessage("CC_BCI1_TRANSLIT_REPLACE_OTHER")?></td><td><?echo $arParams["TRANSLIT_REPLACE_OTHER"]?></td></tr>
		<tr><td><?echo GetMessage("CC_BCI1_TRANSLIT_DELETE_REPEAT_REPLACE")?></td><td><?echo $arParams["TRANSLIT_DELETE_REPEAT_REPLACE"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	<?endif?>
	<tr><td><?echo GetMessage("CC_BCI1_USE_OFFERS")?></td><td><?echo $arParams["USE_OFFERS"]? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?></td></tr>
	</table>
	<?
}
?>