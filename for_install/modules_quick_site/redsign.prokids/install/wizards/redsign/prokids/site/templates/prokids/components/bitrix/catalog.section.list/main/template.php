<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);

//echo"<textarea>";print_r($arResult['SECTIONS']);echo"</textarea>";

if(is_array($arResult["SECTIONS"]) && count($arResult["SECTIONS"])>0)
{
	?><div class="mainsections clearfix"><?
		if( isset($arParams['BLOCK_NAME']) && $arParams['BLOCK_NAME']!='' )
		{
			?><div class="title"><?=$arParams['BLOCK_NAME']?></div><?
		}
		?><ul><?
			$previousLevel = 0;
			$index1 = 1;
			foreach($arResult['SECTIONS'] as $arSection)
			{
				if($index1>($arParams['SHOW_COUNT_LVL1']+1))
					break;
				if($index1>$arParams['SHOW_COUNT_LVL1'] && $arSection['DEPTH_LEVEL']==1)
					continue;
				if($arSection['DEPTH_LEVEL']>2)
					continue;
				if($previousLevel && $arSection['DEPTH_LEVEL']<$previousLevel)
				{
					echo str_repeat('</ul></li>', ($previousLevel - $arSection["DEPTH_LEVEL"]));
				}
				
				if($arSection["DEPTH_LEVEL"] == 1)
				{
					$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT"));
					$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
					?><li class="section" id="<?=$this->GetEditAreaId($arSection['ID']);?>"><?
						?><a href="<?=$arSection['SECTION_PAGE_URL']?>"><img src="<?
						if(empty($arSection['PICTURE']['SRC']))
							echo $arResult['NO_PHOTO']['src'];
						else
							echo $arSection['PICTURE']['SRC'];
						?>" alt="<?=$arSection['PICTURE']['ALT']?>" title="<?=$arSection['PICTURE']['TITLE']?>" /></a><?
						?><a class="parent" href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$arSection['NAME']?>"><?=$arSection['NAME']?></a><?
						if( ($arSection["RIGHT_MARGIN"]-$arSection['LEFT_MARGIN'])>1 && $arParams['SHOW_COUNT_LVL2']>0 && $arParams['TOP_DEPTH']>1 ) // is_parent
						{
							?><ul class="subsections" id="<?=$arSection['ID']?>"><?
						}
					$index1++;
					$index2 = 1;
				} else {
					if($index2>$arParams['SHOW_COUNT_LVL2'])
						continue;
					?><li><a href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$arSection['NAME']?>"><?=$arSection['NAME']?></a></li><?
					$index2++;
				}
				$previousLevel = $arSection['DEPTH_LEVEL'];
			}
			if($previousLevel>1)
			{
				echo str_repeat('</ul></li>', ($previousLevel-1) );
			}
		?></ul><?
	?></div><?
}