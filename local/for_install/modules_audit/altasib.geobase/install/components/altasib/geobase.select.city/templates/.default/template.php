<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

if($_REQUEST["AUTOLOAD"] != 'Y'):
?><span class="altasib_geobase_link"><?
	?><span class="altasib_geobase_link_prefix"><?
		if(isset($arParams["SPAN_LEFT"])){
			if(!empty($arParams["SPAN_LEFT"]) && trim($arParams["SPAN_LEFT"] != ''))
				echo $arParams["SPAN_LEFT"]."&nbsp;";
		}
		else
			echo GetMessage("ALTASIB_GEOBASE_MY_CITY")."&nbsp;";
	?></span><?
	?><span class="altasib_geobase_link_city" onclick="altasib_geobase.sc_open();" title="<?=$userChoiceTitle;?>"><?
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
	var altasib_geobase = new Object;
altasib_geobase.codes = jQuery.parseJSON('<?=json_encode($arResult['SEL_CODES']);?>');
altasib_geobase.is_mobile = false;
</script>
<div id="altasib_geobase_win">
	<div class="altasib_geobase_city">
		<div id="altasib_geobase_popup">
			<div id="altasib_geobase_close">
				<a href="#" title="<?=GetMessage("ALTASIB_GEOBASE_CLOSE");?>" onclick="altasib_geobase.sc_cls(); return false;"></a>
			</div>
			<div class="altasib_geobase_pu_i altasib_geobase_cutting">
				<div class="altasib_geobase_ttl"><?echo ($arResult['RU_ENABLE'] == "Y" ? GetMessage("ALTASIB_GEOBASE_SELECT_CITY") : GetMessage("ALTASIB_GEOBASE_YOUR_CITY")).":";?></div>				
				<div class="altasib_geobase_cities">
					<ul class="<?echo (IsIE() ? 'altasib_geobase_list_ie' : 'altasib_geobase_fst');?>">
						<?$iLi = 0;
						if(isset($arResult['USER_CHOICE']['CODE'])){
						?><li class="altasib_geobase_act">
							<a href="#" title="<?=$userChoiceTitle;?>" id="altasib_geobase_list_<?=$arResult['USER_CHOICE']["CODE"];?>" onclick="altasib_geobase.sc_onclk('<?=$arResult['USER_CHOICE']["CODE"];?>'); return false;"><?
								if(isset($arResult['USER_CHOICE']["CITY"]["NAME"]))
									echo ($city = $arResult['USER_CHOICE']["CITY"]["NAME"]);
							$iLi++;
							?></a>
						</li><?
						}else if(!empty($arResult['USER_CHOICE']['CITY'])){ // C_CODE
							$cityID = (!empty($arResult['USER_CHOICE']["CODE"]) ? $arResult['USER_CHOICE']["CODE"] :
								(!empty($arResult['USER_CHOICE']["C_CODE"]) ? $arResult['USER_CHOICE']["C_CODE"] : $arResult['USER_CHOICE']["CITY"]));
						?><li class="altasib_geobase_act">
							<a href="#" title="<?=$userChoiceTitle;?>" id="altasib_geobase_list_<?echo str_replace(' ','_', $cityID);?>" onclick="altasib_geobase.sc_onclk('<?=$cityID;?>',  '<?=$arResult["USER_CHOICE"]["COUNTRY_CODE"];?>'); return false;"><?
								echo $userSelCity;
								$iLi++;
							?></a>
						</li><?
						} else if(!empty($arResult['USER_CHOICE']['CITY_NAME'])){
							$cityID = (!empty($arResult['USER_CHOICE']["CODE"]) ? $arResult['USER_CHOICE']["CODE"] : $arResult['USER_CHOICE']["CITY_NAME"]);
						?><li class="altasib_geobase_act">
							<a href="#" title="<?=$userChoiceTitle;?>" id="altasib_geobase_list_<?echo str_replace(' ','_', $cityID);?>" onclick="altasib_geobase.sc_onclk('<?=$cityID;?>',  '<?=$arResult["USER_CHOICE"]["COUNTRY_CODE"];?>'); return false;"><?
								echo $userSelCity;
								$iLi++;
							?></a>
						</li><?
						}
						?>
						<?if(isset($arResult['AUTODETECT']['CODE']) && $arResult['AUTODETECT']["CITY"]["NAME"]!=$city){
						?><li class="altasib_geobase_auto"><?
							?><a href="#" title="<?echo $arResult['AUTODETECT']["CITY"]["SOCR"].'. '.$arResult['AUTODETECT']["CITY"]["NAME"].', '.$arResult['AUTODETECT']["REGION"]["FULL_NAME"]
							.(!empty($arResult['AUTODETECT']['DISTRICT']['SOCR']) ? ', '.$arResult['AUTODETECT']['DISTRICT']['NAME'].' '.$arResult['AUTODETECT']['DISTRICT']['SOCR'].'.' : '');?>" id="altasib_geobase_list_<?=$arResult['AUTODETECT']["CODE"];?>" onclick="altasib_geobase.sc_onclk('<?=$arResult['AUTODETECT']["CODE"];?>'); return false;"><?
								if($arResult['AUTODETECT']["CITY"]["NAME"]) echo $arResult['AUTODETECT']["CITY"]["NAME"];
							$iLi++;
							?></a>
						</li><?
						}else if(!empty($arResult['auto']['CITY_NAME']) && $arResult['auto']["CITY_NAME"]!= $city
							&& $arResult['auto']["CITY_NAME"] != $userSelCity && $arResult['auto']["CITY_NAME"] != $arResult['USER_CHOICE']["CITY"]){
							$cityID = (!empty($arResult['auto']["CODE"]) ? $arResult['auto']["CODE"] : $arResult['auto']["CITY_NAME"]);
						?><li class="altasib_geobase_auto"><?
							?><a href="#" title="<?echo $arResult['auto']["CITY_NAME"]
							.(!empty($arResult['auto']['REGION_NAME']) ? ', '.$arResult['auto']['REGION_NAME'] : '')
							.(!empty($arResult['auto']['COUNTRY_NAME']) ? ' ('.$arResult['auto']['COUNTRY_NAME'].')' : '');?>" id="altasib_geobase_list_<?echo str_replace(' ','_', $cityID);?>" onclick="altasib_geobase.sc_onclk('<?=$cityID;?>',  '<?=$arResult["auto"]["COUNTRY_CODE"];?>'); return false;"><?
								echo $arResult['auto']["CITY_NAME"];
							$iLi++;
							?></a>
						</li><?
						}?>
						<?if(IsIE()){ // if IE
							for($i=0, $icnt = count($arResult["SELECTED"]); $i<$icnt; $i++){
								$slct = $arResult["SELECTED"][$i];
								$iLi++;
						?><li <?echo($arResult["CODE"] == $slct["C_CODE"] ? 'class="altasib_geobase_act"' : '')?>>
							<a id="altasib_geobase_list_<?=$slct["C_CODE"]?>" onclick="altasib_geobase.sc_onclk('<?=$slct["C_CODE"]?>'); return false;" title="<?
							echo (!empty($slct["C_SOCR"]) ? $slct["C_SOCR"].'. ' : '').$slct["C_NAME"].(!empty($slct["R_FNAME"]) ? ', '.$slct["R_FNAME"] : '')
							.(isset($slct['D_NAME']) ? ', '.$slct['D_NAME'].' '.$slct['D_SOCR'].'.' : '')
							.(!empty($slct["CTR_NAME_RU"]) ? ', '.$slct["CTR_NAME_RU"] : '');?>" href="#"><?=$slct["C_NAME"];?></a>
						</li><?
								if($iLi == 6){
					?></ul>
					<ul class="<?echo (IsIE() ? 'altasib_geobase_list_ie' : 'altasib_geobase_fst');?>"><?
									$iLi = 0;
								}
							}
						}
						else{ // regular browser (not IE)
							foreach($arResult["SELECTED"] as $sel){
						?><li <?echo($arResult["CODE"] == $sel["C_CODE"] ? 'class="altasib_geobase_act"' : '')?>>
							<a id="altasib_geobase_list_<?=$sel["C_CODE"]?>" onclick="altasib_geobase.sc_onclk('<?=$sel["C_CODE"]?>'); return false;" title="<?
							echo (!empty($sel["C_SOCR"]) ? $sel["C_SOCR"].'. ' : '').$sel["C_NAME"].(!empty($sel["R_FNAME"]) ? ', '.$sel["R_FNAME"] : '')
							.(isset($sel['D_NAME']) ? ', '.$sel['D_NAME'].' '.$sel['D_SOCR'].'.' : '')
							.(!empty($sel["CTR_NAME_RU"]) ? ', '.$sel["CTR_NAME_RU"] : '');?>" href="#"><?=$sel["C_NAME"];?></a>
						</li><?
							}
						}?>
					</ul>
					<div class="altasib_geobase_clear"></div>
				</div><?
				if($arResult['ONLY_SELECT'] != "Y"):?>
				<div class="altasib_geobase_title2"><?=GetMessage("ALTASIB_GEOBASE_ENTER_FIELD");?></div>
				<a id="altasib_geobase_btn" class="altasib_geobase_disabled" onclick="altasib_geobase.sc_onclk(); return false;" href="#"><?=GetMessage("ALTASIB_GEOBASE_THIS_IS_MY_CITY");?></a>
				
				<div class="altasib_geobase_find">
					<input id="altasib_geobase_search" name="altasib_geobase_search" type="text" placeholder="<?=GetMessage('ALTASIB_GEOBASE_ENTER');?>" autocomplete="off" onkeyup='altasib_geobase.sc_inpKey(event);' onkeydown='altasib_geobase.sc_inpKeyDwn(event);'>
					<br/>
					<div id="altasib_geobase_info" onclick="altasib_geobase.sc_add_city(event); return false;" onkeyup='altasib_geobase.sc_selKey(event);' ondblclick="altasib_geobase.sc_onclk();">
					</div>
				</div><?
				endif;?>
			</div>
		</div>
	</div>
</div>
<?if($arResult["POPUP_BACK"] != 'N'){
	?><div id="altasib_geobase_popup_back"></div>
<?}
$frame->end(); ?>