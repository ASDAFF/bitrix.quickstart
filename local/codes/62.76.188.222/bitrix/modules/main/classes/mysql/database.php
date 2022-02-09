<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/database.php");

/********************************************************************
*	MySQL database classes
********************************************************************/
class CDatabase extends CAllDatabase
{
	var $DBName;
	var $DBHost;
	var $DBLogin;
	var $DBPassword;
	var $bConnected;
	var $version;
	var $cntQuery;
	var $timeQuery;
	var $obSlave;

	public
		$escL = '`',
		$escR = '`';

	public
		$alias_length = 256;

	function GetVersion()
	{
		if($this->version)
			return $this->version;

		$rs = $this->Query("SELECT VERSION() as R", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		if($ar = $rs->Fetch())
		{
			$version = trim($ar["R"]);
			preg_match("#[0-9]+\\.[0-9]+\\.[0-9]+#", $version, $arr);
			$version = $arr[0];
			$this->version = $version;
			return $version;
		}
		else
		{
			return false;
		}
	}

	function StartTransaction()
	{
		$this->Query("START TRANSACTION");
	}

	function Commit()
	{
		$this->Query("COMMIT", true);
	}

	function Rollback()
	{
		$this->Query("ROLLBACK", true);
	}

	//Connect to database
	function Connect($DBHost, $DBName, $DBLogin, $DBPassword)
	{
		$this->type="MYSQL";
		$this->DBHost = $DBHost;
		$this->DBName = $DBName;
		$this->DBLogin = $DBLogin;
		$this->DBPassword = $DBPassword;
		$this->bConnected = false;

		if (!defined("DBPersistent"))
			define("DBPersistent",true);

		if(defined("DELAY_DB_CONNECT") && DELAY_DB_CONNECT===true)
			return true;
		else
			return $this->DoConnect();
	}

	function DoConnect()
	{
		if($this->bConnected)
			return true;
		$this->bConnected = true;

		if (DBPersistent && !$this->bNodeConnection)
			$this->db_Conn = @mysql_pconnect($this->DBHost, $this->DBLogin, $this->DBPassword);
		else
			$this->db_Conn = @mysql_connect($this->DBHost, $this->DBLogin, $this->DBPassword, true);

		if(!$this->db_Conn)
		{
			$s = (DBPersistent && !$this->bNodeConnection? "mysql_pconnect" : "mysql_connect");
			if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
				echo "<br><font color=#ff0000>Error! ".$s."('-', '-', '-')</font><br>".mysql_error()."<br>";

			SendError("Error! ".$s."('-', '-', '-')\n".mysql_error()."\n");
			return false;
		}

		if(!mysql_select_db($this->DBName, $this->db_Conn))
		{
			if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
				echo "<br><font color=#ff0000>Error! mysql_select_db(".$this->DBName.")</font><br>".mysql_error($this->db_Conn)."<br>";

			SendError("Error! mysql_select_db(".$this->DBName.")\n".mysql_error($this->db_Conn)."\n");
			return false;
		}

		$this->cntQuery = 0;
		$this->timeQuery = 0;
		$this->arQueryDebug = array();

		/** @noinspection PhpUnusedLocalVariableInspection */
		global $DB, $USER, $APPLICATION;
		if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/after_connect.php"))
			include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/after_connect.php");

		return true;
	}

	//This function executes query against database
	function Query($strSql, $bIgnoreErrors=false, $error_position="", $arOptions=array())
	{
		global $DB;

		$this->DoConnect();
		$this->db_Error="";

		if($this->DebugToFile || $DB->ShowSqlStat)
			$start_time = microtime(true);

		//We track queries for DML statements
		//and when there is no one we can choose
		//to run query against master connection
		//or replicated one
		static $bSelectOnly = true;

		if($this->bModuleConnection)
		{
			//In case of dedicated module database
			//were is nothing to do
		}
		elseif($DB->bMasterOnly > 0)
		{
			//We requested to process all queries
			//by master connection
		}
		elseif(isset($arOptions["fixed_connection"]))
		{
			//We requested to process this query
			//by current connection
		}
		elseif($this->bNodeConnection)
		{
			//It is node so nothing to do
		}
		else
		{
			$bSelect = preg_match('/^\s*(select|show)/i', $strSql) && !preg_match('/get_lock/i', $strSql);
			if(!$bSelect && !isset($arOptions["ignore_dml"]))
				$bSelectOnly = false;

			if($bSelect && $bSelectOnly)
			{
				if(!isset($this->obSlave))
				{
					$this->StartUsingMasterOnly(); //This is bootstrap code
					$this->obSlave = CDatabase::SlaveConnection();
					$this->StopUsingMasterOnly();
				}

				if(is_object($this->obSlave))
					return $this->obSlave->Query($strSql, $bIgnoreErrors, $error_position, $arOptions);
			}
		}

		$result = @mysql_query($strSql, $this->db_Conn);

		if($this->DebugToFile || $DB->ShowSqlStat)
		{
			/** @noinspection PhpUndefinedVariableInspection */
			$exec_time = round(microtime(true) - $start_time, 10);

			if($DB->ShowSqlStat)
				$DB->addDebugQuery($strSql, $exec_time);

			if($this->DebugToFile)
			{
				$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/mysql_debug.sql","ab+");
				$str = "TIME: ".$exec_time." SESSION: ".session_id()."  CONN: ".$this->db_Conn."\n";
				$str .= $strSql."\n\n";
				$str .= "----------------------------------------------------\n\n";
				fputs($fp, $str);
				@fclose($fp);
			}
		}

		if(!$result)
		{
			$this->db_Error = mysql_error($this->db_Conn);
			$this->db_ErrorSQL = $strSql;
			if(!$bIgnoreErrors)
			{
				AddMessage2Log($error_position." MySql Query Error: ".$strSql." [".$this->db_Error."]", "main");
				if ($this->DebugToFile)
				{
					$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/mysql_debug.sql","ab+");
					fputs($fp,"SESSION: ".session_id()." ERROR: ".$this->db_Error."\n\n----------------------------------------------------\n\n");
					@fclose($fp);
				}

				if($this->debug || (@session_start() && $_SESSION["SESS_AUTH"]["ADMIN"]))
					echo $error_position."<br><font color=#ff0000>MySQL Query Error: ".htmlspecialcharsbx($strSql)."</font>[".htmlspecialcharsbx($this->db_Error)."]<br>";

				$error_position = preg_replace("#<br[^>]*>#i","\n",$error_position);
				SendError($error_position."\nMySQL Query Error:\n".$strSql." \n [".$this->db_Error."]\n---------------\n\n");

				if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/dbquery_error.php"))
					include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/dbquery_error.php");
				elseif(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/dbquery_error.php"))
					include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/dbquery_error.php");
				else
					die("MySQL Query Error!");

				die();
			}
			return false;
		}

		$res = new CDBResult($result);
		$res->DB = $this;
		if($DB->ShowSqlStat)
			$res->SqlTraceIndex = count($DB->arQueryDebug) - 1;
		return $res;
	}

	function QueryLong($strSql, $bIgnoreErrors = false)
	{
		return $this->Query($strSql, $bIgnoreErrors);
	}

	function CurrentTimeFunction()
	{
		return "now()";
	}

	function CurrentDateFunction()
	{
		return "CURRENT_DATE";
	}

	function DateFormatToDB($format, $field = false)
	{
//		static $search  = array("YYYY", "MM", "DD", "HH", "MI", "SS");
//		static $replace = array("%Y", "%m", "%d", "%H", "%i", "%s");

		static $search  = array(
			"YYYY",
			"MMMM",
			"MM",
			"MI",
			"DD",
			"HH",
			"GG",
			"G",
			"SS",
			"TT",
			"T"
		);
		static $replace = array(
			"%Y",
			"%M",
			"%m",
			"%i",
			"%d",
			"%H",
			"%h",
			"%l",
			"%s",
			"%p",
			"%p"
		);

		foreach ($search as $k=>$v)
		{
			$format = str_replace($v, $replace[$k], $format);
		}
		if (strpos($format, '%H') === false)
		{
			$format = str_replace("H", "%h", $format);
		}
		if (strpos($format, '%M') === false)
		{
			$format = str_replace("M", "%b", $format);
		}

		if($field === false)
		{
			return $format;
		}
		else
		{
			return "DATE_FORMAT(".$field.", '".$format."')";
		}
	}

	function DateToCharFunction($strFieldName, $strType="FULL", $lang=false, $bSearchInSitesOnly=false)
	{
		static $CACHE=array();
		$id = $strType.",".$lang.",".$bSearchInSitesOnly;
		if(!array_key_exists($id,$CACHE))
			$CACHE[$id] = $this->DateFormatToDB(CLang::GetDateFormat($strType, $lang, $bSearchInSitesOnly));

		$sFieldExpr = $strFieldName;

		//time zone
		if($strType == "FULL" && CTimeZone::Enabled())
		{
			static $diff = false;
			if($diff === false)
				$diff = CTimeZone::GetOffset();

			if($diff <> 0)
				$sFieldExpr = "DATE_ADD(".$strFieldName.", INTERVAL ".$diff." SECOND)";
		}

		return "DATE_FORMAT(".$sFieldExpr.", '".$CACHE[$id]."')";
	}

	function CharToDateFunction($strValue, $strType="FULL", $lang=false)
	{
		$sFieldExpr = "'".CDatabase::FormatDate($strValue, CLang::GetDateFormat($strType, $lang), ($strType=="SHORT"? "Y-M-D":"Y-M-D H:I:S"))."'";

		//time zone
		if($strType == "FULL" && CTimeZone::Enabled())
		{
			static $diff = false;
			if($diff === false)
				$diff = CTimeZone::GetOffset();

			if($diff <> 0)
				$sFieldExpr = "DATE_ADD(".$sFieldExpr.", INTERVAL -(".$diff.") SECOND)";
		}

		return $sFieldExpr;
	}

	function DatetimeToDateFunction($strValue)
	{
		return 'DATE('.$strValue.')';
	}

	//  1 if date1 > date2
	//  0 if date1 = date2
	// -1 if date1 < date2
	function CompareDates($date1, $date2)
	{
		$s_date1 = $this->CharToDateFunction($date1);
		$s_date2 = $this->CharToDateFunction($date2);
		$strSql = "
			SELECT
				if($s_date1 > $s_date2, 1,
					if ($s_date1 < $s_date2, -1,
						if ($s_date1 = $s_date2, 0, 'x')
				)) as RES
			";
		$z = $this->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		$zr = $z->Fetch();
		return $zr["RES"];
	}

	function LastID()
	{
		$this->DoConnect();
		return mysql_insert_id($this->db_Conn);
	}

	//Closes database connection
	function Disconnect()
	{
		if(!DBPersistent && $this->bConnected)
		{
			$this->bConnected = false;
			mysql_close($this->db_Conn);
		}

		foreach(self::$arNodes as $arNode)
		{
			if(is_array($arNode) && array_key_exists("DB", $arNode))
			{
				mysql_close($arNode["DB"]->db_Conn);
				unset($arNode["DB"]);
			}
		}
	}

	function PrepareFields($strTableName, $strPrefix = "str_", $strSuffix = "")
	{
		$arColumns = $this->GetTableFields($strTableName);
		foreach($arColumns as $arColumn)
		{
			$column = $arColumn["NAME"];
			$type = $arColumn["TYPE"];
			global $$column;
			$var = $strPrefix.$column.$strSuffix;
			global $$var;
			switch ($type)
			{
				case "int":
					$$var = IntVal($$column);
					break;
				case "real":
					$$var = DoubleVal($$column);
					break;
				default:
					$$var = $this->ForSql($$column);
			}
		}
	}

	function PrepareInsert($strTableName, $arFields, $strFileDir="", $lang=false)
	{
		$strInsert1 = "";
		$strInsert2 = "";

		$arColumns = $this->GetTableFields($strTableName);
		foreach($arColumns as $strColumnName => $arColumnInfo)
		{
			$type = $arColumnInfo["TYPE"];
			if(isset($arFields[$strColumnName]))
			{
				$value = $arFields[$strColumnName];

				if($value === false)
				{
					$strInsert1 .= ", `".$strColumnName."`";
					$strInsert2 .= ",  NULL ";
				}
				else
				{
					$strInsert1 .= ", `".$strColumnName."`";
					switch ($type)
					{
						case "datetime":
							if(strlen($value)<=0)
								$strInsert2 .= ", NULL ";
							else
								$strInsert2 .= ", ".CDatabase::CharToDateFunction($value, "FULL", $lang);
							break;
						case "date":
							if(strlen($value)<=0)
								$strInsert2 .= ", NULL ";
							else
								$strInsert2 .= ", ".CDatabase::CharToDateFunction($value, "SHORT", $lang);
							break;
						case "int":
							$strInsert2 .= ", '".IntVal($value)."'";
							break;
						case "real":
							$strInsert2 .= ", '".DoubleVal($value)."'";
							break;
						default:
							$strInsert2 .= ", '".$this->ForSql($value)."'";
					}
				}
			}
			elseif(array_key_exists("~".$strColumnName, $arFields))
			{
				$strInsert1 .= ", `".$strColumnName."`";
				$strInsert2 .= ", ".$arFields["~".$strColumnName];
			}
		}

		if($strInsert1!="")
		{
			$strInsert1 = substr($strInsert1, 2);
			$strInsert2 = substr($strInsert2, 2);
		}
		return array($strInsert1, $strInsert2);
	}

	function PrepareUpdate($strTableName, $arFields, $strFileDir="", $lang = false, $strTableAlias = "")
	{
		return $this->PrepareUpdateBind($strTableName, $arFields, $strFileDir, $lang, $arBinds, $strTableAlias);
	}

	function PrepareUpdateBind($strTableName, $arFields, $strFileDir, $lang, &$arBinds, $strTableAlias = "")
	{
		$arBinds = array();
		if ($strTableAlias != "")
			$strTableAlias .= ".";
		$strUpdate = "";
		$arColumns = $this->GetTableFields($strTableName);
		foreach($arColumns as $strColumnName => $arColumnInfo)
		{
			$type = $arColumnInfo["TYPE"];
			if(isset($arFields[$strColumnName]))
			{
				$value = $arFields[$strColumnName];
				if($value === false)
				{
					$strUpdate .= ", $strTableAlias`".$strColumnName."` = NULL";
				}
				else
				{
					switch ($type)
					{
						case "int":
							$value = IntVal($value);
							break;
						case "real":
							$value = DoubleVal($value);
							break;
						case "datetime":
							if(strlen($value)<=0)
								$value = "NULL";
							else
								$value = CDatabase::CharToDateFunction($value, "FULL", $lang);
							break;
						case "date":
							if(strlen($value)<=0)
								$value = "NULL";
							else
								$value = CDatabase::CharToDateFunction($value, "SHORT", $lang);
							break;
						default:
							$value = "'".$this->ForSql($value)."'";
					}
					$strUpdate .= ", $strTableAlias`".$strColumnName."` = ".$value;
				}
			}
			elseif(is_set($arFields, "~".$strColumnName))
			{
				$strUpdate .= ", $strTableAlias`".$strColumnName."` = ".$arFields["~".$strColumnName];
			}
		}

		if($strUpdate!="")
			$strUpdate = substr($strUpdate, 2);

		return $strUpdate;
	}

	function Insert($table, $arFields, $error_position="", $DEBUG=false, $EXIST_ID="", $ignore_errors=false)
	{
		if (!is_array($arFields))
			return false;

		$str1 = "";
		$str2 = "";
		foreach ($arFields as $field => $value)
		{
			$str1 .= ($str1 <> ""? ", ":"")."`".$field."`";
			if (strlen($value) <= 0)
				$str2 .= ($str2 <> ""? ", ":"")."''";
			else
				$str2 .= ($str2 <> ""? ", ":"").$value;
		}

		if (strlen($EXIST_ID)>0)
		{
			$strSql = "INSERT INTO ".$table."(ID,".$str1.") VALUES ('".$this->ForSql($EXIST_ID)."',".$str2.")";
		}
		else
		{
			$strSql = "INSERT INTO ".$table."(".$str1.") VALUES (".$str2.")";
		}

		if ($DEBUG)
			echo "<br>".htmlspecialcharsEx($strSql)."<br>";

		$res = $this->Query($strSql, $ignore_errors, $error_position);

		if ($res === false)
			return false;

		if (strlen($EXIST_ID) > 0)
			return $EXIST_ID;
		else
			return $this->LastID();
	}

	function Update($table, $arFields, $WHERE="", $error_position="", $DEBUG=false, $ignore_errors=false, $additional_check=true)
	{
		$rows = 0;
		if(is_array($arFields))
		{
			$ar = array();
			foreach($arFields as $field => $value)
			{
				if (strlen($value)<=0)
					$ar[] = "`".$field."` = ''";
				else
					$ar[] = "`".$field."` = ".$value."";
			}

			if (!empty($ar))
			{
				$strSql = "UPDATE ".$table." SET ".implode(", ", $ar)." ".$WHERE;
				if ($DEBUG)
					echo "<br>".htmlspecialcharsEx($strSql)."<br>";
				$w = $this->Query($strSql, $ignore_errors, $error_position);
				if (is_object($w))
				{
					$rows = $w->AffectedRowsCount();
					if ($DEBUG)
						echo "affected_rows = ".$rows."<br>";

					if ($rows <= 0 && $additional_check)
					{
						$w = $this->Query("SELECT 'x' FROM ".$table." ".$WHERE, $ignore_errors, $error_position);
						if (is_object($w))
						{
							if ($w->Fetch())
								$rows = $w->SelectedRowsCount();
							if ($DEBUG)
								echo "num_rows = ".$rows."<br>";
						}
					}
				}
			}
		}
		return $rows;
	}

	function Add($tablename, $arFields, $arCLOBFields = Array(), $strFileDir="", $ignore_errors=false, $error_position="", $arOptions=array())
	{
		global $DB;

		if(!is_object($this) || !isset($this->type))
		{
			return $DB->Add($tablename, $arFields, $arCLOBFields, $strFileDir, $ignore_errors, $error_position, $arOptions);
		}
		else
		{
			$arInsert = $this->PrepareInsert($tablename, $arFields, $strFileDir);
			$strSql =
				"INSERT INTO ".$tablename."(".$arInsert[0].") ".
				"VALUES(".$arInsert[1].")";
			$this->Query($strSql, $ignore_errors, $error_position, $arOptions);
			return $this->LastID();
		}
	}

	function TopSql($strSql, $nTopCount)
	{
		$nTopCount = intval($nTopCount);
		if($nTopCount>0)
			return $strSql."\nLIMIT ".$nTopCount;
		else
			return $strSql;
	}

	function ForSql($strValue, $iMaxLength = 0)
	{
		if ($iMaxLength > 0)
			$strValue = substr($strValue, 0, $iMaxLength);

		if (!is_object($this) || !$this->db_Conn)
		{
			global $DB;
			$DB->DoConnect();
			return mysql_real_escape_string($strValue, $DB->db_Conn);
		}
		else
		{
			$this->DoConnect();
			return mysql_real_escape_string($strValue, $this->db_Conn);
		}
	}

	function ForSqlLike($strValue, $iMaxLength = 0)
	{
		if ($iMaxLength > 0)
			$strValue = substr($strValue, 0, $iMaxLength);

		if(!is_object($this) || !$this->db_Conn)
		{
			global $DB;
			$DB->DoConnect();
			return mysql_real_escape_string(str_replace("\\", "\\\\", $strValue), $DB->db_Conn);
		}
		else
		{
			$this->DoConnect();
			return mysql_real_escape_string(str_replace("\\", "\\\\", $strValue), $this->db_Conn);
		}
	}

	function InitTableVarsForEdit($tablename, $strIdentFrom="str_", $strIdentTo="str_", $strSuffixFrom="", $bAlways=false)
	{
		$this->DoConnect();
		$db_result = mysql_list_fields($this->DBName, $tablename, $this->db_Conn);
		if($db_result > 0)
		{
			$intNumFields = mysql_num_fields($db_result);
			while(--$intNumFields >= 0)
			{
				$strColumnName = mysql_field_name($db_result, $intNumFields);

				$varnameFrom = $strIdentFrom.$strColumnName.$strSuffixFrom;
				$varnameTo = $strIdentTo.$strColumnName;
				global ${$varnameFrom}, ${$varnameTo};
				if((isset(${$varnameFrom}) || $bAlways))
				{
					if(is_array(${$varnameFrom}))
					{
						${$varnameTo} = array();
						foreach(${$varnameFrom} as $k => $v)
							${$varnameTo}[$k] = htmlspecialcharsbx($v);
					}
					else
						${$varnameTo} = htmlspecialcharsbx(${$varnameFrom});
				}
			}
		}
	}

	function GetTableFieldsList($table)
	{
		return array_keys($this->GetTableFields($table));
	}

	function GetTableFields($table)
	{
		if(!array_key_exists($table, $this->column_cache))
		{
			$this->column_cache[$table] = array();
			$this->DoConnect();
			$rs = @mysql_list_fields($this->DBName, $table, $this->db_Conn);
			if($rs > 0)
			{
				$intNumFields = mysql_num_fields($rs);
				while(--$intNumFields >= 0)
				{
					$ar = array(
						"NAME" => mysql_field_name($rs, $intNumFields),
						"TYPE" => mysql_field_type($rs, $intNumFields),
					);
					$this->column_cache[$table][$ar["NAME"]] = $ar;
				}
			}
		}
		return $this->column_cache[$table];
	}

	function LockTables($str)
	{
		register_shutdown_function(array(&$this, "UnLockTables"));
		$this->Query("LOCK TABLE ".$str, false, '', array("fixed_connection"=>true));
	}

	function UnLockTables()
	{
		$this->Query("UNLOCK TABLES", true, '', array("fixed_connection"=>true));
	}

	function Concat()
	{
		$str = "";
		$ar = func_get_args();
		if (is_array($ar)) $str .= implode(" , ", $ar);
		if (strlen($str)>0) $str = "concat(".$str.")";
		return $str;
	}

	function IsNull($expression, $result)
	{
		return "ifnull(".$expression.", ".$result.")";
	}

	function Length($field)
	{
		return "length($field)";
	}

	function ToChar($expr, $len=0)
	{
		return $expr;
	}

	function TableExists($tableName)
	{
		$tableName = preg_replace("/[^A-Za-z0-9%_]+/i", "", $tableName);
		$tableName = Trim($tableName);

		if (strlen($tableName) <= 0)
			return False;

		$dbResult = $this->Query("SHOW TABLES LIKE '".$this->ForSql($tableName)."'", false, '', array("fixed_connection"=>true));
		if ($arResult = $dbResult->Fetch())
			return True;
		else
			return False;
	}

	function IndexExists($tableName, $arColumns)
	{
		return $this->GetIndexName($tableName, $arColumns) !== "";
	}

	function GetIndexName($tableName, $arColumns, $bStrict = false)
	{
		if(!is_array($arColumns) || count($arColumns) <= 0)
			return "";

		$rs = $this->Query("SHOW INDEX FROM `".$this->ForSql($tableName)."`", true, '', array("fixed_connection"=>true));
		if(!$rs)
			return "";

		$arIndexes = array();
		while($ar = $rs->Fetch())
			$arIndexes[$ar["Key_name"]][$ar["Seq_in_index"]-1] = $ar["Column_name"];

		$strColumns = implode(",", $arColumns);
		foreach($arIndexes as $Key_name => $arKeyColumns)
		{
			ksort($arKeyColumns);
			$strKeyColumns = implode(",", $arKeyColumns);
			if($bStrict)
			{
				if($strKeyColumns === $strColumns)
					return $Key_name;
			}
			else
			{
				if(substr($strKeyColumns, 0, strlen($strColumns)) === $strColumns)
					return $Key_name;
			}
		}

		return "";
	}

	function SlaveConnection()
	{
		if(!class_exists('cmodule') || !class_exists('csqlwhere'))
			return null;

		if(!CModule::IncludeModule('cluster'))
			return false;

		$arSlaves = CClusterSlave::GetList();
		if(empty($arSlaves))
			return false;

		$max_slave_delay = COption::GetOptionInt("cluster", "max_slave_delay", 10);
		if(isset($_SESSION["BX_REDIRECT_TIME"]))
		{
			$redirect_delay = time() - $_SESSION["BX_REDIRECT_TIME"] + 1;
			if(
				$redirect_delay > 0
				&& $redirect_delay < $max_slave_delay
			)
				$max_slave_delay = $redirect_delay;
		}

		$total_weight = 0;
		foreach($arSlaves as $i=>$slave)
		{
			if(defined("BX_CLUSTER_GROUP") && BX_CLUSTER_GROUP != $slave["GROUP_ID"])
			{
				unset($arSlaves[$i]);
			}
			elseif($slave["ROLE_ID"] == "SLAVE")
			{
				$arSlaveStatus = CClusterSlave::GetStatus($slave["ID"], true, false, false);
				if(
					$arSlaveStatus['Seconds_Behind_Master'] > $max_slave_delay
					|| $arSlaveStatus['Last_SQL_Error'] != ''
					|| $arSlaveStatus['Last_IO_Error'] != ''
					|| $arSlaveStatus['Slave_SQL_Running'] === 'No'
				)
				{
					unset($arSlaves[$i]);
				}
				else
				{
					$total_weight += $slave["WEIGHT"];
				}
			}
			else
			{
				$total_weight += $slave["WEIGHT"];
			}
		}

		$found = false;
		foreach($arSlaves as $slave)
		{
			if(mt_rand(0, $total_weight) < $slave["WEIGHT"])
			{
				$found = $slave;
				break;
			}
		}

		if(!$found || $found["ROLE_ID"] != "SLAVE")
		{
			return false; //use main connection
		}
		else
		{
			ob_start();
			$conn = CDatabase::GetDBNodeConnection($found["ID"], true);
			ob_end_clean();

			if(is_object($conn))
			{
				return $conn;
			}
			else
			{
				self::$arNodes[$found["ID"]]["ONHIT_ERROR"] = true;
				CClusterDBNode::SetOffline($found["ID"]);
				return false; //use main connection
			}
		}
	}
}

class CDBResult extends CAllDBResult
{
	function CDBResult($res=NULL)
	{
		parent::CAllDBResult($res);
	}

	/**
	 * Returns next row of the select result in form of associated array
	 *
	 * @return array
	 */
	function Fetch()
	{
		global $DB;

		if($this->bNavStart || $this->bFromArray)
		{
			if(!is_array($this->arResult))
				$res = false;
			elseif($res = current($this->arResult))
				next($this->arResult);
		}
		elseif($this->SqlTraceIndex)
		{
			$start_time = microtime(true);

			if(!$this->arUserMultyFields)
			{
				$res = mysql_fetch_array($this->result, MYSQL_ASSOC);
			}
			else
			{
				$res = mysql_fetch_array($this->result, MYSQL_ASSOC);
				if($res)
					foreach($this->arUserMultyFields as $FIELD_NAME=>$flag)
						if($res[$FIELD_NAME])
							$res[$FIELD_NAME] = unserialize($res[$FIELD_NAME]);
			}

			if ($res && $this->arReplacedAliases)
			{
				foreach($this->arReplacedAliases as $tech => $human)
				{
					$res[$human] = $res[$tech];
					unset($res[$tech]);
				}
			}

			$exec_time = round(microtime(true) - $start_time, 10);
			$DB->addDebugTime($this->SqlTraceIndex, $exec_time);
			$DB->timeQuery += $exec_time;
		}
		else
		{
			if(!$this->arUserMultyFields)
			{
				$res = mysql_fetch_array($this->result, MYSQL_ASSOC);
			}
			else
			{
				$res = mysql_fetch_array($this->result, MYSQL_ASSOC);
				if($res)
					foreach($this->arUserMultyFields as $FIELD_NAME=>$flag)
						if($res[$FIELD_NAME])
							$res[$FIELD_NAME] = unserialize($res[$FIELD_NAME]);
			}

			if ($res && $this->arReplacedAliases)
			{
				foreach($this->arReplacedAliases as $tech => $human)
				{
					$res[$human] = $res[$tech];
					unset($res[$tech]);
				}
			}
		}

		return $res;
	}
 
	function SelectedRowsCount()
	{
		if($this->nSelectedCount !== false)
			return $this->nSelectedCount;

		if(is_resource($this->result))
			return mysql_num_rows($this->result);
		else
			return 0;
	}

	function AffectedRowsCount()
	{
		if(is_object($this) && is_object($this->DB))
		{
			/** @noinspection PhpUndefinedMethodInspection */
			$this->DB->DoConnect();
			return mysql_affected_rows($this->DB->db_Conn);
		}
		else
		{
			global $DB;
			$DB->DoConnect();
			return mysql_affected_rows($DB->db_Conn);
		}
	}

	function AffectedRowsCountEx()
	{
		if(is_resource($this->result) && mysql_num_rows($this->result) > 0)
			return 0;
		else
			return mysql_affected_rows();
	}

	function FieldsCount()
	{
		if(is_resource($this->result))
			return mysql_num_fields($this->result);
		else
			return 0;
	}

	function FieldName($iCol)
	{
		return mysql_field_name($this->result, $iCol);
	}

	function DBNavStart()
	{
		global $DB;

		//total rows count
		if(is_resource($this->result))
			$this->NavRecordCount = mysql_num_rows($this->result);
		else
			return;

		if($this->NavRecordCount < 1)
			return;

		if($this->NavShowAll)
			$this->NavPageSize = $this->NavRecordCount;

		//calculate total pages depend on rows count. start with 1
		$this->NavPageCount = floor($this->NavRecordCount/$this->NavPageSize);
		if($this->NavRecordCount % $this->NavPageSize > 0)
			$this->NavPageCount++;

		//page number to display. start with 1
		$this->NavPageNomer = ($this->PAGEN < 1 || $this->PAGEN > $this->NavPageCount? ($_SESSION[$this->SESS_PAGEN] < 1 || $_SESSION[$this->SESS_PAGEN] > $this->NavPageCount? 1:$_SESSION[$this->SESS_PAGEN]):$this->PAGEN);

		//rows to skip
		$NavFirstRecordShow = $this->NavPageSize * ($this->NavPageNomer-1);
		$NavLastRecordShow = $this->NavPageSize * $this->NavPageNomer;

		if($this->SqlTraceIndex)
			$start_time = microtime(true);

		mysql_data_seek($this->result, $NavFirstRecordShow);
		$temp_arrray = array();
		for($i=$NavFirstRecordShow; $i<$NavLastRecordShow; $i++)
		{
			if(($res = mysql_fetch_array($this->result, MYSQL_ASSOC)))
			{
				if($this->arUserMultyFields)
					foreach($this->arUserMultyFields as $FIELD_NAME=>$flag)
						if($res[$FIELD_NAME])
							$res[$FIELD_NAME] = unserialize($res[$FIELD_NAME]);

				if ($this->arReplacedAliases)
				{
					foreach($this->arReplacedAliases as $tech => $human)
					{
						$res[$human] = $res[$tech];
						unset($res[$tech]);
					}
				}

				$temp_arrray[] = $res;
			}
			else
			{
				break;
			}
		}

		if($this->SqlTraceIndex)
		{
			/** @noinspection PhpUndefinedVariableInspection */
			$exec_time = round(microtime(true) - $start_time, 10);
			$DB->addDebugTime($this->SqlTraceIndex, $exec_time);
			$DB->timeQuery += $exec_time;
		}

		$this->arResult = $temp_arrray;
	}

	function NavQuery($strSql, $cnt, $arNavStartParams)
	{
		global $DB;

		if(is_set($arNavStartParams, "SubstitutionFunction"))
		{
			$arNavStartParams["SubstitutionFunction"]($this, $strSql, $cnt, $arNavStartParams);
			return;
		}
		if(is_set($arNavStartParams, "bShowAll"))
			$bShowAll = $arNavStartParams["bShowAll"];
		else
			$bShowAll = true;

		if(is_set($arNavStartParams, "iNumPage"))
			$iNumPage = $arNavStartParams["iNumPage"];
		else
			$iNumPage = false;

		if(is_set($arNavStartParams, "bDescPageNumbering"))
			$bDescPageNumbering = $arNavStartParams["bDescPageNumbering"];
		else
			$bDescPageNumbering = false;

		$this->InitNavStartVars($arNavStartParams);
		$this->NavRecordCount = $cnt;

		if($this->NavShowAll)
			$this->NavPageSize = $this->NavRecordCount;

		//calculate total pages depend on rows count. start with 1
		$this->NavPageCount = ($this->NavPageSize>0 ? floor($this->NavRecordCount/$this->NavPageSize) : 0);
		if($bDescPageNumbering)
		{
			$makeweight = ($this->NavRecordCount % $this->NavPageSize);
			if($this->NavPageCount == 0 && $makeweight > 0)
				$this->NavPageCount = 1;

			//page number to display
			//if($iNumPage===false)
			//	$this->PAGEN = $this->NavPageCount;
			$this->NavPageNomer =
				(
					$this->PAGEN < 1 || $this->PAGEN > $this->NavPageCount
					?
						($_SESSION[$this->SESS_PAGEN] < 1 || $_SESSION[$this->SESS_PAGEN] > $this->NavPageCount
						?
							$this->NavPageCount
						:
							$_SESSION[$this->SESS_PAGEN]
						)
					:
						$this->PAGEN
				);

			//rows to skip
			$NavFirstRecordShow = 0;
			if($this->NavPageNomer != $this->NavPageCount)
				$NavFirstRecordShow += $makeweight;

			$NavFirstRecordShow += ($this->NavPageCount - $this->NavPageNomer) * $this->NavPageSize;
			$NavLastRecordShow = $makeweight + ($this->NavPageCount - $this->NavPageNomer + 1) * $this->NavPageSize;
		}
		else
		{
			if($this->NavPageSize && ($this->NavRecordCount % $this->NavPageSize > 0))
				$this->NavPageCount++;

			//calculate total pages depend on rows count. start with 1
			if($this->PAGEN >= 1 && $this->PAGEN <= $this->NavPageCount)
				$this->NavPageNomer = $this->PAGEN;
			elseif($_SESSION[$this->SESS_PAGEN] >= 1 && $_SESSION[$this->SESS_PAGEN] <= $this->NavPageCount)
				$this->NavPageNomer = $_SESSION[$this->SESS_PAGEN];
			elseif($arNavStartParams["checkOutOfRange"] !== true)
				$this->NavPageNomer = 1;
			else
				return;

			//rows to skip
			$NavFirstRecordShow = $this->NavPageSize*($this->NavPageNomer-1);
			$NavLastRecordShow = $this->NavPageSize*$this->NavPageNomer;
		}

		$NavAdditionalRecords = 0;
		if(is_set($arNavStartParams, "iNavAddRecords"))
			$NavAdditionalRecords = $arNavStartParams["iNavAddRecords"];

		if(!$this->NavShowAll)
			$strSql .= " LIMIT ".$NavFirstRecordShow.", ".($NavLastRecordShow - $NavFirstRecordShow + $NavAdditionalRecords);

		if(is_object($this->DB))
			$res_tmp = $this->DB->Query($strSql);
		else
			$res_tmp = $DB->Query($strSql);

		if($this->SqlTraceIndex)
			$start_time = microtime(true);

		$temp_arrray = array();
		$temp_arrray_add = array();
		$tmp_cnt = 0;

		while($ar = mysql_fetch_array($res_tmp->result, MYSQL_ASSOC))
		{
			$tmp_cnt++;
			if($this->arUserMultyFields)
				foreach($this->arUserMultyFields as $FIELD_NAME=>$flag)
					if($ar[$FIELD_NAME])
						$ar[$FIELD_NAME] = unserialize($ar[$FIELD_NAME]);

			if ($this->arReplacedAliases)
			{
				foreach($this->arReplacedAliases as $tech => $human)
				{
					$ar[$human] = $ar[$tech];
					unset($ar[$tech]);
				}
			}

			if (intval($NavLastRecordShow - $NavFirstRecordShow) > 0 && $tmp_cnt > ($NavLastRecordShow - $NavFirstRecordShow))
				$temp_arrray_add[] = $ar;
			else
				$temp_arrray[] = $ar;
		}

		if($this->SqlTraceIndex)
		{
			/** @noinspection PhpUndefinedVariableInspection */
			$exec_time = round(microtime(true) - $start_time, 10);
			$DB->addDebugTime($this->SqlTraceIndex, $exec_time);
			$DB->timeQuery += $exec_time;
		}

		$this->result = $res_tmp->result; // added for FieldsCount and other compatibility
		$this->arResult = count($temp_arrray)? $temp_arrray: false;
		$this->arResultAdd = count($temp_arrray_add)? $temp_arrray_add: false;
		$this->nSelectedCount = $cnt;
		$this->bDescPageNumbering = $bDescPageNumbering;
		$this->bFromLimited=true;
		$this->DB = $res_tmp->DB;
	}
}
