<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><div class="iblockdetail"><?
	if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"]))
	{
		?><div class="pic"><?
			?><img <?
				?>src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" <?
				?>alt="<?=$arResult['DETAIL_PICTURE']['ALT']?>" <?
				?>title="<?=$arResult['DETAIL_PICTURE']['TITLE']?>" <?
			?>/><?
		?></div><?
	}
	?><div class="text clearfix"><?
		if($arResult["NAV_RESULT"])
		{
			if($arParams["DISPLAY_TOP_PAGER"]) { ?><?=$arResult["NAV_STRING"];?><? }
			?><?=$arResult["NAV_TEXT"];?><?
			if($arParams["DISPLAY_BOTTOM_PAGER"]) { ?><?=$arResult["NAV_STRING"];?><? }
		} elseif(strlen($arResult["DETAIL_TEXT"])>0) {
			?><?=$arResult["DETAIL_TEXT"];?><?
		} else {
			?><?=$arResult["PREVIEW_TEXT"];?><?
		}
	?></div><?
	?><div class="bot clearfix"><?
		?><div class="back"><?
			?><a class="fullback" href="<?=$arParams['IBLOCK_URL']?>"><i class="icon pngicons"></i><?=GetMessage('GO_BACK')?></a><?
		?></div><?
		if($arParams['DISPLAY_DATE']!='N' && $arResult['DISPLAY_ACTIVE_FROM'])
		{
			?><div class="date"><?=$arResult['DISPLAY_ACTIVE_FROM']?></div><?
		}
	?></div><?
?></div>