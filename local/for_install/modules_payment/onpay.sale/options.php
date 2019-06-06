<?
include_once(dirname(__FILE__)."/include.php");

$module_id = COnpayPayment::$module_id;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$arAllOptions = Array(
	Array("login", GetMessage("ONPAY.SALE_OPTIONS_LOGIN")." ", Array("text", ""), GetMessage("ONPAY.SALE_OPTIONS_LOGIN_DESC")),
	Array("api_in_key", GetMessage("ONPAY.SALE_OPTIONS_API_IN_KEY")." ", Array("text", 60), GetMessage("ONPAY.SALE_OPTIONS_API_IN_KEY_DESC")),
	Array("success_url", GetMessage("ONPAY.SALE_OPTIONS_SUCCESS_URL")." ", Array("text", 60), GetMessage("ONPAY.SALE_OPTIONS_SUCCESS_URL_DESC")),
	Array("fail_url", GetMessage("ONPAY.SALE_OPTIONS_FAIL_URL")." ", Array("text", 60), GetMessage("ONPAY.SALE_OPTIONS_FAIL_URL_DESC")),
	Array("form_id", GetMessage("ONPAY.SALE_OPTIONS_FORM")." ", Array("form_id"), GetMessage("ONPAY.SALE_OPTIONS_FORM_DESC")),
	Array("convert", GetMessage("ONPAY.SALE_OPTIONS_CONVERT")." ", Array("checkbox", 60), GetMessage("ONPAY.SALE_OPTIONS_CONVERT_DESC")),
	Array("price_final", GetMessage("ONPAY.SALE_OPTIONS_PRICE_FINAL")." ", Array("checkbox", 60), GetMessage("ONPAY.SALE_OPTIONS_PRICE_FINAL_DESC")),
	Array("form_lang", GetMessage("ONPAY.SALE_OPTIONS_LANG")." ", Array("lang", 60), GetMessage("ONPAY.SALE_OPTIONS_LANG_DESC")),
);
if(CModule::IncludeModule("currency")) {
	$lcur = CCurrency::GetList(($b="name"), ($order1="asc"), LANGUAGE_ID);
	while($lcur_res = $lcur->Fetch()) {
		$arAllOptions[] = Array("currency_".$lcur_res['CURRENCY'], GetMessage("ONPAY.SALE_OPTIONS_CURRENCY", array("#CURRENCY#"=>$lcur_res['CURRENCY']))." ", Array("currency"), GetMessage("ONPAY.SALE_OPTIONS_CURRENCY_DESC"));
	}
}
$arAllOptions[] = Array("ext_params", GetMessage("ONPAY.SALE_OPTIONS_EXT_PARAMS")." ", Array("text", 60), GetMessage("ONPAY.SALE_OPTIONS_EXT_PARAMS_DESC"));
$arAllOptions[] = Array("width_debug", GetMessage("ONPAY.SALE_OPTIONS_WIDTH_DEBUG")." ", Array("checkbox", 60), GetMessage("ONPAY.SALE_OPTIONS_WIDTH_DEBUG_DESC"));
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => $module_id."_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply)>0 && check_bitrix_sessid())
{
	foreach($arAllOptions as $arOption) {
		$name=$arOption[0];
		$val=$_POST[$name];
		COption::SetOptionString($module_id, $name, $val, $arOption[1]);
	}
	
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}


$tabControl->Begin();
?><form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?
$tabControl->BeginNextTab();
	if(false && function_exists('gethostbyname')):?>
		<tr>
			<td valign="top" width="50%">
				<?=GetMessage("ONPAY.SALE_OPTIONS_HOST_IP")?>
			</td>
			<td valign="top" width="50%">
				<strong><?=gethostbyname($_SERVER['HTTP_HOST'])?></strong>
			</td>
		</tr>
<?	endif;?>
		<tr>
			<td valign="top" width="50%">
				<?=GetMessage("ONPAY.SALE_OPTIONS_URL_API")?>
				<br /><small><?=GetMessage("ONPAY.SALE_OPTIONS_URL_API_DESC")?></small>
			</td>
			<td valign="top" width="50%">
				<strong>http://<?=$_SERVER['HTTP_HOST']?>/bitrix/tools/onpay_sale_api.php</strong>
			</td>
		</tr>
<?	foreach($arAllOptions as $arOption):
		switch($arOption[0]) {
			case "currency_RUB":
			case "currency_RUR":
				$val = COption::GetOptionString($module_id, $arOption[0], 'WMR');
				break;
			case "currency_USD":
				$val = COption::GetOptionString($module_id, $arOption[0], 'WMZ');
				break;
			case "currency_EUR":
				$val = COption::GetOptionString($module_id, $arOption[0], 'WME');
				break;
			case "convert":
				$val = COption::GetOptionString($module_id, $arOption[0], 'Y');
				break;
			case "form_id":
				$val = COption::GetOptionString($module_id, $arOption[0], COnpayPayment::$_df_form_id);
				break;
			default:
				$val = COption::GetOptionString($module_id, $arOption[0]);
		}
		$type = $arOption[2];
	?>
		<tr>
			<td valign="top" width="50%"><?if($type[0]=="checkbox")
							echo "<label for=\"".htmlspecialchars($arOption[0])."\">".$arOption[1]."</label><br /><small>", $arOption[3], "</small>";
						else
							echo $arOption[1], "\n<br /><small>", $arOption[3], "</small>";?></td>
			<td valign="top" width="50%">
					<?if($type[0]=="checkbox"):?>
						<input type="checkbox" name="<?echo htmlspecialchars($arOption[0])?>" id="<?echo htmlspecialchars($arOption[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
					<?elseif($type[0]=="text"):?>
						<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($arOption[0])?>">
					<?elseif($type[0]=="textarea"):?>
						<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($arOption[0])?>"><?echo htmlspecialchars($val)?></textarea>
					<?elseif($type[0]=="currency"):
						$arCurrency = COnpayPayment::$currency;
						$arCurrencyCaption = array();
						foreach($arCurrency as $currency) {
							$arCurrencyCaption[$currency] = GetMessage("ONPAY.SALE_OPTIONS_CURRENCY_".strtoupper($currency)."_CAPTION");
							$arCurrencyCaption[$currency] = $arCurrencyCaption[$currency] ? $arCurrencyCaption[$currency] : $currency;
						}
						?>
						<select name="<?echo htmlspecialchars($arOption[0])?>"><option value=""><?=GetMessage("ONPAY.SALE_OPTIONS_CURRENCY_EMPTY")?></option>
							<?foreach($arCurrency as $currency):?> <option value="<?=$currency?>"<?=($val==$currency ? ' selected' : '')?>><?=$arCurrencyCaption[$currency]?></option> <?endforeach;?>
						</select>
					<?elseif($type[0]=="lang"):?>
						<select name="<?echo htmlspecialchars($arOption[0])?>"><option value=""><?=GetMessage("ONPAY.SALE_OPTIONS_LANG_EMPTY")?></option>
							<?foreach(array('en') as $lang):?> <option value="<?=$lang?>"<?=($val==$lang ? ' selected' : '')?>><?=GetMessage("ONPAY.SALE_OPTIONS_LANG_".strtoupper($lang)."_CAPTION")?></option> <?endforeach;?>
						</select>
					<?elseif($type[0]=="form_id"):?>
						<select name="<?echo htmlspecialchars($arOption[0])?>">
							<?foreach(COnpayPayment::$form_design as $i=>$caption):?> <option value="<?=$i?>"<?=($val==$i ? ' selected' : '')?>><?=(GetMessage("ONPAY.SALE_OPTIONS_FORMID_".$caption."_CAPTION") ? GetMessage("ONPAY.SALE_OPTIONS_FORMID_".$caption."_CAPTION") : $caption)?></option> <?endforeach;?>
						</select>
					<?endif?>
			</td>
		</tr>
	<?endforeach?>
<?$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	<?endif?>
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
