<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="catalog-compare-result"><?
?><a name="compare_table"></a><?
	?><noindex><p><?
	if($arResult['DIFFERENT']){
		?><a href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("DIFFERENT=N",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a><?
	}
	else{
		echo GetMessage("CATALOG_ALL_CHARACTERISTICS");
	}
	?>&nbsp;|&nbsp;<?
	if(!$arResult['DIFFERENT']){
		?><a href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("DIFFERENT=Y",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></a><?
	}
	else{
		echo GetMessage("CATALOG_ONLY_DIFFERENT");
	}
	?></p></noindex><?
?><br /><?
	?><form class="data-table-form" action="<?=$APPLICATION->GetCurPage()?>" method="get"><?
		?><table class="data-table" cellspacing="0" cellpadding="0" border="0" ><?
			?><thead><?
				?><tr class="element"><td></td><?
				foreach($arResult['ITEMS'] as $key4 => $arItem) {
					?><td><?
							?><div class="element_info-picture"><?
								?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
								if($arItem['PREVIEW_PICTURE']['SRC']!='') {
									?><img class="image" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" border="0" alt="" /><?
								} else {
									?><img class="image" src="<?=$arResult['NO_PHOTO']['src']?>" border="0" alt="" /><?
								}
								?></a><?
								?><a rel="nofollow" class="delete_icon" href="<?=$arItem['DELETE_FROM_COMPARE_URL']?>" title="<?=$arItem['NAME']?>" alt="<?=$arItem['NAME']?>"><i class="icon pngicons"></i></a><?
							?></div><?
								?><div class="catalog-item-name"><a class="text_fader" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a></div><?
								?><div class="element_info-price clearfix"><?
								if(is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) {
									$crossed_price = '';
									?><div class="price"><?=GetMessage('PRICE_FROM').' '.$arItem['OFFERS'][0]['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];?></div><?
									if($arItem['OFFERS'][0]['MIN_PRICE']['DISCOUNT_DIFF']){
										?><span class="crossed_price"><?=$arItem['OFFERS'][0]['MIN_PRICE']['PRINT_VALUE'];?></span><?
										?><span class="discount"><?=$arItem['OFFERS'][0]['MIN_PRICE']['PRINT_DISCOUNT_DIFF'];?></span><?
									} else {
										?><br /><?
									}
								} else {
									$crossed_price = '';
									?><span class="price"><?=$arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></span><?
									if($arItem['MIN_PRICE']['DISCOUNT_DIFF']) {
										?><span class="crossed_price"><?=$arItem['MIN_PRICE']['PRINT_VALUE']?></span><?
										?><span class="discount"><?=$arItem['MIN_PRICE']['PRINT_DISCOUNT_DIFF'];?></span><?
									} else {
										?><br /><?
									}
								}
								?></div><?
								?><div class="element_info-buybtn"><?
									?><a class="btn btn1" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=GetMessage('GO2DETAIL')?></a><?
							?></div><?
					?></td><?
				}
				?></tr><?
			?></thead><?
			?><tbody><?
				if(is_array($arResult['PROPERTY_GROUPS']) && count($arResult['PROPERTY_GROUPS'])>0) {
					foreach($arResult['PROPERTY_GROUPS'] as $key1 => $arGroup) {
						if($arGroup['ALL_EMPTY'] != 'Y' && $arGroup['SHOW']=='Y') {
							?><tr class="group back1 name" data-groupcode="<?=$arGroup['GROUP']['CODE']?>"><?
								if (count($arGroup['BINDS_CODE'])!=0) {
									?><td class="back1" valign="top"><?=$arGroup['GROUP']['NAME']?></td><?
									$i=0;
									do {
										$i++;
										?><td class="no_border"></td><?
									} while($i<=(sizeof($arResult['ITEMS'])-2));
									?><td class="back2 no_border"></td><?
								}
							?></tr><?
							foreach ($arGroup['BINDS_CODE'] as $key => $code) {
								if($arResult['SHOW_PROPERTIES'][$code]['SHOW']=='Y') {
									?><tr><?
										?><td class="property_name"><?=$arResult['SHOW_PROPERTIES'][$code]['NAME']?></td><?
										foreach($arResult['ITEMS'] as $key7 => $arItem) {
											?><td><?
												if($arItem['DISPLAY_PROPERTIES'][$code]['USER_TYPE'] != 'HTML') {
													?><span title="<?=$arItem['DISPLAY_PROPERTIES'][$code]['DISPLAY_VALUE'];?>"><?=$arItem['DISPLAY_PROPERTIES'][$code]['DISPLAY_PROPERTIES_FORMATED']?></span><?
												} else {
													?><span><div class="scroll"><?=$arItem['DISPLAY_PROPERTIES'][$code]['DISPLAY_VALUE']?></div></span><?
												}
											?></td><?
										}
									?></tr><?
								}
							}
						}
					}
				}
				if(is_array($arResult['NOT_GRUPED_PROPS']['CODES']) && count($arResult['NOT_GRUPED_PROPS']['CODES'])>0 && $arResult['NOT_GRUPED_PROPS']['SHOW']=='Y') {
					?><tr class="group back1 name" data-groupcode="rsgopro_other777"><?
						?><td class="back1" valign="top">&nbsp;</td><?
						$i=0;
						do {
							$i++;
							?><td class="no_border">&nbsp;</td><?
						} while($i<=(sizeof($arResult['ITEMS'])-2));
						?><td class="back2 no_border"></td><?
					?></tr><?
					foreach($arResult['NOT_GRUPED_PROPS']['CODES'] as $key => $code) {
						if($arResult['SHOW_PROPERTIES'][$code]['SHOW']=='Y') {
							?><tr><?
								?><td class="property_name"><?=$arResult['SHOW_PROPERTIES'][$code]['NAME']?></td><?
								if($arResult['NOT_GRUPED_PROPS']['ALL_EMPTY'] != 'Y') {
									foreach($arResult['ITEMS'] as $key7 => $arItem) {
										?><td><?
											if($arItem['DISPLAY_PROPERTIES'][$code]['USER_TYPE'] != 'HTML') {
												?><span title="<?=$arItem['DISPLAY_PROPERTIES'][$code]['DISPLAY_VALUE'];?>"><?=$arItem['DISPLAY_PROPERTIES'][$code]['DISPLAY_PROPERTIES_FORMATED']?></span><?
											} else {
												?><span><div class="scroll"><?=$arItem['DISPLAY_PROPERTIES'][$code]['DISPLAY_VALUE']?></div></span><?
											}
										?></td><?
									}
								}
							?></tr><?
						}
					}
				}
			?></tbody><?
		?></table><?
		?><br /><?
		?><input type="hidden" name="action" value="DELETE_FROM_COMPARE_RESULT" /><?
		?><input type="hidden" name="IBLOCK_ID" value="<?=$arParams['IBLOCK_ID']?>" /><?
	?></form><?
?><br /><?
if(count($arResult['ITEMS_TO_ADD'])>0) {
	?><p><?
		?><form action="<?=$APPLICATION->GetCurPage()?>" method="get"><?
			?><input type="hidden" name="IBLOCK_ID" value="<?=$arParams['IBLOCK_ID']?>" /><?
			?><input type="hidden" name="action" value="ADD_TO_COMPARE_RESULT" /><?
			?><select name="id"><?
				foreach($arResult['ITEMS_TO_ADD'] as $ID => $NAME) {
					?><option value="<?=$ID?>"><?=$NAME?></option><?
				}
			?></select><?
			?><input type="submit" value="<?=GetMessage('CATALOG_ADD_TO_COMPARE_LIST')?>" /><?
		?></form><?
	?></p><?
}
?></div><?