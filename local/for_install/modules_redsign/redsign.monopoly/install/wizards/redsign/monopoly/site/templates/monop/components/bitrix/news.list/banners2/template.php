<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
?>

<?php if(is_array($arResult['ITEMS'])): ?>
    <div 
        class="mainbanners js-mainbanners mainbanners--<?=$arResult['BANNER_CLASSES']?>"
        data-changespeed=<?=$arResult['CHANGE_SPEED']?>
        data-changedelay=<?=$arResult['CHANGE_DELAY']?>
        data-useowl="Y">

        <div class="mainbanners_items js-mainbanners_items owl_banners_colors">
    
        <?php foreach($arResult['ITEMS'] as $arItem): 
        
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
                
                if($arItem['BANNER_TYPE'] != 'video'):
                ?>
                
                    <div class="mainbanners_item <?=$arItem['BANNER_TYPE']?>">
                        
                        <a 
                            href="<?=$arItem['PROPERTIES'][$arParams['RSMONOPOLY_LINK']]['VALUE']?>"
                            <?=$arItem['PROPERTIES'][$arParams['RSMONOPOLY_BLANK']]['VALUE']!='' ?'target="_blank"':''?>
                            title=<?=$arItem['PREVIEW_PICTURE']['TITLE']?>
                        >
                            <?php if($arItem['BANNER_TYPE'] == 'text' || $arItem['BANNER_TYPE'] == 'banner'): ?>
                                <div class="mainbanners_image js-mainbanners_image" 
                                     data-img-src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>"
                                     style="background-image: url('<?=$arItem['PREVIEW_PICTURE']['SRC']?>');"
                                >
                                    <div class="mainbanners_info-container">
                                        <div class="info">
                                            <?php if(!empty($arItem['RS_TEXT1'])): ?>                                            
                                                <div class="text1 robotolight">
                                                    <p><span class="aprimary"><?=$arItem['RS_TEXT1']?></span></p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($arItem['RS_PRICE'])): ?>  
                                                <div class="cost robotolight">
                                                    <p><span> <?=$arItem['RS_PRICE']?></span></p>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($arItem['RS_TEXT2'])): ?>  
                                                <br>
                                                <div class="text2 robotolight">
                                                    <p><span><?=$arItem['RS_TEXT2']?></span></p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif($arItem['BANNER_TYPE'] == 'product'): ?>
                                <div class="row">
                                    <div class="col col-xs-6 text1 robotolight text-fadeout">
                                        
                                        <?php if(!empty($arItem['RS_TEXT1'])): ?>                                            
                                                <p class="text"><span class="aprimary"><?=$arItem['RS_TEXT1']?></span></p>
                                        <?php endif; ?>
                                        <?php if(!empty($arItem['RS_PRICE'])): ?>                                            
                                                <p class="buy">
                                                    <button type="button" class="btn btn-default"><?=Loc::getMessage('RSMONOPOLY_BTN_BUY');?></button>
                                                    <?=$arItem['RS_PRICE']?></span>
                                                </p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col col-xs-12 col-sm-6 text-center mainbanners_image-container">
                                        <div class="mainbanners_image js-mainbanners_image">                                                 
                                            <img 
                                                src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>"
                                                alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" 
                                                title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>"
                                                data-img-src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>"
                                            >
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </a>

                    </div>
                
                <?php endif; ?>
                
        <?php endforeach; ?>
        </div>
    
        <div class="preloader mainbanners_preloader js-mainbanners_preloader"></div>
    </div>
<?php endif; ?>