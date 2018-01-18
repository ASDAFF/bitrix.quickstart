<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?// echo "<pre>"; print_r($arResult["ITEMS"][0]); echo "</pre>";?>

<div class="media-container">
					    	<iframe id="media-player" width="640" height="360" src="http://www.youtube.com/embed/O3c4dPxN1qM?rel=0;showinfo=0" frameborder="0" allowfullscreen></iframe>
				    	</div>
				    	<div class="playlist">
				    		<ul>
				    		
				    		<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<li id="<?=$this->GetEditAreaId($arItem['ID']);?>"><p><span class="set-name" data-href="<?=$arItem[PREVIEW_TEXT]?>"><?=$arItem[NAME]?></span></p></li>
	<?endforeach;?>
				    		
				    			
				    			
				    		</ul>
				    	</div>

