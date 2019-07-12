<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CModule::IncludeModule("catalog");
CModule::IncludeModule("iblock");
$iblockType = "catalog";
$ABS_FILE_NAME = $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/offers.xml";
$WORK_DIR_NAME = $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/";

$NS = array(
			"STEP" => 0,
			"IBLOCK_TYPE" => $iblockType,
			"LID" => Array(WIZARD_SITE_ID),
			"URL_DATA_FILE" => $_SERVER["DOCUMENT_ROOT"].WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/offers.xml",
			"ACTION" => "D",
			"PREVIEW" => "N",
		);

$obXMLFile = new CIBlockXMLFile;
//#1
$_SESSION["BX_CML2_IMPORT"] = array(
	"SECTION_MAP" => false,
	"PRICES_MAP" => false,
);
CIBlockXMLFile::DropTemporaryTables();
//#2
if(CIBlockCMLImport::CheckIfFileIsCML($ABS_FILE_NAME)){$NS["STEP"]++;}
//#3
if(CIBlockXMLFile::CreateTemporaryTables()){$NS["STEP"]++;}
//#4
if(file_exists($ABS_FILE_NAME) && is_file($ABS_FILE_NAME) && ($fp = fopen($ABS_FILE_NAME, "rb")))
{
	if($obXMLFile->ReadXMLToDatabase($fp, $NS)){$NS["STEP"]++;}
	fclose($fp);
}
//#5
if(CIBlockXMLFile::IndexTemporaryTables()){$NS["STEP"]++;}
$obCatalog = new CIBlockCMLImport;
$obCatalog->Init($NS, $WORK_DIR_NAME, true, $NS["PREVIEW"], false, true);
$result = $obCatalog->ImportMetaData(1, $NS["IBLOCK_TYPE"], $NS["LID"]);
$obCatalog->ImportSections();
$obCatalog->DeactivateSections("A");
$obCatalog->SectionsResort();
if($result === true){$NS["STEP"]++;}
//#6
if(($NS["DONE"]["ALL"] <= 0) && $NS["XML_ELEMENTS_PARENT"])
{
	$rs = $DB->Query("select count(*) C from b_xml_tree where PARENT_ID = ".intval($NS["XML_ELEMENTS_PARENT"]));
	$ar = $rs->Fetch();
	$NS["DONE"]["ALL"] = $ar["C"];
}
$obCatalog = new CIBlockCMLImport;
$obCatalog->Init($NS, $WORK_DIR_NAME, true, $NS["PREVIEW"], false, true);
$obCatalog->ReadCatalogData($_SESSION["BX_CML2_IMPORT"]["SECTION_MAP"], $_SESSION["BX_CML2_IMPORT"]["PRICES_MAP"]);
$result = $obCatalog->ImportElements();
$counter = 0;
foreach($result as $key=>$value)
{
	$NS["DONE"][$key] += $value;
	$counter+=$value;
}
?>