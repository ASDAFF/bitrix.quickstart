<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section">
	<div class="background-drop-shadow"></div>
	<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
	<?
		$this->AddEditAction($arElement["ID"], $arElement["EDIT_LINK"], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement["ID"], $arElement["DELETE_LINK"], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BCS_ELEMENT_DELETE_CONFIRM")));
	?>
	<div class="catalog-element-item" id="<?=$this->GetEditAreaId($arElement["ID"]);?>">
		<div class="col col_1_4 box box_border box_white">
			<div class="inner">
				<div class="head"><h3><?=$arElement["NAME"]?></h3></div>
				<div class="img">
					<?if(in_array("SALELEADER" 	      ,$arParams["PROPERTY_CODE"]) && !empty($arElement["PROPERTIES"]["SALELEADER"]["VALUE"])):?><div><?=$arElement["PROPERTIES"]["SALELEADER"]["NAME"]?></div>
					<?elseif(in_array("SPECIALOFFER"  ,$arParams["PROPERTY_CODE"]) && !empty($arElement["PROPERTIES"]["SPECIALOFFER"]["VALUE"])):?><div><?=$arElement["PROPERTIES"]["SPECIALOFFER"]["NAME"]?></div>
					<?elseif(in_array("NEWPRODUCT" 	  ,$arParams["PROPERTY_CODE"]) && !empty($arElement["PROPERTIES"]["NEWPRODUCT"]["VALUE"])):?><div><?=$arElement["PROPERTIES"]["NEWPRODUCT"]["NAME"]?></div>
					<?elseif(in_array("ACTION" 		  ,$arParams["PROPERTY_CODE"]) && !empty($arElement["PROPERTIES"]["ACTION"]["VALUE"])):?><div><?=$arElement["PROPERTIES"]["ACTION"]["NAME"]?></div><?endif?>
					<?if(is_array($arElement["PREVIEW_PICTURE"])):?><img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>"><?endif?>
				</div>
				<div class="footer">
					<div><div><?=GetMessage("SERGELAND_STORE_BREND")?>:<div><?if(!empty($arElement["PROPERTIES"]["BREND"]["VALUE"])):?><?=$arElement["PROPERTIES"]["BREND"]["VALUE"]?><?else:?>-<?endif?></div></div></div>
					<div><div><?=GetMessage("SERGELAND_STORE_COLLECTION")?>:<div><?if(!empty($arElement["PROPERTIES"]["COLLECTION"]["VALUE"])):?><?=$arElement["PROPERTIES"]["COLLECTION"]["VALUE"]?><?else:?>-<?endif?></div></div></div>
					<div><div><?=GetMessage("SERGELAND_STORE_COUNTRY")?>:<div><?if(!empty($arElement["PROPERTIES"]["COUNTRY"]["VALUE"])):?><?=$arElement["PROPERTIES"]["COUNTRY"]["VALUE"]?><?else:?>-<?endif?></div></div></div>
				</div>
			</div>
		</div>
		<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="button_link btn_yellow all"><i class="fa fa-bars fa-fw"></i></a>
		<a href="#" class="button_link btn_yellow detail"><i class="fa fa-shopping-cart fa-fw"></i>&nbsp;<?=GetMessage("SERGELAND_STORE_CATALOG_BUY")?></a>

		<?CModule::IncludeModule("sale");?>
		<div class="price">						
		<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>
			<?if($arElement["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] > 0):?>
				<?if($arElement["~MAXIMUM_PRICE"][$PRICE_CODE]["VALUE"] == $arElement["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]):?>												
					<?if($arElement["~PRICES"][$PRICE_CODE][$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]] > $arElement["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]):?>								
						<div class="sale"><span><?=$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["DISCOUNT_PRICE_PERCENT_FORMATED"]?></span></div>
						<div class="price-format new"><span><?=$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"]?></span> <?=$arElement["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"]?></div>
						<div class="price-format old"><span><?=$arElement["~PRICES_OLD_PRINT"][$PRICE_CODE][$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]]?></span> <?=$arElement["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"]?></div>		
					<?else:?>
						<div class="price-format"><span><?=$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"]?></span> <?=$arElement["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"]?></div>
					<?endif;?>					
				<?else:?>				
					<?if($arElement["~PRICES"][$PRICE_CODE][$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]] > $arElement["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]):?>								
						<div class="sale"><span><?=$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["DISCOUNT_PRICE_PERCENT_FORMATED"]?></span></div>
						<div class="price-format new offer"><?=GetMessage("SERGELAND_STORE_CR_PRICE_OT")?> <span><?=$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"]?></span> <?=$arElement["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"]?></div>
						<div class="price-format old offer"><?=GetMessage("SERGELAND_STORE_CR_PRICE_OT")?> <span><?=$arElement["~PRICES_OLD_PRINT"][$PRICE_CODE][$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]]?></span> <?=$arElement["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"]?></div>		
					<?else:?>
						<div class="price-format offer"><?=GetMessage("SERGELAND_STORE_CR_PRICE_OT")?> <span><?=$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"]?></span> <?=$arElement["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"]?></div>
					<?endif;?>					
				<?endif;?>
			<?endif?>	
		<?endforeach?>
		</div>
		<div class="detail-item-block"><div class="close">x</div>
			<div class="drop-shadow"><div class="detail-content">
				<h3><?=$arElement["NAME"]?></h3>
				<div class="left-block">
					<div class="img"><?if(is_array($arElement["PREVIEW_PICTURE"])):?><img data-help="<?=GetMessage("SERGELAND_STORE_HELP")?>" data-large="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arElement["NAME"]?>"><?endif?></div>
					<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>						
						<div class="color-item"><select <?if($arElement["~SKU_PRICES_COUNT"][$PRICE_CODE] > 0):?>class="sku"<?endif?>>
							<?foreach($arElement["~SELECT"]["COLOR"] as $arColor):?>
								<?if($arColor["NAME"] == $arElement["~MINIMUM_PRICE"][$PRICE_CODE]["COLOR"]):?>
									<option selected data-site="<?=SITE_ID?>" data-quantity="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]?>" value="<?=$arColor["SELECT_COLOR_VALUE"]?>"><?=$arColor["NAME"]?></option>
								<?else:?>
									<option data-site="<?=SITE_ID?>" data-quantity="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]?>" value="<?=$arColor["SELECT_COLOR_VALUE"]?>"><?=$arColor["NAME"]?></option>
								<?endif?>
							<?endforeach?>
						</select></div>
						<div class="size-item"><select <?if($arElement["~SKU_PRICES_COUNT"][$PRICE_CODE] > 0):?>class="sku"<?endif?>>
							<?foreach($arElement["~SELECT"]["SIZE"] as $key=>$arSize):?>
								<?if($arSize["COLOR"] == $arElement["~MINIMUM_PRICE"][$PRICE_CODE]["COLOR"]):?>
									<?if($arSize["NAME"] == $arElement["~MINIMUM_PRICE"][$PRICE_CODE]["SIZE"]):?>
										<option <?if(!$selected):$selected=true?>selected<?endif?> data-price="<?=$arSize["PRICES_PRINT"][$PRICE_CODE]?>" data-id="<?=$arSize["ID"]?>" data-quantity="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]?>" data-buy="<?=$arSize["BUY_URL"]?>" data-add="<?=$arSize["ADD_URL"]?>" data-artnumber="<?=$arSize["SELECT_ARTNUMBER_VALUE"]?>" data-color="<?=$arSize["SELECT_COLOR_VALUE"]?>" data-size="<?=$arSize["SELECT_SIZE_VALUE"]?>" value="<?=$key?>"><?=$arSize["NAME"]?></option>
									<?else:?>
										<option data-price="<?=$arSize["PRICES_PRINT"][$PRICE_CODE]?>" data-id="<?=$arSize["ID"]?>" data-quantity="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]?>" data-buy="<?=$arSize["BUY_URL"]?>" data-add="<?=$arSize["ADD_URL"]?>" data-artnumber="<?=$arSize["SELECT_ARTNUMBER_VALUE"]?>" data-color="<?=$arSize["SELECT_COLOR_VALUE"]?>" data-size="<?=$arSize["SELECT_SIZE_VALUE"]?>" value="<?=$key?>"><?=$arSize["NAME"]?></option>
									<?endif?>
								<?else:?>
									<option disabled data-price="<?=$arSize["PRICES_PRINT"][$PRICE_CODE]?>" data-id="<?=$arSize["ID"]?>" data-quantity="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]?>" data-buy="<?=$arSize["BUY_URL"]?>" data-add="<?=$arSize["ADD_URL"]?>" data-artnumber="<?=$arSize["SELECT_ARTNUMBER_VALUE"]?>" data-color="<?=$arSize["SELECT_COLOR_VALUE"]?>" data-size="<?=$arSize["SELECT_SIZE_VALUE"]?>" value="<?=$key?>"><?=$arSize["NAME"]?></option>
								<?endif?>
							<?endforeach?>
						</select></div>
					<?endforeach?>
					<div class="price-format">
						<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>
							<span><?if($arElement["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] > 0):?><?=$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"]?><?endif?></span> <?=$arElement["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"]?><br>
						<?endforeach?>
						<input class="quantity" type="text" value="1" name="quantity" maxlength="3" size="1">
					</div>											
					<div class="quickly"><?=GetMessage("SERGELAND_STORE_QUICKLY")?></div>
					<div class="quickly-send"><?=GetMessage("SERGELAND_STORE_QUICKLY_SEND")?><span>x</span></div>
					<div class="basket-error"><?=GetMessage("SERGELAND_STORE_BASKET_ERROR")?><span>x</span></div>
					<div class="quickly-form">
						<div><input type="text" data-value="<?=GetMessage("SERGELAND_STORE_NAME")?>" value="" name="quickly-name"></div>
						<div><input type="text" data-value="<?=GetMessage("SERGELAND_STORE_PHONE")?>" value="" name="quickly-phone"></div>
						<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>
							<div class="button send" data-id="<?=$arElement["ID"]?>" data-path="<?=$templateFolder?>/quickly.php" data-size="<?=$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_SIZE_VALUE"]?>" data-color="<?=$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_COLOR_VALUE"]?>"><?=GetMessage("SERGELAND_STORE_SEND")?></div>
						<?endforeach?>
						<div class="quickly-text"><?=GetMessage("SERGELAND_STORE_QUICKLY_TEXT")?></div>
						<div class="button cancel">x</div>
					</div>
					<div class="clear"></div>
				</div>				
				<div class="right-block">
					<div class="text">
						<div class="section-detail-text"><?=$arElement["DETAIL_TEXT"]?></div>
					</div>									
				</div>
				<div class="share42init social" data-url="http://<?=$_SERVER["SERVER_NAME"]?>" data-title="<?=$arElement["NAME"]?>" data-description="<?=$arElement["PREVIEW_TEXT"]?>" data-path="<?=SITE_DIR?>images/share42/"></div>
				<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>
					<?if($arElement["~PRICES_ALL"][$PRICE_CODE][$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]]["CAN_BUY"] == "Y"):?>
						<?if(in_array("ARTNUMBER", $arParams["PROPERTY_CODE"])):?><div class="artnumber-container"><span class="artnumber"><?=$arElement["~MINIMUM_PRICE"][$PRICE_CODE]["ARTNUMBER"]?></span></div><?endif?>
						<div class="button-make-goods"><a href="<?=$arElement["BUY_URL"][$PRICE_CODE]?>" class="button_link btn_yellow order"><?=GetMessage("SERGELAND_STORE_MAKE")?>&nbsp;<i class="fa fa-chevron-right fa-fw"></i></a></div>
						<div class="button-add-basket"><div class="loader"></div><a data-path="<?=$templateFolder?>/basket.php" href="<?=$arElement["ADD_URL"][$PRICE_CODE]?>" class="button_link btn_yellow cart"><?=GetMessage("SERGELAND_STORE_ADD")?>&nbsp;<i class="fa fa-shopping-cart fa-fw"></i></a></div>
					<?endif?>
				<?endforeach?>
				<div class="clear"></div>
			</div></div>
		</div>
	</div>
	<?endforeach?>	
	<div class="clear"></div>
	<?=$arResult["NAV_STRING"]?>	
</div>