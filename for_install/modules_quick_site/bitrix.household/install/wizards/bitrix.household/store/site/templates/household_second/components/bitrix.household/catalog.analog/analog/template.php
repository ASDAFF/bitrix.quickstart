<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>



				<table width="100%">
				<tr>
					<td colspan="4">
						<div class="maintext">
							<a href="<?=SITE_DIR.$arParams['SECTION_LINK']?>"><?=$arParams['SECTION_NAME']?></a>
						</div>
					</td>

				</tr>

<?

$all=0;
foreach($arResult["ROWS"] as $arItems):
	foreach($arItems as $key => $arElement):
		if ($arElement!="") $all++;
	endforeach;
endforeach;

$count=0;
foreach($arResult["ROWS"] as $arItems):
?>
<tr>




<?
	$i=0;
	foreach($arItems as $key => $arElement):
	$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CATALOG_ELEMENT_DELETE_CONFIRM')));
	
	$section_id = $arElement["IBLOCK_SECTION_ID"];
	if(!$section_id)
	{		
		$arElement["DETAIL_PAGE_URL"] = str_replace("/".$arElement['CODE'].".php","/0/".$arElement['CODE'].".php", $arElement["DETAIL_PAGE_URL"]);		
	}		
		if(is_array($arElement)):
			$i++; $count++;
			$bPicture = is_array($arElement["PREVIEW_IMG"]);
?>
			<td width="<?=100/$arParams['~LINE_ELEMENT_COUNT']?>%">
			
			
				<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td colspan="2">
									<div class="cart">
												<h2><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><h2><?=strip_tags($arElement["DISPLAY_PROPERTIES"]["PRODUSER"]["DISPLAY_VALUE"])." ".$arElement["NAME"]?></h2></a></h2>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="image">
									<?if ($bPicture):?>
										<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="catalog_list_image_<?=$arElement['ID']?>" /></a>
									<?endif;?>
										<div style="position:relative; top:-<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>px;">
											<?$i=0; if ($arElement["PROPERTIES"]["NOVELTY"]["VALUE"]=="Y") {?><span class="new"></span><?$i++;}?>
											<?if ($arElement["PROPERTIES"]["HIT"]["VALUE"]=="Y") {?><span class="<?if ($i>0) echo "drop_";?>hit"></span><?$i++;}?>
											<?if ($arElement["PROPERTIES"]["BESTPRICE"]["VALUE"]=="Y") {?><span class="<?if ($i>0) echo "drop_";?>super"></span><?}?>
										</div>
									</div>
								</td>
								<td>
									<div class="info">
										<p><?=substr($arElement["PREVIEW_TEXT"], 0, 50)?>...</p>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="image">
													<?
											if(count($arElement["PRICES"])>0): ;
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
														if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):
								?>
															<p class="price"><strong><?=$thousand2?></strong><?=$hundred2?>-</p>
															<?
														else:
								?>
															<p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p>
								<?
														endif;
													endif;
												endforeach;
											endif;
								?>
									</div>
								</td>
								<td	>
									<div class="info">
										<?if($arElement["CAN_BUY"]):?>
											<a href="<?echo $arElement["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/btn_buy.png" width="79px" height="19px" alt="Купить" border="0"/></a>
										<?endif;?>
									</div>
								</td>
							</tr>
				</table>
			
				
			</td>
<?
		endif;
?>
<?
	endforeach;
?>





		</tr>
		
<?endforeach;?>

</table>

				





