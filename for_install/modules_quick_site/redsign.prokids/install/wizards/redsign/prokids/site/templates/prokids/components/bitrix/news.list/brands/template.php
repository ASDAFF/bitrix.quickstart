<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( count($arResult['ITEMS'])>0 )
{
	if($arParams['DISPLAY_TOP_PAGER'])
	{
		?><?=$arResult['NAV_STRING']?><?
	}
	
	?><div class="brandslist clearfix<?if($arParams['ADD_STYLES_FOR_MAIN']=='Y'):?> mainstyles<?endif;?>"><?
		
		$index = 1;
		$maxSepNum = 7;
		
		if($arParams['ADD_STYLES_FOR_MAIN']=='Y')
		{
			?><div class="title"><h1><a href="<?=$arParams['BRAND_PAGE']?>"><?=GetMessage('BRAND_TITLE')?></a></h1></div><?
		}
		
		foreach($arResult['DIGITAL'] as $BUKVA => $arData)
		{
			?><div class="item bukva"><?
				?><span><h2><?=$BUKVA?></h2></span><?
				$count = 0;
				foreach($arData['ITEMS'] as $key => $arItem)
				{
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					
					?><div class="subitem" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
						?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a><?
					?></div><?
					$count++;
					if(IntVal($arParams['COUNT_ITEMS'])>0 && ($count+1)>IntVal($arParams['COUNT_ITEMS']))
					{
						$count=0;
						break;
					}
				}
			?></div><?
			?><div class="separator x<?=$index?>"></div><?
			$index++;
			if($index>$maxSepNum){$index=1;}
		}
		
		foreach($arResult['ENG_LETTER'] as $BUKVA => $arData)
		{
			?><div class="item bukva"><?
				?><span><h2><?=$BUKVA?></h2></span><?
				$count = 0;
				foreach($arData['ITEMS'] as $key => $arItem)
				{
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					
					?><div class="subitem" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
						?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a><?
					?></div><?
					$count++;
					if(IntVal($arParams['COUNT_ITEMS'])>0 && ($count+1)>IntVal($arParams['COUNT_ITEMS']))
					{
						$count=0;
						break;
					}
				}
			?></div><?
			?><div class="separator x<?=$index?>"></div><?
			$index++;
			if($index>$maxSepNum){$index=1;}
		}
		
		foreach($arResult['RUS_LETTER'] as $BUKVA => $arData)
		{
			?><div class="item bukva"><?
				?><span><h2><?=$BUKVA?></h2></span><?
				$count = 0;
				foreach($arData['ITEMS'] as $key => $arItem)
				{
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					
					?><div class="subitem" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
						?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a><?
					?></div><?
					$count++;
					if(IntVal($arParams['COUNT_ITEMS'])>0 && ($count+1)>IntVal($arParams['COUNT_ITEMS']))
					{
						$count=0;
						break;
					}
				}
			?></div><?
			?><div class="separator x<?=$index?>"></div><?
			$index++;
			if($index>$maxSepNum){$index=1;}
		}
		
	?></div><?
	
	if($arParams['DISPLAY_BOTTOM_PAGER'])
	{
		?><?=$arResult['NAV_STRING']?><?
	}
}