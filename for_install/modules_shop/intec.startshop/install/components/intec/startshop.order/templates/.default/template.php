<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?global $USER;?>
<?$sUniqueID = 'startshop_order_'.spl_object_hash($this);?>
<?
    $arUser = array();

    if ($USER->IsAuthorized())
        $arUser = CUser::GetByID($USER->GetID())->Fetch();
?>
<?$fPropertyDraw = function ($arProperty, $arUser = array()) {?>
    <?
        $sValue = '';

        if (isset($_REQUEST['PROPERTY_'.$arProperty['ID']])) {
            $sValue = $_REQUEST['PROPERTY_'.$arProperty['ID']];
        } else {
            if (!empty($arProperty['USER_FIELD']))
                if (!empty($arUser))
                    if (!empty($arUser[$arProperty['USER_FIELD']]))
                        $sValue = $arUser[$arProperty['USER_FIELD']];
        }
    ?>
    <?if ($arProperty['TYPE'] == "S" && empty($arProperty['SUBTYPE'])):?>
        <div class="startshop-order-property">
            <div class="startshop-order-name">
                <?=$arProperty['LANG'][LANGUAGE_ID]['NAME']?>:
                <?if ($arProperty['REQUIRED'] == 'Y'):?>
                    <span class="startshop-order-required">*</span>
                <?endif;?>
            </div>
            <div class="startshop-order-field">
                <input type="text"<?=$arProperty['DATA']['LENGTH'] > 0 ? ' maxlength="'.$arProperty['DATA']['LENGTH'].'"' : ''?> class="startshop-input-text startshop-input-text-standart" name="PROPERTY_<?=$arProperty['ID']?>" value="<?=htmlspecialcharsbx($sValue)?>" />
                <?if (!empty($arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'])):?>
                    <div class="startshop-order-field-description"><?=$arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION']?></div>
                <?endif;?>
            </div>
        </div>
    <?elseif ($arProperty['TYPE'] == "S" && $arProperty['SUBTYPE'] == "TEXT"):?>
        <div class="startshop-order-property">
            <div class="startshop-order-name">
                <?=$arProperty['LANG'][LANGUAGE_ID]['NAME']?>:
                <?if ($arProperty['REQUIRED'] == 'Y'):?>
                    <span class="startshop-order-required">*</span>
                <?endif;?>
            </div>
            <div class="startshop-order-field">
                <textarea class="startshop-input-textarea startshop-input-textarea-standart" name="PROPERTY_<?=$arProperty['ID']?>"><?=htmlspecialcharsbx($sValue)?></textarea>
                <?if (!empty($arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'])):?>
                    <div class="startshop-order-field-description"><?=$arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION']?></div>
                <?endif;?>
            </div>
        </div>
    <?elseif ($arProperty['TYPE'] == 'B' && empty($arProperty['SUBTYPE'])):?>
        <div class="startshop-order-property">
            <div class="startshop-order-name">
                <?=$arProperty['LANG'][LANGUAGE_ID]['NAME']?>:
                <?/*if ($arProperty['REQUIRED'] == 'Y'):?>
                    <span class="startshop-order-required">*</span>
                <?endif;*/?>
            </div>
            <div class="startshop-order-field">
                <div style="padding-top: 7px;"></div>
                <input type="hidden" value="N" name="PROPERTY_<?=$arProperty['ID']?>" />
                <label class="startshop-button-checkbox">
                    <input type="checkbox" value="Y" name="PROPERTY_<?=$arProperty['ID']?>"<?=$sValue == 'Y' ? ' checked="checked"' : ''?> />
                    <div class="selector"></div>
                </label>
                <?if (!empty($arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'])):?>
                    <div class="startshop-order-field-description"><?=$arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION']?></div>
                <?endif;?>
            </div>
        </div>
    <?elseif ($arProperty['TYPE'] == 'L' && $arProperty['SUBTYPE'] == 'IBLOCK_ELEMENT'):?>
        <div class="startshop-order-property">
            <div class="startshop-order-name">
                <?=$arProperty['LANG'][LANGUAGE_ID]['NAME']?>:
                <?if ($arProperty['REQUIRED'] == 'Y'):?>
                    <span class="startshop-order-required">*</span>
                <?endif;?>
            </div>
            <div class="startshop-order-field">
                <select name="PROPERTY_<?=$arProperty['ID']?>" class="startshop-input-select startshop-input-select-standart">
                    <?foreach ($arProperty['VALUES'] as $iPropertyKey => $arPropertyValue):?>
                        <option value="<?=$iPropertyKey?>"<?=$sValue == $iPropertyKey ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx($arPropertyValue['NAME'])?></option>
                    <?endforeach;?>
                </select>
                <?if (!empty($arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'])):?>
                    <div class="startshop-order-field-description"><?=$arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION']?></div>
                <?endif;?>
            </div>
        </div>
    <?endif;?>
<?}?>
<div class="startshop-order default<?=$arParams['USE_ADAPTABILITY'] == 'Y' ? ' adaptability' : ''?>" id="<?=$sUniqueID?>">
    <?foreach ($arResult['ERRORS'] as $arError):?>
        <?if ($arError['CODE'] == "DELIVERY_EMPTY"):?>
            <div class="startshop-order-notify startshop-order-notify-red">
                <div class="startshop-order-notify-wrapper">
                    <?=GetMessage('SO_DEFAULT_ERRORS_DELIVERY_EMPTY');?>
                </div>
            </div>
        <?elseif ($arError['CODE'] == "PAYMENT_EMPTY"):?>
            <div class="startshop-order-notify startshop-order-notify-red">
                <div class="startshop-order-notify-wrapper">
                    <?=GetMessage('SO_DEFAULT_ERRORS_PAYMENT_EMPTY');?>
                </div>
            </div>
        <?elseif ($arError['CODE'] == "PROPERTIES_EMPTY"):?>
            <div class="startshop-order-notify startshop-order-notify-red">
                <div class="startshop-order-notify-wrapper">
                    <?
                        $arPropertiesEmpty = array();

                        foreach ($arError['PROPERTIES'] as $arProperty)
                            $arPropertiesEmpty[] = $arProperty['LANG'][LANGUAGE_ID]['NAME'];
                    ?>
                    <?=GetMessage('SO_DEFAULT_ERRORS_PROPERTIES_EMPTY', array('#FIELDS#' => '<b>"'.implode('"</b>, <b>"', $arPropertiesEmpty).'"</b>'));?>
                </div>
            </div>
        <?endif;?>
    <?endforeach;?>
    <?if (!empty($arResult['ITEMS'])):?>
        <form method="POST">
            <?$oFrame = $this->createFrame()->begin();?>
                <input type="hidden" name="<?=$arParams['REQUEST_VARIABLE_ACTION']?>" value="order" />
                <?if (!empty($arResult['ITEMS'])):?>
                    <?if (!empty($arResult['PROPERTIES'])):?>
                        <div class="startshop-order-section">
                            <div class="startshop-order-caption"><?=GetMessage('SO_DEFAULT_SECTIONS_PROPERTIES')?></div>
                            <div class="startshop-order-properties">
                                <?foreach ($arResult['PROPERTIES'] as $arProperty):?>
                                    <?$fPropertyDraw($arProperty, $arUser);?>
                                <?endforeach;?>
                            </div>
                        </div>
                    <?endif;?>
                <?endif;?>
                <?if (!empty($arResult['DELIVERIES'])):?>
                    <div class="startshop-order-section">
                        <div class="startshop-order-caption"><?=GetMessage('SO_DEFAULT_SECTIONS_DELIVERIES')?></div>
                        <div class="startshop-order-properties">
                            <div class="startshop-order-property startshop-order-property-full">
                                <div class="startshop-order-name">
                                    <?=GetMessage('SO_DEFAULT_SECTIONS_DELIVERIES_DELIVERY')?>:
                                    <span class="startshop-order-required">*</span>
                                </div>
                                <div class="startshop-order-field">
                                    <select name="DELIVERY" class="startshop-input-select startshop-input-select-standart">
                                        <?foreach ($arResult['DELIVERIES'] as $iDeliveryKey => $arDelivery):?>
                                            <option value="<?=$iDeliveryKey?>"<?=$_REQUEST['DELIVERY'] == $iDeliveryKey ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx($arDelivery['LANG'][LANGUAGE_ID]['NAME']).' ('.($arDelivery['PRICE']['VALUE'] > 0 ? $arDelivery['PRICE']['PRINT_VALUE'] : GetMessage('SO_DEFAULT_SECTIONS_DELIVERIES_DELIVERY_FREE')).')'?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                            <?foreach ($arResult['DELIVERIES_PROPERTIES'] as $arDeliveryProperty):?>
                                    <?$fPropertyDraw($arDeliveryProperty, $arUser);?>
                            <?endforeach;?>
                        </div>
                    </div>
                <?endif;?>
                <?if (!empty($arResult['PAYMENTS'])):?>
                    <div class="startshop-order-section">
                        <div class="startshop-order-caption"><?=GetMessage('SO_DEFAULT_SECTIONS_PAYMENTS')?></div>
                        <div class="startshop-order-properties">
                            <div class="startshop-order-property startshop-order-property-full">
                                <div class="startshop-order-name">
                                    <?=GetMessage('SO_DEFAULT_SECTIONS_PAYMENTS_PAYMENT')?>:
                                    <span class="startshop-order-required">*</span>
                                </div>
                                <div class="startshop-order-field">
                                    <select name="PAYMENT" class="startshop-input-select startshop-input-select-standart">
                                        <?foreach ($arResult['PAYMENTS'] as $iPaymentKey => $arPayment):?>
                                            <option value="<?=$iPaymentKey?>" <?=$_REQUEST['PAYMENT'] == $iPaymentKey ? ' selected="selected"' : ''?>><?=htmlspecialcharsbx($arPayment['LANG'][LANGUAGE_ID]['NAME'])?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                <?endif;?>
                <div class="startshop-order-section">
                    <div class="startshop-order-caption"><?=GetMessage('SO_DEFAULT_SECTIONS_ITEMS')?></div>
                    <div class="startshop-order-table-wrapper">
                        <table class="startshop-order-table">
                            <tr class="startshop-order-row startshop-order-row-header">
                                <?if ($arParams['USE_ITEMS_PICTURES'] == 'Y'):?>
                                    <td class="startshop-order-column">
                                        <div class="startshop-order-cell">

                                        </div>
                                    </td>
                                <?endif;?>
                                <td class="startshop-order-column">
                                    <div class="startshop-order-cell" style="white-space: nowrap;">
                                        <?=GetMessage('SO_DEFAULT_SECTIONS_ITEMS_COLUMN_NAME')?>
                                    </div>
                                </td>
                                <td class="startshop-order-column"></td>
                                <td class="startshop-order-column">
                                    <div class="startshop-order-cell" style="white-space: nowrap;">
                                        <?=GetMessage('SO_DEFAULT_SECTIONS_ITEMS_COLUMN_QUANTITY')?>
                                    </div>
                                </td>
                                <td class="startshop-order-column">
                                    <div class="startshop-order-cell" style="white-space: nowrap;">
                                        <?=GetMessage('SO_DEFAULT_SECTIONS_ITEMS_COLUMN_PRICE')?>
                                    </div>
                                </td>
                                <td class="startshop-order-column">
                                    <div class="startshop-order-cell" style="white-space: nowrap;">
                                        <?=GetMessage('SO_DEFAULT_SECTIONS_ITEMS_COLUMN_TOTAL')?>
                                    </div>
                                </td>
                            </tr>
                            <?foreach ($arResult['ITEMS'] as $arItem):?>
                                <tr class="startshop-order-row">
                                    <?if ($arParams['USE_ITEMS_PICTURES'] == 'Y'):?>
                                        <td class="startshop-order-column">
                                            <div class="startshop-order-cell">
                                                <div class="startshop-image">
                                                    <div class="startshop-aligner-vertical"></div>
                                                    <img src="<?=$arItem['PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" />
                                                </div>
                                            </div>
                                        </td>
                                    <?endif;?>
                                    <td class="startshop-order-column">
                                        <div class="startshop-order-cell">
                                            <a class="startshop-link startshop-link-standart" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
                                        </div>
                                    </td>
                                    <td class="startshop-basket-column">
                                        <?if ($arItem['STARTSHOP']['OFFER']['OFFER']):?>
                                            <div class="startshop-order-cell">
                                                <?foreach ($arItem['STARTSHOP']['OFFER']['PROPERTIES'] as $arProperty):?>
                                                    <?if ($arProperty['TYPE'] == 'TEXT'):?>
                                                        <div class="startshop-order-property startshop-order-property-text">
                                                            <div class="startshop-order-name">
                                                                <?=$arProperty['NAME']?>:
                                                            </div>
                                                            <div class="startshop-order-value">
                                                                <?=$arProperty['VALUE']['TEXT']?>
                                                            </div>
                                                        </div>
                                                    <?else:?>
                                                        <div class="startshop-order-property startshop-order-property-picture">
                                                            <div class="startshop-order-name">
                                                                <?=$arProperty['NAME']?>:
                                                            </div>
                                                            <div class="startshop-order-value">
                                                                <div class="startshop-order-value-wrapper">
                                                                    <img src="<?=$arProperty['VALUE']['PICTURE']?>" alt="<?=$arProperty['VALUE']['TEXT']?>" title="<?=$arProperty['VALUE']['TEXT']?>" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?endif;?>
                                                <?endforeach?>
                                            </div>
                                        <?endif;?>
                                    </td>
                                    <td class="startshop-order-column">
                                        <div class="startshop-order-cell" style="white-space: nowrap;">
                                            <?=$arItem['STARTSHOP']['BASKET']['QUANTITY']?>
                                        </div>
                                    </td>
                                    <td class="startshop-order-column">
                                        <div class="startshop-order-cell" style="white-space: nowrap;">
                                            <?=$arItem['STARTSHOP']['PRICES']['MINIMAL']['PRINT_VALUE']?>
                                        </div>
                                    </td>
                                    <td class="startshop-order-column">
                                        <div class="startshop-order-cell" style="white-space: nowrap;">
                                            <?=CStartShopCurrency::FormatAsString($arItem['STARTSHOP']['PRICES']['MINIMAL']['VALUE'] * $arItem['STARTSHOP']['BASKET']['QUANTITY'], $arParams['CURRENCY'])?>
                                        </div>
                                    </td>
                                </tr>
                            <?endforeach;?>
                        </table>
                    </div>
                </div>
                <div class="startshop-order-total">
                    <div class="startshop-order-field"><?=GetMessage('SO_DEFAULT_TOTAL_ITEMS')?>: <span class="startshop-order-field-value startshop-order-field-value-items"><?=$arResult['SUM']['PRINT_VALUE']?></span></div>
                    <?if (!empty($arResult['DELIVERIES'])):?>
                        <div class="startshop-order-field"><?=GetMessage('SO_DEFAULT_TOTAL_DELIVERY')?>: <span class="startshop-order-field-value startshop-order-field-value-delivery"></span></div>
                    <?endif;?>
                    <div class="startshop-order-field"><?=GetMessage('SO_DEFAULT_TOTAL')?>: <span class="startshop-order-field-value startshop-order-field-value-total"><?=$arResult['SUM']['PRINT_VALUE']?></span></div>
                </div>
                <div class="startshop-clear"></div>
                <div class="startshop-order-buttons">
                    <div class="startshop-order-buttons-wrapper">
                        <input type="submit" class="startshop-button startshop-button-standart" value="<?=GetMessage('SO_DEFAULT_BUTTONS_ORDER')?>" />
                        <?if ($arParams['USE_BUTTON_BASKET'] == "Y"):?>
                            <a class="startshop-button startshop-button-standart" href="<?=$arParams['URL_BASKET']?>"><?=GetMessage("SO_DEFAULT_BUTTONS_BASKET")?></a>
                        <?endif;?>
                    </div>
                </div>
                <script type="text/javascript">
                    $('document').ready(function(){
                        var $oRoot = $('#<?=$sUniqueID?>');
                        var $oProperties = <?=!empty($arResult['PROPERTIES']) ? CUtil::PhpToJSObject($arResult['PROPERTIES']) : '{}'?>;
                        var $oDeliveries = <?=!empty($arResult['DELIVERIES']) ? CUtil::PhpToJSObject($arResult['DELIVERIES']) : '{}'?>;
                        var $oPayments = <?=!empty($arResult['PAYMENTS']) ? CUtil::PhpToJSObject($arResult['PAYMENTS']) : '{}'?>;
                        var $oCurrency = <?=!empty($arResult['CURRENCY']) ? CUtil::PhpToJSObject($arResult['CURRENCY']) : 'null'?>;
                        var $sLanguageID = <?=CUtil::PhpToJSObject(LANGUAGE_ID)?>;

                        var $oItemsSum = <?=CUtil::PhpToJSObject($arResult['SUM'])?>;

                        function UpdateForm() {
                            var $iCurrentDelivery = $oRoot.find('[name=DELIVERY]').val();
                            var $fDeliverySum = 0;
                            Startshop.Functions.forEach($oDeliveries, function($iKey, $oDelivery) {
                                Startshop.Functions.forEach($oDelivery['PROPERTIES'], function ($iDeliveryPropertyKey, $oDeliveryProperty) {
                                    $oRoot.find('[name=PROPERTY_' + $iDeliveryPropertyKey + ']').parents('div.startshop-order-property').hide();
                                });
                            });

                            if ($iCurrentDelivery !== undefined) {
                                Startshop.Functions.forEach($oDeliveries[$iCurrentDelivery]['PROPERTIES'], function($iDeliveryPropertyKey, $oDeliveryProperty) {
                                    $oRoot.find('[name=PROPERTY_' + $iDeliveryPropertyKey + ']').parents('div.startshop-order-property').show();
                                });

                                $fDeliverySum = parseFloat($oDeliveries[$iCurrentDelivery]['PRICE']['VALUE']);
                            }

                            var $fTotalSum = parseFloat($oItemsSum['VALUE']) + parseFloat($fDeliverySum);

                            var $oFieldDelivery = $oRoot.find('.startshop-order-field-value.startshop-order-field-value-delivery');
                            var $oFieldTotal = $oRoot.find('.startshop-order-field-value.startshop-order-field-value-total');

                            if ($oCurrency != null) {
                                $fDeliverySum = Startshop.Functions.stringReplace(
                                    {'#': Startshop.Functions.numberFormat($fDeliverySum, $oCurrency['FORMAT'][$sLanguageID]['DECIMALS_COUNT'], $oCurrency['FORMAT'][$sLanguageID]['DELIMITER_DECIMAL'], $oCurrency['FORMAT'][$sLanguageID]['DELIMITER_THOUSANDS'])},
                                    $oCurrency['FORMAT'][$sLanguageID]['FORMAT']
                                );

                                $fTotalSum = Startshop.Functions.stringReplace(
                                    {'#': Startshop.Functions.numberFormat($fTotalSum, $oCurrency['FORMAT'][$sLanguageID]['DECIMALS_COUNT'], $oCurrency['FORMAT'][$sLanguageID]['DELIMITER_DECIMAL'], $oCurrency['FORMAT'][$sLanguageID]['DELIMITER_THOUSANDS'])},
                                    $oCurrency['FORMAT'][$sLanguageID]['FORMAT']
                                );
                            }

                            $oFieldDelivery.html($fDeliverySum);
                            $oFieldTotal.html($fTotalSum);
                        }

                        $oRoot.find('[name=DELIVERY]').change(function () {
                            UpdateForm();
                        });

                        UpdateForm();
                    });
                </script>
            <?$oFrame->end();?>
        </form>
    <?else:?>
        <?if (is_numeric($arResult['ORDER'])):?>
            <div class="startshop-order-notify startshop-order-notify-green">
                <div class="startshop-order-notify-wrapper">
                    <?=GetMessage('SO_DEFAULT_NOTIFIES_ORDER_CREATED', array('#NUMBER#' => $arResult['ORDER']))?>
                </div>
            </div>
        <?else:?>
            <div class="startshop-order-notify">
                <div class="startshop-order-notify-wrapper">
                    <?=GetMessage('SO_DEFAULT_NOTIFIES_ITEMS_EMPTY')?>
                </div>
            </div>
        <?endif;?>
    <?endif;?>
</div>
<?
    unset($fPropertyDraw);
?>
