<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php  //echo "<pre>"; print_r($arResult); echo "</pre>";?>


<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<?$file = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width'=>270, 'height'=>380), BX_RESIZE_IMAGE_EXACT, true);           ?>
		<?$data=$arItem[PROPERTY_DATA_VALUE];
		$data=explode(" ", $data);
		$time=explode(":", $data[1]);
		
		?>
		<div class="flyer-wrapper"  id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				    		<p class="event-date"><?=$data[0]?> <span><?=$time[0]?>:<?=$time[1]?></span></p>
				    		<p class="view-event"><span><?echo $arItem["NAME"]?></span></p>
				    		<figure>
				    			<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>" title=""><img src="<?=$file[src]?>" alt="<?echo $arItem["NAME"]?>"/></a>
				    		</figure>		
	</div>
	<?endforeach;?>

				    	

