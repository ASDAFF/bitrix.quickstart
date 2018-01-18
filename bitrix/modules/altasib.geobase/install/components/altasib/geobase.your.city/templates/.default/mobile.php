<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=1">
/**
 * @var $arResult array
 * @var $arParams array
 * @var $APPLICATION CMain
 * @var $USER CUser
 * @var $component CBitrixComponent
  * @var $this CBitrixComponentTemplate
 */
$this->setFrameMode(true);
$frame = $this->createFrame()->begin("");

$shortName = ""; $fullName = "";
$shortName = (!empty($arResult["CITY"]["NAME"]) ? $arResult["CITY"]["NAME"] : $arResult["auto"]["CITY_NAME"]);
if(!empty($arResult["CITY"]["NAME"]))
	$fullName = $arResult["CITY"]["SOCR"].'. '.$arResult["CITY"]["NAME"].', '.$arResult["REGION"]["FULL_NAME"]
		.(!empty($arResult['DISTRICT']['SOCR']) ? ', '.$arResult['DISTRICT']['NAME'].' '.$arResult['DISTRICT']['SOCR'].'.' : '');
else
	$fullName = (!empty($arResult["CITY"]["NAME"]) ? $arResult["CITY"]["NAME"] : $arResult["auto"]["CITY_NAME"])
		.(!empty($arResult['auto']['REGION_NAME']) ? ', '.$arResult['auto']['REGION_NAME'] : '')
		.(!empty($arResult['auto']['COUNTRY_NAME']) ? ' ('.$arResult['auto']['COUNTRY_NAME'].')' : '');
?>
<script language="JavaScript">
if(typeof altasib_geobase == "undefined") var altasib_geobase = new Object();
altasib_geobase.short_name = "<?=$shortName;?>";
altasib_geobase.full_name = "<?=$fullName;?>";
altasib_geobase.is_mobile = true;
</script>
<div id="altasib_geobase_mb_window">
	<div id="altasib_geobase_mb_window_block">
		<a href="#" onclick="altasib_geobase.yc_x_clc(); return false" title="<?=GetMessage("ALTASIB_GEOBASE_CLOSE");?>">
			<div id="altasib_geobase_mb_close_kr"></div>
		</a>
		<div id="altasib_geobase_mb_page">
			<div class="altasib_geobase_yc_mb_ttl"><?=GetMessage("ALTASIB_GEOBASE_YOUR_CITY");?></div>
			<?if($arResult["CODE"] == 00000000000 && empty($arResult["auto"]["CITY_NAME"])){?>
				<div class="altasib_geobase_mb_your_city_block">
					<div class="altasib_geobase_mb_your_city"><?=GetMessage("ALTASIB_GEOBASE_NOT_CITY");?></div>
				</div>
			<a class="altasib_geobase_yc_mb_btn altasib_geobase_yc_mb_disabled" style="margin: 10px 28%;" onclick="altasib_geobase.yc_no_click(); return false;" href="#"><?=GetMessage("ALTASIB_GEOBASE_SELECT");?></a><?
			}
			else {?>
			<div class="altasib_geobase_mb_your_city_block">
				<?if(isset($arResult["CITY"]["NAME"])){?>
					<span class="altasib_geobase_mb_your_city"><?=$arResult["CITY"]["NAME"];?></span>
					<?if($arResult["REGION_DISABLE"] != "Y"){?>
						<span class="altasib_geobase_mb_your_city_2"><?=' ('.$arResult["REGION"]["FULL_NAME"]
						.((isset($arResult["DISTRICT"]["NAME"]) && $arResult["DISTRICT"]["NAME"] != '') ? ', '.$arResult['DISTRICT']['NAME'].' '.$arResult['DISTRICT']['SOCR'].'.' : '').')';?></span>
					<?}?>
				<?} else if(isset($arResult["auto"]["CITY_NAME"])){?>
				<span class="altasib_geobase_mb_your_city"><?=$arResult["auto"]["CITY_NAME"];?></span>
					<?if($arResult["REGION_DISABLE"] != "Y" && !empty($arResult["auto"]["REGION_NAME"])){?>
						<span class="altasib_geobase_mb_your_city_2"><?=' ('.$arResult["auto"]["REGION_NAME"].')';?></span>
					<?}?>
					<?if(!empty($arResult["auto"]["COUNTRY_NAME"])){?>
						<span class="altasib_geobase_mb_your_city_2"><?=', '.$arResult["auto"]["COUNTRY_NAME"];?></span>
					<?}?>
				<?}?>
			</div>
			<a class="altasib_geobase_yc_mb_btn altasib_geobase_yc_mb_disabled" onclick="altasib_geobase.yc_yes_click('<?=$arResult["CODE"]?>'); return false;" href="#"><?=GetMessage("ALTASIB_GEOBASE_YES");?></a>
			<a class="altasib_geobase_yc_mb_btn altasib_geobase_yc_mb_disabled" onclick="altasib_geobase.yc_no_click(); return false;" href="#"><?=GetMessage("ALTASIB_GEOBASE_NO");?></a><?
			}
		
		?></div>
	</div>
</div>
<?if($arResult["POPUP_BACK"] != 'N'){
	?><div id="altasib_geobase_yc_mb_backg"></div>
<?}
$frame->end(); ?>