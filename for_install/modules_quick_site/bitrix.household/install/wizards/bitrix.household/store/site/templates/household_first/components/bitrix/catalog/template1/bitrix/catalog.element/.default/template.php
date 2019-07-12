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

<script type="text/javascript">
function allClose(id){
	var list = document.getElementById("prop").getElementsByTagName("div");
	for(var i=0;i<list.length;i++){
		list[i].style.display = "none";
	}
	document.getElementById(id).style.display = "block";
}
function openMenu(node){
	var subMenu = node.parentNode.getElementsByTagName("ul")[0];
	subMenu.style.display == "none" ? subMenu.style.display = "block" : subMenu.style.display = "none";
}
</script>

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
<div class="catalog-detail">
	

		<?if (is_array($arResult['DETAIL_PICTURE_350']) || count($arResult["MORE_PHOTO"])>0):?>
			
			<?if (is_array($arResult['DETAIL_PICTURE_350'])):?>
			<div class="big_prev">
				<div class="catalog-detail-image" id="catalog-detail-main-image">
					<a rel="catalog-detail-images" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>" title="<?=(strlen($arResult["DETAIL_PICTURE"]["DESCRIPTION"]) > 0 ? $arResult["DETAIL_PICTURE"]["DESCRIPTION"] : $arResult["NAME"])?>"><img src="<?=$arResult['DETAIL_PICTURE_350']['SRC']?>" alt="<?=$arResult["NAME"]?>" id="catalog_detail_image" width="<?=$arResult['DETAIL_PICTURE_350']["WIDTH"]?>" height="<?=$arResult['DETAIL_PICTURE_350']["HEIGHT"]?>" /></a>
					<div style="position:relative; top:-<?=$arResult['DETAIL_PICTURE_350']["HEIGHT"]?>px;">
						<?if ($arResult["PROPERTIES"]["NOVELTY"]["VALUE"]=="Y") {?><span class="new"></span><?}?>
						<?if ($arResult["PROPERTIES"]["HIT"]["VALUE"]=="Y") {?><span class="drop_hit"></span><?}?>
						<?if ($arResult["PROPERTIES"]["BESTPRICE"]["VALUE"]=="Y") {?><span class="prc"></span><?}?>
					</div>
				</div>
			</div>
			<?endif;?>
			
			 <div class="info">
             	<div class="info_body">
						                        		
							<?if(count($arResult["MORE_PHOTO"])>0):?>
							<ul>
								<?foreach($arResult["MORE_PHOTO"] as $PHOTO):
							?>
								<li><div class="catalog-detail-image"><a rel="catalog-detail-images" href="<?=$PHOTO["SRC"]?>" title="<?=(strlen($PHOTO["DESCRIPTION"]) > 0 ? $PHOTO["DESCRIPTION"] : $arResult["NAME"])?>"><img border="0" src="<?=$PHOTO["SRC_PREVIEW"]?>" width="<?=$PHOTO["PREVIEW_WIDTH"]?>" height="<?=$PHOTO["PREVIEW_HEIGHT"]?>" alt="<?=$arResult["NAME"]?>" /></a></div></li>
								<?endforeach;?>
							</ul>
							<?endif?>
						
						<div class="clear"></div>
						
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
						<?if($arParams["USE_COMPARE"]):?>							
							<div class="lable">
								<a href="<?echo $arResult['COMPARE_URL']?>" class="catalog-item-compare" onclick="addToCompare(this, '<?=GetMessage("CATALOG_COMPARE")?>', '<?=GetMessage("CATALOG_IN_COMPARE")?>', <?=$arResult['ID']?>, '<?=$arParams["COMPARE_NAME"]?>', <?=$arParams["IBLOCK_ID"]?>); return false;" rel="nofollow" id="catalog_add2compare_link_<?=$arResult['ID']?>">
									<input id="check_<?=$arElement['ID']?>" name="compare[]" type="checkbox" value="ON" />
									<label for="check_<?=$arElement['ID']?>"><?echo GetMessage("CATALOG_COMPARE")?></label>
								</a>
							</div>
						<?endif?>
						<div class="catalog-detail-price">
						<?
						$minPriseArr=array();
						foreach($arResult["PRICES"] as $code=>$arPrice):
								
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
								<p>
								<?if($minPriseArr["DISCOUNT_VALUE"] < $minPriseArr["VALUE"]):?>
									<p class="price"><strong><?=$thousand2?></strong><?=$hundred2?>-</p>
								<?else:?>
									<p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p>
								<?endif;?>
								</p>
							<?	
						endif;
						?>
						</div>
      											
						<?if ($arResult['CAN_BUY']):?>
							<a href="<?=$arResult["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_detail_image', 'detail', '<?=GetMessage("CATALOG_IN_BASKET")?>');" id="catalog_add2cart_link"><img src="<?=SITE_TEMPLATE_PATH?>/images/button_buy.gif" width="79px" height="19px" alt="˳𐩲" /></a>
						<?elseif (count($arResult["PRICES"]) > 0):?>
							<span class="catalog-item-not-available"><!--noindex--><?=GetMessage("CATALOG_NOT_AVAILABLE");?><!--/noindex--></span>
						<?endif;?>
						

					<?if($arResult["PREVIEW_TEXT"]):?>
						<div class="catalog-preview"><?=$arResult["PREVIEW_TEXT"];?></div>
					<?endif;?>
				
					<?if($arResult["DETAIL_TEXT"]):?>
					<br/><div class="catalog-detail">
						<h4><?=GetMessage('CATALOG_FULL_DESC')?></h4>
						<?=$arResult["DETAIL_TEXT"];?>
					</div>
					<?endif;?>
						
					</div>				
				</div>


	
	<?if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):?>
	<div class="offers">
	<b><?=GetMessage('CATALOG_VARIANT')?></b>
		<?foreach($arResult["OFFERS"] as $arOffer):?>
	
			
			<table width="100%" border=0 class="taboffer">
			<tr>
				<td class="image" width="50%">
					<div class="catalog-item-image">
						<img alt="<?=$arOffer["NAME"]?>" title="<?=$arOffer["NAME"]?>" id="catalog_list_image_<?=$arOffer['ID']?>" src="<?=CFile::GetPath(CFile::GetPath($arOffer["PREVIEW_PICTURE"]))?>"/>
						<div style="position:relative; top:-10px;">
							<?if ($arOffer["DISPLAY_PROPERTIES"]["NOVELTY"]["DISPLAY_VALUE"]=="Y") {?><span class="new"></span><?}?>
							<?if ($arOffer["DISPLAY_PROPERTIES"]["HIT"]["DISPLAY_VALUE"]=="Y") {?><span class="drop_hit"></span><?}?>
							<?if ($arOffer["DISPLAY_PROPERTIES"]["BESTPRICE"]["DISPLAY_VALUE"]=="Y") {?><span class="prc"></span><?}?>
						</div>
					</div>
				</td>
				<td width="50%">
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
				</td>
			</tr>
			<tr>
				<td>
						<?foreach($arOffer["PRICES"] as $code=>$arPrice):
								$pr=PRICES($arPrice["VALUE"], $arParams['SHOW_FRACTION_PRICE']);
								$hundred=$pr[2];
								$thousand=$pr[1];

								$pr2=PRICES($arPrice["DISCOUNT_VALUE"], $arParams['SHOW_FRACTION_PRICE']);
								$hundred2=$pr2[2];
								$thousand2=$pr2[1];
								
							if($arPrice["CAN_ACCESS"]):
						?>
							<p>
							<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
								<p class="price"><strong><?=$thousand2?></strong><?=$hundred2?>-</p>
							<?else:?>
								<p class="price"><strong><?=$thousand?></strong><?=$hundred?>-</p>
							<?endif;?>
							</p>
						<?
								break;
							endif;
						endforeach;
						?>
				
				</td>
				<td>
					<a href="<?=$arOffer["ADD_URL"]?>" class="catalog-item-buy<?/*catalog-item-in-the-cart*/?>" rel="nofollow"  onclick="return addToCart(this, 'catalog_list_image_<?=$arOffer['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_BASKET")?>');" id="catalog_add2cart_link_<?=$arOffer['ID']?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/button_buy.gif" width="79px" height="19px" alt="˳𐩲" /></a>
				</td>
			</tr>

			</table>
		<?endforeach;?>
		</div>
	<?endif?>
				
	<?global $USER;
	//if ($USER->IsAdmin())
	$nonTechPropDispl=array();
	$nonTechProp=array(
						"ACCESSORY", "ANALOG", "PROPANALOG", 
						"PERCENT_FOR_NUM", "PERCENT_FOR_PROPERTY", 
						"vote_count", "vote_sum", "rating", "BESTPRICE", "HIT", "NOVELTY"
					);
	$nonTechPropKeys=array_flip($nonTechProp);	
	$nonTechPropDispl=array_diff_key($arResult["DISPLAY_PROPERTIES"], $nonTechPropKeys);		
	?>
	
				<div class="clear"></div>
                        	<div class="tabs">
                        		<ul class="technical">
	                         		<?if ((is_array($nonTechPropDispl) && count($nonTechPropDispl) > 0) || $USER->IsAdmin()):?>
	                         			<li><a class="" href="#tech"><?=GetMessage("CATALOG_TECH")?></a></li>
	                         		<?endif?>
	                         		<?if($arResult["DISPLAY_PROPERTIES"]["ANALOG"]["VALUE"] || $arResult["DISPLAY_PROPERTIES"]["PROPANALOG"]["VALUE"] || $USER->IsAdmin()  || $arParams["PROPANALOG"]):?>
	                         			<li><a class="" href="#analog"><?=GetMessage("CATALOG_ANALOG")?></a></li>
	                         		<?endif?>
	                         		<? if($arResult["DISPLAY_PROPERTIES"]["ACCESSORY"]["VALUE"] || $USER->IsAdmin()):?>
	                         			<li><a class="" href="#ac�"><?=GetMessage("CATALOG_ACCESS")?></a></li>
	                         		<?endif?>
	                         		<li><a class="" href="#available"><?=GetMessage("CATALOG_SHOP")?></a></li>
	                         		<li><a class="" href="#feedback"><?=GetMessage("CATALOG_FEEDBACK")?></a></li>
	                         	</ul>
							<?if (is_array($nonTechPropDispl) && count($nonTechPropDispl) > 0):?>
								<div id="tech">
									<table cellpadding="0" cellspacing="0" border="0">	
										<?foreach($nonTechPropDispl as $pid=>$arProperty):?>											
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
										<?endforeach;?>																		
									</table>
								</div>
							<?elseif($USER->IsAdmin()):?>
								<div id="tech">
									<?=GetMessage("TECH_NOT_SET")?>
								</div>
							<?endif;?>
							<?if($arResult["DISPLAY_PROPERTIES"]["ANALOG"]["VALUE"] || $arResult["DISPLAY_PROPERTIES"]["PROPANALOG"]["VALUE"] || $arParams["PROPANALOG"]):?>
								<div id="analog">
									<?if($arResult["DISPLAY_PROPERTIES"]["ANALOG"]["VALUE"]):?>
										<?
										global $anFilter;
										$anFilter=array();									
										$anFilter=array("ID"=>$arResult["DISPLAY_PROPERTIES"]["ANALOG"]["VALUE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"], 'ACTIVE'=>'Y');
										?>
											<?$APPLICATION->IncludeComponent("bitrix:catalog.section", "access", array(
												"IBLOCK_TYPE" => "catalog",
												"IBLOCK_ID" => $arParams["IBLOCK_ID"],
												"SECTION_ID" => "",
												"SECTION_CODE" => "",
												"SECTION_USER_FIELDS" => array(
													0 => "",
													1 => "",
												),
												"ELEMENT_SORT_FIELD" => "sort",
												"ELEMENT_SORT_ORDER" => "asc",
												"FILTER_NAME" => "anFilter",
												"INCLUDE_SUBSECTIONS" => "Y",
												"SHOW_ALL_WO_SECTION" => "Y",
												"PAGE_ELEMENT_COUNT" => "30",
												"LINE_ELEMENT_COUNT" => "3",
												"PROPERTY_CODE" => array(
													0 => "",
													1 => "",
												),
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
												"PAGER_TITLE" => "Ӯ㡰",
												"PAGER_SHOW_ALWAYS" => "N",
												"PAGER_TEMPLATE" => "",
												"PAGER_DESC_NUMBERING" => "N",
												"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
												"PAGER_SHOW_ALL" => "N",
												"AJAX_OPTION_ADDITIONAL" => ""
												),
												false
											);?>
										<?endif?>
										<?if($arResult["DISPLAY_PROPERTIES"]["PROPANALOG"]["VALUE"] || $arParams["PROPANALOG"]):?>								
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
												"PROPERTY_CODE" => array(
												0 => "PRODUSER"),
												"USE_PRODUCT_QUANTITY" => "N",
												"DISPLAY_TOP_PAGER" => "N",
												"DISPLAY_BOTTOM_PAGER" => "N",
												"PAGER_TITLE" => "Ӯ㡰",
												"PAGER_SHOW_ALWAYS" => "N",
												"PAGER_TEMPLATE" => "",
												"PAGER_DESC_NUMBERING" => "N",
												"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
												"PAGER_SHOW_ALL" => "N",
												"AJAX_OPTION_ADDITIONAL" => ""
												),
												false
											);?>
										<?endif?>
									</div>
								<?elseif($USER->IsAdmin()):?>
									<div id="analog">
										<?=GetMessage("ANALOG_NOT_SET")?>
									</div>
								<?endif;?>
								<?if($arResult["DISPLAY_PROPERTIES"]["ACCESSORY"]["VALUE"]):?>
									<div id="ac�">
										<?
										global $Filter;
										$Filter=array();									
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
												"PAGER_TITLE" => "Ӯ㡰",
												"PAGER_SHOW_ALWAYS" => "N",
												"PAGER_TEMPLATE" => "",
												"PAGER_DESC_NUMBERING" => "N",
												"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
												"PAGER_SHOW_ALL" => "N",
												"AJAX_OPTION_ADDITIONAL" => ""
												),
												false
											);?>
									</div>
								<?elseif($USER->IsAdmin()):?>
									<div id="ac�">
										<?=GetMessage("ACPLI_NOT_SET")?>
									</div>
								<?endif;?>																		
									<div id="available">
										<?=GetMessage('CATALOG_AVAILABLE')?> : 
										<?if ($arResult["CATALOG_QUANTITY"]>0) {?><em class="yes"><?=$arResult["CATALOG_QUANTITY"]?> <?=GetMessage('CATALOG_COUNT')?></em><?}
										else {?><em class="no"><?=GetMessage('CATALOG_ELEMENT_NO')?></em><?}?>
									</div>
									
                        	</div>			
		<?endif;?>
<?
if (is_array($arResult['DISPLAY_PROPERTIES']) && count($arResult['DISPLAY_PROPERTIES']) > 0):
?>
	<?$arProperty = $arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]?>
	
	<?if(count($arProperty["DISPLAY_VALUE"]) > 0):?>
	<div class="catalog-detail-recommends">
		<h4><?=$arProperty["NAME"]?></h4>
			<div class="catalog-detail-recommend">
			<?
			global $arRecPrFilter;
			$arRecPrFilter["ID"] = $arResult["DISPLAY_PROPERTIES"]["RECOMMEND"]["VALUE"];
			$APPLICATION->IncludeComponent("bitrix:store.catalog.top", "", array(
				"IBLOCK_TYPE" => "",
				"IBLOCK_ID" => "",
				"ELEMENT_SORT_FIELD" => "sort",
				"ELEMENT_SORT_ORDER" => "desc",
				"ELEMENT_COUNT" => $arParams["ELEMENT_COUNT"],
				"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"DISPLAY_COMPARE" => "N",
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"FILTER_NAME" => "arRecPrFilter",
				"ELEMENT_COUNT" => 30,
				),
				$component
			);
			?>
		</div>
	</div>
	<?unset($arResult["DISPLAY_PROPERTIES"]["RECOMMEND"])?>
	<?endif;?>
<?endif;?>

<div id="prop">	
</div>

</div>
</div>