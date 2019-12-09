<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);

?><div class="presscentermainn clearfix"><?
	if( isset($arParams['BLOCK_NAME']) && $arParams['BLOCK_NAME']!='' )
	{
		?><div class="title"><?=$arParams['BLOCK_NAME']?></div><?
	}
	?><div class="in clearfix"><?
		$count = count($arResult['ITEMS']);
		foreach($arResult['ITEMS'] as $key => $arItem)
		{
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			
			?><div class="item<?if($count==($key+1)):?> last<?endif;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
				?><div class="img"><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" border="0" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" /></a></div><?
				?><div class="data"><?
					?><a class="name" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>"><?=$arItem['NAME']?></a> | <a class="blockname" href="<?=$arResult['IBLOCKS'][$arItem['IBLOCK_ID']]['LIST_PAGE_URL']?>" title="<?=$arItem['IBLOCK_NAME']?>"><?=$arItem['IBLOCK_NAME']?></a><?
				?></div><?
			?></div><?
		}
	?></div><?
?></div>