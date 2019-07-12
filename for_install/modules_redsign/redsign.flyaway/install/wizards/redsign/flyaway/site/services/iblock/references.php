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
		"filter" => array("NAME" => "ColorReferenceCurrent")
	)
);
if (!$dbHblock->Fetch())
{
	$data = array(
		'NAME' => 'ColorReferenceCurrent',
		'TABLE_NAME' => 'flyaway_color_reference',
	);

	$result = HL\HighloadBlockTable::add($data);
	$ID = $result->getId();

	$_SESSION["ESHOP_HBLOCK_COLOR_ID"] = $ID;

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
}

/*****************************************************************************************************************/

if (WIZARD_THEME_ID == 'default' || !file_exists(WIZARD_SERVICE_RELATIVE_PATH.'/hl/'.WIZARD_THEME_ID.'.php'))
	return true;

// $entityName
// $tableName
// $arData
include_once(WIZARD_SERVICE_RELATIVE_PATH.'/hl/'.WIZARD_THEME_ID.'.php');
	
$dbHblock = HL\HighloadBlockTable::getList(
	array(
		"filter" => array("NAME" => $entityName)
	)
);
if (!$dbHblock->Fetch())
{
	$data = array(
		'NAME' => $entityName,
		'TABLE_NAME' => $tableName,
	);

	$result = HL\HighloadBlockTable::add($data);
	$ID = $result->getId();

	$_SESSION["ESHOP_HBLOCK_".$tableName."_ID"] = $ID;

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
}
