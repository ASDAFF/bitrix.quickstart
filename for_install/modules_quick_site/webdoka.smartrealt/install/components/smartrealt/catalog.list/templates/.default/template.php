<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (is_array($arParams['TYPE_ID']) && count($arParams['TYPE_ID']) > 1)
{
    $TypeIdForView = $arParams['TYPE_ID'][0];
    $bManyTypes = true;
}
else
{
    $bManyTypes = false;
    $TypeIdForView = $arParams['TYPE_ID'][0];
}
?>
<h1 id="h1"><?=$arResult['TITLE']?></h1>
<?php if (!$arResult['bNotFound']) { ?> 
    <table cellpadding="0" cellspacing="1" border="0" class="objectsTable">
        <tr>
            <?php foreach ($arResult['arTableFileds'] as $sField=>$arFieldData) { ?>
                <?php if (!isset($arFieldData['arTypeId']) || in_array($TypeIdForView , $arFieldData['arTypeId'])) { ?>
                    <?php if (strlen(GetColumnSortLink($sField)) > 0) { ?>
                        <th class="<?=$arFieldData['sCssClass']?> theme-bgcolor <?php echo IsColumnSort($sField)?'sort':''?>"><div class=" <?php echo GetColumnSortCSSClassName($sField); ?>"></div><a href="<?php echo GetColumnSortLink($sField); ?>" rel="nofollow"><?=$arFieldData['sName']?></a></th>
                    <?php } else { ?> 
                        <th class="<?=$arFieldData['sCssClass']?> theme-bgcolor"><?=$arFieldData['sName']?></th>
                    <?php } ?>
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
                    if (strlen($arItem['Photo']['src']) > 0)
                    {
                        ?><a href="<? echo $arItem['DetailUrl']?>"><img src="<?=$arItem['Photo']['src']?>" alt="<?echo $arItem['SectionFullName'];?>, <?echo $arItem['Address'];?>" title="" border="0" /></a><?
                        if ($arItem['PhotoCount'] > 2)
                        {
                            ?><a href="<? echo $arItem['DetailUrl']?>" class="objectPhotoCount">(<?php echo GetMessage('SMARTREALT_MORE') ?> <?echo $arItem['PhotoCount']-1;?> <?php echo GetMessage('SMARTREALT_PHOTO') ?>)</a><?
                        }
                    }
                ?>
            </td>
            <td class="address"><?php
                if ($bManyTypes)
                {
                    ?><div class="objectType"><?echo $arItem['SectionFullName'];?></div><?
                }
                ?><a href="<? echo $arItem['DetailUrl']?>"><?
                    echo $arItem['Address'];
                ?></a>
                <?php if (strlen($arItem['CityAreaName']) > 0) { ?>
                    <div class="cityArea"><?php echo GetMessage('SMARTREALT_CITY_AREA') ?> <?php echo $arItem['CityAreaName'] ?></div>
                <?php } ?>
                <?php if (strlen($arItem['MetroStationName']) > 0) { ?>
                    <div class="metroStation"><span class="label"><?php echo GetMessage('SMARTREALT_METRO') ?></span> <?php echo $arItem['MetroStationName'] ?></div>
                <?php } ?>
                <div class="contactInfo"><?php echo GetMessage('SMARTREALT_PHONE') ?> <?echo $arItem['AgentPhone'];?></div><?
                ?>
                </td>
            <?php if (in_array($TypeIdForView , $arResult['arTableFileds']['RoomQuantity']['arTypeId'])) { ?> 
            <td class="roomQuantity">
                <?php
                echo !empty($arItem['RoomOffer'])?$arItem['RoomOffer']."/":"";
                echo !empty($arItem['RoomQuantity'])?$arItem['RoomQuantity']:"-";?>
            </td>
            <?php } ?>
            <?php if (in_array($TypeIdForView , $arResult['arTableFileds']['Floor']['arTypeId'])) { ?>
            <td class="floor">
                <?php
                echo !empty($arItem['Floor'])?$arItem['Floor']:"-";
                echo "/"; 
                echo !empty($arItem['FloorQuantity'])?$arItem['FloorQuantity']:"-";?>
            </td>
            <?php } ?>
            <?php if (in_array($TypeIdForView , $arResult['arTableFileds']['FloorQuantity']['arTypeId'])) { ?>
            <td class="floor">
                <?php echo !empty($arItem['FloorQuantity'])?$arItem['FloorQuantity']:"-"?>
            </td>
            <?php } ?>
            <td class="generalArea">
                <?php
                    echo SmartRealt_CatalogElement::GetAreaString($arItem);
                ?>
            </td>
            <?php if (in_array($TypeIdForView , $arResult['arTableFileds']['LandArea']['arTypeId'])) { ?>
            <td class="landArea">        
                <?php echo doubleval($arItem['LandArea']) > 0?(round($arItem['LandArea']) ." ". $arItem['LandAreaUnitName']):"-"?>
            </td>
            <?php } ?>
            <td class="price"><?php echo $arItem['Price']?></td>
        </tr>
        <?php } ?>            
    </table>
    <?php echo $arResult['rsItems']->GetPageNavStringEx();?>
    <div class="rubricDescription">
        <?php echo $arResult['arRubric']['Description'];?>
    </div>
<?php } else { ?>
    <?php echo GetMessage('SMARTREALT_NOT_FOUND') ?>
<?php if ($arResult['bSetFilter']) { ?>
    <?php echo GetMessage('SMARTREALT_NOT_FOUND_CLEAR_FILTER') ?>
<?php } } ?>
