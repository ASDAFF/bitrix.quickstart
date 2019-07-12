<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog-section element">
	<div class="catalog-element-item">
		<div class="detail-item-block">
			<div class="drop-shadow"><div class="detail-content">
				<h3><a href="<?=$arResult["DETAIL_PAGE_URL"]?>"><?=$arResult["NAME"]?></a></h3>
				<div class="left-block">
					<div id="picture" class="img"><?if(is_array($arResult["PREVIEW_PICTURE"])):?><img data-help="<?=GetMessage("SERGELAND_STREETSTYLE_HELP")?>" data-large="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arResult["NAME"]?>"><?endif?></div>
					<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>						
						<div class="color-item"><select <?if($arResult["~SKU_PRICES_COUNT"][$PRICE_CODE] > 0):?>class="sku"<?endif?>>
							<?foreach($arResult["~SELECT"]["COLOR"] as $arColor):?>
								<?if($arColor["NAME"] == $arResult["~MINIMUM_PRICE"][$PRICE_CODE]["COLOR"]):?>
									<option selected data-site="<?=SITE_ID?>" data-quantity="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]?>" value="<?=$arColor["SELECT_COLOR_VALUE"]?>"><?=$arColor["NAME"]?></option>
								<?else:?>
									<option data-site="<?=SITE_ID?>" data-quantity="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]?>" value="<?=$arColor["SELECT_COLOR_VALUE"]?>"><?=$arColor["NAME"]?></option>
								<?endif?>
							<?endforeach?>
						</select></div>
						<div class="size-item"><select <?if($arResult["~SKU_PRICES_COUNT"][$PRICE_CODE] > 0):?>class="sku"<?endif?>>
							<?foreach($arResult["~SELECT"]["SIZE"] as $key=>$arSize):?>
								<?if($arSize["COLOR"] == $arResult["~MINIMUM_PRICE"][$PRICE_CODE]["COLOR"]):?>
									<?if($arSize["NAME"] == $arResult["~MINIMUM_PRICE"][$PRICE_CODE]["SIZE"]):?>
										<option <?if(!$selected):$selected=true?>selected<?endif?> data-price="<?=$arSize["PRICES_PRINT"][$PRICE_CODE]?>" data-old="<?=$arSize["PRICES_OLD_PRINT"][$PRICE_CODE]?>" data-id="<?=$arSize["ID"]?>" data-quantity="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]?>" data-buy="<?=$arSize["BUY_URL"]?>" data-add="<?=$arSize["ADD_URL"]?>" data-amount="<?=$arSize["SELECT_QUANTITY_VALUE"]?>" data-artnumber="<?=$arSize["SELECT_ARTNUMBER_VALUE"]?>" data-color="<?=$arSize["SELECT_COLOR_VALUE"]?>" data-size="<?=$arSize["SELECT_SIZE_VALUE"]?>" value="<?=$key?>"><?=$arSize["NAME"]?></option>
									<?else:?>
										<option data-price="<?=$arSize["PRICES_PRINT"][$PRICE_CODE]?>" data-old="<?=$arSize["PRICES_OLD_PRINT"][$PRICE_CODE]?>" data-id="<?=$arSize["ID"]?>" data-quantity="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]?>" data-buy="<?=$arSize["BUY_URL"]?>" data-add="<?=$arSize["ADD_URL"]?>" data-amount="<?=$arSize["SELECT_QUANTITY_VALUE"]?>" data-artnumber="<?=$arSize["SELECT_ARTNUMBER_VALUE"]?>" data-color="<?=$arSize["SELECT_COLOR_VALUE"]?>" data-size="<?=$arSize["SELECT_SIZE_VALUE"]?>" value="<?=$key?>"><?=$arSize["NAME"]?></option>
									<?endif?>
								<?else:?>
									<option disabled data-price="<?=$arSize["PRICES_PRINT"][$PRICE_CODE]?>" data-old="<?=$arSize["PRICES_OLD_PRINT"][$PRICE_CODE]?>" data-id="<?=$arSize["ID"]?>" data-quantity="<?=$arParams["PRODUCT_QUANTITY_VARIABLE"]?>" data-buy="<?=$arSize["BUY_URL"]?>" data-add="<?=$arSize["ADD_URL"]?>" data-amount="<?=$arSize["SELECT_QUANTITY_VALUE"]?>" data-artnumber="<?=$arSize["SELECT_ARTNUMBER_VALUE"]?>" data-color="<?=$arSize["SELECT_COLOR_VALUE"]?>" data-size="<?=$arSize["SELECT_SIZE_VALUE"]?>" value="<?=$key?>"><?=$arSize["NAME"]?></option>
								<?endif?>
							<?endforeach?>
						</select></div>
					<?endforeach?>	
					<div class="basket-error"><?=GetMessage("SERGELAND_STREETSTYLE_BASKET-ERROR")?><span>x</span></div>
					
					<div class="quickly"><?=GetMessage("SERGELAND_STREETSTYLE_QUICKLY")?></div>
					<div class="quickly-send"><?=GetMessage("SERGELAND_STREETSTYLE_QUICKLY-SEND")?><span>x</span></div>
					<div class="quickly-form">
						<div><input type="text" data-value="<?=GetMessage("SERGELAND_STREETSTYLE_NAME")?>" value="" name="quickly-name"></div>
						<div><input type="text" data-value="<?=GetMessage("SERGELAND_STREETSTYLE_PHONE")?>" value="" name="quickly-phone"></div>
						<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>
							<div class="button send" data-id="<?=$arResult["ID"]?>" data-path="<?=$templateFolder?>/quickly.php" data-size="<?=$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_SIZE_VALUE"]?>" data-color="<?=$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["SELECT_COLOR_VALUE"]?>"><?=GetMessage("SERGELAND_STREETSTYLE_SEND")?></div>
						<?endforeach?>
						<div class="quickly-text"><?=GetMessage("SERGELAND_STREETSTYLE_QUICKLY-TEXT")?></div>
						<div class="button cancel">x</div>
					</div>
					
					<div class="clear"></div>
				</div>
				<div class="tmb">
					<div class="img"><?if(is_array($arResult["PREVIEW_PICTURE"]) && !empty($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])):?><img data-help="<?=GetMessage("SERGELAND_STREETSTYLE_HELP")?>" data-large="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arResult["NAME"]?>"><?endif?></div>				
					<div class="img"><?if(is_array($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"][0])):?><img data-help="<?=GetMessage("SERGELAND_STREETSTYLE_HELP")?>" data-large="<?=$arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"][0]["DETAIL_PICTURE"]["SRC"]?>" src="<?=$arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"][0]["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arResult["NAME"]?>"><?endif?></div>
					<div class="img"><?if(is_array($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"][1])):?><img data-help="<?=GetMessage("SERGELAND_STREETSTYLE_HELP")?>" data-large="<?=$arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"][1]["DETAIL_PICTURE"]["SRC"]?>" src="<?=$arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"][1]["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arResult["NAME"]?>"><?endif?></div>
				</div>
				<div class="right-block">
					<div class="text">
						<div class="section-detail-text"><?=$arResult["DETAIL_TEXT"]?></div>
					</div>
					<div class="price-format"><div>
						<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>
							<?if($arResult["~PRICES"][$PRICE_CODE][$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]] > $arResult["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]):?>								
								<span><?=$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"]?></span>&nbsp;<?=$arResult["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"]?><br>
								<div class="old"><span><?=$arResult["~PRICES_OLD_PRINT"][$PRICE_CODE][$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]]?></span>&nbsp;<?=$arResult["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"]?></div>
							<?else:?>
								<span><?if($arResult["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"] > 0):?><?=$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["PRINT_VALUE"]?><?endif?></span>&nbsp;<?=$arResult["~CURRENCY_FORMAT"][$PRICE_CODE]["FORMAT_PRINT"]?>
							<?endif;?>						
						<?endforeach?></div>
						<input class="quantity" type="text" value="1" name="quantity" maxlength="3" size="1">
					</div>					
				<div class="share2 social" data-url="http://<?=$_SERVER["SERVER_NAME"].$arResult["DETAIL_PAGE_URL"]?>" data-title="<?=$arResult["NAME"]?>" data-description="<?=$arResult["PREVIEW_TEXT"]?>" data-image="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>">
					<a class="vk"     title="<?=GetMessage("SERGELAND_STREETSTYLE_VK")?>"></a>
					<a class="fb"     title="<?=GetMessage("SERGELAND_STREETSTYLE_FB")?>"></a>
					<a class="twi"    title="<?=GetMessage("SERGELAND_STREETSTYLE_TWI")?>"></a>
					<a class="odkl"   title="<?=GetMessage("SERGELAND_STREETSTYLE_ODKL")?>"></a>
					<a class="mail"   title="<?=GetMessage("SERGELAND_STREETSTYLE_MAIL")?>"></a>
					<a class="google" title="<?=GetMessage("SERGELAND_STREETSTYLE_GOOGLE")?>"></a>				
				</div>
					<div class="countdown-container <?=$arResult["ID"]?>">
						<div class="countdown-item <?=$arResult["ID"]?>">
							<span class="days">00</span>:<span class="hours">00</span>:<span class="minutes">00</span>:<span class="seconds">00</span>
						</div>
					</div>				
				</div>
				<div class="services">
					<div class="cheap-form">
						<div class="head-cheap"><?=GetMessage("SERGELAND_STREETSTYLE_CHEAP-HEADER")?></div>
						<div><input type="text" data-value="<?=GetMessage("SERGELAND_STREETSTYLE_NAME")?>" 	value="" name="cheap-name"></div>
						<div><input type="text" data-value="<?=GetMessage("SERGELAND_STREETSTYLE_PHONE")?>" value="" name="cheap-phone"></div>
						<div><input type="text" data-value="<?=GetMessage("SERGELAND_STREETSTYLE_URL")?>" 	value="" name="cheap-url"></div>
						<div><textarea  data-value="<?=GetMessage("SERGELAND_STREETSTYLE_COMMENT")?>"></textarea></div>

						<div class="button send" data-id="<?=$arResult["ID"]?>" data-loader="<?=GetMessage("SERGELAND_STREETSTYLE_SEND-LOAD")?>" data-path="<?=$templateFolder?>/cheap.php"><?=GetMessage("SERGELAND_STREETSTYLE_SEND")?></div>
						<div class="cheap-text"><?=GetMessage("SERGELAND_STREETSTYLE_CHEAP-TEXT")?></div>
					</div>				
				</div>				
				<div class="clear"></div>				
				<?foreach($arParams["PRICE_CODE"] as $PRICE_CODE):?>
					<?if($arResult["~PRICES_ALL"][$PRICE_CODE][$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["VALUE"]]["CAN_BUY"] == "Y"):?>
						<div class="icons">
							<?if(in_array("ARTNUMBER", $arParams["PROPERTY_CODE"])):?>
								<div class="artnumber"><i class="fa fa-tag"></i><span class="artnumber"><?=$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["ARTNUMBER"]?></span><div><?=GetMessage("SERGELAND_STREETSTYLE_TOOLTIP3")?></div></div>
							<?endif?>
							<div class="amount"><i class="fa fa-signal"></i><span class="amount"><?=$arResult["~MINIMUM_PRICE"][$PRICE_CODE]["QUANTITY"]?></span><div><?=GetMessage("SERGELAND_STREETSTYLE_TOOLTIP2")?></div></div>						
						</div>						
						<div class="button-make-goods"><a href="<?=$arResult["BUY_URL"][$PRICE_CODE]?>" class="button_link btn_yellow order"><?=GetMessage("SERGELAND_STREETSTYLE_MAKE")?>&nbsp;<i class="fa fa-chevron-right fa-fw"></i></a></div>
						<div class="button-add-basket"><div class="loader"></div><a data-path="<?=$templateFolder?>/basket.php" href="<?=$arResult["ADD_URL"][$PRICE_CODE]?>" class="button_link btn_yellow cart"><?=GetMessage("SERGELAND_STREETSTYLE_ADD")?>&nbsp;<i class="fa fa-shopping-cart fa-fw"></i></a></div>
					<?endif?>
				<?endforeach?>
				<div class="clear"></div>
			</div></div>
		</div>
	</div>
	<div class="clear"></div>	
</div>