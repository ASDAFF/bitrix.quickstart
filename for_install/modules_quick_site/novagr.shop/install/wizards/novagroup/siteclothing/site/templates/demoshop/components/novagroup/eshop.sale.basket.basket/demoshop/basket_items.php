<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="invest-ed" id="id-cart-list">
	<ul class="nav nav-tabs" id="myTab-i">
		<li class="active"><a data-toggle="tab" href="#id-01" class="btn invested"><?=GetMessage("SALE_PRD_IN_BASKET_ACT")?></a></li>
		<?php 
		if (count($arResult["ITEMS"]["DelDelCanBuy"])) {
		?>
		<li><a data-toggle="tab" href="#id-02" id="id02" class="btn invested"><?=GetMessage("SALE_PRD_IN_BASKET_SHELVE")?></a></li>
		<?php 
		}
		if (count($arResult["ITEMS"]["ProdSubscribe"])) {
			?>
			<li><a data-toggle="tab" href="#id-03" class="btn invested"><?=GetMessage("SALE_PRD_IN_BASKET_SUBSCRIBE")?></a></li>
			<?php 
		}

		?>
	</ul>
    <script type="text/javascript">
        $(document).ready(function(){
            <?if(!empty($_REQUEST['t'])):?>
            $("a#id02").click();
            <?endif?>
        });
    </script>
	<div class="tab-content" id="myTabContent-i">
		<div id="id-01" class="tab-pane fade active in">
		<?php 
		$countElems = count($arResult["ITEMS"]["AnDelCanBuy"]);
		if ($countElems>0) {
			$tableClass = 'table-striped';
			
		} else {
			$tableClass = 'empty-basket';
		}
		?>

		<table  class="table table-bordered <?=$tableClass?> equipment">
		<thead>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td></td>
				<td><?= GetMessage("SALE_NAME")?></td>
				
				<?$numCells += 2;?>
			<?endif;?>
			<?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
				<td><?= GetMessage("SALE_VAT")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-type"><?= GetMessage("SALE_PRICE_TYPE")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-discount"><?= GetMessage("SALE_DISCOUNT")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-weight"><?= GetMessage("SALE_WEIGHT")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-quantity"><?= GetMessage("SALE_QUANTITY")?></td>
				<?$numCells++;?>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-price"><?= GetMessage("SALE_PRICE")?></td>
				<?$numCells++;?>
			<?endif;?>
			<td>&nbsp;</td>
		</tr>
		</thead>
		<?
		if ($countElems>0) :
		?>
		<tbody>
			<?
			$i=0;
			foreach($arResult["ITEMS"]["AnDelCanBuy"] as $arBasketItems)
			{
                $params["sourceURL"] = $arBasketItems["DETAIL_PAGE_URL"];
                $params["colorID"] = $arBasketItems["COLOR"];
                $params["productID"] = $arBasketItems["ELEMENT_ID"]["ID"];
                $params["sizeID"] = $arBasketItems["SIZE"];
                $arBasketItems["DETAIL_PAGE_URL"] = Novagroup_Classes_General_Basket::makeDetailLink($params);
      			?>
				<tr>
				<?
				if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td class="prev-img">								
				<?
				// выводим превью				
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
				?><a target="_blank" href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?
				endif;
				
                ?>
                <img width="90" height="119"
                     alt="<?=htmlspecialcharsEx($arBasketItems["NAME"])?>"
                     src="<?$APPLICATION->IncludeComponent(
                        "novagroup:catalog.element.photo",
                        "path",
                        Array(
                            "CATALOG_IBLOCK_ID" => $arBasketItems['ELEMENT_ID']['IBLOCK_ID'],
                            "CATALOG_ELEMENT_ID" => $arBasketItems['ELEMENT_ID']['ID'],
                            "PHOTO_ID" => $arBasketItems['COLOR'],
                            "PHOTO_WIDTH" => "90",
                            "PHOTO_HEIGHT" => "119"
                        ),
                        false,
                        Array(
                            'ACTIVE_COMPONENT' => 'Y',
                            "HIDE_ICONS"=>"Y"
                        )
                    );?>">						
                <?php

				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
						?></a><?
				endif;
				/*if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
						<a class="deleteitem" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" onclick="//return DeleteFromCart(this);" title="<?=GetMessage("SALE_DELETE_PRD")?>"></a>
					<?endif;?>
					<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
						<a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
					<?endif;?>
					<?if (strlen($arBasketItems["DETAIL_PICTURE"]["SRC"]) > 0) :?>
						<img src="<?=$arBasketItems["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arBasketItems["NAME"] ?>"/>
					<?else:?>
						<img src="/bitrix/components/bitrix/eshop.sale.basket.basket/templates/.default/images/no-photo.png" alt="<?=$arBasketItems["NAME"] ?>"/>
					<?endif?>
					<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
						</a>
					<?endif;*/?>
				</td>
				<td class="cart-item-name">
					<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
						<p><a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
					<?endif;?>
						<?=$arBasketItems["NAME"] ?>
					<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
						</a></p>
					<?endif;
					
					if (!empty($arResult['COLORS'][$arBasketItems["COLOR"]]["PIC"])) {
						$colorPic = CFile::GetPath($arResult['COLORS'][$arBasketItems["COLOR"]]["PIC"]);
					} else {
						$colorPic = SITE_TEMPLATE_PATH."/images/not-f.jpg";;
					}
					?>
					<p><span class="m-demo"><?=GetMessage("SIZE_PRODUCT")?>:</span> <span class="size-bas-demo"><?=$arResult['SIZES'][$arBasketItems["SIZE"]]?></span></p>
					<p><span class="m-demo"><?=GetMessage("COLOR_PRODUCT")?>:</span> <span rel="tooltip" data-placement="top" class="btn" data-original-title="<?=$arResult['COLORS'][$arBasketItems["COLOR"]]["NAME"]?>"><img width="35" height="33" border="0" src="<?=$colorPic?>" alt="<?=$arResult['COLORS'][$arBasketItems["COLOR"]]["NAME"]?>"></span></p>
					<?if (in_array("PROPS", $arParams["COLUMNS_LIST"]))
					{
						/*foreach($arBasketItems["PROPS"] as $val)
						{
							echo "<br />".$val["NAME"].": ".$val["VALUE"];
						}*/
					}?>
				</td>
					<?endif;?>
					<?if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
						<td><?=$arBasketItems["VAT_RATE_FORMATED"]?></td>
					<?endif;?>
					<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
						<td><?=$arBasketItems["NOTES"]?></td>
					<?endif;?>
					<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
						<td><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
					<?endif;?>
					<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
						<td><?=$arBasketItems["WEIGHT_FORMATED"]?></td>
					<?endif;?>
					<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
						<td>
							<input maxlength="18" type="text" name="QUANTITY_<?=$arBasketItems["ID"]?>" value="<?=$arBasketItems["QUANTITY"]?>" size="3" id="QUANTITY_<?=$arBasketItems["ID"]?>" class="bas-in">
							
							<div class="count_nav tooltip-demo my-tooltip">
							<?php 
							
							?>
								<a <? if ($arResult["PRODUCTS_QUANTITIES"][$arBasketItems["PRODUCT_ID"]] == $arBasketItems["QUANTITY"]) {
									?>rel="tooltip" data-placement="top" data-original-title="<?=GetMessage("CAN_NOT_PLUS_PRODUCT")?>" <?php 
								} 				
								?>id="addQuantity_<?=$arBasketItems["ID"]?>" href="javascript:void(0)" class="plus" onclick="addQuantity(<?=$arResult["PRODUCTS_QUANTITIES"][$arBasketItems["PRODUCT_ID"]]?>, '<?=$arBasketItems["ID"]?>', '<?=GetMessage("CAN_NOT_PLUS_PRODUCT")?>')"></a>
								<a href="javascript:void(0)" class="minus" onclick="minusQuantity(<?=$arResult["PRODUCTS_QUANTITIES"][$arBasketItems["PRODUCT_ID"]]?>, '<?=$arBasketItems["ID"]?>')"></a>
							</div>
						</td>
					<?endif;?>
					<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
						<td class="cart-item-price">
							<?if(doubleval($arBasketItems["FULL_PRICE"]) > 0):?>
								<div class="discount-price"><?=$arBasketItems["PRICE_FORMATED"]?></div>
								<div class="old-price"><?=$arBasketItems["FULL_PRICE_FORMATED"]?></div>
							<?else:?>
								<div class="price"><?=$arBasketItems["PRICE_FORMATED"];?></div>
							<?endif?>
						</td>
					<?endif;?>
					<?//if (in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
						<td>

						<p><a class="setaside" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["shelve"])?>"><i class="icon-arrow-down"></i> <?=GetMessage("SALE_OTLOG")?></a></p>
					<?//endif;?>
					<?//if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
						<p><a title="<?=GetMessage("SALE_DELETE_PRD")?>" class="deleteitem" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>"><i class="icon-remove-sign"></i><?=GetMessage("SALE_DELETE")?></a>
						</p>
						</td>
					<?//endif;?>
				</tr>
				<?
				$i++;
			} // end foreach
			?>
		</tbody>
		</table>
		
		<div class="myorders_itog">
		<table>
		<?if ($arParams["HIDE_COUPON"] != "Y"):?>
		<tr>
			<td colspan="2" class="tal">
				<input class="input_text_style"
					<?if(empty($arResult["COUPON"])):?>
						onclick="if (this.value=='<?=GetMessage("SALE_COUPON_VAL")?>')this.value='';"
						onblur="if (this.value=='') {this.value='<?=GetMessage("SALE_COUPON_VAL")?>'; }"
					<?endif;?>
						value="<?if(!empty($arResult["COUPON"])):?><?=$arResult["COUPON"]?><?else:?><?=GetMessage("SALE_COUPON_VAL")?><?endif;?>"
						name="COUPON">
			</td>
		</tr>
		<?endif;?>
			<?php 
			/*
			<tr><td><?=GetMessage('SALE_VAT_EXCLUDED')?></td><td><?=$arResult["allNOVATSum_FORMATED"]?></td></tr>
			<tr><td><?=GetMessage('SALE_VAT_INCLUDED')?></td><td><?=$arResult["allVATSum_FORMATED"]?></td></tr>
			*/?>
			<tr><td><?= GetMessage("SALE_ITOGO")?>:</td><td><b><?=$arResult["allSum_FORMATED"]?></b></td></tr>
		</table>
			<div class="clear"></div>
		</div>
		<div class="order">
			<input type="submit" value="<?echo GetMessage("SALE_UPDATE")?>" name="BasketRefresh" class="btn bt2">
            <a class="btn bt2 quickBuyButton" data-toggle="modal" href="#oneClickCart"><?= GetMessage('QUICK_BUY') ?></a>
			<input type="submit" value="<?echo GetMessage("SALE_ORDER")?>" name="BasketOrder"  id="basketOrderButton2" class="btn bt3">
        </div>
		<?
		else:
		//$numCells
		?>
		<tbody>
		<tr>
		<td colspan="5">
			<div class="alert alert-info"><i class="basket-min"></i> <?=GetMessage("SALE_NO_ACTIVE_PRD");?></div>
			<p><a class="btn bt3" href="<?=SITE_DIR?>"><?=GetMessage("SALE_NO_ACTIVE_PRD_START")?></a></p>
		</td>
		</tr>
		</tbody>
		</table>
		<?endif;
		?>					
		</div>
        <div id="id-02" class="tab-pane fade">
        	<?
        	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delay.php");
        	?>
		</div>
		<div id="id-03" class="tab-pane fade">
        	<?
        	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribe.php");
        	?>
		</div>
	</div>              
<? 
//deb($arParams["COLUMNS_LIST"]);
$numCells = 0;
/*
BX('QUANTITY_<?=$arBasketItems["ID"]?>').value++;
*/
?>
</div>
<script type="text/javascript">
    UpdateBasketAfterLoadOrderList();
</script>