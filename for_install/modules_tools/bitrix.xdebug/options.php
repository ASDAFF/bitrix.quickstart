<?
if(!$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
$module_id = 'bitrix.xdebug';
$strWarning = "";
if ($_POST['Reset'] && check_bitrix_sessid())
{
	COption::RemoveOption($module_id);
}
elseif ($_POST['Update'] && check_bitrix_sessid())
{
	if ($start_trace_condition = trim($_REQUEST['start_trace_condition']))
	{
		eval($start_trace_condition.';');
		if ($e = error_get_last())
		{
			if ($e['type'] &  ~E_NOTICE && false !== strpos($e['file'], 'eval'))
				$strWarning .= GetMessage("BITRIX_XDEBUG_USLOVIE_SODERJIT").' [type '.$e['type'].'] '.$e['message'].'<br>';
		}

		COption::SetOptionString($module_id, 'start_trace_condition', $start_trace_condition);
	}

	if ($m = intval($_REQUEST['start_trace']))
		COption::SetOptionString($module_id, 'trace_active_time', time() + $m * 60);
	elseif ($_REQUEST['stop_trace'])
		COption::RemoveOption($module_id, 'trace_active_time');

	COption::SetOptionString($module_id, 'start_trace_cookie', $_REQUEST['start_trace_cookie'] == 1 ? 1 : 0);

	COption::SetOptionString($module_id, 'xdebug.collect_assignments', $_REQUEST['collect_assignments'] == 1 ? 1 : 0);
	COption::SetOptionString($module_id, 'xdebug.collect_return', $_REQUEST['collect_return'] == 1 ? 1 : 0);
	COption::SetOptionString($module_id, 'xdebug.collect_params', intval($_REQUEST['collect_params']));
	if ($trace_output_dir = trim($_REQUEST['trace_output_dir']))
	{
		if (!is_dir($trace_output_dir))
			$strWarning .= GetMessage("BITRIX_XDEBUG_PAPKA_DLA_SOHRANENIA").'<br>';
		elseif (!is_writable($trace_output_dir))
			$strWarning .= GetMessage("BITRIX_XDEBUG_PAPKA_DLA_SOHRANENIA1").'<br>';
		COption::SetOptionString($module_id, 'xdebug.trace_output_dir', $trace_output_dir);
	}
	else
		COption::RemoveOption($module_id, 'xdebug.trace_output_dir');

	if ($trace_output_name = trim($_REQUEST['trace_output_name']))
		COption::SetOptionString($module_id, 'xdebug.trace_output_name', $trace_output_name);
	else
		COption::SetOptionString($module_id, 'xdebug.trace_output_name', 'trace.%t');

	COption::SetOptionString($module_id, 'buffer_size', intval($_REQUEST['buffer_size']));
}
elseif (!extension_loaded('xdebug'))
	$strWarning = GetMessage("BITRIX_XDEBUG_MODULQ_NE_VKL");

if (strlen($strWarning) > 0)
	CAdminMessage::ShowMessage($strWarning);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("BITRIX_XDEBUG_NASTROYKA"), "ICON" => "", "TITLE" => GetMessage("BITRIX_XDEBUG_NASTROYKA_OTLADCIKA")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
<form method="POST" name="" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>" ENCTYPE="multipart/form-data"><?
echo bitrix_sessid_post();
$tabControl->BeginNextTab();
?>
	<tr class="heading">
		<td colspan="2"><b><?=GetMessage("BITRIX_XDEBUG_SBOR_OTLADOCNOY_INFO")?></b></td>
	</tr>
	<? 
	$trace_active_time = COption::GetOptionString($module_id, 'trace_active_time', 0);
	$left = $trace_active_time - time();
	?>
	<script>
		var left = <?=$left?>;
		var timer_span;
		function Tick()
		{
			left--;
			if (left >= 0)
			{
				if (!timer_span)
					timer_span = BX('timer_span');
				h = Math.floor(left/3600);
				m = Math.floor(left%3600 / 60);
				s = left%60;

				h = h < 10 ? '0' + h : h;
				m = m < 10 ? '0' + m : m;
				s = s < 10 ? '0' + s : s;
				timer_span.innerHTML = h + ':' + m + ':' + s; 
				window.setTimeout(Tick, 1000);
			}
			else
			{
				BX('xdebug_timer').style.display = 'none';
				BX('xdebug_starter').style.display = '';
			}
		}
		window.setTimeout(Tick, 1000);

		function SetCondition(num)
		{
			if (ob = BX('start_trace_condition'))
			{
				if (num == 0)
					ob.value = '';
				if (num == 1)
					ob.value = '$USER->IsAdmin()';
				if (num == 2)
					ob.value = '$USER->GetLogin() == "<?=CUtil::JSEscape($USER->GetLogin())?>"';
				if (num == 3)
					ob.value = '$_SERVER["REMOTE_ADDR"] == "<?=$_SERVER['REMOTE_ADDR']?>"';
				if (num == 4)
					ob.value = '$_SERVER["REQUEST_METHOD"] == "POST"';
				if (num == 5)
					ob.value = 'CSite::InDir("/")';
				if (num == 6)
					ob.value = '$_GET["trace"] == "Y"';
			}
		}
	</script>
	<tr id="xdebug_starter" style="display:<?=$left > 0 ? 'none' : '' ?>">
		<td width="50%"><?=GetMessage("BITRIX_XDEBUG_VKLUCITQ_SBOR_OTLADK")?></td>
		<td width="50%">
			<select name="start_trace">
			<?
			foreach(array(
					0 => GetMessage("BITRIX_XDEBUG_NET"),
					1 => GetMessage("BITRIX_XDEBUG_NA_MINUTU"),
					5 => GetMessage("BITRIX_XDEBUG_NA_MINUT"),
					10 => GetMessage("BITRIX_XDEBUG_NA_MINUT1"),
					30 => GetMessage("BITRIX_XDEBUG_NA_MINUT2"),
					60 => GetMessage("BITRIX_XDEBUG_NA_CAS")
				) as $k => $v)
					echo '<option value="'.$k.'">'.$v.'</option>';
			?>
			</select>
		</td>
	</tr>
	<tr id="xdebug_timer" style="display:<?=$left > 0 ? '' : 'none' ?>">
		<td width="50%"><?=GetMessage("BITRIX_XDEBUG_DO_OKONCANIA_SBORA_O")?></td>
		<td width="50%"><span id="timer_span"><? printf('%02d:%02d:%02d',floor($left/3600), floor($left % 3600)/60, $left % 60); ?></span> <label><input type=checkbox name=stop_trace> <?=GetMessage("BITRIX_XDEBUG_OSTANOVITQ")?></label></td>
	</tr>
	<tr>
	<?
		$start_trace_condition = COption::GetOptionString($module_id, 'start_trace_condition', '');
	?>
		<td width="50%"><?=GetMessage("BITRIX_XDEBUG_USLOVIE")?></td>
		<td width="50%">
			<select onchange="SetCondition(this.value)">
			<?
			foreach(array(
					GetMessage("BITRIX_XDEBUG_DLA_VSEH"),
					GetMessage("BITRIX_XDEBUG_DLA_ADMINISTRATOROV"),
					GetMessage("BITRIX_XDEBUG_DLA_MOEGO_LOGINA"),
					GetMessage("BITRIX_XDEBUG_DLA_MOEGO"),
					GetMessage("BITRIX_XDEBUG_DLA_ZAPROSOV"),
					GetMessage("BITRIX_XDEBUG_DLA_PAPKI"),
				) as $k => $v)
					echo '<option value="'.$k.'">'.$v.'</option>';
			?>
			</select>
			<input name="start_trace_condition" id="start_trace_condition" value="<?=htmlspecialcharsbx($start_trace_condition)?>" size=20>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><b><?=GetMessage("BITRIX_XDEBUG_SBOR_OTLADOCNOY_INFO1")?></b></td>
	</tr>
	<tr>
	<?
		$start_trace_cookie = COption::GetOptionString($module_id, 'start_trace_cookie', 0);
	?>
		<td width="50%"><?=GetMessage("BITRIX_XDEBUG_SOBIRATQ_OTLADKU_ESL")?></td>
		<td width="50%">
			<input type="checkbox" name="start_trace_cookie" value="1" <?=$start_trace_cookie ? 'checked' : ''?>>
		</td>
	</tr>
	<tr>
		<td colspan=2><?=GetMessage("BITRIX_XDEBUG_PECIALQNYE_SSYLKI_PO")?>
			<a href="javascript:(function() {document.cookie='BITRIX_XDEBUG_TRACE='+'1'+';path=/;';})()">Start BX</a> 
			<?=GetMessage("BITRIX_XDEBUG_I")?>
			<a href="javascript:(function() {document.cookie='BITRIX_XDEBUG_TRACE='+''+';expires=Mon, 05 Jul 2000 00:00:00 GMT;path=/;';})()">Stop BX</a>.
			<br>
			<?=GetMessage("BITRIX_XDEBUG_VY_MOJETE_SOHRANITQ")?></td>
	</tr>
	<tr class="heading">
		<td colspan="2"><b><?=GetMessage("BITRIX_XDEBUG_PARAMETRY")?></b></td>
	</tr>
	<?
$arAllOptions = array(
	array("xdebug.collect_params", "", "3", Array("select", )),
);
	?>
	<tr>
		<td width="50%"><?=GetMessage("BITRIX_XDEBUG_SOBIRATQ_IZMENENIA_Z")?></td>
		<td width="50%" valign=top><input type="checkbox" name="collect_assignments" value="1" <?=COption::GetOptionString($module_id, 'xdebug.collect_assignments', 1) == 1 ? 'checked' : ''?>></td>
	</tr>
	<tr>
		<td width="50%"><?=GetMessage("BITRIX_XDEBUG_SOBIRATQ_VOZVRASAEMY")?></td>
		<td width="50%" valign=top><input type="checkbox" name="collect_return" value="1" <?=COption::GetOptionString($module_id, 'xdebug.collect_return', 1) == 1 ? 'checked' : ''?>></td>
	</tr>
	<?
		$collect_params = COption::GetOptionString($module_id, 'xdebug.collect_params', 3);
	?>
	<tr>
		<td width="50%"><?=GetMessage("BITRIX_XDEBUG_FIKSIROVATQ_PARAMETR")?></td>
		<td width="50%">
			<select name="collect_params">
			<?
			foreach(array(
					0 => GetMessage("BITRIX_XDEBUG_VYKLUCENO"),
					1 => GetMessage("BITRIX_XDEBUG_TOLQKO_TIP_PEREMENNY"),
					2 => GetMessage("BITRIX_XDEBUG_DOPOLNITELQNAA_INFOR"),
					3 => GetMessage("BITRIX_XDEBUG_ZNACENIA_PEREMENNYH"),
					4 => GetMessage("BITRIX_XDEBUG_ZNACENIA_I_IMENA_PER")
				) as $k => $v)
					echo '<option value="'.$k.'" '.($collect_params == $k ? 'selected' : '').'>'.$v.'</option>';
			?>
			</select>
		</td>
	</tr>
	<?
		$buffer_size = intval(COption::GetOptionString($module_id, 'buffer_size', 100));
	?>
	<?
		$trace_output_dir = COption::GetOptionString($module_id, 'xdebug.trace_output_dir', ini_get('xdebug.trace_output_dir'));
	?>
	<tr>
		<td width="50%"><?=GetMessage("BITRIX_XDEBUG_PUTQ_DLA_SOHRANENIA")?></td>
		<td width="50%" valign=top><input name="trace_output_dir" value="<?=htmlspecialcharsbx($trace_output_dir)?>" size=30></td>
	</tr>
	<?
		$trace_output_name = COption::GetOptionString($module_id, 'xdebug.trace_output_name', 'trace.%t');
	?>
	<tr>
		<td width="50%"><a href="http://xdebug.org/docs/all_settings#trace_output_name" target=_blank><?=GetMessage("BITRIX_XDEBUG_SABLON_IMENI_FAYLA_T")?></a>:</td>
		<td width="50%" valign=top><input name="trace_output_name" value="<?=htmlspecialcharsbx($trace_output_name)?>" size=20></td>
	</tr>
	<tr class="heading">
		<td colspan="2"><b><?=GetMessage("BITRIX_XDEBUG_REJIM_RABOTY")?></b></td>
	</tr>
	<tr>
		<td width="50%"><?=GetMessage("BITRIX_XDEBUG_OBQEM_BUFERA_CTENIA")?></td>
		<td width="50%" valign=top><input name="buffer_size" value="<?=$buffer_size?>" size=4></td>
	</tr>
<?$tabControl->Buttons();?>

<input type="submit" class="adm-btn-save" name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
<input type="submit" name="Reset" value="<?=GetMessage("BITRIX_XDEBUG_PO_UMOLCANIU")?>" onclick="if(!confirm('<?=GetMessage("BITRIX_XDEBUG_SBROSITQ_VSE_NASTROY")?>'))return false;">
<?$tabControl->End();?>
</form>
