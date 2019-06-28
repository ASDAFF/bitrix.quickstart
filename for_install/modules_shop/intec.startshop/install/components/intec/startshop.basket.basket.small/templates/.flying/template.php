<?global $APPLICATION?>
<?$this->setFrameMode(true)?>
<?$sUniqueID = 'startshop_basket_small_fly_'.spl_object_hash($this);?>
<?$frame = $this->createFrame()->begin()?>
<div class="startshop-basket-small flying" id="<?=$sUniqueID?>">
	<div class="startshop-basket-small-overlay" onClick="return StartShopFlyBasket.switchSectionByID('StartshopFlyingBasket')">
		<div class="startshop-aligner-vertical"></div>
		<div class="startshop-text startshop-element-text"><?=$arResult['COUNT']?></div>
		<div class="startshop-icon"></div>
	</div>
	<div class="startshop-basket-small-container">
		<div class="startshop-basket-fly-sections">
			<div class="startshop-basket-fly-section<?=empty($arResult['ITEMS']) ? ' startshop-empty' : ''?>" id="StartshopFlyingBasket">
				<?if (!empty($arResult['ITEMS'])):?>
					<div class="startshop-header startshop-element-text">
						<?=GetMessage('SBBS_FLYING_PRODUCTS')?> <?=$arResult['COUNT']?> &mdash; <?=$arResult['SUM']['PRINT_VALUE']?>
					</div>
					<div style="overflow: auto; height: 240px;">
						<table width="100%" class="startshop-products">
							<tr class="startshop-products-header">
								<th style="width: 20px;"><div style="width: 20px;"></div></th>
								<th style="white-space: nowrap;"></th>
								<th style="width: 100%;"><div class="startshop-wrapper"><?=GetMessage('SBBS_FLYING_FIELD_NAME')?></div></th>
								<th style="width: 100%;"></th>
								<th style="white-space: nowrap;"><div class="startshop-wrapper"><?=GetMessage('SBBS_FLYING_FIELD_PRICE')?></div></th>
								<th style="white-space: nowrap;"><div class="startshop-wrapper"><?=GetMessage('SBBS_FLYING_FIELD_COUNT')?></div></th>
								<th style="white-space: nowrap;"><div class="startshop-wrapper"><?=GetMessage('SBBS_FLYING_FIELD_TOTAL')?></div></th>
								<th style="white-space: nowrap;"></th>
								<th style="width: 20px;"><div style="width: 20px;"></div></th>
							</tr>
							<?foreach ($arResult['ITEMS'] as $arItem):?>
								<?
									$image = $this->GetFolder().'/images/product.noimage.png';
									
									if (!empty($arItem['PREVIEW_PICTURE']))
									{
										$image = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width' => '70', 'height' => '70'), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
										$image = $image['src'];
									}
									else if (!empty($arItem['DETAIL_PICTURE']))
									{
										$image = CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], array('width' => '70', 'height' => '70'), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
										$image = $image['src'];
									}
								?>
								<tr>
									<td style="width: 20px;"><div style="width: 20px;"></div></td>
									<td style="white-space: nowrap;">
										<div class="startshop-wrapper">
											<div class="startshop-image" style="width: 70px; height: 70px;">
												<div class="startshop-aligner-vertical"></div>
												<img src="<?=$image?>" />
											</div>
										</div>
									</td>
									<td>
										<div class="startshop-wrapper">
											<a class="startshop-link startshop-link-standart" href="<?=$arItem['DETAIL_PAGE_URL']?>" style="text-decoration: none"><?=$arItem['NAME']?></a>
										</div>
									</td>
                                    <td style="white-space: nowrap;">
                                        <div class="startshop-wrapper">
                                            <?if ($arItem['STARTSHOP']['OFFER']['OFFER']):?>
                                                <div class="startshop-wrapper">
                                                    <?foreach ($arItem['STARTSHOP']['OFFER']['PROPERTIES'] as $arProperty):?>
                                                        <?if ($arProperty['TYPE'] == 'TEXT'):?>
                                                            <div class="startshop-basket-small-property startshop-basket-small-property-text">
                                                                <div class="startshop-basket-small-name">
                                                                    <?=$arProperty['NAME']?>:
                                                                </div>
                                                                <div class="startshop-basket-small-value">
                                                                    <?=$arProperty['VALUE']['TEXT']?>
                                                                </div>
                                                            </div>
                                                        <?else:?>
                                                            <div class="startshop-basket-small-property startshop-basket-small-property-picture">
                                                                <div class="startshop-basket-small-name">
                                                                    <?=$arProperty['NAME']?>:
                                                                </div>
                                                                <div class="startshop-basket-small-value">
                                                                    <div class="startshop-basket-small-value-wrapper">
                                                                        <img src="<?=$arProperty['VALUE']['PICTURE']?>" alt="<?=$arProperty['VALUE']['TEXT']?>" title="<?=$arProperty['VALUE']['TEXT']?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?endif;?>
                                                    <?endforeach?>
                                                </div>
                                            <?endif;?>
                                        </div>
                                    </td>
									<td style="white-space: nowrap;">
										<div class="startshop-wrapper">
											<?=$arItem['STARTSHOP']['PRICES']['MINIMAL']['PRINT_VALUE']?>
										</div>
									</td>
									<td style="white-space: nowrap;">
										<div class="startshop-wrapper">
											<div class="startshop-numeric">
												<button class="startshop-numeric-button startshop-decrease QuantityDecrease<?=$arItem['ID']?>">-</button>
												<input type="text" class="startshop-numeric-field QuantityNumeric<?=$arItem['ID']?>" value="<?=$arItem['STARTSHOP']['BASKET']['QUANTITY']?>" />
												<button class="startshop-numeric-button startshop-increase QuantityIncrease<?=$arItem['ID']?>">+</button>
											</div>
											<?$arJSNumeric = array(
												"Value" => $arItem['STARTSHOP']['BASKET']['QUANTITY'],
												"Minimum" => $arItem['STARTSHOP']['QUANTITY']['RATIO'],
												"Ratio" => $arItem['STARTSHOP']['QUANTITY']['RATIO'],
												"Maximum" => $arItem['STARTSHOP']['QUANTITY']['VALUE'],
												"Unlimited" => !$arItem['STARTSHOP']['QUANTITY']['USE'],
												"ValueType" => "Float",
											);?>
											<script>
												$(document).ready(function() {
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
                                                        window.location.href = Startshop.Functions.stringReplace({'%23QUANTITY%23': Quantity.GetValue()}, <?=CUtil::PhpToJSObject($arItem['ACTIONS']['SET_QUANTITY'])?>);
                                                    }
												});
											</script>
										</div>
									</td>
									<td style="white-space: nowrap;">
										<div class="startshop-wrapper">
											<?=CStartShopCurrency::ConvertAndFormatAsString($arItem['STARTSHOP']['PRICES']['MINIMAL']['VALUE'] * $arItem['STARTSHOP']['BASKET']['QUANTITY'], $arItem['STARTSHOP']['CURRENCY'], $arParams['CURRENCY'])?>
										</div>
									</td>
									<td>
										<div class="startshop-wrapper">
											<a class="startshop-small-button startshop-delete" href="<?=$arItem['ACTIONS']['DELETE']?>"></a>
										</div>
									</td>
									<td style="width: 20px;"><div style="width: 20px;"></div></td>
								</tr>
							<?endforeach;?>
						</table>
					</div>
					<div class="startshop-buttons">
						<?if ($arParams['USE_BUTTON_BUY'] == "Y"):?>
							<div class="startshop-button startshop-button-gray" onClick="return StartShopFlyBasket.switchSectionByID('StartshopFlyingBasket')"><?=GetMessage('SBBS_FLYING_BUTTONS_BUY')?></div>
						<?endif?>
						<div style="float: right; width: 50%; text-align: right; font-size: 0px;">
							<?if (!empty($arParams['URL_BASKET'])):?>
								<a class="startshop-button startshop-button-gray" href="<?=$arParams['URL_BASKET']?>"><?=GetMessage('SBBS_FLYING_BUTTONS_CART')?></a>
							<?endif?>
							<?if (!empty($arParams['URL_ORDER'])):?>
								<a class="startshop-button startshop-button-standart" href="<?=$arParams['URL_ORDER']?>" style="margin-left: 10px;"><?=GetMessage('SBBS_FLYING_BUTTONS_ORDER')?></a>
							<?endif?>
						</div>
					</div>
				<?else:?>
					<div class="startshop-header startshop-element-text" style="text-align: center;">
						<?=GetMessage('SBBS_FLYING_EMPTY')?>
					</div>
				<?endif?>
			</div>
		</div>
	</div>
</div>
<script>
	var StartShopFlyBasket = new StartShopFlyBasket({
		basket: '.startshop-basket-small-container',
		switcher: '.startshop-basket-small-overlay',
		sections: '.startshop-basket-fly-sections',
		section: '.startshop-basket-fly-section'
	});
	
	<?if ($_REQUEST[$arParams['REQUEST_VARIABLE_BASKET_OPENED']] == "Y"):?>
		StartShopFlyBasket.switchSectionByID('StartshopFlyingBasket', false);
	<?endif?>
</script>
<?$frame->end()?>