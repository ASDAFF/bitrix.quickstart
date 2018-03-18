<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*
------------------------------------------------------------------------------
	sms4b_module.config
	version 1.1.0
	Wizard for adjustment of sms4b module.
	Needed for convenient get of demo account.
------------------------------------------------------------------------------
*/

/*
	functions for correct viewing of forms elements
	@ShowCheckedSelectField - for select
	@ShowCheckedCheckboxField - for checkbox
*/
function ShowCheckedSelectField($arr, $arname, $checkedKey, $params = "")
{
	if (is_array($arr) && !empty($arr)){
		$res = "<select name = '$arname' $params >";
		foreach($arr as $key => $val)
		{
			$res .= "<option value = '$key' ";
			if (($key == $checkedKey) || ($val == $checkedKey))
			{
				$res .= " SELECTED ";
			}
			$res .= "> $val </option>";
		}
		$res .= "</select>";
		return $res;
	}
	else
	{
		return false;
	}
}

function ShowCheckedCheckboxField ($name, $val, $is_checked, $params = "")
{
	$res = "<input type = 'checkbox' name = '$name' value = $val";
	if (($is_checked == 'Y') || ($is_checked == 'y'))
		$res .= " checked ";
	if (strlen($params) > 0)
		$res .= $params;
	$res .= " >";
	return $res;
}


//step 0 - simple info
class Step0 extends CWizardStep
{
	function InitStep()
	{
		if (IsModuleInstalled("rarus.sms4b")){
			$this->SetTitle(GetMessage("WW_STEP0_TITLE"));
			$this->SetStepID("step0");
			$this->SetNextStep("step1");
			$this->SetCancelStep("cancel");
		}
		else
		{
			$this->SetTitle(GetMessage("WW_ERROR_TITLE"));
			$this->SetStepID("error");
			$this->SetCancelStep("cancel");
			$this->SetCancelCaption(GetMessage("WW_CLOSE"));
		}

	}

	function ShowStep()
	{
		if (IsModuleInstalled("rarus.sms4b"))
		{
			$this->content = GetMessage("WW_STEP0_DESCR");
		}
		else
		{
			$this->content = GetMessage("WW_ERROR_DESCR");
		}
	}
}
// step 1 general options
class Step1 extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WW_STEP1_TITLE"));
		$this->SetNextStep("step2");
		$this->SetStepID("step1");
		$this->SetPrevStep("step0");
		$this->SetCancelStep("cancel");
	}

	function ShowStep()
	{
		$arsGmt = array(	4 => "(+4) ".GetMessage('MOSCOW'),
				3 => "(+3) ".GetMessage('KALININGRAD'),
				6 => "(+6) ".GetMessage('EKATA'),
				7 => "(+7) ".GetMessage('OMSK'),
				8 => "(+8) ".GetMessage('KEMEROVO'),
				9 => "(+9) ".GetMessage('IRKYTSK'),
				10 => "(+10) ".GetMessage('CHITA'),
				11 => "(+11) ".GetMessage('VLADIVOSTOK'),
				12 => "(+12) ".GetMessage('MAGA'),
		);

		$curr_proxy_host 	= COption::GetOptionString("rarus.sms4b", "proxy_host");
		$curr_proxy_port	= COption::GetOptionString("rarus.sms4b", "proxy_port");
		$curr_proxy_use		= COption::GetOptionString("rarus.sms4b", "proxy_use");
		$curr_login			= COption::GetOptionString("rarus.sms4b", "login");
		$curr_password		= COption::GetOptionString("rarus.sms4b", "password");
		$curr_gmt 			= COption::GetOptionString("rarus.sms4b", "gmt");
		$send_email		= COption::GetOptionString("rarus.sms4b", "send_email");

		$this->content .= '<table cellspacing=2%>';

		$this->content .= '<tr><td>'.GetMessage("WW_STEP2_1_TEXT4").': </td><td>'.ShowCheckedCheckboxField("proxy_use", "Y", $curr_proxy_use).'</td></tr>';
		$this->content .= '<tr><td>'.GetMessage("WW_STEP2_1_TEXT2").":</span> </td><td><input type = 'text' name = 'proxy_host'  size = '20' value = $curr_proxy_host></td></tr>";
		$this->content .= '<tr><td>'.GetMessage("WW_STEP2_1_TEXT3").":</span></td><td><input type = 'text' name = 'proxy_port'  size = '20' value = $curr_proxy_port></td></tr>";
		$this->content .= '<tr><td>'.GetMessage("WW_STEP2_1_TEXT5").": </td><td><input type = 'text' name = 'login' size = '20' value = $curr_login></td></tr>";
		$this->content .= '<tr><td>'.GetMessage("WW_STEP2_1_TEXT6").": </td><td><input type = 'password' name = 'password' size = '20' value = $curr_password></td></tr>";
		$this->content .= '<tr><td>'.GetMessage("WW_STEP2_1_TEXT7").': </td><td>'.ShowCheckedSelectField($arsGmt, "gmt", $curr_gmt, "style = 'width: 365px !important;'").'</td></tr>';

		$this->content .= '<tr><td>'.GetMessage("WW_STEP2_1_TEXT9").': </td><td>'.ShowCheckedCheckboxField("send_email", "Y", $send_email).'</td></tr>';
		$this->content .= '</table>';
	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		if ($wizard->IsNextButtonClick())
		{
			$wizard->SetVar("proxy_host", $_REQUEST["proxy_host"]);
			$wizard->SetVar("proxy_port", $_REQUEST["proxy_port"]);
			$wizard->SetVar("proxy_use", $_REQUEST["proxy_use"]);
			$wizard->SetVar("login", $_REQUEST["login"]);
			$wizard->SetVar("password", $_REQUEST["password"]);
			$wizard->SetVar("gmt", $_REQUEST["gmt"]);
			$wizard->SetVar("sms_sym_count", $_REQUEST["sms_sym_count"]);
			$wizard->SetVar("send_email", $_REQUEST["send_email"]);

			COption::RemoveOption("rarus.sms4b","proxy_host");
			COption::RemoveOption("rarus.sms4b","proxy_port");
			COption::RemoveOption("rarus.sms4b","proxy_use");
			COption::RemoveOption("rarus.sms4b","login");
			COption::RemoveOption("rarus.sms4b","password");
			COption::RemoveOption("rarus.sms4b","gmt");

			$proxy_host = $wizard->GetVar("proxy_host");
			$proxy_port = $wizard->GetVar("proxy_port");
			$proxy_use = $wizard->GetVar("proxy_use");
			$login = $wizard->GetVar("login");
			$password = $wizard->GetVar("password");
			$gmt = $wizard->GetVar("gmt");
			$use_translit = $wizard->GetVar("use_translit");
			$send_email = $wizard->GetVar("send_email");

			COption::SetOptionString("rarus.sms4b", "proxy_host", $proxy_host);
			COption::SetOptionString("rarus.sms4b", "proxy_port", $proxy_port);
			COption::SetOptionString("rarus.sms4b", "proxy_use", $proxy_use);
			COption::SetOptionString("rarus.sms4b", "login", $login);
			COption::SetOptionString("rarus.sms4b", "password", $password);
			COption::SetOptionString("rarus.sms4b", "gmt", $gmt);
			COption::SetOptionString("rarus.sms4b", "send_email", $send_email);
		}
	}
}


// step 2 Send SMS options
class Step2 extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WW_STEP2_TITLE"));
		$this->SetNextStep("step3");
		$this->SetStepID("step2");
		$this->SetPrevStep("step1");
		$this->SetCancelStep("cancel");
	}

	function ShowStep()
	{
		//get wizard object
		$wizard = &$this->GetWizard();

		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="asc", Array());
		while($arRes = $rsSites->GetNext())
		{
			$siteList[] = Array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);
		}
		//сохран€ем массив со свойствами заказа
		if  (CModule::IncludeModule("sale"))
		{
			$db_props = CSaleOrderProps::GetList(
				array("SORT" => "ASC"),
				array(),
				false,
				false,
				array("NAME", "CODE", "PERSON_TYPE_ID")
			);

			while ($props = $db_props->Fetch())
			{
				$orderProps[] = $props;
				$personTypes[] = $props['PERSON_TYPE_ID'];

			}
			$personTypes = array_unique($personTypes);
			/* Ќаходи типы плательщиков */
			$db_ptype = CSalePersonType::GetList(Array("SORT" => "ASC"));
			while ($ptype = $db_ptype->Fetch())
			{
			   $person[$ptype['ID']] =  $ptype['NAME'];
			}
		}


		$siteCount = count($siteList);
		CModule::IncludeModule("rarus.sms4b");

		global $SMS4B;
		$arrDefSender = $SMS4B->GetSender();

		foreach($arrDefSender as $val)
		{
			$arrDF[addslashes(htmlspecialchars_decode($val))] = addslashes(htmlspecialchars_decode($val));
		}
			$this->content.= "<script language=\"javascript\">
var cur_site = \"" . CUtil::JSEscape($siteList[0]["ID"]). "\";

function changeSiteList(value, add_id)
{
	var SLHandler = document.getElementById(add_id + '_site_id');
	SLHandler.disabled = value;
}";
$this->content .= "
function selectSite(current, add_id)
{
	if (current == cur_site) return;
	var last_handler = document.getElementById('par_' + add_id + '_' + cur_site);
	var current_handler = document.getElementById('par_' + add_id + '_' + current);
	var CSHandler = document.getElementById(add_id + '_current_site');

	last_handler.style.display = 'none';
	current_handler.style.display = 'inline';

	cur_site = current;
	CSHandler.value = current;

	return;
}
</script>
	<table>
	<tr>
	<td valign=\"top\" width=\"50%\" align=\"right\">" . GetMessage("SMO_DIF_SETTINGS") . " </td>
	<td valign=\"top\" width=\"50%\"><input type=\"checkbox\" name=\"SITE_dif_settings\" id=\"SITE_dif_settings\"";

	if (COption::GetOptionString("rarus.sms4b", "SITE_different_set", "N") == "Y")
	$this->content .= " checked=\"checked\" ";

	$this->content .=  " OnClick=\"changeSiteList(!this.checked, 'SITE')\" /></td>
	</tr>
		<tr>
		<td valign=\"top\" align=\"right\">" . GetMessage("SMO_SITE_LIST") . "</td>
		<td valign=\"top\"><select name=\"site\" id=\"SITE_site_id\"";
		if (COption::GetOptionString("rarus.sms4b", "SITE_different_set", "N") != "Y")
			$this->content .= " disabled=\"disabled\"";

		 $this->content .= "  onChange=\"selectSite(this.value, 'SITE')\"> ";

				for($i = 0; $i < $siteCount; $i++)
					$this->content .= "<option value=\"".($siteList[$i]["ID"])."\">".($siteList[$i]["NAME"])."</option>";

		 $this->content .=	"</select><input type=\"hidden\" name=\"SITE_current_site\" id=\"SITE_current_site\" value=\"".($siteList[0]["ID"]) ."\" /></td>
	</tr>
	</table>";
	if (count($arrDefSender) == 0 || !isset($arrDefSender))
	{
		$this->content .= "<p><span style='color:red'>".GetMessage('WW_STEP3_1_ERROR_TEXT1')."</span></p>";
		$this->content .= "<p><span style='color:green'>".GetMessage('WW_STEP3_1_NOTE_TEXT1')."</span></p>";
	}
	else
	{
		for ($i = 0; $i < $siteCount; $i++)
		{
			$defsender = COption::GetOptionString('rarus.sms4b', 'defsender', '', $siteList[$i]["ID"]);
			$defsenderPublic = COption::GetOptionString('rarus.sms4b', 'defsenderPublic', '', $siteList[$i]["ID"]);
			$use_translit = COption::GetOptionString('rarus.sms4b', 'use_translit', '', $siteList[$i]["ID"]);

			if (CModule::IncludeModule("sale"))
			{
				$phone_number_code = COption::GetOptionString('rarus.sms4b', 'phone_number_code', '', $siteList[$i]["ID"]);
			}
			//echo "<pre>"; print_r($siteList); echo "</pre>";
		$this->content .= "<div  id=\"par_SITE_" . $siteList[$i]["ID"] . "\" style=\"display:" . ($i == 0 ? "inline" : "none") ."\">
			<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" width=\"100%\" align=\"center\">
				<tr style=\"text-align:center\"><td colspan=\"2\"><b>". GetMessage("SMS4B_TAB_SEND"). "</b></td></tr>
				<tr>
					<td valign=\"top\" align=\"right\" width=\"50%\">" . GetMessage("opt_defsender") . "</td>
					<td valign=\"top\">
						<select name=\"defsender[" . $siteList[$i]["ID"] . "]\">";
							foreach ($arrDF as $ardefsender)
							{
				$this->content .= "<option value=\"" . $ardefsender ."\"" . ($ardefsender ==$defsender ? " selected=\"selected\" " : "") . ">" . $ardefsender . "</option>";
							}
				$this->content .="</select>
					</td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"right\">" .  GetMessage("use_translit") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"use_translit[".$siteList[$i]["ID"]. "]\" value=\"Y\"" . ($use_translit == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>";
				if (IsModuleInstalled("sale"))
				{
				$this->content .= "<tr>
					<td valign=\"top\" align=\"right\">". GetMessage("phone_number_code") . "</td>
					<td valign=\"top\">
						<select name=\"phone_number_code[" . $siteList[$i]["ID"] . "]\">";
							foreach ($orderProps as $prop)
							{
				$this->content .= "<option value=\"" . $prop['CODE'] ."\"" . ($prop['CODE'] == $phone_number_code ? " selected=\"selected\" " : "") . ">" . $prop['NAME'] . " (" .$person[$prop['PERSON_TYPE_ID']] . ")" . "</option>";
							}
				$this->content .="</select></td>
				</tr>";
				}
				$this->content .= "<tr>
				<td valign=\"top\" align=\"right\">" . GetMessage("defsenderPublic") . "</td>
				<td valign=\"top\">
					<select name=\"defsenderPublic[" . $siteList[$i]["ID"]. "]\">";
						foreach ($arrDF as $ardefsender)
						{
							$this->content .=	"<option value=\"" . $ardefsender . "\""  . ($ardefsender ==$defsenderPublic ? " selected=\"selected\"" : "") . ">" . $ardefsender . "</option>";
						}
					$this->content .= "</select>
				</td>
			</tr>
			</table>
			</div>";
		}
	}

	}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="asc", Array());
		while($arRes = $rsSites->GetNext())
		{
			$siteList[] = Array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);
		}
		$siteCount = count($siteList);
		COption::RemoveOption("rarus.sms4b", "use_translit");
		COption::RemoveOption("rarus.sms4b", "defsender");
		COption::RemoveOption("rarus.sms4b", "defsenderPublic");
		COption::RemoveOption("rarus.sms4b", "phone_number_code");

		$wizard->SetVar('SITE_dif_settings', $_REQUEST["SITE_dif_settings"]);

		if (!empty($_REQUEST["SITE_dif_settings"]))
			{
				for ($i = 0; $i < $siteCount; $i++)
				{
					COption::SetOptionString("rarus.sms4b", "use_translit", trim($_REQUEST["use_translit"][$siteList[$i]["ID"]]), GetMessage('use_translit'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "defsender", trim($_REQUEST["defsender"][$siteList[$i]["ID"]]), GetMessage('opt_defsender'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "phone_number_code", trim($_REQUEST["phone_number_code"][$siteList[$i]["ID"]]), GetMessage('phone_number_code'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "defsenderPublic", trim($_REQUEST["defsenderPublic"][$siteList[$i]["ID"]]), GetMessage('defsenderPublic'), $siteList[$i]["ID"]);
				}
				COption::SetOptionString("rarus.sms4b", "SITE_different_set", "Y", GetMessage('SITE_different_set'));
			}
			else
			{
				$site_id = trim($_REQUEST["SITE_current_site"]);
				COption::SetOptionString("rarus.sms4b", "use_translit", trim($_REQUEST["use_translit"][$site_id]), GetMessage('use_translit'));
				COption::SetOptionString("rarus.sms4b", "defsender", trim($_REQUEST["defsender"][$site_id]), GetMessage('opt_defsender'));
				COption::SetOptionString("rarus.sms4b", "phone_number_code", trim($_REQUEST["phone_number_code"][$site_id]), GetMessage('phone_number_code'));
				COption::SetOptionString("rarus.sms4b", "defsenderPublic", trim($_REQUEST["defsenderPublic"][$site_id]), GetMessage('defsenderPublic'));

				COption::SetOptionString("rarus.sms4b", "SITE_different_set", "N", GetMessage('SITE_different_set'));
			}

	}

}

// step 3 setting options for module events
class Step3 extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WW_STEP3_TITLE"));
		$this->SetStepID("step3");
		$this->SetPrevStep("step2");
		$this->SetNextStep("step4");
		$this->SetCancelStep("cancel");
	}

	function ShowStep()
	{
		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="asc", Array());
		while($arRes = $rsSites->GetNext())
		{
			$siteList[] = Array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);
		}
		$siteCount = count($siteList);
		CModule::IncludeModule("rarus.sms4b");
		global $SMS4B;
		$arrDefSender = $SMS4B->GetSender();

		$wizard = &$this->GetWizard();
		$changeSite = $wizard->GetVar('SITE_dif_settings');


		foreach($arrDefSender as $val)
		{
			$arrDF[addslashes(htmlspecialchars_decode($val))] = addslashes(htmlspecialchars_decode($val));
		}

		if (!empty($changeSite))
		{

			$this->content.= "<script language=\"javascript\">
var cur_site = \"" . CUtil::JSEscape($siteList[0]["ID"]). "\";

function changeSiteList(value, add_id)
{
	var SLHandler = document.getElementById(add_id + '_site_id');
	SLHandler.disabled = value;
}";
$this->content .= "
function selectSite(current, add_id)
{
	if (current == cur_site) return;
	var last_handler = document.getElementById('par_' + add_id + '_' + cur_site);
	var current_handler = document.getElementById('par_' + add_id + '_' + current);
	var CSHandler = document.getElementById(add_id + '_current_site');

	last_handler.style.display = 'none';
	current_handler.style.display = 'inline';

	cur_site = current;
	CSHandler.value = current;

	return;
}
</script>
	<table>
	<tr>
	<td valign=\"top\" width=\"50%\" align=\"right\">" . GetMessage("SMO_DIF_SETTINGS") . " </td>
	<td valign=\"top\" width=\"50%\"><input type=\"checkbox\" name=\"SITE_dif_settings\" id=\"SITE_dif_settings\"";

	if (COption::GetOptionString("rarus.sms4b", "SITE_different_set", "N") == "Y")
	$this->content .= " checked=\"checked\" disabled";

	$this->content .=  " OnClick=\"changeSiteList(!this.checked, 'SITE')\" /></td>
	</tr>
		<tr>
		<td valign=\"top\" align=\"right\">" . GetMessage("SMO_SITE_LIST") . "</td>
		<td valign=\"top\"><select name=\"site\" id=\"SITE_site_id\"";
		if (COption::GetOptionString("rarus.sms4b", "SITE_different_set", "N") != "Y")
			$this->content .= " disabled=\"disabled\"";

		 $this->content .= "  onChange=\"selectSite(this.value, 'SITE')\"> ";

				for($i = 0; $i < $siteCount; $i++)
					$this->content .= "<option value=\"".($siteList[$i]["ID"])."\">".($siteList[$i]["NAME"])."</option>";

		 $this->content .=	"</select>
	</tr>
	</table>";
	}

	$this->content .= "<input type=\"hidden\" name=\"SITE_current_site\" id=\"SITE_current_site\" value=\"".($siteList[0]["ID"]) ."\" /></td>";

	if (count($arrDefSender) == 0 || !isset($arrDefSender))
	{
		$this->content .= "<p><span style='color:red'>".GetMessage('WW_STEP3_1_ERROR_TEXT1')."</span></p>";
		$this->content .= "<p><span style='color:green'>".GetMessage('WW_STEP3_1_NOTE_TEXT1')."</span></p>";
	}
	else
	{
		for ($i = 0; $i < $siteCount; $i++)
		{

			if (CModule::IncludeModule("subscribe"))
			{
				$event_subscribe_confirm = COption::GetOptionString('rarus.sms4b', 'event_subscribe_confirm', '', $siteList[$i]["ID"]);
			}

			if (CModule::IncludeModule("sale"))
			{
				$event_sale_new_order = COption::GetOptionString('rarus.sms4b', 'event_sale_new_order', '', $siteList[$i]["ID"]);
				$event_sale_order_paid = COption::GetOptionString('rarus.sms4b', 'event_sale_order_paid', '', $siteList[$i]["ID"]);
				$event_sale_order_delivery = COption::GetOptionString('rarus.sms4b', 'event_sale_order_delivery', '', $siteList[$i]["ID"]);
				$event_sale_order_cancel = COption::GetOptionString('rarus.sms4b', 'event_sale_order_cancel', '', $siteList[$i]["ID"]);

				$arStatus = CSaleStatus::GetList(
					array("ID"=> "ASC"),
					array("LID" => "ru"),
					false,
					false,
					array("NAME", "ID")
					);
					$arSaleStatus = "";
					$arAdminStatus = "";
					while ($status = $arStatus->GetNext() )
					{
						$arSaleStatus['sale'][] = array(
							"event_sale_status_".$status['ID'] => COption::GetOptionString('rarus.sms4b', "event_sale_status_".$status['ID'], '', $siteList[$i]["ID"]),
							"NAME" => $status['NAME'],
						);
					}
			}
			if (IsModuleInstalled("support"))
			{
				$event_ticket_new_for_techsupport = COption::GetOptionString('rarus.sms4b', 'event_ticket_new_for_techsupport', '', $siteList[$i]["ID"]);
			}
		$this->content .= "<div  id=\"par_SITE_" . $siteList[$i]["ID"] . "\" style=\"display:" . ($i == 0 ? "inline" : "none") ."\">
			<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" width=\"100%\" align=\"center\">
			<tr style=\"text-align:center\"><td colspan=\"2\"><b>". GetMessage("SMS4B_TAB_SEND_EVENTS"). "</b></td></tr>
			<tr>
				<td valign=\"top\" align=\"right\" width=\"50%\">" .  GetMessage('opt_subscribe_confirm') ."</td>
				<td valign=\"top\"><input type=\"checkbox\" name=\"event_subscribe_confirm[" . $siteList[$i]["ID"]. "]\" value=\"Y\"" . ($event_subscribe_confirm == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
			</tr>";
			if (IsModuleInstalled("support"))
			{
			$this->content .= "<tr>
				<td valign=\"top\" align=\"right\">" .  GetMessage("opt_ticket_new_for_techsupport") . "</td>
				<td valign=\"top\"><input type=\"checkbox\" name=\"event_ticket_new_for_techsupport[" . $siteList[$i]["ID"]. "]\" value=\"Y\"" . ($event_ticket_new_for_techsupport == 'Y'? " checked = \"checked\" " : "" ). "/></td>
			</tr>";
			}

			if (IsModuleInstalled("sale"))
			{
			$this->content .=	"<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_new_order") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"event_sale_new_order[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($event_sale_new_order == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_order_paid"). "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"event_sale_order_paid[" . $siteList[$i]["ID"]. "]\" value=\"Y\"" . ($event_sale_order_paid == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_order_delivery") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"event_sale_order_delivery[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($event_sale_order_delivery == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_order_cancel") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"event_sale_order_cancel[" . $siteList[$i]["ID"] . "]\" value=\"Y\""  . ($event_sale_order_cancel == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>
				<tr style=\"text-align:center\"><td colspan=\"2\"><b>" . GetMessage('SMS4B_TAB_TITLE_STATUS_CHANGE') . "</b></td></tr>
				";
						foreach ($arSaleStatus['sale'] as $status)
						{
						$this->content .= "<tr>
							<td align=\"right\">" . $status['NAME'] . "</td>
							<td><input type=\"checkbox\" name=\"" .key($status). "[" . $siteList[$i]["ID"]. "]\" value=\"Y\"" . ($status[key($status)] == 'Y'? " checked = \"checked\" " : "" ). "/></td>
						</tr>";
						}
		 }
		$this->content .= "	</table>
			</div>";
		}
	}
}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();

		$changeSite = $wizard->GetVar('SITE_dif_settings');

		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="asc", Array());
		while($arRes = $rsSites->GetNext())
		{
			$siteList[] = Array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);
		}
		$siteCount = count($siteList);

			if (CModule::IncludeModule("sale"))
			{
				$arStatus = CSaleStatus::GetList(
					array("ID"=> "ASC"),
					array("LID" => "ru"),
					false,
					false,
					array("NAME", "ID")
					);
				$arSaleStatus = "";
				$arAdminStatus = "";
				while ($status = $arStatus->GetNext() )
				{
					$arSaleStatus['sale'][] = array(
							"event_sale_status_".$status['ID'] => COption::GetOptionString('rarus.sms4b', "event_sale_status_".$status['ID'], '', $siteList[$i]["ID"]),
							"NAME" => $status['NAME'],
						);
				}
				foreach ($arSaleStatus['sale'] as $status)
				{
					COption::RemoveOption("rarus.sms4b", key($status));
				}
			}
			COption::RemoveOption("rarus.sms4b", "event_subscribe_confirm");
			COption::RemoveOption("rarus.sms4b", "event_ticket_new_for_techsupport");
			COption::RemoveOption("rarus.sms4b", "event_sale_new_order");
			COption::RemoveOption("rarus.sms4b", "event_sale_order_paid");
			COption::RemoveOption("rarus.sms4b", "event_sale_order_delivery");
			COption::RemoveOption("rarus.sms4b", "event_sale_order_cancel");

		if (!empty($changeSite))
			{
				for ($i = 0; $i < $siteCount; $i++)
				{
					if (IsModuleInstalled("subscribe"))
					{
						COption::SetOptionString("rarus.sms4b", "event_subscribe_confirm", trim($_REQUEST["event_subscribe_confirm"][$siteList[$i]["ID"]]), GetMessage('opt_subscribe_confirm'), $siteList[$i]["ID"]);
					}
					if (IsModuleInstalled("sale"))
					{
						COption::SetOptionString("rarus.sms4b", "event_sale_new_order", trim($_REQUEST["event_sale_new_order"][$siteList[$i]["ID"]]), GetMessage('opt_new_order'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "event_sale_order_paid", trim($_REQUEST["event_sale_order_paid"][$siteList[$i]["ID"]]), GetMessage('opt_order_paid'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "event_sale_order_cancel", trim($_REQUEST["event_sale_order_cancel"][$siteList[$i]["ID"]]), GetMessage('opt_order_cancel'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "event_sale_order_delivery", trim($_REQUEST["event_sale_order_delivery"][$siteList[$i]["ID"]]), GetMessage('opt_order_delivery'), $siteList[$i]["ID"]);

						//статусы
						foreach ($arSaleStatus['sale'] as $option)
						{
							COption::SetOptionString("rarus.sms4b", key($option), trim($_REQUEST[key($option)][$siteList[$i]["ID"]]), $option['NAME'], $siteList[$i]["ID"]);
						}
					}
					if (IsModuleInstalled("support"))
					{
						COption::SetOptionString("rarus.sms4b", "event_ticket_new_for_techsupport", trim($_REQUEST["event_ticket_new_for_techsupport"][$siteList[$i]["ID"]]), GetMessage('opt_ticket_new_for_techsupport'), $siteList[$i]["ID"]);
					}
				}
				//COption::SetOptionString("rarus.sms4b", "SITE_different_set", "Y", GetMessage('SITE_different_set'));
			}
			else
			{
				$site_id = trim($_REQUEST["SITE_current_site"]);
				if (IsModuleInstalled("subscribe"))
				{
					COption::SetOptionString("rarus.sms4b", "event_subscribe_confirm", trim($_REQUEST["event_subscribe_confirm"][$site_id]), GetMessage('opt_subscribe_confirm'));
				}
				if (IsModuleInstalled("sale"))
				{
					COption::SetOptionString("rarus.sms4b", "event_sale_new_order", trim($_REQUEST["event_sale_new_order"][$site_id]), GetMessage('opt_new_order'));
					COption::SetOptionString("rarus.sms4b", "event_sale_order_paid", trim($_REQUEST["event_sale_order_paid"][$site_id]), GetMessage('opt_order_paid'));
					COption::SetOptionString("rarus.sms4b", "event_sale_order_cancel", trim($_REQUEST["event_sale_order_cancel"][$site_id]), GetMessage('opt_order_cancel'));
					COption::SetOptionString("rarus.sms4b", "event_sale_order_delivery", trim($_REQUEST["event_sale_order_delivery"][$site_id]), GetMessage('opt_order_delivery'));
					foreach ($arSaleStatus['sale'] as $option)
					{
						COption::SetOptionString("rarus.sms4b", key($option), trim($_REQUEST[key($option)][$site_id]), $option['NAME']);
					}
				}
				if (IsModuleInstalled("support"))
				{
					COption::SetOptionString("rarus.sms4b", "event_ticket_new_for_techsupport", trim($_REQUEST["event_ticket_new_for_techsupport"][$site_id]), GetMessage('opt_ticket_new_for_techsupport'));
				}
				//COption::SetOptionString("rarus.sms4b", "SITE_different_set", "N", GetMessage('SITE_different_set'));
			}
	}
}

// step 4 setting options for module events
class Step4 extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WW_STEP4_TITLE"));
		$this->SetStepID("step4");
		$this->SetPrevStep("step3");
		if (IsModuleInstalled("tasks"))
		{
			$this->SetNextStep("step5");
		}
		else
		{
			$this->SetNextStep("finish");
		}
		$this->SetCancelStep("cancel");
	}

	function ShowStep()
	{
		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="asc", Array());
		while($arRes = $rsSites->GetNext())
		{
			$siteList[] = Array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);
		}
		$siteCount = count($siteList);
		CModule::IncludeModule("rarus.sms4b");
		global $SMS4B;
		$arrDefSender = $SMS4B->GetSender();

		$wizard = &$this->GetWizard();
		$changeSite = $wizard->GetVar('SITE_dif_settings');

		foreach($arrDefSender as $val)
		{
			$arrDF[addslashes(htmlspecialchars_decode($val))] = addslashes(htmlspecialchars_decode($val));
		}

		if (!empty($changeSite))
		{
		$this->content.= "<script language=\"javascript\">
var cur_site = \"" . CUtil::JSEscape($siteList[0]["ID"]). "\";

function changeSiteList(value, add_id)
{
	var SLHandler = document.getElementById(add_id + '_site_id');
	SLHandler.disabled = value;
}";
$this->content .= "
function selectSite(current, add_id)
{
	if (current == cur_site) return;
	var last_handler = document.getElementById('par_' + add_id + '_' + cur_site);
	var current_handler = document.getElementById('par_' + add_id + '_' + current);
	var CSHandler = document.getElementById(add_id + '_current_site');

	last_handler.style.display = 'none';
	current_handler.style.display = 'inline';

	cur_site = current;
	CSHandler.value = current;

	return;
}
</script>
	<table>
	<tr>
	<td valign=\"top\" width=\"50%\" align=\"right\">" . GetMessage("SMO_DIF_SETTINGS") . " </td>
	<td valign=\"top\" width=\"50%\"><input type=\"checkbox\" name=\"SITE_dif_settings\" id=\"SITE_dif_settings\"";

	if (COption::GetOptionString("rarus.sms4b", "SITE_different_set", "N") == "Y")
	$this->content .= " checked=\"checked\" disabled";

	$this->content .=  " OnClick=\"changeSiteList(!this.checked, 'SITE')\" /></td>
	</tr>
		<tr>
		<td valign=\"top\" align=\"right\">" . GetMessage("SMO_SITE_LIST") . "</td>
		<td valign=\"top\"><select name=\"site\" id=\"SITE_site_id\"";
		if (COption::GetOptionString("rarus.sms4b", "SITE_different_set", "N") != "Y")
			$this->content .= " disabled=\"disabled\"";

		 $this->content .= "  onChange=\"selectSite(this.value, 'SITE')\"> ";

				for($i = 0; $i < $siteCount; $i++)
					$this->content .= "<option value=\"".($siteList[$i]["ID"])."\">".($siteList[$i]["NAME"])."</option>";

		 $this->content .=	"</select>
	</tr>
	</table>";
	}
	$this->content .= "<input type=\"hidden\" name=\"SITE_current_site\" id=\"SITE_current_site\" value=\"".($siteList[0]["ID"]) ."\" /></td>";


	if (count($arrDefSender) == 0 || !isset($arrDefSender))
	{
		$this->content .= "<p><span style='color:red'>".GetMessage('WW_STEP3_1_ERROR_TEXT1')."</span></p>";
		$this->content .= "<p><span style='color:green'>".GetMessage('WW_STEP3_1_NOTE_TEXT1')."</span></p>";
	}
	else
	{
		for ($i = 0; $i < $siteCount; $i++)
		{

			if (CModule::IncludeModule("subscribe"))
			{
				$admin_event_subscribe_confirm = COption::GetOptionString('rarus.sms4b', 'admin_event_subscribe_confirm', '', $siteList[$i]["ID"]);
			}

			$admin_phone = COption::GetOptionString('rarus.sms4b', 'admin_phone', '', $siteList[$i]["ID"]);

			if (CModule::IncludeModule("sale"))
			{
				$admin_event_sale_new_order = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_new_order', '', $siteList[$i]["ID"]);
				$admin_event_sale_order_paid = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_order_paid', '', $siteList[$i]["ID"]);
				$admin_event_sale_order_delivery = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_order_delivery', '', $siteList[$i]["ID"]);
				$admin_event_sale_order_cancel = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_order_cancel', '', $siteList[$i]["ID"]);

				$arStatus = CSaleStatus::GetList(
					array("ID"=> "ASC"),
					array("LID" => "ru"),
					false,
					false,
					array("NAME", "ID")
					);
					$arSaleStatus = "";
					$arAdminStatus = "";
					while ($status = $arStatus->GetNext() )
					{
						$arAdminStatus['sale'][] = array(
							"admin_event_sale_status_".$status['ID'] => COption::GetOptionString('rarus.sms4b', "admin_event_sale_status_".$status['ID'], '', $siteList[$i]["ID"]),
							"NAME" => $status['NAME'],
						);
					}
			}
			if (IsModuleInstalled("support"))
			{
				$admin_event_ticket_new_for_techsupport = COption::GetOptionString('rarus.sms4b', 'admin_event_ticket_new_for_techsupport', '', $siteList[$i]["ID"]);
			}
		$this->content .= "<div  id=\"par_SITE_" . $siteList[$i]["ID"] . "\" style=\"display:" . ($i == 0 ? "inline" : "none") ."\">
			<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" width=\"100%\" align=\"center\">
			<tr style=\"text-align:center\"><td colspan=\"2\"><b>". GetMessage("SMS4B_TAB_SEND_EVENTS"). "</b></td></tr>
			<tr>
				<td valign=\"top\" align=\"right\">" . GetMessage("SMS4B_ADMIN_PHONE") . "</td>
				<td valign=\"top\"><textarea name=\"admin_phone[" . $siteList[$i]["ID"] ."]\" cols=\"20\" rows=\"3\">" . $admin_phone . "</textarea></td>
			</tr>

			<tr>
				<td valign=\"top\" align=\"right\" width=\"50%\">" .  GetMessage('opt_subscribe_confirm') ."</td>
				<td valign=\"top\"><input type=\"checkbox\" name=\"admin_event_subscribe_confirm[" . $siteList[$i]["ID"]. "]\" value=\"Y\"" . ($admin_event_subscribe_confirm == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
			</tr>";
			if (IsModuleInstalled("support"))
			{
			$this->content .= "<tr>
				<td valign=\"top\" align=\"right\">" .  GetMessage("opt_ticket_new_for_techsupport") . "</td>
				<td valign=\"top\"><input type=\"checkbox\" name=\"admin_event_ticket_new_for_techsupport[" . $siteList[$i]["ID"]. "]\" value=\"Y\"" . ($admin_event_ticket_new_for_techsupport == 'Y'? " checked = \"checked\" " : "" ). "/></td>
			</tr>";
			}

			if (IsModuleInstalled("sale"))
			{
			$this->content .=	"<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_new_order") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"admin_event_sale_new_order[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($admin_event_sale_new_order == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_order_paid"). "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"admin_event_sale_order_paid[" . $siteList[$i]["ID"]. "]\" value=\"Y\"" . ($admin_event_sale_order_paid == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_order_delivery") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"admin_event_sale_order_delivery[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($admin_event_sale_order_delivery == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_order_cancel") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"admin_event_sale_order_cancel[" . $siteList[$i]["ID"] . "]\" value=\"Y\""  . ($admin_event_sale_order_cancel == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>
				<tr style=\"text-align:center\"><td colspan=\"2\"><b>" . GetMessage('SMS4B_TAB_TITLE_STATUS_CHANGE') . "</b></td></tr>
				";
						foreach ($arAdminStatus['sale'] as $status)
						{
						$this->content .= "<tr>
							<td align=\"right\">" . $status['NAME'] . "</td>
							<td><input type=\"checkbox\" name=\"" .key($status). "[" . $siteList[$i]["ID"]. "]\" value=\"Y\"" . ($status[key($status)] == 'Y'? " checked = \"checked\" " : "" ). "/></td>
						</tr>";
						}
		 }
		$this->content .= "	</table>
			</div>";
		}
	}
}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$changeSite = $wizard->GetVar('SITE_dif_settings');
		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="asc", Array());
		while($arRes = $rsSites->GetNext())
		{
			$siteList[] = Array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);
		}
		$siteCount = count($siteList);

			if (CModule::IncludeModule("sale"))
			{
				$arStatus = CSaleStatus::GetList(
					array("ID"=> "ASC"),
					array("LID" => "ru"),
					false,
					false,
					array("NAME", "ID")
					);
				$arSaleStatus = "";
				$arAdminStatus = "";
				while ($status = $arStatus->GetNext() )
				{
					$arAdminStatus['sale'][] = array(
						"admin_event_sale_status_".$status['ID'] => COption::GetOptionString('rarus.sms4b', "admin_event_sale_status_".$status['ID'], '', $siteList[$i]["ID"]),
						"NAME" => $status['NAME'],
					);
				}
				foreach ($arAdminStatus['sale'] as $status)
				{
					COption::RemoveOption("rarus.sms4b", key($status));
				}
			}
			COption::RemoveOption("rarus.sms4b", "admin_event_subscribe_confirm");
			COption::RemoveOption("rarus.sms4b", "admin_event_ticket_new_for_techsupport");
			COption::RemoveOption("rarus.sms4b", "admin_event_sale_new_order");
			COption::RemoveOption("rarus.sms4b", "admin_event_sale_order_paid");
			COption::RemoveOption("rarus.sms4b", "admin_event_sale_order_delivery");
			COption::RemoveOption("rarus.sms4b", "admin_event_sale_order_cancel");
			COption::RemoveOption("rarus.sms4b", "admin_phone");

		if (!empty($changeSite))
			{
				for ($i = 0; $i < $siteCount; $i++)
				{
					if (IsModuleInstalled("subscribe"))
					{
						COption::SetOptionString("rarus.sms4b", "admin_event_subscribe_confirm", trim($_REQUEST["admin_event_subscribe_confirm"][$siteList[$i]["ID"]]), GetMessage('opt_subscribe_confirm'), $siteList[$i]["ID"]);
					}

					COption::SetOptionString("rarus.sms4b", "admin_phone", trim($_REQUEST["admin_phone"][$siteList[$i]["ID"]]), GetMessage('SMS4B_ADMIN_PHONE'), $siteList[$i]["ID"]);

					if (IsModuleInstalled("sale"))
					{
						COption::SetOptionString("rarus.sms4b", "admin_event_sale_new_order", trim($_REQUEST["admin_event_sale_new_order"][$siteList[$i]["ID"]]), GetMessage('opt_new_order'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "admin_event_sale_order_paid", trim($_REQUEST["admin_event_sale_order_paid"][$siteList[$i]["ID"]]), GetMessage('opt_order_paid'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "admin_event_sale_order_cancel", trim($_REQUEST["admin_event_sale_order_cancel"][$siteList[$i]["ID"]]), GetMessage('opt_order_cancel'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "admin_event_sale_order_delivery", trim($_REQUEST["admin_event_sale_order_delivery"][$siteList[$i]["ID"]]), GetMessage('opt_order_delivery'), $siteList[$i]["ID"]);

						//статусы
						foreach ($arAdminStatus['sale'] as $option)
						{
							COption::SetOptionString("rarus.sms4b", key($option), trim($_REQUEST[key($option)][$siteList[$i]["ID"]]), $option['NAME'], $siteList[$i]["ID"]);
						}
					}
					if (IsModuleInstalled("support"))
					{
						COption::SetOptionString("rarus.sms4b", "admin_event_ticket_new_for_techsupport", trim($_REQUEST["admin_event_ticket_new_for_techsupport"][$siteList[$i]["ID"]]), GetMessage('opt_ticket_new_for_techsupport'), $siteList[$i]["ID"]);
					}
				}
				//COption::SetOptionString("rarus.sms4b", "SITE_different_set", "Y", GetMessage('SITE_different_set'));
			}
			else
			{
				$site_id = trim($_REQUEST["SITE_current_site"]);
				if (IsModuleInstalled("subscribe"))
				{
					COption::SetOptionString("rarus.sms4b", "admin_event_subscribe_confirm", trim($_REQUEST["admin_event_subscribe_confirm"][$site_id]), GetMessage('opt_subscribe_confirm'));
				}

				COption::SetOptionString("rarus.sms4b", "admin_phone", trim($_REQUEST["admin_phone"][$site_id]), GetMessage('SMS4B_ADMIN_PHONE'));

				if (IsModuleInstalled("sale"))
				{
					COption::SetOptionString("rarus.sms4b", "admin_event_sale_new_order", trim($_REQUEST["admin_event_sale_new_order"][$site_id]), GetMessage('opt_new_order'));
					COption::SetOptionString("rarus.sms4b", "admin_event_sale_order_paid", trim($_REQUEST["admin_event_sale_order_paid"][$site_id]), GetMessage('opt_order_paid'));
					COption::SetOptionString("rarus.sms4b", "admin_event_sale_order_cancel", trim($_REQUEST["admin_event_sale_order_cancel"][$site_id]), GetMessage('opt_order_cancel'));
					COption::SetOptionString("rarus.sms4b", "admin_event_sale_order_delivery", trim($_REQUEST["admin_event_sale_order_delivery"][$site_id]), GetMessage('opt_order_delivery'));
					foreach ($arAdminStatus['sale'] as $option)
					{
						COption::SetOptionString("rarus.sms4b", key($option), trim($_REQUEST[key($option)][$site_id]), $option['NAME']);
					}
				}
				if (IsModuleInstalled("support"))
				{
					COption::SetOptionString("rarus.sms4b", "admin_event_ticket_new_for_techsupport", trim($_REQUEST["admin_event_ticket_new_for_techsupport"][$site_id]), GetMessage('opt_ticket_new_for_techsupport'));
				}
				//COption::SetOptionString("rarus.sms4b", "SITE_different_set", "N", GetMessage('SITE_different_set'));
			}
	}
}


// step 5 setting options for module tasks
class Step5 extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WW_STEP5_TITLE"));
		$this->SetStepID("step5");
		$this->SetPrevStep("step4");
		$this->SetNextStep("finish");
		$this->SetCancelStep("cancel");
	}

	function ShowStep()
	{
		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="asc", Array());
		while($arRes = $rsSites->GetNext())
		{
			$siteList[] = Array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);
		}
		$siteCount = count($siteList);
		CModule::IncludeModule("rarus.sms4b");
		global $SMS4B;
		$arrDefSender = $SMS4B->GetSender();

		$wizard = &$this->GetWizard();
		$changeSite = $wizard->GetVar('SITE_dif_settings');

		foreach($arrDefSender as $val)
		{
			$arrDF[addslashes(htmlspecialchars_decode($val))] = addslashes(htmlspecialchars_decode($val));
		}

		if (!empty($changeSite))
		{
		$this->content.= "<script language=\"javascript\">
var cur_site = \"" . CUtil::JSEscape($siteList[0]["ID"]). "\";

function changeSiteList(value, add_id)
{
	var SLHandler = document.getElementById(add_id + '_site_id');
	SLHandler.disabled = value;
}";
$this->content .= "
function selectSite(current, add_id)
{
	if (current == cur_site) return;
	var last_handler = document.getElementById('par_' + add_id + '_' + cur_site);
	var current_handler = document.getElementById('par_' + add_id + '_' + current);
	var CSHandler = document.getElementById(add_id + '_current_site');

	last_handler.style.display = 'none';
	current_handler.style.display = 'inline';

	cur_site = current;
	CSHandler.value = current;

	return;
}
</script>
	<table>
	<tr>
	<td valign=\"top\" width=\"50%\" align=\"right\">" . GetMessage("SMO_DIF_SETTINGS") . " </td>
	<td valign=\"top\" width=\"50%\"><input type=\"checkbox\" name=\"SITE_dif_settings\" id=\"SITE_dif_settings\"";

	if (COption::GetOptionString("rarus.sms4b", "SITE_different_set", "N") == "Y")
	$this->content .= " checked=\"checked\" disabled";

	$this->content .=  " OnClick=\"changeSiteList(!this.checked, 'SITE')\" /></td>
	</tr>
		<tr>
		<td valign=\"top\" align=\"right\">" . GetMessage("SMO_SITE_LIST") . "</td>
		<td valign=\"top\"><select name=\"site\" id=\"SITE_site_id\"";
		if (COption::GetOptionString("rarus.sms4b", "SITE_different_set", "N") != "Y")
			$this->content .= " disabled=\"disabled\"";

		 $this->content .= "  onChange=\"selectSite(this.value, 'SITE')\"> ";

				for($i = 0; $i < $siteCount; $i++)
					$this->content .= "<option value=\"".($siteList[$i]["ID"])."\">".($siteList[$i]["NAME"])."</option>";

		 $this->content .=	"</select>
	</tr>
	</table>";
	}

	$this->content .= "<input type=\"hidden\" name=\"SITE_current_site\" id=\"SITE_current_site\" value=\"".($siteList[0]["ID"]) ."\" /></td>";

	if (count($arrDefSender) == 0 || !isset($arrDefSender))
	{
		$this->content .= "<p><span style='color:red'>".GetMessage('WW_STEP3_1_ERROR_TEXT1')."</span></p>";
		$this->content .= "<p><span style='color:green'>".GetMessage('WW_STEP3_1_NOTE_TEXT1')."</span></p>";
	}
	else
	{
		for ($i = 0; $i < $siteCount; $i++)
		{
			if (IsModuleInstalled("tasks"))
			{
				$add_low_task = COption::GetOptionString('rarus.sms4b', 'add_low_task', '', $siteList[$i]["ID"]);
				$add_middle_task = COption::GetOptionString('rarus.sms4b', 'add_middle_task', '', $siteList[$i]["ID"]);
				$add_hight_task = COption::GetOptionString('rarus.sms4b', 'add_hight_task', '', $siteList[$i]["ID"]);
				$update_low_task = COption::GetOptionString('rarus.sms4b', 'update_low_task', '', $siteList[$i]["ID"]);
				$update_middle_task = COption::GetOptionString('rarus.sms4b', 'update_middle_task', '', $siteList[$i]["ID"]);
				$update_hight_task = COption::GetOptionString('rarus.sms4b', 'update_hight_task', '', $siteList[$i]["ID"]);
				$delete_low_task = COption::GetOptionString('rarus.sms4b', 'delete_low_task', '', $siteList[$i]["ID"]);
				$delete_middle_task = COption::GetOptionString('rarus.sms4b', 'delete_middle_task', '', $siteList[$i]["ID"]);
				$delete_hight_task = COption::GetOptionString('rarus.sms4b', 'delete_hight_task', '', $siteList[$i]["ID"]);
			}

			$admin_phone = COption::GetOptionString('rarus.sms4b', 'admin_phone', '', $siteList[$i]["ID"]);

			if (CModule::IncludeModule("sale"))
			{
				$admin_event_sale_new_order = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_new_order', '', $siteList[$i]["ID"]);
				$admin_event_sale_order_paid = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_order_paid', '', $siteList[$i]["ID"]);
				$admin_event_sale_order_delivery = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_order_delivery', '', $siteList[$i]["ID"]);
				$admin_event_sale_order_cancel = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_order_cancel', '', $siteList[$i]["ID"]);

				$arStatus = CSaleStatus::GetList(
					array("ID"=> "ASC"),
					array("LID" => "ru"),
					false,
					false,
					array("NAME", "ID")
					);
					$arSaleStatus = "";
					$arAdminStatus = "";
					while ($status = $arStatus->GetNext() )
					{
						$arAdminStatus['sale'][] = array(
							"admin_event_sale_status_".$status['ID'] => COption::GetOptionString('rarus.sms4b', "admin_event_sale_status_".$status['ID'], '', $siteList[$i]["ID"]),
							"NAME" => $status['NAME'],
						);
					}
			}
			if (IsModuleInstalled("support"))
			{
				$admin_event_ticket_new_for_techsupport = COption::GetOptionString('rarus.sms4b', 'admin_event_ticket_new_for_techsupport', '', $siteList[$i]["ID"]);
			}
		$this->content .= "<div  id=\"par_SITE_" . $siteList[$i]["ID"] . "\" style=\"display:" . ($i == 0 ? "inline" : "none") ."\">
			<table cellpadding=\"2\" cellspacing=\"2\" border=\"0\" width=\"100%\" align=\"center\">";

			if (IsModuleInstalled("tasks"))
			{
				$this->content .= "<tr class=\"heading\"><td align=\"center\" colspan=\"2\"><strong>" . GetMessage('SMS4B_HEADER_TASKS') . "</strong></td></tr>
			<tr><td align=\"center\" colspan=\"2\">
			<table width=\"240px\">
				<tr class=\"heading\"><td colspan=\"2\" align=\"center\"><i>" . GetMessage('SMS4B_TAB_TITLE_ADD_TASK') . "<i></td></tr>
				<tr>
					<td valign=\"top\" align=\"left\">" . GetMessage("SMS4B_LOW_TASK") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"add_low_task[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($add_low_task == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"left\">" . GetMessage("SMS4B_MIDDLE_TASK") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"add_middle_task[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($add_middle_task == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"left\">" . GetMessage("SMS4B_HIGHT_TASK") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"add_hight_task[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($add_hight_task == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>

				<tr class=\"heading\"><td colspan=\"2\" align=\"center\"><i>" . GetMessage('SMS4B_TAB_TITLE_UPDATE_TASK') . "</i></td></tr>
				<tr>
					<td valign=\"top\" align=\"left\">" . GetMessage("SMS4B_LOW_TASK") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"update_low_task[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($update_low_task == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"left\">" . GetMessage("SMS4B_MIDDLE_TASK") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"update_middle_task[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($update_middle_task == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"left\">" . GetMessage("SMS4B_HIGHT_TASK") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"update_hight_task[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($update_hight_task == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>

				<tr class=\"heading\"><td colspan=\"2\" align=\"center\"><i>" . GetMessage('SMS4B_TAB_TITLE_DELETE_TASK') . "</i></td></tr>
				<tr>
					<td valign=\"top\" align=\"left\">" . GetMessage("SMS4B_LOW_TASK") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"delete_low_task[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($delete_low_task == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"left\">" . GetMessage("SMS4B_MIDDLE_TASK") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"delete_middle_task[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($delete_middle_task == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"left\">" . GetMessage("SMS4B_HIGHT_TASK") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"delete_hight_task[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($delete_hight_task == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>
			</table>
		</td></tr>";
			}


			if (IsModuleInstalled("sale"))
			{
			$this->content .=	"<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_new_order") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"admin_event_sale_new_order[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($admin_event_sale_new_order == 'Y'? " checked = \"checked\" " : "" ) . "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_order_paid"). "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"admin_event_sale_order_paid[" . $siteList[$i]["ID"]. "]\" value=\"Y\"" . ($admin_event_sale_order_paid == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_order_delivery") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"admin_event_sale_order_delivery[" . $siteList[$i]["ID"] . "]\" value=\"Y\"" . ($admin_event_sale_order_delivery == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>
				<tr>
					<td valign=\"top\" align=\"right\">" . GetMessage("opt_order_cancel") . "</td>
					<td valign=\"top\"><input type=\"checkbox\" name=\"admin_event_sale_order_cancel[" . $siteList[$i]["ID"] . "]\" value=\"Y\""  . ($admin_event_sale_order_cancel == 'Y'? " checked = \"checked\" " : "" ). "/></td>
				</tr>
				<tr style=\"text-align:center\"><td colspan=\"2\"><b>" . GetMessage('SMS4B_TAB_TITLE_STATUS_CHANGE') . "</b></td></tr>
				";
						foreach ($arAdminStatus['sale'] as $status)
						{
						$this->content .= "<tr>
							<td align=\"right\">" . $status['NAME'] . "</td>
							<td><input type=\"checkbox\" name=\"" .key($status). "[" . $siteList[$i]["ID"]. "]\" value=\"Y\"" . ($status[key($status)] == 'Y'? " checked = \"checked\" " : "" ). "/></td>
						</tr>";
						}
		 }
		$this->content .= "	</table>
			</div>";
		}
	}
}

	function OnPostForm()
	{
		$wizard = &$this->GetWizard();
		$changeSite = $wizard->GetVar('SITE_dif_settings');
		$siteList = array();
		$rsSites = CSite::GetList($by="sort", $order="asc", Array());
		while($arRes = $rsSites->GetNext())
		{
			$siteList[] = Array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);
		}
		$siteCount = count($siteList);

		COption::RemoveOption("rarus.sms4b", "add_low_task");
		COption::RemoveOption("rarus.sms4b", "add_middle_task");
		COption::RemoveOption("rarus.sms4b", "add_hight_task");
		COption::RemoveOption("rarus.sms4b", "update_low_tast");
		COption::RemoveOption("rarus.sms4b", "update_middle_task");
		COption::RemoveOption("rarus.sms4b", "update_hight_task");
		COption::RemoveOption("rarus.sms4b", "delete_low_task");
		COption::RemoveOption("rarus.sms4b", "delete_middle_task");
		COption::RemoveOption("rarus.sms4b", "delete_hight_task");

		if (!empty($changeSite))
			{
				for ($i = 0; $i < $siteCount; $i++)
				{
					if (IsModuleInstalled("tasks"))
					{
						COption::SetOptionString("rarus.sms4b", "add_low_task", trim($_REQUEST["add_low_task"][$siteList[$i]["ID"]]), GetMessage('add_low_task'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "add_middle_task", trim($_REQUEST["add_middle_task"][$siteList[$i]["ID"]]), GetMessage('add_middle_task'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "add_hight_task", trim($_REQUEST["add_hight_task"][$siteList[$i]["ID"]]), GetMessage('add_hight_task'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "update_low_tast", trim($_REQUEST["update_low_tast"][$siteList[$i]["ID"]]), GetMessage('update_low_tast'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "update_middle_task", trim($_REQUEST["update_middle_task"][$siteList[$i]["ID"]]), GetMessage('update_middle_task'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "update_hight_task", trim($_REQUEST["update_hight_task"][$siteList[$i]["ID"]]), GetMessage('update_hight_task'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "delete_low_task", trim($_REQUEST["delete_low_task"][$siteList[$i]["ID"]]), GetMessage('delete_low_task'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "delete_middle_task", trim($_REQUEST["delete_middle_task"][$siteList[$i]["ID"]]), GetMessage('delete_middle_task'), $siteList[$i]["ID"]);
						COption::SetOptionString("rarus.sms4b", "delete_hight_task", trim($_REQUEST["delete_hight_task"][$siteList[$i]["ID"]]), GetMessage('delete_hight_task'), $siteList[$i]["ID"]);

					}
				}
				//COption::SetOptionString("rarus.sms4b", "SITE_different_set", "Y", GetMessage('SITE_different_set'));
			}
			else
			{
				$site_id = trim($_REQUEST["SITE_current_site"]);
				if (IsModuleInstalled("tasks"))
				{
					COption::SetOptionString("rarus.sms4b", "add_low_task", trim($_REQUEST["add_low_task"][$site_id]), GetMessage('add_low_task'));
					COption::SetOptionString("rarus.sms4b", "add_middle_task", trim($_REQUEST["add_middle_task"][$site_id]), GetMessage('add_middle_task'));
					COption::SetOptionString("rarus.sms4b", "add_hight_task", trim($_REQUEST["add_hight_task"][$site_id]), GetMessage('add_hight_task'));
					COption::SetOptionString("rarus.sms4b", "update_low_tast", trim($_REQUEST["update_low_tast"][$site_id]), GetMessage('update_low_tast'));
					COption::SetOptionString("rarus.sms4b", "update_middle_task", trim($_REQUEST["update_middle_task"][$site_id]), GetMessage('update_middle_task'));
					COption::SetOptionString("rarus.sms4b", "update_hight_task", trim($_REQUEST["update_hight_task"][$site_id]), GetMessage('update_hight_task'));
					COption::SetOptionString("rarus.sms4b", "delete_low_task", trim($_REQUEST["delete_low_task"][$site_id]), GetMessage('delete_low_task'));
					COption::SetOptionString("rarus.sms4b", "delete_middle_task", trim($_REQUEST["delete_middle_task"][$site_id]), GetMessage('delete_middle_task'));
					COption::SetOptionString("rarus.sms4b", "delete_hight_task", trim($_REQUEST["delete_hight_task"][$site_id]), GetMessage('delete_hight_task'));
				}
				//COption::SetOptionString("rarus.sms4b", "SITE_different_set", "N", GetMessage('SITE_different_set'));
			}
	}
}


class Finish extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WW_CANCEL_TITLE"));
		$this->SetStepID("finish");
		$this->SetCancelStep("cancel");
		$this->SetCancelCaption(GetMessage("WW_CLOSE"));
	}

	function ShowStep()
	{
		$this->content .= GetMessage("WW_FINISH_DESCR");
	}
}

class CancelStep extends CWizardStep
{
	function InitStep()
	{
		$this->SetTitle(GetMessage("WW_CANCEL_TITLE"));
		$this->SetStepID("cancel");
		$this->SetCancelStep("cancel");
		$this->SetCancelCaption(GetMessage("WW_CLOSE"));
	}

	function ShowStep()
	{
		$this->content .= GetMessage("WW_CANCEL_DESCR");
	}
}
?>
