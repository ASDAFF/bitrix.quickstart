<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>


<!-- Картинка детальная -->
<img src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["NAME"]?>" />
				

<!-- Доп картинки -->
				<?
                $LINE_ELEMENT_COUNT = 1;
                if (count($arResult["MORE_PHOTO"]) > 0):?>
                    <? foreach ($arResult["MORE_PHOTO"] as $PHOTO): ?>
                        <a href="<?= $PHOTO["SRC"] ?>" title="<?= $arResult["NAME"] ?>" data-fancybox="group" data-caption="<?= $arResult['NAME']?>">
                            <?
                            $renderImage = CFile::ResizeImageGet($PHOTO, Array("width" => 400, "height" => 400), BX_RESIZE_IMAGE_EXACT, false);
                            ?>
                            <img class="image_sec" border="0" src="<?= $renderImage["src"] ?>"
                                 alt="<?= $arResult["NAME"] ?>"/>
                        </a>
                    <? endforeach ?>
                <? endif ?>



<!-- Свойства -->
				<?foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
					<?=$arProperty["NAME"]?>: <?
					if(is_array($arProperty["DISPLAY_VALUE"])):
						echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
					elseif($pid=="MANUAL"):
						?><a href="<?=$arProperty["VALUE"]?>"><?=GetMessage("CATALOG_DOWNLOAD")?></a><?
					else:
						echo $arProperty["DISPLAY_VALUE"];?>
					<?endif?>
				<?endforeach?>





			

	<?if(is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"])):?>
	<!-- Если есть преддожения -->
		<?foreach($arResult["OFFERS"] as $arOffer):?>
			<!-- Свойства -->
			<?foreach($arOffer["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
				<small><?=$arProperty["NAME"]?>:&nbsp;<?
					if(is_array($arProperty["DISPLAY_VALUE"]))
						echo implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);
					else
						echo $arProperty["DISPLAY_VALUE"];?></small><br />
			<?endforeach?>
			<!-- Цены -->
			<?foreach($arOffer["PRICES"] as $code=>$arPrice):?>
				<?if($arPrice["CAN_ACCESS"]):?>
					<?=$arResult["CAT_PRICES"][$code]["TITLE"];?>
					<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
						<s><?=$arPrice["PRINT_VALUE"]?></s> <?=$arPrice["PRINT_DISCOUNT_VALUE"]?>
					<?else:?>
						<?=$arPrice["PRINT_VALUE"]?>
					<?endif?>
					</p>
				<?endif;?>
			<?endforeach;?>
			<!-- Покупка -->
			<?if($arOffer["CAN_BUY"]):?>
					<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data" сlass="add_form">
						<a href="javascript:void(0)" onclick="if (BX('QUANTITY<?= $arOffer['ID'] ?>').value &gt; 1) BX('QUANTITY<?= $arOffer['ID'] ?>').value--;">-</a>
	                        <input type="text" name="QUANTITY" value="1" id="QUANTITY<?= $arOffer['ID'] ?>"/>
	                    <a href="javascript:void(0)" onclick="BX('QUANTITY<?= $arOffer['ID'] ?>').value++;">+</a>
						<input type="text" name="<?echo $arParams["PRODUCT_QUANTITY_VARIABLE"]?>" value="1" size="5">
						<input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="BUY">
						<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arOffer["ID"]?>">
						<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."BUY"?>" value="<?echo GetMessage("CATALOG_BUY")?>">
						<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" value="<?echo GetMessage("CT_BCE_CATALOG_ADD")?>">
					</form>
			<?elseif(count($arResult["CAT_PRICES"]) > 0):?>
				<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
			<?endif?>
		<?endforeach;?>
	<?else:?>
	<!-- Если нет преддожений -->

		<!-- Цены -->
		<?foreach($arResult["PRICES"] as $code=>$arPrice):?>
			<?if($arPrice["CAN_ACCESS"]):?>
				<?=$arResult["CAT_PRICES"][$code]["TITLE"];?>
				<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
					<s><?=$arPrice["PRINT_VALUE"]?></s> <?=$arPrice["PRINT_DISCOUNT_VALUE"]?>
				<?else:?>
					<?=$arPrice["PRINT_VALUE"]?>
				<?endif?>
				</p>
			<?endif;?>
		<?endforeach;?>
		<!-- Покупка -->
		<?if($arResult["CAN_BUY"]):?>
				<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data" сlass="add_form">
				<a href="javascript:void(0)" onclick="if (BX('QUANTITY<?= $arElement['ID'] ?>').value &gt; 1) BX('QUANTITY<?= $arElement['ID'] ?>').value--;">-</a>
                        <input type="text" name="QUANTITY" value="1" id="QUANTITY<?= $arElement['ID'] ?>"/>
                    <a href="javascript:void(0)" onclick="BX('QUANTITY<?= $arElement['ID'] ?>').value++;">+</a>
				<input type="hidden" name="<?echo $arParams["ACTION_VARIABLE"]?>" value="BUY">
				<input type="hidden" name="<?echo $arParams["PRODUCT_ID_VARIABLE"]?>" value="<?echo $arResult["ID"]?>">
				<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."BUY"?>" value="<?echo GetMessage("CATALOG_BUY")?>">
				<input type="submit" name="<?echo $arParams["ACTION_VARIABLE"]."ADD2BASKET"?>" value="<?echo GetMessage("CATALOG_ADD_TO_BASKET")?>">
				</form>
		<?elseif((count($arResult["PRICES"]) > 0) || is_array($arResult["PRICE_MATRIX"])):?>
			<?=GetMessage("CATALOG_NOT_AVAILABLE")?>
		<?endif?>
	<?endif?>
		
	
	<!-- Описание -->
	<?=$arResult["DETAIL_TEXT"]?>



	<!-- Оценка -->
	<? $APPLICATION->IncludeComponent("bitrix:iblock.vote", "starstemp", Array(
                    "IBLOCK_TYPE" => "catalog",    // Тип инфоблока
                    "IBLOCK_ID" => "5",    // Инфоблок
                    "ELEMENT_ID" => $arResult["ID"],    // ID элемента
                    "MAX_VOTE" => "5",    // Максимальный балл
                    "VOTE_NAMES" => array(    // Подписи к баллам
                        0 => "0",
                        1 => "1",
                        2 => "2",
                        3 => "3",
                        4 => "4",
                        5 => "",
                    ),
                    "SET_STATUS_404" => "N",    // Устанавливать статус 404
                    "MESSAGE_404" => "",    // Сообщение для показа (по умолчанию из компонента)
                    "CACHE_TYPE" => "N",    // Тип кеширования
                    "CACHE_TIME" => "0",    // Время кеширования (сек.)
                    "COMPONENT_TEMPLATE" => "stars",
                    "DISPLAY_AS_RATING" => "vote_avg",    // В качестве рейтинга показывать
                ),
                    false
                ); ?> 