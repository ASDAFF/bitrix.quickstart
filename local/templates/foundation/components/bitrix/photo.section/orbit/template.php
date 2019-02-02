<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="slideshow-wrapper">
  <div class="preloader"></div>
	<ul data-orbit>
  <?foreach($arResult["ROWS"] as $arItems):?>
	  <?foreach($arItems as $arItem):?>
		  <?if(is_array($arItem)):?>
			  <?
			  $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			  $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BPS_ELEMENT_DELETE_CONFIRM')));
			  ?>
			  <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				  <?if(is_array($arItem["PICTURE"])):?>
					  <img border="0" src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arItem["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PICTURE"]["HEIGHT"]?>" alt="<?=$arItem["NAME"]?>" />
				  <?endif?>
				  <div class="orbit-caption"><?=$arItem["NAME"]?></div>
			  </li>
		  <?endif;?>
	  <?endforeach?>
  <?endforeach?>
  </ul>
</div>

