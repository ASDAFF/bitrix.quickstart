<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
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
<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
	<?foreach($arResult["HIDDEN"] as $arItem):?>
		<input
			type="hidden"
			name="<?echo $arItem["CONTROL_NAME"]?>"
			id="<?echo $arItem["CONTROL_ID"]?>"
			value="<?echo $arItem["HTML_VALUE"]?>"
		/>
	<?endforeach;?>
	<input type="hidden" name="set_filter"/>
		
    <div class="filter slider-widget-input">
        
        <div class="keyword">
            <dl>
            	<dt><a href="#no" class="bt_gray submit"><?=GetMessage("FIND")?></a></dt>
                <dd>
                    <input type="hidden" name="IBLOCK_ID" value="<?=$arParams['IBLOCK_ID']?>">
                    <input type="hidden" name="SECTION_ID" value="<?=$arParams['SECTION_ID']?>">
                    <input type="text" value="<?=$_REQUEST['key']?>" class="inp-text tooltip" name="key" placeholder="<?=GetMessage("KEYWORD")?>" class="inp-text">
                </dd>
            </dl>
        </div><!--.keyword-end-->
        <?foreach($arResult["ITEMS"] as $arItem):?>
		   <?if(isset($arItem["PRICE"])):?>
		        <?if($arResult['MIN_PRICE'] != $arResult['MAX_PRICE']):
		        	$min = $arItem["VALUES"]["MIN"]["VALUE"];
					$max = $arItem["VALUES"]["MAX"]["VALUE"];
					if($max > 1) $max = ceil($max);
					if($min > 1) $min = floor($min); 

					
					$step = ($max - $min) / 100;
					if($step > 1) $step = ceil($step);
					$quater = ($max - $min) / 4 ;
					if($quater > 1) $quater = ceil($quater);
					$st1 = $min + $quater * 1;
					$st2 = $min + $quater * 2;
					$st3 = $min + $quater * 3;?>
		            <div class="range-price">
                        <dl class="left">
                            <dt><?=GetMessage("PRICE_FROM")?></dt>
                            <dd><input type="text" class="inp-text minVal" name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
							id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
							value="<?=($_GET[$arItem["VALUES"]["MIN"]["CONTROL_NAME"]]!="")?$_GET[$arItem["VALUES"]["MIN"]["CONTROL_NAME"]]:$arItem["VALUES"]["MIN"]["VALUE"]?>"
							onkeyup="smartFilter.keyup(this)"></dd>
							<!-- Минимальное значение, которое мы показываем на слайдере при загрузке страницы -->
                        </dl>
                        <dl class="right">
                            <dt><?=GetMessage("VAL_TO")?></dt>
                            <dd><input type="text" class="inp-text maxVal" name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
							id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
							value="<?=($_GET[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]]!="")?$_GET[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]]:$arItem["VALUES"]["MAX"]["VALUE"]?>"
							onkeyup="smartFilter.keyup(this)"></dd>
							<!-- Максимальное значение, которое мы показываем на слайдере при загрузке страницы -->
                            <dt></dt>
                        </dl>
                    </div><!--.range-widget-end-->
                    <div class="slider-widget-container">
                        <div class="values-container">
                            <div class="val first"><p>&lt;&lt;</p></div><!--.val-end-->
                            <div class="val" style="left:25%"><p><?=$st1?></p></div><!--.val-end-->
                            <div class="val" style="left:50%;"><p><?=$st2?></p></div><!--.val-end-->
                            <div class="val" style="left:75%;"><p><?=$st3?></p></div><!--.val-end-->
                            <div class="val last" style="left:100%;"><p>&gt;&gt;</p></div><!--.val-end-->
                        </div><!--.values-container-end-->
                        <div class="slider-widget">
                            <input type="text" class="minValue" value="<?echo $arItem["VALUES"]["MIN"]["VALUE"]?>" /> <!-- Минимально допустимое знаечение для виджета -->
                            <input type="text" class="maxValue" value="<?echo $arItem["VALUES"]["MAX"]["VALUE"]?>" /> <!-- Максимально допустимое знаечение для виджета -->
                            <input type="hidden" class="step" value="<?=$step?>"> <!-- Шаг виджета значений -->
                            <a href="#" class="ui-slider-handle ui-state-default ui-corner-all first"></a>
                            <a href="#" class="ui-slider-handle ui-state-default ui-corner-all last"></a>
                        </div><!--.slider-widget-end-->
                    </div><!--.slider-widget-container-end-->
		        <?endif;?>
		    <?endif;?>
		<?endforeach;?>
        <?if(sizeof($arResult["ITEMS"])>2):?>
        	<span class="link-open"><i></i></span>
        <?endif;?>
    </div><!--.filter-end-->
    <?if(sizeof($arResult["ITEMS"])>1):?>
	    <div class="filter-extended">
	    	<div class="hint"><a href="#no" class="submit bt_gray"><?=GetMessage("FIND")?></a></div><!--.hint-end-->
	    		<ul>
		            <?foreach($arResult["ITEMS"] as $arItem):?>
		            	<?if(isset($arItem["PRICE"])):?>
						<?elseif($arItem["PROPERTY_TYPE"] == "N"):
							$min = $arItem["VALUES"]["MIN"]["VALUE"];
							$max = $arItem["VALUES"]["MAX"]["VALUE"];
							if($max > 1) $max = ceil($max);
							if($min > 1) $min = floor($min); 

							
							$step = ($max - $min) / 100;
							if($step > 1) $step = ceil($step);
							$quater = ($max - $min) / 4 ;
							if($quater > 1) $quater = ceil($quater);
							$st1 = $min + $quater * 1;
							$st2 = $min + $quater * 2;
							$st3 = $min + $quater * 3;
							?>
							<li class="cell slider-values slider-widget-input">
			                    <div class="narrow">
			                        <p class="title"><?=$arItem["NAME"]?>:</p>
			                        <div class="range-widget">
			                            <dl class="left">
			                                <dt><?=GetMessage("VAL_FROM")?></dt>
			                                <dd><input type="text" class="inp-text minVal" name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
											id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
											value="<?=($_GET[$arItem["VALUES"]["MIN"]["CONTROL_NAME"]]!="")?$_GET[$arItem["VALUES"]["MIN"]["CONTROL_NAME"]]:$arItem["VALUES"]["MIN"]["VALUE"]?>"
											onkeyup="smartFilter.keyup(this)"></dd>
											<!-- Минимальное значение, которое мы показываем на слайдере при загрузке страницы -->
			                            </dl>
			                            <dl class="right">
			                                <dt><?=GetMessage("VAL_TO")?></dt>
			                                <dd><input type="text" class="inp-text maxVal" name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
											id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
											value="<?=($_GET[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]]!="")?$_GET[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]]:$arItem["VALUES"]["MAX"]["VALUE"]?>"
											onkeyup="smartFilter.keyup(this)"></dd>
											<!-- Максимальное значение, которое мы показываем на слайдере при загрузке страницы -->
			                                <dt></dt>
			                            </dl>
			                        </div><!--.range-widget-end-->
			                        <div class="slider-widget-container">
			                            <div class="values-container">
			                                <div class="val first"><p>&lt;&lt;</p></div><!--.val-end-->
			                                <div class="val" style="left:25%"><p><?=$st1?></p></div><!--.val-end-->
			                                <div class="val" style="left:50%;"><p><?=$st2?></p></div><!--.val-end-->
			                                <div class="val" style="left:75%;"><p><?=$st3?></p></div><!--.val-end-->
			                                <div class="val last" style="left:100%;"><p>&gt;&gt;</p></div><!--.val-end-->
			                            </div><!--.values-container-end-->
			                            <div class="slider-widget">
			                                <input type="text" class="minValue" value="<?echo $arItem["VALUES"]["MIN"]["VALUE"]?>" /> <!-- Минимально допустимое знаечение для виджета -->
			                                <input type="text" class="maxValue" value="<?echo $arItem["VALUES"]["MAX"]["VALUE"]?>" /> <!-- Максимально допустимое знаечение для виджета -->
			                                <input type="hidden" class="step" value="<?=$step?>"> <!-- Шаг виджета значений -->
			                                <a href="#" class="ui-slider-handle ui-state-default ui-corner-all first"></a>
			                                <a href="#" class="ui-slider-handle ui-state-default ui-corner-all last"></a>
			                            </div><!--.slider-widget-end-->
			                        </div><!--.slider-widget-container-end-->
			                    </div><!--.narrow-end-->
			                <!--li-end-->
						<?elseif(!empty($arItem["VALUES"]) && sizeof($arItem["VALUES"])==1):;?>
							<li class="cell options">
			                    <div class="narrow">
			                        <ul>
			                        	<?foreach($arItem["VALUES"] as $val => $ar):?>
			                            	<li class="lvl2<?echo $ar["DISABLED"]? ' lvl2_disabled': ''?>"><label><input
										type="checkbox"
										value="<?echo $ar["HTML_VALUE"]?>"
										name="<?echo $ar["CONTROL_NAME"]?>"
										id="<?echo $ar["CONTROL_ID"]?>"
										<?echo $ar["CHECKED"]? 'checked="checked"': ''?>
										onclick="smartFilter.click(this)"
									/> <span><?echo $arItem["NAME"];?></span></label><!--li-end-->
			                            <?endforeach;?>
			                        </ul>
			                    </div><!--.narrow-end-->
			                <!--li-end-->
			            <?elseif(!empty($arItem["VALUES"]) && sizeof($arItem["VALUES"])<=8):;?>
			            	<li class="cell type">
			                    <div class="narrow">
			                        <p class="title"><?echo $arItem["NAME"];?>:</p>
			                        <ul>
			                            <?foreach($arItem["VALUES"] as $val => $ar):?>
			                            	<li class="lvl2<?echo $ar["DISABLED"]? ' lvl2_disabled': ''?>"><label><input
										type="checkbox"
										value="<?echo $ar["HTML_VALUE"]?>"
										name="<?echo $ar["CONTROL_NAME"]?>"
										id="<?echo $ar["CONTROL_ID"]?>"
										<?echo $ar["CHECKED"]? 'checked="checked"': ''?>
										onclick="smartFilter.click(this)"
									/> <span><?echo $ar["VALUE"];?></span></label><!--li-end-->
			                            <?endforeach;?>
			                        </ul>
			                    </div><!--.narrow-end-->
			                <!--li-end-->
						<?elseif(!empty($arItem["VALUES"])):;?>
							<li class="cell manufacturers">
		                    <div class="narrow">
		                        <p class="title"><?echo $arItem["NAME"];?> <a href="#" class="link-all-manufacturers"><span><?=GetMessage("ALL")?></span><span style="display:none;">скрыть</span></a></p>
		                        <ul class="three">
		                            <?$n = 0;
		                            foreach($arItem["VALUES"] as $val => $ar):
		                            	$n++?>
		                            	<?if($n==6):?>
		                            		</ul>
					                        <div class="all-manufacturers">
					                            <ul class="three">
		                            	<?endif?>
			                            	<li class="lvl2<?echo $ar["DISABLED"]? ' lvl2_disabled': ''?>"><label><input
										type="checkbox"
										value="<?echo $ar["HTML_VALUE"]?>"
										name="<?echo $ar["CONTROL_NAME"]?>"
										id="<?echo $ar["CONTROL_ID"]?>"
										<?echo $ar["CHECKED"]? 'checked="checked"': ''?>
										onclick="smartFilter.click(this)"
									/> <span><?echo $ar["VALUE"];?></span></label><!--li-end-->
			                            <?endforeach;?>

		                        
		                            </ul>
		                        </div><!--.all-manufacturers-end-->
		                    </div><!--.narrow-end-->
		                <!--li-end-->
						<?endif;?>
					<?endforeach;?>
	                
	            </ul>
	        <a href="#no" class="bt_gray submit"><?=GetMessage("FIND")?></a>
	        <span class="link-open"><i></i></span>
	    </div><!--.filter-extended-->
	<?endif;?>
	<input value="<?=$price_type['PRICE']['CATALOG_GROUP_ID']?>" name="price_type" type="hidden">
	<input type="image" width="1" height="1" src="<?=$templateFolder?>/images/blank.gif" class="fleft">
</form>
<script>
	var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>');
</script>