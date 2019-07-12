<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arSection = null;
if (is_set($arResult, 'SECTION') && is_Set($arResult['SECTION'], 'PATH') && is_array($arResult['SECTION']['PATH']))
	$arSection = array_shift(array_values($arResult['SECTION']['PATH']));

if (is_array($arSection))
{
	$arResult['SET_ADDITIONAL_TITLE'] = $arSection['NAME'];
	$component->SetResultCacheKeys(Array("SET_ADDITIONAL_TITLE"));
}

if ($arParams['MY_QUESTIONS'] == 'Y')
{
	?><h2><?=GetMessage("CITRUS_MY_QUESTIONS")?></h2><?
}
elseif ($arSection)
{
	?><h2><?=$arSection['NAME']?></h2><?
}

if (count($arResult['ITEMS']) <= 0)
{
	?><p><i style="color: #94979a"><?=GetMessage("CITRUS_QUESTSION_LIST_EMPTY")?></i></p><?
	return;
}

?>
<ol class="news question-list" start="<?=($arResult['NAV_RESULT']->NavPageSize * ($arResult['NAV_RESULT']->NavPageNomer-1) + 1)?>">
	<?
	if($arParams["DISPLAY_TOP_PAGER"])
	{
		echo $arResult["NAV_STRING"] . '<br />';
	}
	?><?
	foreach($arResult["ITEMS"] as $arItem)
	{
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

		$bAnswered = strlen(trim($arItem["DETAIL_TEXT"])) > 0;
		?>
		<li class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
			<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="news-item-title dotted" name="<?=$arItem['ID']?>"><?=$arItem["PREVIEW_TEXT"]?></a>
			<div class="news-item-date"><?=$arItem['NAME']?>, <?=ToLower($arItem["DISPLAY_ACTIVE_FROM"])?></div>
			<div class="news-item-text"><?=($bAnswered ? $arItem["DETAIL_TEXT"] : '<strong><?=GetMessage("CITRUS_NO_ANSWER")?></strong>')?></div>
		</li>
		<?
	}
	?>
</ol>
<script type="text/javascript">
$(function () {
	$('.question-list .news-item-text').hide();
	$('.question-list .news-item a').click(function (e) {
		e.preventDefault();
		var $this = $(this);
		$this.parent().toggleClass('news-item-selected');
		$this.siblings('.news-item-text').slideToggle(300);
		//window.history.pushState({"element":this.name},"", $this.attr('href'));
	});
});
</script>
<?
	if($arParams["DISPLAY_BOTTOM_PAGER"])
	{
		echo $arResult["NAV_STRING"] . '<br />';
	}
?>