<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(empty($arResult['OFFERS'])){ $HAVE_OFFERS = false; $PRODUCT = &$arResult; } else { $HAVE_OFFERS = true; $PRODUCT = &$arResult['OFFERS'][0]; }

// pictures
$arImages = array();
if($HAVE_OFFERS) {
    foreach($arResult['OFFERS'] as $key1 => $arOffer) {
        if( is_array($arOffer['DETAIL_PICTURE']['RESIZE']) ) {
            $arImages[] = array(
                'DATA' => array(
                    'OFFER_KEY' => $key1,
                    'OFFER_ID' => $arOffer['ID'],
                ),
                'PIC' => $arOffer['DETAIL_PICTURE'],
            );
        }
        if( is_array($arOffer['PROPERTIES'][$arParams['RSFLYAWAY_PROP_SKU_MORE_PHOTO']]['VALUE'][0]['RESIZE']) ) {
            foreach($arOffer['PROPERTIES'][$arParams['RSFLYAWAY_PROP_SKU_MORE_PHOTO']]['VALUE'] as $arImage) {
                $arImages[] = array(
                    'DATA' => array(
                        'OFFER_KEY' => $key1,
                        'OFFER_ID' => $arOffer['ID'],
                    ),
                    'PIC' => $arImage,
                );
            }
        }
    }
}
if( is_array($arResult['DETAIL_PICTURE']['RESIZE']) ) {
    $arImages[] = array(
        'DATA' => array(
            'OFFER_KEY' => 0,
            'OFFER_ID' => 0,
        ),
        'PIC' => $arResult['DETAIL_PICTURE'],
    );
}
if( is_array($arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_MORE_PHOTO']]['VALUE'][0]['RESIZE']) ) {
    foreach ($arResult['PROPERTIES'][$arParams['RSFLYAWAY_PROP_MORE_PHOTO']]['VALUE'] as $arImage) {
        $arImages[] = array(
            'DATA' => array(
                'OFFER_KEY' => 0,
                'OFFER_ID' => 0,
            ),
            'PIC' => $arImage,
        );
    }
}

?><div class="overflower popupgallery js-gallery"><?
    ?><div class="row"><?
        ?><div class="col col-md-12"><?

            ?><div class="row"><?

                // general picture
                ?><div class="col col-sm-9"><?
                    ?><div class="navigations"><?
                        ?><div class="around_changeit"><?
                            ?><div class="changeit"><?

                                if(is_array($arImages[0]['PIC']) && isset($arImages[0]['PIC']['SRC'])>0) {

                                    ?><img src="<?=$arImages[0]['PIC']['SRC']?>" alt="" title="" /><?
                                    if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]) {
                                        ?><span class="date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span><?
                                    }
                                }
                                ?></div><?
                            ?></div><?
                        ?><div class="nav prev js-nav"><span></span></div><?
                        ?><div class="nav next js-nav"><span></span></div><?
                        ?></div><?
                    if(is_array($arImages) && count($arImages)>0) {
                        ?><div class="description"><?=$arImages[0]['PIC']['DESCRIPTION']?></div><?
                    }
                    ?></div><?

                // other pictures
                ?><div class="col col-sm-3 fullright"><?
                    ?><div class="preview"><?=$arResult['PREVIEW_TEXT']?></div><?
                    if(is_array($arImages) && count($arImages)>0) {
                        ?><div class="thumbs style1" data-changeto=".changeit img"><?
                        foreach($arImages as $arImage) {
                            if(isset($arParams['RS_FLYAWAY_OFFER_ID']) && IntVal($arImage['DATA']['OFFER_ID'])>0 && IntVal($arImage['DATA']['OFFER_ID'])!=$arParams['RS_FLYAWAY_OFFER_ID']){
                                continue;
                            }
                            ?><div class="pic<?=$arImage['PIC']['ID']?> thumb" data-picture-id="<?=$arImage['PIC']['ID']?>"><?
                            ?><a <?
                            ?>href="<?=$arImage['PIC']['SRC']?>" <?
                            ?>data-index="<?=$arImage['PIC']['ID']?>" <?
                            ?>data-descr="<?=CUtil::JSEscape($arImage['PIC']['DESCRIPTION'])?>" <?
                            ?>style="background-image: url('<?=$arImage['PIC']['RESIZE']['src']?>');" <?
                            ?>><?
                            ?><div class="overlay"></div><?
                            ?><i class="fa"></i><?
                            ?></a><?
                            ?></div><?
                        }
                        ?></div><?
                    }
                    ?></div><?

                ?></div><?

            ?></div><?
        ?></div><?
    ?></div>
