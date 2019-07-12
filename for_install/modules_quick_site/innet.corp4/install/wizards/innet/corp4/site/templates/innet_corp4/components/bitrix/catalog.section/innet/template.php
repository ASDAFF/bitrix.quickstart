<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="items">
    <?foreach ($arResult['ITEMS'] as $arItem){?>
        <?
        if (!empty($arItem['PREVIEW_PICTURE']['ID'])){
            $pic = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 166, "height" => 96), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        } else if (!empty($arItem['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
            $pic = CFile::ResizeImageGet($arItem['PROPERTIES']['MORE_PHOTO']['VALUE'][0], array("width" => 166, "height" => 96), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        }

        if ($arParams['INNET_USE_PREVIEW_CATEGORIES_IN_SECTION'] == 'Y'){
            $dbSec = CIBlockSection::GetList(Array(), Array('ID' => $arItem['IBLOCK_SECTION_ID']), true, array('NAME', 'SECTION_PAGE_URL'));
            if ($arSection = $dbSec->GetNext()) {
                $arItem['SECTION_NAME'] = $arSection['NAME'];
                $arItem['SECTION_PAGE_URL'] = $arSection['SECTION_PAGE_URL'];
            }
        }

        $arPrice = INNETGetPrice(
            $arItem['PROPERTIES']['PRICE']['VALUE'],
            $arItem['PROPERTIES']['SALE']['VALUE'],
            $arItem['PROPERTIES']['SALE_TYPE']['VALUE_XML_ID'],
            $arItem['PROPERTIES']['CURRENCY']['VALUE']
        );
        ?>
        <div>
            <div>
                <?if (!empty($arPrice['OLD_PRICE'])){?>
                    <div class="label label-red"><span class="sale active"><span><?=GetMessage('INNET_CATALOG_SECTION_SALE') . $arPrice['PRICE_DIFF']?></span></div>
                <?} else if ($arItem['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE_XML_ID'] == 'new'){?>
                    <div class="label label-green"><?=$arItem['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE']?></div>
                <?} else if ($arItem['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE_XML_ID'] == 'special'){?>
                    <div class="label label-blue"><?=$arItem['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE']?></div>
                <?} else {?>
                    <?if (!empty($arItem['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE'])){?>
                        <div class="label label-purple"><?=$arItem['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE']?></div>
                    <?}?>
                <?}?>
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="hid">
                    <i><img class="prev_pic" src="<?=$pic['src']?>" alt="<?=$arItem['NAME']?>"></i>
                </a>
                <div class="item-name">
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
                    <?if ($arParams['INNET_USE_PREVIEW_TEXT_IN_SECTION'] == 'Y'){?>
                        <p><?=$arItem['PREVIEW_TEXT']?></p>
                    <?}?>
                </div>
                <?if (!empty($arPrice['PRICE'])){?>
                    <div class="price-block">
                        <?if (!empty($arPrice['PRICE'])){?>
                            <div class="price"><?=$arPrice['PRICE']?></div>
                        <?}?>
                        <?if (!empty($arPrice['OLD_PRICE'])){?>
                            <div class="price-old"><?=$arPrice['OLD_PRICE']?></div>
                        <?}?>
                        <?/*if (!empty($arPrice['OLD_PRICE'])){?>
                            <div class="price_diff_box"><?=GetMessage('INNET_CATALOG_SECTION_SALE')?><span class="price_diff"><?=$arPrice['PRICE_DIFF']?></span></div>
                        <?}*/?>
                    </div>
                <?}?>
                <div class="item-more">
                    <?if (!empty($arItem['PROPERTIES']['PRESENCE']['VALUE'])){?>
                        <?
                        $label_availability = '';
                        switch ($arItem['PROPERTIES']['PRESENCE']['VALUE_XML_ID']) {
                            case 'order':
                                $label_availability = 'bx_order';
                                break;
                            case 'availability':
                                $label_availability = 'bx_available';
                                break;
                            case 'notavailabile':
                                $label_availability = 'bx_notavailable';
                                break;
                        }
                        ?>
                        <div class="item-status <?=$label_availability?>"><?=$arItem['PROPERTIES']['PRESENCE']['VALUE']?></div>
                    <?}?>
                </div>
            </div>
        </div>
    <?}?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <?=$arResult["NAV_STRING"]?>
<?endif;?>