<?
IncludeModuleLangFile(__FILE__);
define("POSTING_TEMPLATE_DIR", substr(BX_PERSONAL_ROOT, 1)."/php_interface/imaginweb.sms/templates");

class SMSCPostingTemplate
{
	var $LAST_ERROR="";
	//Get list
	function GetList()
	{
		$arTemplates = array();
		$dir = $_SERVER["DOCUMENT_ROOT"]."/".POSTING_TEMPLATE_DIR;
		if(is_dir($dir) && ($dh = opendir($dir)))
		{
			while (($file = readdir($dh)) !== false)
				if(is_dir($dir."/".$file) && $file!="." && $file!="..")
					$arTemplates[]=POSTING_TEMPLATE_DIR."/".$file;
			closedir($dh);
		}
		return $arTemplates;
	}

	function GetByID($path="")
	{
		global $MESS;
		if(!SMSCPostingTemplate::IsExists($path))
			return false;
		$arTemplate = array();
		$strFileName= $_SERVER["DOCUMENT_ROOT"]."/".$path."/lang/".LANGUAGE_ID."/description.php";
		if(file_exists($strFileName)) include($strFileName);
		$strFileName= $_SERVER["DOCUMENT_ROOT"]."/".$path."/description.php";
		if(file_exists($strFileName)) include($strFileName);
		$arTemplate["PATH"] = $path;
		return $arTemplate;
	}

	function IsExists($path="")
	{
		if(substr($path, 0, strlen(POSTING_TEMPLATE_DIR)+1) !== POSTING_TEMPLATE_DIR."/")
			return false;

		$template = substr($path, strlen(POSTING_TEMPLATE_DIR)+1);
		if(
			strpos($template, "\0") !== false
			|| strpos($template, "\\") !== false
			|| strpos($template, "/") !== false
			|| strpos($template, "..") !== false
		)
		{
			return false;
		}

		return is_dir($_SERVER["DOCUMENT_ROOT"]."/".$path);
	}

	function Execute()
	{
		global $DB;

		$rubrics = SMSCRubric::GetList(array(), array("ACTIVE"=>"Y", "AUTO"=>"Y"));
		$current_time = time();
		$arCDate = localtime($current_time);
		$ct = $arCDate[0]+$arCDate[1]*60+$arCDate[2]*3600; //number of seconds science midnight
		$time_of_exec = false;
		$result = "";
		while(($arRubric=$rubrics->Fetch()) && $time_of_exec===false)
		{
			if(strlen($arRubric["LAST_EXECUTED"])>0)
				$last_executed = MakeTimeStamp(ConvertDateTime($arRubric["LAST_EXECUTED"], "DD.MM.YYYY HH:MI:SS"), "DD.MM.YYYY HH:MI:SS");
			else
				continue;
			//parse schedule
			$arDoM = SMSCPostingTemplate::ParseDaysOfMonth($arRubric["DAYS_OF_MONTH"]);
			$arDoW = SMSCPostingTemplate::ParseDaysOfWeek($arRubric["DAYS_OF_WEEK"]);
			$arToD = SMSCPostingTemplate::ParseTimesOfDay($arRubric["TIMES_OF_DAY"]);
			if($arToD)
				sort($arToD, SORT_NUMERIC);
			$arSDate = localtime($last_executed);
			//le = number of seconds scince midnight
			$le = $arSDate[0]+$arSDate[1]*60+$arSDate[2]*3600;
			//sdate = truncate(last_execute)
			$sdate = mktime(0, 0, 0, $arSDate[4]+1, $arSDate[3], $arSDate[5]+1900);
			while($sdate < $current_time && $time_of_exec===false)
			{
				$arSDate = localtime($sdate);
				if($arSDate[6]==0) $arSDate[6]=7;
				//determine if date is good for execution
				if($arDoM)
				{
					$flag = array_search($arSDate[3], $arDoM);
					if($arDoW)
						$flag = array_search($arSDate[6], $arDoW);
				}
				elseif($arDoW)
					$flag = array_search($arSDate[6], $arDoW);
				else
					$flag=false;
				if($flag!==false && $arToD)
					foreach($arToD as $intToD)
					{
						if($sdate+$intToD >  $last_executed && $sdate+$intToD <= $current_time)
						{
							$time_of_exec = $sdate+$intToD;
							break;
						}
					}
				$sdate = mktime(0, 0, 0, date("m",$sdate), date("d",$sdate)+1, date("Y",$sdate));//next day
			}
			if($time_of_exec!==false)
			{
				$arRubric["START_TIME"] = ConvertTimeStamp($last_executed, "FULL");
				$arRubric["END_TIME"] = ConvertTimeStamp($time_of_exec, "FULL");
				$arRubric["SITE_ID"] = $arRubric["LID"];
				SMSCPostingTemplate::AddPosting($arRubric);
			}
			$result = "SMSCPostingTemplate::Execute();";
		}
		return $result;
	}

	function AddPosting($arRubric)
	{
		global $DB, $USER, $MESS;
		if(!is_object($USER)) $USER = new CUser;
		//Include language file for template.php
		$rsSite = CSite::GetByID($arRubric["SITE_ID"]);
		$arSite = $rsSite->Fetch();
		$rsLang = CLanguage::GetByID($arSite["LANGUAGE_ID"]);
		$arLang = $rsLang->Fetch();

		$arFields=false;
		if(SMSCPostingTemplate::IsExists($arRubric["TEMPLATE"]))
		{
			$strFileName= $_SERVER["DOCUMENT_ROOT"]."/".$arRubric["TEMPLATE"]."/lang/".$arSite["LANGUAGE_ID"]."/template.php";
			if(file_exists($strFileName))
				include($strFileName);
			//Execute template
			$strFileName= $_SERVER["DOCUMENT_ROOT"]."/".$arRubric["TEMPLATE"]."/template.php";
			if(file_exists($strFileName))
			{
				ob_start();
				$arFields = @include($strFileName);
				$strBody = ob_get_contents();
				ob_end_clean();
			}
		}
		//If there was an array returned then add posting
		if(is_array($arFields))
		{
			$arFields["BODY"] = $strBody;
			$cPosting = new SMSCPosting;
			$arFields["AUTO_SEND_TIME"]=$arRubric["END_TIME"];
			$arFields["RUB_ID"]=array($arRubric["ID"]);
			$arFields["MSG_CHARSET"] = $arLang["CHARSET"];
			$ID = $cPosting->Add($arFields);
			if($ID)
			{
				if(array_key_exists("FILES", $arFields))
				{
					foreach($arFields["FILES"] as $arFile)
						$cPosting->SaveFile($ID, $arFile);
				}
				if(!array_key_exists("DO_NOT_SEND", $arFields) || $arFields["DO_NOT_SEND"]!="Y")
				{
					$cPosting->ChangeStatus($ID, "P");
					if(COption::GetOptionString("imaginweb.sms", "imaginweb.sms_auto_method")!=="cron")
						CAgent::AddAgent("SMSCPosting::AutoSend(".$ID.",true,\"".$arRubric["LID"]."\");", "imaginweb.sms", "N", 0, $arRubric["END_TIME"], "Y", $arRubric["END_TIME"]);
				}
			}
		}
		//Update last execution time mark
		$strSql = "UPDATE iwebsms_list_rubric SET LAST_EXECUTED=".$DB->CharToDateFunction($arRubric["END_TIME"])." WHERE ID=".intval($arRubric["ID"]);
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	function ParseDaysOfMonth($strDaysOfMonth)
	{
		$arResult=array();
		if(strlen($strDaysOfMonth) > 0)
		{
			$arDoM = explode(",", $strDaysOfMonth);
			$arFound = array();
			foreach($arDoM as $strDoM)
			{
				if(preg_match("/^(\d{1,2})$/", trim($strDoM), $arFound))
				{
					if(intval($arFound[1]) < 1 || intval($arFound[1]) > 31)
						return false;
					else
						$arResult[]=intval($arFound[1]);
				}
				elseif(preg_match("/^(\d{1,2})-(\d{1,2})$/", trim($strDoM), $arFound))
				{
					if(intval($arFound[1]) < 1 || intval($arFound[1]) > 31 || intval($arFound[2]) < 1 || intval($arFound[2]) > 31 || intval($arFound[1]) >= intval($arFound[2]))
						return false;
					else
						for($i=intval($arFound[1]);$i<=intval($arFound[2]);$i++)
							$arResult[]=intval($i);
				}
				else
					return false;
			}
		}
		else
			return false;
		return $arResult;
	}

	function ParseDaysOfWeek($strDaysOfWeek)
	{
		if(strlen($strDaysOfWeek) <= 0)
			return false;

		$arResult = array();

		$arDoW = explode(",", $strDaysOfWeek);
		foreach($arDoW as $strDoW)
		{
			$arFound = array();
			if(
				preg_match("/^(\d)$/", trim($strDoW), $arFound)
				&& $arFound[1] >= 1
				&& $arFound[1] <= 7
			)
			{
				$arResult[]=intval($arFound[1]);
			}
			else
			{
				return false;
			}
		}

		return $arResult;
	}

	function ParseTimesOfDay($strTimesOfDay)
	{
		if(strlen($strTimesOfDay) <= 0)
			return false;

		$arResult = array();

		$arToD = explode(",", $strTimesOfDay);
		foreach($arToD as $strToD)
		{
			$arFound = array();
			if(
				preg_match("/^(\d{1,2}):(\d{1,2})$/", trim($strToD), $arFound)
				&& $arFound[1] <= 23
				&& $arFound[2] <= 59
			)
			{
				$arResult[] = intval($arFound[1]) * 3600 + intval($arFound[2]) * 60;
			}
			else
			{
				return false;
			}
		}

		return $arResult;
	}
}
?>
