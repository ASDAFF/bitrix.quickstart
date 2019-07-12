<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>

		<?$bHasPicture = is_array($arElement['PREVIEW_IMG']);?>
		<td valign="top" width="<?=round(100/$arParams["LINE_ELEMENT_COUNT"])?>%" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
		<div class="catalog-item<?if (!$bHasPicture):?> no-picture-mode<?endif;?>"  id="<?=$this->GetEditAreaId($arElement['ID']);?>">



		
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td colspan="2">
							<div class="cart">
								<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><h2><?=strip_tags($arElement["DISPLAY_PROPERTIES"]["PRODUSER"]["DISPLAY_VALUE"])." ".$arElement["NAME"]?></h2></a>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="image">
								<?if($bHasPicture):?>
									<div class="catalog-item-image">
										<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="catalog_list_image_<?=$arElement['ID']?>" /></a>
										<div style="position:relative; top:-<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>px;">
											<?if ($arElement["PROPERTIES"]["NOVELTY"]["VALUE"]=="Y") {?><span class="new"></span><?}?>
											<?if ($arElement["PROPERTIES"]["HIT"]["VALUE"]=="Y") {?><span class="hit"></span><?}?>
											<?if ($arElement["PROPERTIES"]["BESTPRICE"]["VALUE"]=="Y") {?><span class="prc"></span><?}?>
										</div>
									</div>
								<?endif;?>
							
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
									
																								
									
									
									<?
										endif;
									endforeach;
								?>
								
	                       	</div>
						</td>
						<td>
							<div class="info">
	                             <p><?=substr($arElement['PREVIEW_TEXT'], 0, 150)."..."?></p>
								<?if ($arElement['CAN_BUY']):?>
											<a href="<?echo $arElement["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/btn_buy.png" width="79px" height="19px" alt="Купить" border="0"/></a>
										<?elseif (count($arResult["PRICES"]) > 0):?>
											<span class="catalog-item-not-available"><?=GetMessage('CATALOG_NOT_AVAILABLE')?></span>
								<?endif;?>
							</div>
						</td>
					</tr>

						


				</table>

		</div>			
		</td>



		<?endforeach; ?>


<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>

