<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$this->SetViewTarget('catalog_section_description');
?><div class="catalog_section_description clearfix"><?
	?><h1><?=getMessage('SEARCH_BLOCK_TITLE')?></h1><?
	?><form action="" method="get" class="around_q_input clearfix"><?
		?><a class="submit" href="#"><i class="icon multimage_icons"></i></a><?
		?><input type="text" name="q" value="<?=$arResult['REQUEST']['QUERY']?>" size="40" /><?
		?><input class="none2" type="submit" value="<?=getMessage('SEARCH_GO')?>" /><?
		?><input type="hidden" name="how" value="<?echo $arResult['REQUEST']['HOW']=='d'? 'd': 'r'?>" /><?
	?></form><?
?></div><?
$this->EndViewTarget();
$this->SetViewTarget('catalog_search_other');
?><div class="catalog_search_other context-wrap"><?
	$index = 0;
	foreach($arResult['SEARCH'] as $key => $arItem){
		if($arItem['PARAM1']!=$arParams['CATALOG_IBLOCK_TYPE']){
			if($index>=$arParams['COUNT_RESULT_NOT_CATALOG']){
				break;
			}
			?><div class="catalog_search_other-item"><?
				?><div class="catalog_search_other-item_inner"><?
					?><div class="catalog_search_other-item-pic"><?
						if(is_array($arItem['IMAGES']) && $arItem['IMAGES'][0]['src'] != '')
						?><a href="<?=$arItem['URL']?>"><?
							?><img src="<?=$arItem['IMAGES'][0]['src']?>" alt="" title="Конкурс на лучший рассказ о вашем походе"><?
						?></a><?
					?></div><?
					?><div class="catalog_search_other-item-iblock_name"><a href="<?=$arItem['IBLOCK_LINK']?>"><?=$arResult['IBLOCKS'][$arItem['PARAM2']]['NAME']?></a></div><?
					?><div class="catalog_search_other-item-name"><a href="<?=$arItem['URL']?>" title="<?=$arItem['TITLE']?>"><?=$arItem['TITLE']?></a></div><?
				?></div><?
			?></div><?
			$index++;
		}
	}
?></div><?
$this->EndViewTarget();