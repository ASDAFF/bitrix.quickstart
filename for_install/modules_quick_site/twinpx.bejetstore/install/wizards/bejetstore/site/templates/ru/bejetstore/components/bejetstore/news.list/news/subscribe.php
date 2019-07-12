<?
define("NO_KEEP_STATISTIC", true);
define('NO_AGENT_CHECK', true);
define("NO_AGENT_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/templates/".SITE_TEMPLATE_ID."/subscribe.php");

//if(check_bitrix_sessid()){

if(isset($_REQUEST["unsubscribe"])){
	if($USER->IsAuthorized()){

		if(!CModule::IncludeModule("subscribe")){
			die('{ "error": "error" }');
		}

		$subscription = CSubscription::GetByEmail($USER->GetEmail());

	    if($subscription->ExtractFields("str_"))
	        $ID = (integer)$str_ID;
	    else
	        $ID=0;

		$res = CSubscription::Delete($ID);
		$APPLICATION->set_cookie("NEWS_SUBSCRIBED", 0, 0);
		echo '{ "success": "success" }';
	}else{
		$APPLICATION->set_cookie("NEWS_SUBSCRIBED", 0, 0);
		echo '{ "success": "success" }';
	}
}else{
	if(strlen($_POST["email"])){
		die('{ "error": "error" }');
	}

	if(strlen($_POST["ml"])){

		if(!CModule::IncludeModule("subscribe")){
			die('{ "error": "error" }');
		}

		$subscription = CSubscription::GetByEmail($_POST["ml"]);
		if($subscription->ExtractFields("str_"))
	        $ID = (integer)$str_ID;
	    else
	        $ID=0;

	    if($ID){
	    	$APPLICATION->set_cookie("NEWS_SUBSCRIBED", $ID, time()+60*60*24*30*12*2);
			echo '{ "success": "success" }';
	    }else{
	    	$rsSub = new CSubscription;

			$arFields = Array(
				"USER_ID" => ($USER->IsAuthorized()? $USER->GetID():false),
				"FORMAT" => "html",
				"EMAIL" => trim($_POST["ml"]),
				"ACTIVE" => "Y",
				"RUB_ID" => array(1),
				"SEND_CONFIRM" => "Y",
			);

			$ID = $rsSub->Add($arFields);

			if($ID>0){
			    CSubscription::Authorize($ID);
			    $APPLICATION->set_cookie("NEWS_SUBSCRIBED", $ID, time()+60*60*24*30*12*2);
			    echo '{ "success": "success" }';
			}else{
			   	die('{ "error": "error" }');
			   	//echo $rsSub->LAST_ERROR;
			}
	    }
	}
}
?>