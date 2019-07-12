<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

?><div class="characteristics__content"><?
	$count_ul = 1;
	foreach($arResult["GROUPED_ITEMS"] as $arrValue)
	{
		if(is_array($arrValue["PROPERTIES"]) && count($arrValue["PROPERTIES"])>0)
		{
			?><div class="characteristics__item"><?
				?><h2><?=$arrValue["GROUP"]["NAME"]?></h2><?
				?><ul class="characteristics-list" data-ul="<?=$count_ul?>"><?
					$count = 1;
					foreach($arrValue["PROPERTIES"] as $property)
					{
						?><li class="characteristics-list__item <?if($count%2 == 0){echo 'characteristics-list__item_even';}?>"><?
							?><span class="characteristics-list__name"><?
								?><span class="characteristics-list__label"><?=$property["NAME"]?></span><?
							?></span><?
								if( is_array($property["DISPLAY_VALUE"]) )
								{
									?><span class="characteristics-list__value"><?=implode('&nbsp;/&nbsp;', $property["DISPLAY_VALUE"] )?></span><?
								} else {
									?><span class="characteristics-list__value"><?=$property["DISPLAY_VALUE"]?></span><?
								}
						?></li><?
						$count++;
					}
				?></ul><?
				$count_ul++;
			?></div><!--/characteristics__item--><?
		}
	}

	if(is_array($arResult["NOT_GROUPED_ITEMS"]) && count($arResult["NOT_GROUPED_ITEMS"])>0)
	{
		?><div class="characteristics__item"><?
			?><ul class="characteristics-list"><?
					foreach($arResult["NOT_GROUPED_ITEMS"] as $property)
					{
						?><li class="characteristics-list__item"><?
							?><span class="characteristics-list__name"><?
								?><span class="characteristics-list__label"><?=$property["NAME"]?></span><?
							?></span><?
							if( is_array($property["DISPLAY_VALUE"]) )
							{
								?><span class="characteristics-list__value"><?=implode('&nbsp;/&nbsp;', $property["DISPLAY_VALUE"] )?></span><?
							} else {
								?><span class="characteristics-list__value"><?=$property["DISPLAY_VALUE"]?></span><?
							}
						?></li><?
					}
			?></ul><?
		?></div><!--/characteristics__item--><?
	}
?></div><!--/characteristics__content--><?
