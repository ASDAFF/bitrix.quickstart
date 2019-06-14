<?
IncludeModuleLangFile(__FILE__);
Class CBitrixXdebug 
{
	public static function StartTrace()
	{
		global $USER, $DB, $APPLICATION;
		if (!extension_loaded('xdebug'))
			return;

		$module_id = 'bitrix.xdebug';

		if ((defined('ADMIN_MODULE_NAME') && ADMIN_MODULE_NAME == $module_id) || (CBitrixXdebug::BinStrpos($_SERVER['REQUEST_URI'], '/bitrix/admin/settings.php?mid=bitrix.xdebug') === 0))
			return;

		if (!COption::GetOptionString($module_id, 'start_trace_cookie', 0) || !$_COOKIE['BITRIX_XDEBUG_TRACE'])
		{
			$trace_active_time = COption::GetOptionString($module_id, 'trace_active_time', 0);
			if ($trace_active_time < time())
				return;

			if ($start_trace_condition = trim(COption::GetOptionString($module_id, 'start_trace_condition', '')))
			{
				if (!eval('return '.$start_trace_condition.';'))
					return false;
			}
		}


		ini_set('xdebug.collect_includes', 1);
		ini_set('xdebug.trace_format', 0);
		ini_set('xdebug.show_mem_delta', 0);

		ini_set('xdebug.trace_output_dir', COption::GetOptionString($module_id, 'xdebug.trace_output_dir', ini_get('xdebug.trace_output_dir')));
		ini_set('xdebug.trace_output_name', COption::GetOptionString($module_id, 'xdebug.trace_output_name', 'trace.%t'));
		ini_set('xdebug.collect_params', COption::GetOptionString($module_id, 'xdebug.collect_params', 3));
		ini_set('xdebug.collect_return', COption::GetOptionString($module_id, 'xdebug.collect_return', 1));

		$collect_assignments = COption::GetOptionString($module_id, 'xdebug.collect_assignments', 1);
		ini_set('xdebug.collect_assignments', 1);
		xdebug_start_trace();
/*
*
* —ейчас вы в самом начале трейса, при этом часть кода уже отработала. 
* Ёто происходит потому, что запуск трассировки делаетс€ на событии битрикс OnPageStart. 
* ≈сли вам требуетс€ получить полный трейс, включите опцию xdebug.auto_trace в php.ini или 
* добавьте вызов xdebug_start_trace() в самое начало файла дл€ отладки.
*
* ƒалее определ€ютс€ переменные, чтобы вы могли получить информацию об услови€х запуска скрипта.
*
*/
		$PAGE = $_SERVER['REQUEST_METHOD'].' '.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$TIME = date('d.m.Y H:i:s');
		$INFO = array(
			'_SERVER' => $_SERVER,
			'_GET' => $_GET,
			'_POST'=> $_POST,
			'_COOKIE' => $_COOKIE
		);
		if ($collect_assignments == 0)
			ini_set('xdebug.collect_assignments', 0);
	}

	public function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
			return;

		$MODULE_ID = basename(dirname(__FILE__));
		$aMenu = array(
			//"parent_menu" => "global_menu_services",
			"parent_menu" => "global_menu_settings",
			"section" => $MODULE_ID,
			"sort" => 50,
			"text" => GetMessage("BITRIX_XDEBUG_OTLADCIK"),
			"title" => '',
//			"url" => "partner_modules.php?module=".$MODULE_ID,
			"icon" => "",
			"page_icon" => "",
			"items_id" => $MODULE_ID."_items",
			"more_url" => array(),
			"items" => array()
		);

		if (file_exists($path = dirname(__FILE__).'/admin'))
		{
			if ($dir = opendir($path))
			{
				$aMenu['items'][] = array(
					'text' => GetMessage("BITRIX_XDEBUG_OBZOR"),
					'url' => $MODULE_ID.'_browse.php',
					'module_id' => $MODULE_ID,
					"title" => "",
				);
				$aMenu['items'][] = array(
					'text' => GetMessage("BITRIX_XDEBUG_OTLADKA"),
					'url' => $MODULE_ID.'_tracer.php',
					'module_id' => $MODULE_ID,
					"title" => "",
				);
				$aMenu['items'][] = array(
					'text' => GetMessage("BITRIX_XDEBUG_PROFILIROVANIE"),
					'url' => $MODULE_ID.'_profiler.php',
					'module_id' => $MODULE_ID,
					"title" => "",
				);
			}
		}
		$aModuleMenu[] = $aMenu;
	}

	public static function BinStrpos($a, $b, $c = 0)
	{
		if (ini_get('mbstring.func_overload') > 1)
		{
			if (function_exists('mb_orig_strpos'))
				return mb_orig_strpos($a, $b, $c);
			return mb_strpos($a, $b, $c, 'latin1');
		}
		return strpos($a, $b, $c);
	}

	public static function GetStringCharset($str)
	{
		if (preg_match("/[\xe0\xe1\xe3-\xff]/",$str))
			return 'windows-1251';
		$str0 = $GLOBALS['APPLICATION']->ConvertCharset($str, 'utf8', 'cp1251');
		if (preg_match("/[\xe0\xe1\xe3-\xff]/",$str0,$regs))
			return 'utf8';
		return '';
	}

	public static function FixCharset($str)
	{
		global $APPLICATION;
		if (($charset = CBitrixXdebug::GetStringCharset($str)) != 'ascii')
		{
			if ($charset != strtolower(str_replace('-', '' , LANG_CHARSET)))
				$str = $APPLICATION->ConvertCharset($str, $charset, LANG_CHARSET);
		}
		return $str;
	}

	public static function ClearOldProfiles()
	{
		global $DB;
		$rs = $DB->Query('SELECT * FROM b_xdebug_trace');
		while($f = $rs->Fetch())
		{
			if (!file_exists($f['trace']))
				CXDProfiler::ClearTrace($f['trace']);
		}
		return "CBitrixXdebug::ClearOldProfiles();";
	}

	public static function HumanSize($s)
	{
		$i = 0;
		$ar = array('b','kb','M','G');
		while($s > 1024)
		{
			$s /= 1024;
			$i++;
		}
		return round($s,1).'&nbsp;'.$ar[$i];
	}
}

class CXDTracer
{
	var $buffer_size = 1024000;
	var $seek = 0;
	var $pos = 0;
	var $last_pos;
	var $depth = 0;
	var $f;
	var $feof;
	var $XDResult = null;
	var $cntNeedResult = 0;
	var $trace_charset = '';

	var $text_search_pos = 0;

	var $search_depth;
	var $search_result;
	var $text_search_depth;
	var $text_search_file;
	var $text_search_func;
	var $text_search_var;

	var $arStack;

	public function Start($file, $seek = 0)
	{
		if (!$f = fopen($file, 'r'))
			return false;

		if ($seek > $this->buffer_size)
			fseek($f, $seek - $this->buffer_size);

		$this->f = $f;
		$this->pos = ftell($this->f);
		$this->seek = $seek;

		return true;
	}

	public function GetLine()
	{
		if (($this->text_search_file || $this->text_search_func || $this->text_search_var) && ftell($this->f) > $this->text_search_pos)
		{
			while (true)
			{
				$pos = ftell($this->f);
				$str = fread($this->f, 100000).fgets($this->f);

				if ($this->text_search_var && !$this->trace_charset && preg_match('#[^\x00-\x7f]#', $str)) // non ASCII character detected
					$this->trace_charset = CBitrixXdebug::GetStringCharset($str);

				if ($this->trace_charset)
					$str = $GLOBALS['APPLICATION']->ConvertCharset($str, $this->trace_charset, LANG_CHARSET);

				if (
					($this->text_search_file && false !== CBitrixXdebug::BinStrpos($str, $this->text_search_file))
					||
					($this->text_search_func && false !== CBitrixXdebug::BinStrpos($str, $this->text_search_func))
					||
					($this->text_search_var && false !== CBitrixXdebug::BinStrpos($str, $this->text_search_var))
				)
				{
					$this->text_search_pos = ftell($this->f);
					fseek($this->f, $pos);
					break;
				}

				if (feof($this->f))
					return false;
			}
		}

		$this->last_pos = ftell($this->f);
		$l = fgets($this->f);
		if ($this->trace_charset)
			$l = $GLOBALS['APPLICATION']->ConvertCharset($l, $this->trace_charset, LANG_CHARSET);
		return $l;

	}

	public function Scan()
	{
		if ($this->text_search_var && (!defined('BX_UTF') || !BX_UTF))
			$this->text_search_var = $GLOBALS['APPLICATION']->ConvertCharset($this->text_search_var, 'UTF-8', LANG_CHARSET);

		while(false !== $l = $this->GetLine())
		{
			$XRec = new CXDRecord;
			if ($XRec->InitFromLine($l))
			{
				if ($this->pos <= $this->seek) // searching for function
				{
					if ($XRec->type == CXDRecord::TYPE_FUNC || $XRec->type == CXDRecord::TYPE_VAR)
					{
						$XRec->pos = $this->last_pos;

						$pos = ftell($this->f);
						if ($this->search_depth == -1 && $pos == $this->seek)
						{
							$this->search_depth = 0;
							fseek($this->f, $this->pos);
							$this->seek = $this->pos - 1;
						}
						else
						{
							$this->arStack[$XRec->depth] = $XRec;

							if ($this->search_depth > 0 && $XRec->depth > $this->search_depth)
								continue;

							if ($this->text_search_file && false === CBitrixXdebug::BinStrpos($XRec->file.':'.$XRec->line, $this->text_search_file))
								continue;

							if ($this->text_search_func)
							{
								if ($this->bool_search_by_func)
								{
									if (false === CBitrixXdebug::BinStrpos($XRec->text_func_name, $this->text_search_func))
										continue;
								}
								elseif (false === CBitrixXdebug::BinStrpos($XRec->text_var_name, $this->text_search_func))
									continue;
							}

							if ($this->text_search_var && false === CBitrixXdebug::BinStrpos($XRec->text_func_params, $this->text_search_var) && false === CBitrixXdebug::BinStrpos($XRec->text_var_val, $this->text_search_var))
								continue;

							$this->pos = $pos;

							$this->depth = $XRec->depth;
							$this->file = $XRec->file;
							$this->line = $XRec->line;
						}

						if ($this->pos > $this->seek)
						{
							if ($this->search_result)
							{
								for($i = 1; $i <= $this->depth; $i++)
								{
									if (($XRec0 = $this->arStack[$i]) && $XRec0->type == CXDRecord::TYPE_FUNC && !$XRec0->XDResult)
										$this->cntNeedResult++;
								}
							}
							if ($this->cntNeedResult == 0)
								break;
						}
					}
				}
				elseif ($this->search_result) // searching for result
				{
					$pos = ftell($this->f);
					if ($XRec->type == CXDRecord::TYPE_FUNC)
					{
						$this->SetEmptyResult($XRec);
						if ($this->cntNeedResult == 0)
							break;
					}
					elseif ($XRec->type == CXDRecord::TYPE_RESULT && $XRec->depth <= $this->depth)
					{
						if (($XRec0 = $this->GetRecByDepth($XRec)) && $XRec0->type == CXDRecord::TYPE_FUNC)
						{
							if ($XRec0->SetResult($XRec))
								$this->cntNeedResult--;

							$this->SetEmptyResult($XRec);
							if ($this->cntNeedResult == 0)
								break;
						}
					}
					if ($pos > $this->pos + $this->buffer_size)
						break;
				}
			}
			else
			{
				// AddMessage2Log($l);
				if ($debug = 0) // DEBUG 
					die($l);
			}
		}
		$this->feof = feof($this->f);
		fclose($this->f);
		return true;
	}

	public function GetRecByDepth($XRec)
	{
		if ($this->arStack[$XRec->depth])
			return $this->arStack[$XRec->depth];
		return false;
	}

	public function SetEmptyResult($XRec)
	{
		$depth = $XRec->depth;

		foreach($this->arStack as $XRec0)
		{
			if ($XRec0->depth >= $depth && $XRec0->depth <= $this->depth)
			{
				$XDResult = new CXDRecord;
				$XDResult->InitEmplyResult();
				$XDResult->depth = $XRec0->depth;

				if ($XRec0->SetResult($XDResult))
					$this->cntNeedResult--;
			}
		}
	}

	public static function GetTraceTime($file)
	{
		if (!$f = fopen($file, 'rb'))
			return 0;
		$t = 0;
		$s = 0;
		fseek($f, -1024, SEEK_END);
		fgets($f);
		while(false !== $l = fgets($f))
		{
			$s += strlen($l);
			if ($s > 1024)
				break;
			$XRec = new CXDRecord;
			if ($XRec->InitFromLine($l))
				$t = max($t, $XRec->time);
		}
		fclose($f);
		return $t;
	}
}

class CXDProfiler
{
	var $seek = 0;
	var $f;
	var $feof;
	var $pos;
	var $arStack;
	var $ProfileByTime = false;
	var $arResult = array();
	var $total_time = 0;
	var $total_mem = 0;
	var $arRecordBuffer = array();
	var $accuracy;

	var $trace_id;
	var $start_time;
	var $time_limit = 5;


	public function Start($file, $seek = 0)
	{
		global $DB;

		if (!$f = fopen($file, 'r'))
			return false;
		$this->f = $f;

		fseek($f, $seek);
		$this->seek = $seek;

		if (!self::GetTraceId($file))
			$DB->Query('INSERT INTO b_xdebug_trace (trace) VALUES ("'.$DB->ForSQL($file).'")');

		if (!$this->trace_id = self::GetTraceId($file))
			return false;

		$this->start_time = time();

		return true;
	}
	
	public function BreakTime()
	{
		return time() - $this->start_time > $this->time_limit;
	}

	public function Parse()
	{
		$pos = $fails = 0;
		while(false !== $l = fgets($this->f))
		{
			$XRec = new CXDRecord;
			if (!$XRec->InitFromLine($l))
			{
				if (++$fails > 100) // unknown file format
				{
					fclose($this->f);
					return true; 
				}
				$pos = ftell($this->f);
				continue;
			}
			$fails = 0;

			if ($XRec->type != CXDRecord::TYPE_FUNC)
			{
				$pos = ftell($this->f);
				continue;
			}

			$total_time = $XRec->time;
			$total_mem = max($total_mem, $XRec->mem);

			$XRec->pos = $pos; // позици€, с которой можно прочитать $XRec
			$pos = ftell($this->f);

			if ($XRec0)
			{
				(float) $XRec0->own_time = $XRec->time - $XRec0->time;
				if ($XRec0->own_time > 0)
				{
					$this->FlushRecordBuffer();
					$this->SetRecord($XRec0);
				}
				else
					$this->arRecordBuffer[] = $XRec0;
			}
			$XRec0 = $XRec;

			if (!$this->arRecordBuffer && $this->BreakTime())
				break;
		}

		$this->FlushRecordBuffer();
		if ($this->feof = $l === false)
		{
			// if ($XRec->type == CXDRecord::TYPE_FUNC) // Last function is time mark
			// 	$this->SetRecord($XRec);
			$this->pos = ftell($this->f);
		}
		else
			$this->pos = $XRec->pos; // вернутьс€ назад чтобы не потер€ть на следующий шаг $XRec

		foreach($this->arResult as $XRecS)
			$this->SaveRecord($XRecS);

		fclose($this->f);
		return $this->feof;
	}

	public function FlushRecordBuffer()
	{
		if ($this->arRecordBuffer)
		{
			$c = count($this->arRecordBuffer);
			foreach($this->arRecordBuffer as $XRecX)
			{
				if (!$this->accuracy)
					(float) $this->accuracy = 1 / pow(10, strlen(end(explode('.',$XRecX->time))));
				$XRecX->own_time = $this->accuracy / $c;
				$this->SetRecord($XRecX);
			}
			$this->arRecordBuffer = array();
		}
	}

	public function SetRecord($XRec0)
	{
		(float) $include_time = $XRec0->own_time;
		$id = $XRec0->GetId();
		if ($this->GetRecord($id))
		{
			$XRecS = &$this->arResult[$id];

			if ($XRec0->own_time > $XRecS->own_time / $XRecS->cnt)
				$XRecS->pos = $XRec0->pos; // save longest

			(float) $XRecS->own_time += $XRec0->own_time;
			$XRecS->cnt++;
		}
		else
			$this->arResult[$id] = $XRec0;

		$this->arStack[$XRec0->depth] = $id;
		for($i = 0;$i <= $XRec0->depth; $i++)
		{
			if (($id = $this->arStack[$i]) && $this->GetRecord($id))
				(float) $this->arResult[$id]->include_time += $include_time;
		}
	}

	public function GetRecord($id)
	{
		global $DB;

		if (!$this->arResult[$id])
		{
			$rs = $DB->Query('SELECT * FROM b_xdebug WHERE trace_id='.$this->trace_id.' AND id="'.$DB->ForSQL($id).'"');
			if (!$f = $rs->Fetch())
				return false;

			$XRec = new CXDRecord;
			$XRec->type = CXDRecord::TYPE_FUNC;

			foreach(array('pos', 'time', 'own_time', 'include_time', 'cnt', 'depth', 'file', 'line', 'text_func_name') as $var)
				$XRec->$var = $f[$var];

			$this->arResult[$id] = $XRec;
		}
		return $this->arResult[$id];
	}

	public function SaveRecord($XRec)
	{
		global $DB;

		$DB->Query('DELETE FROM b_xdebug WHERE trace_id='.$this->trace_id.' AND id="'.$DB->ForSQL($XRec->GetId()).'"');
		$arFields = array( 
			'id' => '"'.$DB->ForSQL($XRec->GetId()).'"',
			'trace_id' => intval($this->trace_id),
			'file' => '"'.$DB->ForSQL($XRec->file).'"',
			'line' => intval($XRec->line),
			'pos' => intval($XRec->pos),
			'text_func_name' => '"'.$DB->ForSQL($XRec->text_func_name).'"',
			'own_time' => floatval($XRec->own_time),
			'time' => floatval($XRec->time),
			'include_time' => floatval($XRec->include_time),
			'cnt' => intval($XRec->cnt),
			'depth' => intval($XRec->depth)
		);

		$DB->Insert('b_xdebug', $arFields);
	}

	public static function ClearTrace($trace)
	{
		global $DB;
		if (!$id = self::GetTraceId($trace))
			return false;

		$DB->Query('DELETE FROM b_xdebug WHERE trace_id='.$id);
		$rs = $DB->Query('DELETE FROM b_xdebug_trace WHERE id='.$id);

		return $rs->SelectedRowsCount();
	}

	public static function GetTraceId($trace)
	{
		global $DB;
		$rs = $DB->Query('SELECT id FROM b_xdebug_trace WHERE trace="'.$DB->ForSQL($trace).'"');
		if ($f = $rs->Fetch())
			return $f['id'];
		return false;
	}
}

class CXDRecord
{
	const TYPE_FUNC = 'FUNCTION';
	const TYPE_VAR = 'VARIABLE';
	const TYPE_RESULT = 'RESULT';
	const TYPE_OTHER = 'OTHER';

	const TAB_SIZE = 5;
	const PAD_SIZE = 22;

	var $depth;
	var $type;
	var $file;
	var $line;
	var $pos;
	var $time;
	var $cnt = 1;
	var $own_time;
	var $include_time;
	var $mem;

	var $text_func_name;
	var $text_func_params;
	var $text_var_name;
	var $text_var_val;
	var $text_result;

	public function InitFromLine($l)
	{
		if (preg_match('#^ *([0-9\.]+) *([0-9]+)( *)-> ([a-z_0-9\-\>\:\\\\{}]+)\((.*)\) ?([a-z]?:?[^\(]+)\(([0-9]+)\) : eval#i',$l,$regs))
		{
			$this->type = self::TYPE_FUNC;
			list($all, $this->time, $this->mem, $depth_str, $this->text_func_name, $this->text_func_params, $this->file, $this->line) = $regs;
			$this->depth = $depth = 1 + (strlen($depth_str) - self::TAB_SIZE)/2;
			return true;
		}
		elseif (preg_match('#^ *([0-9\.]+) *([0-9]+)( *)-> ([a-z_0-9\-\>\:\\\\{}]+)\((.*)\) ?([a-z]?:?[^:]+):([0-9]+)$#i',$l,$regs))
		{
			$this->type = self::TYPE_FUNC;
			list($all, $this->time, $this->mem, $depth_str, $this->text_func_name, $this->text_func_params, $this->file, $this->line) = $regs;
			if (CBitrixXdebug::BinStrpos($this->file, ': ') === 0)
			{
				if (preg_match('#^ *([0-9\.]+) *([0-9]+)( *)-> ([a-z_0-9\-\>\:\\\\]+)\((.*)\) ([a-z]?:?[^:]+)\(([0-9]+)\)#i',$l,$regs))
					list($all, $this->time, $this->mem, $depth_str, $this->text_func_name, $this->text_func_params, $this->file, $this->line) = $regs;
				else
					ShowErrAndDie('Can\'t parse lambda function');
			}
			$this->depth = $depth = 1 + (strlen($depth_str) - self::TAB_SIZE)/2;
			return true;
		}
		elseif (preg_match('#^ *([0-9\.]+) *([0-9]+)$#i',$l,$regs)) // End time mark
		{
			$this->type = self::TYPE_FUNC;
			list($all, $this->time, $this->mem) = $regs;
			$this->depth = 0;
			return true;
		}
		elseif (preg_match('#^( *)>=> (.+)$#i',$l,$regs))
		{
			$this->type = self::TYPE_RESULT;
			list($all, $depth_str, $this->text_result) = $regs;
			$this->depth = $depth = 1 + (strlen($depth_str) - self::PAD_SIZE - self::TAB_SIZE)/2;
			return true;
		}
		elseif (preg_match('#^ *[0-9\.]+ *[0-9]+( *)>=> (.+)$#i',$l,$regs))
		{
			$this->type = self::TYPE_RESULT;
			list($all, $depth_str, $this->text_result) = $regs;
			$this->depth = $depth = 1 + (strlen($depth_str) - self::TAB_SIZE - 1)/2;
			return true;
		}
		elseif (preg_match('#^( *)=> \$?(.+) [^=]?= (.+) ([a-z]?:?[^:]+):([0-9]+)$#i',$l,$regs))
		{
			$this->type = self::TYPE_VAR;
			list($all, $depth_str, $this->text_var_name, $this->text_var_val, $this->file, $this->line) = $regs;
			$this->depth = $depth = 2 + (strlen($depth_str) - self::PAD_SIZE - self::TAB_SIZE)/2;
			return true;
		}
		elseif (preg_match('#^( *)=> \$?([^= ]+)([+-/*]{2}) ([a-z]?:?[^:]+):([0-9]+)$#i',$l,$regs))
		{
			$this->type = self::TYPE_VAR;
			list($all, $depth_str, $this->text_var_name, $this->text_var_val, $this->file, $this->line) = $regs;
			$this->depth = $depth = 2 + (strlen($depth_str) - self::PAD_SIZE - self::TAB_SIZE)/2;
			return true;
		}
		elseif (preg_match('#^( *)=> ([+-/*]{2})\$?([^= ]+) ([a-z]?:?[^:]+):([0-9]+)$#i',$l,$regs))
		{
			$this->type = self::TYPE_VAR;
			list($all, $depth_str, $this->text_var_val, $this->text_var_name, $this->file, $this->line) = $regs;
			$this->depth = $depth = 2 + (strlen($depth_str) - self::PAD_SIZE - self::TAB_SIZE)/2;
			return true;
		}
		elseif (preg_match('#^TRACE#i',$l,$regs))
		{
			// TRACE START | END
			$this->type = self::TYPE_OTHER;
			return true;
		}
		return false;
	}

	public function GetId()
	{
		if (!$this->Id)
			$this->Id = md5(strtolower($this->text_func_name).$this->file.$this->line);
		return $this->Id;
	}

	public function InitEmplyResult()
	{
		$this->type = CXDRecord::TYPE_RESULT;
		$this->text_result = null;
	}

	public function GetHTML()
	{
		$res = '';

		switch ($this->type)
		{
			case self::TYPE_FUNC:
				$title = htmlspecialcharsbx($this->text_func_params);
				$title = str_replace(",", ",\\n", $title);
				$res = '<span class=funcName>'.htmlspecialcharsbx(addslashes($this->text_func_name)).'(</span><span title=\"'.$title.'\">'.htmlspecialcharsbx(addslashes($this->text_func_params)).'</span><span class=funcName>)</span>';
			break;
			case self::TYPE_VAR:
				$title = htmlspecialcharsbx($this->text_var_val);
				$title = str_replace(",", ",\\n", $title);
				$res = '<span class=varS>$</span><span class=varName>'.htmlspecialcharsbx($this->text_var_name).'</span>';
				if (!preg_match('#^[+-/*]{2}$#', $this->text_var_val))
					$res .= ' = ';
				$res .= '<span class=varVal title=\"'.$title.'\">'.htmlspecialcharsbx($this->text_var_val).'</span>';
			break;
			case self::TYPE_RESULT:
				$res = $this->text_result === null ? null : '<span title=\"\">'.htmlspecialcharsbx($this->text_result).'</span>';
			break;
		}
		return $res;
	}

	public function SetResult($XDResult)
	{
		if ($this->type == self::TYPE_FUNC && $this->depth == $XDResult->depth && !$this->XDResult)
		{
			$this->XDResult = $XDResult;
			return true;
		}
		return false;
	}
}

function ShowErrAndDie($str)
{
	echo '<span style="color:red">'.$str.'</span>';
	die();
}

?>
