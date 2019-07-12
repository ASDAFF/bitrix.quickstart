<?
global $USER;
$tf = $templateFolder;
global $templateFolder;

// For filter Price
$item = CIBlockElement::GetList(Array(),Array("IBLOCK_ID"=>$arParams['IBLOCK_ID']))->GetNext();
$price = CCatalogProduct::GetOptimalPrice($item['ID'],1,$USER->GetUserGroupArray());
$price_id = $price['PRICE']['CATALOG_GROUP_ID'];
$cond = Array("IBLOCK_ID"=>$arParams['IBLOCK_ID']);
if($arResult['ID']){
	$cond["SECTION_ID"] = $arResult['ID'];
	$cond["INCLUDE_SUBSECTIONS"] = "Y";
}
$min = CIBlockElement::GetList(Array("CATALOG_PRICE_".$price_id=>"ASC"),$cond)->GetNext();
$max = CIBlockElement::GetList(Array("CATALOG_PRICE_".$price_id=>"DESC"),$cond)->GetNext();

$arResult['MIN_PRICE'] = floor(iarga::getprice($min['ID'])/100)*100;
$arResult['MAX_PRICE'] = ceil(iarga::getprice($max['ID'])/100)*100;
$price_type = CCatalogProduct::GetOptimalPrice($max['ID'],1,$USER->GetUserGroupArray());

$step = pround(($arResult['MAX_PRICE'] - $arResult['MIN_PRICE'])/4);
for($i=0;$i<3;$i++) $arResult['STEPS_PRICE'][$i] = $arResult['MIN_PRICE'] + ($i+1)*$step;



?>
<div class="filter slider-widget-input">
	<form action="<?=$_SERVER['REQUEST_URI']?>">
		<input value="<?=$price_type['PRICE']['CATALOG_GROUP_ID']?>" name="price_type" type="hidden">
		<div class="keyword">
			<dl>
				<dt><?=GetMessage("KEYWORD")?>:</dt>
				<dd>					
					<input type="hidden" name="IBLOCK_ID" value="<?=$arParams['IBLOCK_ID']?>">
					<input type="hidden" name="SECTION_ID" value="<?=$arResult['ID']?>">
					<input type="text" value="<?=$_REQUEST['key']?>" class="inp-text tooltip" name="key">
				</dd>
			</dl>
		</div><!--.keyword-end-->

		<div class="sep"></div>
		
		<?if($arResult['MIN_PRICE'] != $arResult['MAX_PRICE']):?>
			<div class="range-price">
				<dl class="left">
					<dt><?=GetMessage("PRICE_FROM")?>:</dt>
					<dd><input type="text" class="inp-text minVal" name="price_from" value="<?=($_REQUEST['price_from'])?$_REQUEST['price_from']:$arResult['MIN_PRICE']?>"></dd><!-- ћинимальное значение, которое мы показываем на слайдере при загрузке страницы -->
				</dl>
				<dl class="right">
					<dt><?=GetMessage("PRICE_TO")?></dt>
					<dd><input type="text" class="inp-text maxVal" name="price_to" value="<?=($_REQUEST['price_to'])?$_REQUEST['price_to']:$arResult['MAX_PRICE']?>"></dd><!-- ћаксимальное значение, которое мы показываем на слайдере при загрузке страницы -->
					<dt style="padding-right:0;">&nbsp; <?=GetMessage("VALUTE_MEDIUM")?></dt>
				</dl>
			</div><!--.range-price-end-->
		<?endif;?>
		<div class="bt">
			<a href="#" class="bt_gray submit"><?=GetMessage("FIND")?></a>
		</div><!--.bt-end-->
		<?if($arResult['MIN_PRICE'] != $arResult['MAX_PRICE']):?>
			<div class="slider-widget-container">
				<div class="values-container">
					<div class="val first"><p>&lt;&lt;</p></div><!--.val-end-->
					<div class="val" style="left:25%"><p><?=iarga::prep($arResult['STEPS_PRICE'][0])?></p></div><!--.val-end-->
					<div class="val" style="left:50%;"><p><?=iarga::prep($arResult['STEPS_PRICE'][1])?></p></div><!--.val-end-->
					<div class="val" style="left:75%;"><p><?=iarga::prep($arResult['STEPS_PRICE'][2])?></p></div><!--.val-end-->
					<div class="val last" style="left:100%;"><p>&gt;&gt;</p></div><!--.val-end-->
				</div><!--.values-container-end-->
				<div class="slider-widget">
					<input type="text" class="minValue" value="<?=$arResult['MIN_PRICE']?>" /> <!-- ћинимально допустимое знаечение дл€ виджета -->
					<input type="text" class="maxValue" value="<?=$arResult['MAX_PRICE']?>" /> <!-- ћаксимально допустимое знаечение дл€ виджета -->
					<a href="#" class="ui-slider-handle ui-state-default ui-corner-all first"></a>
					<a href="#" class="ui-slider-handle ui-state-default ui-corner-all last"></a>
				</div><!--.slider-widget-end-->
			</div><!--.slider-widget-container-end-->
		<?endif;?>
		<input type="image" width="1" height="1" src="<?=$templateFolder?>/images/blank.gif" class="fleft">
	</form>
</div><!--.filter-end-->