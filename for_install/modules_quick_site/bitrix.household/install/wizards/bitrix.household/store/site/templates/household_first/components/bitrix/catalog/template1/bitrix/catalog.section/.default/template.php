<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
function PRICES($p, $showFraction)
{
	if($showFraction=="Y")
		$decimal=2;
	else
		$decimal=0;
	$p=number_format($p, $decimal, '.', ',');
	$thousand=substr($p,0,strpos($p,","));
	if ($thousand!="")
		{
			$hundred=substr($p,strpos($p,",")+1,strlen($p));
		}
	else
		{
			$thousand=substr($p,0,1);
			$hundred=substr($p,1);
		}
	return(array(1=>$thousand, 2=>$hundred));
}
?>


<?
if (count($arResult['ITEMS']) < 1)
	return;
?>

		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<?=$arResult["NAV_STRING"];?>
		<?endif;?>

	<div class="clear"></div> <!---->
	</div> <!--<div class="sort">-->
</div><!--<div class="filter">-->


<div class="filter_sort">

	<div class="catalog-item-list">
	
	
	
	<?foreach ($arResult['ITEMS'] as $key => $arElement):

		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CATALOG_ELEMENT_DELETE_CONFIRM')));


		$bHasPicture = is_array($arElement['PREVIEW_IMG']);

	?>
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
						<td class="info" rowspan="2">
                           	<a href="<?=$arElement["DETAIL_PAGE_URL"]?>">
                           		<h2>
                           			<?if($arParams['ADD_PRODUSER_TO_TITLE']!="N"):?>
							<?=strip_tags($arElement["DISPLAY_PROPERTIES"]["PRODUSER"]["DISPLAY_VALUE"])." "?>
						<?endif?>
                           			<?=$arElement["NAME"]?>
                           		</h2>
                           	</a>				
                           	<div class="rating">
								<div style="float:left;"><?=GetMessage('CATALOG_ELEMENT_RATING')?> : </div>
								<?$APPLICATION->IncludeComponent(
									"bitrix:iblock.vote",
									"star_ajax",
									Array(
										"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
										"IBLOCK_ID" => $arParams["IBLOCK_ID"],
										"ELEMENT_ID" => $arElement['ID'],
										"CACHE_TYPE" => "Y",
										"CACHE_TIME" => "3600",
										"MAX_VOTE" => "5",
										"VOTE_NAMES" => array(
											0 => "1",
											1 => "2",
											2 => "3",
											3 => "4",
											4 => "5",
											5 => "",
										),
										"SET_STATUS_404" => "N",
										'DISPLAY_AS_RATING'=>$arParams["DISPLAY_AS_RATING"]
									),
									$component
								);?>
								<?=GetMessage('CATALOG_AVAILABLE')?> : 
									<?if ($arElement["CATALOG_QUANTITY"]>0) {?><em class="yes"><?=GetMessage('CATALOG_ELEMENT_YES')?></em><?}
									else {?><em class="no"><?=GetMessage('CATALOG_ELEMENT_NO')?></em><?}?>
								<?=GetMessage('CATALOG_ELEMENT_FEEDBACK')?> : 
									<strong><?if ($arElement["PROPERTIES"]["FORUM_MESSAGE_CNT"]["VALUE"]!="") echo $arElement["PROPERTIES"]["FORUM_MESSAGE_CNT"]["VALUE"]; else echo "0";?></strong>
							</div>
						    <p><?=$arElement['PREVIEW_TEXT']?></p>
							<?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
                                                           <?if (!in_array($arProperty["CODE"], array("rating", "vote_count", "vote_sum", "BESTPRICE", "NOVELTY", "HIT", "PRODUSER"))):?>
								<?=$arProperty["NAME"]?>:&nbsp;<?
									if(is_array($arProperty["DISPLAY_VALUE"]))
										echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
									else
										echo $arProperty["DISPLAY_VALUE"];?><br />
                                                           <?endif;?>
							<?endforeach?>
							<br/>
							<div class="more"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=GetMessage('CATALOG_ELEMENT_ABOUT')?></a></div>
						</td>
						<td class="price" rowspan="2">


							<div class="catalog-item-desc">

							<?							
							$minPriseArr=array();
							foreach($arElement["PRICES"] as $code=>$arPrice):								
								if($arPrice["CAN_ACCESS"]):
									if($minPriseArr["VALUE"]>$arPrice["VALUE"] || !$minPriseArr["VALUE"])
										$minPriseArr=$arPrice;								
								endif;
							endforeach;
							if($minPriseArr):
								$pr=PRICES($minPriseArr["VALUE"], $arParams['SHOW_FRACTION_PRICE']);
								$hundred=$pr[2];
								$thousand=$pr[1];

								$pr2=PRICES($minPriseArr["DISCOUNT_VALUE"], $arParams['SHOW_FRACTION_PRICE']);
								$hundred2=$pr2[2];
								$thousand2=$pr2[1];
								
								?>
								<div class="catalog-item-price">
								
								<?
								$disc_name=array();
								$arDiscounts=array();
								$arDiscounts = CCatalogDiscount::GetDiscountByProduct(
										$arElement['ID'],
										$USER->GetUserGroupArray(),
										"N",
										1,
										SITE_ID
									);
								foreach ($arDiscounts as $key => $el)
								{
									$disc_name[]=$el['NAME'];
								}
								
								$class="";
								if (in_array(GetMessage('CATALOG_ACT1'), $disc_name)) $class="place_2";
								else if (in_array(GetMessage('CATALOG_ACT2'), $disc_name)) $class="place_1";
								
								?>
								
								<?if($minPriseArr["DISCOUNT_VALUE"] < $minPriseArr["VALUE"]):?>
									<div class="<?=$class?>"><strong><?=$thousand2?></strong><?=$hundred2?>-</div>
									<s><p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p></s>
								<?else:?>
									<p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p>
								<?endif;?>
								</div>
							<?endif;?>
																							
								<?if ($arElement['CAN_BUY']):?>
									<a href="<?echo $arElement["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/button_buy.gif" width="79px" height="19px" alt="Купить" /></a>
								<?elseif (count($arResult["PRICES"]) > 0):?>
									<span class="catalog-item-not-available"><?=GetMessage('CATALOG_NOT_AVAILABLE')?></span>
								<?endif;?>
							</div>
						</td>
					</tr>
					<tr>				
						<td class='bott'>
								<?if($arParams["DISPLAY_COMPARE"]):?>
									<div class="lable">
										<a href="<?echo $arElement["COMPARE_URL"];?>&ajax_compare=1&backurl=<?=urlencode($APPLICATION->GetCurPageParam())?>" class="catalog-item-compare" onclick="addToCompare(this, '<?=GetMessage("CATALOG_COMPARE")?>', '<?=GetMessage("CATALOG_IN_COMPARE")?>', <?=$arElement['ID']?>, '<?=$arParams["COMPARE_NAME"]?>', <?=$arParams["IBLOCK_ID"]?>); return false;" rel="nofollow" id="catalog_add2compare_link_<?=$arElement['ID']?>">
											<input id="check_<?=$arElement['ID']?>" name="compare[]" type="checkbox" value="ON" />
											<label for="check_<?=$arElement['ID']?>"><?echo GetMessage("CATALOG_COMPARE")?></label>
										</a>
									</div>
								<?endif;?>
								
						</td>
					</tr>



				</table>
			</div>
		</div>
		
	<?endforeach;?>

	
	
	
	
	<?
$arAvailableSort = array(
	"price" => Array('catalog_PRICE_'.$arResult['_PRICE_ID'], "asc"),
	"brand" => Array('PROPERTY_PRODUSER', "desc"),
	"rating" => Array('PROPERTY_rating', "desc"),

);

$sort = array_key_exists("sort", $_REQUEST) && array_key_exists(ToLower($_REQUEST["sort"]), $arAvailableSort) ? $arAvailableSort[ToLower($_REQUEST["sort"])][0] : "name";
$sort_order = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arAvailableSort[$sort][1];	

?>
<div class="filter">
	<div class="sort">
		<div class="array">
			<p class="e_act"><strong><?=GetMessage('SECT_SORT_LABEL')?>:</strong>&nbsp;&nbsp;&nbsp;
			<?$i=0;?>
			<?foreach ($arAvailableSort as $key => $val):
				$i++;
				$className = $sort == $val[0] ? 'active_down' : '';
				if ($className) 
					$className .= $sort_order == 'asc' ? ' asc' : ' desc';
				$newSort = $sort == $val[0] ? $sort_order == 'desc' ? 'asc' : 'desc' : $arAvailableSort[$key][1];
			?>

			<a href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, 	array('sort', 'order'))?>" class="<?=$className?>" rel="nofollow"><?=GetMessage('SECT_SORT_'.$key)?></a>
			<?if ($i!=count($arAvailableSort)) {?>&nbsp;&nbsp;|&nbsp;&nbsp;<?}?>
			
			<?endforeach;?>
			</p>

			<div class="comp_main"></div>
			
 
			
		</div>

		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<?=$arResult["NAV_STRING"];?>
		<?endif;?>

	<div class="clear"></div> <!---->
	</div> <!--<div class="sort">-->
</div>

