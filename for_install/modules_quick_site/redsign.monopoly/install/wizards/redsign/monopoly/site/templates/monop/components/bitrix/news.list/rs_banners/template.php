<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
?>

<style>
   <?php if($arResult['IS_JS_HEIGHT_ADJUST'] != "Y"): ?>
       .rs-banners .rs-banners_banner,
       .rs-banners-sidebanner,
       .rs_banner-preloader {
           height: 40vw
       }
       @media(min-width: 768px) {           
           .rs-banners .rs-banners_banner,
           .rs-banners-sidebanner,
           .rs_banner-preloader {
               height: <?=$arResult['BANNER_HEIGHT']?>
           }
       }

   <?php endif; ?>
</style>

<div class="rs-banners-container js-mainbanners-container <?=$arResult['BANNER_CLASS']?>" 
    style="opacity: 0; transition: 1s; <?php if(!empty($arResult['MARGIN_TOP'])) echo 'margin-top: 7px;'?>">

    <div class="rs-banners-sidebanner __left js-sidebanners <?php if(in_array("left", $arResult['SELECTED_SIDEBANNERS'])) {echo 'js-sidebanners_selected';} ?>" 
         style="display: none;">
        <?php foreach($arResult['SIDEBANNERS']['LEFT'] as $arImage): ?>
            <div class="rs-banners-sidebanner_image">
                <a href="<?$arImage['link']?>">
                    <img src="<?=$arImage['src']?>">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="rs-banners-sidebanner __right js-sidebanners <?php if(in_array("right", $arResult['SELECTED_SIDEBANNERS'])) {echo 'js-sidebanners_selected';} ?>" 
        style="  display: none;">
        <?php foreach($arResult['SIDEBANNERS']['RIGHT'] as $arImage): ?>
            <div class="rs-banners-sidebanner_image">
                <a href="<?$arImage['link']?>">
                    <img src="<?=$arImage['src']?>">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div 
        class="js-banner-options"
        style="display: none;"
        <?php foreach($arResult['BANNER_OPTIONS'] as $optionName => $optionValue): ?>
            <?php if(is_bool($optionValue)): ?>
                data-<?=$optionName?>="<?=$optionValue ? 'true' : 'false'?>"
            <?php elseif(is_array($optionValue)): ?>
            
            <?php else: ?>
                data-<?=$optionName?>="<?=$optionValue?>"
            <?php endif; ?>
        <?php endforeach; ?>
    ></div>
    
    <div class="rs-banners js-banners owl-theme owl-carousel" style=" display: none;">
    
    <?php foreach($arResult['ITEMS'] as $arItem): ?>
    
        <?php
         $this->AddEditAction(
            $arItem['ID'],
            $arItem['EDIT_LINK'],
            CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT")
        );
        $this->AddDeleteAction(
            $arItem['ID'],
            $arItem['DELETE_LINK'],
            CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), 
            array("CONFIRM" => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM'))
        );
        ?>
    
        <div class="rs-banners_banner">
            <?php if($arItem['VIDEO_TYPE'] == 'frame'): ?>
                <a class="owl-video" href="<?=$arItem['VIDEO_URL']?>"></a>
            <?php elseif($arItem['VIDEO_TYPE'] == 'file'): ?>
                
                <div class="rs-banners_video" data-play="false">
                    <video src="<?=$arItem['VIDEO_URL']?>"></video>
                </div>
                <div class="rs-banners_video-play"></div>
                <div class="rs-banners_wrap">
                        <div class="rs-banners_infowrap rs-banners_infovideo">
                            <div class="rs-banners_info">
                            
                                <?php if(!empty($arItem['PRODUCT_TITLE'])): ?>                                
                                    <div class="rs-banners_title rs-banners_video-blockwrap">
                                        <?=$arItem['PRODUCT_TITLE']?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if(!empty($arItem['PRODUCT_PRICE'])): ?>   
                                    <div class="rs-banners_price rs-banners_video-blockwrap">
                                        <?=$arItem['PRODUCT_PRICE']?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if(!empty($arItem['PRODUCT_DESC'])): ?>   
                                    <div class="rs-banners_desc rs-banners_video-blockwrap">
                                        <?=$arItem['PRODUCT_DESC']?>
                                    </div>
                                <?php endif; ?>
                                
                            </div>
                        </div>
                    </div>
            <?php else: ?>
            
            
                <?php if(!empty($arItem['BACKGROUND'])): ?>
                <div 
                    class="rs-banners_background" 
                    data-img-src="<?=$arItem['BACKGROUND']?>" 
                    style="background-image:url('<?=$arItem['BACKGROUND']?>')"
                >
                </div>
                <?php endif; ?>
                
                
                <div class="rs-banners_wrap">
                    <?php if(!empty($arItem['PRODUCT_IMG'])): ?>
                    
                        <div class="rs-banners_product">
                            <img 
                                data-img-src="<?=$arItem['PRODUCT_IMG']?>"
                                src="<?=$arItem['PRODUCT_IMG']?>"
                                alt="<?=$arItem['NAME']?>"
                            >
                        </div>
                        
                    <?php endif; ?>
                        
                        <div class="rs-banners_infowrap" style="opacity: 0; transition: 1s;">
                            <div class="rs-banners_info">
                                <?php if(!empty($arItem['PRODUCT_TITLE'])): ?>
                                    <div 
                                        class="rs-banners_title"
                                        style="<?=!(empty($arItem['PRODUCT_TITLE_BACKGROUND'])) ? 'background: '.htmlspecialcharsbx($arItem['PRODUCT_TITLE_BACKGROUND']).';' : ''?>"
                                    >
                                        <?=$arItem['PRODUCT_TITLE'];?>
                                    </div>
                                <?php endif; ?>
                                <?php if(!empty($arItem['PRODUCT_PRICE'])): ?>
                                    <br>
                                    <div 
                                        class="rs-banners_price"
                                        style="<?=!(empty($arItem['PRODUCT_PRICE_BACKGROUND'])) ? 'background: '.htmlspecialcharsbx($arItem['PRODUCT_PRICE_BACKGROUND']).';' : ''?>"
                                    >
                                        <?=$arItem['PRODUCT_PRICE'];?>
                                    </div>
                                <?php endif; ?>
                                <?php if(!empty($arItem['PRODUCT_DESC'])): ?>
                                    <br>
                                    <div class="rs-banners_desc">
                                        <?=$arItem['PRODUCT_DESC'];?>
                                    </div>
                                <?php endif; ?>
                                <?php if(!empty($arItem['PRODUCT_BUTTON_TEXT'])): ?>
                                    <br>
                                    <a 
                                        href="<?=$arItem['PRODUCT_LINK']?>"
                                        target="_blank" class="rs-banners_button"
                                        style="
                                            <?=(!empty($arItem['PRODUCT_BUTTON_BACKGROUND_COLOR'])) ? 'background: '.$arItem['PRODUCT_BUTTON_BACKGROUND_COLOR'].'; ' : ''?>
                                            <?=(!empty($arItem['PRODUCT_BUTTON_TEXT_COLOR'])) ? 'color: '.$arItem['PRODUCT_BUTTON_TEXT_COLOR'].'; ' : ''?>
                                        "
                                    >
                                            <?=$arItem['PRODUCT_BUTTON_TEXT']?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                </div>
                <?php if(!empty($arItem['PRODUCT_LINK'])): ?>
                    <a href="<?=$arItem['PRODUCT_LINK']?>" target="_blank" class="rs-banners_link"></a>
                <?php endif; ?>
            <?php endif; ?>
            
        </div>
    <?php endforeach; ?>
    </div>
    
    <div class="rs-banners_bottom-line"></div>
    
</div>
<div class="js-preloader rs_banner-preloader preloader" style="width: 100%;"></div>