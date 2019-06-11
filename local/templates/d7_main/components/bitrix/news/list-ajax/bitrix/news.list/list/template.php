<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["ITEMS"])>0):?>



	<div class="news-list">
		<b><?=$arResult["NAME"]?></b>
		<ul class="pagination-items">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<li class="paginator-item"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></li>
		<?endforeach;?>
		</ul>
	</div>


    <? $leftItems = $arResult['NAV_RESULT']->NavRecordCount - ($arResult['NAV_RESULT']->NavPageNomer * $arResult['NAV_RESULT']->NavPageSize);
    if ($leftItems > $arResult['NAV_RESULT']->NavPageSize) $leftItems = $arResult['NAV_RESULT']->NavPageSize; ?>
    <div class="pages-container pagination_wrap">
        <? if ($leftItems > 0): ?>
            <a href="javascript:void(0)" class="more_goods">еще <?= $leftItems ?></a>
        <? endif; ?>
        <? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?><br/><?= $arResult["NAV_STRING"] ?><? endif; ?>
    </div>
<?endif?>

