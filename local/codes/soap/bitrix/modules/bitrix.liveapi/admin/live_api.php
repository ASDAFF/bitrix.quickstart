<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if(!$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
if ($_REQUEST['file'])
	$APPLICATION->SetTitle(GetMessage("BITRIX_LIVEAPI_ISHODNYY_KOD_FAYLA"));
elseif ($_REQUEST['scan'])
	$APPLICATION->SetTitle(GetMessage("BITRIX_LIVEAPI_SKANIROVANIE"));
elseif ($_REQUEST['module'])
	$APPLICATION->SetTitle('API '.GetMessage("BITRIX_LIVEAPI_MODULA").htmlspecialchars($_REQUEST['module']));
elseif ($_REQUEST['search'])
	$APPLICATION->SetTitle(GetMessage("BITRIX_LIVEAPI_POISK_PO"));
elseif ($_REQUEST['show_diff'])
	$APPLICATION->SetTitle(GetMessage("BITRIX_LIVEAPI_IZMENENIA_API_POSLE"));
else
	$APPLICATION->SetTitle(GetMessage("BITRIX_LIVEAPI_AKTUALQNOE_OPISANIE"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?><?
if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');



define('DATA_FILE',$_SERVER["DOCUMENT_ROOT"].'/bitrix/managed_cache/live_api.data');
define('DATA_FILE_OLD', DATA_FILE.'_old');

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

	$str = Beautiful($str);
	if (defined('BX_UTF') && BX_UTF)
		$str = $APPLICATION->ConvertCharSet($str, 'cp1251', 'utf8');
	echo $str;
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
	die();
}
elseif ($_REQUEST['scan'])
{
	$dbtype = 'mysql';
	$path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules';
	if (!($dir = opendir($path)))
		die('Cannot read '.$path);

	$gotIt = false;
	while(false !== ($file = readdir($dir)))
	{
		if ($file == '.' || $file == '..' || !is_dir($path.'/'.$file))
			continue;

		$next_file = $file;
		if ($gotIt)
			break;

		$gotIt = !$_REQUEST['next_file'] || $_REQUEST['next_file'] == $file;
		$last_file = $file;
	}
	closedir($dir);

	if ($gotIt)
	{
		CheckDirPath(dirname(DATA_FILE));
		if (!$_REQUEST['next_file'] && file_exists(DATA_FILE))
			rename(DATA_FILE, DATA_FILE_OLD);
		$ar = ScanModule($last_file);
		$f = fopen(DATA_FILE,'ab');
		fwrite($f, '<'.'? $DATA[\''.$last_file.'\'] = \''.str_replace("'","\'",str_replace('\\','\\\\',serialize($ar))).'\'; ?'.'>'."\n");
		fclose($f);

		if ($next_file != $last_file)
			die('<div>'.GetMessage("BITRIX_LIVEAPI_SKANIROVANIE_MODULA") . $last_file . '</div> <script>document.location="?scan=Y&next_file='.htmlspecialchars($next_file).'";</script>');
		else
			echo '<div>'.GetMessage("BITRIX_LIVEAPI_SKANIROVANIE_ZAVERSE").'</div>';
	}
	else
		die('Logical error');
}
else
{
	$bNeedToRescan = true;
	if (file_exists(DATA_FILE))
	{
		$utime = COption::GetOptionString("main", "update_system_update", 0);
		$bNeedToRescan = MakeTimeStamp($utime) > filemtime(DATA_FILE);
	}
	if ($bNeedToRescan)
		echo '<div style="color:red">'.GetMessage("BITRIX_LIVEAPI_BYLI_USTANOVLENY_OBN").'.</div>';
}

echo '<div><input type=button value="'.GetMessage("BITRIX_LIVEAPI_SKANIROVATQ_MODULI").'" onclick="document.location=\'?scan=Y\'"></div>';

if (file_exists(DATA_FILE))
{
	echo '<div id="live_api">';
	echo '<form method=GET>'.GetMessage("BITRIX_LIVEAPI_POISK_PO_VSEM_MODULA").': <input name=search value="'.htmlspecialchars($_REQUEST['search']).'" size=30> <input type=submit value='.GetMessage("BITRIX_LIVEAPI_POISK").'></form>';

	include(DATA_FILE);
	$arModules = array_keys($DATA);
	sort($arModules);
	echo GetMessage("BITRIX_LIVEAPI_VYBERITE_MODULQ").': <select onchange="document.location=\'?module=\'+this.value"><option></option>';
	foreach($arModules as $k)
		echo '<option value="'.$k.'" '.($k== $_REQUEST['module'] ? 'selected' : '').'>'.$k.'</option>';
	echo '</select>';


	if (isset($DATA[$_REQUEST['module']]))
	{
		$arClasses = array();
		list($arRes,$arEvt,$arConst) = unserialize($DATA[$_REQUEST['module']]);
		$ar = array_keys($arRes);
		foreach($ar as $str)
		{
			if ($class = ($p = strpos($str,'::')) ? substr($str,0,$p) : false)
				$arClasses[$class] = 1;
		}

		if (count($arClasses))
		{
			echo ' '.GetMessage("BITRIX_LIVEAPI_KLASS").': <select onchange="document.location=\'?module='.htmlspecialchars($_REQUEST['module']).'&class=\'+this.value"><option></option>';
			foreach($arClasses as $k=>$v)
				echo '<option value="'.$k.'" '.($k== $_REQUEST['class'] ? 'selected' : '').'>'.$k.'</option>';
			echo '</select>';
		}
	}

	if ($_REQUEST['search'])
	{
		foreach($DATA as $module=>$sar)
		{
			$ar = unserialize($sar);
			list($arRes,$arEvt,$arConst) = $ar;
			$bFound = false;

			$header = 
				'<h2><a href="?module='.$module.'">'.$module.'</a></h2>'.
				'<table border=1 cellpadding=4 cellspacing=0>'.
				'<tr align=center bgcolor="#CCCCCC">'.
					"<td><b>".GetMessage("BITRIX_LIVEAPI_GDE_NAYDENO")."</td>".
					"<td><b>".GetMessage("BITRIX_LIVEAPI_METOD")."</td>".
				'</tr>';

			foreach($arRes as $k=>$v)
			{
				if (stripos($k,$_REQUEST['search']) !== false)
				{
					if (!$bFound)
						echo $header;

					echo '<tr>'.
						'<td class="method">'.GetMessage("BITRIX_LIVEAPI_METOD1").'</td>'.
						'<td class="code">'.colorize($k,$v,false,$module).'</td>'.
					'</tr>';

					$bFound = true;
				}
			}
			foreach($arEvt as $evt => $func)
			{
				if (stripos($evt,$_REQUEST['search']) !== false)
				{
					if (!$bFound)
						echo $header;

					$ar = $arRes[$func];
					$link = "?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$evt.'#'.$evt;
					echo 
					'<tr>'.
						'<td class="event">'.GetMessage("BITRIX_LIVEAPI_SOBYTIE1").'</td>'.
						"<td class=code><a href='$link' target=_blank>$evt</a> ($func)</td>".
					'</tr>';

					$bFound = true;
				}
			}
			foreach($arConst as $const => $func)
			{
				if (stripos($const,$_REQUEST['search']) !== false)
				{
					if (!$bFound)
						echo $header;

					$ar = $arRes[$func];
					$link = "?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$const.'#'.$const;
					echo 
					'<tr>'.
						'<td class="const">'.GetMessage("BITRIX_LIVEAPI_KONSTANTA1").'</td>'.
						"<td class=code><a href='$link' target=_blank>$const</a> ($func)</td>".
					'</tr>';

					$bFound = true;
				}
			}
			if ($bFound)
				echo '</table>';
		}
	}
	elseif (isset($DATA[$_REQUEST['module']]))
	{
		Show($_REQUEST['module'],unserialize($DATA[$_REQUEST['module']]),$_REQUEST['class']);
	}
	elseif ($_REQUEST['show_diff'])
	{
		$DATA_OLD = ReadOld();
		foreach($DATA as $module => $v)
		{
			if ($v != $DATA_OLD[$module])
			{
				echo '<h2><a href="?module='.$module.'">'.$module.'</a></h2>';
				$ar_new = unserialize($v);
				$ar_old = unserialize($DATA_OLD[$module]);

				if (count($tmp = array_diff_assoc($ar_new[0], $ar_old[0])))
				{
					echo '<table border=1 cellpadding=4 cellspacing=0>';

					foreach($tmp as $k=>$v)
						echo '<tr>'.
							'<td class="method">'.GetMessage("BITRIX_LIVEAPI_METOD1").'</td>'.
							'<td class="code">'.colorize($k,$v,false,$module).'</td>'.
						'</tr>';
					echo '</table>';
				}

				if (count($tmp = array_diff_assoc($ar_new[1], $ar_old[1])))
				{
					echo '<table border=1 cellpadding=4 cellspacing=0>';

					foreach($tmp as $const=>$func)
					{
						$ar = $ar_new[0][$func];
						$link = "?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$const.'#'.$const;
						echo 
						'<tr>'.
							'<td class="const">'.GetMessage("BITRIX_LIVEAPI_KONSTANTA1").'</td>'.
							"<td class=code><a href='$link' target=_blank>$const</a> ($func)</td>".
						'</tr>';
					}
					echo '</table>';
				}

				if (count($tmp = array_diff_assoc($ar_new[2], $ar_old[2])))
				{
					echo '<table border=1 cellpadding=4 cellspacing=0>';

					foreach($tmp as $evt=>$func)
					{
						$ar = $ar_new[0][$func];
						$link = "?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$evt.'#'.$evt;
						echo 
						'<tr>'.
							'<td class="event">'.GetMessage("BITRIX_LIVEAPI_SOBYTIE1").'</td>'.
							"<td class=code><a href='$link' target=_blank>$evt</a> ($func)</td>".
						'</tr>';
					}
					echo '</table>';
				}
			}
		}
	}
	elseif (file_exists(DATA_FILE_OLD))
	{
		echo '<div>'.GetMessage("BITRIX_LIVEAPI_SOHRANENY_DANNYE_PRO").'. <a href="?show_diff=Y">'.GetMessage("BITRIX_LIVEAPI_POKAZATQ_IZMENENIA").'</a></div>';
	}
	echo '</div>';
}


#########################################
function ScanModule($module)
{
	global $dbtype;
	$arRes = array();
	$arEvt = array();
	$arConst = array();

	$path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module;
	if (!($dir = opendir($path)))
		die('Cannot read '.$path);

	while(false !== ($file = readdir($dir)))
	{
		if ($file == '.' || $file == '..' || is_dir($path.'/'.$file) || end(explode('.',$file)) != 'php')
			continue;

		if (!is_array($ar = ParseFile($path.'/'.$file, $arEvt, $arConst)))
			continue;

		$arRes = array_merge($arRes, $ar);
	}
	closedir($dir);

	$arScanDir = array('general',$dbtype,'include','interface','public');
	foreach($arScanDir as $folder)
	{
		$path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module.'/classes/'.$folder;

		if (!file_exists($path))
			$path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module.'/'.$folder;

		if (!file_exists($path))
			continue;

		if (!($dir = opendir($path)))
			die('Cannot read '.$path);

		while(false !== ($file = readdir($dir)))
		{
			if ($file == '.' || $file == '..' || is_dir($path.'/'.$file) || end(explode('.',$file)) != 'php')
				continue;

			if (!is_array($ar = ParseFile($path.'/'.$file, $arEvt, $arConst)))
				continue;

			$arRes = array_merge($arRes, $ar);
		}
		closedir($dir);
	}

	ksort($arRes);
	ksort($arEvt);
	ksort($arConst);
	return array($arRes,$arEvt,$arConst);
}
	
function Show($module, $ar, $class)
{
	list($arRes,$arEvt,$arConst) = $ar;
	if (!$class && count($arEvt))
	{
		echo '<h2>'.GetMessage("BITRIX_LIVEAPI_SOBYTIA_MODULA").htmlspecialchars($module).'</h2>';
		echo '<table border=1 cellpadding=4 cellspacing=0>';
			echo 
			'<tr align=center bgcolor="#CCCCCC">'.
				"<td><b>".GetMessage("BITRIX_LIVEAPI_SOBYTIE")."</td>".
				"<td><b>".GetMessage("BITRIX_LIVEAPI_VYZYVAETSA")."</td>".
			'</tr>';

		foreach($arEvt as $evt => $func)
		{
			$ar = $arRes[$func];
			$link = "?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$evt.'#'.$evt;
			echo 
			'<tr>'.
				"<td valign=top class=code><a href='$link' target=_blank>$evt</td>".
				"<td valign=top class=code>$func</td>".
			'</tr>';
		}
		echo '</table>';
	}

	if (!$class && count($arConst))
	{
		echo '<h2>'.GetMessage("BITRIX_LIVEAPI_KONSTANTY_MODULA").htmlspecialchars($module).'</h2>';
		echo '<table border=1 cellpadding=4 cellspacing=0>';
			echo 
			'<tr align=center bgcolor="#CCCCCC">'.
				"<td><b>".GetMessage("BITRIX_LIVEAPI_KONSTANTA")."</td>".
				"<td><b>".GetMessage("BITRIX_LIVEAPI_PROVERAETSA")."</td>".
			'</tr>';

		foreach($arConst as $const => $func)
		{
			$ar = $arRes[$func];
			$link = "?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$const.'#'.$const;
			echo 
			'<tr>'.
				"<td valign=top class=code><a href='$link' target=_blank>$const</td>".
				"<td valign=top class=code>$func</td>".
			'</tr>';
		}
		echo '</table>';
	}

	if (count($arRes))
	{

		echo '<h2>'.GetMessage("BITRIX_LIVEAPI_SPISOK_FUNKCIY_I_MET").htmlspecialchars($module).'</h2>';
		echo '<table border=1 cellpadding=4 cellspacing=0>';
			echo 
			'<tr align=center bgcolor="#CCCCCC">'.
				"<td><b>".GetMessage("BITRIX_LIVEAPI_METOD")."</td>".
			'</tr>';

		foreach($arRes as $func => $ar)
		{
			if ($str = colorize($func,$ar,$class, $module))
				echo 
				'<tr>'.
					"<td valign=top class=code>".$str."</td>".
				'</tr>';
		}
		echo '</table>';
	}
}

function colorize($func,$ar,$class = false, $module = '')
{
	$link = "?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]";
	if ($c = strpos($func, "::"))
	{
		if ($class && substr($func,0,$c) != $class)
			return;
		$func = '<a href="?module='.$module.'&class='.substr($func,0,$c).'" class=class>'.substr($func,0,$c).'</span>::<a href="'.$link.'" target=_blank><span class=method>'.substr($func,$c+2).'</span></a>';
	}
	else
	{
		if ($class)
			return;
		$func = '<a href="'.$link.'" target=_blank><span class=method>'.$func.'</span></a>';
	}

	$args = preg_replace('#(\$[a-z0-9_]+)#i','<span class=var>\\1</span>',htmlspecialchars($ar['ARGS']));
	return $func.'('.$args.')';
}

function ParseFile($file, &$arEvt, &$arConst)
{
	$f = fopen($file, 'rb');
	if ($f === false)
		return false;
	$arRes = array();

	$len = strlen($_SERVER['DOCUMENT_ROOT']);
	$i = 0;
	$offset = 0;
	$curClass = '';
	$curFunc = '';
	$js = false;
	while(false !== ($l = fgets($f)))
	{
		$i++;
		if (preg_match('#<script>#i',$l))
			$js = true;
		elseif (preg_match('#<script language#i',$l))
			$js = true;
		elseif (preg_match('#<script type#i',$l))
			$js = true;
		if (preg_match('#</script>#i',$l))
			$js = false;

		if (!$js)
		{
			if (preg_match('#^[a-z\s]*(class|interface) ([a-z0-9_]+)#i', $l, $regs))
			{
				$curClass = $regs[2];
				$curClass = preg_replace('#^CAll#i','C',$curClass);
				$curClass = preg_replace('#_all$#i','',$curClass);
				$open = $close = 0;
			}
			elseif (preg_match('#^([a-z\s]*)function ([a-z0-9_]+) ?\((.*)\)#i', $l, $regs))
			{
				$curFunc = $func = ($curClass ? $curClass.'::' : '').$regs[2];
				$args = $regs[3];
				$arRes[$func] = array(
					'FILE' => substr($file,$len),
					'LINE' => $i, 
					'OFFSET' => $offset,
					'ARGS' => $args,
				);
			}
			elseif (preg_match('#^([a-z 	]*)function ([a-z0-9_]+) ?\(#i', $l, $regs))
			{
				$curFunc = $func = ($curClass ? $curClass.'::' : '').$regs[2];
				$args = 'N/A';
				$arRes[$func] = array(
					'FILE' => substr($file,$len),
					'LINE' => $i, 
					'OFFSET' => $offset,
					'ARGS' => $args,
				);
			}
			elseif (preg_match('#GetModuleEvents\([^,]+,["\' ]*([a-z0-9_]+)#i', $l, $regs))
			{
				$event = $regs[1];
				$arEvt[$event] = $curFunc;
			}
			elseif (preg_match('#ExecuteEvents\([\'"]?([a-z0-9_]+)#i', $l, $regs))
			{
				$event = $regs[1];
				$arEvt[$event] = $curFunc;
			}

			if ($curFunc && preg_match('#defined\(["\']([a-z_]+)["\']\)#i', $l, $regs))
				$arConst[$regs[1]] = $curFunc;

			if ($curClass)
			{
				$open += substr_count($l, '{');
				$close += substr_count($l, '}');
			}

			if ($open > 0 && $close >= $open)
				$curClass = '';
		}
		$offset += strlen($l);
	}
	fclose($f);

	/*
	if (strpos($file, 'bitrix/modules/main/classes/general/main.php') !== false)
	{
		echo $file;
		echo '<pre>';print_r($arEvt);echo '</pre>';
		die();
	}
	*/

	return $arRes;
}

function Beautiful($html)
{
	global $raw;
	$raw = $html;
	$html = highlight_string("<?"."php \n//	$_REQUEST[name]\n//	$_REQUEST[file]:$_REQUEST[line]\n\n".$html,true);

	if (file_exists($file = DATA_FILE))
	{
		$class = ($p = strpos($_REQUEST['name'],'::')) ? substr($_REQUEST['name'],0,$p) : false;
		include($file);
		foreach($DATA as $module=>$ar)
		{
			list($arRes,$arEvt) = unserialize($ar);
			if (is_array($arRes))
			{
				foreach($arRes as $k=>$v)
				{
					if ($k == $_REQUEST['name'])
						continue;

					if (strpos($k, '::'))
						$html = GetLink($k, $v, $html);

					if ($class)
						$html = GetLink($k, $v, $html, $class.'::','$this->');

					if ($module == 'main')
					{
						$html = GetLink($k, $v, $html,'CUser::', '$USER->');
						$html = GetLink($k, $v, $html,'CMain::', '$APPLICATION->');
						$html = GetLink($k, $v, $html,'CDatabase::', '$DB->');
					}

					$curClass = ($p0 = strpos($k,'::')) ? substr($k,0,$p0) : false;
					if ($curClass && $lastClass != $curClass)
					{
						$lastClass = $curClass;
						$html = preg_replace('#(new&nbsp;</span><span[^>]*>)'.$curClass.'#i',"$1".'<a href="?module='.$module.'&class='.htmlspecialchars($curClass).'">'.$curClass.'</a>',$html);
					}
				}
#				foreach($arRes as $k=>$v)
#					if (!strpos($k, '::'))
#						$html = GetLink($k, $v, $html);
			}
		}
	}

	if ($_REQUEST['highlight'])
		$html = str_replace($_REQUEST['highlight'],'<a name="'.htmlspecialchars($_REQUEST['highlight']).'"></a><span style="background:#FFFF00">'.$_REQUEST['highlight'].'</span>',$html);

	if ($class)
	{
		$file = str_replace('\\','/',$_REQUEST['file']);
		if (preg_match('#^/bitrix/modules/([^/]+)/#',$file,$regs))
		{
			$module = $regs[1];
			$html = str_replace($_REQUEST['name'],'<a href="?module='.$module.'&class='.$class.'">'.$class.'</a>'.substr($_REQUEST['name'],$p),$html);
		}
		$html = str_ireplace('public&nbsp;','<span style="color:#933;font-weight:bold">public</span>&nbsp;',$html);
		$html = str_ireplace('private&nbsp;','<span style="color:#933;font-weight:bold">private</span>&nbsp;',$html);
		$html = str_ireplace('protected&nbsp;','<span style="color:#933;font-weight:bold">protected</span>&nbsp;',$html);
		$html = str_ireplace('static&nbsp;','<span style="color:#333;font-weight:bold">static</span>&nbsp;',$html);
	}

	return $html;
}

function GetLink($code, $v, $html, $from = false, $to = false)
{
	global $raw;

	$s_code = $code;
	if ($from)
	{
		if (false === strpos($code,$from))
			return $html;
		$s_code = str_replace($from,$to, $code);
	}
	if (false === strpos($raw,$s_code))
		return $html;

	$p_code = str_replace('::','</span><span[^>]+>::</span><span[^>]+>',$s_code);
	$p_code = str_replace('->','</span><span[^>]+>-&gt;</span><span[^>]+>',$p_code);
	$p_code = str_replace('$','\$',$p_code);

	return preg_replace(
		'#<span[^>]+>'.$p_code.'</span>#i',
		'<a href="?file='.$v['FILE'].'&offset='.$v['OFFSET'].'&name='.$code.'&line='.$v['LINE'].'">'.$s_code.'</a>',
		$html
	);
}

function ReadOld()
{
	include(DATA_FILE_OLD);
	return $DATA;
}
?>
<style>
	.divx {
		border:1px solid #CCC;
		margin:2px;
	}

	.code {
		font-family:Courier;
		width:600;
		vertical-align:top;
	}

	.class {
		color:#993;
		font-weight:bold;
	}

	span.method {
		color:#66F;
	}

	.var {
		color:#363;
	}

	div#live_api td {
		font-family:Verdana,Tahoma,Arial;
	}

	td.method {
		background-color:#CFC;
		width:200;
	}
	td.event {
		background-color:#FFC;
		width:200;
	}
	td.const {
		background-color:#FCC;
		width:200;
	}
</style>
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
