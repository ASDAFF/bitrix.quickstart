<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/imaginweb.sms/classes/general/subscription.php");

class SMSCSubscription extends SMSCSubscriptionGeneral
{
	//get by e-mail
	function GetByPhone($phone)
	{
		global $DB;

		$strSql =
			"SELECT S.*, ".
			"	".$DB->DateToCharFunction("S.DATE_UPDATE", "FULL")." AS DATE_UPDATE, ".
			"	".$DB->DateToCharFunction("S.DATE_INSERT", "FULL")." AS DATE_INSERT, ".
			"	".$DB->DateToCharFunction("S.DATE_CONFIRM", "FULL")." AS DATE_CONFIRM ".
			"FROM iwebsms_subscription S ".
			"WHERE S.PHONE='".$DB->ForSQL($phone)."' ";

		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

	//agent to delete unconfirmed subscription
	function CleanUp()
	{
		global $DB;
		$interval = intval(COption::GetOptionString("imaginweb.sms", "subscribe_confirm_period"));
		if($interval > 0)
		{
			$strSql = 
				"SELECT ID ".
				"FROM iwebsms_subscription ".
				"WHERE CONFIRMED<>'Y' AND DATE_CONFIRM < DATE_ADD(now(), INTERVAL -".$interval." DAY) ";
			$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$sIn = "0";
			while($res_arr = $res->Fetch())
				$sIn .= ",".$res_arr["ID"];
			
			$DB->Query("DELETE FROM iwebsms_subscription_rubric WHERE SUBSCRIPTION_ID IN (".$sIn.")", false, "File: ".__FILE__."<br>Line: ".__LINE__);
			$DB->Query("DELETE FROM iwebsms_subscription WHERE ID IN (".$sIn.")", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		return "SMSCSubscription::CleanUp();";
	}
}
?>