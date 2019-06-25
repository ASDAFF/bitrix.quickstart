<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
	global $APPLICATION;

	$this->setFrameMode(true);
?>
<div class="startshop-catalog<?=$arParams['ADAPTABLE'] == "Y" ? " adaptiv" : ""?>">
	<?$frame = $this->createFrame()->begin()?>
	<?if (!empty($arResult['ITEMS'])):?>
		<?if ($arParams["DISPLAY_TOP_PAGER"]):?>
			<div class="clear"></div>
			<?=$arResult['NAV_STRING']?>
			<div class="startshop-indents-vertical indent-35"></div>
		<?endif;?>
		<div class="startshop-catalog-section">
			<?
				$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
				$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
				$arElementDeleteParams = array("CONFIRM" => GetMessage('SH_CS_TEXT_ELEMENT_DELETE_CONFIRM'));
			?>
            <?foreach ($arResult["ITEMS"] as $sKey => $arItem):?>
                <?
                    $sAddToBasketUrl = $APPLICATION->GetCurPageParam(
                        urlencode('CatalogBasketAction').'=Add&'.
                        urlencode('CatalogBasketItem').'='.urlencode($arItem['ID']),
                        array('CatalogBasketAction', 'CatalogBasketItem')
                    );

                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
                ?>
                <div class="startshop-element startshop-hover-shadow" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                    <div class="startshop-name">
                        <div><a class="startshop-link startshop-link-standart" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
                    </div>
                    <div class="startshop-buys">
                        <div class="startshop-price">
                            <?if (empty($arItem['STARTSHOP']['OFFERS'])):?>
                                <div class="startshop-new"><?=$arItem['STARTSHOP']['PRICES']['MINIMAL']['PRINT_VALUE']?></div>
                            <?else:?>
                                <?$arPrice = CStartShopToolsIBlock::GetOffersMinPrice($arItem);?>
                                <?if (!empty($arPrice)):?>
                                    <div class="startshop-new"><?=GetMessage("SH_CS_TEXT_FROM")?> <?=$arPrice['PRINT_VALUE']?></div>
                                <?endif;?>
                                <?unset($arPrice);?>
                            <?endif;?>
                        </div>
                        <?if (empty($arItem['STARTSHOP']['OFFERS'])):?>
                            <div class="min-buttons">
								<div class="min-button compare">
									<div class="add addToCompare addToCompare<?=$arItem["ID"]?>"
										onclick="return addToCompare('<?=SITE_DIR?>', '<?=$arItem['IBLOCK_TYPE_ID']?>','<?=$arItem["IBLOCK_ID"]?>','<?=$arParams["COMPARE_NAME"]?>','<?=$arItem['COMPARE_URL']?>')" 
										title="<?=GetMessage('COMPARE_TEXT_DETAIL')?>"
									>
									</div>
									<div class="remove removeFromCompare removeFromCompare<?=$arItem["ID"]?>"
										style="display:none"
										onclick="return removeFromCompare('<?=SITE_DIR?>', '<?=$arItem['IBLOCK_TYPE_ID']?>','<?=$arItem["IBLOCK_ID"]?>','<?=$arParams["COMPARE_NAME"]?>','<?=$arItem['COMPARE_REMOVE_URL']?>')"
										title="<?=GetMessage('COMPARE_DELETE_TEXT_DETAIL')?>"
									>
									</div>
								</div>
							</div>
							<?if ($arItem['STARTSHOP']['AVAILABLE']) {?>
                                <div class="startshop-buy">
                                    <?if (!$arItem['STARTSHOP']['BASKET']['INSIDE']):?>
                                        <a rel="nofollow" class="startshop-button startshop-button-standart to-cart" id="to_cart_<?=$arElement['ID']?>" href="<?=$sAddToBasketUrl?>">
                                            <?=GetMessage("SH_CS_TEXT_ADD_TO_BASKET")?>
                                        </a>
                                    <?else:?>
                                        <a rel="nofollow" href="<?=$arParams["BASKET_URL"];?>" id="in_cart_<?=$arElement['ID']?>" class="startshop-button startshop-button-standart startshop-status-focus to-cart-added">
                                            <?=GetMessage("SH_CS_TEXT_ADDED_TO_BASKET")?>
                                        </a>
                                    <?endif;?>
                                </div>
                            <?} else {?>
                                <div class="startshop-state-unavailable">
                                    <?=GetMessage('SH_CS_TEXT_PRODUCT_NOT_AVAILABLE')?>
                                </div>
                            <?}?>
                        <?else:?>
							<div class="min-buttons"></div>
                            <div class="startshop-buy">
                                <a class="startshop-button startshop-button-standart to-cart" id="to_cart_<?=$arElement['ID']?>" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                    <?=GetMessage("SH_CS_TEXT_SHOW_MORE")?>
                                </a>
                            </div>
                        <?endif;?>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
			<?endforeach;?>
		</div>
		<div class="clear"></div>
		<?if ($arParams["DISPLAY_BOTTOM_PAGER"]):?>
			<div class="startshop-indents-vertical indent-35"></div>
			<?=$arResult['NAV_STRING']?>
			<div class="clear"></div>
		<?endif;?>
	<?endif;?>
	<?$frame->end();?>
</div>