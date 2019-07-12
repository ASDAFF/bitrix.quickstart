<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="catalog-section">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<table cellpadding="0" cellspacing="0" border="0" class="analog">
		<?foreach($arResult["ITEMS"] as $cell=>$arElement):
		$section_id = $arElement["IBLOCK_SECTION_ID"];
		if(!$section_id)
		{		
			$arElement["DETAIL_PAGE_URL"] = str_replace("/".$arElement['CODE'].".php","/0/".$arElement['CODE'].".php", $arElement["DETAIL_PAGE_URL"]);		
		}		
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
		<tr>
		<?endif;?>
		<?$bHasPicture = is_array($arElement['PREVIEW_IMG']);?>
		<td valign="top" width="<?=round(100/$arParams["LINE_ELEMENT_COUNT"])?>%" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
		<div class="catalog-item<?if (!$bHasPicture):?> no-picture-mode<?endif;?>"  id="<?=$this->GetEditAreaId($arElement['ID']);?>">
			<div class="filter_sort_item">
				<table class="table_sort" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td class="image">
							<?if($bHasPicture):?>
								<div class="catalog-item-image">
									<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="catalog_list_image_<?=$arElement['ID']?>" /></a>
									<div style="position:relative; top:-<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>px;">
										<?if ($arElement["PROPERTIES"]["NOVELTY"]["VALUE"]=="Y") {?><span class="new"></span><?}?>
										<?if ($arElement["PROPERTIES"]["HIT"]["VALUE"]=="Y") {?><span class="drop_hit"></span><?}?>
										<?if ($arElement["PROPERTIES"]["BESTPRICE"]["VALUE"]=="Y") {?><span class="prc"></span><?}?>
									</div>
								</div>
							<?endif;?>
						
						</td>
						<td class="price">

                            <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><h2><?=strip_tags($arElement["DISPLAY_PROPERTIES"]["PRODUSER"]["DISPLAY_VALUE"])." ".$arElement["NAME"]?></h2></a>
							<div class="catalog-item-desc">

							<?
							foreach($arElement["PRICES"] as $code=>$arPrice):
								$pr=PRICES($arPrice["VALUE"]);
								$hundred=$pr[2];
								$thousand=$pr[1];
								
								$pr2=PRICES($arPrice["DISCOUNT_VALUE"]);
								$hundred2=$pr2[2];
								$thousand2=$pr2[1];
								
								if($arPrice["CAN_ACCESS"]):
								?>
								<div class="catalog-item-price">
								
							
								<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]): ?>
									<p class="price"><strong><?=$thousand2?></strong><?=$hundred2?>-</p>
								<?else:?>
									<p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p>
								<?endif;?>
								</div>
							
																							
								<?if ($arElement['CAN_BUY']):?>
									<a href="<?echo $arElement["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/button_buy.gif" width="79px" height="19px" alt="Купить" /></a>
								<?elseif (count($arResult["PRICES"]) > 0):?>
									<span class="catalog-item-not-available"><?=GetMessage('CATALOG_NOT_AVAILABLE')?></span>
								<?endif;?>
							
							
							<?
								endif;
							endforeach;
							?>
							</div>
						</td>
                                             </tr>
                                             <tr>
						<td class="info" colspan=2>
                         				
						
						    <br/><p><?=$arElement['PREVIEW_TEXT']?></p>
						</td>
					</tr>
						


				</table>
			</div>
		</div>


			

			
		</td>

		<?$cell++;
		if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
			</tr>
		<?endif?>

		<?endforeach; ?>

		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
			<?while(($cell++)%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
				<td>&nbsp;</td>
			<?endwhile;?>
			</tr>
		<?endif?>

</table>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
