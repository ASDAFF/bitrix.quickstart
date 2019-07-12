<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if ($arParams["SHOW_LINKED_PRODUCTS"] == "Y" && strlen($arParams["LINKED_PRODUCTS_PROPERTY"])):?>
<?IncludeTemplateLangFile(__FILE__);?>
<hr class="long"/>
<div class="similar_products_wrapp">
	<h3><?=GetMessage("BRAND_PRODUCTS", Array ("#BRAND_NAME#" => $arResult["NAME"]));?></h3>
	<?$GLOBALS[$arParams["CATALOG_FILTER_NAME"]] = array( "PROPERTY_".$arParams["LINKED_PRODUCTS_PROPERTY"] => $arResult["ID"] )?>
	<?include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/news.detail.products_slider.php');?>
</div>
<?endif;?>
<?if ($arParams["SHOW_BACK_LINK"]=="Y"):?>
	<?$refer=$_SERVER['HTTP_REFERER'];
	if (strpos($refer, $arResult["LIST_PAGE_URL"])!==false) {?>
		<div class="back"><a class="back" href="javascript:history.back();"><span><?=GetMessage("BACK");?></span></a></div>
	<?}else{?>
		<div class="back"><a class="back" href="<?=$arResult["LIST_PAGE_URL"]?>"><span><?=GetMessage("BACK");?></span></a></div>
	<?}?>
<?endif;?>