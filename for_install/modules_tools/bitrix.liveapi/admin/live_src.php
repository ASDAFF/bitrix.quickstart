<?
define('NOT_CHECK_PERMISSIONS', (strpos($_SERVER['REMOTE_ADDR'], '127.') === 0));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if(!NOT_CHECK_PERMISSIONS && !$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
CModule::IncludeModule('bitrix.liveapi');

if (file_exists(DATA_FILE))
	include(DATA_FILE);

if (!is_array($ar = unserialize($DATA[$_REQUEST['module_id']])))
	die(GetMessage("BITRIX_LIVEAPI_MODULQ_NE_NAYDEN"));

//echo '<pre>'; print_r($ar); echo '</pre>';

$name = $_REQUEST['name'];
$name0 = strtolower($name);
$v = false;
foreach($ar[0] as $k0 => $v0)
{
	if (strtolower($k0) == $name0)
	{
		$name = $k0;
		$v = $v0;
		break;
	}
}

if (!$v)
	die(GetMessage("BITRIX_LIVEAPI_METOD_NE_NAYDEN"));
	
$offset = $v['OFFSET'];
$file = $v['FILE'];
$line = $v['LINE'];


if (!($f = fopen($_SERVER['DOCUMENT_ROOT'].$file, 'rb')))
	die('Cannot read '.htmlspecialchars($file));

$namespace = '';
$i=0;
while(true)
{
	$l = fgets($f);
	if (preg_match("#^[ \t]*namespace ([a-z\\\\0-9]+);#i", $l, $regs))
	{
			$namespace = $regs[1].'\\';
			break;
	}
	
	if (preg_match('#function|class#i',$l))
		break;
	if (++$i > 20)
		break;
}

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

$str = CBitrixLiveapi::Beautiful($str, $name, $namespace, $file, $line, $_REQUEST['highlight']);
if (defined('BX_UTF') && BX_UTF)
	$str = $APPLICATION->ConvertCharSet($str, 'cp1251', 'utf8');
echo $str;
	
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_after.php");
