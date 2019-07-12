<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
<div class="title-box4">
    <div class="left">
        <div class="right">
            <h2>
            	<?if($arParams["SECTION_URL"]):?>
            		<a href="<?=$arParams["SECTION_URL"]?>">
            	<?endif?>
            	<?=GetMessage("POPULAR_TITLE")?>
            	<?if($arParams["SECTION_URL"]):?>
            		</a>
            	<?endif?>            	
            </h2>
        </div>
    </div>
</div>
<!-- title end -->
<div class="inner1">
<?$i=0;
foreach($arResult["ITEMS"] as $key => $arElement):
	$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
	$i++;
	?>
    <div class="img-box" id="<?=$this->GetEditAreaId($arElement['ID']);?>" <?if($i==count($arResult["ITEMS"])):?>style="border-bottom:none;"<?endif?>>
		 <form action="<?=SITE_DIR?>include/addToCartAjax.php" method="post" enctype="multipart/form-data">  
		    <?$bPicture = is_array($arElement["PREVIEW_IMG"]);
			if ($bPicture):?>
			    <div class="image">
			     <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" itemprop="image" src="<?=$arElement["PREVIEW_IMG"]["SRC"]?>" width="<?=$arElement["PREVIEW_IMG"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_IMG"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="catalog_list_image_saleleader_<?=$arElement['ID']?>" /></a>
				</div>
			<?endif;?>        	
				<div class="info">
					<h4><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></h4>
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
							<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" class="catalog-item-buy" value="&nbsp;" onclick="return addToCartForm(this.form, 'catalog_list_image_saleleader_<?=$arElement['ID']?>', 'list', '<?=GetMessage("CATALOG_IN_CART")?>', this);" id="catalog_add2cart_link_<?=$arElement['ID']?>">
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
<?endforeach;?>
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
