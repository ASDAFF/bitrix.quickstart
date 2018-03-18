<?
define('NOT_CHECK_PERMISSIONS', (strpos($_SERVER['REMOTE_ADDR'], '127.') === 0));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if(!NOT_CHECK_PERMISSIONS && !$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
CModule::IncludeModule('bitrix.liveapi');

$offset = intval($_REQUEST['offset']);
if ($_REQUEST['file'])
{
	if (!($f = fopen($_SERVER['DOCUMENT_ROOT'].$_REQUEST['file'], 'rb')))
		die('Cannot read '.htmlspecialchars($_REQUEST['file']));
	fseek($f, $offset);

	$str = '';
	$open = $close = 0;
	while(false !== ($l = fgets($f)))
	{
		$open += substr_count($l, '{');
		$close += substr_count($l, '}');

		$str .= $l;

		if ($open > 0 && $close >= $open)
			break;
	}
	fclose($f);

	$str = CBitrixLiveapi::Beautiful($str);
	if (defined('BX_UTF') && BX_UTF)
		$str = $APPLICATION->ConvertCharSet($str, 'cp1251', 'utf8');
	echo $str;
}
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_after.php");
