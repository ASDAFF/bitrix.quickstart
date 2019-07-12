<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (count($arResult['ITEMS']) < 1)
	return;
?>

<td style="padding-left:10px;" width="<?=100/count($arResult['ITEMS'])?>%">
	<?
	foreach ($arResult['ITEMS'] as $key => $arElement):

		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CATALOG_ELEMENT_DELETE_CONFIRM')));
		
		$section_id = $arElement["IBLOCK_SECTION_ID"];
		if(!$section_id)
		{		
			$arElement["DETAIL_PAGE_URL"] = str_replace("/".$arElement['CODE'].".php","/0/".$arElement['CODE'].".php", $arElement["DETAIL_PAGE_URL"]);		
		}
		$bHasPicture = is_array($arElement['PREVIEW_IMG']);

	?>
	<div class="catalog-item<?if (!$bHasPicture):?> no-picture-mode<?endif;?>"  id="<?=$this->GetEditAreaId($arElement['ID']);?>">
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td colspan="2">
				<div class="cart">
					<h2>Вентилятор IF 50-V</h2>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="image">
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
	                <?foreach($arElement["PRICES"] as $code=>$arPrice):
							
										$arPrice["VALUE"]=number_format($arPrice["VALUE"], 0, '.', ',');
										$thousand=substr($arPrice["VALUE"],0,strpos($arPrice["VALUE"],","));
										if ($thousand!="")
										{
											$hundred=substr($arPrice["VALUE"],strpos($arPrice["VALUE"],",")+1,strlen($arPrice["VALUE"]));
										}
										else
										{
											$thousand=substr($arPrice["VALUE"],0,1);
											$hundred=substr($arPrice["VALUE"],1);
										}
										
										$arPrice["DISCOUNT_VALUE"]=number_format($arPrice["DISCOUNT_VALUE"], 0, '.', ',');
										$thousand2=substr($arPrice["DISCOUNT_VALUE"],0,strpos($arPrice["DISCOUNT_VALUE"],","));
										if ($thousand2!="")
										{
											$hundred2=substr($arPrice["DISCOUNT_VALUE"],strpos($arPrice["DISCOUNT_VALUE"],",")+1,strlen($arPrice["DISCOUNT_VALUE"]));
										}
										else
										{
											$thousand2=substr($arPrice["DISCOUNT_VALUE"],0,1);
											$hundred2=substr($arPrice["DISCOUNT_VALUE"],1);
										}
								if($arPrice["CAN_ACCESS"]):
								?>			
									<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
										<div class="place"><strong><?=$thousand2?></strong><?=$hundred2?>-</div>
									<?else:?>
										<p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p>
									<?endif;?>
	            </div>
			</td>
			<td>
				<div class="info">
	                                    		<p>Мощность: 40 Вт <br/>Функция поворота: есть<br/>Таймер: нет<br/>Количество скоростей: 3</p>
												<div class="buy"><a href="#"><img src="images/btn_buy.png" border="0" width="79px" height="19px" alt="Купить" /></a></div>
											</div>
										</td>
									</tr>
									</table>
	</div>
	
	
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


							<div class="catalog-item-desc">

							<?//echo "<pre>";print_r($arElement);echo "<pre>";
							foreach($arElement["PRICES"] as $code=>$arPrice):
							
										$arPrice["VALUE"]=number_format($arPrice["VALUE"], 0, '.', ',');
										$thousand=substr($arPrice["VALUE"],0,strpos($arPrice["VALUE"],","));
										if ($thousand!="")
										{
											$hundred=substr($arPrice["VALUE"],strpos($arPrice["VALUE"],",")+1,strlen($arPrice["VALUE"]));
										}
										else
										{
											$thousand=substr($arPrice["VALUE"],0,1);
											$hundred=substr($arPrice["VALUE"],1);
										}
										
										$arPrice["DISCOUNT_VALUE"]=number_format($arPrice["DISCOUNT_VALUE"], 0, '.', ',');
										$thousand2=substr($arPrice["DISCOUNT_VALUE"],0,strpos($arPrice["DISCOUNT_VALUE"],","));
										if ($thousand2!="")
										{
											$hundred2=substr($arPrice["DISCOUNT_VALUE"],strpos($arPrice["DISCOUNT_VALUE"],",")+1,strlen($arPrice["DISCOUNT_VALUE"]));
										}
										else
										{
											$thousand2=substr($arPrice["DISCOUNT_VALUE"],0,1);
											$hundred2=substr($arPrice["DISCOUNT_VALUE"],1);
										}
								if($arPrice["CAN_ACCESS"]):
								?>
								<div class="catalog-item-price">							
									<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
										<div class="place_1"><strong><?=$thousand2?></strong><?=$hundred2?>-</div>
										<s><p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p></s>
									<?else:?>
										<p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p>
									<?endif;?>
								</div>
							
																							
								<?if ($arElement['CAN_BUY']):?>
									<a href="<?echo $arElement["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="/images/button_buy.gif" width="79px" height="19px" alt="<?=GetMessage("CATALOG_BUY")?>" /></a>
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
				</table>
			</div>
		</div>
	<?endforeach;?>
</td>
