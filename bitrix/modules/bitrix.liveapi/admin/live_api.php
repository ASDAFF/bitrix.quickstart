<?php
define('START_TIME', time());
define('NOT_CHECK_PERMISSIONS', (strpos($_SERVER['REMOTE_ADDR'], '127.') === 0));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if(!NOT_CHECK_PERMISSIONS && !$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule('bitrix.liveapi');

if ($_REQUEST['file'])
	$APPLICATION->SetTitle(GetMessage("BITRIX_LIVEAPI_ISHODNYY_KOD_FAYLA"));
elseif ($_REQUEST['scan'])
	$APPLICATION->SetTitle(GetMessage("BITRIX_LIVEAPI_SKANIROVANIE"));
elseif ($_REQUEST['module_id'])
	$APPLICATION->SetTitle('API '.GetMessage("BITRIX_LIVEAPI_MODULA").htmlspecialchars($_REQUEST['module_id']));
elseif ($_REQUEST['search'])
	$APPLICATION->SetTitle(GetMessage("BITRIX_LIVEAPI_POISK_PO"));
elseif ($_REQUEST['show_diff'])
	$APPLICATION->SetTitle(GetMessage("BITRIX_LIVEAPI_IZMENENIA_API_POSLE"));
else
	$APPLICATION->SetTitle(GetMessage("BITRIX_LIVEAPI_AKTUALQNOE_OPISANIE"));

?><?
if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');


$offset = intval($_REQUEST['offset']);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if ($_REQUEST['scan'])
{
	$path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules';
	if (!($dir = opendir($path)))
		die('Cannot read '.$path);

	CheckDirPath(dirname(DATA_FILE));
	if (!$_REQUEST['next_file'] && file_exists(DATA_FILE))
		rename(DATA_FILE, DATA_FILE_OLD);

	while(false !== ($file = readdir($dir)))
	{
		if ($file == '.' || $file == '..' || strpos($file, '.') === 0 || !is_dir($path.'/'.$file))
			continue;

		if ($_REQUEST['next_file'] && $file != $_REQUEST['next_file'])
			continue;
		unset($_REQUEST['next_file']);

		if (time() - START_TIME > 10)
			die('<div>'.GetMessage("BITRIX_LIVEAPI_SKANIROVANIE_MODULA") . $file . '</div> <script>document.location="?scan=Y&next_file='.htmlspecialchars($file).'";</script>');

		$ar = CBitrixLiveapi::ScanModule($path.'/'.$file);
		$f = fopen(DATA_FILE,'ab');
		fwrite($f, '<'.'? $DATA[\''.$file.'\'] = \''.str_replace("'","\'",str_replace('\\','\\\\',serialize($ar))).'\'; ?'.'>'."\n");
		fclose($f);
	}
	closedir($dir);

	echo '<div>'.GetMessage("BITRIX_LIVEAPI_SKANIROVANIE_ZAVERSE").'</div>';
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
	echo GetMessage("BITRIX_LIVEAPI_VYBERITE_MODULQ").': <select onchange="document.location=\'?module_id=\'+this.value"><option></option>';
	foreach($arModules as $k)
		echo '<option value="'.$k.'" '.($k== $_REQUEST['module_id'] ? 'selected' : '').'>'.$k.'</option>';
	echo '</select>';


	if (isset($DATA[$_REQUEST['module_id']]))
	{
		$arClasses = array();
		list($arRes,$arEvt,$arConst) = unserialize($DATA[$_REQUEST['module_id']]);
		$ar = array_keys($arRes);
		foreach($ar as $str)
		{
			if ($class = ($p = strpos($str,'::')) ? substr($str,0,$p) : false)
				$arClasses[$class] = 1;
		}

		if (count($arClasses))
		{
			echo ' '.GetMessage("BITRIX_LIVEAPI_KLASS").': <select onchange="document.location=\'?module_id='.htmlspecialchars($_REQUEST['module_id']).'&class=\'+this.value"><option></option>';
			foreach($arClasses as $k=>$v)
				echo '<option value="'.$k.'" '.($k== $_REQUEST['class'] ? 'selected' : '').'>'.$k.'</option>';
			echo '</select>';
		}
	}

	if ($_REQUEST['search'])
	{
		foreach($DATA as $module_id=>$sar)
		{
			$ar = unserialize($sar);
			list($arRes,$arEvt,$arConst) = $ar;
			$bFound = false;

			$header = 
				'<h2><a href="?module_id='.$module_id.'">'.$module_id.'</a></h2>'.
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
						'<td class="code">'.CBitrixLiveapi::colorize($k,$v,false,$module_id).'</td>'.
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
					$link = "bitrix.liveapi_live_src.php?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$evt.'#'.$evt;
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
					$link = "bitrix.liveapi_live_src.php?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$const.'#'.$const;
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
	elseif (isset($DATA[$_REQUEST['module_id']]))
	{
		$module_id = $_REQUEST['module_id'];
		$class = $_REQUEST['class'];
		$ar = unserialize($DATA[$_REQUEST['module_id']]);
		list($arRes,$arEvt,$arConst) = $ar;
		if (!$class && count($arEvt))
		{
			echo '<h2>'.GetMessage("BITRIX_LIVEAPI_SOBYTIA_MODULA").htmlspecialchars($module_id).'</h2>';
			echo '<table border=1 cellpadding=4 cellspacing=0>';
				echo 
				'<tr align=center bgcolor="#CCCCCC">'.
					"<td><b>".GetMessage("BITRIX_LIVEAPI_SOBYTIE")."</td>".
					"<td><b>".GetMessage("BITRIX_LIVEAPI_VYZYVAETSA")."</td>".
				'</tr>';

			foreach($arEvt as $evt => $func)
			{
				$ar = $arRes[$func];
				$link = "bitrix.liveapi_live_src.php?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$evt.'#'.$evt;
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
			echo '<h2>'.GetMessage("BITRIX_LIVEAPI_KONSTANTY_MODULA").htmlspecialchars($module_id).'</h2>';
			echo '<table border=1 cellpadding=4 cellspacing=0>';
				echo 
				'<tr align=center bgcolor="#CCCCCC">'.
					"<td><b>".GetMessage("BITRIX_LIVEAPI_KONSTANTA")."</td>".
					"<td><b>".GetMessage("BITRIX_LIVEAPI_PROVERAETSA")."</td>".
				'</tr>';

			foreach($arConst as $const => $func)
			{
				$ar = $arRes[$func];
				$link = "bitrix.liveapi_live_src.php?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$const.'#'.$const;
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

			echo '<h2>'.GetMessage("BITRIX_LIVEAPI_SPISOK_FUNKCIY_I_MET").htmlspecialchars($module_id).'</h2>';
			echo '<table border=1 cellpadding=4 cellspacing=0>';
				echo 
				'<tr align=center bgcolor="#CCCCCC">'.
					"<td><b>".GetMessage("BITRIX_LIVEAPI_METOD")."</td>".
				'</tr>';

			foreach($arRes as $func => $ar)
			{
				if ($str = CBitrixLiveapi::colorize($func,$ar,$class, $module_id))
					echo 
					'<tr>'.
						"<td valign=top class=code>".$str."</td>".
					'</tr>';
			}
			echo '</table>';
		}
	}
	elseif ($_REQUEST['show_diff'])
	{
		$DATA_OLD = CBitrixLiveapi::ReadOld();
		foreach($DATA as $module_id => $v)
		{
			if ($v != $DATA_OLD[$module_id])
			{
				echo '<h2><a href="?module_id='.$module_id.'">'.$module_id.'</a></h2>';
				$ar_new = unserialize($v);
				$ar_old = unserialize($DATA_OLD[$module_id]);

				if (count($tmp = array_diff_assoc($ar_new[0], $ar_old[0])))
				{
					echo '<table border=1 cellpadding=4 cellspacing=0>';

					foreach($tmp as $k=>$v)
						echo '<tr>'.
							'<td class="method">'.GetMessage("BITRIX_LIVEAPI_METOD1").'</td>'.
							'<td class="code">'.CBitrixLiveapi::colorize($k,$v,false,$module_id).'</td>'.
						'</tr>';
					echo '</table>';
				}

				if (count($tmp = array_diff_assoc($ar_new[1], $ar_old[1])))
				{
					echo '<table border=1 cellpadding=4 cellspacing=0>';

					foreach($tmp as $const=>$func)
					{
						$ar = $ar_new[0][$func];
						$link = "bitrix.liveapi_live_src.php?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$const.'#'.$const;
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
						$link = "bitrix.liveapi_live_src.php?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]&highlight=".$evt.'#'.$evt;
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
