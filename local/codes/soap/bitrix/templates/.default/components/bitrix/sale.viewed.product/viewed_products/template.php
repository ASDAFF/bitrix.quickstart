<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
					<script>
					$(function() {
						$(document).on("click", ".js-cart__close", function() {
							$(this).closest(".b-cart__hidden").hide();
						});
						$(".b-cart-mini__link").hover(
							function() {
								$(this).addClass("active");
								
								var id = "#cart-" + $(this).data("id"),
									sl = "#slider-" + $(this).data("id");
								//console.log(id);
								if($(id).length) {
									$(id).show();								
									var is_active = $(id).find(".slides_control");
									if(is_active.length == false) {
										// из файла js.js удалил инициализацию этого слайдера http://clip2net.com/s/4WxfPH
										$(sl).slides({
											container: "b-slider",
											prev: "m-prev",
											next: "m-next",
											paginationClass: "b-pager",
											autoHeight: true,
										});
									}
								}
							},
							function() {
								$(this).removeClass("active");
								var id = "#cart-" + $(this).data("id");
								if($(id).length) {
									$(id).hide();								
								}
							}
						);
					});
					</script>
			<div class="b-nav-category m-visit">
				<div class="b-cart-mini__link" data-id="2">
					<a href="/catalogue/viewed/" class="b-cart-mini m-cart-visit">
						<div class="b-cart-mini__line">Просмотрено</div>
						<div class="b-cart-mini__line"><b><?=count($arResult);?></b> товаров</div>
					</a>
<?if (count($arResult) > 0):?>
							<div class="b-cart__hidden" id="cart-2">
								<div class="b-slider-wrapper" id="slider-2">
									<a href="#" class="b-slider__control m-prev"  title="назад"></a>
									<div class="b-slider">
	<?
$i=1;
foreach($arResult as $arItem):?>
<?$ar_res = CIBlockElement::GetList( Array("SORT"=>"ASC"), 
                                     Array("ID" => $arItem["PRODUCT_ID"], 
                                           "IBLOCK_ID" => "1"),
                                     false , 
                                     false , 
                                     Array("ID","IBLOCK_ID", "NAME",
                                           "PREVIEW_PICTURE",
                                           "DETAIL_PAGE_URL", "PROPERTY_model",
                                           "PROPERTY_type", "PROPERTY_article"));   
$props = $ar_res->GetNext();
 
?>
<?if($i==1 or $i%4==0){?>
<div class="clearfix">
<?}?>
		<div class="b-slider__item">
			<div class="b-slider__text">
			<?if( is_array($arItem["PICTURE"])):?>
				<div class="b-slider__image"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PICTURE"]["src"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>"></a></div>
<?else:?>
<div class="b-slider__image"><img border="0" src="/images/img-element__image.png"  alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></div>
<?endif?>
			<?if($arParams["VIEWED_NAME"]=="Y"):?>
				<div class="b-slider__link"><?=$props["PROPERTY_TYPE_VALUE"]?> <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=cutString($arItem["NAME"], 28)?></a></div>
			<?endif?>
			<?if($arParams["VIEWED_PRICE"]=="Y" && $arItem["CAN_BUY"]=="Y"):?>
				<div class="b-slider__price"><?=$arItem["PRICE_FORMATED"]?></div>
			<?endif?>
			</div>
			<div class="b-slider__btn clearfix">
			<?if($arParams["VIEWED_CANBUY"]=="Y" && $arItem["CAN_BUY"]=="Y"):?>
				<noindex>
					<a href="<?=$arItem["BUY_URL"]?>" rel="nofollow"><?=GetMessage("PRODUCT_BUY")?></a>
				</noindex>
			<?endif?>
			<?if($arParams["VIEWED_CANBUSKET"]=="Y" && $arItem["CAN_BUY"]=="Y"):?>
				<noindex>
					<a href="<?=$arItem["ADD_URL"]?>" rel="nofollow"><?=GetMessage("PRODUCT_BUSKET")?></a>
				</noindex>
			<?endif?>
			</div>
		</div>
<?if($i==3 or end($arResult)==$arItem){?>
</div>
<?
$i=0;
}
$i++;
?>
	<?endforeach;?>
									</div>
									<a href="#" class="b-slider__control m-next" title="вперед"></a>
								</div>
								<hr class="b-hr" />
							</div>
<?endif;?>
				</div>
			</div>