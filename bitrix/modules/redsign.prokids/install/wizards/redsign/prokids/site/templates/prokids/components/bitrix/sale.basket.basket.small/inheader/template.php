<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="basketinhead"><?
	?><a href="<?=$arParams['PATH_TO_BASKET']?>"><?
		?><i class="icon pngicons"></i><?
		?><div class="title opensansbold"><?=GetMessage('RSGOPRO.SMALLBASKET_TITLE')?></div><?
		?><div id="basketinfo" class="descr"><?
			$frame = $this->createFrame('basketinfo',false)->begin();
				if($arResult['NUM_PRODUCTS']>0)
				{
					?><?=$arResult["NUM_PRODUCTS"]?> <?=GetMessage('RSGOPRO.SMALLBASKET_TOVAR')?><?=$arResult['RIGHT_WORD']?> <?=GetMessage('RSGOPRO.SMALLBASKET_NA')?> <?=$arResult['PRINT_FULL_PRICE']?><?
				} else {
					echo GetMessage('RSGOPRO.SMALLBASKET_PUSTO');
				}
			$frame->beginStub();
				echo GetMessage('RSGOPRO.SMALLBASKET_PUSTO');
			$frame->end();
		?></div><?
	?></a><?
?></div>