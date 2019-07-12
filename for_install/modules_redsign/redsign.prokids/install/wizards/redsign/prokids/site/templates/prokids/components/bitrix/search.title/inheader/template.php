<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if($arParams["SHOW_INPUT"]!=="N")
{
	?><div id="<?=$CONTAINER_ID?>" class="searchinhead nowrap"><?
		?><form action="<?=$arResult["FORM_ACTION"]?>"><?
			?><i class="icon pngicons icon2"></i><?
			?><i class="icon pngicons icon1"></i><?
			?><div class="aroundtext"><input class="text" id="<?=$INPUT_ID?>" type="text" name="q" value="" size="40" maxlength="50" autocomplete="off" placeholder="<?=GetMessage("RSGOPRO_PLACEHOLDER")?>" /></div><?
			?><input class="nonep" type="submit" name="s" value="<?=GetMessage("RSGOPRO_BTN")?>" /><?
		?></form><?
	?></div><?
}
?><script type="text/javascript">
var jsControl_<?=md5($CONTAINER_ID)?> = new JCTitleSearch({
	'AJAX_PAGE' : '<?=CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
	'CONTAINER_ID': '<?=$CONTAINER_ID?>',
	'INPUT_ID': '<?=$INPUT_ID?>',
	'MIN_QUERY_LEN': 3
});
</script>
