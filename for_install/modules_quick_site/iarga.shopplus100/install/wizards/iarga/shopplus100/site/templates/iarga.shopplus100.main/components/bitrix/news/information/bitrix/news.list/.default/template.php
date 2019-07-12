<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="search-tags">
	<form>
		<span><?=GetMessage("TAGSEARCH")?></span> <input type="text" value="<?=$_GET['q']?>"name="q" class="inp-text">
		<input type="image" src="<?=$templateFolder?>/blank.gif">
	</form>
</div><!--.search-tags-end-->

<h1><?=$arResult['NAME']?></h1>
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<div class="article-list">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>	
	<article id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<h2><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></h2>
		<p><?=$arItem['PREVIEW_TEXT']?></p>
	</article>
<?endforeach;?>
	
</div><!--.article-list-end-->
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>