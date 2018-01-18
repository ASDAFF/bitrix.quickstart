<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=1">
/**
 * @var $arResult array
 * @var $arParams array
 * @var $APPLICATION CMain
 * @var $USER CUser
 * @var $component CBitrixComponent
 * @var $this CBitrixComponentTemplate
 * @var $city
 */
$this->setFrameMode(true);
$frame = $this->createFrame()->begin("");

$userChoiceTitle = "";
$userSelCity = "";
if(!empty($arResult['USER_CHOICE']) && is_array($arResult['USER_CHOICE'])){
	if(is_array($arResult['USER_CHOICE']["CITY"]) && !empty($arResult['USER_CHOICE']["CITY"]["SOCR"])){
		$userChoiceTitle = $arResult['USER_CHOICE']["CITY"]["SOCR"].'. '.$arResult['USER_CHOICE']["CITY"]["NAME"].', '.$arResult['USER_CHOICE']["REGION"]["FULL_NAME"]
		.(!empty($arResult['USER_CHOICE']['DISTRICT']['SOCR']) ? ', '.$arResult['USER_CHOICE']['DISTRICT']['NAME'].' '.$arResult['USER_CHOICE']['DISTRICT']['SOCR'].'.' : '');
		$userSelCity = (!empty($arResult['USER_CHOICE']["CITY"]["NAME"]) ? $arResult['USER_CHOICE']["CITY"]["NAME"] : '');
	}
	elseif(!empty($arResult['USER_CHOICE']["COUNTRY_RU"]) || !empty($arResult['USER_CHOICE']["CITY_RU"])){
		$userChoiceTitle = (($arResult['RU_ENABLE'] == "Y") ? $userSelCity = $arResult['USER_CHOICE']["CITY_RU"] :
			(!empty($arResult['USER_CHOICE']["CITY"]) ? $userSelCity = $arResult['USER_CHOICE']["CITY"] : ''))
			.(!empty($arResult['USER_CHOICE']['REGION']) ? ', '.$arResult['USER_CHOICE']['REGION'] : '')
			.((!empty($arResult['USER_CHOICE']["COUNTRY_RU"]) && $arResult['RU_ENABLE'] == "Y") ?
			' ('.$arResult['USER_CHOICE']["COUNTRY_RU"].')' : (!empty($arResult['USER_CHOICE']["COUNTRY"]) ? ' ('.$arResult['USER_CHOICE']["COUNTRY"].')' : ''));
	}
	elseif(!empty($arResult['USER_CHOICE']["CITY_NAME"])) {
		$userChoiceTitle = ($userSelCity = $arResult['USER_CHOICE']["CITY_NAME"])
			.(!empty($arResult['USER_CHOICE']['REGION_NAME']) ? ', '.$arResult['USER_CHOICE']['REGION_NAME'] : '')
			.(!empty($arResult['USER_CHOICE']['COUNTRY_NAME']) ? ' ('.$arResult['USER_CHOICE']['COUNTRY_NAME'].')' : '');
	}
}

if($_REQUEST["AUTOLOAD"] != 'Y'):?>
<span class="altasib_geobase_mb_link">
	<span class="altasib_geobase_mb_link_prefix"><?
		if(isset($arParams["SPAN_LEFT"])){
			if(!empty($arParams["SPAN_LEFT"]) && trim($arParams["SPAN_LEFT"] != ''))
				echo $arParams["SPAN_LEFT"]."&nbsp;";
		}
		else
			echo GetMessage("ALTASIB_GEOBASE_MY_CITY")."&nbsp;";
	?></span><?
	?><span class="altasib_geobase_mb_link_city" onclick="altasib_geobase.sc_open();" title="<?=$userChoiceTitle;?>"><?
		if(!empty($userSelCity))
			echo $userSelCity;
		elseif($arParams["RIGHT_ENABLE"] == "Y" && $arResult['AUTODETECT_ENABLE'] == "Y"
			&& (is_array($arResult['AUTODETECT']) || is_array($arResult['auto'])))
		{
			if(is_array($arResult['AUTODETECT']) && isset($arResult['AUTODETECT']["CITY"]["NAME"]))
				echo $arResult['AUTODETECT']["CITY"]["NAME"];
			elseif(is_array($arResult['auto']) && isset($arResult['auto']["CITY_NAME"]))
				echo $arResult['auto']["CITY_NAME"];
		}
		elseif(isset($arParams["SPAN_RIGHT"]))
			echo $arParams["SPAN_RIGHT"];
		else
			echo GetMessage("ALTASIB_GEOBASE_SELECT_CITY");
	?></span>
</span>
<?endif;?>
<script language="JavaScript">
if(typeof altasib_geobase == "undefined")
	var altasib_geobase = {};
altasib_geobase.codes = jQuery.parseJSON('<?=json_encode($arResult['SEL_CODES']);?>');
altasib_geobase.is_mobile = true;
</script>
<div id="altasib_geobase_mb_win">
	<div class="altasib_geobase_mb_city">
		<div id="altasib_geobase_mb_popup">
			<div id="altasib_geobase_mb_header">
				<div id="altasib_geobase_mb_close">
					<a href="#" title="<?=GetMessage("ALTASIB_GEOBASE_CLOSE");?>" onclick="altasib_geobase.sc_mb_cls(); return false;"></a>
				</div>
				<div class="altasib_geobase_mb_ttl"><?=GetMessage("ALTASIB_GEOBASE_SELECT_CITY").":"?></div>	
			</div>
			<div class="altasib_geobase_mb_pu_i altasib_geobase_mb_cutting">
				<?if($arResult['ONLY_SELECT'] != "Y"):?>										
					<div id="altasib_geobase_mb_find" class="altasib_geobase_mb_find">
						<input id="altasib_geobase_mb_search" name="altasib_geobase_mb_search" type="text" placeholder="<?=GetMessage('ALTASIB_GEOBASE_ENTER');?>" autocomplete="off" onkeyup='altasib_geobase.sc_inpKey(event);' onkeydown='altasib_geobase.sc_inpKeyDwn(event);' onfocus='altasib_geobase.city_field_focus();' onblur='altasib_geobase.city_field_blur();' onclick='altasib_geobase.clear_field();'>
						<br/>
						<div id="altasib_geobase_mb_info" onclick="altasib_geobase.sc_add_city(event); return false;" onkeyup='altasib_geobase.sc_selKey(event);' ondblclick="altasib_geobase.sc_onclk();">
						</div>
					</div>
					<a id="altasib_geobase_mb_btn" class="altasib_geobase_mb_disabled" onclick="altasib_geobase.sc_onclk(); return false;" href="#"><?=GetMessage("ALTASIB_GEOBASE_THIS_IS_MY_CITY");?></a><?
				endif;?><?
				?><div id="altasib_geobase_mb_cities" class="altasib_geobase_mb_cities">
					<ul class="<?echo (IsIE() ? 'altasib_geobase_mb_list_ie' : 'altasib_geobase_mb_fst');?>">
						<?$iLi = 0;
						if(isset($arResult['USER_CHOICE']['CODE'])){
						?><li class="altasib_geobase_mb_act">
							<a href="#" title="<?=$userChoiceTitle;?>" id="altasib_geobase_mb_list_<?=$arResult['USER_CHOICE']["CODE"];?>" onclick="altasib_geobase.sc_onclk('<?=$arResult['USER_CHOICE']["CODE"];?>'); return false;"><?
								if(isset($arResult['USER_CHOICE']["CITY"]["NAME"]))
									echo ($city = $arResult['USER_CHOICE']["CITY"]["NAME"]);
							$iLi++;
							?></a>
						</li><?
						}else if(!empty($arResult['USER_CHOICE']['CITY'])){
							$cityID = (!empty($arResult['USER_CHOICE']["CODE"]) ? $arResult['USER_CHOICE']["CODE"] : $arResult['USER_CHOICE']["CITY"]);
						?><li class="altasib_geobase_mb_act">
							<a href="#" title="<?=$userChoiceTitle;?>" id="altasib_geobase_mb_list_<?echo str_replace(' ','_', $cityID);?>" onclick="altasib_geobase.sc_onclk('<?=$cityID;?>',  '<?=$arResult["USER_CHOICE"]["COUNTRY_CODE"];?>'); return false;"><?
								echo $userSelCity;
								$iLi++;
							?></a>
						</li><?
						} else if(!empty($arResult['USER_CHOICE']['CITY_NAME'])){
							$cityID = (!empty($arResult['USER_CHOICE']["CODE"]) ? $arResult['USER_CHOICE']["CODE"] : $arResult['USER_CHOICE']["CITY_NAME"]);
						?><li class="altasib_geobase_mb_act">
							<a href="#" title="<?=$userChoiceTitle;?>" id="altasib_geobase_mb_list_<?echo str_replace(' ','_', $cityID);?>" onclick="altasib_geobase.sc_onclk('<?=$cityID;?>',  '<?=$arResult["USER_CHOICE"]["COUNTRY_CODE"];?>'); return false;"><?
								echo $userSelCity;
								$iLi++;
							?></a>
						</li><?
						}
						?>
						
						<?if(isset($arResult['AUTODETECT']['CODE']) && $arResult['AUTODETECT']["CITY"]["NAME"]!=$city){
						?><li class="altasib_geobase_mb_auto">
							<a href="#" title="<?echo $arResult['AUTODETECT']["CITY"]["SOCR"].'. '.$arResult['AUTODETECT']["CITY"]["NAME"].', '.$arResult['AUTODETECT']["REGION"]["FULL_NAME"]
							.(!empty($arResult['AUTODETECT']['DISTRICT']['SOCR']) ? ', '.$arResult['AUTODETECT']['DISTRICT']['NAME'].' '.$arResult['AUTODETECT']['DISTRICT']['SOCR'].'.' : '');?>" id="altasib_geobase_mb_list_<?=$arResult['AUTODETECT']["CODE"];?>" onclick="altasib_geobase.sc_onclk('<?=$arResult['AUTODETECT']["CODE"];?>'); return false;"><?
								if($arResult['AUTODETECT']["CITY"]["NAME"]) echo $arResult['AUTODETECT']["CITY"]["NAME"];
							$iLi++;
							?></a>
						</li><?
						
						}elseif(!empty($arResult['auto']['CITY_NAME']) && $arResult['auto']["CITY_NAME"]!= $city
							&& $arResult['auto']["CITY_NAME"] != $userSelCity && $arResult['auto']["CITY_NAME"] != $arResult['USER_CHOICE']["CITY"]){
							$cityID = (!empty($arResult['auto']["CODE"]) ? $arResult['auto']["CODE"] : $arResult['auto']["CITY_NAME"]);
						?><li class="altasib_geobase_mb_auto">
							<a href="#" title="<?echo $arResult['auto']["CITY_NAME"]
							.(!empty($arResult['auto']['REGION_NAME']) ? ', '.$arResult['auto']['REGION_NAME'] : '')
							.(!empty($arResult['auto']['COUNTRY_NAME']) ? ' ('.$arResult['auto']['COUNTRY_NAME'].')' : '');?>" id="altasib_geobase_mb_list_<?echo str_replace(' ','_', $cityID);?>" onclick="altasib_geobase.sc_onclk('<?=$cityID;?>',  '<?=$arResult["auto"]["COUNTRY_CODE"];?>'); return false;"><?
								if(isset($arResult['auto']["CITY_NAME"])) echo $arResult['auto']["CITY_NAME"];
							$iLi++;
							?></a>
						</li><?
						}?>

						<?if(IsIE()){ // if IE
							for($i=0; $i<count($arResult["SELECTED"]); $i++){
								$slct = $arResult["SELECTED"][$i];
								$iLi++;
						?><li <?echo($arResult["CODE"] == $slct["C_CODE"] ? 'class="altasib_geobase_mb_act"' : '')?>>
							<a id="altasib_geobase_mb_list_<?=$slct["C_CODE"]?>" onclick="altasib_geobase.sc_onclk('<?=$slct["C_CODE"]?>'); return false;" title="<?echo $slct["C_SOCR"].'. '.$slct["C_NAME"].', '.$slct["R_FNAME"].(isset($slct['D_NAME']) ? ', '.$slct['D_NAME'].' '.$slct['D_SOCR'].'.' : '');?>" href="#"><?=$slct["C_NAME"];?></a>
						</li><?
								if($iLi == 6){
					?></ul>
					<ul class="<?echo (IsIE() ? 'altasib_geobase_mb_list_ie' : 'altasib_geobase_mb_fst');?>"><?
									$iLi = 0;
								}
							}
						}
						else{ // regular browser (not IE)
							foreach($arResult["SELECTED"] as $sel){
						?><li <?echo($arResult["CODE"] == $sel["C_CODE"] ? 'class="altasib_geobase_mb_act"' : '')?>>
							<a id="altasib_geobase_mb_list_<?=$sel["C_CODE"]?>" onclick="altasib_geobase.sc_onclk('<?=$sel["C_CODE"]?>'); return false;" title="<?
							echo (!empty($sel["C_SOCR"]) ? $sel["C_SOCR"].'. ' : '').$sel["C_NAME"].(!empty($sel["R_FNAME"]) ? ', '.$sel["R_FNAME"] : '')
							.(isset($sel['D_NAME']) ? ', '.$sel['D_NAME'].' '.$sel['D_SOCR'].'.' : '')
							.(!empty($sel["CTR_NAME_RU"]) ? ', '.$sel["CTR_NAME_RU"] : '');?>" href="#"><?=$sel["C_NAME"];?></a>
						</li><?
							}
						}?>
					</ul>
					<div class="altasib_geobase_mb_clear"></div>
				</div>
				<a id="all_cities_button_mobile" href="#" onClick="altasib_geobase.all_cities();"><?=GetMessage("ALTASIB_GEOBASE_ALL_CITIES");?></a>
			</div>
		</div>
	</div>
</div>
<?php
if($arResult["POPUP_BACK"] != 'N'):
	?><div id="altasib_geobase_mb_popup_back"></div>
<? endif;
$frame->end(); ?>