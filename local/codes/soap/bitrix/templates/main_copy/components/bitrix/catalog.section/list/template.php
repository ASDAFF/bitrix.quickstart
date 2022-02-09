<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
			<aside class="b-sidebar">
				<div class="b-sidebar-filter m-sidebar">
					<div class="b-tab-head">
						<a href="#" class="b-tab-head__link active">Аксессуары</a>
					</div>
					<button class="b-slider-vert__btn m-vert__up"></button>
					<div class="b-slider-vert">
					<ul class="b-slider-vert__list">
	<?foreach($arResult["ITEMS"] as $arElement):?>
	<?
	$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
	?>
							<li class="b-slider-vert__item" id="<?=$this->GetEditAreaId($arElement['ID']);?>">
								<div class="b-slider-vert__image"><img src='<?=($arElement["PREVIEW_PICTURE"]["SRC"]?$arElement["PREVIEW_PICTURE"]["SRC"]:"/images/img-element__image.png")?>' alt="" /></div>
								<div class="b-slider-vert__link">Ноутбук <a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a></div>


		<?if(count($arResult["PRICES"]) > 0):?>
		<div class="b-slider__price"><?=$arResult["PRICES"]["PRINT_VALUE"]?></div>
			<?if($arElement["CAN_BUY"]):?>
				<noindex>
				<div><a href="<?echo $arElement["BUY_URL"]?>" rel="nofollow" class="b-icon m-icon__buy" title="<?echo GetMessage("CATALOG_BUY")?>"></a></div>
				</noindex>
			<?endif?>
		<?endif;?>
							</li>
	<?endforeach;?>
						</ul>
					</div>
					<button class="b-slider-vert__btn m-vert__down"></button>
				</div>
			</aside><!--/.b-sidebar-->