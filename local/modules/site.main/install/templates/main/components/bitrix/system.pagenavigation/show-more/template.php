<?
/**
* Чтобы склонялось кол-во оставшихся элементов ("Показать еще 10 новостей", "Показать еще 1 новость")
* в настройках bitrix:news (или ему подобного) задать параметр PAGER_TITLE в формате "новость|новости|новостей"
*/
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
if(!$arResult['NavShowAlways']) {
	if ($arResult['NavRecordCount'] == 0
		|| ($arResult['NavPageCount'] == 1 && $arResult['NavShowAll'] == false)
	) {
		return;
	}
}
if($arResult['NavPageNomer'] >= $arResult['nEndPage']) {
	return;
}

$nextFirstRecordShow = 1 + $arResult['NavLastRecordShow'];
$nextLastRecordShow = min($nextFirstRecordShow + $arResult['NavPageSize'] - 1, $arResult['NavRecordCount']);
$nextCount = 1 + $nextLastRecordShow - $nextFirstRecordShow;
$strNavQueryString = $arResult['NavQueryString'] ? $arResult['NavQueryString'] . '&amp;' : '';
?>

<div class="pagenvaigation pagenvaigation-show-more" data-parent-selector="section" data-articles-selector="article">
	<a class="fake" href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=1+$arResult['NavPageNomer']?>">
		<?=sprintf(
			GetMessage('PAGENAVIGATION_TITLE'),
			\Site\Main\Util::getNumEnding($nextCount, explode('|', $arResult['NavTitle'])),
			$nextFirstRecordShow . ($nextLastRecordShow > $nextFirstRecordShow ? '&ndash;' . $nextLastRecordShow : ''),
			$arResult['NavRecordCount']
		)?>
	</a>
</div>