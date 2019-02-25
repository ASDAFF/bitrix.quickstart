<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<a href="#" class="geoip_city" style="display: inline;"><?=$arResult["CITY"]["CITY_NAME"]?></a>
<div class="rs-fancy" id="rs-location" style="display: none;">
	<a class="close" href="javascript:viod(0);" title="<?=GetMessage("CLOSE")?>"><img src="<?=$templateFolder."/img/close.png"?>" width="30" height="30" /></a>
		<span class="rs-city-header"><?=$arResult["CITY"]["CITY_NAME"]?></span><span class="rs-vopros">?</span>	
		<div class="rs-geocity">
		<?if(($arResult["CITY"]["COUNTRY_CODE"] == 'ru' || $arResult["CITY"]["COUNTRY_CODE"] == 'RU')&&(!empty($arResult["CITY"]["CITY_NAME"]))):?>
			<div class="rs-geocountry-flag"></div>
		<?endif;?>
		
		<div class="rs-geocountry"><span class="rscountry"><?=$arResult["CITY"]["COUNTRY_NAME"]?></span>, <span class="rsregion"><?=$arResult["CITY"]["REGION_NAME"]?></span></div>
		</div>
		<span class="rs-city-header-not"><?global $USER; if ($USER->IsAdmin()){ echo GetMessage("ADMIN_NOT_CITY");}else{ echo GetMessage("NOT_CITY");}?></span>
	<div>
		<input class="txt" style="width: 290px;" type="text" placeholder="<?=GetMessage("WRITE_CITY")?>">
	</div>

	<div class="rs-download" style="display: none;"></div>
	<div class="clear"></div>
	<div class="rs-my-city">
		<input class="button" type="submit" name="submit" value="<?=GetMessage("CITY")?>">
	</div>
</div>
