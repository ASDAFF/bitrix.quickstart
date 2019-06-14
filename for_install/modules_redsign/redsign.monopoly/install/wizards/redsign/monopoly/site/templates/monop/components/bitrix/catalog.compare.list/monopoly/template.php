<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$frame = $this->createFrame('compare', false)->begin();
?><div class="hidden"><?
    ?><div class="comparelist clearfix<?=( $arResult['COMPARE_CNT']<1 ? ' hidden' : '' )?>"><?
        ?><a class="btn btn-default notajax" type="button" href="<?=$arParams["COMPARE_URL"]?>"><?
            ?><span class="hidden-xs"><?=GetMessage('CATALOG_IN_COMPARE')?></span><i class="fa visible-xs-inline"></i> <span class="count"><?=$arResult['COMPARE_CNT']?><span class="hidden-xs"> <?=GetMessage('CATALOG_COMPARE_PRODUCT')?><?=$arResult["RIGHT_WORD"]?></span></span><?
        ?></a><?
    ?></div><?
?></div><?
$frame->end();