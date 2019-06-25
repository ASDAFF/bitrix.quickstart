<?if (!empty($arResult['STARTSHOP']['OFFERS'])):?>
    <div class="startshop-offers-properties">
        <div class="startshop-offers-properties-wrapper">
            <?foreach($arResult['STARTSHOP']['OFFER']['PROPERTIES'] as $arOffersProperty):?>
                <?if ($arOffersProperty['TYPE'] == "TEXT"):?>
                    <div class="startshop-offers-property startshop-offers-property-text StartShopOffersProperty_<?=$arOffersProperty['CODE']?>">
                        <div class="startshop-offers-property-wrapper">
                            <div class="startshop-offers-title"><?=$arOffersProperty['NAME']?>:</div>
                            <?foreach ($arOffersProperty['VALUES'] as $arOffersPropertyValue):?>
                                <div class="startshop-offers-value StartShopOffersValue_<?=$arOffersPropertyValue['CODE']?>">
                                    <div class="startshop-offers-value-wrapper" onclick="<?=$sUniqueID?>_Offers.SetCurrentOfferByPropertyValue(<?=CUtil::PhpToJSObject($arOffersProperty['CODE'])?>, <?=CUtil::PhpToJSObject($arOffersPropertyValue['CODE'])?>)">
                                        <div class="startshop-aligner-vertical"></div>
                                        <div class="startshop-offers-text"><?=$arOffersPropertyValue['TEXT']?></div>
                                    </div>
                                </div>
                            <?endforeach;?>
                            <div class="startshop-offers-value startshop-offers-value-empty StartShopOffersValue_">
                                <div class="startshop-offers-value-wrapper" onclick="<?=$sUniqueID?>_Offers.SetCurrentOfferByPropertyValue(<?=CUtil::PhpToJSObject($arOffersProperty['CODE'])?>, '')">
                                    <div class="startshop-aligner-vertical"></div>
                                    <div class="startshop-offers-text">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?else:?>
                    <div class="startshop-offers-property startshop-offers-property-image StartShopOffersProperty_<?=$arOffersProperty['CODE']?>">
                        <div class="startshop-offers-property-wrapper">
                            <div class="startshop-offers-title"><?=$arOffersProperty['NAME']?>:</div>
                            <?foreach ($arOffersProperty['VALUES'] as $arOffersPropertyValue):?>
                                <div class="startshop-offers-value StartShopOffersValue_<?=$arOffersPropertyValue['CODE']?>">
                                    <div class="startshop-offers-value-wrapper" onclick="<?=$sUniqueID?>_Offers.SetCurrentOfferByPropertyValue(<?=CUtil::PhpToJSObject($arOffersProperty['CODE'])?>, <?=CUtil::PhpToJSObject($arOffersPropertyValue['CODE'])?>)">
                                        <div class="startshop-offers-image">
                                            <img src="<?=$arOffersPropertyValue['PICTURE']?>" title="<?=$arOffersPropertyValue['TEXT']?>" alt="<?=$arOffersPropertyValue['TEXT']?>" />
                                        </div>
                                        <div class="startshop-offers-sprite"></div>
                                    </div>
                                </div>
                            <?endforeach;?>
                            <div class="startshop-offers-value startshop-offers-value-empty StartShopOffersValue_">
                                <div class="startshop-offers-value-wrapper" onclick="<?=$sUniqueID?>_Offers.SetCurrentOfferByPropertyValue(<?=CUtil::PhpToJSObject($arOffersProperty['CODE'])?>, '')">
                                    <div class="startshop-offers-image"></div>
                                    <div class="startshop-offers-sprite"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?endif;?>
            <?endforeach;?>
        </div>
    </div>
<?endif;?>