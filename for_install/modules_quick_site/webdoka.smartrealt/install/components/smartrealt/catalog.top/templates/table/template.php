<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (is_array($arParams['TYPE']) && count($arParams['TYPE']) > 1)
{
    $TypeIdForView = $arParams['TYPE'][0];
    $bManyTypes = true;
}
else
{
    $bManyTypes = false;
    $TypeIdForView = $arParams['TYPE'][0];
} 
?>
    <?php if ($arParams['SHOW_TITLE'] == "Y") { ?>
    <?php if (strlen($arParams['TITLE']) > 0) { ?>
    <h3><?=$arParams['TITLE']?></h3>
    <?php } else { ?>
    <h3><?=$arResult['arItems'][0]['SectionFullName']?></h3>
    <?php } ?>
    <?php } ?>
    <?php if (!$arResult['bNotFound']) { ?> 
    <table cellpadding="0" cellspacing="1" border="0" class="objectsTable">
        <tr>
            <?php foreach ($arResult['arTableFileds'] as $sField=>$arFieldData) { ?>
                <?php if (!isset($arFieldData['arTypeId']) || in_array($TypeIdForView , $arFieldData['arTypeId'])) { ?>
                    <?php /*if (in_array($sField, $arResult['arSortFields'])) { ?>
                    <?php
                        $sOrderClass = '';
                        if ($arParams['SORT_FIELD'] == $sField)
                        {
                            $sOrderClass = strtolower($arParams['SORT_ORDER'])=='asc'?'sort_asc':'sort_desc';
                            $sNewOrder = strtolower($arParams['SORT_ORDER'])=='asc'?'desc':'asc';
                        }
                        else
                        {
                            $sNewOrder = 'asc';
                        }
                        
                        $sSortUrl = $APPLICATION->GetCurPageParam('by='.$sField.'&order='.$sNewOrder, array('order', 'by'))
                    ?>
                        <th class="<?=$arFieldData['sCssClass']?> <?php echo $sOrderClass; ?>"><a href="<?php echo $sSortUrl; ?>" rel="nofollow"><?=$arFieldData['sName']?></a></th>
                    <?php } else { */?> 
                        <th class="<?=$arFieldData['sCssClass']?> theme-bgcolor"><?=$arFieldData['sName']?></th>
                    <?php //} ?>
                <?php } ?>
            <?php } ?>
        </tr> 
        <?php
            $i = $arResult['iCountOnPage']*($arResult['iPageNumber']-1);
                foreach ($arResult['arItems'] as $iIndex => $arItem)
                {
                    $i++;
        ?>
        <tr class="<?php echo $i%2==0?"bgr":""?>">
            <td class="number"><?php echo $arItem['Number'];?>. </td>
            <td class="photo">
                <?php
                        $arPhoto = array();
                        if (intval($arItem['PhotoFileId']) > 0)
                            $arPhoto = CFile::ResizeImageGet($arItem['PhotoFileId'], array('width'=>74, 'height'=>56), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        
                        if (strlen($arPhoto['src']) > 0) {
                        ?><img src="<?=$arPhoto['src']?>" width="74" alt="<?echo $arItem['SectionFullName'];?>, <?echo $arItem['Address'];?>" title="" /><?
                        if ($arItem['ObjectPhotoCount'] > 2)
                        {
                            ?><a href="<?=$arItem['DetailUrl']?>" class="objectPhotoCount">(<?php echo GetMessage('SMARTREALT_MORE');?> <?echo $arItem['ObjectPhotoCount']-1;?> <?php echo GetMessage('SMARTREALT_PHOTO');?>)</a><?
                        }
                    }
                ?>
            </td>
            <td class="address"><?php
                if ($bManyTypes)
                {
                    ?><div class="objectType"><?echo $arItem['SectionFullName'];?></div><?
                }
                ?><a href="<?php echo $arItem['DetailUrl'];?>"><?
                    echo $arItem['Address'];
                ?></a>
                <?php if (strlen($arItem['CityAreaName']) > 0) { ?>
                    <div class="cityArea"><?php echo GetMessage('SMARTREALT_CITY_AREA') ?> <?php echo $arItem['CityAreaName'] ?></div>
                <?php } ?>
                <?php if (strlen($arItem['MetroStationName']) > 0) { ?>
                    <div class="metroStation"><span class="label"><?php echo GetMessage('SMARTREALT_METRO') ?></span> <?php echo $arItem['MetroStationName'] ?></div>
                <?php } ?>
                <div class="contactInfo"><?php echo GetMessage('SMARTREALT_PHONE');?> <?echo $arItem['AgentPhone'];?></div><?
                ?>
                </td>
            <?php if (in_array($TypeIdForView , $arResult['arTableFileds']['RoomQuantity']['arTypeId'])) { ?> 
            <td class="roomQuantity"><?php echo !empty($arItem['RoomQuantity'])?$arItem['RoomQuantity']:"-"?></td>
            <?php } ?>
            <?php if (in_array($TypeIdForView , $arResult['arTableFileds']['Floor']['arTypeId'])) { ?>
            <td class="floor">
                <?php
                echo !empty($arItem['Floor'])?$arItem['Floor']:"-";
                echo "/"; 
                echo !empty($arItem['FloorQuantity'])?$arItem['FloorQuantity']:"-"?>
            </td>
            <?php } ?>
            <?php if (in_array($TypeIdForView , $arResult['arTableFileds']['FloorQuantity']['arTypeId'])) { ?>
            <td class="floor">
                <?php echo !empty($arItem['FloorQuantity'])?$arItem['FloorQuantity']:"-"?>
            </td>
            <?php } ?>
            <td class="generalArea">
                <?php echo SmartRealt_CatalogElement::GetAreaString($arItem);?>
            </td>
            <?php if (in_array($TypeIdForView , $arResult['arTableFileds']['LandArea']['arTypeId'])) { ?>
            <td class="landArea">        
                <?php echo doubleval($arItem['LandArea']) > 0?round($arItem['LandArea']) ." ". $arItem['LandAreaUnitShortName']:"-"?>
            </td>
            <?php } ?>
            <td class="price"><?php echo $arItem['Price']; ?></td>
        </tr>
        <?php } ?>            
    </table>
    <?php } else { ?>
    <p><?php echo GetMessage('SMARTREALT_NO_OFFERS');?></p>
    <?php } ?>