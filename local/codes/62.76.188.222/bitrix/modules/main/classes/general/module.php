<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

define("MODULE_NOT_FOUND", 0);
define("MODULE_INSTALLED", 1);
define("MODULE_DEMO", 2);
define("MODULE_DEMO_EXPIRED", 3);

class CModule
{
	private static $includedModules = array("main" => true);
	private static $includedModulesEx = array();
	private static $classes = array();
	public static $installedModules = false;

	public static $events = array();

	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_VERSION;
	var $MODULE_ID;
	var $MODULE_SORT = 10000;
	var $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
	var $MODULE_GROUP_RIGHTS;

	function AddAutoloadClasses($module, $arParams = array())
	{
		if (!is_array($arParams) || empty($arParams))
			return false;

		$module = trim($module);

		if (defined("NO_BITRIX_AUTOLOAD") && NO_BITRIX_AUTOLOAD)
		{
			foreach ($arParams as $value)
				include_once($_SERVER["DOCUMENT_ROOT"].($module <> ''? BX_ROOT."/modules/".$module."/" : "").$value);
		}
		else
		{
			static $search  = 'QWERTYUIOPASDFGHJKLZXCVBNM';
			static $replace = 'qwertyuiopasdfghjklzxcvbnm';
			foreach ($arParams as $key => $value)
			{
				self::$classes[strtr($key, $search, $replace)] = array(
					"module" => $module,
					"file" => $value
				);
			}
		}

		return true;
	}

	function AutoloadClassDefined($className)
	{
		$className = trim($className);
		if ($className == '')
			return false;

		$className = strtolower($className);

		return array_key_exists($className, self::$classes);
	}

	static function RequireAutoloadClass($className)
	{
		$className = trim($className);
		if ($className == '')
			return false;

		static $search  = 'QWERTYUIOPASDFGHJKLZXCVBNM';
		static $replace = 'qwertyuiopasdfghjklzxcvbnm';

		$className = strtr($className, $search, $replace);

		if (isset(self::$classes[$className]))
		{
			if (self::$classes[$className]['module'] != '')
				$dir = BX_ROOT.'/modules/'.self::$classes[$className]['module'].'/';
			else
				$dir = '';

			require_once($_SERVER["DOCUMENT_ROOT"].$dir.self::$classes[$className]["file"]);
			return true;
		}

		return false;
	}

	function _GetCache()
	{
		global $DB, $CACHE_MANAGER;

		if (!self::$installedModules)
		{
			if($CACHE_MANAGER->Read(3600, "b_module"))
				self::$installedModules = $CACHE_MANAGER->Get("b_module");

			if(self::$installedModules === false)
			{
				self::$installedModules = array();
				$rs = $DB->Query("SELECT m.* FROM b_module m ORDER BY m.ID");
				while($ar = $rs->Fetch())
					self::$installedModules[$ar['ID']] = $ar;
				$CACHE_MANAGER->Set("b_module", self::$installedModules);
			}
		}

		return self::$installedModules;
	}

	function _GetName($arEvent)
	{
		$strName = '';
		if(array_key_exists("CALLBACK", $arEvent))
		{
			if(is_array($arEvent["CALLBACK"]))
				$strName .= (is_object($arEvent["CALLBACK"][0]) ? get_class($arEvent["CALLBACK"][0]) : $arEvent["CALLBACK"][0]).'::'.$arEvent["CALLBACK"][1];
			else
				$strName .= $arEvent["CALLBACK"];
		}
		else
		{
			$strName .= $arEvent["TO_CLASS"].'::'.$arEvent["TO_METHOD"];
		}
		if(isset($arEvent['TO_MODULE_ID']) && $arEvent['TO_MODULE_ID'] <> '')
			$strName .= ' ('.$arEvent['TO_MODULE_ID'].')';
		return $strName;
	}

	function InstallDB()
	{
		return false;
	}

	function UnInstallDB()
	{
	}

	function InstallEvents()
	{
	}

	function UnInstallEvents()
	{
	}

	function InstallFiles()
	{
	}

	function UnInstallFiles()
	{
	}

	function DoInstall()
	{
	}

	function GetModuleTasks()
	{
		return array(
			/*
			"NAME" => array(
				"LETTER" => "",
				"BINDING" => "",
				"OPERATIONS" => array(
					"NAME",
					"NAME",
				),
			),
			*/
		);
	}

	function InstallTasks()
	{
		global $DB, $CACHE_MANAGER;

		$sqlMODULE_ID = $DB->ForSQL($this->MODULE_ID, 50);

		$arDBOperations = array();
		$rsOperations = $DB->Query("SELECT NAME FROM b_operation WHERE MODULE_ID = '$sqlMODULE_ID'");
		while($ar = $rsOperations->Fetch())
			$arDBOperations[$ar["NAME"]] = $ar["NAME"];

		$arDBTasks = array();
		$rsTasks = $DB->Query("SELECT NAME FROM b_task WHERE MODULE_ID = '$sqlMODULE_ID' AND SYS = 'Y'");
		while($ar = $rsTasks->Fetch())
			$arDBTasks[$ar["NAME"]] = $ar["NAME"];

		$arModuleTasks = $this->GetModuleTasks();
		foreach($arModuleTasks as $task_name => $arTask)
		{
			$sqlBINDING = isset($arTask["BINDING"]) && $arTask["BINDING"] <> ''? $DB->ForSQL($arTask["BINDING"], 50): 'module';
			$sqlTaskOperations = array();

			if(isset($arTask["OPERATIONS"]) && is_array($arTask["OPERATIONS"]))
			{
				foreach($arTask["OPERATIONS"] as $operation_name)
				{
					$operation_name = substr($operation_name, 0, 50);

					if(!isset($arDBOperations[$operation_name]))
					{
						$DB->Query("
							INSERT INTO b_operation
							(NAME, MODULE_ID, BINDING)
							VALUES
							('".$DB->ForSQL($operation_name)."', '$sqlMODULE_ID', '$sqlBINDING')
						", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

						$arDBOperations[$operation_name] = $operation_name;
					}

					$sqlTaskOperations[] = $DB->ForSQL($operation_name);
				}
			}

			$task_name = substr($task_name, 0, 100);
			$sqlTaskName = $DB->ForSQL($task_name);

			if(!isset($arDBTasks[$task_name]) && $task_name <> '')
			{
				$DB->Query("
					INSERT INTO b_task
					(NAME, LETTER, MODULE_ID, SYS, BINDING)
					VALUES
					('$sqlTaskName', '".$DB->ForSQL($arTask["LETTER"], 1)."', '$sqlMODULE_ID', 'Y', '$sqlBINDING')
				", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			}

			if(!empty($sqlTaskOperations) && $task_name <> '')
			{
				$DB->Query("
					INSERT INTO b_task_operation
					(TASK_ID,OPERATION_ID)
					SELECT T.ID TASK_ID, O.ID OPERATION_ID
					FROM
						b_task T
						,b_operation O
					WHERE
						T.SYS='Y'
						AND T.NAME='$sqlTaskName'
						AND O.NAME in ('".implode("','", $sqlTaskOperations)."')
						AND O.NAME not in (
							SELECT O2.NAME
							FROM
								b_task T2
								inner join b_task_operation TO2 on TO2.TASK_ID = T2.ID
								inner join b_operation O2 on O2.ID = TO2.OPERATION_ID
							WHERE
								T2.SYS='Y'
								AND T2.NAME='$sqlTaskName'
						)
				", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			}
		}

		if(is_object($CACHE_MANAGER))
		{
			$CACHE_MANAGER->CleanDir("b_task");
			$CACHE_MANAGER->CleanDir("b_task_operation");
		}
	}

	function UnInstallTasks()
	{
		global $DB, $CACHE_MANAGER;

		$sqlMODULE_ID = $DB->ForSQL($this->MODULE_ID, 50);

		$DB->Query("
			DELETE FROM b_group_task
			WHERE TASK_ID IN (
				SELECT T.ID
				FROM b_task T
				WHERE T.MODULE_ID = '$sqlMODULE_ID'
			)
		", false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$DB->Query("
			DELETE FROM b_task_operation
			WHERE TASK_ID IN (
				SELECT T.ID
				FROM b_task T
				WHERE T.MODULE_ID = '$sqlMODULE_ID')
		", false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$DB->Query("
			DELETE FROM b_operation
			WHERE MODULE_ID = '$sqlMODULE_ID'
		", false, "File: ".__FILE__."<br>Line: ".__LINE__);

		$DB->Query("
			DELETE FROM b_task
			WHERE MODULE_ID = '$sqlMODULE_ID'
		", false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if(is_object($CACHE_MANAGER))
		{
			$CACHE_MANAGER->CleanDir("b_task");
			$CACHE_MANAGER->CleanDir("b_task_operation");
		}
	}

	function IsInstalled()
	{
		if (!self::$installedModules)
			CModule::_GetCache();
		return isset(self::$installedModules[$this->MODULE_ID]);
	}

	function DoUninstall()
	{
	}

	function Remove()
	{
		global $DB,$CACHE_MANAGER;
		$DB->Query("DELETE FROM b_module WHERE ID='".$this->MODULE_ID."'");
		$CACHE_MANAGER->Clean("b_module");
		self::$installedModules = false;
	}

	function Add()
	{
		global $DB, $CACHE_MANAGER;
		$DB->Query(
			"INSERT INTO b_module(ID) ".
			"VALUES('".$this->MODULE_ID."')"
		);
		unset(self::$includedModules[$this->MODULE_ID]);
		unset(self::$includedModulesEx[$this->MODULE_ID]);
		$CACHE_MANAGER->Clean("b_module");
		self::$installedModules = false;
	}

	function GetList()
	{
		$result = new CDBResult;
		$result->InitFromArray(CModule::_GetCache());
		return $result;
	}

	/**
	 * Makes module classes and function available. Returns true on success.
	 *
	 * @param string $module_name
	 * @return bool
	 */
	public static function IncludeModule($module_name)
	{
		/** @noinspection PhpUnusedLocalVariableInspection */
		global $DB, $MESS;

		if(defined("SM_SAFE_MODE") && SM_SAFE_MODE===true)
		{
			if(!in_array($module_name, array("main", "fileman")))
				return false;
		}

		if(isset(self::$includedModules[$module_name]))
			return self::$includedModules[$module_name];

		if (!self::$installedModules)
			CModule::_GetCache();

		if(!array_key_exists($module_name, self::$installedModules))
		{
			self::$includedModules[$module_name] = false;
			return false;
		}

		if(!file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$module_name."/include.php"))
		{
			self::$includedModules[$module_name] = false;
			return false;
		}

		$res = include_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$module_name."/include.php");
		if($res === false)
		{
			self::$includedModules[$module_name] = false;
			return false;
		}

		self::$includedModules[$module_name] = true;
		return true;
	}

	function IncludeModuleEx($module_name)
	{
		if (is_set(self::$includedModulesEx, $module_name))
			return self::$includedModulesEx[$module_name];

		$module_name_tmp = str_replace(".", "_", $module_name);

		if (CModule::IncludeModule($module_name))
		{
			if (defined($module_name_tmp."_DEMO") && constant($module_name_tmp."_DEMO") == "Y")
				self::$includedModulesEx[$module_name] = MODULE_DEMO;
			else
				self::$includedModulesEx[$module_name] = MODULE_INSTALLED;

			return self::$includedModulesEx[$module_name];
		}

		if (defined($module_name_tmp."_DEMO") && constant($module_name_tmp."_DEMO") == "Y")
		{
			self::$includedModulesEx[$module_name] = MODULE_DEMO_EXPIRED;
			return MODULE_DEMO_EXPIRED;
		}

		self::$includedModulesEx[$module_name] = MODULE_NOT_FOUND;
		return MODULE_NOT_FOUND;
	}

	function err_mess()
	{
		return "<br>Class: CModule;<br>File: ".__FILE__;
	}

	function GetDropDownList($strSqlOrder="ORDER BY ID")
	{
		global $DB;
		$err_mess = (CModule::err_mess())."<br>Function: GetDropDownList<br>Line: ";
		$strSql = "
			SELECT
				ID as REFERENCE_ID,
				ID as REFERENCE
			FROM
				b_module
			$strSqlOrder
			";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
		return $res;
	}

	function CreateModuleObject($moduleId)
	{
		$moduleId = trim($moduleId);
		$moduleId = preg_replace("/[^a-zA-Z0-9_.]+/i", "", $moduleId);
		if ($moduleId == '')
			return false;

		$path = $_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$moduleId."/install/index.php";
		if (!file_exists($path))
			return false;

		include_once($path);

		$className = str_replace(".", "_", $moduleId);
		if (!class_exists($className))
			return false;

		return new $className;
	}
}


// register autoload
if (!function_exists("__autoload"))
{
	if (function_exists('spl_autoload_register'))
	{
		spl_autoload_register(array('CModule', 'RequireAutoloadClass'));
	}
	else
	{
		function __autoload($className)
		{
			CModule::RequireAutoloadClass($className);
		}
	}

	define("NO_BITRIX_AUTOLOAD", false);
}
else
{
	define("NO_BITRIX_AUTOLOAD", true);
}

function RegisterModule($id)
{
	$m = new CModule;
	$m->MODULE_ID = $id;
	$m->Add();

	foreach(GetModuleEvents("main", "OnAfterRegisterModule", true) as $arEvent)
		ExecuteModuleEventEx($arEvent, array($id));
}

function UnRegisterModule($id)
{
	global $DB;

	$DB->Query("DELETE FROM b_agent WHERE MODULE_ID='".$DB->ForSQL($id)."'");
	CMain::DelGroupRight($id);

	$m = new CModule;
	$m->MODULE_ID = $id;
	$m->Remove();

	foreach(GetModuleEvents("main", "OnAfterUnRegisterModule", true) as $arEvent)
		ExecuteModuleEventEx($arEvent, array($id));
}

function AddEventHandler($FROM_MODULE_ID, $MESSAGE_ID, $CALLBACK, $SORT=100, $FULL_PATH = false)
{
	$arEvent = array("FROM_MODULE_ID"=>$FROM_MODULE_ID, "MESSAGE_ID"=>$MESSAGE_ID, "CALLBACK"=>$CALLBACK, "SORT"=>$SORT, "FULL_PATH"=>$FULL_PATH, "VERSION" => 1);
	$arEvent['TO_NAME'] = CModule::_GetName($arEvent);

	$FROM_MODULE_ID = strtoupper($FROM_MODULE_ID);
	$MESSAGE_ID = strtoupper($MESSAGE_ID);

	if (!isset(CModule::$events[$FROM_MODULE_ID]) || !is_array(CModule::$events[$FROM_MODULE_ID]))
		CModule::$events[$FROM_MODULE_ID] = array();

	$arEvents = &CModule::$events[$FROM_MODULE_ID];

	if (!isset($arEvents[$MESSAGE_ID]) || !is_array($arEvents[$MESSAGE_ID]))
		$arEvents[$MESSAGE_ID] = array();

	$iEventHandlerKey = count($arEvents[$MESSAGE_ID]);

	$arEvents[$MESSAGE_ID][$iEventHandlerKey] = $arEvent;

	uasort($arEvents[$MESSAGE_ID], create_function('$a, $b', 'if($a["SORT"] == $b["SORT"]) return 0; return ($a["SORT"] < $b["SORT"])? -1 : 1;'));

	if (class_exists("\\Bitrix\\Main\\EventManager"))
	{
		$eventManager = \Bitrix\Main\EventManager::getInstance();
		/** @noinspection PhpDeprecationInspection */
		$eventManager->addEventHandlerOld($FROM_MODULE_ID, $MESSAGE_ID, $CALLBACK, $FULL_PATH, $SORT);
	}

	return $iEventHandlerKey;
}

function RemoveEventHandler($FROM_MODULE_ID, $MESSAGE_ID, $iEventHandlerKey)
{
	$FROM_MODULE_ID = strtoupper($FROM_MODULE_ID);
	$MESSAGE_ID = strtoupper($MESSAGE_ID);

	if(is_array(CModule::$events[$FROM_MODULE_ID][$MESSAGE_ID]))
	{
		if(isset(CModule::$events[$FROM_MODULE_ID][$MESSAGE_ID][$iEventHandlerKey]))
		{
			unset(CModule::$events[$FROM_MODULE_ID][$MESSAGE_ID][$iEventHandlerKey]);
			return true;
		}
	}

	return false;
}

function GetModuleEvents($MODULE_ID, $MESSAGE_ID, $bReturnArray = false)
{
	global $DB, $CACHE_MANAGER;
	static $init = false;

	if($init === false)
	{
		if($CACHE_MANAGER->Read(3600, "b_module_to_module"))
		{
			$arEvents = $CACHE_MANAGER->Get("b_module_to_module");
		}
		else
		{
			$arEvents = array();
			$rs = $DB->Query("
				SELECT
					*
				FROM
					b_module_to_module m2m
				INNER JOIN b_module m ON (m2m.TO_MODULE_ID = m.ID)
				ORDER BY SORT
			");
			while($ar = $rs->Fetch())
			{
				$ar['TO_NAME'] = CModule::_GetName($ar);
				$ar["~FROM_MODULE_ID"] = strtoupper($ar["FROM_MODULE_ID"]);
				$ar["~MESSAGE_ID"] = strtoupper($ar["MESSAGE_ID"]);
				if ($ar["TO_METHOD_ARG"] <> '')
					$ar["TO_METHOD_ARG"] = unserialize($ar["TO_METHOD_ARG"]);
				else
					$ar["TO_METHOD_ARG"] = array();
				$arEvents[] = $ar;
			}
			$CACHE_MANAGER->Set("b_module_to_module", $arEvents);
		}

		if(!is_array($arEvents))
			$arEvents = array();

		$copy_MAIN_MODULE_EVENTS = CModule::$events;

		foreach($arEvents as $ar)
		{
			if (intval($ar["VERSION"]) < 2)
				CModule::$events[$ar["~FROM_MODULE_ID"]][$ar["~MESSAGE_ID"]][] = $ar;
		}

		// need to re-sort because of AddEventHandler() calls
		foreach($copy_MAIN_MODULE_EVENTS as $module => $temp1)
			foreach($copy_MAIN_MODULE_EVENTS[$module] as $message => $temp2)
				sortByColumn(CModule::$events[$module][$message], "SORT");

		$init = true;
	}

	$MODULE_ID = strtoupper($MODULE_ID);
	$MESSAGE_ID = strtoupper($MESSAGE_ID);
	if(array_key_exists($MODULE_ID, CModule::$events) && array_key_exists($MESSAGE_ID, CModule::$events[$MODULE_ID]))
		$arrResult = CModule::$events[$MODULE_ID][$MESSAGE_ID];
	else
		$arrResult = array();

	if($bReturnArray)
	{
		return $arrResult;
	}
	else
	{
		$resRS = new CDBResult;
		$resRS->InitFromArray($arrResult);
		return $resRS;
	}
}

function ExecuteModuleEvent($arEvent, $param1=NULL, $param2=NULL, $param3=NULL, $param4=NULL, $param5=NULL, $param6=NULL, $param7=NULL, $param8=NULL, $param9=NULL, $param10=NULL)
{
	$CNT_PREDEF = 10;
	$r = true;
	if($arEvent["TO_MODULE_ID"] <> '' && $arEvent["TO_MODULE_ID"] <> 'main')
	{
		if(!CModule::IncludeModule($arEvent["TO_MODULE_ID"]))
			return null;
		$r = include_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$arEvent["TO_MODULE_ID"]."/include.php");
	}
	elseif($arEvent["TO_PATH"] <> '' && file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT.$arEvent["TO_PATH"]))
	{
		$r = include_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT.$arEvent["TO_PATH"]);
	}
	elseif($arEvent["FULL_PATH"]<>"" && file_exists($arEvent["FULL_PATH"]))
	{
		$r = include_once($arEvent["FULL_PATH"]);
	}

	if(($arEvent["TO_CLASS"] == '' || $arEvent["TO_METHOD"] == '') && !is_set($arEvent, "CALLBACK"))
		return $r;

	$args = array();
	if (is_array($arEvent["TO_METHOD_ARG"]) && count($arEvent["TO_METHOD_ARG"]) > 0)
	{
		foreach ($arEvent["TO_METHOD_ARG"] as $v)
			$args[] = $v;
	}

	$nArgs = func_num_args();
	for($i = 1; $i <= $CNT_PREDEF; $i++)
	{
		if($i > $nArgs)
			break;
		$args[] = &${"param".$i};
	}

	for($i = $CNT_PREDEF + 1; $i < $nArgs; $i++)
		$args[] = func_get_arg($i);

	if(is_set($arEvent, "CALLBACK"))
	{
		$resmod = call_user_func_array($arEvent["CALLBACK"], $args);
	}
	else
	{
		//php bug: http://bugs.php.net/bug.php?id=47948
		class_exists($arEvent["TO_CLASS"]);
		$resmod = call_user_func_array(array($arEvent["TO_CLASS"], $arEvent["TO_METHOD"]), $args);
	}

	return $resmod;
}

function ExecuteModuleEventEx($arEvent, $arParams = array())
{
	$r = true;

	if(
		isset($arEvent["TO_MODULE_ID"])
		&& $arEvent["TO_MODULE_ID"]<>""
		&& $arEvent["TO_MODULE_ID"]<>"main"
	)
	{
		if(CModule::IncludeModule($arEvent["TO_MODULE_ID"]))
			$r = include_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/".$arEvent["TO_MODULE_ID"]."/include.php");
		else
			return null;
	}
	elseif(
		isset($arEvent["TO_PATH"])
		&& $arEvent["TO_PATH"]<>""
		&& file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT.$arEvent["TO_PATH"])
	)
	{
		$r = include_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT.$arEvent["TO_PATH"]);
	}
	elseif(
		isset($arEvent["FULL_PATH"])
		&& $arEvent["FULL_PATH"]<>""
		&& file_exists($arEvent["FULL_PATH"])
	)
	{
		$r = include_once($arEvent["FULL_PATH"]);
	}

	if(array_key_exists("CALLBACK", $arEvent))
	{
		if(isset($arEvent["TO_METHOD_ARG"]) && is_array($arEvent["TO_METHOD_ARG"]) && count($arEvent["TO_METHOD_ARG"]))
			$args = array_merge($arEvent["TO_METHOD_ARG"], $arParams);
		else
			$args = $arParams;

		return call_user_func_array($arEvent["CALLBACK"], $args);
	}
	elseif($arEvent["TO_CLASS"] != "" && $arEvent["TO_METHOD"] != "")
	{
		if(is_array($arEvent["TO_METHOD_ARG"]) && count($arEvent["TO_METHOD_ARG"]))
			$args = array_merge($arEvent["TO_METHOD_ARG"], $arParams);
		else
			$args = $arParams;

		//php bug: http://bugs.php.net/bug.php?id=47948
		class_exists($arEvent["TO_CLASS"]);
		return call_user_func_array(array($arEvent["TO_CLASS"], $arEvent["TO_METHOD"]), $args);
	}
	else
	{
		return $r;
	}
}

function UnRegisterModuleDependences($FROM_MODULE_ID, $MESSAGE_ID, $TO_MODULE_ID, $TO_CLASS="", $TO_METHOD="", $TO_PATH="", $TO_METHOD_ARG = array())
{
	global $DB, $CACHE_MANAGER;

	$TO_METHOD_ARG = ((!is_array($TO_METHOD_ARG) || is_array($TO_METHOD_ARG) && count($TO_METHOD_ARG) <= 0) ? "" : serialize($TO_METHOD_ARG));

	$strSql = "DELETE FROM b_module_to_module ".
			"WHERE FROM_MODULE_ID='".$DB->ForSql($FROM_MODULE_ID)."'".
			"	AND MESSAGE_ID='".$DB->ForSql($MESSAGE_ID)."' ".
			"	AND TO_MODULE_ID='".$DB->ForSql($TO_MODULE_ID)."' ".
			($TO_CLASS <> ''?
				"	AND TO_CLASS='".$DB->ForSql($TO_CLASS)."' ":
				"	AND (TO_CLASS='' OR TO_CLASS IS NULL) ").
			($TO_METHOD <> ''?
				"	AND TO_METHOD='".$DB->ForSql($TO_METHOD)."'":
				"	AND (TO_METHOD='' OR TO_METHOD IS NULL) ").
			($TO_PATH <> '' && $TO_PATH !== 1/*controller disconnect correction*/?
				"	AND TO_PATH='".$DB->ForSql($TO_PATH)."'":
				"	AND (TO_PATH='' OR TO_PATH IS NULL) ").
			($TO_METHOD_ARG <> ''?
				"	AND TO_METHOD_ARG='".$DB->ForSql($TO_METHOD_ARG)."'":
				"	AND (TO_METHOD_ARG='' OR TO_METHOD_ARG IS NULL) ");
	$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
	$CACHE_MANAGER->Clean("b_module_to_module");
}

function RegisterModuleDependences($FROM_MODULE_ID, $MESSAGE_ID, $TO_MODULE_ID, $TO_CLASS="", $TO_METHOD="", $SORT=100, $TO_PATH="", $TO_METHOD_ARG = array())
{
	global $DB, $CACHE_MANAGER;

	$TO_METHOD_ARG = ((!is_array($TO_METHOD_ARG) || is_array($TO_METHOD_ARG) && count($TO_METHOD_ARG) <= 0) ? "" : serialize($TO_METHOD_ARG));

	$r = $DB->Query(
		"SELECT 'x' ".
		"FROM b_module_to_module ".
		"WHERE FROM_MODULE_ID='".$DB->ForSql($FROM_MODULE_ID)."'".
		"	AND MESSAGE_ID='".$DB->ForSql($MESSAGE_ID)."' ".
		"	AND TO_MODULE_ID='".$DB->ForSql($TO_MODULE_ID)."' ".
		"	AND TO_CLASS='".$DB->ForSql($TO_CLASS)."' ".
		"	AND TO_METHOD='".$DB->ForSql($TO_METHOD)."'".
		($TO_PATH == ''?
			"	AND (TO_PATH='' OR TO_PATH IS NULL)"
			:"	AND TO_PATH='".$DB->ForSql($TO_PATH)."'"
		).
		($TO_METHOD_ARG == ''?
			"	AND (TO_METHOD_ARG='' OR TO_METHOD_ARG IS NULL)"
			:"	AND TO_METHOD_ARG='".$DB->ForSql($TO_METHOD_ARG)."'"
		)
	);

	if(!$r->Fetch())
	{
		$arFields = array(
			"SORT" => intval($SORT),
			"FROM_MODULE_ID" => "'".$DB->ForSql($FROM_MODULE_ID)."'",
			"MESSAGE_ID" => "'".$DB->ForSql($MESSAGE_ID)."'",
			"TO_MODULE_ID" => "'".$DB->ForSql($TO_MODULE_ID)."'",
			"TO_CLASS" => "'".$DB->ForSql($TO_CLASS)."'",
			"TO_METHOD" => "'".$DB->ForSql($TO_METHOD)."'",
			"TO_PATH" => "'".$DB->ForSql($TO_PATH)."'",
			"TO_METHOD_ARG" => "'".$DB->ForSql($TO_METHOD_ARG)."'",
			"VERSION" => 1,
		);
		$DB->Insert("b_module_to_module",$arFields, "FILE: ".__FILE__."<br>LINE: ".__LINE__);
		$CACHE_MANAGER->Clean("b_module_to_module");
	}
}

function IsModuleInstalled($module_id)
{
	if (!CModule::$installedModules)
		CModule::_GetCache();
	return isset(CModule::$installedModules[$module_id]);
}

function GetModuleID($str)
{
	$arr = explode("/",$str);
	$i = array_search("modules",$arr);
	return $arr[$i+1];
}

/**
 * Returns TRUE if version1 >= version2
 * version1 = "XX.XX.XX"
 * version2 = "XX.XX.XX"
 */
function CheckVersion($version1, $version2)
{
	$arr1 = explode(".",$version1);
	$arr2 = explode(".",$version2);
	if (intval($arr2[0])>intval($arr1[0])) return false;
	elseif (intval($arr2[0])<intval($arr1[0])) return true;
	else
	{
		if (intval($arr2[1])>intval($arr1[1])) return false;
		elseif (intval($arr2[1])<intval($arr1[1])) return true;
		else
		{
			if (intval($arr2[2])>intval($arr1[2])) return false;
			elseif (intval($arr2[2])<intval($arr1[2])) return true;
			else return true;
		}
	}
}
