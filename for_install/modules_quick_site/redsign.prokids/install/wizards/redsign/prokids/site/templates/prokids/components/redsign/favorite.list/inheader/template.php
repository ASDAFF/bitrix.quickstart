<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

?><div class="rsfavorite"><?
	?><a id="inheadfavorite" href="<?=SITE_DIR?>personal/favorite/"><?
		$frame = $this->createFrame('inheadfavorite',false)->begin();
			?><i class="icon pngicons"></i><?
			?><div class="title opensansbold"><?=GetMessage('RSGOPRO_TITLE')?></div><?
			?><div class="descr"><?=GetMessage('RSGOPRO_PRODUCTS')?>&nbsp;(<span id="favorinfo"><?=$arResult['COUNT']?></span>)</div><?
		$frame->beginStub();
			?><i class="icon pngicons"></i><?
			?><div class="title opensansbold"><?=GetMessage('RSGOPRO_TITLE')?></div><?
			?><div class="descr"><?=GetMessage('RSGOPRO_PRODUCTS')?>&nbsp;(<span id="favorinfo">0</span>)</div><?
		$frame->end();
	?></a><?
?></div>