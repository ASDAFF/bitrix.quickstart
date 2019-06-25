<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
	global $APPLICATION;

	$this->setFrameMode(true);

	switch ($arParams['LINE_ELEMENT_COUNT']) {
		case 3: $sGridStyle = "startshop-33"; break;
		case 4: $sGridStyle = "startshop-25"; break;
		case 5: $sGridStyle = "startshop-20"; break;
		default : $sGridStyle = "startshop-50"; break;
	}
?>
<div class="startshop-catalog<?=$arParams['ADAPTABLE'] == "Y" ? " adaptiv" : ""?>">
	<?$frame = $this->createFrame()->begin()?>
	<?if (!empty($arResult['ITEMS'])):?>
		<?if ($arParams["DISPLAY_TOP_PAGER"]):?>
			<div class="clear"></div>
			<?=$arResult['NAV_STRING']?>
			<div class="startshop-indents-vertical indent-35"></div>
		<?endif;?>
		<div class="startshop-catalog-section startshop_parent_col">
			<?
				$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
				$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
				$arItemDeleteParams = array("CONFIRM" => GetMessage('SH_CS_TILE_ELEMENT_DELETE_CONFIRM'));
			?>
			<?foreach($arResult["ITEMS"] as $sKey => $arItem):?>
                <?
                    $sAddToBasketUrl = $APPLICATION->GetCurPageParam(
                        urlencode('CatalogBasketAction').'=Add&'.
                        urlencode('CatalogBasketItem').'='.urlencode($arItem['ID']),
                        array('CatalogBasketAction', 'CatalogBasketItem')
                    );

                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arItemDeleteParams);
                ?>
                <div class="startshop_col <?=$sGridStyle?>">
                    <div class="startshop-element startshop-hover-shadow">
						<?if(!empty($arItem["DETAIL_PICTURE"]["SRC"])){
							$file = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"],array('width'=>300, 'height'=>300),"BX_RESIZE_IMAGE_PROPORTIONAL_ALT");
									$src=$file['src'];
								}else{
									$src=SITE_TEMPLATE_PATH."/images/noimg/no-img.png";
							}?>
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="image_product" style="background-image:url(<?=$src?>)">	
							<div class="marks">
								<?if( $arItem["PROPERTIES"]["CML2_HIT"]["VALUE"] ){?>
									<span class="mark hit"><?=GetMessage("MESSAGE_HIT");?></span>
								<?}?>			
								<?if( $arItem["PROPERTIES"]["CML2_NEW"]["VALUE"] ){?>
									<span class="mark new"><?=GetMessage("MESSAGE_NEW");?></span>
								<?}?>
								<?if( $arItem["PROPERTIES"]["CML2_RECOMEND"]["VALUE"] ){?>
    								<span class="mark recommend"><?=GetMessage("MARK_RECOMEND");?></span>
    							<?}?>
							</div>		
						</a>
                        <?if(empty($arItem['STARTSHOP']['OFFERS']) && $arParams["DISPLAY_COMPARE"]=="Y"):?>
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
    					<?endif?>		
						<div class="name_product title_product">
							<a class="name" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
							<a class="name_group" href="<?=$url_parent_group ?>"><?=$name_parent_group;?></a>
						</div>				
						<div class="buys">		
							<div class="price_block">							
								<?if (empty($arItem['STARTSHOP']['OFFERS'])):?>
                                    <div class="new_price"><?=$arItem['STARTSHOP']['PRICES']['MINIMAL']['PRINT_VALUE']?></div>
                                <?else:?>
                                    <?$arPrice = CStartShopToolsIBlock::GetOffersMinPrice($arItem);?>
                                    <?if (!empty($arPrice)):?>
                                        <div class="new_price"><?=GetMessage("FROM")?> <?=$arPrice['PRINT_VALUE']?></div>
                                    <?endif;?>
                                    <?unset($arPrice);?>
                                <?endif;?>			
							</div>
							<?if (empty($arItem['STARTSHOP']['OFFERS'])):?>
                                <?if ($arItem['STARTSHOP']['AVAILABLE']) {?>
                                    <div class="startshop-buy">
                                        <?if (!$arItem['STARTSHOP']['BASKET']['INSIDE']):?>
                                            <a rel="nofollow" class="startshop-button startshop-button-standart to-cart" id="to_cart_<?=$arItem['ID']?>" href="<?=$sAddToBasketUrl?>">
                                                <?=GetMessage("SH_CS_TILE_ADD_TO_BASKET")?>
                                            </a>
                                        <?else:?>
                                            <a rel="nofollow" href="<?=$arParams["BASKET_URL"];?>" id="in_cart_<?=$arItem['ID']?>" class="startshop-button startshop-button-standart startshop-status-focus to-cart-added">
                                                <?=GetMessage("SH_CS_TILE_ADDED_TO_BASKET")?>
                                            </a>
                                        <?endif;?>
                                    </div>
                                <?} else {?>
                                    <div class="startshop-state-unavailable">
                                        <?=GetMessage('SH_CS_TILE_PRODUCT_NOT_AVAILABLE')?>
                                    </div>
                                <?}?>
                            <?else:?>
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