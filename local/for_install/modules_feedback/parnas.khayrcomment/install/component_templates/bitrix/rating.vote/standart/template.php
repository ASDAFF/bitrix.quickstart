<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arResult["TOTAL_VALUE"] = intval($arResult["TOTAL_POSITIVE_VOTES"]) - intval($arResult["TOTAL_NEGATIVE_VOTES"]); // для случая, когда нужно банально посчитать +1 и -1, а не нужны эти коэффициенты по подсчету рейтинга и авторитета
?>
<span id="rating-vote-<?=htmlspecialcharsbx($arResult['VOTE_ID'])?>" class="rating-vote <?=($arResult['VOTE_AVAILABLE'] == 'N' ? 'rating-vote-disabled' : '')?>" title="<?=($arResult['VOTE_AVAILABLE'] == 'N'? htmlspecialcharsbx($arResult['ALLOW_VOTE']['ERROR_MSG']) : '')?>">
	<span id="rating-vote-<?=htmlspecialcharsbx($arResult['VOTE_ID'])?>-result" class="rating-vote-result rating-vote-result-<?=($arResult['TOTAL_VALUE'] < 0 ? 'minus' : 'plus')?>" title="<?=htmlspecialcharsbx($arResult['VOTE_TITLE'])?>"> <?=htmlspecialcharsbx($arResult['TOTAL_VALUE'])?></span>
	<a id="rating-vote-<?=htmlspecialcharsbx($arResult['VOTE_ID'])?>-plus" class="rating-vote-plus <?=($arResult['VOTE_BUTTON'] == 'PLUS'? 'rating-vote-plus-active': '')?>" title="<?=$arResult['VOTE_AVAILABLE'] == 'N'? '' : ($arResult['VOTE_BUTTON'] == 'PLUS'? GetMessage("RATING_COMPONENT_CANCEL"): GetMessage("RATING_COMPONENT_PLUS"))?>"></a>&nbsp;<a id="rating-vote-<?=htmlspecialcharsbx($arResult['VOTE_ID'])?>-minus" class="rating-vote-minus <?=($arResult['VOTE_BUTTON'] == 'MINUS'? 'rating-vote-minus-active': '')?>"  title="<?=$arResult['VOTE_AVAILABLE'] == 'N'? '' : ($arResult['VOTE_BUTTON'] == 'MINUS'? GetMessage("RATING_COMPONENT_CANCEL"): GetMessage("RATING_COMPONENT_MINUS"))?>"></a>
</span>
<script type="text/javascript">
BX.ready(function(){
<?if ($arResult['AJAX_MODE'] == 'Y'):?>
	BX.loadCSS('/bitrix/components/bitrix/rating.vote/templates/standart/style.css');
	BX.loadScript('/bitrix/js/main/rating.js', function() {
<?endif;?>
	if (!window.Rating && top.Rating)
		Rating = top.Rating;
	Rating.Set(
		'<?=CUtil::JSEscape($arResult['VOTE_ID'])?>',
		'<?=CUtil::JSEscape($arResult['ENTITY_TYPE_ID'])?>',
		'<?=IntVal($arResult['ENTITY_ID'])?>',
		'<?=CUtil::JSEscape($arResult['VOTE_AVAILABLE'])?>',
		'<?=$USER->GetId()?>',
		{'PLUS' : '<?=GetMessageJS("RATING_COMPONENT_PLUS")?>', 'MINUS' : '<?=GetMessageJS("RATING_COMPONENT_MINUS")?>', 'CANCEL' : '<?=GetMessageJS("RATING_COMPONENT_CANCEL")?>'},
		'standart',
		'<?=CUtil::JSEscape($arResult['PATH_TO_USER_PROFILE'])?>',
		'<?=str_replace($_SERVER["DOCUMENT_ROOT"], "", dirname(__FILE__))?>/vote.ajax.php'
	);
<?if ($arResult['AJAX_MODE'] == 'Y'):?>
	});
<?endif;?>
});
</script>