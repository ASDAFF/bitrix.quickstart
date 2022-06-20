<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("lists"))
	return;

$strSelectedType = $arCurrentValues["IBLOCK_TYPE_ID"];

$arTypes = array();
$rsTypes = CLists::GetIBlockTypes();
while($ar = $rsTypes->Fetch())
{
	$arTypes[$ar["IBLOCK_TYPE_ID"]] = "[".$ar["IBLOCK_TYPE_ID"]."] ".$ar["NAME"];
	if(!$strSelectedType)
		$strSelectedType = $ar["IBLOCK_TYPE_ID"];
}

$arIBlocks = array();
$rsIBlocks = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $strSelectedType, "ACTIVE"=>"Y"));
while($ar = $rsIBlocks->Fetch())
{
	$arIBlocks[$ar["ID"]] = "[".$ar["ID"]."] ".$ar["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE_ID" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BLL_IBLOCK_TYPE_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arTypes,
			"DEFAULT" => "lists",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BLL_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => '={$_REQUEST["list_id"]}',
		),
		"SECTION_ID" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("CP_BLL_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["section_id"]}',
		),
		"LISTS_URL" => CListsParameters::GetPathTemplateParam(
			"LISTS",
			"LISTS_URL",
			GetMessage("CP_BLL_LISTS_URL"),
			"lists.lists.php",
			"URL_TEMPLATES"
		),
		"LIST_EDIT_URL" => CListsParameters::GetPathTemplateParam(
			"LIST",
			"LIST_EDIT_URL",
			GetMessage("CP_BLL_LIST_EDIT_URL"),
			"lists.list.edit.php?list_id=#list_id#",
			"URL_TEMPLATES"
		),
		"LIST_URL" => CListsParameters::GetPathTemplateParam(
			"SECTIONS",
			"LIST_URL",
			GetMessage("CP_BLL_LIST_URL"),
			"lists.list.php?list_id=#list_id#&section_id=#section_id#",
			"URL_TEMPLATES"
		),
		"LIST_SECTIONS_URL" => CListsParameters::GetPathTemplateParam(
			"SECTIONS",
			"LIST_SECTIONS_URL",
			GetMessage("CP_BLL_LIST_SECTION_URL"),
			"lists.sections.php?list_id=#list_id#&section_id=#section_id#",
			"URL_TEMPLATES"
		),
		"LIST_ELEMENT_URL" => CListsParameters::GetPathTemplateParam(
			"ELEMENT",
			"LIST_ELEMENT_URL",
			GetMessage("CP_BLL_LIST_ELEMENT_URL"),
			"lists.element.edit.php?list_id=#list_id#&section_id=#section_id#&element_id=#element_id#",
			"URL_TEMPLATES"
		),
		"LIST_FILE_URL" => CListsParameters::GetPathTemplateParam(
			"FILE",
			"LIST_FILE_URL",
			GetMessage("CP_BLL_LIST_FILE_URL"),
			"lists.file.php?list_id=#list_id#&section_id=#section_id#&element_id=#element_id#&field_id=#field_id#&file_id=#file_id#",
			"URL_TEMPLATES"
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
	),
);

if(IsModuleInstalled("bizproc"))
{
	$arComponentParameters["PARAMETERS"]["BIZPROC_LOG_URL"] = array(
		"PARENT" => "URL_TEMPLATES",
		"NAME" => GetMessage("CP_BLL_BIZPROC_LOG_URL"),
		"TYPE" => "STRING",
		"DEFAULT" => 'bizproc.log.php?ID=#document_state_id#',
	);
	$arComponentParameters["PARAMETERS"]["BIZPROC_WORKFLOW_START_URL"] = array(
		"PARENT" => "URL_TEMPLATES",
		"NAME" => GetMessage("CP_BLL_BIZPROC_WORKFLOW_START_URL"),
		"TYPE" => "STRING",
		"DEFAULT" => 'bizproc.workflow.start.php?element_id=#element_id#&list_id=#list_id#&workflow_template_id=#workflow_template_id#',
	);
	$arComponentParameters["PARAMETERS"]["BIZPROC_WORKFLOW_ADMIN_URL"] = array(
		"PARENT" => "URL_TEMPLATES",
		"NAME" => GetMessage("CP_BLL_BIZPROC_WORKFLOW_ADMIN_URL"),
		"TYPE" => "STRING",
		"DEFAULT" => 'bizproc.workflow.admin.php?list_id=#list_id#',
	);
}
?>
