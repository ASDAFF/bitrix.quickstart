<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if (empty($arResult['STAR_IMAGE_PATH'])) {
	$arResult['STAR_IMAGE_PATH'] = $templateFolder.'/images';
}
?>

<div id="wd_reviews2_rating_<?=$arResult['RATING_UNIQ_ID'];?>" class="wd_reviews2_rating" style="white-space:nowrap">
	<?if($arParams['SCHEMA_ORG']=='Y'):?>
		<div style="display:none" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
			<span itemprop="ratingValue"><?=$arResult['SCORE'];?></span> / <span itemprop="reviewCount"><?=$arResult['COUNT'];?></span>
		</div>
	<?endif?>
</div>

<script>
//<![CDATA[
$('#wd_reviews2_rating_<?=$arResult['RATING_UNIQ_ID'];?>').raty({
	number:<?=$arResult['INTERFACE']['RATING_STARS_COUNT'];?>
	,score: <?=$arResult['SCORE'];?>
	,starOff:'inactive.png'
	,starOn:'active.png'
	,precision:false
	,space:<?if($arResult['INTERFACE']['RATING_SHOW_SPACES']=='Y'):?>true<?else:?>false<?endif?>
	<?if(strlen($arParams['INPUT_NAME'])):?>
		,scoreName:'<?=$arParams['INPUT_NAME'];?>'
	<?endif?>
	<?if($arResult['INTERFACE']['RATING_HALF_SHOW']=='Y' && !$arResult['USE_RANGE']):?>
	,half:true
	,halfShow:true
	,starHalf:'half.png'
	<?endif?>
	,hints:<?=$arResult['HINTS_VALUE'];?>
	<?if($arResult['USE_RANGE']):?>
	,iconRange:<?=$arResult['RANGE_VALUE']?>
	<?endif?>
	,path:'<?=$arResult['STAR_IMAGE_PATH'];?>'
	<?if($arParams['READ_ONLY']=='Y'):?>
	,readOnly:true
	<?endif?>
});
//]]>
</script>
