<?
###################################################
# askaron.agents module                           #
# Copyright (c) 2011-2014 Askaron Systems ltd.    #
# http://askaron.ru                               #
# mailto:mail@askaron.ru                          #
###################################################

class CAskaronAgents
{
	public static function OnPageStartHandler()
	{
		if ( $_SERVER["SCRIPT_FILENAME"] === $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/tools/cron_events.php" )
		{
			if ( COption::GetOptionString("main", "check_agents", "Y") !== "Y")
			{
				self::CheckAgents();
			}
		}
	}
		
	private static function CheckAgents()
	{
		global $CACHE_MANAGER;

		//For a while agents will execute only on primary cluster group
		if((defined("NO_AGENT_CHECK") && NO_AGENT_CHECK===true) || (defined("BX_CLUSTER_GROUP") && BX_CLUSTER_GROUP !== 1))
			return null;

		if(CACHED_b_agent !== false && $CACHE_MANAGER->Read(CACHED_b_agent, ($cache_id = "agents"), "agents"))
        {
			$saved_time = $CACHE_MANAGER->Get($cache_id);
			if(time() < $saved_time)
				return "";
		}

		return CAgent::ExecuteAgents("");
	}
}
?>