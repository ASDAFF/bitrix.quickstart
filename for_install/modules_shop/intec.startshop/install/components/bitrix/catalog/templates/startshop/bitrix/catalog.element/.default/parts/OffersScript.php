<?if (!empty($arResult['STARTSHOP']['OFFERS'])):?>
    <script type="text/javascript">
        var <?=$sUniqueID?>_Offers = new Startshop.Catalog.Offers(<?=CUtil::PhpToJSObject(CStartShopToolsIBlock::GetOffersJSStructure($arResult))?>);
        <?=$sUniqueID?>_Offers.Events.OnOfferChange = function ($oParameters) {
            var $oRoot = $('#<?=$sUniqueID?>');

            $oRoot.find('.startshop-offers-properties .startshop-offers-property .startshop-offers-value')
                .removeClass('displayed')
                .removeClass('enabled')
                .removeClass('disabled')
                .removeClass('selected')
                .children('.startshop-offers-value-wrapper')
                    .removeClass('startshop-element-background')
                    .removeClass('startshop-element-border');

            Startshop.Functions.forEach($oParameters.Properties.Displayed, function ($iKey, $oPropertyValue) {
                $oRoot.find('.StartShopOffersProperty_' + $oPropertyValue['Key'] + ' .StartShopOffersValue_'  + $oPropertyValue['Value'])
                    .addClass('displayed');
            });

            Startshop.Functions.forEach($oParameters.Properties.Enabled, function ($iKey, $oPropertyValue) {
                $oRoot.find('.StartShopOffersProperty_' + $oPropertyValue['Key'] + ' .StartShopOffersValue_'  + $oPropertyValue['Value'])
                    .addClass('enabled');
            });

            Startshop.Functions.forEach($oParameters.Properties.Disabled, function ($iKey, $oPropertyValue) {
                $oRoot.find('.StartShopOffersProperty_' + $oPropertyValue['Key'] + ' .StartShopOffersValue_'  + $oPropertyValue['Value'])
                    .addClass('disabled');
            });

            Startshop.Functions.forEach($oParameters.Properties.Selected, function ($iKey, $oPropertyValue) {
                $oRoot.find('.StartShopOffersProperty_' + $oPropertyValue['Key'] + ' .StartShopOffersValue_'  + $oPropertyValue['Value'])
                    .addClass('selected').children('.startshop-offers-value-wrapper')
                        .addClass('startshop-element-background')
                        .addClass('startshop-element-border');
            });

            $oRoot.find('.startshop-information .startshop-order').css('display', 'none');
            $oRoot.find('.startshop-slider').css('display', 'none');
            $oRoot.find('.StartShopOffersPrice').html($oParameters.Offer['PRICES']['MINIMAL']['PRINT_VALUE']);
            $oRoot.find('.StartShopOffersQuantity').html($oParameters.Offer['QUANTITY']['VALUE']);
            $oRoot.find('.StartShopOffersSlider' + $oParameters.Offer['ID']).css('display', 'block');

            if ($oParameters.Offer['AVAILABLE']) {
                $oRoot.find('.StartShopOffersStateAvailable').css('display', '');
                $oRoot.find('.StartShopOffersStateUnavailable').css('display', 'none');
                $oRoot.find('.StartShopOffersOrder' + $oParameters.Offer['ID']).css('display', 'block');

                if ($oParameters.Offer['QUANTITY']['VALUE'] > 0) {
                    $oRoot.find('.StartShopOffersQuantity').css('display', '');
                } else {
                    $oRoot.find('.StartShopOffersQuantity').css('display', 'none');
                }
            } else {
                $oRoot.find('.StartShopOffersStateAvailable').css('display', 'none');
                $oRoot.find('.StartShopOffersStateUnavailable').css('display', '');
            }

            Startshop.Functions.forEach($arSliders<?=$sUniqueID?>, function ($iSliderIndex, $oSlider) {
                $oSlider.Refresh();
            });
        };

        <?=$sUniqueID?>_Offers.Initialize();
    </script>
<?endif;?>