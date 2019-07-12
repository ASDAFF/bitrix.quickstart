<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="news-list">
<div class="news-list-title"><?=GetMessage("NEWS_TITLE")?></div>
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
    <div class="title-news">
		<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
			<?if($arr = ParseDateTime($arItem["DISPLAY_ACTIVE_FROM"], "DD.MM.YYYY HH:MI:SS")){?>
                  <span class="news-date-time"><?=$arr["DD"];?>.<?=$arr["MM"];?><span class="year"><?=$arr["YYYY"];?></span></span>
                <?}?>
		<?endif?>
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
<span class="n-name">
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a><br />
			<?else:?>
				<?echo $arItem["NAME"]?><br />
			<?endif;?>
</span>
		<?endif;?>
        </div>

		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
<?$arImg = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"]["ID"],array("width"=>"80","height"=>"80"),BX_RESIZE_IMAGE_PROPORTIONAL,true);?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
<div class="img_zoom">
				<a href="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"><img class="preview_picture" border="0" src="<?=$arImg["src"]?>" width="<?=$arImg["width"]?>" height="<?=$arImg["height"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" style="float:left" />
<div style="width: 80px; height: 80px; float: left; background: url('<?=$templateFolder;?>/images/img.png') top left no-repeat; margin-left: -85px"></div>
</a>
</div>
			<?else:?>
				<img class="preview_picture" border="0" src="<?=$arImg["src"]?>" width="<?=$arImg["width"]?>" height="<?=$arImg["height"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" style="float:left" />
<div style="width: 80px; height: 80px; float: left; background: url('<?=$templateFolder;?>/images/img.png') top left no-repeat; margin-left: -85px"></div>
			<?endif;?>
		<?endif?>
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<?echo $arItem["PREVIEW_TEXT"];?>
		<?endif;?>
	</div>
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
