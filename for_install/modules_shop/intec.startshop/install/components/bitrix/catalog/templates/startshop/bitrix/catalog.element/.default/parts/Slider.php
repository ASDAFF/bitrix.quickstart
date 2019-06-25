<?if (empty($arResult['STARTSHOP']['OFFERS'])):?>
    <?$arPictures = array();?>
    <?if (!empty($arResult['PICTURE'])):?>
        <?$arPictures[] = $arResult['PICTURE']['SRC'];?>
    <?endif;?>
    <?foreach ($arResult['MORE_PHOTO'] as $sPicture):?>
        <?$arPictures[] = $sPicture;?>
    <?endforeach;?>
    <div class="startshop-slider">
        <div class="startshop-slider-preview">
            <div class="startshop-slider-preview-wrapper">
				<div class="marks">
					<?if( $arResult["PROPERTIES"]["CML2_HIT"]["VALUE"] ){?>
						<span class="mark hit"><?=GetMessage("MARK_HIT");?></span>
					<?}?>			
					<?if( $arResult["PROPERTIES"]["CML2_NEW"]["VALUE"] ){?>
						<span class="mark new"><?=GetMessage("MARK_NEW");?></span>
					<?}?>
					<?if( $arResult["PROPERTIES"]["CML2_RECOMEND"]["VALUE"] ){?>
						<span class="mark recommend"><?=GetMessage("MARK_RECOMEND");?></span>
					<?}?>
				</div>
                <div class="startshop-slider-preview-images">
                    <?if (!empty($arPictures)):?>
                        <?foreach ($arPictures as $sPicture):?>
                            <a rel="startshop-preview-images" href="<?=$sPicture?>" class="startshop-slider-preview-image startshop-image startshop-fancy">
                                <div class="startshop-aligner-vertical"></div>
                                <img src="<?=$sPicture?>" alt="<?=htmlspecialcharsbx($arResult['NAME'])?>" title="<?=htmlspecialcharsbx($arResult['NAME'])?>" />
                            </a>
                        <?endforeach;?>
                    <?else:?>
                        <div class="startshop-slider-preview-image startshop-image">
                            <div class="startshop-aligner-vertical"></div>
                            <img src="<?=$this->GetFolder().'/images/product.noimage.png'?>" alt="<?=htmlspecialcharsbx($arResult['NAME'])?>" title="<?=htmlspecialcharsbx($arResult['NAME'])?>" />
                        </div>
                    <?endif;?>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <?if ($arFlags['SLIDER_SHOW'] && !empty($arPictures)):?>
            <div class="startshop-slider-slides">
                <div class="startshop-slider-buttons">
                    <div class="startshop-aligner-vertical"></div>
                    <div class="startshop-slider-buttons-wrapper">
                        <div class="startshop-slider-button-small startshop-slider-button-left"><div class="icon"></div></div>
                        <div class="startshop-slider-button-small startshop-slider-button-right"><div class="icon"></div></div>
                    </div>
                </div>
                <div class="startshop-slider-list">
                    <?foreach($arPictures as $sPicture):?>
                        <div class="startshop-slider-image">
                            <div class="startshop-slider-image-wrapper">
                                <div class="startshop-slider-image-wrapper-wrapper">
                                    <div class="startshop-slider-image-wrapper-wrapper-wrapper startshop-image">
                                        <div class="startshop-aligner-vertical"></div>
                                        <img src="<?=$sPicture?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?endforeach;?>
                </div>
                <script type="text/javascript">
                    $('document').ready(function () {
                        var $oRoot = $(<?=CUtil::PhpToJSObject('#'.$sUniqueID.' .startshop-slider')?>);
                        var $oSlider = new Startshop.Controls.Slider({
                            "Container": $oRoot.find('.startshop-slider-slides .startshop-slider-list'),
                            "Element": ".startshop-slider-image"
                        });

                        var $oSliderPreviewImages = $oRoot.find('.startshop-slider-preview .startshop-slider-preview-images .startshop-slider-preview-image');
                        var $oSliderButtonLeft = $oRoot.find('.startshop-slider-slides .startshop-slider-buttons .startshop-slider-button-left');
                        var $oSliderButtonRight = $oRoot.find('.startshop-slider-slides .startshop-slider-buttons .startshop-slider-button-right');

                        $oSliderButtonLeft.click(function () {
                            $oSlider.SlidePrev();
                        });

                        $oSliderButtonRight.click(function () {
                            $oSlider.SlideNext();
                        });

                        $oSlider.Events.On("BeforeAdaptability", function ($oSlider) {
                            var $oElementSize = Startshop.Functions.GetElementSize($oSlider.GetElements());
                            var $oContainerSize = Startshop.Functions.GetElementSize($oSlider.GetContainer());
                            $oSlider.Settings.Count = Math.round($oContainerSize.Width/$oElementSize.Width);
                        });

                        function SetPreview ($iIndex) {
                            var $oElements = $oSlider.GetElements();
                            $oElements.removeClass('startshop-ui-state-active').eq($iIndex).addClass('startshop-ui-state-active');
                            $oSliderPreviewImages.css('display', 'none');
                            $oSliderPreviewImages.eq($iIndex).css('display', '');
                        }

                        $oSlider.GetElements().click(function () {
                            SetPreview($(this).index());
                        });

                        $oSlider.Events.On("BeforeSlide", function ($oSettings) {
                            if ($oSettings.Element.Next.Number == 1) {
                                $oSliderButtonLeft.css('display', 'none');
                            } else {
                                $oSliderButtonLeft.css('display', '');
                            }

                            if ($oSettings.Element.Next.Number == $oSettings.Boundaries.Maximum) {
                                $oSliderButtonRight.css('display', 'none');
                            } else {
                                $oSliderButtonRight.css('display', '');
                            }
                        });

                        SetPreview(0);
                        $oSlider.Initialize();
                    });
                </script>
            </div>
        <?endif;?>
    </div>
    <?unset($arPictures, $sPicture);?>
<?else:?>
    <script type="text/javascript">
        var $arSliders<?=$sUniqueID?> = [];
    </script>
    <?foreach ($arResult['STARTSHOP']['OFFERS'] as $arOffer):?>
        <?$arPictures = array();?>
        <?if (!empty($arOffer['PICTURE'])):?>
            <?$arPictures[] = $arOffer['PICTURE']['SRC'];?>
        <?endif;?>
        <?foreach ($arOffer['MORE_PHOTO'] as $sPicture):?>
            <?$arPictures[] = $sPicture;?>
        <?endforeach;?>
        <div class="startshop-slider StartShopOffersSlider<?=$arOffer['ID']?>" style="display: none;">
            <div class="startshop-slider-preview">
                <div class="startshop-slider-preview-wrapper">
					<div class="marks">
						<?if( $arResult["PROPERTIES"]["CML2_HIT"]["VALUE"] ){?>
							<span class="mark hit"><?=GetMessage("MARK_HIT");?></span>
						<?}?>			
						<?if( $arResult["PROPERTIES"]["CML2_NEW"]["VALUE"] ){?>
							<span class="mark new"><?=GetMessage("MARK_NEW");?></span>
						<?}?>
						<?if( $arResult["PROPERTIES"]["CML2_RECOMEND"]["VALUE"] ){?>
							<span class="mark recommend"><?=GetMessage("MARK_RECOMEND");?></span>
						<?}?>
					</div>
                    <div class="startshop-slider-preview-images">
                        <?if (!empty($arPictures)):?>
                            <?foreach ($arPictures as $sPicture):?>
                                <a rel="startshop-preview-images" href="<?=$sPicture?>" class="startshop-slider-preview-image startshop-image startshop-fancy">
                                    <div class="startshop-aligner-vertical"></div>
                                    <img src="<?=$sPicture?>" alt="<?=htmlspecialcharsbx($arResult['NAME'])?>" title="<?=htmlspecialcharsbx($arResult['NAME'])?>" />
                                </a>
                            <?endforeach;?>
                        <?else:?>
                            <div class="startshop-slider-preview-image startshop-image">
                                <div class="startshop-aligner-vertical"></div>
                                <img src="<?=$this->GetFolder().'/images/product.noimage.png'?>" alt="<?=htmlspecialcharsbx($arResult['NAME'])?>" title="<?=htmlspecialcharsbx($arResult['NAME'])?>" />
                            </div>
                        <?endif;?>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <?if ($arFlags['SLIDER_SHOW'] && !empty($arPictures)):?>
                <div class="startshop-slider-slides">
                    <div class="startshop-slider-buttons">
                        <div class="startshop-aligner-vertical"></div>
                        <div class="startshop-slider-buttons-wrapper">
                            <div class="startshop-slider-button-small startshop-slider-button-left"><div class="icon"></div></div>
                            <div class="startshop-slider-button-small startshop-slider-button-right"><div class="icon"></div></div>
                        </div>
                    </div>
                    <div class="startshop-slider-list">
                        <?foreach($arPictures as $sPicture):?>
                            <div class="startshop-slider-image">
                                <div class="startshop-slider-image-wrapper">
                                    <div class="startshop-slider-image-wrapper-wrapper">
                                        <div class="startshop-slider-image-wrapper-wrapper-wrapper startshop-image">
                                            <div class="startshop-aligner-vertical"></div>
                                            <img src="<?=$sPicture?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?endforeach;?>
                    </div>
                    <script type="text/javascript">
                        $('document').ready(function () {
                            var $oRoot = $(<?=CUtil::PhpToJSObject('#'.$sUniqueID.' .startshop-slider.StartShopOffersSlider'.$arOffer['ID']);?>);
                            var $oSlider = new Startshop.Controls.Slider({
                                "Container": $oRoot.find('.startshop-slider-slides .startshop-slider-list'),
                                "Element": ".startshop-slider-image"
                            });

                            $arSliders<?=$sUniqueID?>.push($oSlider);

                            var $oSliderPreviewImages = $oRoot.find('.startshop-slider-preview .startshop-slider-preview-images .startshop-slider-preview-image');
                            var $oSliderButtonLeft = $oRoot.find('.startshop-slider-slides .startshop-slider-buttons .startshop-slider-button-left');
                            var $oSliderButtonRight = $oRoot.find('.startshop-slider-slides .startshop-slider-buttons .startshop-slider-button-right');

                            $oSliderButtonLeft.click(function () {
                                $oSlider.SlidePrev();
                            });

                            $oSliderButtonRight.click(function () {
                                $oSlider.SlideNext();
                            });

                            $oSlider.Events.On("BeforeAdaptability", function ($oSlider) {
                                var $oElementSize = Startshop.Functions.GetElementSize($oSlider.GetElements());
                                var $oContainerSize = Startshop.Functions.GetElementSize($oSlider.GetContainer());
                                $oSlider.Settings.Count = Math.round($oContainerSize.Width/$oElementSize.Width);
                            });

                            function SetPreview ($iIndex) {
                                var $oElements = $oSlider.GetElements();
                                $oElements.removeClass('startshop-ui-state-active').eq($iIndex).addClass('startshop-ui-state-active');
                                $oSliderPreviewImages.css('display', 'none');
                                $oSliderPreviewImages.eq($iIndex).css('display', '');
                            }

                            $oSlider.GetElements().click(function () {
                                SetPreview($(this).index());
                            });

                            $oSlider.Events.On("BeforeSlide", function ($oSettings) {
                                if ($oSettings.Element.Next.Number == 1) {
                                    $oSliderButtonLeft.css('display', 'none');
                                } else {
                                    $oSliderButtonLeft.css('display', '');
                                }

                                if ($oSettings.Element.Next.Number == $oSettings.Boundaries.Maximum) {
                                    $oSliderButtonRight.css('display', 'none');
                                } else {
                                    $oSliderButtonRight.css('display', '');
                                }
                            });

                            SetPreview(0);
                            $oSlider.Initialize();
                        });
                    </script>
                </div>
            <?endif;?>
        </div>
        <?unset($arPictures, $sPicture);?>
    <?endforeach;?>
<?endif;?>
<script type="text/javascript">
    $(document).ready(function(){
        $('.startshop-fancy').startshopFancybox();
    });
</script>
