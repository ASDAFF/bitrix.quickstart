<?if(!check_bitrix_sessid()) return;

global $errors;
$module_id = 'onpay.sale';
//$cls_path = str_replace("install/step1.php", "", __FILE__)."classes/onpay_payment.php";
$cls_path = str_replace("step1.php", "", __FILE__)."../classes/onpay_payment.php";
include_once $cls_path;

if($errors!==false):
	for($i=0; $i<count($errors); $i++)
		$alErrors .= $errors[$i]."<br>";
	echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage("MOD_INST_ERR"), "DETAILS"=>$alErrors, "HTML"=>true));
endif;
$arAllOptions = COnpayPayment::_GetAllOptions();
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANG?>">
<input type="hidden" name="id" value="<?=$module_id?>">
<input type="hidden" name="install" value="Y">
<input type="hidden" name="step" value="2">
<table cellpadding="3" cellspacing="0" border="0" width="0%">
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
			default:
				$val = COption::GetOptionString($module_id, $arOption[0]);
		}
		$type = $arOption[2];
	?>
		<tr>
			<td valign="top" width="50%"><?if($type[0]=="checkbox")
							echo "<p><label for=\"".htmlspecialchars($arOption[0])."\">".$arOption[1]."</label><br /><small>", $arOption[3], "</small></p>";
						else
							echo "<p><label for=\"id_install_public\">", $arOption[1], ":\n<br /><small>", $arOption[3], "</small></label></p>";?></td>
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
	<?endforeach?></table>
<p>
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("MOD_INSTALL")?>">	
</p>
<form>