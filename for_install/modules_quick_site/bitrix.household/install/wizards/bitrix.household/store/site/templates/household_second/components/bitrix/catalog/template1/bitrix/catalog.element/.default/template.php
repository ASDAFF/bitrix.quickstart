<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$compareUrl = str_replace("#IBLOCK_ID#", $arParams['IBLOCK_ID'], $arParams["COMPARE_URL"]);
$compareUrl = str_replace("#SECTION_CODE#", $arParams['SECTION_CODE'], $arParams["COMPARE_URL"]);
?>
	<div class="comp_main" <?if(count($_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"])==0):?>style="display:none;"<?endif?>>
		<div class="compare">			
				<a title="<?echo GetMessage("CATALOG_COMPARE2")?>" href="<?echo $compareUrl?>"><?echo GetMessage("CATALOG_COMPARE3")?><span class="compare_num"><?=count($_SESSION[$arParams["COMPARE_NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"])?></span><?echo GetMessage("CATALOG_COMPARE4")?></a>	
		</div>
	</div>
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

<?if (is_array($arResult['DETAIL_PICTURE_350']) || count($arResult["MORE_PHOTO"])>0):?>
<script type="text/javascript">
$(function() {
	$('div.catalog-detail-image a').fancybox({
		'transitionIn': 'elastic',
		'transitionOut': 'elastic',
		'speedIn': 600,
		'speedOut': 200,
		'overlayShow': true,
		'cyclic' : true,
		'padding': 20,
		'titlePosition': 'over',
		'onComplete': function() {
			$("#fancybox-title").css({ 'top': '100%', 'bottom': 'auto' });
		} 
	});
});
</script>
<?endif;?>

<?
$arUrlTemplates = array(
	"section" => $arParams['SEF_URL_TEMPLATES']['section'],
	"element" => $arParams['SEF_URL_TEMPLATES']['element']
);

$arVariables = array();
$page = CComponentEngine::ParseComponentPath($arParams['SEF_FOLDER'], $arUrlTemplates, $arVariables);
?>



								<div class="card">
								
									<?if (is_array($arResult['DETAIL_PICTURE_350'])):?>
									<div class="big_prev">
										<div class="catalog-detail-image" id="catalog-detail-main-image">
											<a rel="catalog-detail-images" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" title="<?=(strlen($arResult["DETAIL_PICTURE"]["DESCRIPTION"]) > 0 ? $arResult["DETAIL_PICTURE"]["DESCRIPTION"] : $arResult["NAME"])?>"><img src="<?=$arResult['DETAIL_PICTURE_350']['SRC']?>" border="0" alt="<?=$arResult["NAME"]?>" id="catalog_detail_image" width="<?=$arResult['DETAIL_PICTURE_350']["WIDTH"]?>" height="<?=$arResult['DETAIL_PICTURE_350']["HEIGHT"]?>" /></a>
											<div style="position:relative; top:-<?=$arResult['DETAIL_PICTURE_350']["HEIGHT"]?>px;">
												<?$i=0; if ($arResult["PROPERTIES"]["NOVELTY"]["VALUE"]=="Y") {?><span class="new"></span><?$i++;}?>
												<?if ($arResult["PROPERTIES"]["HIT"]["VALUE"]=="Y") {?><span class="<?if ($i>0) echo "drop_";?>hit"></span><?$i++;}?>
												<?if ($arResult["PROPERTIES"]["BESTPRICE"]["VALUE"]=="Y") {?><span class="<?if ($i>0) echo "drop_";?>prc"></span><?}?>
											</div>
										</div>
									</div>
									<?endif;?>

									<div class="info1">
										<div class="info_body">
											
											<?if(count($arResult["MORE_PHOTO"])>0):?>
												<div class="other"><?=GetMessage("CATALOG_PICTURE")?></div>
												<ul>
													<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
													<li><div class="catalog-detail-image"><a rel="catalog-detail-images" href="<?=$PHOTO["SRC"]?>" title="<?=(strlen($PHOTO["DESCRIPTION"]) > 0 ? $PHOTO["DESCRIPTION"] : $arResult["NAME"])?>"><img border="0" src="<?=$PHOTO["SRC_PREVIEW"]?>" width="<?=$PHOTO["PREVIEW_WIDTH"]?>" height="<?=$PHOTO["PREVIEW_HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" /></a></div></li>
													<?endforeach;?>
												</ul>
											<?endif?>
											
											
											<?foreach($arResult["PRICES"] as $code=>$arPrice):
											$pr=PRICES($arPrice["VALUE"], $arParams['SHOW_FRACTION_PRICE']);
											$hundred=$pr[2];
											$thousand=$pr[1];

											$pr2=PRICES($arPrice["DISCOUNT_VALUE"], $arParams['SHOW_FRACTION_PRICE']);
											$hundred2=$pr2[2];
											$thousand2=$pr2[1];
											
											endforeach;?>
											
											<table width="50%" cellpadding="0" cellspacing="0" border="0">
												<tr>
													<td>	
														<?if($arPrice["CAN_ACCESS"]):?>
															<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
																<div class="price"><strong><?=$thousand2?></strong><?=$hundred2?>-</div>
															<?else:?>
																<div class="price"><strong><?=$thousand?></strong><?=$hundred?>-</div>
															<?endif;?>
														<?endif;?>
														
														
														<div class="buy">
															<?if ($arResult['CAN_BUY']):?>
																<a href="<?=$arResult["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_detail_image', 'detail', '<?=GetMessage("CATALOG_IN_BASKET")?>');" id="catalog_add2cart_link"><img src="<?=SITE_TEMPLATE_PATH?>/images/btn_buy.png" width="79px" height="19px" border="0"/></a>
															<?elseif (count($arResult["PRICES"]) > 0):?>
																<span class="catalog-item-not-available"><!--noindex--><?=GetMessage("CATALOG_NOT_AVAILABLE");?><!--/noindex--></span>
															<?endif;?>
														</div>
													</td>
													<td>
														<div class="rating">
															<div style="float:left;"><?=GetMessage('CATALOG_ELEMENT_RATING')?> : </div>
																<?$APPLICATION->IncludeComponent(
																	"bitrix:iblock.vote",
																	"star_ajax",
																	Array(
																		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
																		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
																		"ELEMENT_ID" => $arResult['ID'],
																		"CACHE_TYPE" => "N",
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
																<?if ($arResult["CATALOG_QUANTITY"]>0) {?><em class="yes"><?=GetMessage('CATALOG_ELEMENT_YES')?></em><?}
																else {?><em class="no"><?=GetMessage('CATALOG_ELEMENT_NO')?></em><?}?>
															<?=GetMessage('CATALOG_ELEMENT_FEEDBACK')?> : 
																<strong><?if ($arResult["PROPERTIES"]["FORUM_MESSAGE_CNT"]["VALUE"]!="") echo $arResult["PROPERTIES"]["FORUM_MESSAGE_CNT"]["VALUE"]; else echo "0";?></strong>
														</div>
														<div class="clear"></div>	
														<?if($arParams["USE_COMPARE"]):?>
																					
															<div class="lable" style="margin-left: 10px; margin-top: 10px;">
																<a href="<?echo $arResult['COMPARE_URL']?>" class="catalog-item-compare" onclick="addToCompare(this, '<?=GetMessage("CATALOG_COMPARE")?>', '<?=GetMessage("CATALOG_IN_COMPARE")?>', <?=$arResult['ID']?>, '<?=$arParams["COMPARE_NAME"]?>', <?=$arParams["IBLOCK_ID"]?>); return false;" rel="nofollow" id="catalog_add2compare_link_<?=$arResult['ID']?>">
																	<input id="check_<?=$arElement['ID']?>" name="compare[]" type="checkbox" value="ON" />
																	<label for="check_<?=$arElement['ID']?>"><?echo GetMessage("CATALOG_COMPARE")?></label>
																</a>
															</div>
														<?endif?>
													</td>
												</tr>
											</table>
											<br/>
											<div><?=$arResult['DETAIL_TEXT']?></div>
											<h2>
											<?=GetMessage("CATALOG_TECH")?>
											</h2>
											
										</div>									
									
									<div class="main_content1">
										<table cellpadding="0" cellspacing="0" border="0" class="table_polos">
											<?
											if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0):
											?>
												
													<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
													<?if (!in_array($arProperty["CODE"],array("ACCESSORY", "ANALOG", "PROPANALOG", "PERCENT_FOR_NUM","HIT", "NOVELTY","BESTPRICE" , "PERCENT_FOR_PROPERTY", "vote_count", "vote_sum", "rating"))):?>
														<tr>
															<td><b><?=$arProperty["NAME"]?></b></td>
															<td style="border-right: none;">														
															<?
															if(is_array($arProperty["DISPLAY_VALUE"])):
																echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
															elseif($pid=="MANUAL"):
															?>
																		<a href="<?=$arProperty["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a>
															<?
															else:
																echo $arProperty["DISPLAY_VALUE"];
															endif;
															?>
															</td>		
														</tr>	
												<?endif;?>
												<?endforeach;?>
											<?endif;?>
										</table>	
									</div>
									<div class="clear"></div>
									
								<?if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):?>	
								<div class="offers">
									<table>
										<tr>
											<td colspan="2">
															<div class="cart1">
																		<?=GetMessage('CATALOG_VARIANT')?>
															</div>
														</td>
										</tr>
										<tr>
											<td width="5%" >
											</td>
											<td width="95%">
											<?foreach($arResult["OFFERS"] as $arOffer):?>
												<table cellpadding="0" cellspacing="0" border="0">
													<tr>
														<td>
															<div class="image">
																<div>
																	<img alt="<?=$arOffer["NAME"]?>" title="<?=$arOffer["NAME"]?>" id="catalog_list_image_<?=$arOffer['ID']?>" src="<?=CFile::GetPath(CFile::GetPath($arOffer["PREVIEW_PICTURE"]))?>"/>
																		
																			<?$i=0; if ($arOffer["DISPLAY_PROPERTIES"]["NOVELTY"]["DISPLAY_VALUE"]=="Y") {?><span class="new"></span><?$i++;}?>
																			<?if ($arOffer["DISPLAY_PROPERTIES"]["HIT"]["DISPLAY_VALUE"]=="Y") {?><span class="<?if ($i>0) echo "drop_";?>hit"></span><?$i++;}?>
																			<?if ($arOffer["DISPLAY_PROPERTIES"]["BESTPRICE"]["DISPLAY_VALUE"]=="Y") {?><span class="<?if ($i>0) echo "drop_";?>super"></span><?}?>
																		
																</div>
																		
															</div>
														</td>
														<td>
														<div class="info">
															<?foreach($arOffer["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
															<?if (!in_array($arProperty["CODE"], array("rating", "vote_count", "vote_sum", "BESTPRICE", "NOVELTY", "HIT", "PRODUSER"))):?>
																<?=$arProperty["NAME"]?>:&nbsp;<?
																	if(is_array($arProperty["DISPLAY_VALUE"]))
																		echo "<font color='green'><b>".implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"])."</b></font>";
																	else
																		echo "<font color='green'><b>".$arProperty["DISPLAY_VALUE"]."</b></font>";?><br />
															<?endif;?>
														<?endforeach?>
														<?=GetMessage('CATALOG_AVAILABLE')?> : 
																<?if ($arOffer["CATALOG_QUANTITY"]>0) {?><em class="yes"><?=GetMessage('CATALOG_ELEMENT_YES')?></em><?}
																else {?><em class="no"><?=GetMessage('CATALOG_ELEMENT_NO')?></em><?}?>
														</div>
														</td>
													</tr>
													<tr>
														<td>
																<?
																$minPriseArr=array();
																foreach($arOffer["PRICES"] as $code=>$arPrice):																		
																		
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
																	
																	<?if($minPriseArr["DISCOUNT_VALUE"] < $minPriseArr["VALUE"]):?>
																		<p class="price"><strong><?=$thousand2?></strong><?=$hundred2?>-</p>
																	<?else:?>
																		<p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p>
																	<?endif;
																endif;
																?>
														</td>
														<td>
															<div class="info">
																<a href="<?=$arOffer["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arOffer['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_BASKET")?>');" id="catalog_add2cart_link_<?=$arOffer['ID']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/btn_buy.png" width="79px" height="19px" border="0"/></a>
															</div>
														</td>
													</tr>
												</table>
											<?endforeach;?>
											</td>
										</tr>
									</table>							
									</div>
									<?endif;?>
									
									
									
									
								</div>
								
							</td>
						</tr>
						
						
						
						<tr>
							<td colspan="4" style="padding-left:10px;">
								<div class="maintext">
									<h2><?=GetMessage("CATALOG_ELEMENT_FEEDBACK")?> <font color="gray">(<?if ($arResult["PROPERTIES"]["FORUM_MESSAGE_CNT"]["VALUE"]!="") echo $arResult["PROPERTIES"]["FORUM_MESSAGE_CNT"]["VALUE"]; else echo "0";?>)</font></h2>
								</div>
								<div class="item"></div>
							</td>
						</tr>
						<tr>
							<td colspan="4" style="padding-left:10px;" id="feedback">
						<?if($arParams["USE_REVIEW"]=="Y" && IsModuleInstalled("forum") && $arResult['ID']):?>
							<?$APPLICATION->IncludeComponent("bitrix:forum.topic.reviews", "template1", Array(
								"CACHE_TYPE" => $arParams["CACHE_TYPE"],	// Тип кеширования
								"CACHE_TIME" => $arParams["CACHE_TIME"],	// Время кеширования (сек.)
								"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],	// Количество сообщений на одной странице
								"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],	// Использовать CAPTCHA
								"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],	// Путь относительно корня сайта к папке со смайлами
								"FORUM_ID" => $arParams["FORUM_ID"],	// ID форума для отзывов
								"URL_TEMPLATES_READ" => $arParams["URL_TEMPLATES_READ"],	// Страница чтения темы форума
								"SHOW_LINK_TO_FORUM" => $arParams["SHOW_LINK_TO_FORUM"],	// Показать ссылку на форум
								"ELEMENT_ID" => $arResult["ID"],	// ID элемента
								"IBLOCK_ID" => $arParams["IBLOCK_ID"],	// Код информационного блока
								"POST_FIRST_MESSAGE" => $arParams["POST_FIRST_MESSAGE"],	// Начинать тему текстом элемента
								"URL_TEMPLATES_DETAIL" => $arParams["POST_FIRST_MESSAGE"]==="Y"?$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"]:"",	// Страница элемента инфоблока
								),
								$component
							);?>
						<?endif?>
							</td>
						</tr>											
						<tr>
							<td colspan="4" style="padding-left:10px;">
								<div class="maintext">
									<h2><?=GetMessage("CATALOG_ANALOG")?>&nbsp;&nbsp;<font color="gray">(<span id='an_count'>0</span>)</font></h2>
								</div>
								<div class="item"></div>
							</td>
						</tr>

						<tr align="left">
							<td colspan="4" style="padding-left:10px;">
							<?
										if($arResult["DISPLAY_PROPERTIES"]["PROPANALOG"]["VALUE"])
													$mas=explode(",", $arResult["DISPLAY_PROPERTIES"]["PROPANALOG"]["VALUE"]);
										elseif($arParams["PROPANALOG"])
										{
											$mas=$arParams["PROPANALOG"];
											$arResult["DISPLAY_PROPERTIES"]["PERCENT_FOR_PROPERTY"]["VALUE"]=$arParams["PERCENT_FOR_PROPERTY"];
											$arResult["DISPLAY_PROPERTIES"]["PERCENT_FOR_NUM"]["VALUE"]=$arParams["PERCENT_FOR_NUM"];
										}
										
										?>
										<?$APPLICATION->IncludeComponent("bitrix.household:catalog.analog", "analog", array(
											"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
											"IBLOCK_ID" => $arParams["IBLOCK_ID"],
											"SECTION_ID" => "",
											"SECTION_CODE" => $arVariables['SECTION_CODE'],
											"ELEMENT_ID" => $arResult['ID'],
											"PERCENT_FOR_NUM" => $arResult["DISPLAY_PROPERTIES"]["PERCENT_FOR_NUM"]["VALUE"],
											"PERCENT_FOR_PROPERTY" => $arResult["DISPLAY_PROPERTIES"]["PERCENT_FOR_PROPERTY"]["VALUE"],
											"PROPERTY_CODE_ANALOG" => $mas,
											"SECTION_USER_FIELDS" => array(
												0 => "PRODUSER",
												1 => "",
											),
											"ELEMENT_SORT_FIELD" => "sort",
											"ELEMENT_SORT_ORDER" => "asc",
											"FILTER_NAME" => "arrFilter",
											"INCLUDE_SUBSECTIONS" => "Y",
											"SHOW_ALL_WO_SECTION" => "Y",
											"PAGE_ELEMENT_COUNT" => "6",
											"LINE_ELEMENT_COUNT" => "3",
											"SECTION_URL" => "",
											"DETAIL_URL" => "",
											"BASKET_URL" => "/personal/basket.php",
											"ACTION_VARIABLE" => "action",
											"PRODUCT_ID_VARIABLE" => "id",
											"PRODUCT_QUANTITY_VARIABLE" => "quantity",
											"PRODUCT_PROPS_VARIABLE" => "prop",
											"SECTION_ID_VARIABLE" => "SECTION_ID",
											"AJAX_MODE" => "N",
											"AJAX_OPTION_SHADOW" => "Y",
											"AJAX_OPTION_JUMP" => "N",
											"AJAX_OPTION_STYLE" => "Y",
											"AJAX_OPTION_HISTORY" => "N",
											"CACHE_TYPE" => "N",
											"CACHE_TIME" => "36000000",
											"CACHE_GROUPS" => "Y",
											"META_KEYWORDS" => "-",
											"META_DESCRIPTION" => "-",
											"BROWSER_TITLE" => "-",
											"ADD_SECTIONS_CHAIN" => "N",
											"DISPLAY_COMPARE" => "N",
											"SET_TITLE" => "Y",
											"SET_STATUS_404" => "N",
											"CACHE_FILTER" => "N",
											"PRICE_CODE" => array(
												0 => "BASE",
											),
											"USE_PRICE_COUNT" => "N",
											"SHOW_PRICE_COUNT" => "1",
											"PRICE_VAT_INCLUDE" => "Y",
											"PROPERTY_CODE" => array(
											0 => "PRODUSER"),
											"USE_PRODUCT_QUANTITY" => "N",
											"DISPLAY_TOP_PAGER" => "N",
											"DISPLAY_BOTTOM_PAGER" => "N",
											"PAGER_TITLE" => "Товары",
											"PAGER_SHOW_ALWAYS" => "N",
											"PAGER_TEMPLATE" => "",
											"PAGER_DESC_NUMBERING" => "N",
											"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
											"PAGER_SHOW_ALL" => "N",
											"AJAX_OPTION_ADDITIONAL" => ""
											),
											false
										);?>
							</td>
						</tr>
						<?if (count($arResult["DISPLAY_PROPERTIES"]["ACCESSORY"]["VALUE"])!=0):?>
						
						<tr>
							<td colspan="4" style="padding-left:10px;">
								<div class="maintext">
									<h2><?=GetMessage("CATALOG_ACCESS")?>&nbsp;&nbsp;<font color="gray">(<?=count($arResult["DISPLAY_PROPERTIES"]["ACCESSORY"]["VALUE"])?>)</font></h2>
								</div>
								<div class="item"></div>
							</td>
						</tr>

						<tr align="left">
							<td colspan="4" style="padding-left:10px;">
							<?
									global $Filter;
									$Filter=array();
									if ($arResult["DISPLAY_PROPERTIES"]["ACCESSORY"]["VALUE"]=="") $arResult["DISPLAY_PROPERTIES"]["ACCESSORY"]["VALUE"][]=-1;									
									$Filter=array("ID"=>$arResult["DISPLAY_PROPERTIES"]["ACCESSORY"]["VALUE"], "IBLOCK_ID" => $arResult['ACCESSORY_IBLOCKS'], 'ACTIVE'=>'Y');?>
										<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "access", array(
											"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
											"IBLOCK_ID" => $arParams["IBLOCK_ID"],
											"SECTION_ID" => "",
											"SECTION_CODE" => "",
											"SECTION_USER_FIELDS" => array(
												0 => "",
												1 => "",
											),
											"ELEMENT_SORT_FIELD" => "sort",
											"ELEMENT_SORT_ORDER" => "asc",
											"FILTER_NAME" => "Filter",
											"INCLUDE_SUBSECTIONS" => "Y",
											"SHOW_ALL_WO_SECTION" => "Y",
											"PAGE_ELEMENT_COUNT" => "30",
											"LINE_ELEMENT_COUNT" => "3",
											"SECTION_URL" => "",
											"DETAIL_URL" => "",
											"BASKET_URL" => "/personal/basket.php",
											"ACTION_VARIABLE" => "action",
											"PRODUCT_ID_VARIABLE" => "id",
											"PRODUCT_QUANTITY_VARIABLE" => "quantity",
											"PRODUCT_PROPS_VARIABLE" => "prop",
											"SECTION_ID_VARIABLE" => "SECTION_ID",
											"AJAX_MODE" => "N",
											"AJAX_OPTION_SHADOW" => "Y",
											"AJAX_OPTION_JUMP" => "N",
											"AJAX_OPTION_STYLE" => "Y",
											"AJAX_OPTION_HISTORY" => "N",
											"CACHE_TYPE" => "N",
											"CACHE_TIME" => "36000000",
											"CACHE_GROUPS" => "Y",
											"META_KEYWORDS" => "-",
											"META_DESCRIPTION" => "-",
											"BROWSER_TITLE" => "-",
											"ADD_SECTIONS_CHAIN" => "N",
											"DISPLAY_COMPARE" => "N",
											"SET_TITLE" => "Y",
											"SET_STATUS_404" => "N",
											"CACHE_FILTER" => "N",
											"PRICE_CODE" => array(
												0 => "BASE",
											),
											"USE_PRICE_COUNT" => "N",
											"SHOW_PRICE_COUNT" => "1",
											"PRICE_VAT_INCLUDE" => "Y",
											"PRODUCT_PROPERTIES" => array(
											),
											"PROPERTY_CODE" => array(
											0 => "PRODUSER"),
											"USE_PRODUCT_QUANTITY" => "N",
											"DISPLAY_TOP_PAGER" => "N",
											"DISPLAY_BOTTOM_PAGER" => "N",
											"PAGER_TITLE" => "Товары",
											"PAGER_SHOW_ALWAYS" => "N",
											"PAGER_TEMPLATE" => "",
											"PAGER_DESC_NUMBERING" => "N",
											"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
											"PAGER_SHOW_ALL" => "N",
											"AJAX_OPTION_ADDITIONAL" => ""
											),
											false
										);?>
							</td>
						</tr>
						
						<?endif;?>
						
						<tr>
							<td colspan="4" style="padding-left:10px;">
							
							
					
