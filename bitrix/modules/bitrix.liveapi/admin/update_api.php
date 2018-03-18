<?php
// define('NOT_CHECK_PERMISSIONS', (strpos($_SERVER['REMOTE_ADDR'], '127.') === 0));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
if(!NOT_CHECK_PERMISSIONS && !$USER->CanDoOperation('edit_php'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule('bitrix.liveapi');
?><?
if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');

$path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/updates';
if ($_REQUEST['scan'])
{
	if (!($dir = opendir($path)))
		die('Cannot read '.$path);
	
	$next_module_id = $_REQUEST['next_module_id'];
	if ($_REQUEST['force'])
		CBitrixLiveapi::Clear();

	$loop = false;
	while(false !== ($module_id = readdir($dir)))
	{
		if (preg_match('#^(_|(\.|\.\.|update$))#', $module_id) || !is_dir($path.'/'.$module_id))
			continue;

		if ($next_module_id && $module_id != $next_module_id)
			continue;
		else
			unset($next_module_id);

		if ($loop)
		{
			echo '<div>'.GetMessage("BITRIX_LIVEAPI_SKANIRUEM_OBNOVLENIA").$module_id. '</div> <script>document.location="?scan=Y&next_module_id='.urlencode($module_id).'";</script>';
			exit();
		}
		$ver_path = $path.'/'.$module_id;

		if (!($ver_dir = opendir($ver_path)))
			die('Cannot read '.$ver_path);

		while(false !== $ver = readdir($ver_dir))
		{
			if ($ver == '.' || $ver == '..' || !is_dir($ver_path.'/'.$ver))
				continue;

			if (!$version = CBitrixLiveapi::GetModuleVersion($ver_path.'/'.$ver, $module_id))
				$version = $ver;

			$rs = $DB->Query('SELECT item FROM b_liveapi WHERE module_id="'.$DB->ForSQL($module_id).'" AND version="'.$DB->ForSQL($version).'" LIMIT 1'); // MySQL only
			if ($rs->Fetch())
				continue;

			$ar = CBitrixLiveapi::ScanModule($ver_path.'/'.$ver);
			foreach($ar as $type => $arList)
			{
				foreach($arList as $item => $Location)
				{
					CBitrixLiveapi::Insert(array(
							'module_id' => $module_id,
							'version' => $version,
							'type' => $type,
							'item' => $item,
							'location' => $Location
						)
					);
				}
			}
		}
		closedir($ver_dir);
		$loop = true;
	}
	closedir($dir);
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

echo '<div>
	<input type=button value="'.GetMessage("BITRIX_LIVEAPI_SKANIROVATQ_PAPKU_S").'" onclick="if(confirm(\''.GetMessage("BITRIX_LIVEAPI_NACATQ_SKANIROVANIE").'?\'))document.location=\'?scan=Y\'">
	<input type=button value="'.GetMessage("BITRIX_LIVEAPI_POKAZATQ_POSLEDNIE_O").'" onclick="document.location=\'?last_updates=Y\'">
</div>';

echo '<div id="live_api">';
echo '<form method=GET>'.GetMessage("BITRIX_LIVEAPI_POISK_PO_IMENI_FUNKC").': 
	<input name=search value="'.htmlspecialchars($_REQUEST['search']).'" size=30> 
	<label><input type=checkbox name="exact" '.($_REQUEST['exact'] ? 'checked' : '').'> '.GetMessage("BITRIX_LIVEAPI_TOCNO").'</label>
	<input type=submit value="'.GetMessage("BITRIX_LIVEAPI_ISKATQ").'"></form>';

echo GetMessage("BITRIX_LIVEAPI_VYBERITE_MODULQ").': <select onchange="document.location=\'?module_id=\'+this.value"><option></option>';
$rs = $DB->Query('SELECT DISTINCT module_id FROM b_liveapi ORDER BY module_id ASC');
while($f = $rs->Fetch())
{
	$k = $f['module_id'];
	echo '<option value="'.$k.'" '.($k == $_REQUEST['module_id'] ? 'selected' : '').'>'.$k.'</option>';
}
echo '</select>';


$module_id = trim($_REQUEST['module_id']);
$search = $DB->ForSQL(trim($_REQUEST['search']));

if ($module_id || $search)
{
	if ($module_id)
	{
		$rs = $DB->Query('SELECT DISTINCT version FROM b_liveapi WHERE module_id = "'.$DB->ForSQL($module_id).'" ORDER BY version_sort ');
		$version = trim($_REQUEST['version']);
		echo ' '.GetMessage("BITRIX_LIVEAPI_VERSIA").': <select onchange="document.location=\'?module_id='.htmlspecialchars($module_id).'&version=\'+this.value"><option></option>';
		while($f = $rs->Fetch())
		{
			$k = $f['version'];
			echo '<option value="'.$k.'" '.($k == $version ? 'selected' : '').'>'.$k.'</option>';
		}
		echo '</select>';
	}


	if ($search)
	{
		if ($_REQUEST['exact'])
			$rs = $DB->Query('SELECT * FROM b_liveapi WHERE item LIKE "'.$search.'" OR item LIKE "'.$search.'::%"'.' ORDER BY module_id, type DESC, item, version_sort ');
		else
			$rs = $DB->Query('SELECT * FROM b_liveapi WHERE item LIKE "%'.$search.'%" ORDER BY module_id, type DESC, item, version_sort ');
	}
	else
		$rs = $DB->Query('SELECT * FROM b_liveapi WHERE module_id = "'.$DB->ForSQL($module_id).'" '.($version ? 'AND version = "'.$DB->ForSQL($version).'"' : '').' ORDER BY type DESC, item, version_sort ');
	$arResult = array();
	while($f = $rs->Fetch())
		$arResult[$f['module_id']][$f['type']][] = $f;

	foreach($arResult as $module_id => $arType)
	{
		echo '<h2>'.htmlspecialchars($module_id).'</h2>';
		foreach($arType as $type => $arItems)
		{
			if ($type == 0)
			{
				echo '<h3>'.GetMessage("BITRIX_LIVEAPI_FUNKCII_I_METODY_MOD").htmlspecialchars($module_id).'</h3>';
				echo '<table border=1 cellpadding=4 cellspacing=0>';
				echo '<tr align=center bgcolor="#CCCCCC">'.
					"<td><b>".GetMessage("BITRIX_LIVEAPI_METOD")."</td>".
					"<td><b>".GetMessage("BITRIX_LIVEAPI_S")."&nbsp;".GetMessage("BITRIX_LIVEAPI_VERSII")."</td>".
					'</tr>';
			}
			elseif ($type == 1)
			{
				echo '<h3>'.GetMessage("BITRIX_LIVEAPI_SOBYTIA_MODULA").htmlspecialchars($module_id).'</h3>';
				echo '<table border=1 cellpadding=4 cellspacing=0>';
				echo '<tr align=center bgcolor="#CCCCCC">'.
					"<td><b>".GetMessage("BITRIX_LIVEAPI_SOBYTIE")."</td>".
					"<td><b>".GetMessage("BITRIX_LIVEAPI_VYZYVAETSA")."</td>".
					"<td><b>".GetMessage("BITRIX_LIVEAPI_S")."&nbsp;".GetMessage("BITRIX_LIVEAPI_VERSII")."</td>".
					'</tr>';
			}
			elseif ($type == 2)
			{
				echo '<h3>'.GetMessage("BITRIX_LIVEAPI_KONSTANTY_MODULA").htmlspecialchars($module_id).'</h3>';
				echo '<table border=1 cellpadding=4 cellspacing=0>';
				echo '<tr align=center bgcolor="#CCCCCC">'.
					"<td><b>".GetMessage("BITRIX_LIVEAPI_KONSTANTA")."</td>".
					"<td><b>".GetMessage("BITRIX_LIVEAPI_PROVERAETSA")."</td>".
					"<td><b>".GetMessage("BITRIX_LIVEAPI_S")."&nbsp;".GetMessage("BITRIX_LIVEAPI_VERSII")."</td>".
					'</tr>';
			}

			foreach($arItems as $f)
			{
				$ch_mark = $func && $func == $f['item'] ? 'style="background-color:#FFC"' : '';
				$func = $f['item'];
				$ar = unserialize($f['location']);

				if ($type == 0)
				{
					$link = "bitrix.liveapi_live_src.php?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]";
					if ($c = strpos($func, "::"))
						$str = '<span class=class>'.substr($func,0,$c).'</span>::<a href="'.$link.'" target=_blank><span class=method>'.substr($func,$c+2).'</span></a>';
					else
						$str = '<a href="'.$link.'" target=_blank><span class=method>'.$func.'</span></a>';
					$args = preg_replace('#(\$[a-z0-9_]+)#i','<span class=var>\\1</span>',htmlspecialchars($ar['ARGS']));

					echo '<tr '.$ch_mark.'>'.
						"<td valign=top class=code>".$str.'('.$args.')'."</td>".
						"<td valign=top class=code><a href=\"?module_id=".urlencode($f['module_id'])."&version=".urlencode($f['version'])."\">".$f['version']."</a></td>".
					'</tr>';
				}
				else
				{
					echo '<tr '.$ch_mark.'>'.
						"<td valign=top class=code>".htmlspecialchars($func)."</td>".
						"<td valign=top class=code><a href=\"?search=".urlencode($ar)."&exact=Y\">".$ar."</a></td>".
						"<td valign=top class=code><a href=\"?module_id=".urlencode($f['module_id'])."&version=".urlencode($f['version'])."\">".$f['version']."</a></td>".
					'</tr>';
				}
			}

			echo '</table>';
		}
	}
}
elseif ($_REQUEST['last_updates'])
{
	echo '<h2>'.GetMessage("BITRIX_LIVEAPI_POSLEDNIE_VERSII_OBN").'</h2>';
	if (!$dir = opendir($path))
		die('Cannot read '.$path);

	echo '<table border=1 cellpadding=4 cellspacing=0>';
	echo '<tr align=center bgcolor="#CCCCCC">'.
		"<td><b>".GetMessage("BITRIX_LIVEAPI_MODULQ")."</td>".
		"<td><b>".GetMessage("BITRIX_LIVEAPI_VERSIA1")."</td>".
		"<td><b>".GetMessage("BITRIX_LIVEAPI_DATA_IZMENENIA")."</td>".
		'</tr>';
	while(false !== $module_id = readdir($dir))
	{
		if (preg_match('#^(_|(\.|\.\.|update$))#', $module_id) || !is_dir($path.'/'.$module_id))
			continue;

		$last_version = 0;

		if (!($ver_dir = opendir($ver_path = $path.'/'.$module_id)))
			die('Cannot read '.$ver_path);

		while(false !== $ver = readdir($ver_dir))
		{
			if ($ver == '.' || $ver == '..' || !is_dir($ver_path.'/'.$ver))
				continue;

			if (version_compare($ver, $last_version, '>'))
			{
				$mtime = filemtime($ver_path.'/'.$ver);
				$last_version = $ver;
			}
		}
		closedir($ver_dir);

		if ($version = CBitrixLiveapi::GetModuleVersion($ver_path.'/'.$last_version, $module_id))
			$last_version = $version;

		if ($last_version)
			echo '<tr>
				<td><a href="?module_id='.urlencode($module_id).'">'.htmlspecialchars($module_id).'</a></td>
				<td><a href="?module_id='.urlencode($module_id).'&version='.urlencode($last_version).'">'.htmlspecialchars($last_version).'</a></td>
				<td>'.ConvertTimeStamp($mtime, 'FULL').'</a></td>
			</tr>';
	}
	echo '</table>';
	closedir($dir);
}

echo '</div>';


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
