<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="slider" class="slider">
	<div class="container">
		<div class="slides">				
<?$i=1;
foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="item<?=$i?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="picture">
			<a href="<?=$arItem["DISPLAY_PROPERTIES"]['LINK']['VALUE']?>">
				<img class="preview_picture" border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>"/>
			</a>
		</div>		
	</div>
<?$i++;
endforeach;?>
		</div>
	</div>
	<ul class="pagination">
<?$i=1;
foreach($arResult["ITEMS"] as $arItem):?>	
		<li class="item_<?=$i?>"><a href="javascript:void(0)" class="item_on_<?=$i?>"><span></span></a></li>		
<?$i++;
endforeach;?>
	</ul>
</div>
