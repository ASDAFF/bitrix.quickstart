<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="news-list">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<div class="news-list-title"><?=getMessage("TITLE")?></div>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <div class="left">
    		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
    			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
    				<a class="img_link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" /></a>
    			<?else:?>
    				<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
    			<?endif;?>
    		<?endif?>
        </div>
        <div class="right">
            <div class="news-title">
        		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
        			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
        				<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
        			<?else:?>
        				<?echo $arItem["NAME"]?>
        			<?endif;?>
        		<?endif;?>
            </div>
            <?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
    			<div class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></div>
    		<?endif?>
    		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
    			<?echo $arItem["PREVIEW_TEXT"];?>
    		<?endif;?>
    		<?foreach($arItem["FIELDS"] as $code=>$value):?>
    			<small>
    			<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
    			</small>
    		<?endforeach;?>
    		<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
    			<small>
    			<?=$arProperty["NAME"]?>:&nbsp;
    			<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
    				<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
    			<?else:?>
    				<?=$arProperty["DISPLAY_VALUE"];?>
    			<?endif?>
    			</small>
    		<?endforeach;?>
        </div>
	</div>
<?endforeach;?>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>
