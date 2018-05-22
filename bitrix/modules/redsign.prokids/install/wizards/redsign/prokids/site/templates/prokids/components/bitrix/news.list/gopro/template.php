<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( count($arResult['ITEMS'])>0 )
{
	if($arParams['DISPLAY_TOP_PAGER'])
	{
		?><?=$arResult['NAV_STRING']?><?
	}
	
	?><div class="iblocklist"><?
	foreach($arResult['ITEMS'] as $key => $arItem)
	{
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		
		?><div class="item<?if($key>0):?> line<?endif;?> clearfix<?if($arParams['DISPLAY_PICTURE']=='N' || !is_array($arItem['PREVIEW_PICTURE'])):?> noimage<?endif;?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
			if($arParams['DISPLAY_PICTURE']!='N' && is_array($arItem['PREVIEW_PICTURE']))
			{
				?><div class="pic"><?
					if( !$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS']) )
					{
						?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
							?><img <?
								?>src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
								?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
								?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
							?>/><?
						?></a><?
					} else {
							?><img <?
								?>src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" <?
								?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
								?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
							?>/><?
					}
				?></div><?
			}
			?><div class="info"><?
				?><div class="name"><?
					if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS']))
					{
						?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a><?
					} else {
						?><?=$arItem['NAME']?><?
					}
				?></div><?
				if($arParams['DISPLAY_PREVIEW_TEXT']!='N' && $arItem['PREVIEW_TEXT'])
				{
					?><div class="text"><?
						?><?=$arItem['PREVIEW_TEXT'];?><?
					?></div><?
				}
				if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS']))
				{
					?><div class="more"><?
						?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=GetMessage('DETAIL')?><i class="icon pngicons"></i></a><?
					?></div><?
				}
			?></div><?
		?></div><?
	}
	?></div><?
	
	if($arParams['DISPLAY_BOTTOM_PAGER'])
	{
		?><?=$arResult['NAV_STRING']?><?
	}
}