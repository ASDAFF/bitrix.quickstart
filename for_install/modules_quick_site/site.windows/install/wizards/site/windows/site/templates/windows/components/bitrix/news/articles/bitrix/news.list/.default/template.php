<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
	<?endif;?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>

	<article id="post-71" class="post-71 post type-post status-publish format-standard hentry category-uncategorized post__holder cat-1-id">
		<header class="post-header">
			<h2 class="post-title"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>"><?echo $arItem["NAME"]?></a></h2>
		</header>
		<figure class="featured-thumbnail thumbnail "><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" title="<?=$arItem["NAME"]?>">
			<img style="display: inline;" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>"></a></figure>

		<div class="post_content">
			<div class="excerpt"><?echo $arItem["PREVIEW_TEXT"];?></div>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-primary"><?=GetMessage('MORE_INFORMATION')?></a>
			<div class="clear"></div>
		</div>
		<div class="post_meta meta_type_line">
		<?$stmp = MakeTimeStamp($arItem["DATE_CREATE"], FORMAT_DATE);?>
		<i class="icon-calendar"></i><time datetime="<?=$arItem["DATE_CREATE"]?>"><?=FormatDate("d F Y", $stmp)?></time>
		&nbsp;&nbsp;<i class="icon-eye-open"></i><?=intval($arItem["SHOW_COUNTER"])?>
		</div>
	</article>
	<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
	<?endif;?>
