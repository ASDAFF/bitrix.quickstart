<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

if(!$USER->IsAdmin())
	$APPLICATION->AuthForm();

IncludeModuleLangFile(__FILE__);

if (function_exists('mb_internal_encoding'))
	mb_internal_encoding('ISO-8859-1');

define('START_PATH', $_SERVER['DOCUMENT_ROOT']); // стартовая папка для поиска
define('LOG', $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bitrix.xscan/file_list.txt'); // лог файл 
define('START_TIME', time()); // засекаем время старта

$APPLICATION->SetTitle(GetMessage("BITRIX_XSCAN_SEARCH"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

if ($file = $_REQUEST['file'])
{
	$str = file_get_contents(LOG);
	if (strpos($str,$file) === false)
		ShowMsg(GetMessage("BITRIX_XSCAN_NOT_FOUND").htmlspecialchars($file), 'red');
	elseif (file_exists($f = START_PATH.$file))
	{
		if ($_REQUEST['action'] == 'prison')
		{
			$new_f = preg_replace('#\.php$#', '.ph_', $f);
			if (rename($f, $new_f))
				ShowMsg(GetMessage("BITRIX_XSCAN_RENAMED").htmlspecialchars($new_f));
			else
				ShowMsg(GetMessage("BITRIX_XSCAN_ERR_RENAME").htmlspecialchars($f), 'red');
		}
		else
		{
			$str = file_get_contents($f);
			highlight_string(GetMessage("BITRIX_XSCAN_FILE_CONT").$f."\n\n".$str);
			die();
		}
	}
	else
		ShowMsg(GetMessage("BITRIX_XSCAN_FILE_NOT_FOUND").htmlspecialchars($f), 'red');
}

if ($_REQUEST['go'])
{
	if ($_REQUEST['break_point'])
		define('SKIP_PATH',htmlspecialchars($_REQUEST['break_point'])); // промежуточный путь
	elseif (file_exists(LOG))
		unlink(LOG);

	Search(START_PATH);
	if (defined('BREAK_POINT'))
	{
			?><form method=post id=postform action=?>
			<input type=hidden name=go value=Y>
			<input type=hidden name=break_point value="<?=htmlspecialchars(BREAK_POINT)?>">
			</form>
			<?
			ShowMsg('<b>'.GetMessage("BITRIX_XSCAN_IN_PROGRESS").'...</b><br>
			'.GetMessage("BITRIX_XSCAN_CURRENT_FILE").': <i>'.htmlspecialchars(str_replace(START_PATH,'',BREAK_POINT)).'</i>');
			?>
			<script>window.setTimeout("document.getElementById('postform').submit()",500);</script><? // таймаут чтобы браузер показал текст
			die();
	}
	else
	{
		if (!file_exists(LOG))
			ShowMsg(GetMessage("BITRIX_XSCAN_COMPLETED"));
		else
			ShowMsg(GetMessage("BITRIX_XSCAN_COMPLETED_FOUND"), 'red');
	}
}
?><form method=post action=?>
	<input type=submit name=go value="<?=GetMessage("BITRIX_XSCAN_START_SCAN")?>">
</form><?

if (file_exists(LOG))
{
	echo '<table width=80%>';
	echo '<tr>
		<th>'.GetMessage("BITRIX_XSCAN_NAME").'</th>
		<th>'.GetMessage("BITRIX_XSCAN_TYPE").'</th>
		<th>'.GetMessage("BITRIX_XSCAN_SIZE").'</th>
		<th>'.GetMessage("BITRIX_XSCAN_M_DATE").'</th>
		<th></th>
	</tr>';

	$ar = file(LOG);
	foreach($ar as $line)
	{
		list($f, $type) = explode("\t", $line);
		{
			$fu = urlencode(trim($f));
			$bInPrison = trim($type) != 'htaccess';
			echo '<tr>
				<td><a href="?action=showfile&file='.$fu.'" title="'.GetMessage("BITRIX_XSCAN_SRC").'" target=_blank>'.htmlspecialchars($f).'</a></td>
				<td>'.htmlspecialchars($type).'</td>
				<td>'.HumanSize(filesize($_SERVER['DOCUMENT_ROOT'].$f)).'</td>
				<td>'.date('d.m.Y H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'].$f)).'</td>
				<td>'.($bInPrison ? '<a href="?action=prison&file='.$fu.'" onclick="if(!confirm(\''.GetMessage("BITRIX_XSCAN_WARN").'\'))return false;" title="'.GetMessage("BITRIX_XSCAN_QUESTION").'">'.GetMessage("BITRIX_XSCAN_KARANTIN") : '').'</td>
			</tr>';
		}
	}
	echo '</table>';
}

function Search($path)
{
	if (time() - START_TIME > 10)
	{
		if (!defined('BREAK_POINT'))
			define('BREAK_POINT', $path);
		return;
	}

	if (defined('SKIP_PATH') && !defined('FOUND')) // проверим, годится ли текущий путь
	{
		if (0 !== bin_strpos(SKIP_PATH, dirname($path))) // отбрасываем имя или идём ниже 
			return;

		if (SKIP_PATH==$path) // путь найден, продолжаем искать текст
			define('FOUND',true);
	}

	if (is_dir($path)) // dir
	{
		if (is_link($path))
		{
			if (strpos(realpath($path), $_SERVER['DOCUMENT_ROOT']) !== false) // если симлинк ведет на папку внутри структуры сайта
				return true;
		}

		$dir = opendir($path);
		while($item = readdir($dir))
		{
			if ($item == '.' || $item == '..')
				continue;

			Search($path.'/'.$item);
		}
		closedir($dir);
	}
	else // file
	{
		if (!defined('SKIP_PATH') || defined('FOUND'))
			if ($res = CheckFile($path))
				Mark($path, $res);
	}
}

function CheckFile($f)
{
	static $me;
	if (!$me)
		$me = realpath(__FILE__);
	if (realpath($f) == $me)
		return false;

	if (basename($f) == '.htaccess')
	{
		$str = file_get_contents($f);
		return preg_match('#<(\?|script)#i',$str) ? 'htaccess' : false;
	}

	if (!preg_match('#\.php$#',$f))
		return false;
	
	if (false === $str = file_get_contents($f))
		return 'read error';

	$s = "[ \r\t\n]*";
	if (preg_match('#[^a-z](eval|assert|call_user_func|call_user_func_array|create_function|ob_start)'.$s.'\([^\)]*\$_(POST|GET|REQUEST|COOKIE|SERVER)#i', $str))
	{
		if (!LooksLike($f, array('/bitrix/modules/main/admin/php_command_line.php', '/bitrix/modules/iblock/classes/mssql/cml2.php', '/bitrix/modules/iblock/classes/oracle/cml2.php', '/bitrix/modules/xdimport/admin/lf_scheme_getentity.php','/bitrix/modules/iblock/classes/mysql/cml2.php')) && !preg_match('#CTaskAssert::assert\(#', $str))
			return 'eval';
	}

	if (preg_match('#\$(USER|GLOBALS..USER..)->Authorize'.$s.'\([0-9]+\)#i', $str))
	{
		if (!LooksLike($f, array('/bitrix/modules/main/install/install.php','/bitrix/modules/dav/classes/general/principal.php','/bitrix/activities/bitrix/controllerremoteiblockactivity/controllerremoteiblockactivity.php','/bitrix/modules/controller/install/activities/bitrix/controllerremoteiblockactivity/controllerremoteiblockactivity.php')))
			return 'bitrix auth';
	}

	if (preg_match('#[\'"]php://filter#i', $str))
		return 'php wrapper';

	if (preg_match('#(include|require)(_once)?'.$s.'\([^\)]+\.([a-z0-9]+).'.$s.'\)#i', $str, $regs))
		if ($regs[3] != 'php')
			return 'strange include';

	if (preg_match('#\$__+[^a-z_]#i', $str))
		return 'strange vars';

	if (preg_match('#\$['."_\x80-\xff".']+'.$s.'=#i', $str))
		return 'binary vars';

//	if (preg_match('#Zend Optimizer not installed#i',$str))
//		return false;

	if (preg_match('#[a-z0-9+=/]{255,}#i', $str))
	{
		if (!preg_match('#data:image/[^;]+;base64,[a-z0-9+=/]{255,}#i', $str))
			return 'long line';
	}

	if (preg_match('#file_get_contents\(\$[^\)]+\);[^a-z]*file_put_contents#mi', $str))
		return 'file from variable';

	if (preg_match("#[\x01-\x08\x0b\x0c\x0f-\x1f]#", $str,$regs))
	{
		if (!preg_match('#^'.$_SERVER['DOCUMENT_ROOT'].'/bitrix/[^/]*cache/#', $f) || !preg_match('#\$ser_content = \'#',$str))
			return 'binary data';
	}

	return false;
}

function LooksLike($f, $mask)
{
	$f = str_replace('\\','/',$f);
	if (is_array($mask))
	{
		foreach($mask as $m)
		{
			if (preg_match('#'.$m.'$#',$f))
				return true;
		}
	}
	return preg_match('#'.$mask.'$#',$f);
}

function bin_strpos($s, $a)
{
	if (function_exists('mb_orig_strpos'))
		return mb_orig_strpos($s, $a);
	return strpos($s, $a);
}

function Mark($f, $type)
{
	file_put_contents(LOG, str_replace(START_PATH,'',$f)."\t".$type."\n", 8);
}

function ShowMsg($str, $color = 'green')
{
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => '',
		"DETAILS" => $str,
		"TYPE" => $color == 'green' ? "OK" : 'ERROR',
		"HTML" => true));
}

function HumanSize($s)
{
	$i = 0;
	$ar = array('b','kb','M','G');
	while($s > 1024)
	{
		$s /= 1024;
		$i++;
	}
	return round($s,1).' '.$ar[$i];
}

?>
