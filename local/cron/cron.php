<?php 
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__));
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
ini_set('memory_limit', '512M');
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true); 
define("CHK_EVENT", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
 
@set_time_limit(0);
//@ignore_user_abort(true);
 
CModule::IncludeModule('catalog');

$arParams = Array(
    "IBLOCK_TYPE" => COption::GetOptionString("catalog", "1C_IBLOCK_TYPE", "-"),
    "SITE_LIST" => array(COption::GetOptionString("catalog", "1C_SITE_LIST", "-")),
    "INTERVAL" => COption::GetOptionString("catalog", "1C_INTERVAL", "-"),
    "GROUP_PERMISSIONS" => explode(",", COption::GetOptionString("catalog", "1C_GROUP_PERMISSIONS", "")),
    "GENERATE_PREVIEW" => COption::GetOptionString("catalog", "1C_GENERATE_PREVIEW", "Y"),
    "PREVIEW_WIDTH" => COption::GetOptionString("catalog", "1C_PREVIEW_WIDTH", "100"),
    "PREVIEW_HEIGHT" => COption::GetOptionString("catalog", "1C_PREVIEW_HEIGHT", "100"),
    "DETAIL_RESIZE" => COption::GetOptionString("catalog", "1C_DETAIL_RESIZE", "Y"),
    "DETAIL_WIDTH" => COption::GetOptionString("catalog", "1C_DETAIL_WIDTH", "300"),
    "DETAIL_HEIGHT" => COption::GetOptionString("catalog", "1C_DETAIL_HEIGHT", "300"),
    "ELEMENT_ACTION" => COption::GetOptionString("catalog", "1C_ELEMENT_ACTION", "D"),
    "SECTION_ACTION" => COption::GetOptionString("catalog", "1C_SECTION_ACTION", "D"),
    "FILE_SIZE_LIMIT" => COption::GetOptionString("catalog", "1C_FILE_SIZE_LIMIT", 200 * 1024),
    "USE_CRC" => COption::GetOptionString("catalog", "1C_USE_CRC", "Y"),
    "USE_ZIP" => COption::GetOptionString("catalog", "1C_USE_ZIP", "Y"),
    "USE_OFFERS" => COption::GetOptionString("catalog", "1C_USE_OFFERS", "N"),
    "USE_IBLOCK_TYPE_ID" => COption::GetOptionString("catalog", "1C_USE_IBLOCK_TYPE_ID", "N"),
    "USE_IBLOCK_PICTURE_SETTINGS" => COption::GetOptionString("catalog", "1C_USE_IBLOCK_PICTURE_SETTINGS", "N"),
    "TRANSLIT_ON_ADD" => COption::GetOptionString("catalog", "1C_TRANSLIT_ON_ADD", "N"),
    "TRANSLIT_ON_UPDATE" => COption::GetOptionString("catalog", "1C_TRANSLIT_ON_UPDATE", "N"),
    "SKIP_ROOT_SECTION" => COption::GetOptionString("catalog", "1C_SKIP_ROOT_SECTION", "N"),
    );

$arTranslitParams = array(
	"max_len" => $arParams["TRANSLIT_MAX_LEN"],
	"change_case" => $arParams["TRANSLIT_CHANGE_CASE"],
	"replace_space" => $arParams["TRANSLIT_REPLACE_SPACE"],
	"replace_other" => $arParams["TRANSLIT_REPLACE_OTHER"],
	"delete_repeat_replace" => $arParams["TRANSLIT_DELETE_REPEAT_REPLACE"],
);


$DIR_NAME = $DOCUMENT_ROOT . "/".COption::GetOptionString("main", "upload_dir", "upload")."/1c_catalog";
$ABS_FILE_NAME = false;
$WORK_DIR_NAME = false;
   
$dh  = opendir($DIR_NAME);
while (false !== ($filename = readdir($dh))) {
    if(!($filename == '..' || $filename=='.' || $filename=='.htaccess'))
        if(end(explode(".", $filename)) == 'xml')
             $files[] = $filename;
}
 
if(!$files)
    die();

$filename = preg_replace("#^(/tmp/|upload/1c/webdata)#", "", $files[0]);
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
                $ABS_FILE_NAME =$DIR_NAME.$FILE_NAME;
                $WORK_DIR_NAME = substr($ABS_FILE_NAME, 0, strrpos($ABS_FILE_NAME, "/")+1);
        }
}
 

CIBlockXMLFile::DropTemporaryTables();
CIBlockXMLFile::CreateTemporaryTables();

prent($ABS_FILE_NAME);
$fp = fopen($ABS_FILE_NAME, "rb");
if(is_resource($fp)) {

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

CIBlockXMLFile::IndexTemporaryTables();

      
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/devteam/catalog.import.1c/inc.php");

$obCatalog = new myCIBlockCMLImport;
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

$obCatalog->ImportSections();

unlink($ABS_FILE_NAME);
 