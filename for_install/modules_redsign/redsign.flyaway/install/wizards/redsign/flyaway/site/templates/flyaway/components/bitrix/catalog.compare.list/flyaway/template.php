<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;

$frame = $this->createFrame('', false)->begin();

?>
<div class="hidden">
    <div class="comparelist clearfix js-comparising-list js-comparelist<?=( $arResult['COMPARE_CNT'] < 1 ? ' hidden' : '' )?>">
        <a class="hidden-xs btn btn-default compare-line" type="button" href="<?=$arParams["COMPARE_URL"]?>">
            <span class="hidden-xs"><?=Loc::getMessage('CATALOG_IN_COMPARE')?></span>
            <span class="js-comparelist-count">
                    <span class="hidden-xs"><?=$arResult['COMPARE_CNT']?>&nbsp;<?=Loc::getMessage('CATALOG_COMPARE_PRODUCT')?><?=$arResult["RIGHT_WORD"]?></span>
            </span>
        </a>
        <div class="visible-xs loss-menu-right loss-menu-right_count">
            <a class="selected" href="<?=$arParams["COMPARE_URL"]?>">
                <i class="fa fa-align-left visible-xs-inline"></i>
                <span class="js-comparelist-count"><span class="count"><?=$arResult['COMPARE_CNT']?></span></span>
            </a>
        </div>
    </div>
</div>
<?$frame->end();?>
