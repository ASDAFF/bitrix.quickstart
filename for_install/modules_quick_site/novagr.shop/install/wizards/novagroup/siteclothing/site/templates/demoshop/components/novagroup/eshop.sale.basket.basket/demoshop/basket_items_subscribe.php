<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

	<?if(count($arResult["ITEMS"]["ProdSubscribe"]) > 0):?>
	<table class="table table-bordered table-striped equipment" rules="rows">
	
	<thead>
		<tr>
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
				<td><?= GetMessage("SALE_NAME")?></td>
				<td></td>
			<?endif;?>
			<?/*if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
				<td><?= GetMessage("SALE_VAT")?></td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td><?= GetMessage("SALE_PRICE_TYPE")?></td>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td><?= GetMessage("SALE_DISCOUNT")?></td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td><?= GetMessage("SALE_WEIGHT")?></td>
			<?endif;*/?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td><?= GetMessage("SALE_QUANTITY")?></td>
			<?endif;?>
			<?if (in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
				<td><?= GetMessage("SALE_PRICE")?></td>
			<?endif;?>
			<td>&nbsp;</td>
		</tr>
	</thead>
	<tbody>
	<?
	foreach($arResult["ITEMS"]["ProdSubscribe"] as $arBasketItems)
	{
        $params["sourceURL"] = $arBasketItems["DETAIL_PAGE_URL"];
        $params["colorID"] = $arBasketItems["COLOR"];
        $params["productID"] = $arBasketItems["ELEMENT_ID"]["ID"];
        $params["sizeID"] = $arBasketItems["SIZE"];
        $arBasketItems["DETAIL_PAGE_URL"] = Novagroup_Classes_General_Basket::makeDetailLink($params);
		?>
		<tr>
			<td class="prev-img">
				<?
				/*if (in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
					<a class="deleteitem" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" onclick="return DeleteFromCart(this);" title="<?=GetMessage("SALE_DELETE_PRD")?>">ddd</a>
				<?endif;*/
				
				$arBasketItems["DETAIL_PAGE_URL"] = $arResult['mixData']["DETAIL_PAGE_URL"][ $arBasketItems['PRODUCT_ID'] ];
				
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
				?><a target="_blank" href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>"><?
				endif;
				//$imgSrc = CFile::GetPath($arBasketItems["PREVIEW_PICTURE"]);
				/*$photoId = $arResult["PHOTOS"][$arBasketItems["PRODUCT_ID"]]["PREVIEW_PICTURE"];
				$imgSrc =  $arResult['PREVIEW_PICTURE'][$photoId];
				if (strlen($imgSrc) > 0) :?>
						<img src="<?=$imgSrc?>" alt="<?=$arBasketItems["NAME"] ?>" height="119" />
				<?
				else:?>
						<img src="/bitrix/components/bitrix/eshop.sale.basket.basket/templates/.default/images/no-photo.png" alt="<?=$arBasketItems["NAME"] ?>" height="119" />
				<?php
				endif;*/
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
				                    );?>"><?php 
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):
						?></a><?
				endif;
					
				
				/*
				if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
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
			<?if (in_array("NAME", $arParams["COLUMNS_LIST"])):?>
			<td class="cart-item-name">
				<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
					<p><a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>">
				<?endif;?>
					<?=$arBasketItems["NAME"] ?>
				<?if (strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
					</a></p>
				<?endif;?>
                <?
                if (!empty($arResult['COLORS'][$arBasketItems["COLOR"]]["PIC"])) {
                    $colorPic = CFile::GetPath($arResult['COLORS'][$arBasketItems["COLOR"]]["PIC"]);
                } else {
                    $colorPic = SITE_TEMPLATE_PATH."/images/not-f.jpg";;
                }
                $STD_SIZE = "N/A";
                if(is_array($arBasketItems['PROPS']))
                foreach($arBasketItems['PROPS'] as $prop)
                {
                    if($prop['CODE']=='STD_SIZE')$STD_SIZE = $prop['VALUE'];
                }
                ?>
                <p><span class="m-demo"><?=GetMessage("SIZE_PRODUCT")?>:</span> <span class="size-bas-demo"><?=$STD_SIZE?></span></p>
                <p><span class="m-demo"><?=GetMessage("COLOR_PRODUCT")?>:</span> <span><img width="35" height="33" border="0" src="<?=$colorPic?>" alt="<?=$arResult['COLORS'][$arBasketItems["COLOR"]]["NAME"]?>"></span></p>
				<?if (in_array("PROPS", $arParams["COLUMNS_LIST"]))
				{
					/*foreach($arBasketItems["PROPS"] as $val)
					{
						echo "<br />".$val["NAME"].": ".$val["VALUE"];
					}*/
				}?>
			</td>
			<?endif;?>

			<?/*if (in_array("VAT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-price"><?=$arBasketItems["VAT_RATE_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("TYPE", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-type"><?=$arBasketItems["NOTES"]?></td>
			<?endif;?>
			<?if (in_array("DISCOUNT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-discount"><?=$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
			<?endif;?>
			<?if (in_array("WEIGHT", $arParams["COLUMNS_LIST"])):?>
				<td class="cart-item-weight"><?=$arBasketItems["WEIGHT_FORMATED"]?></td>
			<?endif;*/?>
			<?if (in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
				<td ><span><?=$arBasketItems["QUANTITY"]?></span></td>
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
			<td >
				<p><a title="<?=GetMessage("SALE_DELETE_PRD")?>" class="deleteitem" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>"><i class="icon-remove-sign"></i><?=GetMessage("SALE_DELETE")?></a></a>
				</p>
			</td>
		</tr>
		<?php 
	}
	?>
	</tbody>
</table>
<?endif;?>
