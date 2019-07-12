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
	<div id="<?echo $CONTAINER_ID?>">
		<form class="search" method="post" action="<?echo $arResult["FORM_ACTION"]?>">
			<div class="input">
				<input id="<?echo $INPUT_ID?>" type="text" name="q" maxlength="50" autocomplete="off" value="<?if (isset($_REQUEST["q"])) echo htmlspecialcharsbx($_REQUEST["q"])?>" placeholder="<?=GetMessage("CT_BST_PLACEHOLDER")?>" />
                                       
                                       
                                       
                                       <div class="header-form-submit"><span><input name="s" type="submit"  value="<?=GetMessage("CT_BST_SEARCH_BUTTON")?>"/></span></div>
			</div>
		</form>
    </div>
<?endif?>
