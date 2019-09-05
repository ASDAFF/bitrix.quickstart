<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

global $APPLICATION;
if($arResult['SHOW_FORM']==1 and $arResult['CHECK_SPAM']!=1 and !$arResult['SENDFORM']) {
$APPLICATION->RestartBuffer();
//mprint($arResult);
$arr2Price = array();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
<?echo '<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'"'.(true ? ' /':'').'>'."\n";
$APPLICATION->ShowHead();
?>
<link href="<?=$templateFolder."/styled.css"?>" type="text/css" rel="stylesheet">
</head>
<body>
<form method="post" id="bistrclick">
<?if(is_array($arResult['ERROR']) && count($arResult['ERROR'])>0){?>
<div class="errorField">
<?echo implode(', ', $arResult['ERROR']);?>
</div>
<?}?>
<div class="mlf_product">
	<div class="imager">
		<div class="imageWp">
			<?
			if($arResult["TOVAR"]["DETAIL_PICTURE"]) {
			$src = CFile::GetPath($arResult["TOVAR"]["DETAIL_PICTURE"]);?>
			<img class="item_img" itemprop="image" src="<?=$src?>" alt="<?=$arResult["TOVAR"]["NAME"]?>" />
			<?}
			else{?>
			<div class="no-photo130" style="min-height:130px; min-width:130px;"></div>
			<?}
			?>
		</div>
		<div class="pricen">
			<div class="priceFirst">
				<?
				if($arResult["OFFERS"]){
					echo GetMessage('CAT_BK_OT').' '.CurrencyFormat(round($arResult['PRICE_MIN']),$arParams['CURRENCY_ID']);
				}
				else{
					foreach($arResult["TOVAR"]["PRICES"] as $arPrice){
						if($arPrice['CURRENCY']!=$arParams['CURRENCY_ID']) {
							$price = round(CCurrencyRates::ConvertCurrency($arPrice['DISCOUNT_PRICE'], $arPrice['CURRENCY'], $arParams['CURRENCY_ID']));
							$arr2Price[$arResult["TOVAR"]["ID"]] = round($price);
							echo CurrencyFormat(round($price),$arParams['CURRENCY_ID']);
						}
						else {
							echo CurrencyFormat(round($arPrice['DISCOUNT_PRICE']),$arParams['CURRENCY_ID']);
							$arr2Price[$arResult["TOVAR"]["ID"]] = round($arPrice['DISCOUNT_PRICE']);
						}
					}
				}
				?>
			</div>
			<div class="priceSecond">
				<?
				if($arResult["OFFERS"]){
					$price = round(CCurrencyRates::ConvertCurrency($arResult['PRICE_MIN'], $arParams['CURRENCY_ID'], $arParams['CURRENCY_SECOND']));
					if($arParams['CURRENCY_SECOND']) echo CurrencyFormat($price,$arParams['CURRENCY_SECOND']);
				}else{
					if($arPrice['CURRENCY']!=$arParams['CURRENCY_SECOND']) {
						$price = round(CCurrencyRates::ConvertCurrency($arPrice['DISCOUNT_PRICE'], $arPrice['CURRENCY'], $arParams['CURRENCY_SECOND']));
						if($arParams['CURRENCY_SECOND']) echo CurrencyFormat($price,$arParams['CURRENCY_SECOND']);
					}
					else {
						if($arParams['CURRENCY_SECOND']) echo CurrencyFormat($arPrice['DISCOUNT_PRICE'],$arParams['CURRENCY_SECOND']);
					}
				}
				?>
			</div>
		</div>
	</div>
	<div class="naz"><?=$arResult["TOVAR"]["NAME"]?></div>
</div>
<div class="mlfform">
		<?
		if($arResult['OFFERS']) {
		echo '<table class="offers">';
			foreach($arResult['OFFERS'] as $key=>$offer) {
				echo '<tr><td>';?>
				<input type="radio" name="offer" value="<?=$offer['ID']?>"<?if(($key==0 && !$_REQUEST['offer']) || $offer['ID']==intval($_REQUEST['offer'])) echo " checked";?>/>
				<?echo '</td><td><div class="naz">'.$offer['NAME'].'</div>';?>
				<?if(isset($offer['PROP']) && is_array($offer['PROP']) && count($offer['PROP']>0)){
					$props = '';
					foreach ($offer['PROP'] as $orderprop) {
						if(isset($arParams['OFFERS_PROPERTY_CODE']) && is_array($arParams['OFFERS_PROPERTY_CODE']) && $orderprop['VALUE'] && in_array($orderprop['CODE'], $arParams['OFFERS_PROPERTY_CODE'])){
							if($props) $props .= ', ';
							$props .= '<b>'.$orderprop['NAME'].'</b>: '.$orderprop['VALUE'];
						}
					}
				}?>
				<?if($props){
					?>
					<div class="mlfOfferProps"><?=$props?></div>
					<?
				}?>
				<div class="price">
					<?
					if(isset($offer['PRICES']) && is_array($offer['PRICES']) && count($offer['PRICES'])>0){
						foreach($offer['PRICES'] as $arPr) {
							if($arPr['CURRENCY']!=$arParams['CURRENCY_ID']) {
								$price = round(CCurrencyRates::ConvertCurrency($arPr['DISCOUNT_PRICE'], $arPr['CURRENCY'], $arParams['CURRENCY_ID']));
								$arr2Price[$offer['ID']] = round($price);
								echo CurrencyFormat(round($price),$arParams['CURRENCY_ID']);
							}
							else {
								echo CurrencyFormat(round($arPr['DISCOUNT_PRICE']),$arParams['CURRENCY_ID']);
								$arr2Price[$offer['ID']] = round($arPr['DISCOUNT_PRICE']);
							}
						}
					}
					?>
				</div>
				</td></tr><?
			}
		echo '</table>';
		}
		?>
	<input type="hidden" name="prod_id" value="<?=$arResult["TOVAR"]["ID"]?>"/>
	<?
	if(count($arr2Price)>0){
		foreach($arr2Price as $key=>$val){
			?><input type="hidden" name="price_<?=$key?>" value="<?=$val?>"/><?
		}
	}
	?>
	<?
	//print_r($arResult['LABEL']);
	$messfield = false;
	if(is_array($arParams['FIELD_SHOW']) && count($arParams['FIELD_SHOW'])>0){
		foreach($arParams['FIELD_SHOW'] as $value){

		if(isset($arResult['LABEL'][$value])) {
			$fieldname = $arResult['LABEL'][$value];
		}else{
			$fieldname = GetMessage("CAT_BK_FIELD_REQ_".strtoupper($value));
		}
		?><?
			if($value=='mess') { $messfield = true;
			?><?
			}else{
			?><div class="field"><label for="<?=$value?>"><?=$fieldname?><?if($arResult['SEND_REQ'][$value]==1) echo '<span>*</span>';?>:</label>
			<?if($value!='addfield_'.$arResult['LOC_ID']){?>
			<input id="<?=$value?>" type="text" name="<?=$value?>" value="<?=$arResult['SEND'][$value]?>"/>
			<?}else{?><div class="locationwrap">
			<?
			$GLOBALS["APPLICATION"]->IncludeComponent(
							"bitrix:sale.ajax.locations",
							".default",
							array(
								"AJAX_CALL" => "N",
								"COUNTRY_INPUT_NAME" => "cnt",
								"REGION_INPUT_NAME" => "reg",
								"CITY_INPUT_NAME" => $value,
								"CITY_OUT_LOCATION" => "Y",
								"LOCATION_VALUE" => $arResult['SEND'][$value],
								"ORDER_PROPS_ID" => str_replace('addfield_','',$value),
								"ONCITYCHANGE" => "submitForm()",
								"SIZE1" => "",
							),
							null,
							array('HIDE_ICONS' => 'Y')
						);
			?></div>
			<?}?>
			</div>
			<?
			}
		?>
		<?
		}
	}
	?>
	<?if(is_array($arParams["FIELD_DELIVERY"]) && count($arParams["FIELD_DELIVERY"])==1){
		?><input type="hidden" name="delivery" value="<?foreach($arParams["FIELD_DELIVERY"] as $key=>$val){ echo $val; break;}?>"><?
	}elseif(is_array($arParams["FIELD_DELIVERY"]) && count($arParams["FIELD_DELIVERY"])>1){
		?><div class="field"><label for="delivery"><?=GetMessage("CAT_BK_DELIVERY")?></label>
		<div class="locationwrap"><select id="delivery" name="delivery">
			<?
			$i=0;
			foreach($arParams["FIELD_DELIVERY"] as $key=>$val){
			$i++;
				?><option value="<?=$val?>"<?if(($i==1 && $arResult['SEND']['delivery']=="") || $arResult['SEND']['delivery']!="") { echo ' selected';}?>><?=$arResult['DELIVERY_NAME'][$val]?></option><?
			}?>
		</select></div>
		</div><?
	}?>
	<?if(is_array($arParams["FIELD_PAYSYSTEM"]) && count($arParams["FIELD_PAYSYSTEM"])==1){
		?><input type="hidden" name="paysystem" value="<?foreach($arParams["FIELD_PAYSYSTEM"] as $key=>$val){ echo $val; break;}?>"><?
	}elseif(is_array($arParams["FIELD_PAYSYSTEM"]) && count($arParams["FIELD_PAYSYSTEM"])>1){
		?><div class="field"><label for="paysystem"><?=GetMessage("CAT_BK_PAYSYSTEM")?></label>
		<div class="locationwrap"><select id="paysystem" name="paysystem">
			<?
			$i=0;
			foreach($arParams["FIELD_PAYSYSTEM"] as $key=>$val){
			$i++;
				?><option value="<?=$val?>"<?if(($i==1 && $arResult['SEND']['paysystem']=="") || $arResult['SEND']['paysystem']!="") { echo ' selected';}?>><?=$arResult['PAYSYSTEM_NAME'][$val]?></option><?
			}?>
		</select></div>
		</div><?
	}?>
	<?if($messfield){?>
	<div class="field"><label for="mess"><?=GetMessage("CAT_BK_FIELD_REQ_".strtoupper('mess'))?><?if($arResult['SEND_REQ']['mess']==1) echo '<span>*</span>';?>:</label>
		<textarea id="mess" name="mess"><?=$arResult['SEND']['mess']?></textarea>
	</div>
	<?}?>
	<?if($arResult['SHOW_CAPTCHA']==1){?>
	<?$capCode = $GLOBALS["APPLICATION"]->CaptchaGetCode();?>
	<div class="mlf_capcha">
		<div class="label"><?=GetMessage("CAT_BK_CAPTCHA_LABEL")?><span>*</span>:</div>
		<div class="fieldcp"><img src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialchars($capCode) ?>" width="180" height="40"/><br/>
		<input type="hidden" name="captcha_sid" value="<?= htmlspecialchars($capCode) ?>"/>
		<input size="40" value="" name="cap" />
		</div>
	</div>
	<?}?>



</div>

<?
echo bitrix_sessid_post('bistrclick_sessid');
	?>
	<input type="hidden" name="name_bk" value="1"/>
	<input type="submit" class="mlfsubmitbk" value="<?=GetMessage('CAT_BK_SEND_BUTTON')?>"/>
	</form>
	</body>
	</html>
	<?die();
}
elseif($arResult['SHOW_FORM']==1 and $arResult['CHECK_SPAM']!=1 and $arResult['SENDFORM']) {
$APPLICATION->RestartBuffer();
$APPLICATION->SetAdditionalCSS($templateFolder."/styled.css");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
<?echo '<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'"'.(true ? ' /':'').'>'."\n";
$APPLICATION->ShowCSS(true, true);
?>
</head>
<body>
	<div class="sendmessages"><?=$arParams['MESS_OK']?></div>
</body>
</html>
<?
die();
}
else if($arResult['SHOW_FORM']==0 && $arResult['CHECK_SPAM']!=1){

	if($arParams['JQUERY']=="Y") {
	$APPLICATION->AddHeadScript($templateFolder."/js/jquery.1.9.js");
	}
	if($arParams['FANCY']=="Y") {
	$APPLICATION->SetAdditionalCSS($templateFolder."/fancybox/jquery.fancybox-1.3.4.css");
	$APPLICATION->SetAdditionalCSS($templateFolder."/fancybox/jquery.fancybox.css");
	$APPLICATION->AddHeadScript($templateFolder."/fancybox/lib/jquery.mousewheel-3.0.6.pack.js");
	$APPLICATION->AddHeadScript($templateFolder."/fancybox/jquery.fancybox.js");
	}

	$key = md5(strtotime(date("M-d-Y H:00:00")).$arParams['KEY'].$arResult['REF_START']);
	?>
	<script>
	$(document).ready(function(){
		$(".byclick a").each(function() {
			$(this).attr('href','<?=$arResult['REF_START']?>?formclick=1&referer=<?=$key?>&pr_id='+$(this).attr("data-id"));
		});
	});

		$('.byclick a').fancybox({
		'type': 'iframe',
		'width': 600,
		'height': 250,
		});
	</script>
	<?
}elseif($arResult['CHECK_SPAM']==1 && $arResult['SHOW_FORM']==1) {
$APPLICATION->RestartBuffer();
$APPLICATION->SetAdditionalCSS($templateFolder."/styled.css");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
<?echo '<meta http-equiv="Content-Type" content="text/html; charset='.LANG_CHARSET.'"'.(true ? ' /':'').'>'."\n";
$APPLICATION->ShowCSS(true, true);
?>
</head>
<body>
	<? echo GetMessage('CAT_BK_ERROR_KEY');?>
</body>
</html>
<?
die();
}
?>
