<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

foreach ($arResult['ITEMS'] as $key => $arItem) {  
    ?>
    <li data-slide="<?=$key?>" class="slide <?if(!$key) echo 'current';?>">
    	<div class="slide-wrap">
            <?if('Y' == $arItem['PROPERTIES']['EMARKET_HIT']['VALUE']) {?>
    			<a class="item_hit" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>"></a>
    		<?} elseif('Y' == $arItem['PROPERTIES']['EMARKET_NEW']['VALUE']) {?>
    			<a class="item_new" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>"></a>
    		<?}?>
            <a class="item_image" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>" style="background-image: url(<? echo $arItem['DETAIL_PICTURE']['SRC']; ?>)"></a>
            <div class="item_title">
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
            </div>
            <div class="item_rating">
                <?	
    			$rating = intval($arItem['PROPERTIES']['EMARKET_RATING']['VALUE']);
    			for($i=1; $i<=10; $i++)
    			{
    				if(($i == $rating) && ($i%2)) 
    				{
    					echo '<div class="star half"></div>';
    					$i++;
    					continue;
    				}
    				if(!($i%2))
    				{
    					if($i < $rating)
    					{
    						echo '<div class="star"></div>';
    					}
    					elseif($i == $rating)
    					{
    						echo '<div class="star"></div>';
    					}
    					elseif($i > $rating)
    					{
    						echo '<div class="star empty"></div>';
    					}
    				}
    			}
    			?>
            </div>
            <div class="item_price">
                <div class="bx_price"><?
    			if (!empty($arItem['MIN_PRICE']))
    			{
    				if ('N' == $arParams['PRODUCT_DISPLAY_MODE'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
    				{
    					echo GetMessage(
    						'CT_BCS_TPL_MESS_PRICE_SIMPLE_MODE',
    						array(
    							'#PRICE#' => $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'],
    							'#MEASURE#' => GetMessage(
    								'CT_BCS_TPL_MESS_MEASURE_SIMPLE_MODE',
    								array(
    									'#VALUE#' => $arItem['MIN_PRICE']['CATALOG_MEASURE_RATIO'],
    									'#UNIT#' => $arItem['MIN_PRICE']['CATALOG_MEASURE_NAME']
    								)
    							)
    						)
    					);
    				}
    				else
    				{
    					echo $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];
    				}
    				if ('Y' == $arParams['SHOW_OLD_PRICE'] && $arItem['MIN_PRICE']['DISCOUNT_VALUE'] < $arItem['MIN_PRICE']['VALUE'])
    				{
    					?> <span><? echo $arItem['MIN_PRICE']['PRINT_VALUE']; ?></span><?
    				}
    			}
    			?>
    			</div>
    			<?if ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']) {?>
    				<div
    					class="bx_stick_disc right bottom"
    					style="display:<? echo (0 < $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] ? '' : 'none'); ?>;">-<? echo $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']; ?>%</div>
    
    				<div
    					class="bx_stick_disc right bottom"
    					style="display:<? echo (0 < $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] ? '' : 'none'); ?>;">-<? echo $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']; ?>%</div>
    			<?}?>
            </div>
    	</div>
    </li>
<? 

} ?>
<?

?>