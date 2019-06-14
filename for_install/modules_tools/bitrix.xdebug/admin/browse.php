<?
$module_id = 'bitrix.xdebug';
define('ADMIN_MODULE_NAME', $module_id);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
if(!$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if (!CModule::IncludeModule('bitrix.xdebug'))
	ShowErrAndDie('bitrix.xdebug module is not installed');

$trace_output_dir_default = $trace_output_dir = COption::GetOptionString($module_id, 'xdebug.trace_output_dir', ini_get('xdebug.trace_output_dir'));
if ($_REQUEST['trace_output_dir'] && is_dir($_REQUEST['trace_output_dir']))
	$trace_output_dir = realpath($_REQUEST['trace_output_dir']);

$sTableID = "tbl_debug";
$lAdmin = new CAdminList($sTableID);
$lAdmin->BeginPrologContent();
if ($arID = $lAdmin->GroupAction())
{
	if($_REQUEST['action_target'] == 'selected')
	{
		$arID = array();
		if ($dir = opendir($trace_output_dir))
		{
			while($item = readdir($dir))
			{
				if (!is_dir($trace_output_dir.'/'.$item))
					$arID[] = $item;
			}
			closedir($dir);
		}
	}

	foreach ($arID as $ID)
	{

		if (strlen($ID) <= 0)
			continue;

		switch ($_REQUEST['action'])
		{
			case "delete":
				if (is_file($trace_output_dir.'/'.$ID))
				{
					if (unlink($trace_output_dir.'/'.$ID))
						CXDProfiler::ClearTrace($trace_output_dir.'/'.$ID);
					else
						$lAdmin->AddGroupError(GetMessage("BITRIX_XDEBUG_NE_UDALOSQ_UDALITQ_F").$ID, $ID);
				}
			break;
			case "clear":
				CXDProfiler::ClearTrace($trace_output_dir.'/'.$ID);
			break;
		}
	}
}

$lAdmin->AddHeaders(array(
	array("id"=>"NAME", "content"=>GetMessage("BITRIX_XDEBUG_IMA_FAYLA"), "sort"=>"name", "default"=>true),
	array("id"=>"DATE","content"=>GetMessage("BITRIX_XDEBUG_DATA_IZMENENIA"), "sort"=>"date", "default"=>true),
	array("id"=>"SIZE","content"=>GetMessage("BITRIX_XDEBUG_RAZMER"), "sort"=>"size", "default"=>true),
	array("id"=>"PAGE", "content"=>GetMessage("BITRIX_XDEBUG_STRANICA"), "sort"=>"page", "default"=>true),
	array("id"=>"TIME", "content"=>GetMessage("BITRIX_XDEBUG_VREMA"), "sort"=>"page", "default"=>true)
));


$ar = array();
if ($dir = opendir($trace_output_dir))
{
	while($item = readdir($dir))
	{
		if ($item == '.')
			continue;
		$ar[(is_dir($trace_output_dir.'/'.$item) ? ($item == '..' ? 'c' : 'b') : 'a').filectime($trace_output_dir.'/'.$item).$item] = $item;
	}
	closedir($dir);
}
krsort($ar);
	
$XRec = new CXDRecord;
$info_cnt = 0;
foreach($ar as $item)
{
	$row =& $lAdmin->AddRow($item);
	$f = $trace_output_dir.'/'.$item;
	$link = is_dir($f) ? '?trace_output_dir='.urlencode($f) : $module_id.'_#FILE#.php?trace='.urlencode($item).($trace_output_dir == $trace_output_dir_default ? '' : '&trace_output_dir='.urlencode($trace_output_dir));
	$row->AddField('NAME', '<a href="'.str_replace('#FILE#', 'tracer', $link).'">'.$item.'</a>');
	$row->AddField('DATE', is_dir($f) ? '' : FormatDate('x', filemtime($trace_output_dir.'/'.$item)));
	$row->AddField('SIZE', is_dir($f) ? '' : CBitrixXdebug::HumanSize(filesize($trace_output_dir.'/'.$item)));

	if ($info_cnt++ <= 10 && is_file($f) && $rs = fopen($f, 'rb'))
	{
		$l = fgets($rs);
		$l = fgets($rs);
		if ($XRec->InitFromLine($l) && $XRec->type == CXDRecord::TYPE_VAR && $XRec->text_var_name == 'PAGE')
		{
			$page = htmlspecialcharsbx(trim($XRec->text_var_val, "'"));
			$row->AddField('PAGE', '<a href="http://'.preg_replace('#^[A-Z]+ #', '', $page).'" target=_blank>'.substr($page, 0, 100).(strlen($page) > 100 ? '...' : '').'</a>');
		}
		fclose($rs);

		$row->AddField('TIME', CXDTracer::GetTraceTime($f));
	}

	$arActions = array(
		array(
			"ICON" => "file",
			"DEFAULT" => true,
			"TEXT" => GetMessage("BITRIX_XDEBUG_OTLADKA"),
			"ACTION" => $lAdmin->ActionRedirect(str_replace('#FILE#', 'tracer', $link))
		),
		array(
			"ICON" => "file",
			"DEFAULT" => false,
			"TEXT" => GetMessage("BITRIX_XDEBUG_PROFILIROVANIE"),
			"ACTION" => $lAdmin->ActionRedirect(str_replace('#FILE#', 'profiler', $link))
		)
	);

	if (CXDProfiler::GetTraceId($trace_output_dir.'/'.$item))
		$arActions[] = array(
			"ICON" => "clear",
			"TEXT" => GetMessage("BITRIX_XDEBUG_OCISTITQ_PROFILQ"),
			"ACTION" => 'if(confirm("'.GetMessage("BITRIX_XDEBUG_OCISTITQ_DANNYE_PROF").'"))'.$lAdmin->ActionDoGroup($item, "clear", "trace_output_dir=".$trace_output_dir)
		);

	if (is_file($f) && is_writable($f))
		$arActions[] = array(
			"ICON" => "delete",
			"TEXT" => GetMessage("BITRIX_XDEBUG_UDALITQ"),
			"ACTION" => 'if(confirm("'.GetMessage("BITRIX_XDEBUG_UDALITQ_FAYL").'"))'.$lAdmin->ActionDoGroup($item, "delete", "trace_output_dir=".$trace_output_dir)
		);
	$row->AddActions($arActions);
}
$lAdmin->AddGroupActionTable(array("delete" => GetMessage("BITRIX_XDEBUG_UDALITQ")));
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("BITRIX_XDEBUG_VYBOR_TREYSA"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form method=get action=?><?=GetMessage("BITRIX_XDEBUG_PUTQ")?><input name=trace_output_dir value="<?=htmlspecialcharsbx($trace_output_dir)?>" size=60> <input type=submit value=" ok "></form>
<?
$lAdmin->DisplayList();
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
