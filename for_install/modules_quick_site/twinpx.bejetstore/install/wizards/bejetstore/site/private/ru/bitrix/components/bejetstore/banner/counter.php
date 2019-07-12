<?
define("NO_KEEP_STATISTIC", true);
define('NO_AGENT_CHECK', true);
define("NO_AGENT_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (check_bitrix_sessid())
{
	if(
		isset($_REQUEST["id"])
		&& isset($_REQUEST["device"])
	)
	{
		COption::SetOptionString("advertising","DONT_FIX_BANNER_SHOWS","N");
		CAdvBanner::FixShow(array(
		  "FIX_SHOW" => "Y",
		  "ID" => intval($_REQUEST["id"])
		));
		COption::SetOptionString("advertising","DONT_FIX_BANNER_SHOWS","Y");
	}
	
}

die();
?>