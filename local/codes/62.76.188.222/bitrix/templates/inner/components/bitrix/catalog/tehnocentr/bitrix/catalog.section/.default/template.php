<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?//var_dump($arResult);?>
<div class="b-promo">
<h1 class="b-promo__title"><?=$arResult['NAME'];?></h1>
</div>
<aside class="b-sidebar">
<ul class="b-sidebar-menu m-sidebar-block">
<li class="b-sidebar-menu__item has-child">
        <a class="b-sidebar-menu__link" href="#"><span>Мобильные телефоны</span></a>
        <ul class="b-sidebar-submenu">
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Фотокамеры</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Объективы</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Фотовспышки</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Сумки, футляры</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Фоторамки</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Светофильтры</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Рассеиватель</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Адаптер</a></li>
        </ul>
</li>
<li class="b-sidebar-menu__item has-child">
        <a class="b-sidebar-menu__link" href="#"><span>Радиотелефоны</span></a>
        <ul class="b-sidebar-submenu">
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Фотокамеры</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Объективы</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Фотовспышки</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Сумки, футляры</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Фоторамки</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Светофильтры</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Рассеиватель</a></li>
                <li class="b-sidebar-submenu__item"><a class="b-sidebar-submenu__link" href="#">Адаптер</a></li>
        </ul>
</li>
<li class="b-sidebar-menu__item"><a class="b-sidebar-menu__link" href="#"><span>Проводные телефоны</span></a></li>
<li class="b-sidebar-menu__item"><a class="b-sidebar-menu__link" href="#"><span>Цифровое фото</span></a></li>
<li class="b-sidebar-menu__item"><a class="b-sidebar-menu__link" href="#"><span>Видеокамеры</span></a></li>
</ul>
<div class="b-sidebar-filter m-sidebar-block">
<h2 class="b-sidebar-filter__title">Выбор по параметрам:</h2>
<div class="b-sidebar-filter-container">
        <div class="b-sidebar-filter-caption">
                <span class="b-sidebar-filter-caption__text">Розничная цена</span>
        </div>
        <div class="clearfix">
                <div class="b-sidebar-filter__left">
                        <input type="text" id="SLIDER_MIN" class="b-text">
                </div>
                <span class="b-sidebar-filter__mdash">&mdash;</span>
                <div class="b-sidebar-filter__right">
                        <input type="text" id="SLIDER_MAX" class="b-text">
                </div>
                <div class="b-sidebar-filter-slider">
                        <div id="b-slider" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><div class="ui-slider-range ui-widget-header" style="left: 10%; width: 40%;"></div><a href="#" class="ui-slider-handle ui-state-default ui-corner-all" style="left: 10%;"></a><a href="#" class="ui-slider-handle ui-state-default ui-corner-all" style="left: 50%;"></a></div>
                </div>
        </div>
</div>
<div class="b-sidebar-filter-container">
        <div class="b-sidebar-filter-caption">
                <span class="b-sidebar-filter-caption__text">Производитель</span>
        </div>
        <table>
                <tbody><tr>
                        <td><label class="b-checkbox m-checkbox_gp_1"><input type="checkbox" value="" name="checkbox_gp_1">Canon</label></td>
                </tr>
                <tr>
                        <td><label class="b-checkbox m-checkbox_gp_2"><input type="checkbox" value="" name="checkbox_gp_2">Nikon</label></td>
                </tr>
                <tr>
                        <td><label class="b-checkbox m-checkbox_gp_3"><input type="checkbox" value="" name="checkbox_gp_3">Sony</label></td>
                </tr>								
        </tbody></table>
</div>
<div class="b-sidebar-filter-container">
        <div class="b-sidebar-filter-caption">
                <span class="b-sidebar-filter-caption__text">Количество мегапикселей</span>
        </div>
</div>
<div class="b-sidebar-filter-container">
        <div class="b-sidebar-filter-caption">
                <span class="b-sidebar-filter-caption__text">Тип матрицы</span>
        </div>
</div>
<div class="b-sidebar-filter-container">
        <div class="b-sidebar-filter-caption">
                <span class="b-sidebar-filter-caption__text">Оптический зум</span>
        </div>
</div>
<div class="b-sidebar-filter-container">
        <div class="b-sidebar-filter-caption">
                <span class="b-sidebar-filter-caption__text">Формат карты памяти</span>
        </div>
</div>
<div><button class="b-button">Показать</button></div>
</div>
</aside><!--/.b-sidebar-->
<section class="b-content">
<div class="b-catalog-sort clearfix">
<div class="b-catalog-sort-name">
        <span class="b-catalog-sort__text">Сортировать по:</span>
        <a class="b-catalog-sort__link m-sort b-catalog-sort__active" href="#"><span>названию</span></a>
        <a class="b-catalog-sort__link m-sort" href="#"><span>цене</span></a>
        <a class="b-catalog-sort__link m-sort" href="#"><span>новинкам</span></a>
</div>
<div class="b-catalog-sort-count">
        <select class="b-chosen__no-text chzn-done" name="" id="selZJB" style="display: none;">
                <option value="">20</option>
                <option value="">40</option>
                <option value="">60</option>
        </select><div id="selZJB_chzn" class="chzn-container chzn-container-single" style="width: 60px;" title=""><a tabindex="-1" class="chzn-single" href="javascript:void(0)"><span>20</span><div><b></b></div></a><div style="left: -9000px; width: 58px; top: 30px;" class="chzn-drop"><div class="chzn-search" style=""><input type="text" autocomplete="off" style="width: 23px;"></div><ul class="chzn-results"><li style="" class="active-result result-selected" id="selZJB_chzn_o_0">20</li><li style="" class="active-result" id="selZJB_chzn_o_1">40</li><li style="" class="active-result" id="selZJB_chzn_o_2">60</li></ul></div></div>
</div>
<div class="b-catalog-sort-list">
        <a class="b-catalog-sort__link-list active" href="#"></a>
        <a class="b-catalog-sort__link-list m-image-list" href="#"></a>
        <a class="b-catalog-sort__link-list m-list" href="#"></a>
</div>
</div>
<div class="b-catalog-list">
    
    
    
<?
if($arResult["ITEMS"]){
?>
    
    
    
  
<?foreach($arResult["ITEMS"] as $cell=>$arElement){
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

                ?>    
    
    
<?if($cell % 3 == 0){?>
<div class="b-catalog-list__line clearfix">  
<?}?>
    
    <div class="b-catalog-list_item<?if($cell % 3 == 2){?> m-3n<?}?>" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
                
            
            
            <div class="b-catalog-list_item__image"><a href="<?=$arElement['DETAIL_PAGE_URL'];?>"><img alt="" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>"></a></div>
                <div class="b-catalog-list_item__where clearfix">
                        <div class="b-where__left">
                                <span title="че то надо написать" class="b-where__icon"></span>
                        </div>
                        <div class="b-where__right">
                                <span title="че то надо написать" class="b-where__icon m-r"></span>
                                <span title="че то надо написать" class="b-where__icon m-p"></span>
                        </div>
                </div>
                <div class="b-catalog-list_item__name" style="height: 41px;"><a href="<?=$arElement['DETAIL_PAGE_URL'];?>"><?=$arElement['NAME'];?></a></div>
                <div class="b-catalog-list_item__btn clearfix">
                        <div class="b-bth__right">
                                <button class="b-catalog-list_item__buy"><span class="b-catalog-list_item__cart">Купить</span></button>
                        </div>
                        <div class="b-bth__right m-price">
                                <span class="b-price">27 777</span>
                        </div>
                </div>
        </div>
      
    
<?if($cell % 3 == 2){?>
 </div> 
<?}?>
    
    
    
    <?
    // тут надо просечь чтобы всё закрывалось 
    
    } ?>
    
    
    
<?} else {?>    
 

    
   <p>Раздел пуст</p> 
    
    
<?}?>    
</div><!--/.b-catalog-list-->
<div class="b-page-nav">
<span class="b-page-nav__text">Страницы:</span>
<a class="b-page-nav__link" href="#">« предыдущая</a>
<a class="b-page-nav__link" href="#">1</a>
<span class="b-page-nav__current">2</span>
<a class="b-page-nav__link" href="#">3</a>
<a class="b-page-nav__link" href="#">4</a>
<a class="b-page-nav__link" href="#">5</a>
<a class="b-page-nav__link" href="#">следующая »</a>
<a class="b-page-nav__link b-page-nav__all" href="#">Показать все</a>
</div>
</section>








                                
                                
                                
                                
                                
                                <?  return; ?>
                                
                                
                                
                                <div class="catalog-section">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<table cellpadding="0" cellspacing="0" border="0">
		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
		<tr>
		<?endif;?>

		<td valign="top" width="<?=round(100/$arParams["LINE_ELEMENT_COUNT"])?>%" id="<?=$this->GetEditAreaId($arElement['ID']);?>">

			<table cellpadding="0" cellspacing="2" border="0">
				<tr>
					<?if(is_array($arElement["PREVIEW_PICTURE"])):?>
						<td valign="top">
						<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a><br />
						</td>
					<?elseif(is_array($arElement["DETAIL_PICTURE"])):?>
						<td valign="top">
						<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img border="0" src="<?=$arElement["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arElement["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arElement["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a><br />
						</td>
					<?endif?>
					<td valign="top"><a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a><br />
						<?foreach($arElement["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
							<?=$arProperty["NAME"]?>:&nbsp;<?
								if(is_array($arProperty["DISPLAY_VALUE"]))
									echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
								else
									echo $arProperty["DISPLAY_VALUE"];?><br />
						<?endforeach?>
						<br />
						<?=$arElement["PREVIEW_TEXT"]?>
					</td>
				</tr>
			</table>
			<?if(is_array($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):?>
				<?foreach($arElement["OFFERS"] as $arOffer):?>
					<?foreach($arParams["OFFERS_FIELD_CODE"] as $field_code):?>
						<small><?echo GetMessage("IBLOCK_FIELD_".$field_code)?>:&nbsp;<?
								echo $arOffer[$field_code];?></small><br />
					<?endforeach;?>
					<?foreach($arOffer["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
						<small><?=$arProperty["NAME"]?>:&nbsp;<?
							if(is_array($arProperty["DISPLAY_VALUE"]))
								echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
							else
								echo $arProperty["DISPLAY_VALUE"];?></small><br />
					<?endforeach?>
					<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
						<?if($arPrice["CAN_ACCESS"]):?>
							<p><?=$arResult["PRICES"][$code]["TITLE"];?>:&nbsp;&nbsp;
							<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
								<s><?=$arPrice["PRINT_VALUE"]?></s> <span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
							<?else:?>
								<span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span>
							<?endif?>
							</p>
						<?endif;?>
					<?endforeach;?>
					<p>
					<?if($arParams["DISPLAY_COMPARE"]):?>
						<noindex>
						<a href="<?echo $arOffer["COMPARE_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_COMPARE")?></a>&nbsp;
						</noindex>
					<?endif?>
					<?if($arOffer["CAN_BUY"]):?>
						<?if($arParams["USE_PRODUCT_QUANTITY"]):?>
							<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
							<table border="0" cellspacing="0" cellpadding="2">
								<tr valign="top">
									<td><?echo GetMessage("CT_BCS_QUANTITY")?>:</td>
									<td>
										<input type="text" name="<?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>" value="1" size="5">
									</td>
								</tr>
							</table>
							<input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="BUY">
							<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arOffer["ID"]?>">
							<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."BUY"?>" value="<?echo GetMessage("CATALOG_BUY")?>">
							<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" value="<?echo GetMessage("CATALOG_ADD")?>">
							</form>
						<?else:?>
							<noindex>
							<a href="<?echo $arOffer["BUY_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></a>
							&nbsp;<a href="<?echo $arOffer["ADD_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_ADD")?></a>
							</noindex>
						<?endif;?>
					<?elseif(count($arResult["PRICES"]) > 0):?>
						<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
						<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
							"NOTIFY_ID" => $arOffer['ID'],
							"NOTIFY_URL" => htmlspecialcharsback($arOffer["SUBSCRIBE_URL"]),
							"NOTIFY_USE_CAPTHA" => "N"
							),
							false
						);?>
					<?endif?>
					</p>
				<?endforeach;?>
			<?else:?>
				<?foreach($arElement["PRICES"] as $code=>$arPrice):?>
					<?if($arPrice["CAN_ACCESS"]):?>
						<p><?=$arResult["PRICES"][$code]["TITLE"];?>:&nbsp;&nbsp;
						<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
							<s><?=$arPrice["PRINT_VALUE"]?></s> <span class="catalog-price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></span>
						<?else:?><span class="catalog-price"><?=$arPrice["PRINT_VALUE"]?></span><?endif;?>
						</p>
					<?endif;?>
				<?endforeach;?>
				<?if(is_array($arElement["PRICE_MATRIX"])):?>
					<table cellpadding="0" cellspacing="0" border="0" width="100%" class="data-table">
					<thead>
					<tr>
						<?if(count($arElement["PRICE_MATRIX"]["ROWS"]) >= 1 && ($arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_FROM"] > 0 || $arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_TO"] > 0)):?>
							<td valign="top" nowrap><?= GetMessage("CATALOG_QUANTITY") ?></td>
						<?endif?>
						<?foreach($arElement["PRICE_MATRIX"]["COLS"] as $typeID => $arType):?>
							<td valign="top" nowrap><?= $arType["NAME_LANG"] ?></td>
						<?endforeach?>
					</tr>
					</thead>
					<?foreach ($arElement["PRICE_MATRIX"]["ROWS"] as $ind => $arQuantity):?>
					<tr>
						<?if(count($arElement["PRICE_MATRIX"]["ROWS"]) > 1 || count($arElement["PRICE_MATRIX"]["ROWS"]) == 1 && ($arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_FROM"] > 0 || $arElement["PRICE_MATRIX"]["ROWS"][0]["QUANTITY_TO"] > 0)):?>
							<th nowrap><?
								if (IntVal($arQuantity["QUANTITY_FROM"]) > 0 && IntVal($arQuantity["QUANTITY_TO"]) > 0)
									echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_FROM_TO")));
								elseif (IntVal($arQuantity["QUANTITY_FROM"]) > 0)
									echo str_replace("#FROM#", $arQuantity["QUANTITY_FROM"], GetMessage("CATALOG_QUANTITY_FROM"));
								elseif (IntVal($arQuantity["QUANTITY_TO"]) > 0)
									echo str_replace("#TO#", $arQuantity["QUANTITY_TO"], GetMessage("CATALOG_QUANTITY_TO"));
							?></th>
						<?endif?>
						<?foreach($arElement["PRICE_MATRIX"]["COLS"] as $typeID => $arType):?>
							<td><?
								if($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"] < $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"]):?>
									<s><?=FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"])?></s><span class="catalog-price"><?=FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["DISCOUNT_PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"]);?></span>
								<?else:?>
									<span class="catalog-price"><?=FormatCurrency($arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["PRICE"], $arElement["PRICE_MATRIX"]["MATRIX"][$typeID][$ind]["CURRENCY"]);?></span>
								<?endif?>&nbsp;
							</td>
						<?endforeach?>
					</tr>
					<?endforeach?>
					</table><br />
				<?endif?>
				<?if($arParams["DISPLAY_COMPARE"]):?>
					<noindex>
					<a href="<?echo $arElement["COMPARE_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_COMPARE")?></a>&nbsp;
					</noindex>
				<?endif?>
				<?if($arElement["CAN_BUY"]):?>
					<?if($arParams["USE_PRODUCT_QUANTITY"] || count($arElement["PRODUCT_PROPERTIES"])):?>
						<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
						<table border="0" cellspacing="0" cellpadding="2">
						<?if($arParams["USE_PRODUCT_QUANTITY"]):?>
							<tr valign="top">
								<td><?echo GetMessage("CT_BCS_QUANTITY")?>:</td>
								<td>
									<input type="text" name="<?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>" value="1" size="5">
								</td>
							</tr>
						<?endif;?>
						<?foreach($arElement["PRODUCT_PROPERTIES"] as $pid => $product_property):?>
							<tr valign="top">
								<td><?echo $arElement["PROPERTIES"][$pid]["NAME"]?>:</td>
								<td>
								<?if(
									$arElement["PROPERTIES"][$pid]["PROPERTY_TYPE"] == "L"
									&& $arElement["PROPERTIES"][$pid]["LIST_TYPE"] == "C"
								):?>
									<?foreach($product_property["VALUES"] as $k => $v):?>
										<label><input type="radio" name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]" value="<?echo $k?>" <?if($k == $product_property["SELECTED"]) echo '"checked"'?>><?echo $v?></label><br>
									<?endforeach;?>
								<?else:?>
									<select name="<?echo $arParams["PRODUCT_PROPS_VARIABLE"]?>[<?echo $pid?>]">
										<?foreach($product_property["VALUES"] as $k => $v):?>
											<option value="<?echo $k?>" <?if($k == $product_property["SELECTED"]) echo '"selected"'?>><?echo $v?></option>
										<?endforeach;?>
									</select>
								<?endif;?>
								</td>
							</tr>
						<?endforeach;?>
						</table>
						<input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="BUY">
						<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arElement["ID"]?>">
						<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."BUY"?>" value="<?echo GetMessage("CATALOG_BUY")?>">
						<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" value="<?echo GetMessage("CATALOG_ADD")?>">
						</form>
					<?else:?>
						<noindex>
						<a href="<?echo $arElement["BUY_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_BUY")?></a>&nbsp;<a href="<?echo $arElement["ADD_URL"]?>" rel="nofollow"><?echo GetMessage("CATALOG_ADD")?></a>
						</noindex>
					<?endif;?>
				<?elseif((count($arResult["PRICES"]) > 0) || is_array($arElement["PRICE_MATRIX"])):?>
					<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
					<?$APPLICATION->IncludeComponent("bitrix:sale.notice.product", ".default", array(
							"NOTIFY_ID" => $arElement['ID'],
							"NOTIFY_URL" => htmlspecialcharsback($arElement["SUBSCRIBE_URL"]),
							"NOTIFY_USE_CAPTHA" => "N"
							),
							false
						);?>
				<?endif?>
			<?endif?>
			&nbsp;
		</td>

		<?$cell++;
		if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
			</tr>
		<?endif?>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>

		<?if($cell%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
			<?while(($cell++)%$arParams["LINE_ELEMENT_COUNT"] != 0):?>
				<td>&nbsp;</td>
			<?endwhile;?>
			</tr>
		<?endif?>

</table>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
