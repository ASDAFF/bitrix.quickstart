<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<div class="startshop-orders-detail default<?=$arParams['USE_ADAPTABILITY'] == "Y" ? " adaptiv" : ""?>">
    <?$frame = $this->createFrame()->begin();?>
        <?if (!empty($arResult)):?>
            <div class="startshop-orders-detail-section">
                <div class="startshop-orders-detail-section-title"><?=GetMessage('SOD_DEFAULT_SECTION_COMMON')?></div>
                <div class="startshop-orders-detail-section-content">
                    <div class="startshop-orders-detail-section-content-row">
                        <b><?=GetMessage('SOD_DEFAULT_SECTION_PROPERTY_ID')?></b>: <?=htmlspecialcharsbx($arResult['ID'])?>
                    </div>
                    <?if (!empty($arResult['STATUS'])):?>
                        <div class="startshop-orders-detail-section-content-row">
                            <b><?=GetMessage('SOD_DEFAULT_SECTION_PROPERTY_STATUS')?></b>: <?=htmlspecialcharsbx($arResult['STATUS']['LANG'][LANGUAGE_ID]['NAME'])?>
                        </div>
                    <?endif;?>
                    <div class="startshop-orders-detail-section-content-row">
                        <b><?=GetMessage('SOD_DEFAULT_SECTION_PROPERTY_AMOUNT')?></b>: <?=$arResult['AMOUNT']['PRINT_VALUE']?>
                    </div>
                    <?foreach ($arResult['PROPERTIES'] as $arProperty):?>
                        <?if (empty($arProperty['VALUE'])) continue;?>
                        <div class="startshop-orders-detail-section-content-row">
                            <b><?=htmlspecialcharsbx($arProperty['LANG'][LANGUAGE_ID]['NAME'])?></b>: <?=htmlspecialcharsbx($arProperty['VALUE'])?>
                        </div>
                    <?endforeach;?>
                </div>
            </div>
            <?if (!empty($arResult['ITEMS'])):?>
                <div class="startshop-orders-detail-section">
                    <div class="startshop-orders-detail-section-title"><?=GetMessage('SOD_DEFAULT_SECTION_COMMON_POSITIONS')?></div>
                    <div class="startshop-orders-detail-section-content">
                        <div class="startshop-orders-detail-section-content-table-wrapper">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="startshop-orders-detail-section-content-table">
                                <tr class="startshop-orders-detail-section-content-table-row startshop-orders-detail-section-content-table-row-filled">
                                    <td class="startshop-orders-detail-section-content-table-row-column">
                                        <div class="startshop-orders-detail-section-content-table-row-column-indents">
                                            <?=GetMessage('SOD_DEFAULT_SECTION_COLUMN_NAME')?>
                                        </div>
                                    </td>
                                    <td class="startshop-orders-detail-section-content-table-row-column"></td>
                                    <td class="startshop-orders-detail-section-content-table-row-column">
                                        <div class="startshop-orders-detail-section-content-table-row-column-indents">
                                            <?=GetMessage('SOD_DEFAULT_SECTION_COLUMN_PRICE')?>
                                        </div>
                                    </td>
                                    <td class="startshop-orders-detail-section-content-table-row-column">
                                        <div class="startshop-orders-detail-section-content-table-row-column-indents">
                                            <?=GetMessage('SOD_DEFAULT_SECTION_COLUMN_COUNT')?>
                                        </div>
                                    </td>
                                    <td class="startshop-orders-detail-section-content-table-row-column">
                                        <div class="startshop-orders-detail-section-content-table-row-column-indents">
                                            <?=GetMessage('SOD_DEFAULT_SECTION_COLUMN_SUM')?>
                                        </div>
                                    </td>
                                </tr>
                                <?$iCurrentItem = 0;?>
                                <?foreach ($arResult['ITEMS'] as $iKey => $arItem):?>
                                    <tr class="startshop-orders-detail-section-content-table-row<?=$iCurrentItem++ & 1 != 0 ? ' startshop-orders-detail-section-content-table-row-filled' : ''?>">
                                        <td class="startshop-orders-detail-section-content-table-row-column">
                                            <div class="startshop-orders-detail-section-content-table-row-column-indents"><?=$arItem['NAME']?></div>
                                        </td>
                                        <td class="startshop-orders-detail-section-content-table-row-column">
                                            <?if ($arItem['ELEMENT']['STARTSHOP']['OFFER']['OFFER']):?>
                                                <div class="startshop-orders-detail-section-content-table-row-column-indents">
                                                    <?foreach ($arItem['ELEMENT']['STARTSHOP']['OFFER']['PROPERTIES'] as $arProperty):?>
                                                        <?if ($arProperty['TYPE'] == 'TEXT'):?>
                                                            <div class="startshop-orders-detail-property startshop-orders-detail-property-text">
                                                                <div class="startshop-orders-detail-name">
                                                                    <?=$arProperty['NAME']?>:
                                                                </div>
                                                                <div class="startshop-orders-detail-value">
                                                                    <?=$arProperty['VALUE']['TEXT']?>
                                                                </div>
                                                            </div>
                                                        <?else:?>
                                                            <div class="startshop-orders-detail-property startshop-orders-detail-property-picture">
                                                                <div class="startshop-orders-detail-name">
                                                                    <?=$arProperty['NAME']?>:
                                                                </div>
                                                                <div class="startshop-orders-detail-value">
                                                                    <div class="startshop-orders-detail-value-wrapper">
                                                                        <img src="<?=$arProperty['VALUE']['PICTURE']?>" alt="<?=$arProperty['VALUE']['TEXT']?>" title="<?=$arProperty['VALUE']['TEXT']?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?endif;?>
                                                    <?endforeach?>
                                                </div>
                                            <?endif;?>
                                        </td>
                                        <td class="startshop-orders-detail-section-content-table-row-column">
                                            <div class="startshop-orders-detail-section-content-table-row-column-indents"><?=$arItem['PRICE']['PRINT_VALUE']?></div>
                                        </td>
                                        <td class="startshop-orders-detail-section-content-table-row-column">
                                            <div class="startshop-orders-detail-section-content-table-row-column-indents"><?=$arItem['QUANTITY']?></div>
                                        </td>
                                        <td class="startshop-orders-detail-section-content-table-row-column">
                                            <div class="startshop-orders-detail-section-content-table-row-column-indents"><?=$arItem['AMOUNT']['PRINT_VALUE']?></div>
                                        </td>
                                    </tr>
                                <?endforeach;?>
                                <?unset($iCurrentItem);?>
                            </table>
                        </div>
                    </div>
                </div>
            <?endif;?>
            <?if (!empty($arResult['DELIVERY'])):?>
                <div class="startshop-orders-detail-section">
                    <div class="startshop-orders-detail-section-title"><?=GetMessage('SOD_DEFAULT_SECTION_DELIVERY')?></div>
                    <div class="startshop-orders-detail-section-content">
                        <div class="startshop-orders-detail-section-content-row">
                            <b><?=GetMessage('SOD_DEFAULT_SECTION_PROPERTY_DELIVERY')?></b>: <?=$arResult['DELIVERY']['LANG'][LANGUAGE_ID]['NAME']?>
                        </div>
                        <?if ($arResult['DELIVERY']['PRICE']['VALUE'] > 0):?>
                            <div class="startshop-orders-detail-section-content-row">
                                <b><?=GetMessage('SOD_DEFAULT_SECTION_PROPERTY_DELIVERY_PRICE')?></b>: <?=$arResult['DELIVERY']['PRICE']['PRINT_VALUE']?>
                            </div>
                        <?endif;?>
                        <?foreach ($arResult['DELIVERY']['PROPERTIES'] as $arProperty):?>
                            <?if (empty($arProperty['VALUE'])) continue;?>
                            <div class="startshop-orders-detail-section-content-row">
                                <b><?=htmlspecialcharsbx($arProperty['LANG'][LANGUAGE_ID]['NAME'])?></b>: <?=htmlspecialcharsbx($arProperty['VALUE'])?>
                            </div>
                        <?endforeach;?>
                    </div>
                </div>
            <?endif;?>
            <?if (!empty($arResult['PAYMENT'])):?>
                <div class="startshop-orders-detail-section">
                    <div class="startshop-orders-detail-section-title"><?=GetMessage('SOD_DEFAULT_SECTION_PAYMENT')?></div>
                    <div class="startshop-orders-detail-section-content">
                        <div class="startshop-orders-detail-section-content-row">
                            <b><?=GetMessage('SOD_DEFAULT_SECTION_PROPERTY_PAYMENT')?></b>: <?=$arResult['PAYMENT']['LANG'][LANGUAGE_ID]['NAME']?>
                        </div>
                        <div class="startshop-orders-detail-section-content-row">
                            <b><?=GetMessage('SOD_DEFAULT_SECTION_PROPERTY_PAYED')?></b>: <?=$arResult['PAYED'] == 'Y' ? GetMessage('SOD_DEFAULT_SECTION_PROPERTY_PAYED_YES') : GetMessage('SOD_DEFAULT_SECTION_PROPERTY_PAYED_NO')?>
                        </div>
                        <?if (!empty($arResult['PAYMENT']['HANDLER']) && $arResult['PAYED'] != 'Y' && $arResult['STATUS']['CAN_PAY'] == 'Y' && $arResult['PAYMENT']['ACTIVE'] == 'Y'):?>
                            <div class="startshop-orders-detail-section-content-row startshop-orders-section-content-row-single">
                                <?CStartShopPayment::ShowPayForm($arResult['PAYMENT']['ID'], array(
                                    "BUTTON_NAME" => GetMessage('SOD_DEFAULT_SECTION_PROPERTY_PAYMENT_BUTTON')." (".$arResult['AMOUNT']['PRINT_VALUE'].")",
                                    "BUTTON_CLASS" => "startshop-button startshop-button-standart",
                                    "ORDER_ID" => $arResult['ID'],
                                    "ORDER_SUM" => CStartShopCurrency::Convert($arResult['~AMOUNT'], $arResult['~CURRENCY'], $arResult['PAYMENT']['CURRENCY']),
                                    "ORDER_ITEMS" => array_keys($arResult["ITEMS"]),
                                    "CULTURE" => LANGUAGE_ID
                                ))?>
                            </div>
                        <?endif;?>
                    </div>
                </div>
            <?endif;?>
        <?else:?>
            <div class="startshop-orders-detail-notify startshop-orders-detail-notify-red">
                <div class="startshop-orders-detail-notify-wrapper">
                    <?=GetMessage('SOD_DEFAULT_NOTIFY_ORDDER_NOT_EXISTS')?>
                </div>
            </div>
        <?endif;?>
    <?$frame->beginStub();?>
        <div class="startshop-orders-detail-notify startshop-orders-detail-notify-red">
            <div class="startshop-orders-detail-notify-wrapper">
                <?=GetMessage('SOD_DEFAULT_NOTIFY_ORDDER_NOT_EXISTS')?>
            </div>
        </div>
    <?$frame->end();?>
</div>
