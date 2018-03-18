<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("rarus.sms4b")!="D") 
{
	$aMenu = array	(
					"parent_menu" => "global_menu_services",
					"section" => "sms4b",
					"sort" => 100,
					"text" => GetMessage("SMS4B_MENU_MAIN"),
					"title" => GetMessage("SMS4B_MENU_MAIN_TITLE"),
					"url" => "sms4b_index.php?lang=".LANGUAGE_ID,
					"icon" => "sms4b_menu_icon",
					"page_icon" => "sms4b_page_icon",
					"items_id" => "menu_sms4b",
					"items" => array(
										array(
											"text" => GetMessage("SMS4B_SENDSMS"),
											"url" => "sms4b_sendsms.php?lang=".LANGUAGE_ID,
											"title" => GetMessage("SMS4B_SENDSMS_ALT"),
											"more_url" => Array()
											),
										array(
											"text" => GetMessage("SMS4B_BALANCE"),
											"url" => "sms4b_balance.php?lang=".LANGUAGE_ID,
											"title" => GetMessage("SMS4B_BALANCE_ALT"),
											"more_url" => Array()
											),				
										array(
											"text" => GetMessage("SMS4B_SMS_LIST_OUT"),
											"url" => "sms4b_sms_out_list.php?lang=".LANGUAGE_ID,
											"title" => GetMessage("SMS4B_SMS_LIST_OUT_ALT"),
											"more_url" => Array()
											),	
										array(
											"text" => GetMessage("SMS4B_SMS_LIST_INC"),
											"url" => "sms4b_sms_inc_list.php?lang=".LANGUAGE_ID,
											"title" => GetMessage("SMS4B_SMS_LIST_INC_ALT"),
											"more_url" => Array()
											),	
									)
					);

	return $aMenu;
}
return false;
?>
