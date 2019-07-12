<?php
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    $sFilterName = $arParams['FILTER_NAME'];
?>
    <div class="filter">
        <form method="get" id="CatalogFilter" action="<?php echo $arResult['CATALOG_LIST_URL'];?>">
            <div class="row">
                <h3><?php echo GetMessage('SMARTREALT_FILTER_TITLE')?></h3>
            </div>
            <div class="row" id="EstateMarketContainer" style="display: <?php echo (in_array($arParams['FILTER']['TypeId'], array(2,4,5,19)) && !$arResult['bEstateMarketHide'])?'':'none'?>;">
                <label class="f" for=""><?php echo GetMessage('SMARTREALT_ESTATE_MARKET')?></label> 
                <?php $bIsSelected = ($arParams['FILTER']['EstateMarket'] == 'PRIMARY'); ?>
                <input type="radio" name="<?php echo $sFilterName?>[EstateMarket]" <?php if ($bIsSelected) { echo 'checked="checked"'; } ?> class="ch" value="PRIMARY" id="PRIMARY">
                <label for="PRIMARY" class="ch new"><?php echo GetMessage('SMARTREALT_YES')?></label>
                <?php $bIsSelected = ($arParams['FILTER']['EstateMarket'] == 'SECONDARY'); ?>
                <input type="radio" name="<?php echo $sFilterName?>[EstateMarket]" <?php if ($bIsSelected) { echo 'checked="checked"'; } ?> class="ch" value="SECONDARY" id="SECONDARY">
                <label for="SECONDARY" class="ch old"><?php echo GetMessage('SMARTREALT_NO')?></label>
                <?php $bIsSelected = ($arParams['FILTER']['EstateMarket'] == ''); ?>
                <input type="radio" name="<?php echo $sFilterName?>[EstateMarket]" <?php if ($bIsSelected) { echo 'checked="checked"'; } ?> class="ch" value="" id="ALL">
                <label for="ALL" class="ch"><?php echo GetMessage('SMARTREALT_NO_VALUE')?></label>
            </div> 
            <!--<div class="row">
                <label class="f" for="">Материал</label>
                <select name="HouseTypeId" id="">
                    <?php foreach ($arResult['arHouseTypes'] as $iTypeId=>$sTypeName) { ?>
                        <?php $bIsSelected = ($arParams['FILTER']['HouseTypeId'] == $iTypeId); ?>
                        <option value="<?php echo $iTypeId;?>" <?php if ($bIsSelected) { echo 'selected="selected"'; } ?>><?php echo $sTypeName;?></option>
                    <?php } ?>
                </select>
            </div>-->
            <div class="row" id="RoomQuantityContainer" style="display: <?php echo in_array($arParams['FILTER']['TypeId'], array(2,4,5,19))?'':'none'?>;">
                <label class="f" for=""><?php echo GetMessage('SMARTREALT_ROOMS')?></label>
                <div>
                    <?php foreach ($arResult['arRoomQuantity'] as $i=>$s) { ?>
                    <input class="ch" <?php if (in_array($i, $arParams['FILTER']['RoomQuantity'])) { echo 'checked="checked"'; } ?> value="<?php echo $i;?>" name="<?php echo $sFilterName?>[RoomQuantity][]" type="checkbox">
                    <label class="ch" for=""><?php echo $s;?></label>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <label for="" class="f"><?php echo GetMessage('SMARTREALT_PRICE')?> <span class="r"><?php echo GetMessage('SMARTREALT_FROM')?></span></label>
                <div>
                    <input name="<?php echo $sFilterName?>[>=Price]" class="txt" value="<?php echo htmlspecialchars($arParams['FILTER']['>=Price']);?>" type="text">
                    <?php echo GetMessage('SMARTREALT_TO')?>
                    <input name="<?php echo $sFilterName?>[<=Price]" class="txt" value="<?php echo htmlspecialchars($arParams['FILTER']['<=Price']);?>" type="text">
                    <?php echo GetMessage('SMARTREALT_CURRENCY')?>
                </div>
            </div>
            <div class="row">
                <label for="" class="f"><?php echo GetMessage('SMARTREALT_AREA')?> <span class="r"><?php echo GetMessage('SMARTREALT_FROM')?></span></label>
                <div>
                    <input name="<?php echo $sFilterName?>[>=GeneralArea]" class="txt" value="<?php echo htmlspecialchars($arParams['FILTER']['>=GeneralArea']);?>" type="text">
                    <?php echo GetMessage('SMARTREALT_TO')?>
                    <input name="<?php echo $sFilterName?>[<=GeneralArea]" class="txt" value="<?php echo htmlspecialchars($arParams['FILTER']['<=GeneralArea']);?>" type="text">
                    <?php echo GetMessage('SMARTREALT_M2')?>
                </div>
            </div>
            <div class="row">
                <label for="" class="f"><?php echo GetMessage('SMARTREALT_LOCATION')?></label>
                <div>
                    <?php $bIsSelected = ($arParams['FILTER']['LocationType'] == 'City'); ?>
                    <input name="<?php echo $sFilterName?>[LocationType]" value="City" id="LocationTypeCity" <?php if ($bIsSelected) { echo 'checked="checked"'; } ?> class="ch" type="radio">
                    <label for="LocationTypeCity" class="ch"><?php echo GetMessage('SMARTREALT_IN_CITY')?></label>
                    <?php $bIsSelected = ($arParams['FILTER']['LocationType'] == 'RegionArea'); ?>
                    <input name="<?php echo $sFilterName?>[LocationType]" value="RegionArea" id="LocationTypeRegionArea" <?php if ($bIsSelected) { echo 'checked="checked"'; } ?> class="ch" type="radio">
                    <label class="ch" for="LocationTypeRegionArea"><?php echo GetMessage('SMARTREALT_OUT_CITY')?></label><br>                                                                                        
                    <select class="medium" name="<?php echo $sFilterName?>[CityId]" id="CityId" style="display: <?php echo $arParams['FILTER']['LocationType']!='City'?'none':'';?>;">
                        <option class="label" value=""><?php echo GetMessage('SMARTREALT_CITY')?></option>
                        <?php foreach ($arResult['arCities'] as $iCityId=>$sCity) { ?>
                            <?php $bIsSelected = ($arParams['FILTER']['CityId'] == $iCityId); ?>
                            <option value="<?php echo $iCityId;?>" <?php if ($bIsSelected) { echo 'selected="selected"'; } ?>><?php echo $sCity;?></option>
                        <?php } ?>
                    </select>
                    <select class="medium" name="<?php echo $sFilterName?>[RegionAreaId]" id="RegionAreaId" style="display: <?php echo $arParams['FILTER']['LocationType']!='RegionArea'?'none':'';?>;">
                        <option class="label" value=""><?php echo GetMessage('SMARTREALT_REGION_AREA')?></option>
                        <?php foreach ($arResult['arRegionAreas'] as $iRegionAreaId=>$sRegionArea) { ?>
                            <?php $bIsSelected = ($arParams['FILTER']['RegionAreaId'] == $iRegionAreaId); ?>
                            <option value="<?php echo $iRegionAreaId;?>" <?php if ($bIsSelected) { echo 'selected="selected"'; } ?>><?php echo $sRegionArea;?></option>
                        <?php } ?>
                    </select>
                    <?php $bShowTown = $arParams['FILTER']['LocationType'] == 'City' && count($arResult['arTownByCity'][$arParams['FILTER']['CityId']]) > 0 && strlen($arParams['FILTER']['CityId']) > 0;?>
                    <?php $bShowTown = $bShowTown || ($arParams['FILTER']['LocationType'] == 'RegionArea' && count($arResult['arTownByRegionArea'][$arParams['FILTER']['RegionAreaId']]) > 0) && strlen($arParams['FILTER']['RegionAreaId']) > 0;?>
                    <select class="medium" name="<?php echo $sFilterName?>[TownId]" id="TownId" style="display: <?php echo !$bShowTown?'none':'';?>;">
                        <option class="label" value=""><?php echo GetMessage('SMARTREALT_TOWN')?></option>
                        <?php if (strlen($arParams['FILTER']['CityId']) > 0) { ?>
                            <?php foreach ($arResult['arTownByCity'][$arParams['FILTER']['CityId']] as $iTownId=>$sTown) { ?>
                                <?php $bIsSelected = ($arParams['FILTER']['TownId'] == $iTownId); ?>
                                <option value="<?php echo $iTownId;?>" <?php if ($bIsSelected) { echo 'selected="selected"'; } ?>><?php echo $sTown;?></option>
                            <?php } ?>
                        <?php } else if (strlen($arParams['FILTER']['RegionAreaId']) > 0) { ?>
                            <?php foreach ($arResult['arTownByRegionArea'][$arParams['FILTER']['RegionAreaId']] as $iTownId=>$sTown) { ?>
                                <?php $bIsSelected = ($arParams['FILTER']['TownId'] == $iTownId); ?>
                                <option value="<?php echo $iTownId;?>" <?php if ($bIsSelected) { echo 'selected="selected"'; } ?>><?php echo $sTown;?></option>
                            <?php } ?> 
                        <?php } ?> 
                    </select>
                </div>
            </div>
            <div class="row" id="CityAreaContainer" style="display: <?php echo $arResult['bCityAreaHide']?'none':'';?>;">
                <label class="f" for=""><?php echo GetMessage('SMARTREALT_CITY_AREA')?></label>
                <div id="CityAreaIdContainer">
                    <?php if (strlen($arParams['FILTER']['CityId']) > 0) { ?>
                        <?php foreach ($arResult['arCityAreas'][$arParams['FILTER']['CityId']] as $iCityAreaId=>$sCityArea) { ?>
                            <?php $bIsSelected = in_array($iCityAreaId, $arParams['FILTER']['CityAreaId']); ?>
                            <input class="ch" id="<?php echo $iCityAreaId;?>" value="<?php echo $iCityAreaId;?>" <?php if ($bIsSelected) { echo 'checked="checked"'; } ?> name="<?php echo $sFilterName?>[CityAreaId][]" type="checkbox">
                            <label class="ch" for="<?php echo $iCityAreaId;?>"><?php echo $sCityArea;?></label><br>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <?php if ($arResult['bSetFilter']) { ?>
            <a class="reset" href="javascript: void(0);" onclick="oSmartRealtFilter.ClearFilter(true)"><?php echo GetMessage('SMARTREALT_CLEAR')?></a>
            <?php } ?>
            <input class="btn theme-bgcolor" type="submit" value="<?php echo GetMessage('SMARTREALT_SEARCH')?>">
        </form>
    </div>  
    <script type="text/javascript">
     $(function(){
        oSmartRealtFilter = new SmartRealtFilter();
        oSmartRealtFilter.Init();
        oSmartRealtFilter.SetTypeId('<?php  echo htmlspecialchars($arParams['FILTER']['TypeId']);?>');
        oSmartRealtFilter.SetDefTownId('<?php  echo htmlspecialchars($arParams['FILTER']['TownId']);?>');
        oSmartRealtFilter.SetDefRegionAreaId('<?php  echo htmlspecialchars($arParams['FILTER']['RegionAreaId']);?>');
        oSmartRealtFilter.SetDefCityId('<?php echo htmlspecialchars($arParams['FILTER']['CityId']);?>');
        oSmartRealtFilter.SetTownByCity(<?=$arResult['jsTownByCity']?>);
        oSmartRealtFilter.SetTownByRegionArea(<?=$arResult['jsTownByRegionArea']?>);
        oSmartRealtFilter.SetCityAreaByCity(<?=$arResult['jsCityAreas']?>);
        oSmartRealtFilter.SetFilterName('<?= htmlspecialchars($arParams['FILTER_NAME'])?>');  
        oSmartRealtFilter.SetFilterFormName('<?php echo 'CatalogFilter'?>');
     });                                           
</script>