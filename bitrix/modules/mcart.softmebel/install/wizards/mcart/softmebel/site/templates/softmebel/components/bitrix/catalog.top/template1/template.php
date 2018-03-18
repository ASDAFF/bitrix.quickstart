<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

	<?foreach($arResult["ROWS"] as $arItems):?>
		
		<?foreach($arItems as $arElement):?>
		<?if(is_array($arElement)):?>
		<?
		$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCT_ELEMENT_DELETE_CONFIRM')));
		?>
			<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" ><img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arElement["NAME"]?>" border="0" alt="<?=$arElement["NAME"]?>" width="202" /></a>
      <br />
					
		
		<?endif;?>
		<?endforeach?>
	<?endforeach?>	