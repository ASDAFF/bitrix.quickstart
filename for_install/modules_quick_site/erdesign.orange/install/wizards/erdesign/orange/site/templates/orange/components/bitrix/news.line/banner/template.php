<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?php//  echo "<pre>"; print_r($arResult["ITEMS"][0]); echo "</pre>";?>

<ul class="slides">

	
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<?$file = CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], array('width'=>1092, 'height'=>384), BX_RESIZE_IMAGE_EXACT, true);           ?>
		  <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
						        <img src="<?=$file[src]?>" />
						        <div class="flex-caption">
						        	<div>
							        	<hgroup class="fancy-headers">
								        	<h1><?=$arItem[PROPERTY_DATA_VALUE]?> <span><?=$arItem[PREVIEW_TEXT]?></span></h1>
									        <h2><?echo $arItem["NAME"]?></h2>
									    </hgroup>
									    <p><?echo $arItem["DETAIL_TEXT"]?></p>
						        	</div>
						        </div>
						    </li>
		

	<?endforeach;?>


					      
						  
						
						</ul>


	
	

	
