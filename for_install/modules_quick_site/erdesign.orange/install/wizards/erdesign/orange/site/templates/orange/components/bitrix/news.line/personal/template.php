<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<ul class="staff-members">
	<?foreach($arResult["ITEMS"] as $key=>$arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<?$file = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width'=>52, 'height'=>52), BX_RESIZE_IMAGE_EXACT, true);           ?>
			<li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
									<p class="bubble"><?echo $arItem["NAME"]?></p>
									<figure><img src="<?=$file[src]?>" alt="<?echo $arItem["NAME"]?>" /></figure>
			</li>
	<?endforeach;?>
	
</ul>