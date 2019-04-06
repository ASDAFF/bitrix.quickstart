<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arAuditTypes = array(
	"USER_AUTHORIZE" => "[USER_AUTHORIZE] ".GetMessage("MAIN_EVENTLOG_USER_AUTHORIZE"),
	"USER_DELETE" => "[USER_DELETE] ".GetMessage("MAIN_EVENTLOG_USER_DELETE"),
	"USER_INFO" => "[USER_INFO] ".GetMessage("MAIN_EVENTLOG_USER_INFO"),
	"USER_LOGIN" => "[USER_LOGIN] ".GetMessage("MAIN_EVENTLOG_USER_LOGIN"),
	"USER_LOGINBYHASH" => "[USER_LOGINBYHASH] ".GetMessage("MAIN_EVENTLOG_USER_LOGINBYHASH_FAILED"),
	"USER_LOGOUT" => "[USER_LOGOUT] ".GetMessage("MAIN_EVENTLOG_USER_LOGOUT"),
	"USER_PASSWORD_CHANGED" => "[USER_PASSWORD_CHANGED] ".GetMessage("MAIN_EVENTLOG_USER_PASSWORD_CHANGED"),
	"USER_REGISTER" => "[USER_REGISTER] ".GetMessage("MAIN_EVENTLOG_USER_REGISTER"),
	"USER_REGISTER_FAIL" => "[USER_REGISTER_FAIL] ".GetMessage("MAIN_EVENTLOG_USER_REGISTER_FAIL"),
	"USER_GROUP_CHANGED" => "[USER_GROUP_CHANGED] ".GetMessage("MAIN_EVENTLOG_GROUP"),
	"GROUP_POLICY_CHANGED" => "[GROUP_POLICY_CHANGED] ".GetMessage("MAIN_EVENTLOG_GROUP_POLICY"),
	"MODULE_RIGHTS_CHANGED" => "[MODULE_RIGHTS_CHANGED] ".GetMessage("MAIN_EVENTLOG_MODULE"),
	"FILE_PERMISSION_CHANGED" => "[FILE_PERMISSION_CHANGED] ".GetMessage("MAIN_EVENTLOG_FILE"),
	"TASK_CHANGED" => "[TASK_CHANGED] ".GetMessage("MAIN_EVENTLOG_TASK"),
);

$db_events = GetModuleEvents("main", "OnEventLogGetAuditTypes");
while($arEvent = $db_events->Fetch()) {
	$ar = ExecuteModuleEventEx($arEvent);
	if(is_array($ar)) {
		$arAuditTypes = array_merge($ar, $arAuditTypes);
	}
}

$db_events = GetModuleEvents("main", "OnEventLogGetAuditTypes");
while($arEvent = $db_events->Fetch()) {
	$ar = ExecuteModuleEventEx($arEvent);
	if(is_array($ar)) {
		$arAuditTypes = array_merge($ar, $arAuditTypes);
	}
}

$arComponentParameters = array(
	"PARAMETERS" => array(
		"TYPES" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_TYPES"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"SIZE" => "10",
			"VALUES" => $arAuditTypes
		),
		"NOT_SHOW_USER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_NOT_SHOW_USER"),
			"TYPE" => "CHECKBOX",
		),
		"FILTER_USER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_FILTER_USER"),
			"TYPE" => "CHECKBOX",
		),
		"SITE_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_SITE_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arSites,
		),
		"COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"PAGER_TEMPLATE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_PAGER_TEMPLATE"),
			"TYPE" => "STRING",
		),
		"USER_LINK" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ASD_CMP_PARAM_USER_LINK"),
			"TYPE" => "STRING",
		),
	),
);
?>