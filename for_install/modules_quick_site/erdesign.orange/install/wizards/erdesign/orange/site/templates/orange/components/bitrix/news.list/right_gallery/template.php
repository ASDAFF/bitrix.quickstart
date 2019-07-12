<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php // echo "<pre>"; print_r($arResult); echo "</pre>";?>


<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	
	<div class="widget photos" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					    <ul class="photo-stream">
					    <?foreach($arItem[PROPERTIES][PHOTO][VALUE] as $arfoto):?>
					    <?$file = CFile::ResizeImageGet($arfoto, array('width'=>94, 'height'=>94), BX_RESIZE_IMAGE_EXACT, true);           ?>
					    <?$file1 = CFile::ResizeImageGet($arfoto, array('width'=>400, 'height'=>400), BX_RESIZE_IMAGE_EXACT, true);           ?>
					    	<li><a rel="lightbox" href="<?=$file1[src]?>"><img src="<?=$file[src]?>" /></a></li>
						   
			<?endforeach;?>
						    </ul>
			    	</div>

		
<?endforeach;?>


