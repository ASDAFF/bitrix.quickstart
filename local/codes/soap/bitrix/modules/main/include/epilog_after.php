<?
define("START_EXEC_EPILOG_AFTER_1", microtime());
$GLOBALS["BX_STATE"] = "EA";

/// for PHP <4.3.4 (bug)
if(!isset($USER))		{global $USER;}
if(!isset($APPLICATION)){global $APPLICATION;}
if(!isset($DB))			{global $DB;}
///

$db_events = GetModuleEvents("main", "OnEpilog");
while($arEvent = $db_events->Fetch())
	ExecuteModuleEventEx($arEvent);

if(array_key_exists("show_lang_files", $_GET) || array_key_exists("SHOW_LANG_FILES", $_SESSION))
	include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/interface/lang_files.php");

if($USER->CanDoOperation('edit_php'))
	$_SESSION["SHOW_SQL_STAT"] = ($GLOBALS["DB"]->ShowSqlStat? "Y":"N");

$bShowTime = ($_SESSION["SESS_SHOW_TIME_EXEC"] == 'Y');
$bShowStat = ($GLOBALS["DB"]->ShowSqlStat && ($USER->CanDoOperation('edit_php') || $_SESSION["SHOW_SQL_STAT"]=="Y"));

if($bShowStat && !$USER->IsAuthorized())
{
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/init_admin.php");
	$GLOBALS["APPLICATION"]->AddHeadString($GLOBALS["adminPage"]->ShowScript());
	$GLOBALS["APPLICATION"]->AddHeadString('<script type="text/javascript" src="/bitrix/js/main/public_tools.js"></script>');
	$GLOBALS["APPLICATION"]->AddHeadString('<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/pubstyles.css" />');
}

if ($bShowStat || $bShowTime)
{
	CUtil::InitJSCore(array('window', 'admin'));
}

$r = $APPLICATION->EndBufferContentMan();
$main_exec_time = round((getmicrotime()-START_EXEC_TIME), 4);
echo $r;

if(defined("HTML_PAGES_FILE") && !defined("ERROR_404")) CHTMLPagesCache::writeFile(HTML_PAGES_FILE, $r);

$arAllEvents = Array();
$db_events = GetModuleEvents("main", "OnAfterEpilog");
while($arEvent = $db_events->Fetch())
	$arAllEvents[] = $arEvent;

define("START_EXEC_EVENTS_1", microtime());
$GLOBALS["BX_STATE"] = "EV";
CMain::EpilogActions();
define("START_EXEC_EVENTS_2", microtime());
$GLOBALS["BX_STATE"] = "EA";

for($i=0; $i<count($arAllEvents); $i++)
	ExecuteModuleEventEx($arAllEvents[$i]);

if(!IsModuleInstalled("compression") && !defined('PUBLIC_AJAX_MODE') && ($_REQUEST["mode"] != 'excel'))
{
	if($bShowTime || $bShowStat)
		include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/interface/debug_info.php");
}

$DB->Disconnect();

CMain::ForkActions();
?>
