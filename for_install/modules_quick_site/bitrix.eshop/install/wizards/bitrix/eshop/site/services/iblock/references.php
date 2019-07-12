<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!IsModuleInstalled("highloadblock") && file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/highloadblock/"))
{
	$installFile = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/highloadblock/install/index.php";
	if (!file_exists($installFile))
		return false;

	include_once($installFile);

	$moduleIdTmp = str_replace(".", "_", "highloadblock");
	if (!class_exists($moduleIdTmp))
		return false;

	$module = new $moduleIdTmp;
	if (!$module->InstallDB())
		return false;
	$module->InstallEvents();
	if (!$module->InstallFiles())
		return false;
}

if (!CModule::IncludeModule("highloadblock"))
	return;

if (!WIZARD_INSTALL_DEMO_DATA)
	return;

use Bitrix\Highloadblock as HL;

$dbHblock = HL\HighloadBlockTable::getList(
	array(
		"filter" => array("NAME" => "ColorReference")
	)
);
if ($dbHblock->Fetch())
	return;

$data = array(
	'NAME' => 'ColorReference',
	'TABLE_NAME' => 'eshop_color_reference',
);

$result = HL\HighloadBlockTable::add($data);
$ID = $result->getId();

$_SESSION["ESHOP_HBLOCK_ID"] = $ID;

$hldata = HL\HighloadBlockTable::getById($ID)->fetch();
$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

//adding user fields
$arUserFields = array (
	array (
		'ENTITY_ID' => 'HLBLOCK_'.$ID,
		'FIELD_NAME' => 'UF_NAME',
		'USER_TYPE_ID' => 'string',
		'XML_ID' => 'UF_COLOR_NAME',
		'SORT' => '100',
		'MULTIPLE' => 'N',
		'MANDATORY' => 'N',
		'SHOW_FILTER' => 'N',
		'SHOW_IN_LIST' => 'Y',
		'EDIT_IN_LIST' => 'Y',
		'IS_SEARCHABLE' => 'Y',
	),
	array (
		'ENTITY_ID' => 'HLBLOCK_'.$ID,
		'FIELD_NAME' => 'UF_FILE',
		'USER_TYPE_ID' => 'file',
		'XML_ID' => 'UF_COLOR_FILE',
		'SORT' => '200',
		'MULTIPLE' => 'N',
		'MANDATORY' => 'N',
		'SHOW_FILTER' => 'N',
		'SHOW_IN_LIST' => 'Y',
		'EDIT_IN_LIST' => 'Y',
		'IS_SEARCHABLE' => 'Y',
	),
	array (
		'ENTITY_ID' => 'HLBLOCK_'.$ID,
		'FIELD_NAME' => 'UF_LINK',
		'USER_TYPE_ID' => 'string',
		'XML_ID' => 'UF_COLOR_LINK',
		'SORT' => '300',
		'MULTIPLE' => 'N',
		'MANDATORY' => 'N',
		'SHOW_FILTER' => 'N',
		'SHOW_IN_LIST' => 'Y',
		'EDIT_IN_LIST' => 'Y',
		'IS_SEARCHABLE' => 'Y',
	),
	array (
		'ENTITY_ID' => 'HLBLOCK_'.$ID,
		'FIELD_NAME' => 'UF_SORT',
		'USER_TYPE_ID' => 'double',
		'XML_ID' => 'UF_COLOR_SORT',
		'SORT' => '400',
		'MULTIPLE' => 'N',
		'MANDATORY' => 'N',
		'SHOW_FILTER' => 'N',
		'SHOW_IN_LIST' => 'Y',
		'EDIT_IN_LIST' => 'Y',
		'IS_SEARCHABLE' => 'N',
	),
	array (
		'ENTITY_ID' => 'HLBLOCK_'.$ID,
		'FIELD_NAME' => 'UF_DEF',
		'USER_TYPE_ID' => 'boolean',
		'XML_ID' => 'UF_COLOR_DEF',
		'SORT' => '500',
		'MULTIPLE' => 'N',
		'MANDATORY' => 'N',
		'SHOW_FILTER' => 'N',
		'SHOW_IN_LIST' => 'Y',
		'EDIT_IN_LIST' => 'Y',
		'IS_SEARCHABLE' => 'N',
	),
	array (
		'ENTITY_ID' => 'HLBLOCK_'.$ID,
		'FIELD_NAME' => 'UF_XML_ID',
		'USER_TYPE_ID' => 'string',
		'XML_ID' => 'UF_XML_ID',
		'SORT' => '600',
		'MULTIPLE' => 'N',
		'MANDATORY' => 'Y',
		'SHOW_FILTER' => 'N',
		'SHOW_IN_LIST' => 'Y',
		'EDIT_IN_LIST' => 'Y',
		'IS_SEARCHABLE' => 'N',
	)
);
$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while($arLanguage = $rsLanguage->Fetch())
	$arLanguages[] = $arLanguage["LID"];

$obUserField  = new CUserTypeEntity;
foreach ($arUserFields as $arFields)
{
	$dbRes = CUserTypeEntity::GetList(Array(), Array("ENTITY_ID" => $arFields["ENTITY_ID"], "FIELD_NAME" => $arFields["FIELD_NAME"]));
	if ($dbRes->Fetch())
		continue;

	$arLabelNames = Array();
	foreach($arLanguages as $languageID)
	{
		WizardServices::IncludeServiceLang("references.php", $languageID);
		$arLabelNames[$languageID] = GetMessage($arFields["FIELD_NAME"]);
	}

	$arFields["EDIT_FORM_LABEL"] = $arLabelNames;
	$arFields["LIST_COLUMN_LABEL"] = $arLabelNames;
	$arFields["LIST_FILTER_LABEL"] = $arLabelNames;

	$ID_USER_FIELD = $obUserField->Add($arFields);
}

/*if(!CModule::IncludeModule("iblock"))
	return;

$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/references.xml";
$iblockCode = "clothes_colors_".WIZARD_SITE_ID;
$iblockType = "references";

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockCode, "TYPE" => $iblockType));
$iblockID = false; 
if ($arIBlock = $rsIBlock->Fetch())
{
	$iblockID = $arIBlock["ID"]; 
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		CIBlock::Delete($arIBlock["ID"]); 
		$iblockID = false; 
	}
}

if($iblockID == false)
{
	$permissions = Array(
			"1" => "X",
			"2" => "R"
		);
	$dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));
	if($arGroup = $dbGroup -> Fetch())
	{
		$permissions[$arGroup["ID"]] = 'W';
	};
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"clothes_colors",
		$iblockType,
		WIZARD_SITE_ID,
		$permissions
	);

	if ($iblockID < 1)
		return;
	
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"FIELDS" => array ( 'IBLOCK_SECTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'ACTIVE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ), 'ACTIVE_FROM' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '=today', ), 'ACTIVE_TO' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SORT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'PREVIEW_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'PREVIEW_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'PREVIEW_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'DETAIL_TEXT_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'DETAIL_TEXT' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'CODE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'Y', ), ), 'TAGS' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_NAME' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ), 'SECTION_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'N', 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, 'DELETE_WITH_DETAIL' => 'N', 'UPDATE_WITH_DETAIL' => 'N', ), ), 'SECTION_DESCRIPTION_TYPE' => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ), 'SECTION_DESCRIPTION' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_DETAIL_PICTURE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'N', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', 'METHOD' => 'resample', 'COMPRESSION' => 95, ), ), 'SECTION_XML_ID' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ), 'SECTION_CODE' => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'UNIQUE' => 'N', 'TRANSLITERATION' => 'N', 'TRANS_LEN' => 100, 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '_', 'TRANS_OTHER' => '_', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N', ), ), ),
		"CODE" => "references",
		"XML_ID" => "clothes_colors"//$iblockCode,
	);
	
	$iblock->Update($iblockID, $arFields);
}
else
{
	$arSites = array(); 
	$db_res = CIBlock::GetSite($iblockID);
	while ($res = $db_res->Fetch())
		$arSites[] = $res["LID"]; 
	if (!in_array(WIZARD_SITE_ID, $arSites))
	{
		$arSites[] = WIZARD_SITE_ID;
		$iblock = new CIBlock;
		$iblock->Update($iblockID, array("LID" => $arSites));
	}
}
$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if($arSite = $dbSite -> Fetch())
	$lang = $arSite["LANGUAGE_ID"];
if(strlen($lang) <= 0)
	$lang = "ru";

WizardServices::IncludeServiceLang("references.php", $lang);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/interface/admin_lib.php");

CAdminFormSettings::setTabsArray('form_element_'.$iblockID, array(
	'edit1' => array(
		'TAB' => GetMessage('WZD_OPTION_REF_edit1_TAB_TITLE'),
		'FIELDS' => array(
			'NAME' => GetMessage('WZD_OPTION_REF_NAME'),
			'CODE' => GetMessage('WZD_OPTION_REF_CODE'),
			'PREVIEW_PICTURE' => GetMessage('WZD_OPTION_REF_PREVIEW_PICTURE'),
			'SORT' => GetMessage('WZD_OPTION_REF_SORT'),
		),
	),
));

CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockType.".".$iblockID), array ( 'columns' => 'PREVIEW_PICTURE,NAME,SORT', 'by' => 'timestamp_x', 'order' => 'desc', 'page_size' => '20', ));
*/?>