<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if($arParams["SHOW_INPUT"] !== "N"):?>
<div id="<?=$CONTAINER_ID?>" class="bx_search_container">
	<form name="search" action="<?=$arResult["FORM_ACTION"]?>">
		<div class="bx_field">
			<input id="<?=$INPUT_ID?>" type="text" name="q" data-value="<?=GetMessage("SERGELAND_PLACEHOLDER_MSG")?>" value="<?=$_REQUEST["q"]?>" size="23" maxlength="50" autocomplete="off" class="bx_input_text"/>
			<div class="bx_input_submit" onclick="document.forms.search.submit()"><i class="fa fa-search"></i></div>
		</div>
	</form>
</div>
<?endif?>
<script type="text/javascript">
var jsControl_<?=md5($CONTAINER_ID)?> = new JCTitleSearch_SERGELAND({
	//'WAIT_IMAGE': '/bitrix/themes/.default/images/wait.gif',
	'AJAX_PAGE' : '<?=CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
	'CONTAINER_ID': '<?=$CONTAINER_ID?>',
	'INPUT_ID': '<?=$INPUT_ID?>',
	'MIN_QUERY_LEN': 2
});
jQuery(function(){
	$(".bx_search_container .bx_input_text").placeholdersl();	
});
<?if (isset($_REQUEST["q"])):?>
BX.ready(function(){
	BX("<?=$INPUT_ID?>").value = "<?=CUtil::JSEscape($_REQUEST["q"])?>";
});
<?endif?>
</script>