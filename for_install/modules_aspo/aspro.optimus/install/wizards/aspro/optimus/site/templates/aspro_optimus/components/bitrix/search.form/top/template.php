<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<form action="<?=$arResult["FORM_ACTION"]?>" class="search1">
	<input id="<?=$arParams["INPUT_ID"]?>" class="search_field1" type="text" name="q" placeholder="<?=GetMessage("placeholder")?>" autocomplete="off" />
	<button id="search-submit-button" type="submit" class="submit"><i></i></button>
	<?if ($arParams["USE_SEARCH_TITLE"]=="Y"):?>
		<div id="<?=$arParams["CONTAINER_ID"]?>"></div>
		<?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/search.title.catalog2.php');?>
	<?endif;?>
</form>