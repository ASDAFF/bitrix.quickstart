<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);
?>

<?if($arParams["SHOW_INPUT"] !== "N"){?>
    <form class="in-row-mid" name="search" action="<?=$arResult["FORM_ACTION"]?>">
        <span><?=GetMessage("CT_BST_SEARCH_TITLE");?></span>
        <div class="in-row-mid" id="<?=$CONTAINER_ID?>">
            <input type="text" id="<?=$INPUT_ID?>" name="q" value="" autocomplete="off" placeholder="<?=GetMessage("CT_BST_SEARCH_BUTTON");?>">
            <input type="submit" value="<?=GetMessage("CT_BST_SEARCH_BUTTON");?>">
        </div>
        <a class="close"></a>
    </form>
<?}?>

<script>
	BX.ready(function(){
		new JCTitleSearch({
			'AJAX_PAGE' : '<?=CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
			'CONTAINER_ID': '<?=$CONTAINER_ID?>',
			'INPUT_ID': '<?=$INPUT_ID?>',
			'MIN_QUERY_LEN': 2
		});
	});
</script>
