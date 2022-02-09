<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!$_REQUEST["ajax"] && @$_REQUEST["ajax"] != "Y"){?>
<div class="b-catalog-list">
<?}?>
<ul class="quick_view">
		<?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
		?>
		<?$full_name = $arElement['NAME']." ".$arElement['PROPERTIES']['model']['VALUE']." (".$arElement['PROPERTIES']['article']['VALUE'].")";?>
		<div class="b-catalog-list__item clearfix" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
	
			<div class="b-catalog-list__image"><img border="0" src="<?=($arElement["PREVIEW_PICTURE"]["SRC"]?$arElement["PREVIEW_PICTURE"]["SRC"]:"/images/img-element__image.png")?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" id="<?=$arElement['ID']?>"/></div>
		
			<div class="b-catalog-list__text">
				<div class="b-catalog-list__link"><?=$arElement['PROPERTIES']['type']['VALUE']?> <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$full_name?>тест</a><span class="b-rating"><span style="width: <?=($arElement['PROPERTIES']['rating']['VALUE']*20)?>%"></span></span></div>
				<div class="b-catalog-list__info"><?=$arElement['PREVIEW_TEXT']?></div>
				<div class="clearfix">
					<div class="b-catalog-list__price">
						<div class="b-slider__price"><?=$arElement["PRICES"]["price"]["PRINT_VALUE_NOVAT"]?></div>
						<?if($arElement["PRICES"]["clearing"]["PRINT_VALUE_NOVAT"]):?>
						<div class="b-slider__price_clearing">Безнал <b><?=$arElement["PRICES"]["clearing"]["PRINT_VALUE_NOVAT"]?></b></div>
						<?endif;?>
					</div>
					<div class="b-catalog-list__btn">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
Array(),
false
);?>
						<button class="b-button__fast"><?echo GetMessage("FUST_ORDER")?></button>
						<?if($arParams["DISPLAY_COMPARE"]):?>
						<noindex>
							<a href="<?echo $arElement["COMPARE_URL"]?>" rel="nofollow" class="b-icon m-icon__compare" title="<?echo GetMessage("CATALOG_COMPARE")?>"></a>
						</noindex>
						<?endif?>
						<span class="b-icon" title="<?echo GetMessage("WISHLIST")?>"></span>
						<?if($arElement["CAN_BUY"]):?>
							<noindex>
							<a class="b-icon m-icon__buy" id="<?=$arElement['ID']?>" href="<?echo $arElement["ADD_URL"]?>" rel="nofollow" title="<?echo GetMessage("CATALOG_ADD")?>"></a>
							</noindex>
						<?endif;?>
					</div>
				</div>
			</div>
		</div>

		<?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>
		</ul>
<?if(!$_REQUEST["ajax"] && @$_REQUEST["ajax"] != "Y"){?>		
</div>
<?}?>
  <?if($arResult["NAV_STRING"]) {?>
				<div class="b-show_more">
					<a href="<?=$arResult["NAV_STRING"]?>&ajax=Y" id="catlistnavnext" class="b-button m-small__btn"><span>Показать следующие <?=$arParams["PAGE_ELEMENT_COUNT"]?> товаров</span></a>
				</div>
  <?}?>