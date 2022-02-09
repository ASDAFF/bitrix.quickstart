<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<table class="b-history__table">
<thead>
	<tr>
		<td><?=GetMessage("SPOL_T_ID")?></td>
		<td><?=GetMessage("SPOL_T_F_DATE")?></td>
		<td><?=GetMessage("SPOL_T_PAY_SYS")?></td>
		<td><?=GetMessage("SPOL_T_DELIVERY_COAST")?></td>
		<td><?=GetMessage("SPOL_T_PRICE")?></td>
		<td><?=GetMessage("SPOL_T_STATUS")?></td>
	</tr>
</thead>
<tbody>
	<?foreach($arResult["ORDERS"] as $val):?>
	<?//pr($arResult);?>
		<tr class="b-history__tr" rel="#b-order-<?=$val["ORDER"]["ID"]?>">
			<td class="b-history__td"><?=$val["ORDER"]["ID"]?></td>
			<td class="b-history__td"><?=$val["ORDER"]["DATE_INSERT_FORMAT"]?></td>
			<td class="b-history__td">
				<?if (strpos($val["ORDER"]["DELIVERY_ID"], ":") === false):?>
					<?=$arResult["INFO"]["DELIVERY"][$val["ORDER"]["DELIVERY_ID"]]["NAME"]?>
				<?else:
					$arId = explode(":", $val["ORDER"]["DELIVERY_ID"]);
				?>
					<?=$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["NAME"]?> (<?=$arResult["INFO"]["DELIVERY_HANDLERS"][$arId[0]]["PROFILES"][$arId[1]]["TITLE"]?>)
				<?endif?>
			</td>
			<td class="b-history__td"><?=$val["ORDER"]["PRICE_DELIVERY"]?></td>
			<td class="b-history__td"><?=$val["ORDER"]["FORMATED_PRICE"]?></td>
			<td class="b-history__td"><?=$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"]?></td>
		</tr>
	<?endforeach;?>
</tbody>
</table>
	<?foreach($arResult["ORDERS"] as $val):?>
	<?//pr($arResult);?>

						<div id="b-order-<?=$val["ORDER"]["ID"]?>" class="b-history-delail">
						<h3 class="b-detail__h3 m-history__h3">Заказ № <?=$val["ORDER"]["ID"]?><span class="b-history-delail__place">8.01.2103 Доставка: Москва, м. Белорусская, ул. Вавилова, д.19 кв. 3</span><span class="b-history-delail__status">Доставлено</span></h3>
						<div class="b-history__list">
							<?
				$bNeedComa = False;
				foreach($val["BASKET_ITEMS"] as $vval)
				{
					?>
					<?//pr($vval);
					$arFilter = Array("IBLOCK_ID"=>"1", "ID"=>$vval['PRODUCT_ID'], "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
					$res = CIBlockElement::GetList(Array(), $arFilter, false);
					while($ar_res = $res->GetNextElement())
						{
					$product = $ar_res->GetFields();
					$props = $ar_res->GetProperties();
					$full_name = $product['NAME']." ".$props['model']['VALUE']." (".$props['article']['VALUE'].")";
					//pr($props);
					?>
							<div class="b-cart__item clearfix">
								<div class="b-cart__image m-history__image"><img src="<?=CFile::GetPath($product["PREVIEW_PICTURE"]);?>" alt="<?=$vval["NAME"];?>" /></div>
								<div class="b-cart__text m-history__text">
									<div class="b-cart__link m-history__link"><?=$props['type']['VALUE']?> <a href="<?=$vval["DETAIL_PAGE_URL"];?>"><?=$full_name?></a></div>
									<div class="b-cart__info"><?=$product['PREVIEW_TEXT']?></div>
								</div>
								<div class="b-cart__total m-history__total">
									<div class="clearfix">
										<div class="b-cart__total_left">
											<div class="b-slider__price m-price__16px"><?=$vval['PRICE']*$vval["QUANTITY"]?>.–</div>
											<div class="b-slider__price_clearing"><?=$vval['NOTES']?><br /><b><?=$vval['PRICE']?>.–</b></div>							
										</div>
									</div>
								</div>
								<div class="b-cart__price m-history__price">
									<div class="clearfix">
										<div class="b-cart__price_left">
											<div class="b-slider__price m-price__16px"><?=$vval['PRICE']?>.–</div>
											<div class="b-slider__price_clearing"><?=$vval['NOTES']?><br /><b><?=$vval['PRICE']?>.–</b></div>							
										</div>
										<div class="b-cart__price_right">
											<span class="b-cart-count__count" id="item_count_1"><?echo $vval["QUANTITY"].' '.GetMessage("STPOL_SHT");?></span>
										</div>
									</div>
								</div>
							</div>					
					
					<?}
				}
			?>

						</div>
						<div class="b-total m-history__itogo clearfix">
							<div class="b-total__text"><h2>Итого</h2>без стоимости доставки</div>
							<div class="b-total__price">
								<div class="b-slider__price"><?=$val["ORDER"]["FORMATED_PRICE"]?></div>
								<div class="b-slider__price_clearing"><?=$arResult["INFO"]["PAY_SYSTEM"][$val["ORDER"]["PAY_SYSTEM_ID"]]["NAME"]?><br /><b><?=$val["ORDER"]["FORMATED_PRICE"]?></b></div>							
							</div>
						</div>
					</div>
	<?endforeach;?>
