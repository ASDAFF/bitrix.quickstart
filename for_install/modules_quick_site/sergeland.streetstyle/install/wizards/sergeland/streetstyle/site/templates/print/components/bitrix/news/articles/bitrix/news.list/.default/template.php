<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="articles">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="post-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="date-box">
			<span class="post-date"><strong><?=$arItem["~DISPLAY_ACTIVE_FROM"]["DD"]?></strong> <?=$arItem["DISPLAY_ACTIVE_FROM"]?></span>
		</div>		
		<div class="post-title fixed">
			<h2><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h2>
		</div>                            		
		<div class="entry">
			<?if(is_array($arItem["PREVIEW_PICTURE"])):?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" class="frame_center" /></a>		
			<?endif?>
			<p><?=$arItem["PREVIEW_TEXT"]?></p> 
			<div class="post-meta-bot">
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="link-more"><?=GetMessage("DETAIL_PAGE")?></a>
			</div>			
			<div class="clear"></div>     
		</div>       
	</div>
<?endforeach?>	
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
