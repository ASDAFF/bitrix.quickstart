<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?$sUniqueID = 'startshop-basket-default-'.spl_object_hash($this);?>
<?$this->setFrameMode(true);?>
<div class="startshop-basket default<?=$arParams['USE_ADAPTABILITY'] == 'Y' ? ' adaptability' : ''?>" id="<?=$sUniqueID?>">
    <?if (!empty($arResult['ITEMS'])):?>
        <div class="startshop-basket-table-wrapper">
            <table class="startshop-basket-table">
                <tr class="startshop-basket-row startshop-basket-row-header">
                    <?if ($arParams['USE_ITEMS_PICTURES'] == 'Y'):?>
                        <td class="startshop-basket-column">
                            <div class="startshop-basket-cell">

                            </div>
                        </td>
                    <?endif;?>
                    <td class="startshop-basket-column">
                        <div class="startshop-basket-cell" style="white-space: nowrap;">
                            <?=GetMessage('SBB_DEFAULT_COLUMN_NAME')?>
                        </div>
                    </td>
                    <td class="startshop-basket-column"></td>
                    <td class="startshop-basket-column">
                        <div class="startshop-basket-cell" style="white-space: nowrap;">
                            <?=GetMessage('SBB_DEFAULT_COLUMN_QUANTITY')?>
                        </div>
                    </td>
                    <td class="startshop-basket-column">
                        <div class="startshop-basket-cell" style="white-space: nowrap;">
                            <?=GetMessage('SBB_DEFAULT_COLUMN_PRICE')?>
                        </div>
                    </td>
                    <td class="startshop-basket-column">
                        <div class="startshop-basket-cell" style="white-space: nowrap;">
                            <?=GetMessage('SBB_DEFAULT_COLUMN_TOTAL')?>
                        </div>
                    </td>
                    <td class="startshop-basket-column">
                        <div class="startshop-basket-cell">

                        </div>
                    </td>
                </tr>
                <?$oFrame = $this->createFrame()->begin();?>
                    <?foreach ($arResult['ITEMS'] as $arItem):?>
                        <tr class="startshop-basket-row">
                            <?if ($arParams['USE_ITEMS_PICTURES'] == 'Y'):?>
                                <td class="startshop-basket-column">
                                    <div class="startshop-basket-cell">
                                        <div class="startshop-image">
                                            <div class="startshop-aligner-vertical"></div>
                                            <img src="<?=$arItem['PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" />
                                        </div>
                                    </div>
                                </td>
                            <?endif;?>
                            <td class="startshop-basket-column">
                                <div class="startshop-basket-cell">
                                    <a class="startshop-link startshop-link-standart" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
                                </div>
                            </td>
                            <td class="startshop-basket-column">
                                <?if ($arItem['STARTSHOP']['OFFER']['OFFER']):?>
                                    <div class="startshop-basket-cell">
                                        <?foreach ($arItem['STARTSHOP']['OFFER']['PROPERTIES'] as $arProperty):?>
                                            <?if ($arProperty['TYPE'] == 'TEXT'):?>
                                                <div class="startshop-basket-property startshop-basket-property-text">
                                                    <div class="startshop-basket-name">
                                                        <?=$arProperty['NAME']?>:
                                                    </div>
                                                    <div class="startshop-basket-value">
                                                        <?=$arProperty['VALUE']['TEXT']?>
                                                    </div>
                                                </div>
                                            <?else:?>
                                                <div class="startshop-basket-property startshop-basket-property-picture">
                                                    <div class="startshop-basket-name">
                                                        <?=$arProperty['NAME']?>:
                                                    </div>
                                                    <div class="startshop-basket-value">
                                                        <div class="startshop-basket-value-wrapper">
                                                            <img src="<?=$arProperty['VALUE']['PICTURE']?>" alt="<?=$arProperty['VALUE']['TEXT']?>" title="<?=$arProperty['VALUE']['TEXT']?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            <?endif;?>
                                        <?endforeach?>
                                    </div>
                                <?endif;?>
                            </td>
                            <td class="startshop-basket-column">
                                <div class="startshop-basket-cell">
                                    <?
                                        $arJSNumeric = array(
                                            "Value" => $arItem['STARTSHOP']['BASKET']['QUANTITY'],
                                            "Minimum" => $arItem['STARTSHOP']['QUANTITY']['RATIO'],
                                            "Ratio" => $arItem['STARTSHOP']['QUANTITY']['RATIO'],
                                            "Maximum" => $arItem['STARTSHOP']['QUANTITY']['VALUE'],
                                            "Unlimited" => !$arItem['STARTSHOP']['QUANTITY']['USE'],
                                            "ValueType" => "Float",
                                        );
                                    ?>
                                    <div class="startshop-input-numeric">
                                        <button class="startshop-input-numeric-button startshop-input-numeric-button-left QuantityDecrease<?=$arItem['ID']?>">-</button>
                                        <input type="text" class="startshop-input-numeric-text QuantityNumeric<?=$arItem['ID']?>" value="<?=$arItem['STARTSHOP']['BASKET']['QUANTITY']?>" />
                                        <button class="startshop-input-numeric-button startshop-input-numeric-button-right QuantityIncrease<?=$arItem['ID']?>">+</button>
                                    </div>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            var Quantity = new Startshop.Controls.NumericUpDown(<?=CUtil::PhpToJSObject($arJSNumeric)?>);
                                            var QuantityIncrease = $('#<?=$sUniqueID?> .QuantityIncrease<?=$arItem['ID']?>');
                                            var QuantityDecrease = $('#<?=$sUniqueID?> .QuantityDecrease<?=$arItem['ID']?>');
                                            var QuantityNumeric = $('#<?=$sUniqueID?> .QuantityNumeric<?=$arItem['ID']?>');

                                            Quantity.Settings.Events.OnValueChange = function ($oNumeric) {
                                                QuantityNumeric.val($oNumeric.GetValue());
                                                Reload();
                                            };

                                            QuantityIncrease.click(function () {
                                                Quantity.Increase();
                                            });

                                            QuantityDecrease.click(function () {
                                                Quantity.Decrease();
                                            });

                                            QuantityNumeric.change(function () {
                                                Quantity.SetValue($(this).val());
                                            });

                                            function Reload() {
                                                window.location.href = Startshop.Functions.stringReplace({'#QUANTITY#': Quantity.GetValue()}, <?=CUtil::PhpToJSObject($arItem['ACTIONS']['SET_QUANTITY'])?>);
                                            }
                                        })
                                    </script>
                                </div>
                            </td>
                            <td class="startshop-basket-column">
                                <div class="startshop-basket-cell" style="white-space: nowrap;">
                                    <?=$arItem['STARTSHOP']['BASKET']['PRICE']['PRINT_VALUE']?>
                                </div>
                            </td>
                            <td class="startshop-basket-column">
                                <div class="startshop-basket-cell" style="white-space: nowrap;">
                                    <?=CStartShopCurrency::FormatAsString($arItem['STARTSHOP']['BASKET']['PRICE']['VALUE'] * $arItem['STARTSHOP']['BASKET']['QUANTITY'], $arParams['CURRENCY'])?>
                                </div>
                            </td>
                            <td class="startshop-basket-column">
                                <div class="startshop-basket-cell">
                                    <a class="startshop-button-custom startshop-button-delete" href="<?=$arItem['ACTIONS']['DELETE']?>"></a>
                                </div>
                            </td>
                        </tr>
                    <?endforeach;?>
                <?$oFrame->end();?>
            </table>
        </div>
        <?if ($arParams['USE_SUM_FIELD'] == 'Y'):?>
            <div class="startshop-indents-vertical indent-15"></div>
            <div class="startshop-basket-sum">
                <?=GetMessage("SBB_DEFAULT_FIELD_SUM")?>: <?=$arResult['SUM']['PRINT_VALUE']?>
            </div>
        <?endif;?>
        <?if ($arParams['USE_BUTTON_ORDER'] == "Y" || $arParams['USE_BUTTON_CLEAR'] == "Y"):?>
            <div class="startshop-indents-vertical indent-15"></div>
            <div class="startshop-basket-buttons">
                <div class="startshop-basket-buttons-wrapper">
                    <?if ($arParams['USE_BUTTON_ORDER'] == "Y"):?>
                        <a class="startshop-button startshop-button-standart" href="<?=$arParams['URL_ORDER']?>"><?=GetMessage("SBB_DEFAULT_BUTTON_ORDER")?></a>
                    <?endif;?>
                    <?if ($arParams['USE_BUTTON_CLEAR'] == "Y"):?>
                        <a class="startshop-button startshop-button-standart" href="<?=$arResult['ACTIONS']['CLEAR']?>"><?=GetMessage("SBB_DEFAULT_BUTTON_CLEAR")?></a>
                    <?endif;?>
                </div>
            </div>
        <?endif?>
    <?else:?>
        <div class="startshop-basket-notify">
            <div class="startshop-basket-notify-wrapper">
                <?=GetMessage('SBB_DEFAULT_NOTIFIES_ITEMS_EMPTY')?>
            </div>
        </div>
    <?endif;?>
</div>
