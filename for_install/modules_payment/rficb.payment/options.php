<?
$module_id = "rficb.payment";

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/options.php");
if(!CModule::IncludeModule("sale"));
$dbStatus = CSaleStatus::GetList(Array("SORT" => "ASC"), Array("LID" => LANGUAGE_ID), false, false, Array("ID", "NAME", "SORT"));

$sRights = $APPLICATION->GetGroupRight($module_id);
/*$arAllOptions = Array(
    Array("key", GetMessage("RFICB.PAYMENT_OPTIONS_KEY")." ",
         GetMessage("RFICB.PAYMENT_OPTIONS_KEY_DESC")),
    Array("secret_key", GetMessage("RFICB.PAYMENT_OPTIONS_SECRET_KEY")." ",
         GetMessage("RFICB.PAYMENT_OPTIONS_SECRET_KEY_DESC")),
	Array("holdemail", GetMessage("RFICB.PAYMENT_OPTIONS_SECRET_KEY")." ",
         GetMessage("RFICB.PAYMENT_OPTIONS_SECRET_KEY_DESC")),
	Array("secret_key", GetMessage("RFICB.PAYMENT_OPTIONS_HOLD_EMAIL")." ",
         GetMessage("RFICB.PAYMENT_OPTIONS_HOLD_EMAIL_DESC")),
);*/
$arError = Array();
if ($sRights>="R"):

//print_r($_POST["_update"][$sSiteID]);
$arPost=Array();
if(($sRights>="W") && ($REQUEST_METHOD=="POST") && strlen($Update)>0 && check_bitrix_sessid())
{
	if(preg_match("#^site_(\S+)$#",$_POST["tabControl_active_tab"],$arMatches))
	{
		$sSiteID = $arMatches[1];
		foreach($_POST["_update"][$sSiteID] as $sKey=>$sValue)
		{
			$arPost[$sSiteID][$sKey]=$sValue;
			if(!strlen(trim($sValue) ))
			{
				$arError[]=$sKey;
			}
		}
		if(empty($arError))
		{
			foreach($_POST["_update"][$sSiteID] as $sKey=>$sValue)
			{
				COption::SetOptionString($module_id, "{$sSiteID}_{$sKey}", $sValue);
			}
		}
		//die();
	}
}


/*================== Old values ====================*/
$sDefaultKey = COption::GetOptionString($module_id, "key","");
$sDefaultSecretKey = COption::GetOptionString($module_id, "secret_key","");
$sDefaultWidget = COption::GetOptionString($module_id, "widget","N");
$sWidgetType = COption::GetOptionString($module_id, "widgettype","");
$sDefaultHoldStatus = COption::GetOptionString($module_id, "holdstatus","N");
$sDefaultHoldEmail = COption::GetOptionString($module_id, "holdemail","example@example.org");
$sCommission = COption::GetOptionString($module_id, "commission","N");
$sPayType = COption::GetOptionString($module_id, "paytype","N");
$sPayCart = COption::GetOptionString($module_id, "paycart","N");
$sPayWM = COption::GetOptionString($module_id, "paywm","N");
$sPayYM = COption::GetOptionString($module_id, "payym","N");
$sPayMC = COption::GetOptionString($module_id, "paymc","N");
$sPayQiwi = COption::GetOptionString($module_id, "payqiwi","N");
/*==================================================*/

$obSites = CSite::GetList($a="sort",$b="asc",Array());
$arSites = Array();
while($arSite = $obSites->Fetch())
{
	$arSite["DATA"]=Array(
		"url"=>Array(
			"value"=>"http://{$arSite["SERVER_NAME"]}/bitrix/tools/rficb.payment/result.php",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_SCRIPT_URL"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_SCRIPT_URL_DESC")
		),
		"key"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["key"])?$arPost[$arSite["LID"]]["key"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_key",$sDefaultKey),
			"name"=>"_update[{$arSite["LID"]}][key]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_KEY"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_KEY_DESC")
		),
		"secret_key"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["secret_key"])?$arPost[$arSite["LID"]]["secret_key"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_secret_key",$sDefaultSecretKey),
			"name"=>"_update[{$arSite["LID"]}][secret_key]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_SECRET_KEY"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_SECRET_KEY_DESC")
		),
		"widget"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["widget"])?$arPost[$arSite["LID"]]["widget"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_widget",$sDefaultWidget),
			"name"=>"_update[{$arSite["LID"]}][widget]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_WIDGET"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_WIDGET_DESC")
		),
		"widgettype"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["widgettype"])?$arPost[$arSite["LID"]]["widgettype"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_widgettype",$sWidgetType),
			"name"=>"_update[{$arSite["LID"]}][widgettype]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_WIDGETTYPE"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_WIDGETTYPE_DESC")
		),
		"holdemail"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["holdemail"])?$arPost[$arSite["LID"]]["holdemail"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_holdemail",$sDefaultHoldEmail),
			"name"=>"_update[{$arSite["LID"]}][holdemail]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_HOLD_EMAIL"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_HOLD_EMAIL_DESC")
		),
		"holdstatus"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["holdstatus"])?$arPost[$arSite["LID"]]["holdstatus"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_holdstatus",$sDefaultHoldStatus),
			"name"=>"_update[{$arSite["LID"]}][holdstatus]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_HOLD_STATUS"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_HOLD_STATUS_DESC")
		),
		"commission"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["commission"])?$arPost[$arSite["LID"]]["commission"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_commission",$sCommission),
			"name"=>"_update[{$arSite["LID"]}][commission]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_COMMISSION"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_COMMISSION_DESC")
		),
		"paytype"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["paytype"])?$arPost[$arSite["LID"]]["paytype"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_paytype",$sPayType),
			"name"=>"_update[{$arSite["LID"]}][paytype]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYTYPE"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYTYPE_DESC")
		),
		"paycart"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["paycart"])?$arPost[$arSite["LID"]]["paycart"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_paycart",$sPayCart),
			"name"=>"_update[{$arSite["LID"]}][paycart]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYCART"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYCART_DESC")
		),
		"paywm"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["paywm"])?$arPost[$arSite["LID"]]["paywm"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_paywm",$sPayWM),
			"name"=>"_update[{$arSite["LID"]}][paywm]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYWM"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYWM_DESC")
		),
		"payym"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["payym"])?$arPost[$arSite["LID"]]["payym"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_payym",$sPayYM),
			"name"=>"_update[{$arSite["LID"]}][payym]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYYM"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYYM_DESC")
		),
		"paymc"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["paymc"])?$arPost[$arSite["LID"]]["paymc"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_paymc",$sPayMC),
			"name"=>"_update[{$arSite["LID"]}][paymc]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYMC"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYMC_DESC")
		),
		"payqiwi"=>Array(
			"value"=>isset($arPost[$arSite["LID"]]["payqiwi"])?$arPost[$arSite["LID"]]["payqiwi"]:COption::GetOptionString($module_id, "{$arSite["LID"]}_payqiwi",$sPayQiwi),
			"name"=>"_update[{$arSite["LID"]}][payqiwi]",
			"big_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYQIWI"),
			"small_text"=>GetMessage("RFICB.PAYMENT_OPTIONS_PAYQIWI_DESC")
		)
	);
	$arSites[]=$arSite;
}

$aTabs = array();
foreach($arSites as $arSite)
{
	$aTabs[]=array("DIV" => "site_{$arSite["LID"]}", "TAB" => $arSite["NAME"]." [{$arSite["LID"]}]",
		"ICON" => "rficb.payment_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET"));
}
$aTabs[] = array("DIV" => "editrights", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS"));
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if(!empty($arError))
{
	$arErrorsString = Array();

	foreach($arError as $sKey)
	{
		$sName = GetMessage("RFICB.PAYMENT_OPTIONS_".ToUpper($sKey));
		$arErrorsString[]=GetMessage("RFICB.PAYMENT_OPTIONS_ERROR",Array("#FIELD#"=>$sName));
	}
	CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>implode("<br/>",$arErrorsString), "DETAILS"=>"", "HTML"=>true));
}
//print_r($arSite["DATA"]);
$tabControl->Begin();
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>">
	<?=bitrix_sessid_post()?>
	<?foreach($arSites as $arSite):?>
		<?$tabControl->BeginNextTab();?>
		<tr>
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["url"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["url"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<strong><?=$arSite["DATA"]["url"]["value"]?></strong>
			</td>
		</tr>
		<tr>
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["key"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["key"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="text" name="<?=$arSite["DATA"]["key"]["name"]?>" value="<?=$arSite["DATA"]["key"]["value"]?>">
			</td>
		</tr>
		<tr>
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["secret_key"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["secret_key"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="text" name="<?=$arSite["DATA"]["secret_key"]["name"]?>" value="<?=$arSite["DATA"]["secret_key"]["value"]?>">
			</td>
		</tr>

		<!-- <tr>
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["commission"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["commission"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="checkbox" id="<?=$arSite["LID"]?>_commission" <?if ($arSite["DATA"]["commission"]["value"] =='Y') echo "checked";?>>
				<input type="text" id="<?=$arSite["LID"]?>_commission_t" name="<?=$arSite["DATA"]["commission"]["name"]?>" value="<?=$arSite["DATA"]["commission"]["value"]?>" style="display:none">
			</td>
		</tr> -->
		<tr>
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["paytype"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["paytype"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="checkbox" onchange ="checkPay('<?=$arSite["LID"]?>')" id="<?=$arSite["LID"]?>_ptype" <?if ($arSite["DATA"]["paytype"]["value"] =='Y') echo "checked";?>>
				<input type="text" id="<?=$arSite["LID"]?>_ptype_t"name="<?=$arSite["DATA"]["paytype"]["name"]?>" value="<?=$arSite["DATA"]["paytype"]["value"]?>" style="display:none">
			</td>
		</tr>
		<? if ($arSite["DATA"]["paytype"]["value"] =='Y') { $sty = '';}
else $sty = 'display:none'; ?>
		<tr class="<?=$arSite["LID"]?>_pay" style="<?= $sty;?>">
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["paycart"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["paycart"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="checkbox" id="<?=$arSite["LID"]?>_paycart" onchange ="check(this)" <?if ($arSite["DATA"]["paycart"]["value"] =='Y') echo "checked";?>>
				<input type="text" id="<?=$arSite["LID"]?>_paycart_t" name="<?=$arSite["DATA"]["paycart"]["name"]?>" value="<?=$arSite["DATA"]["paycart"]["value"]?>" style="display:none">
			</td>
		</tr>
		<tr class="<?=$arSite["LID"]?>_pay" style="<?= $sty;?>">
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["paywm"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["paywm"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="checkbox" id="<?=$arSite["LID"]?>_paywm" onchange ="check(this)" <?if ($arSite["DATA"]["paywm"]["value"] =='Y') echo "checked";?>>
				<input type="text" id="<?=$arSite["LID"]?>_paywm_t" name="<?=$arSite["DATA"]["paywm"]["name"]?>" value="<?=$arSite["DATA"]["paywm"]["value"]?>" style="display:none">
			</td>
		</tr>
		<tr class="<?=$arSite["LID"]?>_pay" style="<?= $sty;?>">
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["payym"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["payym"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="checkbox" id="<?=$arSite["LID"]?>_payym" onchange ="check(this)" <?if ($arSite["DATA"]["payym"]["value"] =='Y') echo "checked";?>>
				<input type="text" id="<?=$arSite["LID"]?>_payym_t" name="<?=$arSite["DATA"]["payym"]["name"]?>" value="<?=$arSite["DATA"]["payym"]["value"]?>" style="display:none">
			</td>
		</tr>
		<tr class="<?=$arSite["LID"]?>_pay" style="<?= $sty;?>">
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["paymc"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["paymc"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="checkbox" id="<?=$arSite["LID"]?>_paymc" onchange ="check(this)" <?if ($arSite["DATA"]["paymc"]["value"] =='Y') echo "checked";?>>
				<input type="text" id="<?=$arSite["LID"]?>_paymc_t" name="<?=$arSite["DATA"]["paymc"]["name"]?>" value="<?=$arSite["DATA"]["paymc"]["value"]?>" style="display:none">
			</td>
		</tr>
		<tr class="<?=$arSite["LID"]?>_pay" style="<?= $sty;?>">
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["payqiwi"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["payqiwi"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="checkbox" id="<?=$arSite["LID"]?>_payqiwi" onchange ="check(this)" <?if ($arSite["DATA"]["payqiwi"]["value"] =='Y') echo "checked";?>>
				<input type="text" id="<?=$arSite["LID"]?>_payqiwi_t" name="<?=$arSite["DATA"]["payqiwi"]["name"]?>" value="<?=$arSite["DATA"]["payqiwi"]["value"]?>" style="display:none">
			</td>
		</tr>
		<tr>
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["widget"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["widget"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="checkbox" id="<?=$arSite["LID"]?>_chwid" onchange ="checkWidget('<?=$arSite["LID"]?>')" <?if ($arSite["DATA"]["widget"]["value"] == 'Y') echo "checked";?>>
				<input type="text" name="<?=$arSite["DATA"]["widget"]["name"]?>" value="<?=$arSite["DATA"]["widget"]["value"]?>" id="<?=$arSite["LID"]?>_wid" style="display:none">

			</td>
		</tr>
	<? 
if ($arSite["DATA"]["widget"]["value"] == 'Y') { $style = '';}
else $style = 'display:none'; ?>
	<tr id="<?=$arSite["LID"]?>_widgettype" style="<?= $style;?>">
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["widgettype"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["widgettype"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
			<? 	$arTypes = ['1'=>'type 1','2'=>'type 2'];
				$val = $arSite["DATA"]["widgettype"]["value"];
			?>
			<select name="<?=$arSite["DATA"]["widgettype"]["name"]?>">
				<?
				foreach($arTypes as $statusID => $statusName)
				{
					?><option value="<?=$statusID?>"<?if ($val == $statusID) echo " selected";?>><?=$statusName?></option><?
				}
				?>
			</select>
			</td>
		</tr>
		<tr>
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["holdemail"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["holdemail"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<input type="text" name="<?=$arSite["DATA"]["holdemail"]["name"]?>" value="<?=$arSite["DATA"]["holdemail"]["value"]?>">
			</td>
		</tr>
		<tr>
			<td valign="top" width="50%" style="text-align: right">
				<label for="id_install_public">
					<?=$arSite["DATA"]["holdstatus"]["big_text"]?><br/>
					<small><?=$arSite["DATA"]["holdstatus"]["small_text"]?></small>
				</label>
			</td>
			<td valign="top" width="50%" style="text-align: left">
				<!--<input type="text" name="<?=$arSite["DATA"]["holdstatus"]["name"]?>" value="<?=$arSite["DATA"]["holdstatus"]["value"]?>"> -->
				<? while ($arStatus = $dbStatus->GetNext())
					{
						$arStatuses[$arStatus["ID"]] = "[".$arStatus["ID"]."] ".$arStatus["NAME"];
					} 
				$valst = $arSite["DATA"]["holdstatus"]["value"];
			?>
			<select name="<?=$arSite["DATA"]["holdstatus"]["name"]?>">
				<?
				foreach($arStatuses as $statusID => $statusName)
				{
					?><option value="<?=$statusID?>"<?if ($valst == $statusID) echo " selected";?>><?=$statusName?></option><?
				}
				?>
			</select>
			</td>
		</tr>
	<?endforeach;?>
	<?
	$tabControl->BeginNextTab();
	?>
	<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
	<?$tabControl->Buttons();?>

	<input type="submit" <?if ($sRights<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
	<!--<input type="submit" name="reset" value="<?/*echo GetMessage("MAIN_RESET")*/?>">-->
	<?$tabControl->End();?>
</form>
<script type="text/javascript"> 
	function checkWidget(LID) {
		//var element = this;
		var wid = document.getElementById(LID+'_wid');
		var check = document.getElementById(LID+'_chwid');
		var wtype = document.getElementById(LID+'_widgettype');
		if(check.checked) { 
			wid.value ="Y";wtype.style.display = "";
		}
		else { 
			wid.value ="N";wtype.style.display = "none";
		}
	}
	function check(obj) {
		var val = document.getElementById(obj.id+'_t');
		if(obj.checked) {val.value = 'Y';}
		else {val.value = 'N';}
		//		alert(val.value);
	}
	function checkPay(LID) { 
		var check = document.getElementById(LID+'_ptype');
		var val = document.getElementById(LID+'_ptype_t');
		if(check.checked) {
			var f1 = document.getElementsByClassName(LID+'_pay');
			val.value = 'Y';
			for (var i=0; i<f1.length; i++) {
				f1[i].style.display = "";
			 }
		} else {
			var f1 = document.getElementsByClassName(LID+'_pay');
			val.value = 'N';
			for (var i=0; i<f1.length; i++) {
				f1[i].style.display = "none";
			 }
		}
	}
</script>
<?endif?>