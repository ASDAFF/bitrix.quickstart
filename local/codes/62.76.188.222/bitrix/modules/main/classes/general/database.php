<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

class CAllDatabase
{
	var $db_Conn;
	var $debug;
	var $DebugToFile;
	var $ShowSqlStat;
	var $db_Error;
	var $db_ErrorSQL;
	var $result;
	var $type;
	var $arQueryDebug=array();

	static $arNodes = array();
	var $column_cache = Array();
	var $bModuleConnection;
	var $bNodeConnection;
	var $bMasterOnly = 0;
	/** @var CDatabase */
	var $obSlave = null;
	var $cntQuery = 0;
	var $timeQuery = 0;

	function StartUsingMasterOnly()
	{
		$this->bMasterOnly++;
	}

	function StopUsingMasterOnly()
	{
		$this->bMasterOnly--;
	}

	function GetDBNodeConnection($node_id, $bIgnoreErrors = false, $bCheckStatus = true)
	{
		global $DB;

		if(!array_key_exists($node_id, self::$arNodes))
		{
			if(CModule::IncludeModule('cluster'))
				self::$arNodes[$node_id] = CClusterDBNode::GetByID($node_id);
			else
				self::$arNodes[$node_id] = false;
		}
		$node = &self::$arNodes[$node_id];

		if(
			is_array($node)
			&& (
				!$bCheckStatus
				|| (
					$node["ACTIVE"] == "Y"
					&& ($node["STATUS"] == "ONLINE" || $node["STATUS"] == "READY")
				)
			)
			&& !isset($node["ONHIT_ERROR"])
		)
		{
			if(!array_key_exists("DB", $node))
			{
				$node_DB = new CDatabase;
				$node_DB->type = $DB->type;
				$node_DB->debug = $DB->debug;
				$node_DB->DebugToFile = $DB->DebugToFile;
				$node_DB->bNodeConnection = true;
				if($node_DB->Connect($node["DB_HOST"], $node["DB_NAME"], $node["DB_LOGIN"], $node["DB_PASSWORD"]))
				{
					if(defined("DELAY_DB_CONNECT") && DELAY_DB_CONNECT===true)
					{
						if($node_DB->DoConnect())
							$node["DB"] = $node_DB;
					}
					else
					{
						$node["DB"] = $node_DB;
					}
				}
			}

			if(array_key_exists("DB", $node))
				return $node["DB"];
		}

		if($bIgnoreErrors)
		{
			return false;
		}
		else
		{
			if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/dbconn_error.php"))
				include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/dbconn_error.php");
			else
				include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/dbconn_error.php");
			die();
		}
	}

	function GetModuleConnection($module_id, $bModuleInclude = false)
	{
		$node_id = COption::GetOptionString($module_id, "dbnode_id", "N");
		if(is_numeric($node_id))
		{
			if($bModuleInclude)
			{
				$status = COption::GetOptionString($module_id, "dbnode_status", "ok");
				if($status === "move")
					return false;
			}

			$moduleDB = CDatabase::GetDBNodeConnection($node_id, $bModuleInclude);

			if(is_object($moduleDB))
			{
				$moduleDB->bModuleConnection = true;
				return $moduleDB;
			}

			//There was an connection error
			if($bModuleInclude && CModule::IncludeModule('cluster'))
				CClusterDBNode::SetOffline($node_id);

			//TODO: unclear what to return when node went offline
			//in the middle of the hit.
			return false;
		}
		else
		{
			return $GLOBALS["DB"];
		}
	}

	function Connect($DBHost, $DBName, $DBLogin, $DBPassword)
	{
		//is extended
		return false;
	}

	function GetNowFunction()
	{
		return CDatabase::CurrentTimeFunction();
	}

	function GetNowDate()
	{
		return CDatabase::CurrentDateFunction();
	}

	function DateToCharFunction($strFieldName, $strType="FULL")
	{
		//is extended
	}

	function CharToDateFunction($strValue, $strType="FULL")
	{
		//is extended
	}

	function Concat()
	{
		//is extended
	}

	function Substr($str, $from, $length = null)
	{
		// works for mysql and oracle, redefined for mssql
		$sql = 'SUBSTR('.$str.', '.$from;

		if (!is_null($length))
		{
			$sql .= ', '.$length;
		}

		return $sql.')';
	}

	function IsNull($expression, $result)
	{
		//is extended
	}

	function Length($field)
	{
		//is extended
	}

	function ToChar($expr, $len=0)
	{
		return "CAST(".$expr." AS CHAR".($len > 0? "(".$len.")":"").")";
	}

	function DateFormatToPHP($format)
	{
		static $cache = array();
		if (!isset($cache[$format]))
		{
			$f = str_replace("YYYY", "Y", $format);		// 1999
			$f = str_replace("MMMM", "F", $f);		// January - December
			$f = str_replace("MM", "m", $f);		// 01 - 12
	//		$f = str_replace("M", "M", $f);			// Jan - Dec
			$old_f = $f = str_replace("DD", "d", $f);	// 01 - 31
			$f = str_replace("HH", "H", $f);		// 00 - 24
			if ($old_f === $f)
			{
				$f = str_replace("H", "h", $f);		// 01 - 12
			}
			$f = str_replace("TT", "A", $f);		// AM - PM
			$old_f = $f = str_replace("T", "a", $f);	// am - pm
			$f = str_replace("GG", "G", $f);		// 0 - 24
			if ($old_f === $f)
			{
				$f = str_replace("G", "g", $f);		// 1 - 12
			}
			$f = str_replace("MI", "i", $f);		// 00 - 59
			$cache[$format] = str_replace("SS", "s", $f);		// 00 - 59
		}
		return $cache[$format];
	}

	function FormatDate($strDate, $format="DD.MM.YYYY HH:MI:SS", $new_format="DD.MM.YYYY HH:MI:SS")
	{
		$strDate = trim($strDate);

		$new_format = str_replace("MI","I", strtoupper($new_format));
		$new_format = preg_replace("/([DMYIHGST])\\1+/is", "\\1", $new_format);
		$arParsedDate = ParseDateTime($strDate, $format);

		if (!$arParsedDate) return false;

		foreach ($arParsedDate as $k=>$v)
		{
			if(preg_match("/[^0-9]/", $v))
				$arParsedDate[$k] = CDatabase::ForSql($v, 10);
			else
				$arParsedDate[$k] = intval($v);
		}

		/*time hacks*/
		if (isset($arParsedDate["H"]))
		{
			$arParsedDate['HH'] = intval($arParsedDate["H"]);
			unset($arParsedDate["H"]);
		}
		elseif (isset($arParsedDate["GG"]))
		{
			$arParsedDate['HH'] = intval($arParsedDate["GG"]);
			unset($arParsedDate["GG"]);
		}
		elseif (isset($arParsedDate["G"]))
		{
			$arParsedDate['HH'] = intval($arParsedDate["G"]);
			unset($arParsedDate["G"]);
		}
		if (isset($arParsedDate['TT']) || isset($arParsedDate['T']))
		{
			$middletime = $arParsedDate['TT'] ? $arParsedDate['TT'] : $arParsedDate['T'];
			if (strcasecmp($middletime, 'pm')===0)
			{
				if ($arParsedDate['HH'] < 12)
					$arParsedDate['HH'] += 12;
			}
			else
			{
				if ($arParsedDate['HH'] == 12)
					$arParsedDate['HH'] = 0;
			}

			if (isset($arParsedDate['TT']))
			{
				unset($arParsedDate['TT']);
			}
			else
			{
				unset($arParsedDate['T']);
			}
		}

		if (isset($arParsedDate["MMMM"]))
		{
			if (is_numeric($arParsedDate["MMMM"]))
			{
				$arParsedDate['MM'] = intval($arParsedDate["MMMM"]);
			}
			else
			{
				$arParsedDate['MM'] = GetNumMonth($arParsedDate["MMMM"]);
				if (!$arParsedDate['MM'])
					$arParsedDate['MM'] = intval(date('m', strtotime($arParsedDate["MMMM"])));
			}
		}
		elseif (isset($arParsedDate["MM"]))
		{
			$arParsedDate['MM'] = intval($arParsedDate["MM"]);
		}
		elseif (isset($arParsedDate["M"]))
		{
			if (is_numeric($arParsedDate["M"]))
			{
				$arParsedDate['MM'] = intval($arParsedDate["M"]);
			}
			else
			{
				$arParsedDate['MM'] = GetNumMonth($arParsedDate["M"], true);
				if (!$arParsedDate['MM'])
					$arParsedDate['MM'] = intval(date('m', strtotime($arParsedDate["M"])));
			}
		}

		if (isset($arParsedDate["YYYY"]))
			$arParsedDate["YY"] = $arParsedDate["YYYY"];

		if (intval($arParsedDate["DD"])<=0 || intval($arParsedDate["MM"])<=0 || intval($arParsedDate["YY"])<=0) return false;

		$strResult = "";
		if(intval($arParsedDate["YY"])>1970 && intval($arParsedDate["YY"])<2038)
		{
			$ux_time = mktime(
					isset($arParsedDate["HH"])? intval($arParsedDate["HH"]): 0,
					isset($arParsedDate["MI"])? intval($arParsedDate["MI"]): 0,
					isset($arParsedDate["SS"])? intval($arParsedDate["SS"]): 0,
					intval($arParsedDate["MM"]),
					intval($arParsedDate["DD"]),
					intval($arParsedDate["YY"])
					);

			$new_format_len = strlen($new_format);
			for ($i=0; $i<$new_format_len; $i++)
			{
				$ch = substr($new_format, $i, 1);
				if ($ch=="D") $strResult .= date("d", $ux_time);
				elseif ($ch=="M") $strResult .= date("m", $ux_time);
				elseif ($ch=="Y") $strResult .= date("Y", $ux_time);
				elseif ($ch=="H") $strResult .= date("H", $ux_time);
				elseif ($ch=="G") $strResult .= date("h", $ux_time);
				elseif ($ch=="I") $strResult .= date("i", $ux_time);
				elseif ($ch=="S") $strResult .= date("s", $ux_time);
				elseif ($ch=="T") $strResult .= date("a", $ux_time);
				else $strResult .= $ch;
			}
		}
		else
		{
			if($arParsedDate["MM"]<1 || $arParsedDate["MM"]>12) $arParsedDate["MM"] = 1;
			$new_format_len = strlen($new_format);
			for ($i=0; $i<$new_format_len; $i++)
			{
				$ch = substr($new_format, $i, 1);
				if ($ch=="D") $strResult .= str_pad($arParsedDate["DD"], 2, "0", STR_PAD_LEFT);
				elseif ($ch=="M") $strResult .= str_pad($arParsedDate["MM"], 2, "0", STR_PAD_LEFT);
				elseif ($ch=="Y") $strResult .= str_pad($arParsedDate["YY"], 4, "0", STR_PAD_LEFT);
				elseif ($ch=="H") $strResult .= str_pad($arParsedDate["HH"], 2, "0", STR_PAD_LEFT);
				elseif ($ch=="I") $strResult .= str_pad($arParsedDate["MI"], 2, "0", STR_PAD_LEFT);
				elseif ($ch=="S") $strResult .= str_pad($arParsedDate["SS"], 2, "0", STR_PAD_LEFT);
				else $strResult .= $ch;
			}
		}

		return $strResult;
	}

	function Query($strSql, $bIgnoreErrors=false)
	{
		//is extended
		return null;
	}

	//query with CLOB
	function QueryBind($strSql, $arBinds, $bIgnoreErrors=false)
	{
		//is extended
		return $this->Query($strSql, $bIgnoreErrors);
	}

	function QueryLong($strSql, $bIgnoreErrors = false)
	{
		return null;
	}

	function ForSql($strValue, $iMaxLength=0)
	{
		//is extended
	}

	function PrepareInsert($strTableName, $arFields)
	{
		//is extended
	}

	function PrepareUpdate($strTableName, $arFields)
	{
		//is extended
	}

	function ParseSqlBatch($strSql, $bIncremental = False)
	{
		if(strtolower($this->type)=="mysql")
			$delimiter = ";";
		elseif(strtolower($this->type)=="mssql")
			$delimiter = "\nGO";
		else
			$delimiter = "(?<!\\*)/(?!\\*)";

		$strSql = trim($strSql);

		$ret = array();
		$str = "";

		do/**/
		{
			if(preg_match("%^(.*?)(['\"`#]|--|".$delimiter.")%is", $strSql, $match))
			{
				//Found string start
				if($match[2] == "\"" || $match[2] == "'" || $match[2] == "`")
				{
					$strSql = substr($strSql, strlen($match[0]));
					$str .= $match[0];
					//find a qoute not preceeded by \
					if(preg_match("%^(.*?)(?<!\\\\)".$match[2]."%s", $strSql, $string_match))
					{
						$strSql = substr($strSql, strlen($string_match[0]));
						$str .= $string_match[0];
					}
					else
					{
						//String falled beyong end of file
						$str .= $strSql;
						$strSql = "";
					}
				}
				//Comment found
				elseif($match[2] == "#" || $match[2] == "--")
				{
					//Take that was before comment as part of sql
					$strSql = substr($strSql, strlen($match[1]));
					$str .= $match[1];
					//And cut the rest
					$p = strpos($strSql, "\n");
					if($p === false)
					{
						$p1 = strpos($strSql, "\r");
						if($p1 === false)
							$strSql = "";
						elseif($p < $p1)
							$strSql = substr($strSql, $p);
						else
							$strSql = substr($strSql, $p1);
					}
					else
						$strSql = substr($strSql, $p);
				}
				//Delimiter!
				else
				{
					//Take that was before delimiter as part of sql
					$strSql = substr($strSql, strlen($match[0]));
					$str .= $match[1];
					//Delimiter must be followed by whitespace
					if(preg_match("%^[\n\r\t ]%", $strSql))
					{
						$str = trim($str);
						if(strlen($str))
						{
							if ($bIncremental)
							{
								$strSql1 = str_replace("\r\n", "\n", $str);
								if (!$this->QueryLong($strSql1, true))
									$ret[] = $this->GetErrorMessage();
							}
							else
							{
								$ret[] = $str;
								$str = "";
							}
						}
					}
					//It was not delimiter!
					elseif(strlen($strSql))
					{
						$str .= $match[2];
					}
				}
			}
			else //End of file is our delimiter
			{
				$str .= $strSql;
				$strSql = "";
			}
		} while (strlen($strSql));

		$str = trim($str);
		if(strlen($str))
		{
			if ($bIncremental)
			{
				$strSql1 = str_replace("\r\n", "\n", $str);
				if (!$this->QueryLong($strSql1, true))
					$ret[] = $this->GetErrorMessage();
			}
			else
			{
				$ret[] = $str;
			}
		}
		return $ret;
	}

	function RunSQLBatch($filepath, $bIncremental = False)
	{
		if(!file_exists($filepath) || !is_file($filepath))
			return array("File $filepath is not found.");

		$arErr = array();
		$contents = file_get_contents($filepath);

		$arSql = $this->ParseSqlBatch($contents, $bIncremental);
		foreach($arSql as $strSql)
		{
			if ($bIncremental)
			{
				$arErr[] = $strSql;
			}
			else
			{
				$strSql = str_replace("\r\n", "\n", $strSql);
				if(!$this->Query($strSql, true))
					$arErr[] = "<hr><pre>Query:\n".$strSql."\n\nError:\n<font color=red>".$this->GetErrorMessage()."</font></pre>";
			}
		}

		if(!empty($arErr))
			return $arErr;

		return false;
	}

	function IsDate($value, $format=false, $lang=false, $format_type="SHORT")
	{
		if ($format===false) $format = CLang::GetDateFormat($format_type, $lang);
		return CheckDateTime($value, $format);
	}

	function GetErrorMessage()
	{
		if(is_object($this->obSlave) && strlen($this->obSlave->db_Error))
			return $this->obSlave->db_Error;
		elseif(strlen($this->db_Error))
			return $this->db_Error."!";
		else
			return '';
	}

	function GetErrorSQL()
	{
		if(is_object($this->obSlave) && strlen($this->obSlave->db_ErrorSQL))
			return $this->obSlave->db_ErrorSQL;
		elseif(strlen($this->db_ErrorSQL))
			return $this->db_ErrorSQL;
		else
			return '';
	}

	function DDL($strSql, $bIgnoreErrors=false, $error_position="", $arOptions=array())
	{
		$res = $this->Query($strSql, $bIgnoreErrors, $error_position, $arOptions);

		//Reset metadata cache
		$this->column_cache = array();

		return $res;
	}

	function addDebugQuery($strSql, $exec_time)
	{
		$this->cntQuery++;
		$this->timeQuery += $exec_time;

		if (defined("BX_NO_SQL_BACKTRACE"))
		{
			$trace = false;
		}
		else
		{
			$trace = array();
			foreach(debug_backtrace() as $i => $tr)
			{
				if ($i > 0)
				{
					$trace[] = array(
						"file" => $tr["file"],
						"line" => $tr["line"],
						"class" => $tr["class"],
						"type" => $tr["type"],
						"function" => $tr["function"],
						"args" => $tr["args"],
					);
				}
				if ($i > 4)
					break;
			}
		}

		$this->arQueryDebug[] = array(
			"QUERY" => $strSql,
			"TIME" => $exec_time,
			"TRACE" => $trace,
			"BX_STATE" => $GLOBALS["BX_STATE"],
		);
	}

	function addDebugTime($index, $exec_time)
	{
		if (isset($this->arQueryDebug[$index]))
			$this->arQueryDebug[$index]["TIME"] += $exec_time;
	}
}

class CAllDBResult
{
	var $result;
	var $arResult;
	var $arReplacedAliases; // replace tech. aliases in Fetch to human aliases
	var $arResultAdd;
	var $bNavStart = false;
	var $bShowAll = false;
	var $NavNum, $NavPageCount, $NavPageNomer, $NavPageSize, $NavShowAll, $NavRecordCount;
	var $bFirstPrintNav = true;
	var $PAGEN, $SIZEN;
	var $SESS_SIZEN, $SESS_ALL, $SESS_PAGEN;
	var $add_anchor = "";
	var $bPostNavigation = false;
	var $bFromArray = false;
	var $bFromLimited = false;
	var $sSessInitAdd = "";
	var $nPageWindow = 5;
	var $nSelectedCount = false;
	var $arGetNextCache = false;
	var $bDescPageNumbering = false;
	/** @var array */
	var $arUserMultyFields = false;
	var $SqlTraceIndex = false;
	/** @var CDatabase */
	var $DB;
	var $NavRecordCountChangeDisable = false;
	var $is_filtered = false;
	var $nStartPage = 0;
	var $nEndPage = 0;


	function CAllDBResult($res=NULL)
	{
		if(is_object($res) && is_subclass_of($res, "CAllDBResult"))
		{
			$this->result = $res->result;
			$this->nSelectedCount = $res->nSelectedCount;
			$this->arResult = $res->arResult;
			$this->arResultAdd = $res->arResultAdd;
			$this->bNavStart = $res->bNavStart;
			$this->NavPageNomer = $res->NavPageNomer;
			$this->bShowAll = $res->bShowAll;
			$this->NavNum = $res->NavNum;
			$this->NavPageCount = $res->NavPageCount;
			$this->NavPageSize = $res->NavPageSize;
			$this->NavShowAll = $res->NavShowAll;
			$this->NavRecordCount = $res->NavRecordCount;
			$this->bFirstPrintNav = $res->bFirstPrintNav;
			$this->PAGEN = $res->PAGEN;
			$this->SIZEN = $res->SIZEN;
			$this->bFromArray = $res->bFromArray;
			$this->bFromLimited = $res->bFromLimited;
			$this->sSessInitAdd = $res->sSessInitAdd;
			$this->nPageWindow = $res->nPageWindow;
			$this->bDescPageNumbering = $res->bDescPageNumbering;
			$this->SqlTraceIndex = $res->SqlTraceIndex;
			$this->DB = $res->DB;
		}
		elseif(is_array($res))
			$this->arResult = $res;
		else
			$this->result = $res;
	}

	public function __sleep()
	{
		return array(
			'result',
			'arResult',
			'arReplacedAliases',
			'arResultAdd',
			'bNavStart',
			'bShowAll',
			'NavNum',
			'NavPageCount',
			'NavPageNomer',
			'NavPageSize',
			'NavShowAll',
			'NavRecordCount',
			'bFirstPrintNav',
			'PAGEN',
			'SIZEN',
			'add_anchor',
			'bPostNavigation',
			'bFromArray',
			'bFromLimited',
			'sSessInitAdd',
			'nPageWindow',
			'nSelectedCount',
			'arGetNextCache',
			'bDescPageNumbering',
			'arUserMultyFields',
			//'SqlTraceIndex',
			//'DB',
		);
	}

	/**
	 * @return array
	 */
	function Fetch()
	{
		return array();
	}

	function SelectedRowsCount()
	{
	}

	function AffectedRowsCount()
	{
	}

	function FieldsCount()
	{
	}

	function FieldName($iCol)
	{
	}

	function NavContinue()
	{
		if (count($this->arResultAdd) > 0)
		{
			$this->arResult = $this->arResultAdd;
			return true;
		}
		else
			return false;
	}

	function IsNavPrint()
	{
		if ($this->NavRecordCount == 0 || ($this->NavPageCount == 1 && $this->NavShowAll == false))
			return false;

		return true;
	}

	function NavPrint($title, $show_allways=false, $StyleText="text", $template_path=false)
	{
		echo $this->GetNavPrint($title, $show_allways, $StyleText, $template_path);
	}

	function GetNavPrint($title, $show_allways=false, $StyleText="text", $template_path=false, $arDeleteParam=false)
	{
		$res = '';
		$add_anchor = $this->add_anchor;

		$sBegin = GetMessage("nav_begin");
		$sEnd = GetMessage("nav_end");
		$sNext = GetMessage("nav_next");
		$sPrev = GetMessage("nav_prev");
		$sAll = GetMessage("nav_all");
		$sPaged = GetMessage("nav_paged");

		$nPageWindow = $this->nPageWindow;

		if(!$show_allways)
		{
			if ($this->NavRecordCount == 0 || ($this->NavPageCount == 1 && $this->NavShowAll == false))
				return '';
		}

		$sUrlPath = GetPagePath();

		$arDel = array("PAGEN_".$this->NavNum, "SIZEN_".$this->NavNum, "SHOWALL_".$this->NavNum, "PHPSESSID");
		if(is_array($arDeleteParam))
			$arDel = array_merge($arDel, $arDeleteParam);
		$strNavQueryString = DeleteParam($arDel);
		if($strNavQueryString <> "")
			$strNavQueryString = htmlspecialcharsbx("&".$strNavQueryString);

		if($template_path!==false && !file_exists($template_path) && file_exists($_SERVER["DOCUMENT_ROOT"].$template_path))
			$template_path = $_SERVER["DOCUMENT_ROOT"].$template_path;

		if($this->bDescPageNumbering === true)
		{
			if($this->NavPageNomer + floor($nPageWindow/2) >= $this->NavPageCount)
				$nStartPage = $this->NavPageCount;
			else
			{
				if($this->NavPageNomer + floor($nPageWindow/2) >= $nPageWindow)
					$nStartPage = $this->NavPageNomer + floor($nPageWindow/2);
				else
				{
					if($this->NavPageCount >= $nPageWindow)
						$nStartPage = $nPageWindow;
					else
						$nStartPage = $this->NavPageCount;
				}
			}

			if($nStartPage - $nPageWindow >= 0)
				$nEndPage = $nStartPage - $nPageWindow + 1;
			else
				$nEndPage = 1;
			//echo "nEndPage = $nEndPage; nStartPage = $nStartPage;";
		}
		else
		{
			if($this->NavPageNomer > floor($nPageWindow/2) + 1 && $this->NavPageCount > $nPageWindow)
				$nStartPage = $this->NavPageNomer - floor($nPageWindow/2);
			else
				$nStartPage = 1;

			if($this->NavPageNomer <= $this->NavPageCount - floor($nPageWindow/2) && $nStartPage + $nPageWindow-1 <= $this->NavPageCount)
				$nEndPage = $nStartPage + $nPageWindow - 1;
			else
			{
				$nEndPage = $this->NavPageCount;
				if($nEndPage - $nPageWindow + 1 >= 1)
					$nStartPage = $nEndPage - $nPageWindow + 1;
			}
		}

		$this->nStartPage = $nStartPage;
		$this->nEndPage = $nEndPage;

		if($template_path!==false && file_exists($template_path))
		{
/*
			$this->bFirstPrintNav - is first tiem call
			$this->NavPageNomer - number of current page
			$this->NavPageCount - total page count
			$this->NavPageSize - page size
			$this->NavRecordCount - records count
			$this->bShowAll - show "all" link
			$this->NavShowAll - is all shown
			$this->NavNum - number of navigation
			$this->bDescPageNumbering - reverse paging

			$this->nStartPage - first page in chain
			$this->nEndPage - last page in chain

			$strNavQueryString - query string
			$sUrlPath - current url

			Url for link to the page #PAGE_NUMBER#:
			$sUrlPath.'?PAGEN_'.$this->NavNum.'='.#PAGE_NUMBER#.$strNavQueryString.'#nav_start"'.$add_anchor
*/

			ob_start();
			include($template_path);
			$res = ob_get_contents();
			ob_end_clean();
			$this->bFirstPrintNav = false;
			return $res;
		}

		if($this->bFirstPrintNav)
		{
			$res .= '<a name="nav_start'.$add_anchor.'"></a>';
			$this->bFirstPrintNav = false;
		}

		$res .= '<font class="'.$StyleText.'">'.$title.' ';
		if($this->bDescPageNumbering === true)
		{
			$makeweight = ($this->NavRecordCount % $this->NavPageSize);
			$NavFirstRecordShow = 0;
			if($this->NavPageNomer != $this->NavPageCount)
				$NavFirstRecordShow += $makeweight;

			$NavFirstRecordShow += ($this->NavPageCount - $this->NavPageNomer) * $this->NavPageSize + 1;

			if ($this->NavPageCount == 1)
				$NavLastRecordShow = $this->NavRecordCount;
			else
				$NavLastRecordShow = $makeweight + ($this->NavPageCount - $this->NavPageNomer + 1) * $this->NavPageSize;

			$res .= $NavFirstRecordShow;
			$res .= ' - '.$NavLastRecordShow;
			$res .= ' '.GetMessage("nav_of").' ';
			$res .= $this->NavRecordCount;
			$res .= "\n<br>\n</font>";

			$res .= '<font class="'.$StyleText.'">';

			if($this->NavPageNomer < $this->NavPageCount)
				$res .= '<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.$this->NavPageCount.$strNavQueryString.'#nav_start'.$add_anchor.'">'.$sBegin.'</a>&nbsp;|&nbsp;<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.($this->NavPageNomer+1).$strNavQueryString.'#nav_start'.$add_anchor.'">'.$sPrev.'</a>';
			else
				$res .= $sBegin.'&nbsp;|&nbsp;'.$sPrev;

			$res .= '&nbsp;|&nbsp;';

			$NavRecordGroup = $nStartPage;
			while($NavRecordGroup >= $nEndPage)
			{
				$NavRecordGroupPrint = $this->NavPageCount - $NavRecordGroup + 1;
				if($NavRecordGroup == $this->NavPageNomer)
					$res .= '<b>'.$NavRecordGroupPrint.'</b>&nbsp';
				else
					$res .= '<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.$NavRecordGroup.$strNavQueryString.'#nav_start'.$add_anchor.'">'.$NavRecordGroupPrint.'</a>&nbsp;';
				$NavRecordGroup--;
			}
			$res .= '|&nbsp;';
			if($this->NavPageNomer > 1)
				$res .= '<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.($this->NavPageNomer-1).$strNavQueryString.'#nav_start'.$add_anchor.'">'.$sNext.'</a>&nbsp;|&nbsp;<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'=1'.$strNavQueryString.'#nav_start'.$add_anchor.'">'.$sEnd.'</a>&nbsp;';
			else
				$res .= $sNext.'&nbsp;|&nbsp;'.$sEnd.'&nbsp;';
		}
		else
		{
			$res .= ($this->NavPageNomer-1)*$this->NavPageSize+1;
			$res .= ' - ';
			if($this->NavPageNomer != $this->NavPageCount)
				$res .= $this->NavPageNomer * $this->NavPageSize;
			else
				$res .= $this->NavRecordCount;
			$res .= ' '.GetMessage("nav_of").' ';
			$res .= $this->NavRecordCount;
			$res .= "\n<br>\n</font>";

			$res .= '<font class="'.$StyleText.'">';

			if($this->NavPageNomer > 1)
				$res .= '<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'=1'.$strNavQueryString.'#nav_start'.$add_anchor.'">'.$sBegin.'</a>&nbsp;|&nbsp;<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.($this->NavPageNomer-1).$strNavQueryString.'#nav_start'.$add_anchor.'">'.$sPrev.'</a>';
			else
				$res .= $sBegin.'&nbsp;|&nbsp;'.$sPrev;

			$res .= '&nbsp;|&nbsp;';

			$NavRecordGroup = $nStartPage;
			while($NavRecordGroup <= $nEndPage)
			{
				if($NavRecordGroup == $this->NavPageNomer)
					$res .= '<b>'.$NavRecordGroup.'</b>&nbsp';
				else
					$res .= '<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.$NavRecordGroup.$strNavQueryString.'#nav_start'.$add_anchor.'">'.$NavRecordGroup.'</a>&nbsp;';
				$NavRecordGroup++;
			}
			$res .= '|&nbsp;';
			if($this->NavPageNomer < $this->NavPageCount)
				$res .= '<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.($this->NavPageNomer+1).$strNavQueryString.'#nav_start'.$add_anchor.'">'.$sNext.'</a>&nbsp;|&nbsp;<a href="'.$sUrlPath.'?PAGEN_'.$this->NavNum.'='.$this->NavPageCount.$strNavQueryString.'#nav_start'.$add_anchor.'">'.$sEnd.'</a>&nbsp;';
			else
				$res .= $sNext.'&nbsp;|&nbsp;'.$sEnd.'&nbsp;';
		}

		if($this->bShowAll)
			$res .= $this->NavShowAll? '|&nbsp;<a href="'.$sUrlPath.'?SHOWALL_'.$this->NavNum.'=0'.$strNavQueryString.'#nav_start'.$add_anchor.'">'.$sPaged.'</a>&nbsp;' : '|&nbsp;<a href="'.$sUrlPath.'?SHOWALL_'.$this->NavNum.'=1'.$strNavQueryString.'#nav_start'.$add_anchor.'">'.$sAll.'</a>&nbsp;';

		$res .= '</font>';
		return $res;
	}

	function ExtractFields($strPrefix="str_", $bDoEncode=true)
	{
		return $this->NavNext(true, $strPrefix, $bDoEncode);
	}

	function ExtractEditFields($strPrefix="str_")
	{
		return $this->NavNext(true, $strPrefix, true, false);
	}

	function GetNext($bTextHtmlAuto=true, $use_tilda=true)
	{
		if($arRes = $this->Fetch())
		{
			if($this->arGetNextCache==false)
			{
				$this->arGetNextCache = array();
				foreach($arRes as $FName=>$arFValue)
					$this->arGetNextCache[$FName] = array_key_exists($FName."_TYPE", $arRes);
			}
			if($use_tilda)
			{
				$arTilda = array();
				foreach($arRes as $FName=>$arFValue)
				{
					if($this->arGetNextCache[$FName] && $bTextHtmlAuto)
						$arTilda[$FName] = FormatText($arFValue, $arRes[$FName."_TYPE"]);
					elseif(is_array($arFValue))
						$arTilda[$FName] = htmlspecialcharsEx($arFValue);
					elseif(preg_match("/[;&<>\"]/", $arFValue))
						$arTilda[$FName] = htmlspecialcharsEx($arFValue);
					else
						$arTilda[$FName] = $arFValue;
					$arTilda["~".$FName] = $arFValue;
				}
				return $arTilda;
			}
			else
			{
				foreach($arRes as $FName=>$arFValue)
				{
					if($this->arGetNextCache[$FName] && $bTextHtmlAuto)
						$arRes[$FName] = FormatText($arFValue, $arRes[$FName."_TYPE"]);
					elseif(is_array($arFValue))
						$arRes[$FName] = htmlspecialcharsEx($arFValue);
					elseif(preg_match("/[;&<>\"]/", $arFValue))
						$arRes[$FName] = htmlspecialcharsEx($arFValue);
				}
			}
		}
		return $arRes;
	}

	function NavStringForCache($nPageSize=0, $bShowAll=true, $iNumPage=false)
	{
		$NavParams = CDBResult::GetNavParams($nPageSize, $bShowAll, $iNumPage);
		return "|".($NavParams["SHOW_ALL"]?"":$NavParams["PAGEN"])."|".$NavParams["SHOW_ALL"]."|";
	}

	function GetNavParams($nPageSize=0, $bShowAll=true, $iNumPage=false)
	{
		/** @global CMain $APPLICATION */
		global $NavNum, $APPLICATION;

		$bDescPageNumbering = false; //it can be extracted from $nPageSize

		if(is_array($nPageSize))
			extract($nPageSize);

		$nPageSize = intval($nPageSize);
		$NavNum = intval($NavNum);

		$PAGEN_NAME="PAGEN_".($NavNum+1);
		$SHOWALL_NAME="SHOWALL_".($NavNum+1);

		global $$PAGEN_NAME, $$SHOWALL_NAME;
		$md5Path = md5(
			(isset($sNavID)? $sNavID: $APPLICATION->GetCurPage())
			.(is_object($this) && isset($this->sSessInitAdd)? $this->sSessInitAdd: "")
		);

		if($iNumPage===false)
			$PAGEN = $$PAGEN_NAME;
		else
			$PAGEN = $iNumPage;

		$SHOWALL = $$SHOWALL_NAME;

		$SESS_PAGEN = $md5Path."SESS_PAGEN_".($NavNum+1);
		$SESS_ALL = $md5Path."SESS_ALL_".($NavNum+1);
		if(intval($PAGEN) <= 0)
		{
			if(CPageOption::GetOptionString("main", "nav_page_in_session", "Y")=="Y" && intval($_SESSION[$SESS_PAGEN])>0)
				$PAGEN = $_SESSION[$SESS_PAGEN];
			elseif($bDescPageNumbering === true)
				$PAGEN = 0;
			else
				$PAGEN = 1;
		}

		//Number of records on a page
		$SIZEN = $nPageSize;
		if(intval($SIZEN)<1)
			$SIZEN = 10;

		//Show all records
		$SHOW_ALL = ($bShowAll? (isset($SHOWALL) ? ($SHOWALL == 1) : (CPageOption::GetOptionString("main", "nav_page_in_session", "Y")=="Y" && $_SESSION[$SESS_ALL] == 1)) : false);

		//$NavShowAll comes from $nPageSize array
		$res = array(
			"PAGEN"=>$PAGEN,
			"SIZEN"=>$SIZEN,
			"SHOW_ALL"=>(isset($NavShowAll)? $NavShowAll : $SHOW_ALL),
		);

		if(CPageOption::GetOptionString("main", "nav_page_in_session", "Y")=="Y")
		{
			$_SESSION[$SESS_PAGEN] = $PAGEN;
			$_SESSION[$SESS_ALL] = $SHOW_ALL;
			$res["SESS_PAGEN"]=$SESS_PAGEN;
			$res["SESS_ALL"]=$SESS_ALL;
		}

		return $res;
	}

	function InitNavStartVars($nPageSize=0, $bShowAll=true, $iNumPage=false)
	{
		if(is_array($nPageSize) && is_set($nPageSize, "bShowAll"))
			$this->bShowAll = $nPageSize["bShowAll"];
		else
			$this->bShowAll = $bShowAll;

		$this->bNavStart = true;

		$arParams = $this->GetNavParams($nPageSize, $bShowAll, $iNumPage);

		$this->PAGEN = $arParams["PAGEN"];
		$this->SIZEN = $arParams["SIZEN"];
		$this->NavShowAll = $arParams["SHOW_ALL"];
		$this->NavPageSize = $arParams["SIZEN"];
		$this->SESS_SIZEN = $arParams["SESS_SIZEN"];
		$this->SESS_PAGEN = $arParams["SESS_PAGEN"];
		$this->SESS_ALL = $arParams["SESS_ALL"];

		global $NavNum;

		$NavNum++;
		$this->NavNum = $NavNum;

		if($this->NavNum>1)
			$add_anchor = "_".$this->NavNum;
		else
			$add_anchor = "";

		$this->add_anchor = $add_anchor;
	}

	function NavStart($nPageSize=0, $bShowAll=true, $iNumPage=false)
	{
		if($this->bFromLimited)
			return;

		if(is_array($nPageSize))
			$this->InitNavStartVars($nPageSize);
		else
			$this->InitNavStartVars(intval($nPageSize), $bShowAll, $iNumPage);

		if($this->bFromArray)
		{
			$this->NavRecordCount = count($this->arResult);
			if($this->NavRecordCount < 1)
				return;

			if($this->NavShowAll)
				$this->NavPageSize = $this->NavRecordCount;

			$this->NavPageCount = floor($this->NavRecordCount/$this->NavPageSize);
			if($this->NavRecordCount % $this->NavPageSize > 0)
				$this->NavPageCount++;

			$this->NavPageNomer =
				($this->PAGEN < 1 || $this->PAGEN > $this->NavPageCount
				?
					(CPageOption::GetOptionString("main", "nav_page_in_session", "Y")!="Y"
						|| $_SESSION[$this->SESS_PAGEN] < 1
						|| $_SESSION[$this->SESS_PAGEN] > $this->NavPageCount
					?
						1
					:
						$_SESSION[$this->SESS_PAGEN]
					)
				:
					$this->PAGEN
				);

			$NavFirstRecordShow = $this->NavPageSize*($this->NavPageNomer-1);
			$NavLastRecordShow = $this->NavPageSize*$this->NavPageNomer;

			$this->arResult = array_slice($this->arResult, $NavFirstRecordShow, $NavLastRecordShow - $NavFirstRecordShow);
		}
		else
		{
			$this->DBNavStart();
		}
	}

	function DBNavStart()
	{
	}

	function InitFromArray($arr)
	{
		if(is_array($arr))
			reset($arr);
		$this->arResult = $arr;
		$this->nSelectedCount = count($arr);
		$this->bFromArray = true;
	}

	function NavNext($bSetGlobalVars=true, $strPrefix="str_", $bDoEncode=true, $bSkipEntities=true)
	{
		$arr = $this->Fetch();
		if($arr && $bSetGlobalVars)
		{
			foreach($arr as $key=>$val)
			{
				$varname = $strPrefix.$key;
				global $$varname;

				if($bDoEncode && !is_array($val) && !is_object($val))
				{
					if($bSkipEntities)
						$$varname = htmlspecialcharsEx($val);
					else
						$$varname = htmlspecialcharsbx($val);
				}
				else
				{
					$$varname = $val;
				}
			}
		}
		return $arr;
	}

	function GetPageNavString($navigationTitle, $templateName = "", $showAlways=false)
	{
		return $this->GetPageNavStringEx($dummy, $navigationTitle, $templateName, $showAlways);
	}

	function GetPageNavStringEx(&$navComponentObject, $navigationTitle, $templateName = "", $showAlways=false)
	{
		/** @global CMain $APPLICATION */
		global $APPLICATION;

		ob_start();

		$navComponentObject = $APPLICATION->IncludeComponent(
			"bitrix:system.pagenavigation",
			$templateName,
			Array(
				"NAV_TITLE"=> $navigationTitle,
				"NAV_RESULT" => $this,
				"SHOW_ALWAYS" => $showAlways
			),
			null,
			array(
				"HIDE_ICONS" => "Y"
			)
		);

		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	function SetUserFields($arUserFields)
	{
		$this->arUserMultyFields = array();
		if(is_array($arUserFields))
			foreach($arUserFields as $FIELD_NAME=>$arUserField)
				if($arUserField["MULTIPLE"]=="Y")
					$this->arUserMultyFields[$FIELD_NAME] = true;
		if(count($this->arUserMultyFields)<1)
			$this->arUserMultyFields = false;
	}

}
