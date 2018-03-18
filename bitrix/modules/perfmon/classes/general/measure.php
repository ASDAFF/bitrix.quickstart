<?
IncludeModuleLangFile(__FILE__);

class CPerfomanceMeasure
{
	function GetPHPCPUMark()
	{
		$res = array();
		for($j = 0; $j < 4; $j++)
		{
			$N1 = 0;
			$N2 = 0;

			$s1 = getmicrotime();
			for($i = 0; $i < 1000000; $i++)
			{
			}
			$e1 = getmicrotime();
			$N1 = $e1 - $s1;

			$s2 = getmicrotime();
			for($i = 0; $i < 1000000; $i++)
			{
				//This is one op
				$k++;$k--;
				$k++;$k--;
			}
			$e2 = getmicrotime();
			$N2 = $e2 - $s2;


			if($N2 > $N1)
				$res[] = 1 / ($N2 - $N1);
		}

		if(count($res))
			return array_sum($res)/doubleval(count($res));
		else
			return 0;
	}

	function GetPHPFilesMark()
	{
		$res = array();
		$file_name = $_SERVER["DOCUMENT_ROOT"]."/".COption::GetOptionString("main","upload_dir","/upload/"). "/perfmon#i#.php";
		$content = "<?\$s='".str_repeat("x", 1024)."';?><?/*".str_repeat("y", 1024)."*/?><?\$r='".str_repeat("z", 1024)."';?>";

		for($j = 0; $j < 4; $j++)
		{
			$N1 = 0;
			$N2 = 0;

			$s1 = getmicrotime();
			for($i = 0; $i < 100; $i++)
			{
				$fn = str_replace("#i#", $i, $file_name);
			}
			$e1 = getmicrotime();
			$N1 = $e1 - $s1;

			$s2 = getmicrotime();
			for($i = 0; $i < 100; $i++)
			{
				//This is one op
				$fn = str_replace("#i#", $i, $file_name);
				$fh = fopen($fn, "wb");
				fwrite($fh, $content);
				fclose($fh);
				include($fn);
				unlink($fn);
			}
			$e2 = getmicrotime();
			$N2 = $e2 - $s2;


			if($N2 > $N1)
				$res[] = 100 / ($N2 - $N1);
		}

		if(count($res))
			return array_sum($res)/doubleval(count($res));
		else
			return 0;
	}

	function GetPHPMailMark()
	{
		$res = array();
		$addr = "hosting_test@bitrix.ru";
		$subj = "Bitrix server test";
		$body = "This is test message. Delete it.";

		$s1 = getmicrotime();
		bxmail($addr, $subj, $body);
		$e1 = getmicrotime();
		$t1 = $e1 - $s1;

		return $t1;
	}

	function GetDBMark($type)
	{
		global $DB;

		$res = array();
		switch($type)
		{
		case "read":
			$strSql = "select * from b_perf_test WHERE ID = #i#";
			$bFetch = true;
			break;
		case "update":
			$strSql = "update b_perf_test set REFERENCE_ID = ID+1, NAME = '".str_repeat("y", 200)."' WHERE ID = #i#";
			$bFetch = false;
			break;
		default:
			$DB->Query("truncate table b_perf_test");
			$strSql = "insert into b_perf_test (REFERENCE_ID, NAME) values (#i#-1, '".str_repeat("x", 200)."')";
			$bFetch = false;
		}

		for($j = 0; $j < 4; $j++)
		{
			$N1 = 0;
			$N2 = 0;

			$s1 = getmicrotime();
			for($i = 0; $i < 100; $i++)
			{
				$sql = str_replace("#i#", $i, $strSql);
			}
			$e1 = getmicrotime();
			$N1 = $e1 - $s1;

			$s2 = getmicrotime();
			for($i = 0; $i < 100; $i++)
			{
				//This is one op
				$sql = str_replace("#i#", $i, $strSql);
				$rs = $DB->Query($sql);
				if($bFetch)
					$rs->Fetch();
			}
			$e2 = getmicrotime();
			$N2 = $e2 - $s2;


			if($N2 > $N1)
				$res[] = 100 / ($N2 - $N1);
		}

		if(count($res))
			return array_sum($res)/doubleval(count($res));
		else
			return 0;
	}

	function GetAccelerator()
	{
		if(function_exists('accelerator_reset'))
			return new CPerfAccelZend;
		elseif(extension_loaded('apc'))
			return new CPerfAccelAPC;
		elseif(extension_loaded('eAccelerator'))
			return new CPerfAccelEAccel;
		elseif(extension_loaded('xcache'))
			return new CPerfAccelXCache;
		elseif(extension_loaded('wincache'))
			return new CPerfAccelWinCache;
		else
			return false;
	}
}

class CPerfAccel
{
	var $enabled, $cache_ttl, $max_file_size, $check_mtime, $memory_total, $memory_used, $cache_limit;

	function __construct($enabled, $cache_ttl, $max_file_size, $check_mtime, $memory_total, $memory_used, $cache_limit=-1)
	{
		return $this->CPerfAccel($enabled, $cache_ttl, $max_file_size, $check_mtime, $memory_total, $memory_used, $cache_limit);
	}

	function CPerfAccel($enabled, $cache_ttl, $max_file_size, $check_mtime, $memory_total, $memory_used, $cache_limit=-1)
	{
		$this->enabled = $enabled;
		$this->cache_ttl = $cache_ttl;
		$this->max_file_size = $max_file_size;
		$this->check_mtime = $check_mtime;
		$this->memory_total = $memory_total;
		$this->memory_used = $memory_used;
		$this->cache_limit = $cache_limit;
	}

	function IsWorking()
	{
		if(!$this->enabled)
			return false;

		if($this->cache_ttl == 0)
			return false;

		if($this->max_file_size >= 0)
		{
			if($this->max_file_size < 4*1024*1024)
				return false;
		}

		if(!$this->check_mtime)
			return false;

		if($this->memory_used >= 0)
		{
			//Check for 10% free
			if(($this->memory_used / $this->memory_total) > 0.9)
				return false;
		}
		else
		{
			//Or at least 40M total when no used memory stat available
			if($this->memory_total < 40*1024*1024)
				return false;
		}

		if($this->cache_limit == 0)
			return false;

		return true;
	}

	function GetRecommendations()
	{
		$arResult = array();

		$arParams = $this->GetParams();

		if(array_key_exists("enabled", $arParams))
		{
			$is_ok = $this->enabled;
			foreach($arParams["enabled"] as $ar)
			{
				if(!isset($ar["IS_OK"]))
					$ar["IS_OK"] = $is_ok;
				$arResult[] = $ar;
			}
		}

		if(array_key_exists("cache_ttl", $arParams))
		{
			$is_ok =  $this->cache_ttl != 0;
			foreach($arParams["cache_ttl"] as $ar)
			{
				$ar["IS_OK"] = $is_ok;
				$arResult[] = $ar;
			}
		}

		if(array_key_exists("max_file_size", $arParams) && $this->max_file_size >= 0)
		{
			$is_ok = $this->max_file_size >= 4*1024*1024;
			foreach($arParams["max_file_size"] as $ar)
			{
				$ar["IS_OK"] = $is_ok;
				$arResult[] = $ar;
			}
		}

		if(array_key_exists("check_mtime", $arParams))
		{
			$is_ok = $this->check_mtime;
			foreach($arParams["check_mtime"] as $ar)
			{
				$ar["IS_OK"] = $is_ok;
				$arResult[] = $ar;
			}
		}

		if(array_key_exists("memory_pct", $arParams) && $this->memory_used >= 0)
		{
			if($this->memory_total > 0)
			{
				//Check for 10% free
				$is_ok = ($this->memory_used / $this->memory_total) <= 0.9;
				foreach($arParams["memory_pct"] as $ar)
					$arResult[] = array(
						"PARAMETER" => $ar["PARAMETER"],
						"VALUE" => GetMessage("PERFMON_MEASURE_MEMORY_USAGE", array("#percent#"=>number_format(($this->memory_used / $this->memory_total)*100, 2))),
						"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_CACHE_REC"),
						"IS_OK" => $is_ok,
					);
			}
			else
			{
				foreach($arParams["memory_pct"] as $ar)
					$arResult[] = array(
						"PARAMETER" => $ar["PARAMETER"],
						"VALUE" => "",
						"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_GREATER_THAN_ZERO_REC"),
						"IS_OK" => false,
					);
			}
		}
		elseif(array_key_exists("memory_abs", $arParams))
		{
			//Or at least 40M total when no used memory stat available
			$is_ok = $this->memory_total >= 40*1024*1024;
			foreach($arParams["memory_abs"] as $ar)
			{
				$ar["IS_OK"] = $is_ok;
				$arResult[] = $ar;
			}
		}

		if(array_key_exists("cache_limit", $arParams))
		{
			$is_ok =  $this->cache_limit != 0;
			foreach($arParams["cache_limit"] as $ar)
			{
				$ar["IS_OK"] = $is_ok;
				$arResult[] = $ar;
			}
		}

		return $arResult;
	}

	function unformat($str)
	{
		$str = strtolower($str);
		$res = intval($str);
		$suffix = substr($str, -1);
		if($suffix == "k")
			$res *= 1024;
		elseif($suffix == "m")
			$res *= 1048576;
		elseif($suffix == "g")
			$res *= 1048576*1024;
		return $res;
	}
}

class CPerfAccelZend extends CPerfAccel
{
	function __construct()
	{
		return $this->CPerfAccelZend();
	}

	function CPerfAccelZend()
	{
		$zend_enable = ini_get('zend_optimizerplus.enable');
		$zend_mtime  = ini_get('zend_optimizerplus.validate_timestamps');

		parent::CPerfAccel(
			strtolower($zend_enable) == "on" || $zend_enable == "1",
			-1,
			-1,
			strtolower($zend_mtime) == "on" || $zend_mtime == "1",
			intval(ini_get('zend_optimizerplus.memory_consumption'))*1024*1024,
			-1
		);
	}

	function GetRecommendations()
	{
		$arResult = parent::GetRecommendations();

		if(function_exists('accelerator_get_status'))
		{
			$is_ok = is_array(accelerator_get_status());

			array_unshift($arResult, array(
				"PARAMETER" => GetMessage("PERFMON_MEASURE_OPCODE_CACHING"),
				"IS_OK" => $is_ok,
				"VALUE" => $is_ok? GetMessage("PERFMON_MEASURE_UP_AND_RUNNING"): GetMessage("PERFMON_MEASURE_DISABLED"),
				"RECOMMENDATION" => "",
			));
		}

		return $arResult;
	}

	function GetParams()
	{
		return array(
			"enabled" => array(
				array(
					"PARAMETER" => 'zend_optimizerplus.enable',
					"VALUE" => ini_get('zend_optimizerplus.enable'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_SET_REC", array("#value#" => "On"))
				),
			),
			"check_mtime" => array(
				array(
					"PARAMETER" => 'zend_optimizerplus.validate_timestamps',
					"VALUE" => ini_get('zend_optimizerplus.validate_timestamps'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_SET_REC", array("#value#" => "On"))
				),
			),
			"memory_abs" => array(
				array(
					"PARAMETER" => 'zend_optimizerplus.memory_consumption',
					"VALUE" => ini_get('zend_optimizerplus.memory_consumption'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_EQUAL_OR_GREATER_THAN_REC", array("#value#" => "40"))
				),
			),
		);
	}
}

class CPerfAccelAPC extends CPerfAccel
{
	var $is_enabled = null;
	var $is_cache_by_default = null;

	function __construct()
	{
		return $this->CPerfAccelAPC();
	}

	function CPerfAccelAPC()
	{
		$apc_enabled = strtolower(ini_get('apc.enabled'));
		$this->is_enabled = !($apc_enabled=="0" || $apc_enabled=="off");
		$apc_cache_by_default = strtolower(ini_get('apc.cache_by_default'));
		$this->is_cache_by_default = !($apc_cache_by_default=="0" || $apc_cache_by_default=="off");
		$apc_stat = strtolower(ini_get('apc.stat'));
		$memory = apc_sma_info(true);

		parent::CPerfAccel(
			$this->is_enabled && $this->is_cache_by_default,
			intval(ini_get('apc.ttl')),
			CPerfAccel::unformat(ini_get('apc.max_file_size')),
			!($apc_stat=="0" || $apc_stat=="off"),
			$memory["seg_size"],
			$memory["seg_size"] - $memory["avail_mem"]
		);
	}

	function GetParams()
	{
		return array(
			"enabled" => array(
				array(
					"PARAMETER" => 'apc.enabled',
					"VALUE" => ini_get('apc.enabled'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_SET_REC", array("#value#" => "1")),
					"IS_OK" => $this->is_enabled,
				),
				array(
					"PARAMETER" => 'apc.cache_by_default',
					"VALUE" => ini_get('apc.cache_by_default'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_SET_REC", array("#value#" => "1")),
					"IS_OK" => $this->is_cache_by_default,
				),
			),
			"cache_ttl" => array(
				array(
					"PARAMETER" => 'apc.ttl',
					"VALUE" => ini_get('apc.ttl'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_GREATER_THAN_ZERO_REC"),
				),
			),
			"max_file_size" => array(
				array(
					"PARAMETER" => 'apc.max_file_size',
					"VALUE" => ini_get('apc.max_file_size'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_EQUAL_OR_GREATER_THAN_REC", array("#value#" => "4M")),
				),
			),
			"check_mtime" => array(
				array(
					"PARAMETER" => 'apc.stat',
					"VALUE" => ini_get('apc.stat'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_SET_REC", array("#value#" => "1")),
				),
			),
			"memory_pct" => array(
				array(
					"PARAMETER" => 'apc.shm_size ('.GetMessage("PERFMON_MEASURE_CURRENT_VALUE", array("#value#" => ini_get('apc.shm_size'))).')',
					"VALUE" => ini_get('apc.shm_size'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_EQUAL_OR_GREATER_THAN_REC", array("#value#" => "40")),
				),
			),
		);
	}
}

class CPerfAccelEAccel extends CPerfAccel
{
	function __construct()
	{
		return $this->CPerfAccelEAccel();
	}

	function CPerfAccelEAccel()
	{
		if(function_exists("eaccelerator_info"))
			$memory = eaccelerator_info();
		else
			$memory = array(
				"memorySize" => intval(ini_get('eaccelerator.shm_size'))*1024*1024,
				"memoryAllocated" => -1,
			);

		$obCache = new CPHPCache;
		if(strtolower(get_class($obCache->_cache)) == "cphpcacheeaccelerator")
			$cache_limit = intval(ini_get('eaccelerator.shm_max'));
		else
			$cache_limit = -1;

		parent::CPerfAccel(
			ini_get('eaccelerator.enable') != "0",
			intval(ini_get('eaccelerator.shm_ttl')),
			-1,
			ini_get('eaccelerator.check_mtime') != "0",
			$memory["memorySize"],
			$memory["memoryAllocated"],
			$cache_limit
		);
	}

	function GetParams()
	{
		$res = array(
			"enabled" => array(
				array(
					"PARAMETER" => 'eaccelerator.enable',
					"VALUE" => ini_get('eaccelerator.enable'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_SET_REC", array("#value#" => "1")),
				),
			),
			"cache_ttl" => array(
				array(
					"PARAMETER" => 'eaccelerator.shm_ttl',
					"VALUE" => ini_get('eaccelerator.shm_ttl'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_GREATER_THAN_ZERO_REC"),
				),
			),
			"check_mtime" => array(
				array(
					"PARAMETER" => 'eaccelerator.check_mtime',
					"VALUE" => ini_get('eaccelerator.check_mtime'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_SET_REC", array("#value#" => "1")),
				),
			),
			"memory_pct" => array(
				array(
					"PARAMETER" => 'eaccelerator.shm_size ('.GetMessage("PERFMON_MEASURE_CURRENT_VALUE", array("#value#" => ini_get('eaccelerator.shm_size'))).')',
					"VALUE" => ini_get('eaccelerator.shm_size'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_EQUAL_OR_GREATER_THAN_REC", array("#value#" => "40")),
				),
			),
		);
		$obCache = new CPHPCache;
		if(strtolower(get_class($obCache->_cache)) == "cphpcacheeaccelerator")
		{
			$res["cache_limit"] = array(
				array(
					"PARAMETER" => 'eaccelerator.shm_max',
					"VALUE" => ini_get('eaccelerator.shm_max'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_GREATER_THAN_ZERO_REC"),
				),
			);
		}
		return $res;
	}
}

class CPerfAccelXCache extends CPerfAccel
{
	function __construct()
	{
		return $this->CPerfAccelXCache();
	}

	function CPerfAccelXCache()
	{
		$xcache_stat = ini_get('xcache.stat');

		parent::CPerfAccel(
			ini_get('xcache.cacher') != "0",
			intval(ini_get('xcache.ttl')),
			-1,
			!($xcache_stat=="0" || strtolower($xcache_stat)=="off"),
			CPerfAccel::unformat(ini_get('xcache.size')),
			-1
		);
	}

	function GetParams()
	{
		return array(
			"enabled" => array(
				array(
					"PARAMETER" => 'xcache.cacher',
					"VALUE" => ini_get('apc.enabled'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_SET_REC", array("#value#" => "1")),
				),
			),
			"cache_ttl" => array(
				array(
					"PARAMETER" => 'xcache.ttl',
					"VALUE" => ini_get('xcache.ttl'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_GREATER_THAN_ZERO_REC"),
				),
			),
			"check_mtime" => array(
				array(
					"PARAMETER" => 'xcache.stat',
					"VALUE" => ini_get('xcache.stat'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_SET_REC", array("#value#" => "1")),
				),
			),
			"memory_abs" => array(
				array(
					"PARAMETER" => 'xcache.size',
					"VALUE" => ini_get('xcache.size'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_EQUAL_OR_GREATER_THAN_REC", array("#value#" => "40M")),
				),
			),
		);
	}
}

class CPerfAccelWinCache extends CPerfAccel
{
	function __construct()
	{
		return $this->CPerfAccelWinCache();
	}

	function CPerfAccelWinCache()
	{
		$wincache_enabled = ini_get('wincache.ocenabled');
		$memory = wincache_ocache_meminfo();

		parent::CPerfAccel(
			!($wincache_enabled=="0" || strtolower($wincache_enabled)=="off"),
			-1,
			-1,
			true, //Because there is no way to turn on check file mtime we'll assume it's ok
			$memory["memory_total"],
			$memory["memory_total"] - $memory["memory_free"]
		);
	}

	function GetParams()
	{
		return array(
			"enabled" => array(
				array(
					"PARAMETER" => 'wincache.ocenabled',
					"VALUE" => ini_get('wincache.ocenabled'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_SET_REC", array("#value#" => "1")),
				),
			),
			"memory_pct" => array(
				array(
					"PARAMETER" => 'wincache.ocachesize ('.GetMessage("PERFMON_MEASURE_CURRENT_VALUE", array("#value#" => ini_get('wincache.ocachesize'))).')',
					"VALUE" => ini_get('wincache.ocachesize'),
					"RECOMMENDATION" => GetMessage("PERFMON_MEASURE_EQUAL_OR_GREATER_THAN_REC", array("#value#" => "40")),
				),
			),
		);
	}
}

?>