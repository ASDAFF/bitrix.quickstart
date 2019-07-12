<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();



// число элементов в строке
$countElemsInRow = 4;
//echo count($arResult[SEARCH]);
//deb($arResult["NAV_RESULT"]->NavRecordCount);
//deb($arParams);

?>
<div class="search-page col3-list" id="elements">

<?
if (count($arResult["SEARCH"])>0): 
	//deb($arParams);
	/*$countElemsOnPage = $arParams["PAGE_RESULT_COUNT"];
	if ($countElemsOnPage == N_PAGE_SIZE_1) {
		$countElemsOnPage = N_PAGE_SIZE_2;
	} else {
		$countElemsOnPage = N_PAGE_SIZE_1;
	}
	*/
	$j = 1; // счетчик элементов для определения конца строки
	$rowCounter = 1; // счетчик строк
	$countElems = count($arResult["ELEMENTS"]);
	//deb($countElems);
	if ($countElems < ($countElemsInRow+1)) $countRows = 1;
	else {
		$countRows = ceil($countElems/$countElemsInRow);
	
		// число элементов в послед. строке
		$lastRowElemsCount = $countElems % $countElemsInRow;
	}
	//deb($countRows);
	//deb($lastRowElemsCount);
	/*
	?>
	<div class="product-count-bottom">
	Найдено <?=$arResult["NAV_RESULT"]->NavRecordCount?> <?=pluralForm($arResult["NAV_RESULT"]->NavRecordCount, 'модель', 'модели', 'моделей')?> |
	<a class="nPageSize" value="<?=$countElemsOnPage?>">Выводить по <span><?=$countElemsOnPage?></span></a>
	</div>
	<div class="clear"></div>
	
	<?php 
	*/
	?>
	<div class="list">
		<div class="line">
			<div class="item_number">	
			<?php 
	foreach($arResult["ELEMENTS"] as $val) {		
		
		if ($j==1) {
			?>
			<div class="item-block">
			<?php 
			
		}
		$valName = $val['NAME'];
		$val['NAME'] = str_replace("&", "&amp;", $val['NAME']);
				
		$imageryURL = SITE_DIR."imageries/". $val["CODE"] . "/";
		

		//deb($val['PROPERTY_PHOTOS_VALUE']);
		if($arResult['PREVIEW_PICTURE'][ $val['PROPERTY_PHOTOS_VALUE'][0] ] == "")
			$arResult['PREVIEW_PICTURE'][ $val['PROPERTY_PHOTOS_VALUE'][0] ] = SITE_TEMPLATE_PATH."/images/nophoto.png";
		?>
			<div class="item" ><?//=$j?>
				<div class="over">
					<div class="preview">
						<a href="<?=$imageryURL?>"><img src="<?=$arResult['PREVIEW_PICTURE'][ $val['PROPERTY_PHOTOS_VALUE'][0] ]?>"  width="177" height="236" alt="" /></a>
						<div class="info-boxover">
							<div class="middle">
								<h4 class="title"><?=$val['NAME']?></h4>
								<div class="descr">
									<div class="gallery">
						<?
						$ctr = 0;
						if (count($val['PROPERTY_PHOTOS_VALUE']) == 0 )
						{
						?>
									<a href="<?=$val['URL'];?>"><img src="<?=SITE_TEMPLATE_PATH."/images/nophoto.png";?>" width="68" height="90" alt="" /></a>
<?
						}
						foreach($val['PROPERTY_PHOTOS_VALUE'] as $subval)
						{
							if ($ctr++ > 2)break;
				?>
								<a href="<?=$imageryURL?>"><img src="<?=$arResult['PREVIEW_PICTURE'][$subval];?>" width="68" height="90" alt="" /></a>
				<?
						}
?>
									</div>
									<p><a href="<?=$imageryURL?>">Подробнее</a></p>
								</div>
								<div class="clear"></div>
								<div class="others gallery"></div>
							</div>
							<div class="bottom"></div>
						</div>
						<div class="name"><?=$val['NAME']?></div>
						<div class="price">
							<div class="actual"><?=$val['TOTALPRICE']?> <span class="rubles">руб.</span></div>
						</div>
					</div>
						
				</div>
			</div>	
			<?php  
		// выводим закрыв тег для <div class="item-block">
			
		// если всего 1 строка
		$iterFlag = true;
		if (($countRows == 1) && ($j == $countElems)) {
					
			?>
			</div>
			<?php
		} else {
			// если строчек больше 1
			// если мы в последней строке
			if ($rowCounter == $countRows && $lastRowElemsCount>0) {
				$lastElemIndex = $lastRowElemsCount;
			} else {
				$lastElemIndex = $countElemsInRow;
			}
			
			if ($j == $lastElemIndex) {
				$rowCounter++;
				?>
				</div>
				<?php 
				$j = 1;
				$iterFlag = false;
			} 
		}		
					
		if ($iterFlag == true ) $j++;
			
	}
	?>			
			<div class="clear"></div>
			
				
			</div>
		</div>
	</div>
	<?php 

	
		
		
	//}
	?>
	<div id="navigate" class="navigate">
	<?=$arResult["NAV_STRING"]?>
	</div>
<?
else:
	include($_SERVER["DOCUMENT_ROOT"].SITE_DIR."include/catalog_nothing_found.php");
	
endif;?>
</div>