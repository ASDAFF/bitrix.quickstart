<?

function findsub($userid,&$arResult)
{
    $subscription_ureg  = CSubscription::GetList(array("ID" => "ASC"),array("USER_ID" => $userid));
    while(($subscr_arr = $subscription_ureg->Fetch()))
    {
        if(preg_match("/^[ \+\-\(\)0-9]+?@phone\.sms$/i",$subscr_arr["EMAIL"]) && $arResult["SHOW_SMS_FORM"])
        {
            return $subscr_arr["EMAIL"];
        }
        elseif($arResult["SHOW_POST_FORM"] && !preg_match("/^[ \+\-\(\)0-9]+?@phone\.sms$/i",$subscr_arr["EMAIL"]))
        {
            return $subscr_arr["EMAIL"];
        }
    }   
    return '';
}

//some hint
class  CSubscriptionMod extends CSubscription
{
	public $template_id = '';
	
	//message with subscription confirmation
	function ConfirmEvent($ID, $SITE_ID=SITE_ID)
	{
		static $SITE_DIR_CACHE = array();
		$subscr = CSubscription::GetByID($ID);
		if($subscr_arr = $subscr->Fetch())
		{
			if(!array_key_exists($SITE_ID, $SITE_DIR_CACHE))
			{
				$db_lang = CLang::GetByID($SITE_ID);
				if($ar_lang = $db_lang->Fetch())
					$SITE_DIR_CACHE[$SITE_ID] = $ar_lang["DIR"];
				else
					$SITE_DIR_CACHE[$SITE_ID] = LANG_DIR;
			}

			$subscr_arr["USER_NAME"] = "";
			$subscr_arr["USER_LAST_NAME"] = "";
			if(intval($subscr_arr["USER_ID"]) > 0)
			{
				$rsUser = CUser::GetByID($subscr_arr["USER_ID"]);
				if($arUser = $rsUser->Fetch())
				{
					$subscr_arr["USER_NAME"] = $arUser["NAME"];
					$subscr_arr["USER_LAST_NAME"] = $arUser["LAST_NAME"];
				}
			}

			$arFields = Array(
				"ID" => $subscr_arr["ID"],
				"EMAIL" => $subscr_arr["EMAIL"],
				"CONFIRM_CODE" => $subscr_arr["CONFIRM_CODE"],
				"USER_NAME" => $subscr_arr["USER_NAME"]." ".$subscr_arr["USER_LAST_NAME"],
				"DATE_SUBSCR" => ($subscr_arr["DATE_UPDATE"] <> ""? $subscr_arr["DATE_UPDATE"]: $subscr_arr["DATE_INSERT"]),
				"SUBSCR_SECTION" => str_replace(
					array("#SITE_DIR#", "#LANG_DIR#"),
					array($SITE_DIR_CACHE[$SITE_ID], $SITE_DIR_CACHE[$SITE_ID]),
					COption::GetOptionString("subscribe", "subscribe_section")
				),
			);
			
			//sending with ID of template, that we get from user
			CEvent::Send("SUBSCRIBE_CONFIRM", $SITE_ID, $arFields, "Y", $this->template_id);
			return true;
		}
		return false;
	}
}

//kill postfix from str
function kill_post_fix($str = '')
{
	$parts = explode("@",$str);
	return $parts[0];
}
//adding phone.sms to the telephone number
function add_postfix($str = '')
{
	$trig = false;
	$parts = explode("@",$str);
	foreach($parts as $index)
	{
		if ($index == "phone.sms")
		{
			$trig = true;
		}
	}
	if (!$trig) 
		$str = $str."@phone.sms"; 
	return $str;
}
?>