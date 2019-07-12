<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


// число элементов в строке
$countElemsInRow = 4;
//echo count($arResult[SEARCH]);
//deb($arResult["NAV_RESULT"]->NavRecordCount);
//deb($arParams);

?>
<div class="search-page">

<?
if ($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false) :
	// страница /search/ без запроса
	?><?
elseif (count($arResult["SEARCH"])>0): 
	//deb($arParams);
	$countElemsOnPage = $arParams["PAGE_RESULT_COUNT"];
	
	if ($countElemsOnPage == N_PAGE_SIZE_1) {
		$countElemsOnPage = N_PAGE_SIZE_2;
		$nPageSize = N_PAGE_SIZE_2;
	} else {
		$countElemsOnPage = N_PAGE_SIZE_1;
		$nPageSize = N_PAGE_SIZE_1;
	}
	//deb($countElemsOnPage);
	
	$pageNav = $APPLICATION->GetCurPageParam("nPageSize=".$nPageSize, array("nPageSize"));

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
	?>
	<div class="product-count-bottom">
	Найдено <?=$arResult["NAV_RESULT"]->NavRecordCount?> <?=pluralForm($arResult["NAV_RESULT"]->NavRecordCount, 'модель', 'модели', 'моделей')?>
	<?php 
	if ($arResult["NAV_RESULT"]->NavRecordCount > N_PAGE_SIZE_1) {
		

		?>
		 |
	<a class="nPageSizeS" href="<?=$pageNav?>" value="<?=$countElemsOnPage?>">Выводить по <span><?=$countElemsOnPage?></span></a>
		
		<?php		
	}
	
	?>	
	</div>
	<div class="clear"></div>
	<div class="list">
		<div class="line">
			<div class="item_number">	
			<?php 
	foreach($arResult["ELEMENTS"] as $val) {
		//deb($val);
		
		
		if ($j==1) {
			?>
			<div class="item-block">
			<?php 
			
		}
		//deb($val);CATALOG_PRICE_1
		$valName = $val['NAME'];
		$val['NAME'] = str_replace("&", "&amp;", $val['NAME']);
				
		$val['URL'] = $APPLICATION -> IncludeComponent(
			"trendlist:catalog.item.link", "",
			Array(
				"ID" => $val['ID'],
				"ID_PRODUCTS_STRUCTURE_LEVEL_1" => 145,
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "86400"
		));
		
		if (in_array($val["ID"], $arResult['FAVORITE_PRODUCTS'])) {
			$additional_class = ' active';
			$action = 'del';
		}
		else {
			$additional_class = '';
			$action = 'add';
		}
		//deb($val['PROPERTY_PHOTOS_VALUE']);
		if($arResult['PREVIEW_PICTURE'][ $val['PROPERTY_PHOTOS_VALUE'][0] ] == "")
			$arResult['PREVIEW_PICTURE'][ $val['PROPERTY_PHOTOS_VALUE'][0] ] = SITE_TEMPLATE_PATH."/images/nophoto.png";
		?>
			<div class="item" ><?//=$j?>
				<div class="over">
					<div class="preview">
						<a href="<?=$val['URL']?>"><img src="<?=$arResult['PREVIEW_PICTURE'][ $val['PROPERTY_PHOTOS_VALUE'][0] ]?>" width="180" height="240" alt="" /></a>
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
								<a href="<?=$val['URL'];?>"><img src="<?=$arResult['PREVIEW_PICTURE'][$subval];?>" width="68" height="90" alt="" /></a>
				<?
						}
?>
									</div>
									<p><a href="<?=$val['URL'];?>">Подробнее</a></p>
								</div>
								<div class="clear"></div>
								<div class="others gallery"></div>
							</div>
							<div class="bottom"></div>
						</div>
						<div class="name">
							<?
							if (strlen($valName) < 25){
								echo $val['NAME'];
							} else {
								
								$shortName = substr($valName, 0, 25);
								$shortName = str_replace("&", "&amp;", $shortName);
								echo $shortName."...";
							}
							
							if (!empty($val['SELLER_ID'])) {
								
								$sellerName = $arResult["SELLER_NAMES"][$val['SELLER_ID']];
							} else $sellerName = '';
							?> 
						</div>
						<div class="price">
							<div class="actual"><?=$val['PRICE']?> <span class="rubles">руб.</span></div><span class="seller" key="PROPERTY_SELLER" val="<?=$val['SELLER_ID']?>"><?=$sellerName?></span>
						</div>
					</div>
					<div class="choose">
						<a id="product_<?=$val["ID"]?>" class="fav<?=$additional_class?>" onclick="ajaxFav(<?=$val["ID"]?>, 'product', $(this), '<?=$action?>', <?=$auth_flag?>);return false;"></a>
						<a class="msg"></a>
						<a class="btn my<? if($val['MY_STYLE'] == "Y"){echo " active";}?><? if(CUser::GetID() == 0){echo" authbtn";}?>" href="#" value="<?=$val['ID'];?>">Мой стиль</a>
						<a class="btn no<? if($val['NOT_MY'] == "Y"){echo " active";}?><? if(CUser::GetID() == 0){echo" authbtn";}?>" href="#" value="<?=$val['ID'];?>">Не мое</a>
						<span class="clear">&nbsp;</span>
						
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
	<div class="product-count-bottom">
	Найдено <?=$arResult["NAV_RESULT"]->NavRecordCount?> <?=pluralForm($arResult["NAV_RESULT"]->NavRecordCount, 'модель', 'модели', 'моделей')?>
	<?php 
	if ($arResult["NAV_RESULT"]->NavRecordCount > N_PAGE_SIZE_1) {
		

		?>
		 |
	<a class="nPageSizeS" href="<?=$pageNav?>" value="<?=$countElemsOnPage?>">Выводить по <span><?=$countElemsOnPage?></span></a>
		
		<?php		
	}
	
	?>	
	</div>
	<?=$arResult["NAV_STRING"]?>
	</div>
<?else:?>
	<?php 
	require_once($_SERVER["DOCUMENT_ROOT"] . SITE_DIR . "include/search_not_found.php");
	?>	
<?endif;?>
</div>