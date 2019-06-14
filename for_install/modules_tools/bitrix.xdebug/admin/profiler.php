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

if (!$trace = trim($_REQUEST['trace']))
	LocalRedirect('/bitrix/admin/bitrix.xdebug_browse.php');

$pos = intval($_REQUEST['pos']);

if (!file_exists($trace_output_dir.'/'.$trace))
	ShowErrAndDie(GetMessage("BITRIX_XDEBUG_FAYL_NE_NAYDEN").htmlspecialcharsbx($trace_output_dir.'/'.$trace));

$XProfile = new CXDProfiler;
if ($_REQUEST['mode'] == 'ajax')
{
	########## ajax #########
	if (!$XProfile->Start($trace_output_dir.'/'.$trace, $pos))
		ShowErrAndDie("Can't open file: ".$trace); 

	$NS = &$_SESSION['bitrix.xdebug'][$trace];
	if (!$pos)
	{
		$NS = array(
			'start_time' => microtime(1),
			'arStack' => array()
		);
	}

	$XProfile->arStack = $NS['arStack'];
	$end = $XProfile->Parse();
	$pos = $XProfile->pos;
	$NS['arStack'] = $XProfile->arStack;

	$filesize = filesize($trace_output_dir.'/'.$trace);
	$time_left = (microtime(1) - $NS['start_time']) / ($XProfile->pos + 1) * ($filesize - $XProfile->pos);
	$m = new CAdminMessage(array(
		"TYPE" => "PROGRESS",
		"MESSAGE" => GetMessage("BITRIX_XDEBUG_OBRABOTKA_TREYSA"),
		"DETAILS" => '#PROGRESS_BAR#'.' '.GetMessage("BITRIX_XDEBUG_POZICIA").CBitrixXdebug::HumanSize($XProfile->pos).' '.GetMessage("BITRIX_XDEBUG_IZ").CBitrixXdebug::HumanSize($filesize).', '.GetMessage("BITRIX_XDEBUG_OSTALOSQ_PRIMERNO").sprintf('%02d:%02d:%02d', floor($time_left / 3600), floor($time_left % 3600 / 60), $time_left % 60),
		"HTML" => true,
		"PROGRESS_TOTAL" => $filesize,
		"PROGRESS_VALUE" => $XProfile->pos
	));
	?>
	<script>
			if (reset)
				document.location='/bitrix/admin/bitrix.xdebug_browse.php?trace_output_dir=<?=urlencode($trace_output_dir)?>&action=clear&ID=<?=urlencode($trace)?>&<?=bitrix_sessid_get()?>&lang=<?=LANGUAGE_ID?>';
			else
			{
				<?
				if ($end)
					echo "document.location = '?trace_output_dir=".urlencode($trace_output_dir)."&trace=".urlencode($trace)."';";
				else	
					echo "BX.ajax.get('?mode=ajax&trace_output_dir=".urlencode($trace_output_dir)."&trace=".urlencode($trace)."&pos=".$pos."');";
				?>
				BX('progress_status').innerHTML = "<?=CUtil::JSEscape($m->Show())?>";
			}
	</script>
		<?
	/*	
		$rs = $DB->Query('SELECT MAX(trace_id) A from b_xdebug');
		$f = $rs->Fetch();

		$rs = $DB->Query('SELECT SUM(cnt) AS cnt FROM b_xdebug WHERE  trace_id='.$f['A'].' AND text_func_name="count" ' );
		$f = $rs->Fetch();

		echo ' X='.($x=$f['cnt']).' ';
		echo ' Y='.($y=intval(`head -c {$XProfile->pos} $trace_output_dir/$trace | grep \ count\( | wc -l`)).' ';
		echo ' d='.($x-$y).' ';
#		if (intval($x)!=intval($y))
#			echo '<script>xxx=1;</script>';
	*/		

		?>
	<?
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_after.php");
	exit();
	############
}
elseif (!($trace_id = $XProfile->GetTraceId($trace_output_dir.'/'.$trace)) || $pos)
{

	$APPLICATION->SetTitle(GetMessage("BITRIX_XDEBUG_OBRABOTKA_TREYSA1").$trace);
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	$aMenu = array(
		array(
			"TEXT"	=> GetMessage("BITRIX_XDEBUG_OTMENA"),
			"ONCLICK" => 'reset=1;ShowWaitWindow();',
			"ICON"	=> "btn_list"
		)
	);
	$context = new CAdminContextMenu($aMenu);
	$context->Show();

	?>
	<div id=progress_status>
	<?
		CAdminMessage::ShowMessage(array(
			"TYPE" => "PROGRESS",
			"MESSAGE" => GetMessage("BITRIX_XDEBUG_OBRABOTKA_TREYSA"),
			"DETAILS" => '#PROGRESS_BAR#',
			"HTML" => true,
			"PROGRESS_TOTAL" => 100,
			"PROGRESS_VALUE" => 0 
		));

	?>
	</div>
	<script>
		var reset;
		BX.ajax.get('?mode=ajax&trace_output_dir=<?=urlencode($trace_output_dir)?>&trace=<?=urlencode($trace)?>&pos=0');
	</script>
	<?
}
else
{
	$rs = $DB->Query('SELECT * FROM b_xdebug_trace WHERE id='.$trace_id);
	$arTrace = $rs->Fetch();


	$sTableID = "tbl_xdebug";
	$oSort = new CAdminSorting($sTableID, "include_time", "desc");
	$lAdmin = new CAdminList($sTableID, $oSort);

	$filter = new CAdminFilter(
		$sTableID."_filter",
		array(
			GetMessage("curr_rates_curr1"),
		)
	);

	$arFilterFields = Array(
		"group_by_function",
		"filter_by_function"
	);

	$lAdmin->InitFilter($arFilterFields);

	$lAdmin->BeginPrologContent();
	$arHeaders = array(
			array("id"=>"include_time", "content"=>GetMessage("BITRIX_XDEBUG_INTEGRALQNOE_VREMA"), "sort"=>"include_time", "default"=>true),
			array("id"=>"own_time", "content"=>GetMessage("BITRIX_XDEBUG_SOBSTVENNOE_VREMA"), "sort"=>"own_time", "default"=>true),
			array("id"=>"cnt", "content"=>GetMessage("BITRIX_XDEBUG_CISLO_VYZOVOV"), "sort"=>"cnt", "default"=>true),
			array("id"=>"avg_time", "content"=>GetMessage("BITRIX_XDEBUG_SREDNEE_VREMA"), "sort"=>"avg_time", "default"=>false),
			array("id"=>"text_func_name", "content"=>GetMessage("BITRIX_XDEBUG_FUNKCIA"), "sort"=>"text_func_name", "default"=>true),
			array("id"=>"file", "content"=>GetMessage("BITRIX_XDEBUG_VYZYVAETSA"), "sort"=>"file", "default"=>false),
		);
	
	if ($time = CXDTracer::GetTraceTime($arTrace['trace']))
	{
		$arHeaders[] = array("id"=>"include_percent", "content"=>GetMessage("BITRIX_XDEBUG_INTEGRALQNAA_OCENKA"), "sort"=>"include_time", "default"=>false);
		$arHeaders[] = array("id"=>"own_percent", "content"=>GetMessage("BITRIX_XDEBUG_SOBSTVENNOE_VREMA1"), "sort"=>"own_time", "default"=>false);
	}

	$lAdmin->AddHeaders($arHeaders);

	$res_cnt = $DB->Query('SELECT COUNT('.($group_by_function ? 'DISTINCT text_func_name' : 1).') AS C FROM b_xdebug WHERE trace_id='.$trace_id.($filter_by_function ? ' AND text_func_name LIKE "'.$DB->ForSQL($filter_by_function).'"' : ''));
	$f = $res_cnt->Fetch();

	$rsData = new CDBResult();
	if ($group_by_function)
		$strSql = 'SELECT SUM(include_time) include_time, SUM(own_time) own_time, '.($time ? 'SUM(include_time) / '.$time.' * 100 include_percent, SUM(own_time) / '.$time.' * 100 own_percent, ' : '').' SUM(cnt) cnt, SUM(own_time)/SUM(cnt) avg_time, text_func_name FROM b_xdebug WHERE trace_id='.$trace_id.($filter_by_function ? ' AND text_func_name LIKE "'.$DB->ForSQL($filter_by_function).'"' : '').' GROUP BY text_func_name ORDER BY `'.$by.'` '.$order;
	else
		$strSql = 'SELECT *,'.($time ? 'include_time / '.$time.' * 100 include_percent, own_time / '.$time.' * 100 own_percent, ' : '').' own_time/cnt avg_time FROM b_xdebug WHERE trace_id='.$trace_id.($filter_by_function ? ' AND text_func_name LIKE "'.$DB->ForSQL($filter_by_function).'"' : '').' ORDER BY `'.$by.'` '.$order;

	$rsData->NavQuery($strSql, $f["C"], array("nPageSize"=>CAdminResult::GetNavSize($sTableID)));
	$rsData = new CAdminResult($rsData, $sTableID);
	$rsData->NavStart();
	$lAdmin->NavText($rsData->GetNavPrint(GetMessage("BITRIX_XDEBUG_STRANICY")));

	while($f = $rsData->NavNext(true, "f_"))
	{
		$row =& $lAdmin->AddRow($f['id'], $f);
		$link = '/bitrix/admin/bitrix.xdebug_tracer.php?trace_output_dir='.urlencode(dirname($arTrace['trace'])).'&trace='.urlencode(basename($arTrace['trace'])).'#0/'.intval($f['pos']).'/0';
		if (!$group_by_function)
		{
			$row->AddField("text_func_name", '<a href="'.$link.'" title="'.GetMessage("BITRIX_XDEBUG_PEREHOD_K_NAIBOLEE_D").'" target=_blank>'.$f['text_func_name'].'</a>');
			$row->AddField("file", '<a href="'.$link.'" title="'.GetMessage("BITRIX_XDEBUG_PEREHOD_K_NAIBOLEE_D").'" target=_blank>'.$f['file'].':'.$f['line'].'</a>');
		}

		if ($time)
		{
			$row->AddField("include_percent", round($f['include_percent'], 4));
			$row->AddField("own_percent", round($f['own_percent'], 4));
		}

		$row->AddField("include_time", round($f['include_time'], 4));
		$row->AddField("own_time", round($f['own_time'], 4));
		$row->AddField("avg_time", round($f['avg_time'], 4));
	}
	$lAdmin->AddAdminContextMenu(array());
	$lAdmin->CheckListMode();
	$APPLICATION->SetTitle(GetMessage("BITRIX_XDEBUG_PROFILIROVANIE").': '.$trace.' ('.CBitrixXdebug::HumanSize(filesize($trace_output_dir.'/'.$trace)).') '.GetMessage("BITRIX_XDEBUG_OT").date('d.m.Y H:i:s', filemtime($trace_output_dir.'/'.$trace)));
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

	$aMenu = array(
		array(
			"TEXT"	=> GetMessage("BITRIX_XDEBUG_OBZOR"),
			"LINK"	=> '/bitrix/admin/bitrix.xdebug_browse.php?trace_output_dir='.urlencode($trace_output_dir).'&lang='.LANGUAGE_ID,
			"TITLE"	=> '',
			"ICON"	=> "btn_list"
		)
	);
	$context = new CAdminContextMenu($aMenu);
	$context->Show();
	?>
<form method="get" action="<?=$APPLICATION->GetCurPage()?>" name="find_form">
<?$filter->Begin();?>
	<tr>
		<td><?=GetMessage("BITRIX_XDEBUG_FILQTR_PO_IMENI_FUNK")?></td>
		<td><input name="filter_by_function"></td>
	</tr>
	<tr>
		<td><?=GetMessage("BITRIX_XDEBUG_GRUPPIROVATQ_PO_IMEN")?></td>
		<td><input type="checkbox" name="group_by_function"></td>
	</tr>
<?$filter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage().'?trace_output_dir='.urlencode($trace_output_dir).'&trace='.urlencode($trace), "form"=>"find_form"));$filter->End();?>
</form>
<?
	$lAdmin->DisplayList();
}
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>
