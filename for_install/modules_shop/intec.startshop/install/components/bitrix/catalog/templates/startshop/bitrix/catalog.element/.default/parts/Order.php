<div class="startshop-price">
	<div class="startshop-current StartShopOffersPrice"><?=$arResult['STARTSHOP']['PRICES']['MINIMAL']['PRINT_VALUE']?></div>
</div>
<?if (empty($arResult['STARTSHOP']['OFFERS'])):?>
    <?if ($arResult['STARTSHOP']['AVAILABLE']):?>
        <?
            $sAddToBasketUrl = $APPLICATION->GetCurPageParam(
                urlencode('CatalogBasketAction').'=Add&'.
                urlencode('CatalogBasketItem').'='.urlencode($arResult['ID']).'&'.
                urlencode('CatalogBasketPrice').'='.urlencode($arResult['STARTSHOP']['PRICES']['MINIMAL']['TYPE']),
                array('CatalogBasketAction', 'CatalogBasketItem', 'CatalogBasketPrice', 'CatalogBasketQuantity')
            );
        ?>
        <div class="startshop-order">
            <div class="startshop-indents-vertical indent-25"></div>
            <div class="startshop-aligner-vertical"></div>
            <?if (!$arResult['STARTSHOP']['BASKET']['INSIDE']):?>
                <div class="startshop-input-numeric">
                    <?$arProductCount = array(
                        "Value" => floatval($arResult['STARTSHOP']['QUANTITY']['RATIO']),
                        "Minimum" => floatval($arResult['STARTSHOP']['QUANTITY']['RATIO']),
                        "Maximum" => floatval($arResult['STARTSHOP']['QUANTITY']['VALUE']),
                        "Ratio" => floatval($arResult['STARTSHOP']['QUANTITY']['RATIO']),
                        "Unlimited" => !$arResult['STARTSHOP']['QUANTITY']['USE'],
                        "ValueType" => "Float"
                    )?>
                    <button class="startshop-input-numeric-button startshop-input-numeric-button-left" onclick="ProductCount.Decrease();">-</button>
                    <input type="text" class="startshop-input-numeric-text ProductCount" onchange="ProductCount.SetValue($(this).val());" value="<?=floatval($arResult['STARTSHOP']['QUANTITY']['RATIO'])?>" />
                    <button class="startshop-input-numeric-button startshop-input-numeric-button-right" onclick="ProductCount.Increase();">+</button>
                    <script type="text/javascript">
                        var ProductCount = new Startshop.Controls.NumericUpDown(<?=CUtil::PhpToJSObject($arProductCount)?>);
                        ProductCount.Settings.Events.OnValueChange = function($oNumeric) {
                            $('.ProductCount').val($oNumeric.GetValue());
                        };
                    </script>
                </div>
                <div class="startshop-indents-horizontal indent-10"></div>
                <div class="startshop-buy">
                    <a rel="nofollow" class="startshop-button startshop-button-standart to-cart" id="to_cart_<?=$arElement['ID']?>" onClick="window.location.href = '<?=$sAddToBasketUrl?>&CatalogBasketQuantity=' + ProductCount.GetValue()">
                        <?=GetMessage("SH_CE_DEFAULT_ADD_TO_BASKET")?>
                    </a>
                </div>
            <?else:?>
                <div class="startshop-buy">
                    <a rel="nofollow" href="<?=$arParams["BASKET_URL"];?>" id="in_cart_<?=$arElement['ID']?>" class="startshop-button startshop-button-standart startshop-status-focus to-cart-added">
                        <?=GetMessage("SH_CE_DEFAULT_ADDED_TO_BASKET")?>
                    </a>
                </div>
            <?endif;?>
			<div class="startshop-indents-horizontal indent-20"></div>
			<div class="min-buttons">
				<div class="min-button compare">
					<div class="add addToCompare addToCompare<?=$arResult["ID"]?>"
						onclick="return addToCompare('<?=SITE_DIR?>', '<?=$arResult['IBLOCK_TYPE_ID']?>','<?=$arResult["IBLOCK_ID"]?>','<?=$arParams["COMPARE_NAME"]?>','<?=$arResult['COMPARE_URL']?>')" 
						title="<?=GetMessage('COMPARE_TEXT_DETAIL')?>"
					>
					</div>
					<div class="remove removeFromCompare removeFromCompare<?=$arResult["ID"]?>"
						style="display:none"
						onclick="return removeFromCompare('<?=SITE_DIR?>', '<?=$arResult['IBLOCK_TYPE_ID']?>','<?=$arResult["IBLOCK_ID"]?>','<?=$arParams["COMPARE_NAME"]?>','<?=$arResult['COMPARE_REMOVE_URL']?>')"
						title="<?=GetMessage('COMPARE_DELETE_TEXT_DETAIL')?>"
					>
					</div>
				</div>
			</div>
        </div>
    <?endif;?>
<?else:?>
    <?foreach ($arResult['STARTSHOP']['OFFERS'] as $arOffer):?>
        <?
            $sAddToBasketUrl = $APPLICATION->GetCurPageParam(
                urlencode('CatalogBasketAction').'=Add&'.
                urlencode('CatalogBasketItem').'='.urlencode($arOffer['ID']).'&'.
                urlencode('CatalogBasketPrice').'='.urlencode($arOffer['STARTSHOP']['PRICES']['MINIMAL']['TYPE']),
                array('CatalogBasketAction', 'CatalogBasketItem', 'CatalogBasketPrice', 'CatalogBasketQuantity')
            );
        ?>
        <div class="startshop-order StartShopOffersOrder<?=$arOffer['ID']?>" style="display: none;">
            <div class="startshop-indents-vertical indent-25"></div>
            <div class="startshop-aligner-vertical"></div>
            <?if (!$arOffer['STARTSHOP']['BASKET']['INSIDE']):?>
                <div class="startshop-input-numeric">
                    <?$arProductCount = array(
                        "Value" => floatval($arOffer['STARTSHOP']['QUANTITY']['RATIO']),
                        "Minimum" => floatval($arOffer['STARTSHOP']['QUANTITY']['RATIO']),
                        "Maximum" => floatval($arOffer['STARTSHOP']['QUANTITY']['VALUE']),
                        "Ratio" => floatval($arOffer['STARTSHOP']['QUANTITY']['RATIO']),
                        "Unlimited" => !$arOffer['STARTSHOP']['QUANTITY']['USE'],
                        "ValueType" => "Float"
                    )?>
                    <button class="startshop-input-numeric-button startshop-input-numeric-button-left" onclick="<?=$sUnique?>_StartShopOffersProductCount_<?=$arOffer['ID']?>.Decrease();">-</button>
                    <input type="text" name="count" class="startshop-input-numeric-text <?=$sUnique?>_StartShopOffersProductCount_<?=$arOffer['ID']?>" onchange="<?=$sUnique?>_StartShopOffersProductCount_<?=$arOffer['ID']?>.SetValue($(this).val());" value="<?=floatval($arResult['STARTSHOP']['QUANTITY']['RATIO'])?>" />
                    <button class="startshop-input-numeric-button startshop-input-numeric-button-right" onclick="<?=$sUnique?>_StartShopOffersProductCount_<?=$arOffer['ID']?>.Increase();">+</button>
                    <script type="text/javascript">
                        var <?=$sUnique?>_StartShopOffersProductCount_<?=$arOffer['ID']?> = new Startshop.Controls.NumericUpDown(<?=CUtil::PhpToJSObject($arProductCount)?>);
                        <?=$sUnique?>_StartShopOffersProductCount_<?=$arOffer['ID']?>.Settings.Events.OnValueChange = function($oNumeric) {
                            $('.<?=$sUnique?>_StartShopOffersProductCount_<?=$arOffer['ID']?>').val($oNumeric.GetValue());
                        };
                    </script>
                </div>
                <div class="startshop-indents-horizontal indent-10"></div>
                <div class="startshop-buy">
                    <a rel="nofollow" class="startshop-button startshop-button-standart to-cart" id="to_cart_<?=$arElement['ID']?>" onClick="window.location.href = '<?=$sAddToBasketUrl?>&CatalogBasketQuantity=' + <?=$sUnique?>_StartShopOffersProductCount_<?=$arOffer['ID']?>.GetValue()">
                        <?=GetMessage("SH_CE_DEFAULT_ADD_TO_BASKET")?>
                    </a>
                </div>
            <?else:?>
                <div class="startshop-buy">
                    <a rel="nofollow" href="<?=$arParams["BASKET_URL"];?>" id="in_cart_<?=$arElement['ID']?>" class="startshop-button startshop-button-standart startshop-status-focus to-cart-added">
                        <?=GetMessage("SH_CE_DEFAULT_ADDED_TO_BASKET")?>
                    </a>
                </div>
            <?endif;?>
			<div class="startshop-indents-horizontal indent-20"></div>
			<div class="min-buttons">
				<div class="min-button compare">
					<div class="add addToCompare addToCompare<?=$arResult["ID"]?>"
						onclick="return addToCompare('<?=SITE_DIR?>', '<?=$arResult['IBLOCK_TYPE_ID']?>','<?=$arResult["IBLOCK_ID"]?>','<?=$arParams["COMPARE_NAME"]?>','<?=$arResult['COMPARE_URL']?>')" 
						title="<?=GetMessage('COMPARE_TEXT_DETAIL')?>"
					>
					</div>
					<div class="remove removeFromCompare removeFromCompare<?=$arResult["ID"]?>"
						style="display:none"
						onclick="return removeFromCompare('<?=SITE_DIR?>', '<?=$arResult['IBLOCK_TYPE_ID']?>','<?=$arResult["IBLOCK_ID"]?>','<?=$arParams["COMPARE_NAME"]?>','<?=$arResult['COMPARE_REMOVE_URL']?>')"
						title="<?=GetMessage('COMPARE_DELETE_TEXT_DETAIL')?>"
					>
					</div>
				</div>
			</div>
        </div>
    <?endforeach;?>
<?endif;?>
