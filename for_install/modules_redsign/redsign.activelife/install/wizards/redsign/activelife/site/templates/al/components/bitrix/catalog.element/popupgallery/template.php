<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if (empty($arResult['OFFERS']))
{
	$HAVE_OFFERS = false;
	$PRODUCT = &$arResult;
}
else
{
	$HAVE_OFFERS = true;
	$PRODUCT = &$arResult['OFFERS'][0];
}

$bHaveOffer = false;
if (empty($arResult['OFFERS']))
{
	$arItemShow = &$arResult;
}
else
{
	$bHaveOffer = true;
	if (!$arResult['OFFERS_SELECTED'])
	{
		$arResult['OFFERS_SELECTED'] = 0;
	}
	$arItemShow = &$arResult['OFFERS'][$arResult['OFFERS_SELECTED']];
}

$sItemName = (0 < strlen($arItemShow['NAME']) ? $arItemShow['NAME'] : $arResult['NAME']);
$arPhotoChecked = false;
?><div class="catalog-element-head"><?
	?><h1><?=$sItemName?></h1><?
?></div><?
?><div class="rs_gallery"><?
	?><div class="rs_gallery-text"><?=$arResult['PREVIEW_TEXT']?></div><?
	?><div class="rs_gallery-thumbs"><?
		if ($bHaveOffer)
		{
			if (is_array($arItemShow['PRODUCT_PHOTO']) && 0 < count($arItemShow['PRODUCT_PHOTO']))
			{
				foreach ($arItemShow['PRODUCT_PHOTO'] as $arPhoto)
				{
					?><a class="rs_gallery-thumb rs_preview-wrap<?if(!$arPhotoChecked):?> checked<? $arPhotoChecked = $arPhoto; endif?>" href="<?=$arPhoto['SRC']?>"><?
						?><img class="rs_gallery-preview rs_preview" src="<?=$arPhoto['RESIZE']['preview']['src']?>" alt="<?=(0 < strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $sItemName)?>" /><?
						?><div class="rs_overlay"></div><?
					?></a><?
				}
			}
			
		}

		if (is_array($arResult['PRODUCT_PHOTO']) && 0 < count($arResult['PRODUCT_PHOTO']))
		{
			foreach ($arResult['PRODUCT_PHOTO'] as $arPhoto)
			{
				?><a class="rs_gallery-thumb rs_preview-wrap<?if(!$arPhotoChecked):?> checked<? $arPhotoChecked = $arPhoto; endif?>" href="<?=$arPhoto['SRC']?>"><?
					?><img class="rs_gallery-preview rs_preview" src="<?=$arPhoto['RESIZE']['preview']['src']?>" alt="<?=(0 < strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME'])?>" /><?
					?><div class="rs_overlay"></div><?
				?></a><?
			}
		}
	?></div><?
	?><div class="rs_gallery-pic"><?
		if ($arPhotoChecked)
		{
			?><img class="rs_gallery-detal" src="<?=$arPhotoChecked['SRC']?>" alt="<?=(0 < strlen($arPhotoChecked['ALT']) ? $arPhotoChecked['ALT'] : $sItemName)?>" /><?
		}
		if ($arParams['DISPLAY_DATE'] != 'N' && $arResult['DISPLAY_ACTIVE_FROM'])
		{
			?><span class="date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span><?
		}
		?><div class="rs_gallery-prev multimage_icons"></div><?
		?><div class="rs_gallery-next multimage_icons"></div><?
	?></div><?
?></div><?
/*
?><div class="overflower popupgallery js-gallery"><?
    ?><div class="row"><?
        ?><div class="col col-md-12"><?
            ?><div class="row"><?
                // general picture
                ?><div class="col col-sm-9"><?
                    ?><div class="navigations"><?
                        ?><div class="around_changeit"><?
                            ?><div class="changeit"><?
                                if (is_array($arImages[0]['PIC']) && isset($arImages[0]['PIC']['SRC'])>0)
								{
                                    ?><img src="<?=$arImages[0]['PIC']['SRC']?>" alt="" title="" /><?
                                    if ($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"])
									{
                                        ?><span class="date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span><?
                                    }
                                }
                                ?></div><?
                            ?></div><?
                        ?><div class="nav prev js-nav"><span></span></div><?
                        ?><div class="nav next js-nav"><span></span></div><?
					?></div><?
                    if (is_array($arImages) && count($arImages)>0)
					{
                        ?><div class="description"><?=$arImages[0]['PIC']['DESCRIPTION']?></div><?
                    }
                    ?></div><?

                // other pictures
                ?><div class="col col-sm-3 fullright"><?
                    ?><div class="preview"><?=$arResult['PREVIEW_TEXT']?></div><?
                    if (is_array($arImages) && count($arImages)>0)
					{
                        ?><div class="thumbs style1" data-changeto=".changeit img"><?
                        $index = 0;
                        foreach ($arImages as $arImage)
						{
                            if (isset($arParams['RS_MONOPOLY_OFFER_ID']) && IntVal($arImage['DATA']['OFFER_ID'])>0 && IntVal($arImage['DATA']['OFFER_ID'])!=$arParams['RS_MONOPOLY_OFFER_ID'])
							{
                                continue;
                            }
                            ?><div class="pic<?=$index?><?if($index<1):?> checked<?endif;?> thumb"><?
								?><a<?
									?> href="<?=$arImage['PIC']['SRC']?>"<?
									?> data-index="<?=$index?>"<?
									?> data-descr="<?=CUtil::JSEscape($arImage['PIC']['DESCRIPTION'])?>"<?
									?> style="background-image: url('<?=$arImage['PIC']['RESIZE']['src']?>');"<?
								?>><?
									?><div class="overlay"></div><?
									?><i class="fa"></i><?
								?></a><?
                            ?></div><?
                            $index++;
                        }
                        ?></div><?
                    }
				?></div><?
			?></div><?
		?></div><?
	?></div><?
?></div>*/