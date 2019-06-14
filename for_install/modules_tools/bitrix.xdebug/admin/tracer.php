<?
$module_id = 'bitrix.xdebug';
define('ADMIN_MODULE_NAME', $module_id);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
if(!$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if (!CModule::IncludeModule('bitrix.xdebug'))
	ShowErrAndDie('bitrix.xdebug module is not installed');

$file = $_REQUEST['file'];
$line = intval($_REQUEST['line']);
$trace_output_dir_default = $trace_output_dir = COption::GetOptionString($module_id, 'xdebug.trace_output_dir', ini_get('xdebug.trace_output_dir'));
if ($_REQUEST['trace_output_dir'] && is_dir($_REQUEST['trace_output_dir']))
	$trace_output_dir = realpath($_REQUEST['trace_output_dir']);

if (!$trace = trim($_REQUEST['trace']))
	LocalRedirect('/bitrix/admin/bitrix.xdebug_browse.php');

if ($_REQUEST['mode'] == 'src')
{
	$pos = intval($_REQUEST['pos']);
	if (!file_exists($file) || false === $str = file_get_contents($file))
	{ # FILE DOES NOT EXIST
	?><html><body>
		<style>
			body {background-color:#f0f0f0;}
			.currentLine {background-color:#ffff66;}
		</style>
		<?
		$XTR = new CXDTracer;
		$XTR->buffer_size = 10000;
		
		if ($XTR->Start($trace_output_dir.'/'.$trace, $pos))
		{
			fgets($XTR->f);
			$i = $num = 0;
			echo '<pre>';
			while(false !== $l = fgets($XTR->f))
			{
				$num++;
				$fpos = ftell($XTR->f);
				if ($fpos > $pos)
					$i++;
				if ($i > 10)
					break;
				if ($fpos == $pos)
				{
					$cur_num = $num;
					echo '<span class="currentLine">'.htmlspecialcharsbx($l).'</span>';
				}
				else
					echo htmlspecialcharsbx($l);
			}
			echo '</pre>';

			fclose($XTR->f);
			?>
			<script>
				function HighlightLine()
				{
					window.scroll(0, '<?=$cur_num?>' * document.body.scrollHeight / '<?=$num?>' - (window.innerHeight / 2));
					console.log('<?=$cur_num?>' * document.body.scrollHeight / '<?=$num?>' - (window.innerHeight / 2));
					return false;
				}
				
				if (ob = parent.document.getElementById('filePath'))
				{
					ob.innerHTML += '<span style="color:#b00"> - <?=GetMessageJS("BITRIX_XDEBUG_FAYL_NE_NAYDEN1")?></span>';
				}

				window.onload = HighlightLine();
			</script>
			<?
			exit();
		}
		ShowErrAndDie("Can't open file: ".htmlspecialcharsbx($file));
	}
	########## FILE EXISTS ##########

	$arHtml = explode("<br />", highlight_string(CBitrixXdebug::FixCharset($str),true));
	$cnt_lines = count($arHtml);
	?><html><body>
		<style>
			body {background-color:#f0f0f0;}
			.oldLine {background-color:#eeee99;}
			.currentLine {background-color:#ffff66;}
			.selectedLine {background-color:#ff6666;}
		</style>
		<script>
			var line = <?=$line?>;
			var selected_line = 0;
			function HighlightLine(line_num, new_file, file)
			{
				if (new_file && new_file != file) // reload file
					return false;

				if (ob = document.getElementById('line_' + line))
					ob.setAttribute("class", "oldLine");
				if (ob = document.getElementById('code_line_' + line))
					ob.setAttribute("class", "oldLine");
				line = line_num;
				if (ob = document.getElementById('line_' + line))
					ob.setAttribute("class", "currentLine");
				if (ob = document.getElementById('code_line_' + line))
					ob.setAttribute("class", "currentLine");
				window.scroll(0, line_num * document.body.scrollHeight / <?=$cnt_lines?> - (window.innerHeight / 2));
				return true;
			}

			function FindLine(line_num)
			{
				file = "<?=htmlspecialcharsbx($file)?>";
				if (ob = window.parent.document.getElementById('text_search_file'))
				{
					ob.value = file + ":" + line_num;
					if (ob.selectionStart)
					{
						ob.selectionStart = ob.value.length;
						ob.selectionEnd = ob.value.length;
					}
					
					if (ob = document.getElementById('line_' + selected_line))
						ob.setAttribute("class", "");

					selected_line = line_num;

					if (ob = document.getElementById('line_' + selected_line))
						ob.setAttribute("class", "selectedLine");
				}

			}
		</script>
	<?
	echo '<table width=100% cellpadding=0 cellspacing=0><tr><td valign=top align=right><code style="color:#aa5500;cursor:pointer">';
	for ($i = 1; $i <= $cnt_lines; $i++)
		echo '<span id="line_'.$i.'" onclick="FindLine('.$i.')">'.$i.'&nbsp;</span>'."<br>";
	echo '</td><td width=100% valign=top>';
		for($i = 1; $i<=$cnt_lines; $i++)
			echo '<div id="code_line_'.$i.'"><nobr>'.$arHtml[$i - 1].'&nbsp;</nobr></div>';
	echo '</td></tr></table>';

	if ($line)
		echo '<script>window.onload = HighlightLine('.$line.');</script>';
	exit();
}
elseif ($_REQUEST['mode'] == 'ajax')
{
	$XTR = new CXDTracer;
	$XTR->buffer_size = COption::GetOptionString($module_id, 'buffer_size', 100) * 1024;

	if (!$XTR->Start($trace_output_dir.'/'.$trace, intval($_REQUEST['seek'])))
		ShowErrAndDie("Can't open file: ".$trace);
	
	$XTR->search_depth = intval($_REQUEST['search_depth']);
	$XTR->search_result = $_REQUEST['search_result'] == 1;
	$XTR->text_search_file = $_REQUEST['text_search_file'];
	$XTR->text_search_func = $_REQUEST['text_search_func'];
	$XTR->bool_search_by_func = intval($_REQUEST['bool_search_by_func']);
	$XTR->text_search_var = $_REQUEST['text_search_var'];
	
	$XTR->Scan();
	
	?>/*<script>*/

	need_reload = false;
	new_file="<?=htmlspecialcharsbx($XTR->file)?>";
	new_line=<?=intval($XTR->line)?>;
	seek=<?=intval($XTR->pos)?>;
	depth=<?=$XTR->depth?>;

	if (src_iframe = document.getElementById('src_iframe'))
	{
		if (ob = document.getElementById('fileNav'))
		{
			<?
				$filesize = filesize($trace_output_dir.'/'.$trace);
				$p = round($XTR->pos * 100/$filesize)
			?>
			filesize = <?=$filesize?>;
			p = <?=$p?>;
			html = '<div><?=GetMessageJS("BITRIX_XDEBUG_POZICIA_V_TREYSE")?><?=$XTR->pos?> / ' + filesize + ' (<?=CBitrixXdebug::HumanSize($XTR->pos)?> / <?=CBitrixXdebug::HumanSize($filesize)?>) - ' + p + '%</div>';
			for (i = 1; i<=100; i++)
				html += '<div class="' + (i <= p ? 'view_in' : 'view_out') + '" onclick="GoToPercent(' + i + ')"></div>';

			html += '<div style="clear:left"></div>';

			<?
				$time = 0;
				for($i = 1; $i<=$XTR->depth; $i++)
				{
					if ($XRec0 = $XTR->arStack[$i])
						$time = max($time, round($XRec0->time, 2));
				}

				if ($time)
				{
					if ($trace_time = round(CXDTracer::GetTraceTime($trace_output_dir.'/'.$trace), 2))
						$p = round($time * 100 / $trace_time);
					else
						$p = 0;
				}
				else
				{
					$trace_time = 1;
					$p = 0;
				}
			?>
			trace_time = '<?=$trace_time?>';
			p = <?=$p?>;
			html += '<div><?=GetMessageJS("BITRIX_XDEBUG_VREMA")?><?=$time?> / ' + trace_time + ' - ' + p + '%</div>';
			html += '<div class="time_in" style="width:<?=$p*8?>px"></div>';
			html += '<div class="time_out" style="width:<?=(100-$p)*8?>px"></div>';
			html += '<div style="clear:left"></div>';
			ob.innerHTML = html;
		}

		if (ob = document.getElementById('filePath'))
		{
			ob.innerHTML = '<span class=filePath>' + new_file + '</span>:<span class=lineNum>' + new_line + '</span>';
		}

		if (ob = document.getElementById('callStack'))
		{
			ob.innerHTML = "<table><? 
				for($i = 1; $i<=$XTR->depth; $i++)
				{
					$strFunc = addslashes('<span style="cursor:pointer" onclick="window.open(\'/bitrix/admin/settings.php?mid=bitrix.xdebug\')" title="'.GetMessageJS("BITRIX_XDEBUG_DLA_OTOBRAJENIA_FUNK").'">???</span>');
					$strResult = '';

					if ($XRec0 = $XTR->arStack[$i])
					{
						$strFunc = '<span style=\"cursor:pointer\" onclick=\"GoToPosition('.$XRec0->pos.')\">'.$XRec0->GetHTML().'</span>';
						if (($XDResult = $XRec0->XDResult) && null !== $html = $XDResult->GetHTML())
						{
							$strResult = '<nobr>'.str_repeat('&nbsp;',$i).' &gt;=&gt; <b>'.$html.'</b></nobr>';
						}
					}
					echo '<tr valign=\"top\">'.
						'<td style=\"color:#ccc\">'.$i.'</td>'.
						'<td style=\"color:#9cc\">'.($XRec0->time > 0 ? round($XRec0->time,2) : '').'</td>'.
						'<td style=\"color:#9c6\">'.($XRec0->mem > 0 ? CBitrixXdebug::HumanSize($XRec0->mem) : '').'</td>'.
						'<td><nobr>'.str_repeat('&nbsp;',$i).' &gt; '.CBitrixXdebug::FixCharset($strFunc)."</nobr><br>".CBitrixXdebug::FixCharset($strResult).'</td>'.
					'</tr>';
				}
				?></table>";
			ob.scrollTop = ob.scrollHeight;
			document.title = '<?=GetMessageJS("BITRIX_XDEBUG_OTLADKA").' - '.htmlspecialcharsbx(basename($XTR->file)).' - '.$XRec0->text_func_name?>';
		}

		if (!src_iframe.contentWindow.HighlightLine || !src_iframe.contentWindow.HighlightLine(new_line, new_file, file))
		{
			src_iframe.src = '?mode=src&file=' + new_file + '&line=' + new_line + '&pos=<?=$XTR->pos?>&trace_output_dir=<?=urlencode($trace_output_dir)?>&trace=<?=urlencode($trace)?>';
			file = new_file;
			line = new_line;
		}
	}
	/*</script>*/<?
	exit();
}


$APPLICATION->SetTitle(GetMessageJS("BITRIX_XDEBUG_OTLADKA").': '.$trace.' ('.CBitrixXdebug::HumanSize(filesize($trace_output_dir.'/'.$trace)).') '.GetMessageJS("BITRIX_XDEBUG_OT").date('d.m.Y H:i:s', filemtime($trace_output_dir.'/'.$trace)));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (!file_exists($trace_output_dir.'/'.$trace))
	ShowErrAndDie(GetMessageJS("BITRIX_XDEBUG_FAYL_NE_NAYDEN").htmlspecialcharsbx($trace_output_dir.'/'.$trace));
?>
<style>
	.filePath {color:#339; font-weight:bold;}
	.lineNum {color:#a000a0;}
	.funcName {color:#3c9696; font-weight:bold;}
	.varName {color:#3c9696; font-weight:bold}
	.varS{color:#aa5500; font-weight:bold}
	.varVal {}

	.view_in
	{
		float:left;
		width:8px;
		height:18px;
		background-color:#559;
		cursor:pointer;
	}
	.view_out
	{
		float:left;
		width:8px;
		height:18px;
		background-color:#ccc;
		cursor:pointer;
	}
	.time_in
	{
		float:left;
		height:8px;
		background-color:#747481;
	}
	.time_out
	{
		float:left;
		height:8px;
		background-color:#ccc;
	}
</style>
<script>
	var trace = '<?=urlencode($trace)?>';
	var seek = 0;
	var filesize;
	var depth = 0;
	var file = '<?=urlencode($file)?>';
	var line = <?=$line?>;

	function MakeStep(search_depth, text_search_file, text_search_func, text_search_var)
	{
		req = '?mode=ajax&trace=' + trace + '<?=$trace_output_dir_default == $trace_output_dir ? '' : "&trace_output_dir=".urlencode($trace_output_dir)?>&seek=' + seek + '&search_depth=' + search_depth;

		if ((ob = document.getElementById('search_result')) && ob.checked)
		{
			req += '&search_result=1';
			search_result = 1;
		}
		else	
			search_result = 0;

		if (text_search_file)
			req += '&text_search_file=' + encodeURIComponent(text_search_file);
		if (text_search_func)
			req += '&text_search_func=' + encodeURIComponent(text_search_func) + '&bool_search_by_func=' + bool_search_by_func;
		if (text_search_var)
			req += '&text_search_var=' + encodeURIComponent(text_search_var);

		AjaxSend(req);
		document.location = '#' + search_depth + '/' + seek + '/' + search_result;
	}

	function SearchText()
	{
		MakeStep(0, document.getElementById('text_search_file').value, document.getElementById('text_search_func').value, document.getElementById('text_search_var').value);
	}

	function GoToPosition(s)
	{
		seek = s;
		MakeStep(0);
	}

	function GoToPercent(p)
	{
		seek = Math.floor(filesize * (p-1) / 100);
		MakeStep(0);
	}

	var load = false;
	function AjaxSend(url)
	{
		load = true;
		window.setTimeout(function() {if (load) ShowWaitWindow();}, 500);
		xml = new XMLHttpRequest();
		xml.onreadystatechange = function ()
		{
			if (xml.readyState == 4 || xml.readyState == "complete")
			{
				try {
					eval(xml.responseText);
				} catch (e) {
					console.log(e);
					console.log(xml.responseText);
				}
				load = false;
				CloseWaitWindow();
			}
		}

		xml.open("GET", url, true);
		xml.send("");
	}

	window.onload = function () {
		var anc = window.location.hash.replace("#","");
		if (anc)
		{
			s = anc.split('/');
			search_depth = s[0];
			seek = s[1];
			search_result = s[2];

			if (ob = document.getElementById('search_result'))
				ob.checked = search_result == 1;
		}
		else
			search_depth = 0;

		if (ob = document.getElementById('src_iframe'))
		{
			ob.height =  window.innerHeight * 0.5;
			if ((width = ob.clientWidth) && (ob = BX('callStack')))
				ob.style.width = width + 'px';
		}

		MakeStep(search_depth);
	}

	var bool_search_by_func = 1;
	function SwitchToVar()
	{
		BX('block_text_search_func').style.display = 'none';
		BX('block_text_search_var').style.display = '';
		bool_search_by_func = 0;

	}

	function SwitchToFunc()
	{
		BX('block_text_search_func').style.display = '';
		BX('block_text_search_var').style.display = 'none';
		bool_search_by_func = 1;
	}
</script>

<?
	$aMenu = array(
		array(
			"TEXT"	=> GetMessage("BITRIX_XDEBUG_OBZOR"),
			"LINK"	=> '/bitrix/admin/bitrix.xdebug_browse.php?trace_output_dir='.urlencode($trace_output_dir).'&lang='.LANGUAGE_ID,
			"ICON"	=> "btn_list"
		),
		array(
			"HTML"	=> '<label><input type=checkbox id="search_result"> '.GetMessage("BITRIX_XDEBUG_OTOBRAJATQ_REZULQTAT").'</label>',
		),
		array(
			"TEXT"	=> GetMessage("BITRIX_XDEBUG_NACALO"),
			"ONCLICK" => 'GoToPosition(0)',
		),
		array(
			"TEXT"	=> GetMessage("BITRIX_XDEBUG_SAG"),
			"ONCLICK" => 'MakeStep(-1)',
		),
		array(
			"HTML"	=> '<a href="javascript:void(0)" onclick="MakeStep(0)" class="adm-btn-green adm-btn">'.GetMessage("BITRIX_XDEBUG_SAG1").'</a>',
		),
		array(
			"TEXT"	=> GetMessage("BITRIX_XDEBUG_SAG2"),
			"ONCLICK" => 'MakeStep(depth)',
		),
		array(
			"TEXT"	=> GetMessage("BITRIX_XDEBUG_SAG3"),
			"ONCLICK" => 'MakeStep(depth - 1)',
		),
	);
	$context = new CAdminContextMenu($aMenu);
	$context->Show();
?>
<?=GetMessage("BITRIX_XDEBUG_POISK_FAYLA")?><input size=20 title="<?=GetMessage("BITRIX_XDEBUG_POISK_PO_PODSTROKE_P")?>" id="text_search_file" onkeydown="if(event.keyCode == 13)SearchText()"> 
	<span id="block_text_search_func"><a href="javascript:SwitchToVar();"><?=GetMessage("BITRIX_XDEBUG_FUNKCII")?></a></span>
	<span id="block_text_search_var" style="display:none"><a href="javascript:SwitchToFunc();"><?=GetMessage("BITRIX_XDEBUG_PEREMENNOY")?></a></span>
	<input size=10 title="<?=GetMessage("BITRIX_XDEBUG_POISK_S_UCETOM_REGIS")?>" id="text_search_func" onkeydown="if(event.keyCode == 13)SearchText()">
	<?=GetMessage("BITRIX_XDEBUG_ZNACENIA")?><input size=10 title="<?=GetMessage("BITRIX_XDEBUG_POISK_PO_ZNACENIU_PE")?>" id="text_search_var" onkeydown="if(event.keyCode == 13)SearchText()">
	<input type="button" value="<?=GetMessage("BITRIX_XDEBUG_DALEE")?>" onclick="SearchText()">
<div id="fileNav" style="margin:2px"></div>
<div id="callStack" style="border:1px solid #ccc;background-color:#fff;height:150px;overflow:auto"></div>
<div id="filePath" style="background-color:#ccc;padding:2px"></div>
<iframe id="src_iframe" style="border:1px solid #ccc;width:100%;" src="">
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
