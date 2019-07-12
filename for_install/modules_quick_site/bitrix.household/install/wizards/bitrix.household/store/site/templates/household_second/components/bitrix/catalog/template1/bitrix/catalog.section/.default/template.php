<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.trackbar.js"></script>
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
$arAvailableSort = array(
	"price" => Array('catalog_PRICE_'.$arResult['_PRICE_ID'], "asc"),
	"brand" => Array('PROPERTY_PRODUSER', "desc"),
	"rating" => Array('PROPERTY_rating', "desc"),

);

$sort = array_key_exists("sort", $_REQUEST) && array_key_exists(ToLower($_REQUEST["sort"]), $arAvailableSort) ? $arAvailableSort[ToLower($_REQUEST["sort"])][0] : "name";
$sort_order = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arAvailableSort[$sort][1];	

?>

<?
if (count($arResult['ITEMS']) < 1)
	return;
?>




	

	
	
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td width="1%"> </td>
<td colspan="4" width="98%"> </td>
<td width="1%"> </td>
</tr>
	
	<tr>
	<td class="pr_lc" width="1%"></td>
					<td width="98%">
						<div class="filter1">
                         	<div class="sort">
                            	<div class="array">
                            		<p class="e_act"><strong><?=GetMessage('SECT_SORT_LABEL')?>:</strong>&nbsp;&nbsp;&nbsp;
									<?$i=0;?>
									<?foreach ($arAvailableSort as $key => $val):
										$i++;
										$className = $sort == $val[0] ? 'active' : '';
										if ($className) 
											$className .= $sort_order == 'asc' ? '_up' : '_down';
										$newSort = $sort == $val[0] ? $sort_order == 'desc' ? 'asc' : 'desc' : $arAvailableSort[$key][1];
									?>

									<a style="color: #4c4d48;" href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, 	array('sort', 'order'))?>" class="<?=$className?>" rel="nofollow"><?=GetMessage('SECT_SORT_'.$key)?></a>
									<?if ($i!=count($arAvailableSort)) {?>&nbsp;&nbsp;|&nbsp;&nbsp;<?}?>
									
									<?endforeach;?>
									</p>
									<?$compareUrl = str_replace("#IBLOCK_ID#", $arParams['IBLOCK_ID'], $arParams["COMPARE_URL"]);
									$compareUrl = str_replace("#SECTION_CODE#", $arParams['SECTION_CODE'], $arParams["COMPARE_URL"]);
									?>
									<div class="comp_main" <?if(count($_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"])==0):?>style="display:none;"<?endif?>>
										<div class="compare">			
												<a title="<?=GetMessage('CATALOG_COMPARE_ALL')?>" href="<?echo $compareUrl?>"><?=GetMessage('CATALOG_COMPARE_ALL')?> (<span class="compare_num"><?=count($_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"])?></span>)</a>	
										</div>
									</div>									
                            	</div>
								<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
								<?=$arResult["NAV_STRING"];?>
								<?endif;?>
                            	<div class="clear"></div>
                        	</div>
                        </div>
					</td>
					<td class="pr_rc" width="1%">	
					</td>
				</tr>
				<tr>
					<td class="pr_ln" width="1%" >
					</td>
					<td class="pr_n" width="98%">
					</td>
					<td class="pr_rn" width="1%" >
					</td>
				</tr>
	

	
	
			<?foreach ($arResult['ITEMS'] as $key => $arElement):

				$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CATALOG_ELEMENT_DELETE_CONFIRM')));


				$bHasPicture = is_array($arElement['PREVIEW_IMG']);

			?>
			<table width="100%" class="pr" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="pr_lv" width="1%" >
				</td>
				<td class="pr_v" width="98%">
				</td>
				<td class="pr_rv" width="1%">
				</td>
			</tr>
			<tr>
				<td class="pr_lc" width="1%">
				</td>
				<td width="98%">			
				<div class="filter_sort_item" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
				
				
				
					
						<table class="table_sort" cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td class="image" width="15%">
									<?if($bHasPicture):?>
										<div class="img">
											<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="catalog_list_image_<?=$arElement['ID']?>" /></a>
											<div style="position:relative; top:-<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>px;">
												<?if ($arElement["PROPERTIES"]["NOVELTY"]["VALUE"]=="Y") {?><span class="new"></span><?}?>
												<?if ($arElement["PROPERTIES"]["HIT"]["VALUE"]=="Y") {?><span class="hit"></span><?}?>
												<?if ($arElement["PROPERTIES"]["BESTPRICE"]["VALUE"]=="Y") {?><span class="prc"></span><?}?>
											</div>
										</div>
									<?endif;?>
							</td>
							
							
							<td class="info1" width="40%">
								<a href="<?=$arElement["DETAIL_PAGE_URL"]?>">
									<h3>
										<?if($arParams['ADD_PRODUSER_TO_TITLE']!="N"):?>
											<?=strip_tags($arElement["DISPLAY_PROPERTIES"]["PRODUSER"]["DISPLAY_VALUE"])." "?>
										<?endif?>
										<?=$arElement["NAME"]?>
									</h3>
								</a>
								<p><?=$arElement['PREVIEW_TEXT']?></p>
								<div class="more"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=GetMessage('CATALOG_ELEMENT_ABOUT')?></a></div>
							</td>
							
							
							<td class="info" width="30%">
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
								<?if($arParams["DISPLAY_COMPARE"]):?>
											<div class="lable">
												<a href="<?echo $arElement["COMPARE_URL"];?>&ajax_compare=1&backurl=<?=urlencode($APPLICATION->GetCurPageParam())?>" class="catalog-item-compare" onclick="addToCompare(this, '<?=GetMessage("CATALOG_COMPARE")?>', '<?=GetMessage("CATALOG_IN_COMPARE")?>', <?=$arElement['ID']?>, '<?=$arParams["COMPARE_NAME"]?>', <?=$arParams["IBLOCK_ID"]?>); return false;" rel="nofollow" id="catalog_add2compare_link_<?=$arElement['ID']?>">
													<input id="check_<?=$arElement['ID']?>" name="compare[]" type="checkbox" value="ON" />
													<label for="check_<?=$arElement['ID']?>"><?echo GetMessage("CATALOG_COMPARE")?></label>
												</a>
											</div>
								<?endif;?>
							</td>
							<td class="price" width="15%">
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
										$thousand2=$pr2[1];?>
										
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
										if (in_array(GetMessage('CATALOG_ACT1'), $disc_name)) {$class="place_2"; $txt="<p>".GetMessage('CATALOG_ACT1')."</p>";}
										else if (in_array(GetMessage('CATALOG_ACT2'), $disc_name)) {$class="place_1"; $txt="<p>".GetMessage('CATALOG_ACT2')."</p>";}
										
										?>
										
										<?if($minPriseArr["DISCOUNT_VALUE"] < $minPriseArr["VALUE"]):?>
											<div class="<?=$class?>"><?=$txt?><strong><?=$thousand2?></strong><?=$hundred2?>-</div>
											<p class="price line_center"><strong><?=$thousand?></strong><?=$hundred?>-</p>
										<?else:?>
											<p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p>
										<?endif;?>
										</div>									
										
									<?endif;?>
									<?if ($arElement['CAN_BUY']):?>
											<a href="<?echo $arElement["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>');" id="catalog_add2cart_link_<?=$arElement['ID']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/btn_buy.png" border="0" width="79px" height="19px" alt="Купить" /></a>
									<?elseif (count($arResult["PRICES"]) > 0):?>
											<span class="catalog-item-not-available"><?=GetMessage('CATALOG_NOT_AVAILABLE')?></span>
									<?endif;?>
							</td>
						</tr>
					</table>
					
				
				
				
				</div>
				</td>
					<td class="pr_rc" width="1%">	
					</td>
				</tr>
				<tr>
					<td class="pr_ln" width="1%" >
					</td>
					<td class="pr_n" width="98%">
					</td>
					<td class="pr_rn" width="1%" >
					</td>
				</tr>
			</table>
			<?endforeach;?>
	
	
	
	
			<table width="100%" class="pr" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="pr_lv" width="1%" >
				</td>
				<td class="pr_v" width="98%">
				</td>
				<td class="pr_rv" width="1%">
				</td>
			</tr>
			<tr>
				<td class="pr_lc" width="1%">
				</td>
				<td width="98%">				
						<div class="filter1">
							<div class="sort">
								<div class="array">			
								
									<p class="e_act"><strong><?=GetMessage('SECT_SORT_LABEL')?>:</strong>&nbsp;&nbsp;&nbsp;
									<?$i=0;?>
									<?foreach ($arAvailableSort as $key => $val):
										$i++;
										$className = $sort == $val[0] ? 'active' : '';
										if ($className) 
											$className .= $sort_order == 'asc' ? '_up' : '_down';
										$newSort = $sort == $val[0] ? $sort_order == 'desc' ? 'asc' : 'desc' : $arAvailableSort[$key][1];
									?>

									<a style="color: #4c4d48;" href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, 	array('sort', 'order'))?>" class="<?=$className?>" rel="nofollow"><?=GetMessage('SECT_SORT_'.$key)?></a>
									<?if ($i!=count($arAvailableSort)) {?>&nbsp;&nbsp;|&nbsp;&nbsp;<?}?>
									
									<?endforeach;?>
									</p>

									<div class="comp_main" <?if(count($_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"])==0):?>style="display:none;"<?endif?>>
										<div class="compare">			
												<a title="<?=GetMessage('CATALOG_COMPARE_ALL')?>" href="<?echo $compareUrl?>"><?=GetMessage('CATALOG_COMPARE_ALL')?> (<span class="compare_num"><?=count($_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"])?></span>)</a>	
										</div>
									</div>
								</div>
								<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
								<?=$arResult["NAV_STRING"];?>
								<?endif;?>
								<div class="clear"></div>
							</div>
						</div>
				</td>
				<td class="pr_rc" width="1%">	
				</td>
			</tr>
		</table>
	
	
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>
			
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td>
				