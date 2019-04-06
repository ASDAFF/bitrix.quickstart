<?
$HTTP_ACCEPT_ENCODING = '';
session_cache_limiter("public");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
IncludeModuleLangFile(__FILE__);


function _GtFMess()
{
	if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/defa.tools/lang/'.LANGUAGE_ID.'/admin/defa_tools_js.php'))
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/defa.tools/lang/'.LANGUAGE_ID.'/admin/defa_tools_js.php');
	else
		include($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/defa.tools/lang/en/admin/defa_tools_js.php');

	return $MESS;
}

$file_version = @filemtime($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/defa.tools/lang/'.LANGUAGE_ID.'/admin/defa_tools_js.php');

if (!isset($_SERVER['HTTP_IF_NONE_MATCH']) || $_SERVER['HTTP_IF_NONE_MATCH'] != '"'.$file_version.'"')
{
	header("Pragma: private");
	header("Cache-Control: public, max-age=2592000"); // 30 days
	header('ETag: "'.$file_version.'"');
	header("Content-Type: application/x-javascript; charset=".LANG_CHARSET);
	$sMess = "";

	$aMess = _GtFMess();
	$aMess = array_keys($aMess);
	for($i=0; $i<count($aMess); $i++)
		if(substr($aMess[$i], 0, strlen("DT_JS_"))=="DT_JS_")
			$sMess .= "'".substr($aMess[$i], strlen("DT_JS_"))."': '".CUtil::addslashes(GetMessage($aMess[$i]))."',";

	$sMess = rtrim($sMess,',');
	?>var DT_MESS = window.DT_MESS = {<?=$sMess?>};<?
}
else
{
	CHTTP::SetStatus("304 Not Modified");
	header("Pragma: private");
	header("Cache-Control: public, max-age=2592000"); // 30 days
	header('ETag: "'.$file_version.'"');
	header("Content-Type: application/x-javascript; charset=".LANG_CHARSET);

	while(ob_get_level()) ob_end_clean();
	exit;
}
