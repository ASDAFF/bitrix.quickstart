<?
define('DATA_FILE',$_SERVER["DOCUMENT_ROOT"].'/bitrix/managed_cache/live_api.data');
define('DATA_FILE_OLD', DATA_FILE.'_old');

Class CBitrixLiveapi 
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
			return;

		$MODULE_ID = basename(dirname(__FILE__));
		$aMenu = array(
			//"parent_menu" => "global_menu_services",
			"parent_menu" => "global_menu_settings",
			"section" => $MODULE_ID,
			"sort" => 50,
			"text" => $MODULE_ID,
			"title" => '',
//			"url" => "partner_modules.php?module=".$MODULE_ID,
			"icon" => "",
			"page_icon" => "",
			"items_id" => $MODULE_ID."_items",
			"more_url" => array(),
			"items" => array()
		);
/*
		if (file_exists($path = dirname(__FILE__).'/admin'))
		{
			if ($dir = opendir($path))
			{
				$arFiles = array();

				while($item = readdir($dir))
				{
					if (in_array($item,array('.','..','menu.php')))
						continue;

					$arFiles[] = $item;
				}

				sort($arFiles);

				foreach($arFiles as $item)
					$aMenu['items'][] = array(
						'text' => $item,
						'url' => $MODULE_ID.'_'.$item,
						'module_id' => $MODULE_ID,
						"title" => "",
					);
			}
		}
		*/
		$aMenu['items'][] = array(
			'text' => 'live_api.php',
			'url' => $MODULE_ID.'_'.'live_api.php',
			'module_id' => $MODULE_ID,
			"title" => "",
		);
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/updates/main'))
			$aMenu['items'][] = array(
				'text' => 'update_api.php',
				'url' => $MODULE_ID.'_'.'update_api.php',
				'module_id' => $MODULE_ID,
				"title" => "",
			);
		$aModuleMenu[] = $aMenu;
	}

	function OnAdminPageLoad()
	{
		if (
			strpos($r = $_SERVER['REQUEST_URI'],'/bitrix/admin/') === 0 
			&&
			file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/liveapi/liveapi.js')
			&& 
			$GLOBALS['APPLICATION']->GetTitle()
		)
			echo '<script src="/bitrix/js/liveapi/liveapi.js"></script>';
	}

	function ScanModule($path0)
	{
		$arRes = array();

		if (!($dir = opendir($path0)))
			die('Cannot read '.$path0);

		while(false !== ($file = readdir($dir)))
		{
			if (!is_dir($path0.'/'.$file) && preg_match('#\.(php)$#', $file, $regs))
			{
				if ($file == 'updater.php')
					continue;

				$arRes = self::ResMerge($arRes, CBitrixLiveapi::ParseFile($path0.'/'.$file));
			}
		}
		closedir($dir);

		$arScanDir = array('general','mysql','include','interface','public');
		foreach($arScanDir as $folder)
		{
			$path = $path0.'/classes/'.$folder;

			if (!file_exists($path))
				$path = $path0.'/'.$folder;

			if (!file_exists($path))
				continue;

			$arRes = self::ResMerge($arRes, self::ScanDir($path));
		}
		$arRes = self::ResMerge($arRes, self::ScanDir($path0.'/lib'));
		$arRes = self::ResMerge($arRes, self::ScanDir($path0.'/install/index.php'));
		return $arRes;
	}

	function ScanDir($path)
	{
		$arRes = array();
		if (is_dir($path))
		{
			if (!($dir = opendir($path)))
				die('Cannot read '.$path);

			while(false !== ($file = readdir($dir)))
			{
				if ($file == '.' || $file == '..')
					continue;
				$arRes = self::ResMerge($arRes, self::ScanDir($path.'/'.$file));
			}
			closedir($dir);
		}
		elseif (is_file($path) && preg_match('#\.(php)$#', $path, $regs))
			return CBitrixLiveapi::ParseFile($path);
		return $arRes;
	}

	function ParseFile($file)
	{
		$arRes = array();
		$f = fopen($file, 'rb');
		if ($f === false)
			return false;
		$curClass = $curFunc = '';
		$js = false;
		$FILE = substr($file, strlen($_SERVER['DOCUMENT_ROOT']));
		$i = $offset = $offset_doc = 0;
		$cntOpen = $cntClose = 0;
		while(false !== ($l = fgets($f)))
		{
			$i++;
			if (preg_match('#<script>#i',$l))
				$js = true;
			elseif (preg_match('#<script language#i',$l))
				$js = true;
			elseif (preg_match('#<script type#i',$l))
				$js = true;
			if (preg_match('#[^\'"]?</script>#i',$l))
				$js = false;

			if (!$js)
			{
				$cntOpen += substr_count($l, '{');
				$cntClose += substr_count($l, '}');

				if ($cntClose > 0)
				{
					if ($curClass)
					{
						if ($cntClose >= $cntOpen)
						{
							$curClass = '';
							$curFunc = '';
							$interface = false;
							$offset_doc = 0;
						}
						elseif ($cntClose + 1 >= $cntOpen)
						{
							$curFunc = '';
							$offset_doc = 0;
						}
					}
					elseif ($cntClose >= $cntOpen)
					{
						$curFunc = '';
						$offset_doc = 0;
					}
				}

				if (preg_match('#^\s*/\*\*#i', $l, $regs))
				{
					$offset_doc = $offset;
				}
				elseif (preg_match('#^[a-z\s]*(class|interface) ([a-z0-9_]+)#i', $l, $regs))
				{
					$offset_doc = 0;
					if ($interface = strtolower($regs[1]) == 'interface')
						continue;
					$cntOpen = $cntClose = 0;
					$curClass = $regs[2];
					$curClass = preg_replace('#^CAll#i','C',$curClass);
					$curClass = preg_replace('#_all$#i','',$curClass);
				}
				elseif (preg_match('#^([a-z\s]*)function (&?[a-z0-9_]+) ?\(((.*)\))?#i', $l, $regs))
				{
					if ($interface)
						continue;
					$curFunc = $func = ($curClass ? $curClass.'::' : '').$regs[2];
					$args = $regs[4];
					$arRes[0][$func] = array(
						'FILE' => $FILE,
						'LINE' => $i, 
						'OFFSET' => $offset_doc ? $offset_doc : $offset,
						'ARGS' => $regs[3] ? $args : 'N/A',
					);
					$offset_doc = 0;
				}
				elseif (preg_match('#(GetModuleEvents|CLearnHelper::FireEvent)\([^,]+,["\' ]*([a-z0-9_]+)#i', $l, $regs))
				{
					$event = $regs[2];
					$arRes[1][$event] = $curFunc;
				}
				elseif (preg_match('#ExecuteEvents\([\'"]?([a-z0-9_]+)#i', $l, $regs))
					$arRes[1][$regs[1]] = $curFunc;
				elseif ($curFunc && preg_match('#defined\(["\']([a-z_]+)["\']\)#i', $l, $regs))
					$arRes[2][$regs[1]] = $curFunc;
			}
			$offset = ftell($f);
		}
		fclose($f);

		return $arRes;
	}

	function ResMerge($ar0, $ar1)
	{
		$arRes = array();
		$arRes[0] = array_merge((array) $ar0[0], (array) $ar1[0]);
		$arRes[1] = array_merge((array) $ar0[1], (array) $ar1[1]);
		$arRes[2] = array_merge((array) $ar0[2], (array) $ar1[2]);
		return $arRes;
	}

	function Beautiful($html, $name, $namespace, $file, $line, $highlight)
	{
		global $raw;
		$raw = $html;
		$html = "<?"."php \n//	".$namespace.$name."()\n//	$file:$line\n\n".$html;
		if (file_exists($file = DATA_FILE))
		{
			$class = ($p = strpos($name,'::')) ? substr($name,0,$p) : false;
			include($file);
			$close = md5(mt_rand(999,999999999));
			$arMarks = array();
			foreach($DATA as $module_id => $ar)
			{
				list($arRes,$arEvt) = unserialize($ar);
				if (!is_array($arRes))
					continue;

				foreach($arRes as $k => $v)
				{
					$curClass = ($p0 = self::strpos($k,'::')) ? self::substr($k, 0, $p0) : false;
					$curFunc = $curClass ? self::substr($k, $p0 + 2) : $k;

					if (false === self::stripos($html, $curFunc))
						continue;

					if ($curClass)
					{
						$mark0 = md5(mt_rand(999,999999999));
						$mark1 = md5(mt_rand(999,999999999));
						$html = preg_replace('#\b('.$curClass.')::('.$curFunc.')\(#i', $mark0."\${1}".$close.'::'.$mark1."\${2}".$close.'(', $html, -1, $cnt);
						if ($cnt)
						{
							$arMarks[$mark0] = '<a href="bitrix.liveapi_live_api.php?module_id='.$module_id.'&class='.$curClass.'">';
							$arMarks[$mark1] = '<a href="bitrix.liveapi_live_src.php?module_id='.$module_id.'&name='.$k.'">';
							continue;
						}

						if (strcasecmp($curClass, $class) == 0)
						{
							$mark0 = md5(mt_rand(999,999999999));
							$mark1 = md5(mt_rand(999,999999999));
							$html = preg_replace('#\b(self|$this)(..)('.$curFunc.')\(#i', $mark0."\${1}".$close."\${2}".$mark1."\${3}".$close.'(', $html, -1, $cnt);
							if ($cnt)
							{
								$arMarks[$mark0] = '<a href="bitrix.liveapi_live_api.php?module_id='.$module_id.'&class='.$curClass.'">';
								$arMarks[$mark1] = '<a href="bitrix.liveapi_live_src.php?module_id='.$module_id.'&name='.$k.'">';
								continue;
							}
						}

						// method
						$mark = md5(mt_rand(999,999999999));
						$html = preg_replace('#(->)('.$curFunc.')\(#i', "\${1}".$mark."\${2}".$close.'(', $html, -1, $cnt);
						if ($cnt)
							$arMarks[$mark] = '<a href="bitrix.liveapi_live_api.php?search='.$curFunc.'&exact=Y">';
					}
					else // function
					{
						if (preg_match('#(..)\b('.$k.')\b\(#', $html, $regs))
						{
							if ($regs[1] == '::' || $regs[1] == '->' || $regs[1] == 'n ') // functio(n )
								continue;
							$mark = md5(mt_rand(999,999999999));
							$html = str_replace($regs[0], $regs[1].$mark.$regs[2].$close.'(', $html);
							$arMarks[$mark] = '<a href="bitrix.liveapi_live_src.php?module_id='.$module_id.'&name='.$k.'">';
						}
					}
				}
			}
		}
		$html = highlight_string($html, true);

		foreach($arMarks as $mark => $a)
			$html = str_replace($mark, $a, $html);
		$html = str_replace($close, '</a>', $html);

		if ($highlight)
			$html = str_replace($highlight,'<a name="'.htmlspecialchars($highlight).'"></a><span style="background:#FFFF00">'.$highlight.'</span>',$html);

		if ($class)
		{
			$html = str_ireplace('public&nbsp;','<span style="color:#933;font-weight:bold">public</span>&nbsp;',$html);
			$html = str_ireplace('private&nbsp;','<span style="color:#933;font-weight:bold">private</span>&nbsp;',$html);
			$html = str_ireplace('protected&nbsp;','<span style="color:#933;font-weight:bold">protected</span>&nbsp;',$html);
			$html = str_ireplace('static&nbsp;','<span style="color:#333;font-weight:bold">static</span>&nbsp;',$html);
		}

		return $html;
	}

	function stripos($a, $b)
	{
		return function_exists('mb_orig_stripos') ? mb_orig_stripos($a, $b) : stripos($a, $b);
	}

	function strpos($a, $b)
	{
		return function_exists('mb_orig_strpos') ? mb_orig_strpos($a, $b) : strpos($a, $b);
	}

	function substr($a, $b, $c = null)
	{
		if ($c === null)
			return function_exists('mb_orig_substr') ? mb_orig_substr($a, $b) : substr($a, $b);
		else
			return function_exists('mb_orig_substr') ? mb_orig_substr($a, $b, $c) : substr($a, $b, $c);
	}

	function colorize($func, $ar, $class = false, $module_id = '')
	{
		$link = "bitrix.liveapi_live_src.php?module_id=$module_id&name=$func";
		if ($c = strpos($func, "::"))
		{
			if ($class && substr($func,0,$c) != $class)
				return;
			$func = '<a href="bitrix.liveapi_live_api.php?module_id='.$module_id.'&class='.substr($func,0,$c).'" class=class>'.substr($func,0,$c).'</span>::<a href="'.$link.'" target=_blank><span class=method>'.substr($func,$c+2).'</span></a>';
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

	function ReadOld()
	{
		include(DATA_FILE_OLD);
		return $DATA;
	}

	function Insert($arRawFields)
	{
		global $DB;
		$arFields = self::PrepareFields($arRawFields);
		$rs = $DB->Query('SELECT * FROM b_liveapi WHERE etag='.$arFields['etag']);
		if ($f = $rs->Fetch())
		{
			if (version_compare($f['version'], $arRawFields['version'], '<'))
				return true;
			$DB->Query('DELETE FROM b_liveapi WHERE etag='.$arFields['etag']);
		}

		return $DB->Insert('b_liveapi', $arFields);
	}

	function PrepareFields($arRawFields)
	{
		global $DB;
		$arFields = array();
		foreach(array('module_id', 'version', 'type') as $key)
			$arFields[$key] = '"'.$DB->ForSQL(ToLower(trim($arRawFields[$key]))).'"';
		list($a,$b,$c) = explode('.', $arRawFields['version']);
		$arFields['version_sort'] = intval($a) * 10000 + intval($b) * 100 + intval($c);
		$arFields['item'] = '"'.$DB->ForSQL($arRawFields['item']).'"';
		$arFields['location'] = '"'.$DB->ForSQL(serialize($arRawFields['location'])).'"';
		$arFields['etag'] = '"'.md5($arFields['module_id'].$arFields['type'].strtolower($arFields['item']).($arRawFields['type'] == 0 ? strtolower($arRawFields['location']['ARGS']) : '')).'"';
		return $arFields;
	}

	function Clear($arFilter = array())
	{
		global $DB;
		if (!count($arFilter))
			return $DB->Query('TRUNCATE TABLE b_liveapi');

		$sql = 'DELETE FROM b_liveapi WHERE 1=1';
		foreach($arFilter as $k => $v)
			$sql .= ' AND '.$k.'="'.$DB->ForSQL($v).'"';
		return $DB->Query($sql);
	}

	function GetModuleVersion($module_path, $module_id)
	{
		if ($module_id == 'main')
		{
			if (file_exists($f = $module_path.'/classes/general/version.php'))
			{
				$str = file_get_contents($f);
				if (preg_match('#SM_VERSION[" ,]+([^"]+)#m', $str, $regs))
					return $regs[1];
				else
					die('Cannot parse: '.htmlspecialchars($str));
			}
		}
		elseif (file_exists($f = $module_path.'/install/version.php'))
		{
			include($f);
			return $arModuleVersion['VERSION'];
		}
		return false;
	}
}
?>
