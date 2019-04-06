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
							$html = CBitrixLiveapi::GetLink($k, $v, $html);

						if ($class)
							$html = CBitrixLiveapi::GetLink($k, $v, $html, $class.'::','$this->');

						if ($module == 'main')
						{
							$html = CBitrixLiveapi::GetLink($k, $v, $html,'CUser::', '$USER->');
							$html = CBitrixLiveapi::GetLink($k, $v, $html,'CMain::', '$APPLICATION->');
							$html = CBitrixLiveapi::GetLink($k, $v, $html,'CDatabase::', '$DB->');
						}

						$curClass = ($p0 = strpos($k,'::')) ? substr($k,0,$p0) : false;
						if ($curClass && $lastClass != $curClass)
						{
							$lastClass = $curClass;
							$html = preg_replace('#(new&nbsp;</span><span[^>]*>)'.$curClass.'#i',"$1".'<a href="bitrix.liveapi_live_api.php?module_id='.$module.'&class='.htmlspecialchars($curClass).'">'.$curClass.'</a>',$html);
						}
					}
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
				$html = str_replace($_REQUEST['name'],'<a href="bitrix.liveapi_live_api.php?module_id='.$module.'&class='.$class.'">'.$class.'</a>'.substr($_REQUEST['name'],$p),$html);
			}
			$html = str_ireplace('public&nbsp;','<span style="color:#933;font-weight:bold">public</span>&nbsp;',$html);
			$html = str_ireplace('private&nbsp;','<span style="color:#933;font-weight:bold">private</span>&nbsp;',$html);
			$html = str_ireplace('protected&nbsp;','<span style="color:#933;font-weight:bold">protected</span>&nbsp;',$html);
			$html = str_ireplace('static&nbsp;','<span style="color:#333;font-weight:bold">static</span>&nbsp;',$html);
		}

		return $html;
	}

	function colorize($func, $ar, $class = false, $module = '')
	{
		$link = "bitrix.liveapi_live_src.php?file=$ar[FILE]&offset=$ar[OFFSET]&name=$func&line=$ar[LINE]";
		if ($c = strpos($func, "::"))
		{
			if ($class && substr($func,0,$c) != $class)
				return;
			$func = '<a href="bitrix.liveapi_live_api.php?module_id='.$module.'&class='.substr($func,0,$c).'" class=class>'.substr($func,0,$c).'</span>::<a href="'.$link.'" target=_blank><span class=method>'.substr($func,$c+2).'</span></a>';
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
			'<a href="bitrix.liveapi_live_src.php?file='.$v['FILE'].'&offset='.$v['OFFSET'].'&name='.$code.'&line='.$v['LINE'].'">'.$s_code.'</a>',
			$html
		);
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
