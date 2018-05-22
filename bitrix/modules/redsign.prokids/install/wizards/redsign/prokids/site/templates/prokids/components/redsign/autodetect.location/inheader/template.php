<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

?><div class="location"><?
	ShowMessage($arResult['ERROR_MESSAGE']);
	?><form action="<?=$arResult['ACTION_URL']?>" method="POST" id="inheadlocform"><?
		$frame = $this->createFrame('inheadlocform',false)->begin();
		$frame->setBrowserStorage(true);
			echo bitrix_sessid_post();
			?><input type="hidden" name="<?=$arParams['REQUEST_PARAM_NAME']?>" value="Y" /><?
			?><input type="hidden" name="PARAMS_HASH" value="<?=$arParams['PARAMS_HASH']?>" /><?
			?><span><?=GetMessage('RSGOPRO_QUESTION_1')?>: </span><a class="fancyajax fancybox.ajax big" href="<?=SITE_DIR?>mycity/" title="<?=GetMessage('RSGOPRO_QUESTION_2')?>"><?=$arResult['LOCATION']['CITY_NAME']?><i class="icon pngicons"></i></a><?
		$frame->beginStub();
			?><span><?=GetMessage('RSGOPRO_QUESTION_1')?>: </span></a><?
		$frame->end();
	?></form><?
?></div>