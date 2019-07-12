<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if(count($arResult["ITEMS"]) > 0): 
$massToJs='[';
$i=1;
foreach($arParams["PRODUCT_PROPERTIES"] as $pid)
{
	$massToJs.="'".$pid."'";
	if($i!=count($arParams["PRODUCT_PROPERTIES"]))
		$massToJs.=",";
	$i++;
}
$massToJs.=']';
?>
<?//echo "<pre>".print_r($arResult,true)."</pre>";?>

<div class="box2">
    <div class="border-top">
        <div class="border-right">
            <div class="border-bot">
                <div class="border-left">
                    <div class="left-top-corner">
                        <div class="right-top-corner">
                            <div class="right-bot-corner">
                                <div class="left-bot-corner">
                                    <div class="inner">
<!-- title begin -->
<div class="title-box1">
    <div class="left">
        <div class="right">
            <h3>
            	<?if($arParams["SECTION_URL"]):?>
            		<a href="<?=$arParams["SECTION_URL"]?>">
            	<?endif?>
            	<?=GetMessage("NEW_OFFERS_TITLE")?>
            	<?if($arParams["SECTION_URL"]):?>
            		</a>
            	<?endif?>
            </h3>
        </div>
    </div>
</div>
<!-- title end -->

<table class="list_card" width="100%" cellspacing="0" cellspadding="0">
<?$i=0;
foreach ($arResult['ITEMS'] as $key => $arElement):
	$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCT_ELEMENT_DELETE_CONFIRM')));
	$i++;?>
<?=($i%2>0 ? "<tr>" : "")?>
<td width="50%" class="item <?=($i%2>0 ? "itemLeft" : "itemRight")?>">
	<div id="<?=$this->GetEditAreaId($arElement['ID']);?>" itemscope itemtype = "http://schema.org/Product" class="<?=($i%2>0 ? "left" : "right")?>">
	<div class="item_body">
		    <?$bPicture = is_array($arElement["PREVIEW_IMG"]);
			if ($bPicture):?>
	        		<div class="image">
				    <a href="<?=$arElement["DETAIL_PAGE_URL"]?>">
						<img src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="catalog_link_image_new_<?=$arElement['ID']?>" />		
				    </a>
				</div>
			<?endif;?>
			<?if($arElement["PROPERTIES"]["SALELEADER"]["VALUE"]){?>
	        		<div class="hit"></div>        	
			<?}?>
			<form action="<?=SITE_DIR?>include/addToCartAjax.php" method="post" enctype="multipart/form-data">	
				<div class="info">
					<h2><a href="<?=$arElement["DETAIL_PAGE_URL"]?>" itemprop="name"><?=$arElement["NAME"]?></a></h2>
				<?if (is_array($arElement['DISPLAY_PROPERTIES']) && count($arElement['DISPLAY_PROPERTIES']) > 0):?>		    
					    <?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):
					    	if($pid=="SALELEADER")
					    		continue;?>
							<?if(in_array($pid, $arParams["PRODUCT_PROPERTIES"])):?>
								<div class="propCaption"><?=$arProperty["NAME"]?>:&nbsp;</div>	
							<?else:?>
								<?=$arProperty["NAME"]?>:&nbsp;
							<?endif?>	
							<?if(is_array($arProperty["DISPLAY_VALUE"]) && in_array($pid, $arParams["PRODUCT_PROPERTIES"])):?>						
									<div style="float:left;" class="stylized_select select<?=$pid?>">
										<select id="select_<?=$arElement['ID']?>_<?=$pid?>" size="1" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" onchange="this.parentNode.getElementsByTagName('input')[0].value = this.options[this.selectedIndex].innerHTML;checkDisableAddToCart(<?=$massToJs?>, 'catalog_add2cart_link_<?=$arElement['ID']?>', <?=$arElement['ID']?>, 'list', '/include/addToCartAjax.php');">							
											<?$selectedVal=0;?>
											<?foreach($arElement["PRODUCT_PROPERTIES"][$pid]["VALUES"] as $k => $v):
												if($k == $arElement["PRODUCT_PROPERTIES"][$pid]["SELECTED"])
													$selectedVal=$v;?>									
												<option value="<?echo $k?>" <?if($k == $arElement["PRODUCT_PROPERTIES"][$pid]["SELECTED"]) echo '"selected"'?>><?echo $v?></option>
											<?endforeach;?>
										</select>
										<div class="input_wrapper">
											<input type="text" value="<?=$selectedVal?>" name="noname">
										</div>
									</div>
								<?elseif( in_array($pid, $arParams["PRODUCT_PROPERTIES"]) ):?>
									<input type="hidden" id="input_<?=$arElement['ID']?>_<?=$pid?>" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" value="<?=$arProperty["VALUE"][0]?>">						
									<div class="propCaption"><?echo $arProperty["DISPLAY_VALUE"];?></div>
								<?elseif(is_array($arProperty["DISPLAY_VALUE"])):		       
						        		echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
					       			elseif($pid=="MANUAL"):?>
									<a href="<?=$arProperty["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a>
			                	<?else:
						        echo $arProperty["DISPLAY_VALUE"];
					        endif;?>
					       <?if(in_array($pid, $arParams["PRODUCT_PROPERTIES"])):?>
							<div style="clear:both"></div>
						<?else:?>
							<br />
						<?endif?>	
				        <?endforeach;?>
				        
			        <?endif;?>				
			</div>
			<div style="clear:both"></div>
			<div class="priceSravBlock">
				<?$minPriseArr=array();
								foreach($arElement["PRICES"] as $code=>$arPrice):								
									if($arPrice["CAN_ACCESS"]):
										if($minPriseArr["VALUE"]>$arPrice["VALUE"] || !$minPriseArr["VALUE"])
											$minPriseArr=$arPrice;								
									endif;
						endforeach;
				if($minPriseArr):?>				
						<p class="price">
							<?=GetMessage("CATALOG_PRICE")?>						
							<?if($minPriseArr["DISCOUNT_VALUE"] < $minPriseArr["VALUE"]):?>
								<strong><?=$minPriseArr["PRINT_DISCOUNT_VALUE"]?></strong> 
								<br/><s style="padding:0;"><?=$minPriseArr["PRINT_VALUE"]?></s>
							<?else:?>
								<strong><?=$minPriseArr["PRINT_VALUE"]?></strong>
							<?endif;?>						
						</p>
				<?endif;?>			
			</div>
			<?if($arElement["CAN_BUY"]):?>
					<div class="button">
						<?if ($arElement['CAN_BUY']):?>
							<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" class="catalog-item-buy" value="&nbsp;" onclick="return addToCartForm(this.form, 'catalog_link_image_new_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>', this);" id="catalog_add2cart_link_<?=$arElement['ID']?>">
						<?elseif (count($arResult["PRICES"]) > 0):?>
							<?=GetMessage('CATALOG_NOT_AVAILABLE')?>
						<?endif;?>
					</div>
					 <input type="hidden" name="ajax_buy" value="1">
					 <input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="ADD2BASKET">
				         <input type="hidden" name="AJAX_CALL" value="Y">
				         <input type="hidden" name="ADD_PROPS" value="<?=implode(",",$arParams["PRODUCT_PROPERTIES"])?>">
					<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arElement["ID"]?>">
			<?endif;?>	
		</form>
	 </div>	
    </div>
  </td>
<?=($i%2>0 ? "" : '</tr><tr><td class="itemBotBg"></td><td class="itemBotBgRight"></td></tr><tr><td class="space"></td><td class="space"></td></tr>')?>
<? 
endforeach;?>
</table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?endif;?>