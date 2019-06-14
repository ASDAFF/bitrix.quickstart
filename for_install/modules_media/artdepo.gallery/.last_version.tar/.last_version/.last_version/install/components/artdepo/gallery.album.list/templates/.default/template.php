<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

<ul class="photo-items-list photo-album-list">
<?foreach($arResult["ITEMS"] as $arItem):?>
    <?
    if($arItem["COVER"]){
        $thumb = CFile::ResizeImageGet(
            $arItem["COVER"]["SOURCE_ID"],
            array("width" => 176, "height" => 132),
            BX_RESIZE_IMAGE_EXACT
        );
        $src = ($thumb["src"]) ? $thumb["src"] : $arItem["COVER"]["PATH"];
    }
    ?>
	<li class="photo-album-item photo-album-active ">
		<div class="photo-item-cover-block-outside">
			<div class="photo-item-cover-block-container">
				<div class="photo-item-cover-block-outer">
					<div class="photo-item-cover-block-inner">
						<div class="photo-item-cover-block-inside" onclick="window.location.href='<?=$arItem["DETAIL_PAGE_URL"]?>';">
						    <?if($arItem["COVER"] && $src):?><img alt="" src="<?=$src?>" width="176" height="132" /><?endif;?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="photo-item-info-block-outside">
			<div class="photo-item-info-block-container">
				<div class="photo-item-info-block-outer">
					<div class="photo-item-info-block-inner">
						<?if($arParams["DISPLAY_NAME"] == "Y" && $arItem["NAME"]):?>
						<div class="photo-album-name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
						<?endif;?>
						<?if($arParams["DISPLAY_DATE"] == "Y" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
                        <div class="photo-album-date"><span><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span></div>
                        <?endif;?>
                        <?if($arParams["DISPLAY_COUNT"] == "Y" && $arItem["ITEMS_COUNT"]>0):?>
                        <div class="photo-album-photos"><?=$arItem["ITEMS_COUNT"]?> <?=GetMessage("PHOTOS_CNT")?></div>
                        <?endif;?>
					</div>
				</div>
			</div>
		</div>
	</li>
<?endforeach;?>
</ul>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
