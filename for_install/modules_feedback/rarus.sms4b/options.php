<?
$module_id = "rarus.sms4b";
$APPLICATION->AddHeadScript('/bitrix/js/'.$module_id.'/jquery.js');
$APPLICATION->AddHeadScript('/bitrix/js/'.$module_id.'/jquery.dataTables.js');
$APPLICATION->SetAdditionalCSS('/bitrix/js/'.$module_id.'/css/styles.css');

$siteList = array();
$rsSites = CSite::GetList($by="sort", $order="asc", Array());
while($arRes = $rsSites->GetNext())
{
	$siteList[] = Array("ID" => $arRes["ID"], "NAME" => $arRes["NAME"]);
}
$siteCount = count($siteList);

	$module_id = "rarus.sms4b";
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
	IncludeModuleLangFile(__FILE__);

	$SMS_RIGHT = $APPLICATION->GetGroupRight($module_id);

	if ($SMS_RIGHT >="R"):
	$gmt = array(	4 => "(+4) ".GetMessage('MOSCOW'),
				3 => "(+3) ".GetMessage('KALININGRAD'),
				6 => "(+6) ".GetMessage('EKATA'),
				7 => "(+7) ".GetMessage('OMSK'),
				8 => "(+8) ".GetMessage('KEMEROVO'),
				9 => "(+9) ".GetMessage('IRKYTSK'),
				10 => "(+10) ".GetMessage('CHITA'),
				11 => "(+11) ".GetMessage('VLADIVOSTOK'),
				12 => "(+12) ".GetMessage('MAGA'),
	);

	CModule::IncludeModule("rarus.sms4b");

	global $SMS4B;
	$arrDefSender = $SMS4B->GetSender();
	foreach($arrDefSender as $val)
	{
		$arrDF[addslashes(htmlspecialchars_decode($val))] = addslashes(htmlspecialchars_decode($val));
	}

	$arAllOptions = array(
		array("proxy_use", GetMessage("opt_proxy_use"),"n", array("checkbox", "y")),
		array("proxy_host", GetMessage("opt_proxy_host"), "", array("text", 35)),
		array("proxy_port", GetMessage("opt_proxy_port"), "", array("text", 35)),
		array("login", GetMessage("opt_login"), "", array("text",35)),
		array("password", GetMessage("opt_password"), "", array("text",35)),
		array("gmt", GetMessage("opt_gmt"), 3, array("selectbox", $gmt)),
		array("send_email", GetMessage("send_email"), 3, array("checkbox", "y")),

	);

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
			if(!in_array($props['CODE'], $codesSimple))
			{ 
				$orderProps[] = $props;
				$codesSimple[] = $props['CODE'];
			}
			$codes[$props["PERSON_TYPE_ID"]][] = $props['CODE'];
		}
		
		//first elenemt intersects with all others
		$commonCodes = array_shift ($codes);
		foreach ($codes as $arCode)
		{
			$commonCodes = array_intersect($commonCodes, $arCode);
		}
		
		$db_ptype = CSalePersonType::GetList(Array("SORT" => "ASC"));
		while ($ptype = $db_ptype->Fetch())
		{
			$person[$ptype['ID']] =  $ptype['NAME'];
		}
	}

	$aTabs = array();
	$aTabs[] = array("DIV" => "edit0", "TAB" => GetMessage("SMS4B_TAB_PARAM"), "ICON" => "sms4b_settings", "TITLE" => GetMessage("SMS4B_TAB_TITLE_PARAM"));
	$aTabs[] = array("DIV" => "edit1", "TAB" => GetMessage("SMS4B_TAB_SITE"), "ICON" => "sms4b_settings", "TITLE" => GetMessage("SMS4B_TAB_TITLE_SITE"));
	$aTabs[] = array("DIV" => "edit2", "TAB" => GetMessage("SMS4B_TAB_TEMPLATES"), "ICON" => "sms4b_settings", "TITLE" => GetMessage("SMS4B_TAB_TITLE_TEMPLATES"));
	$aTabs[] = array("DIV" => "edit3", "TAB" => GetMessage("SMS4B_TAB_HELP"), "ICON" => "sms4b_settings", "TITLE" => GetMessage("SMS4B_TAB_TITLE_HELP"));
	$aTabs[] = array("DIV" => "edit4", "TAB" => GetMessage("SMS4B_TAB_SUPPORT"), "ICON" => "sms4b_settings", "TITLE" => GetMessage("SMS4B_TAB_TITLE_SUPPORT"));
	$aTabs[] = array("DIV" => "edit5", "TAB" => GetMessage("SMS4B_TAB_RIGHTS"), "ICON" => "sms4b_settings", "TITLE" => GetMessage("SMS4B_TAB_TITLE_RIGHTS"));

	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && $SMS_RIGHT >= "W" && check_bitrix_sessid())
	{
		
		if(strlen($RestoreDefaults)>0)
		{
			COption::RemoveOption("rarus.sms4b");
			$APPLICATION->DelGroupRight("rarus.sms4b");
		}
		else
		{
			COption::RemoveOption("rarus.sms4b");
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
					$arSaleStatus[] = "event_sale_status_".$status['ID'];
					$arAdminStatus[] = "admin_event_sale_status_".$status['ID'];
				}
			}

			for ($i = 0; $i < $siteCount; $i++)
			{
				COption::SetOptionString("rarus.sms4b", "use_translit", trim($_REQUEST["use_translit"][$siteList[$i]["ID"]]), GetMessage('use_translit'), $siteList[$i]["ID"]);
				COption::SetOptionString("rarus.sms4b", "defsender", trim($_REQUEST["defsender"][$siteList[$i]["ID"]]), GetMessage('opt_defsender'), $siteList[$i]["ID"]);
				COption::SetOptionString("rarus.sms4b", "phone_number_code", trim($_REQUEST["phone_number_code"][$siteList[$i]["ID"]]), GetMessage('phone_number_code'), $siteList[$i]["ID"]);
				COption::SetOptionString("rarus.sms4b", "user_property_phone", trim($_REQUEST["user_property_phone"][$siteList[$i]["ID"]]), GetMessage('user_phone'), $siteList[$i]["ID"]);
				
				if (IsModuleInstalled("subscribe"))
				{
					COption::SetOptionString("rarus.sms4b", "event_subscribe_confirm", trim($_REQUEST["event_subscribe_confirm"][$siteList[$i]["ID"]]), GetMessage('opt_subscribe_confirm'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "admin_event_subscribe_confirm", trim($_REQUEST["admin_event_subscribe_confirm"][$siteList[$i]["ID"]]), GetMessage('opt_subscribe_confirm'), $siteList[$i]["ID"]);
				}
					COption::SetOptionString("rarus.sms4b", "admin_phone", trim($_REQUEST["admin_phone"][$siteList[$i]["ID"]]), GetMessage('SMS4B_ADMIN_PHONE'), $siteList[$i]["ID"]);

				if (IsModuleInstalled("sale"))
				{
					COption::SetOptionString("rarus.sms4b", "event_sale_new_order", trim($_REQUEST["event_sale_new_order"][$siteList[$i]["ID"]]), GetMessage('opt_new_order'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "event_sale_order_paid", trim($_REQUEST["event_sale_order_paid"][$siteList[$i]["ID"]]), GetMessage('opt_order_paid'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "event_sale_order_cancel", trim($_REQUEST["event_sale_order_cancel"][$siteList[$i]["ID"]]), GetMessage('opt_order_cancel'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "event_sale_order_delivery", trim($_REQUEST["event_sale_order_delivery"][$siteList[$i]["ID"]]), GetMessage('opt_order_delivery'), $siteList[$i]["ID"]);

					COption::SetOptionString("rarus.sms4b", "admin_event_sale_new_order", trim($_REQUEST["admin_event_sale_new_order"][$siteList[$i]["ID"]]), GetMessage('opt_new_order'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "admin_event_sale_order_paid", trim($_REQUEST["admin_event_sale_order_paid"][$siteList[$i]["ID"]]), GetMessage('opt_order_paid'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "admin_event_sale_order_cancel", trim($_REQUEST["admin_event_sale_order_cancel"][$siteList[$i]["ID"]]), GetMessage('opt_order_cancel'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "admin_event_sale_order_delivery", trim($_REQUEST["admin_event_sale_order_delivery"][$siteList[$i]["ID"]]), GetMessage('opt_order_delivery'), $siteList[$i]["ID"]);
				}
					COption::SetOptionString("rarus.sms4b", "defsenderPublic", trim($_REQUEST["defsenderPublic"][$siteList[$i]["ID"]]), GetMessage('defsenderPublic'), $siteList[$i]["ID"]);

				if (IsModuleInstalled("support"))
				{
					COption::SetOptionString("rarus.sms4b", "admin_event_ticket_new_for_techsupport", trim($_REQUEST["admin_event_ticket_new_for_techsupport"][$siteList[$i]["ID"]]), GetMessage('opt_ticket_new_for_techsupport'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "event_ticket_new_for_techsupport", trim($_REQUEST["event_ticket_new_for_techsupport"][$siteList[$i]["ID"]]), GetMessage('opt_ticket_new_for_techsupport'), $siteList[$i]["ID"]);
				}
				if (IsModuleInstalled("tasks"))
				{
					COption::SetOptionString("rarus.sms4b", "add_low_task", trim($_REQUEST["add_low_task"][$siteList[$i]["ID"]]), GetMessage('add_low_task'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "add_middle_task", trim($_REQUEST["add_middle_task"][$siteList[$i]["ID"]]), GetMessage('add_middle_task'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "add_hight_task", trim($_REQUEST["add_hight_task"][$siteList[$i]["ID"]]), GetMessage('add_hight_task'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "update_low_task", trim($_REQUEST["update_low_task"][$siteList[$i]["ID"]]), GetMessage('update_low_task'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "update_middle_task", trim($_REQUEST["update_middle_task"][$siteList[$i]["ID"]]), GetMessage('update_middle_task'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "update_hight_task", trim($_REQUEST["update_hight_task"][$siteList[$i]["ID"]]), GetMessage('update_hight_task'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "delete_low_task", trim($_REQUEST["delete_low_task"][$siteList[$i]["ID"]]), GetMessage('delete_low_task'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "delete_middle_task", trim($_REQUEST["delete_middle_task"][$siteList[$i]["ID"]]), GetMessage('delete_middle_task'), $siteList[$i]["ID"]);
					COption::SetOptionString("rarus.sms4b", "delete_hight_task", trim($_REQUEST["delete_hight_task"][$siteList[$i]["ID"]]), GetMessage('delete_hight_task'), $siteList[$i]["ID"]);
				}
				foreach ($arSaleStatus as $option)
				{
					COption::SetOptionString("rarus.sms4b", $option, trim($_REQUEST[$option][$siteList[$i]["ID"]]), $option, $siteList[$i]["ID"]);
				}
				foreach ($arAdminStatus as $option)
				{
					COption::SetOptionString("rarus.sms4b", $option, trim($_REQUEST[$option][$siteList[$i]["ID"]]), $option, $siteList[$i]["ID"]);
				}
			}

			foreach($arAllOptions as $arOption)
			{
				$name=$arOption[0];
				$val=$_REQUEST[$name];
				if($arOption[2][0]=="checkbox" && $val!="Y")
					$val="N";
				COption::SetOptionString("rarus.sms4b", $name, $val, $arOption[1]);
			}
		}
	}

	$tabControl->Begin();?>
	<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
	<?
		if($SMS4B->getLogin() <> '' && $SMS4B->getPassword() <> '')
		{
			if(count($arrDefSender) > 0 && $arrDefSender[0] <> '')
			{
				ShowNote(GetMessage('success_connect'));
			}
			else
			{
				ShowError(GetMessage('none_connect'));?>
				<?=GetMessage('registry_information');
			}
		}
		else
			ShowError(GetMessage('no_log_and_pass'));

	$tabControl->BeginNextTab();
	foreach($arAllOptions as $arOption):
		__AdmSettingsDrawRow("rarus.sms4b", $arOption);
	endforeach;

	$tabControl->BeginNextTab();
	?>
	<tr>
		<td colspan="2" valign="top">
<?
$aTabs3 = Array();
foreach($siteList as $val)
{
	$aTabs3[] = Array(
		"DIV"=>"options".$val["ID"],
		"TAB" => "[".$val["ID"]."] ".($val["NAME"]),
		"TITLE" => GetMessage("site_title"). "[".$val["ID"]."] ".($val["NAME"])
	);
}
$tabControl3 = new CAdminViewTabControl("tabControl3", $aTabs3);
$tabControl3->Begin();

for ($i = 0; $i < $siteCount; $i++):

$tabControl3->BeginNextTab();

$defsender = COption::GetOptionString('rarus.sms4b', 'defsender', '', $siteList[$i]["ID"]);
$use_translit = COption::GetOptionString('rarus.sms4b', 'use_translit', '', $siteList[$i]["ID"]);
$admin_phone = COption::GetOptionString('rarus.sms4b', 'admin_phone', '', $siteList[$i]["ID"]);
$defUserProperty = COption::GetOptionString('rarus.sms4b', 'user_property_phone', '', $siteList[$i]["ID"]);
global $USER;
//админ есть всегда
$rsUser = CUser::GetList(($by="ID"), ($order="desc"), array("ID"=>1),array("SELECT"=>array("UF_*")));
	$arUser = $rsUser->Fetch();
	$arUserPhone[] = '';
	foreach($arUser as $index => $value)
	{
		$pattern = '/(PERSONAL|WORK|UF)/';
		
		if (preg_match($pattern, $index)) 
		{
			$arUserPhone[] = $index;
		}
	}

if (CModule::IncludeModule("subscribe"))
{
	$event_subscribe_confirm = COption::GetOptionString('rarus.sms4b', 'event_subscribe_confirm', '', $siteList[$i]["ID"]);
	$admin_event_subscribe_confirm = COption::GetOptionString('rarus.sms4b', 'admin_event_subscribe_confirm', '', $siteList[$i]["ID"]);
}

if (CModule::IncludeModule("sale"))
{
	$event_sale_new_order = COption::GetOptionString('rarus.sms4b', 'event_sale_new_order', '', $siteList[$i]["ID"]);
	$event_sale_order_paid = COption::GetOptionString('rarus.sms4b', 'event_sale_order_paid', '', $siteList[$i]["ID"]);
	$event_sale_order_delivery = COption::GetOptionString('rarus.sms4b', 'event_sale_order_delivery', '', $siteList[$i]["ID"]);
	$event_sale_order_cancel = COption::GetOptionString('rarus.sms4b', 'event_sale_order_cancel', '', $siteList[$i]["ID"]);

	$admin_event_sale_new_order = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_new_order', '', $siteList[$i]["ID"]);
	$admin_event_sale_order_paid = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_order_paid', '', $siteList[$i]["ID"]);
	$admin_event_sale_order_delivery = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_order_delivery', '', $siteList[$i]["ID"]);
	$admin_event_sale_order_cancel = COption::GetOptionString('rarus.sms4b', 'admin_event_sale_order_cancel', '', $siteList[$i]["ID"]);

	$phone_number_code = COption::GetOptionString('rarus.sms4b', 'phone_number_code', '', $siteList[$i]["ID"]);

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

		$arAdminStatus['sale'][] = array(
				"admin_event_sale_status_".$status['ID'] => COption::GetOptionString('rarus.sms4b', "admin_event_sale_status_".$status['ID'], '', $siteList[$i]["ID"]),
				"NAME" => $status['NAME'],
		);
	}
}
if (CModule::IncludeModule("tasks"))
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

if (CModule::IncludeModule("support"))
{
	$event_ticket_new_for_techsupport = COption::GetOptionString('rarus.sms4b', 'event_ticket_new_for_techsupport', '', $siteList[$i]["ID"]);
	$admin_event_ticket_new_for_techsupport = COption::GetOptionString('rarus.sms4b', 'admin_event_ticket_new_for_techsupport', '', $siteList[$i]["ID"]);
 }
if (CModule::IncludeModule("intranet"))
{
//	$event_corp_add_calendar = COption::GetOptionString('rarus.sms4b', 'event_corp_add_calendar', '', $siteList[$i]["ID"]);
//	$event_corp_update_calendar = COption::GetOptionString('rarus.sms4b', 'event_corp_update_calendar', '', $siteList[$i]["ID"]);
//	$event_corp_reminder_calendar = COption::GetOptionString('rarus.sms4b', 'event_corp_reminder_calendar', '', $siteList[$i]["ID"]);
}
$defsenderPublic = COption::GetOptionString('rarus.sms4b', 'defsenderPublic', '', $siteList[$i]["ID"]);
?>
		<table cellpadding="2" cellspacing="2" border="0" width="100%" align="center">
			<tr class="heading"><td colspan="2"><?= GetMessage("SMS4B_TAB_SEND")?></td></tr>
			<tr>
				<td valign="top" align="right" width="50%"><?echo GetMessage("opt_defsender");?></td>
				<td valign="top">
					<select name="defsender[<?=$siteList[$i]["ID"]?>]">
						<?foreach ($arrDF as $ardefsender): ?>
							<option value="<?=$ardefsender?>"<?=($ardefsender ==$defsender ? " selected=\"selected\"" : "")?>><?=$ardefsender?></option>
						<?endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("user_phone");?></td>
				<td valign="top">
					<select name="user_property_phone[<?=$siteList[$i]["ID"]?>]">
						<?foreach ($arUserPhone as $index): ?>
							<option value="<?=$index?>"<?=($index ==$defUserProperty ? " selected=\"selected\"" : "")?>><?=$index?></option>
						<?endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("use_translit");?></td>
				<td valign="top"><input type="checkbox" name="use_translit[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($use_translit == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
	<? if (IsModuleInstalled("sale")):?>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("phone_number_code");?></td>
				<td valign="top">
					<select name="phone_number_code[<?=$siteList[$i]["ID"]?>]">
					<?foreach ($orderProps as $prop): 
					echo "<pre>"; print_r($phone_number_code); echo "</pre>";
					?>
							<option value="<?=$prop['CODE']?>"<?=($prop['CODE'] == $phone_number_code ? " selected=\"selected\"" : "")?>><?=$prop['NAME'] . (in_array($prop['CODE'], $commonCodes)? "" : " (" . $person[$prop['PERSON_TYPE_ID']] . ")" )?></option>
					<?endforeach;?>
					</select>
				</td>
			</tr>
	<? endif;?>
			<tr class="heading"><td colspan="2"><?= GetMessage("SMS4B_TAB_EVENTS")?></td></tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_subscribe_confirm");?></td>
				<td valign="top"><input type="checkbox" name="event_subscribe_confirm[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($event_subscribe_confirm == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
					<? if (IsModuleInstalled("support")):?>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_ticket_new_for_techsupport");?></td>
				<td valign="top"><input type="checkbox" name="event_ticket_new_for_techsupport[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($event_ticket_new_for_techsupport == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
		<? endif;?>
		<? if (IsModuleInstalled("sale")):?>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_new_order");?></td>
				<td valign="top"><input type="checkbox" name="event_sale_new_order[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($event_sale_new_order == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>


			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_order_paid");?></td>
				<td valign="top"><input type="checkbox" name="event_sale_order_paid[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($event_sale_order_paid == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_order_delivery");?></td>
				<td valign="top"><input type="checkbox" name="event_sale_order_delivery[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($event_sale_order_delivery == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_order_cancel");?></td>
				<td valign="top"><input type="checkbox" name="event_sale_order_cancel[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($event_sale_order_cancel == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
			<tr><td colspan="2" align="center">
					<tr class="heading"><td  colspan="2"><?=GetMessage('SMS4B_TAB_TITLE_STATUS_CHANGE')?></td></tr>
					<? foreach ($arSaleStatus['sale'] as $status):?>
					<tr>
						<td align="right"><?=$status['NAME']?></td>
						<td><input type="checkbox" name="<?=key($status);?>[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($status[key($status)] == 'Y'? " checked = \"checked\" " : "" )?>/></td>
					</tr>
					<?endforeach;?>
			</td></tr>

		<? endif;?>

			<tr class="heading"><td colspan="2"><?= GetMessage("SMS4B_TAB_EVENTS_SHOP")?></td></tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("SMS4B_ADMIN_PHONE");?></td>
				<td valign="top"><textarea name="admin_phone[<?=$siteList[$i]["ID"]?>]" cols="20" rows="3"><?=$admin_phone?></textarea></td>
			</tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_subscribe_confirm");?></td>
				<td valign="top"><input type="checkbox" name="admin_event_subscribe_confirm[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($admin_event_subscribe_confirm == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
		<?if (IsModuleInstalled("support")):?>
		<tr>
			<td valign="top" align="right"><?echo GetMessage("opt_ticket_new_for_techsupport");?></td>
			<td valign="top"><input type="checkbox" name="admin_event_ticket_new_for_techsupport[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($admin_event_ticket_new_for_techsupport == 'Y'? " checked = \"checked\" " : "" )?>/></td>
		</tr>
		<?endif;?>
		<? if (IsModuleInstalled("sale")):?>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_new_order");?></td>
				<td valign="top"><input type="checkbox" name="admin_event_sale_new_order[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($admin_event_sale_new_order == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_order_paid");?></td>
				<td valign="top"><input type="checkbox" name="admin_event_sale_order_paid[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($admin_event_sale_order_paid == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_order_delivery");?></td>
				<td valign="top"><input type="checkbox" name="admin_event_sale_order_delivery[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($admin_event_sale_order_delivery == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_order_cancel");?></td>
				<td valign="top"><input type="checkbox" name="admin_event_sale_order_cancel[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($admin_event_sale_order_cancel == 'Y'? " checked = \"checked\" " : "" )?>/></td>
			</tr>
			<tr><td  colspan="2" align="center">
				<tr class="heading"><td colspan="2"><?=GetMessage('SMS4B_TAB_TITLE_STATUS_CHANGE_ADMIN')?></td></tr>
					<? foreach ($arAdminStatus['sale'] as $status):?>
					<tr>
						<td align="right"><?=$status['NAME']?></td>
						<td><input type="checkbox" name="<?=key($status);?>[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($status[key($status)] == 'Y'? " checked = \"checked\" " : "" )?>/></td>
					</tr>
					<?endforeach;?>
			</td></tr>
		<?endif;?>

		<? if (IsModuleInstalled("tasks")):?>
			<tr class="heading"><td align="center" colspan="2"><?=GetMessage('SMS4B_HEADER_TASKS')?></td></tr>
			<tr><td align="center" colspan="2">
			<table width="240px">
				<tr class="heading"><td colspan="2"><?=GetMessage('SMS4B_TAB_TITLE_ADD_TASK')?></td></tr>
				<tr>
					<td valign="top" align="left"><?echo GetMessage("SMS4B_LOW_TASK");?></td>
					<td valign="top"><input type="checkbox" name="add_low_task[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($add_low_task == 'Y'? " checked = \"checked\" " : "" )?>/></td>
				</tr>
				<tr>
					<td valign="top" align="left"><?echo GetMessage("SMS4B_MIDDLE_TASK");?></td>
					<td valign="top"><input type="checkbox" name="add_middle_task[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($add_middle_task == 'Y'? " checked = \"checked\" " : "" )?>/></td>
				</tr>
				<tr>
					<td valign="top" align="left"><?echo GetMessage("SMS4B_HIGHT_TASK");?></td>
					<td valign="top"><input type="checkbox" name="add_hight_task[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($add_hight_task == 'Y'? " checked = \"checked\" " : "" )?>/></td>
				</tr>

				<tr class="heading"><td colspan="2" align="center"><?=GetMessage('SMS4B_TAB_TITLE_UPDATE_TASK')?></td></tr>
				<tr>
					<td valign="top" align="left"><?echo GetMessage("SMS4B_LOW_TASK");?></td>
					<td valign="top"><input type="checkbox" name="update_low_task[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($update_low_task == 'Y'? " checked = \"checked\" " : "" )?>/></td>
				</tr>
				<tr>
					<td valign="top" align="left"><?echo GetMessage("SMS4B_MIDDLE_TASK");?></td>
					<td valign="top"><input type="checkbox" name="update_middle_task[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($update_middle_task == 'Y'? " checked = \"checked\" " : "" )?>/></td>
				</tr>
				<tr>
					<td valign="top" align="left"><?echo GetMessage("SMS4B_HIGHT_TASK");?></td>
					<td valign="top"><input type="checkbox" name="update_hight_task[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($update_hight_task == 'Y'? " checked = \"checked\" " : "" )?>/></td>
				</tr>

				<tr class="heading"><td colspan="2" align="center"><?=GetMessage('SMS4B_TAB_TITLE_DELETE_TASK')?></td></tr>
				<tr>
					<td valign="top" align="left"><?echo GetMessage("SMS4B_LOW_TASK");?></td>
					<td valign="top"><input type="checkbox" name="delete_low_task[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($delete_low_task == 'Y'? " checked = \"checked\" " : "" )?>/></td>
				</tr>
				<tr>
					<td valign="top" align="left"><?echo GetMessage("SMS4B_MIDDLE_TASK");?></td>
					<td valign="top"><input type="checkbox" name="delete_middle_task[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($delete_middle_task == 'Y'? " checked = \"checked\" " : "" )?>/></td>
				</tr>
				<tr>
					<td valign="top" align="left"><?echo GetMessage("SMS4B_HIGHT_TASK");?></td>
					<td valign="top"><input type="checkbox" name="delete_hight_task[<?=$siteList[$i]["ID"]?>]" value="Y"<?=($delete_hight_task == 'Y'? " checked = \"checked\" " : "" )?>/></td>
				</tr>
			</table>
		</td></tr>
		<? endif?>

			<tr class="heading"><td colspan="2"><?=GetMessage("SMS4B_TAB_TITLE_PUBLIC_SEND")?></td></tr>
			<tr>
				<td valign="top" align="right"><?echo GetMessage("opt_defsender");?></td>
				<td valign="top">
					<select name="defsenderPublic[<?=$siteList[$i]["ID"]?>]">
						<?foreach ($arrDF as $ardefsender): ?>
							<option value="<?=$ardefsender?>"<?=($ardefsender ==$defsenderPublic ? " selected=\"selected\"" : "")?>><?=$ardefsender?></option>
						<?endforeach;?>
					</select>
				</td>
			</tr>
		</table>
<?
endfor;
$tabControl3->End();
?>
		</td>
	</tr><?
	//sms event
	$tabControl->BeginNextTab();?>
	<? CUtil::InitJSCore( array('ajax' , 'popup' ));?>
	<?
	//now only ro russia
	$arFilter = array(
		"LID" => "ru"
	);
	$obEvents = CEventType::GetList($arFilter);
	while ($arEvent = $obEvents->Fetch())
	{
		if (strstr($arEvent["EVENT_NAME"], "SMS4B")  //all events sms4b
			//skip events that are already customised by module
			||	strstr($arEvent["EVENT_NAME"], "SALE_STATUS_CHANGED")
			||	strstr($arEvent["EVENT_NAME"], "SUBSCRIBE_CONFIRM")
			||	strstr($arEvent["EVENT_NAME"], "SALE_ORDER_PAID")
			||	strstr($arEvent["EVENT_NAME"], "SALE_ORDER_DELIVERY")
			||	strstr($arEvent["EVENT_NAME"], "SALE_ORDER_CANCEL")
			||	strstr($arEvent["EVENT_NAME"], "SALE_NEW_ORDER")
			||	strstr($arEvent["EVENT_NAME"], "SALE_RECURRING_CANCEL")
			||	strstr($arEvent["EVENT_NAME"], "TICKET_NEW_FOR_TECHSUPPORT")
			||	strstr($arEvent["EVENT_NAME"], "TICKET_CHANGE_FOR_TECHSUPPORT")
			)
		{
			$eventTypes[] = $arEvent["EVENT_NAME"];
			if (strstr($arEvent["EVENT_NAME"], "SMS4B"))
			{
				$sms4bEvents[] = $arEvent["EVENT_NAME"];
			}
		}
		else
		{
			$arEvents[] = $arEvent;
		}
	}
	/* Find all events*/
	foreach($siteList as $val)
	{
		$arFilter = Array(
			"SITE_ID" => $val['ID'],
			"ACTIVE" => "Y",
		);
		$dbMess = CEventMessage::GetList($by="site_id", $order="desc", $arFilter);
		while ($arMessage = $dbMess->Fetch())
		{
			$arTemplateEvent[$val['ID']][] = $arMessage["EVENT_NAME"];
		}
		$arTemplateEvent[$val['ID']] = array_unique($arTemplateEvent[$val['ID']]);
	}

	?>
<tr>
	<td>
	<?
	$aTabs2 = Array();
	foreach($siteList as $val)
	{
		$aTabs2[] = Array(
			"DIV"=>"template".$val["ID"],
			"TAB" => "[".$val["ID"]."] ".($val["NAME"]),
			"TITLE" => GetMessage("SMS4B_TAB_TITLE_EMAIL_EVENTS"). " [".$val["ID"]."] ".($val["NAME"])
		);
	}
	$tabControl2 = new CAdminViewTabControl("tabControl2", $aTabs2);
	$tabControl2->Begin();

	foreach($siteList as $val)
	{
		$tabControl2->BeginNextTab();

	?>
		<div class="site" data-site="<?=$val['ID']?>">
		<table class="display" width="100%">
		<thead align="left">
			<th width="5%"><?=GetMessage("TABLE_EMAIL_SMS") ?></th>
			<th width="30%"><?=GetMessage("TABLE_EMAIL_TYPE") ?></th>
			<th><?=GetMessage("TABLE_EMAIL_NAME")?></th>
		</thead>
		<?foreach ( $arEvents as $event ):?>
		<tr class="gradeU">
			<td align="center">
				<? if (in_array("SMS4B_" . $event["EVENT_NAME"], $arTemplateEvent[$val['ID']])):?>
				<img src="/bitrix/images/workflow/green.gif" width="14" height="14" border="0" alt="<?=GetMessage("TABLE_EMAIL_SMS_EXISTS")?>" title="<?=GetMessage("TABLE_EMAIL_SMS_EXISTS")?>">
				<?else:?>
				<?endif;?>
			</td>
			<td><a href="#" class="click" data-event="<?=$event["ID"]?>" title="<?=GetMessage("TABLE_EMAIL_SMS_CLICK")?>"><?=$event["EVENT_NAME"]?></a></td>
			<td><a href="#" class="click" data-event="<?=$event["ID"]?>" title="<?=GetMessage("TABLE_EMAIL_SMS_CLICK")?>"><?=$event["NAME"]?></a></td>
		</tr>
		<?endforeach;?>
		</table>
		</div>
<?	}?>

	</td>
</tr>
<?
$tabControl2->End();
?>
	<div id="ajax-add-answer"></div>
	<script>
		$(document).ready(function() {
			$('.display').dataTable( {
				"bPaginate": false,
				"bLengthChange": false,
				"bFilter": true,
				"bSort": true,
				"aaSorting": [[1,'asc']],
				"bInfo": false,
				"bAutoWidth": false,
				"oLanguage": {
					"sZeroRecords": "<?=GetMessage("TABLE_EMAIL_NO_ELEMENTS")?>",
					"sSearch": "<?=GetMessage("TABLE_EMAIL_SEARCH")?>",
			}
			});
			//@todo todo window modal and center it
			$(".click").click(function(e){
				var windowId = $(this).data("event");
				e.preventDefault();
				var addTemplate = '';
				var index = BX.PopupWindowManager._getPopupIndex(windowId);
				//we work with manager
				//we do so, because all pop-us have common div#ajax-add-answer
				//another way to create for each pop-up its own div
				if (index >= 0)
				{
					addTemplate = BX.PopupWindowManager._popups[index];
					$("#ajax-add-answer").remove();
					addTemplate.setContent(BX.create('div', {'props': {'id': 'ajax-add-answer'}}));
				}
				else
				{
					var addTemplate = BX.PopupWindowManager.create(windowId, this, {
						content: BX('ajax-add-answer'),
						closeIcon: {right: "20px", top: "10px"},
						titleBar: {content: BX.create("span", {html: '<b><?=GetMessage("TABLE_EMAIL_POPUP_TITLE")?></b>', 'props': {'className': 'access-title-bar'}})},
						zIndex: 0,
						offsetLeft: 0,
						offsetTop: 0,
						draggable: {restrict: true},
						buttons: [
								new BX.PopupWindowButton({
								text: "<?=GetMessage("TABLE_EMAIL_SAVE")?>",
								className: "popup-window-button-accept",
								events: {
									click: function(){
											BX.ajax.submit(BX("myForm"), function(data){ 
											BX('ajax-add-answer').innerHTML = data;
									});
								}}
							}),
							new BX.PopupWindowButton({
								text: "<?=GetMessage("TABLE_EMAIL_CLOSE")?>",
								className: "webform-button-link-cancel",
								events: {click: function(){
									this.popupWindow.close(); // закрытие окна
									}}
							})
								]
					});
				}
				var addlink = '/bitrix/admin/sms4b_addtemplate.php?eventID=' + $(this).data("event") + '&site=' + $(this).parents("div.site").data("site");
				BX.ajax.insertToNode(addlink, BX('ajax-add-answer'));
				addTemplate.show();
			});
		});
	</script>
	<?$tabControl->BeginNextTab();?>
	<?echo GetMessage("HELP");?>

	<? $tabControl->BeginNextTab();?>
	<?	if($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST['submit_button'] && $_REQUEST['ticket_text'])
	{
		$info = CModule::CreateModuleObject('rarus.sms4b');

		$text =  $_REQUEST['ticket_text'] . PHP_EOL;

		$text .= GetMessage("SERVER") . ":" . $_SERVER['HTTP_HOST'] .PHP_EOL .
				GetMessage("SENDER") . ":" . $_REQUEST['email'] .PHP_EOL .
				GetMessage("VERSION") . ":" . $info->MODULE_VERSION .PHP_EOL .
				GetMessage("LOGIN")  . ":" . COption::GetOptionString("rarus.sms4b", "login") .PHP_EOL ;
		mail("info@sms4b.ru", GetMessage("EMAIL_SUBJECT") . $_SERVER['HTTP_HOST'], $text);
	}

	?>
		<tr>
			<td align="right" width="30%"><span class="required">*</span><?=GetMessage("NAME")?></td>
			<td><input type="text" name="fio"/></td>
		</tr>
		<tr>
			<td align="right" width="30%"><span class="required">*</span><?=GetMessage("EMAIL") ?></td>
			<td><input type="text" name="email" value="<?=COption::GetOptionString('main', 'email_from')?>"/></td>
		</tr>
		<tr>
			<td align="right" width="30%"><span class="required">*</span><?=GetMessage("ABOUT")?><br>
			<small><?=GetMessage("ERROR")?></small></td>
			<td><textarea name="ticket_text" rows="6" cols="60"></textarea></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" name="submit_button" value="<?=GetMessage("SUBMIT");?>"></td>
		</tr>

	<?$tabControl->BeginNextTab();?>
	<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
	<?
	if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
	{
		if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
			LocalRedirect($_REQUEST["back_url_settings"]);
		else
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
	}
	?>

	<?$tabControl->Buttons();?>
		<input <?if ($SMS_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
		<input <?if ($SMS_RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
		<?if(strlen($_REQUEST["back_url_settings"])>0):?>
			<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?=htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
			<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
		<?endif?>
		<input <?if ($SMS_RIGHT<"W") echo "disabled" ?> type="submit" name="RestoreDefaults" title="<?=GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="confirm('<?=AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?=GetMessage("MAIN_RESTORE_DEFAULTS")?>">
		<?=bitrix_sessid_post();?>
	<?$tabControl->End();?>

	</form>
	<?else:?>
		<?=CAdminMessage::ShowMessage(GetMessage('NO_RIGHTS_FOR_VIEWING'));?>
	<?endif;
?>