<?
/*
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2012 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################
*/
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/agent.php");

class CAgent extends CAllAgent
{
	function CheckAgents()
	{
		global $CACHE_MANAGER;

		//For a while agents will execute only on primary cluster group
		if((defined("NO_AGENT_CHECK") && NO_AGENT_CHECK===true) || (defined("BX_CLUSTER_GROUP") && BX_CLUSTER_GROUP !== 1))
			return;

		$agents_use_crontab = COption::GetOptionString("main", "agents_use_crontab", "N");
		$str_crontab = "";
		if($agents_use_crontab=="Y" || (defined("BX_CRONTAB_SUPPORT") && BX_CRONTAB_SUPPORT===true))
		{
			if(defined("BX_CRONTAB") && BX_CRONTAB===true)
				$str_crontab = " AND IS_PERIOD='N' ";
			else
				$str_crontab = " AND IS_PERIOD='Y' ";
		}

		$saved_time = 0;
		if(CACHED_b_agent !== false && $CACHE_MANAGER->Read(CACHED_b_agent, $cache_id = "agents".$str_crontab, "agents"))
		{
			$saved_time = $CACHE_MANAGER->Get($cache_id);
			if(time() < $saved_time)
				return "";
		}

		return CAgent::ExecuteAgents($str_crontab);
	}

	function ExecuteAgents($str_crontab)
	{
		global $DB, $DOCUMENT_ROOT, $CACHE_MANAGER;

		if(defined("BX_FORK_AGENTS_AND_EVENTS_FUNCTION"))
		{
			if(CMain::ForkActions(array("CAgent", "ExecuteAgents"), array($str_crontab)))
				return "";
		}

		$saved_time = 0;
		if(CACHED_b_agent !== false && $CACHE_MANAGER->Read(CACHED_b_agent, $cache_id = "agents".$str_crontab, "agents"))
		{
			$saved_time = $CACHE_MANAGER->Get($cache_id);
			if(time() < $saved_time)
				return "";
		}

		$uniq = COption::GetOptionString("main", "server_uniq_id", "");
		if(strlen($uniq)<=0)
		{
			$uniq = md5(uniqid(rand(), true));
			COption::SetOptionString("main", "server_uniq_id", $uniq);
		}

		$strSql = "
			SELECT 'x'
			FROM b_agent
			WHERE
				ACTIVE = 'Y'
				AND NEXT_EXEC <= now()
				AND (DATE_CHECK IS NULL OR DATE_CHECK <= now())
				".$str_crontab."
			LIMIT 1
		";

		$db_result_agents = $DB->Query($strSql);
		if($db_result_agents->Fetch())
		{
			$db_lock = $DB->Query("SELECT GET_LOCK('".$uniq."_agent', 0) as L");
			$ar_lock = $db_lock->Fetch();
			if($ar_lock["L"]=="0")
				return "";
		}
		else
		{
			if(CACHED_b_agent !== false)
			{
				$rs = $DB->Query("SELECT UNIX_TIMESTAMP(MIN(NEXT_EXEC))-UNIX_TIMESTAMP(NOW()) DATE_DIFF FROM b_agent WHERE ACTIVE='Y' ".$str_crontab."");
				$ar = $rs->Fetch();
				if(!$ar || $ar["DATE_DIFF"] < 0)
					$date_diff = 0;
				elseif($ar["DATE_DIFF"] > CACHED_b_agent)
					$date_diff = CACHED_b_agent;
				else
					$date_diff = $ar["DATE_DIFF"];

				if($saved_time > 0)
				{
					$CACHE_MANAGER->Clean($cache_id, "agents");
					$CACHE_MANAGER->Read(CACHED_b_agent, $cache_id, "agents");
				}
				$CACHE_MANAGER->Set($cache_id, intval(time()+$date_diff));
			}

			return "";
		}

		//$DB->LockTables("b_agent WRITE");
		$strSql=
			"SELECT ID, NAME, AGENT_INTERVAL, IS_PERIOD, MODULE_ID ".
			"FROM b_agent ".
			"WHERE ACTIVE='Y' ".
			"	AND NEXT_EXEC<=now() ".
			"	AND (DATE_CHECK IS NULL OR DATE_CHECK<=now()) ".
			$str_crontab.
			" ORDER BY SORT desc";

		$db_result_agents = $DB->Query($strSql);
		$i = 0;
		while($db_result_agents_array = $db_result_agents->Fetch())
 		{
			if($i==0)
			{
				@set_time_limit(0);
				ignore_user_abort(true);
				$i=1;
			}
			$agents_array[] = $db_result_agents_array;
			$strSql="UPDATE b_agent SET DATE_CHECK=DATE_ADD(IF(DATE_CHECK IS NULL, now(), DATE_CHECK), INTERVAL 600 SECOND) WHERE ID=".$db_result_agents_array["ID"];
			$DB->Query($strSql);
		}
		//$DB->UnLockTables();
		$DB->Query("SELECT RELEASE_LOCK('".$uniq."_agent')");

		for($i=0; $i<count($agents_array); $i++)
		{
			$arAgent = $agents_array[$i];

			@set_time_limit(0);

			if(strlen($arAgent["MODULE_ID"])>0 && $arAgent["MODULE_ID"]!="main")
			{
				if(!CModule::IncludeModule($arAgent["MODULE_ID"]))
					continue;
			}

			//these vars can be assigned within agent code
			$pPERIOD = $arAgent["AGENT_INTERVAL"];

			CTimeZone::Disable();

			global $USER;
			unset($USER);
			$eval_result = "";
			eval("\$eval_result=".$arAgent["NAME"]);
			unset($USER);
			CTimeZone::Enable();

			if(strlen($eval_result)<=0)
			{
				$strSql="DELETE FROM b_agent WHERE ID=".$arAgent["ID"];
			}
			else
			{
				if($arAgent["IS_PERIOD"]=="Y")
					$strSql="UPDATE b_agent SET NAME='".$DB->ForSQL($eval_result, 2000)."', LAST_EXEC=now(), NEXT_EXEC=DATE_ADD(NEXT_EXEC, INTERVAL ".$pPERIOD." SECOND), DATE_CHECK=NULL WHERE ID=".$arAgent["ID"];
				else
					$strSql="UPDATE b_agent SET NAME='".$DB->ForSQL($eval_result, 2000)."', LAST_EXEC=now(), NEXT_EXEC=DATE_ADD(now(), INTERVAL ".$pPERIOD." SECOND), DATE_CHECK=NULL WHERE ID=".$arAgent["ID"];
			}
			$DB->Query($strSql);
		}
	}
}
?>