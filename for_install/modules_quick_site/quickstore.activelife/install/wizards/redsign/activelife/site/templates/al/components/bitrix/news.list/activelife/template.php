<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

?><div class="news-list"><?
if($arParams['DISPLAY_TOP_PAGER']){
	echo $arResult['NAV_STRING'];?><br /><?
}

foreach($arResult['ITEMS'] as $arItem){
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?><div class="news-item clearfix" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
		?><div class="row"><?
			?><div class="col-xs-5 col-sm-4 col-md-2 news_pic_sm"><?
				if($arItem['PREVIEW_PICTURE']['SRC']!=""){
					?><div class="news-item-prev_image"><?
						if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS'])){
							?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
						}
						?><img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" alt="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" /><?
						if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS'])){
							?></a><?
						}
					?></div><?
				}
				elseif($arItem['DETAIL_PICTURE']['SRC']!=""){
					?><div class="news-item-prev_image"><?
						if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS'])){
							?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
						}
						?><img src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" alt="<?=$arItem['DETAIL_PICTURE']['ALT']?>" alt="<?=$arItem['DETAIL_PICTURE']['TITLE']?>" /><?
						if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS'])){
							?></a><?
						}
					?></div><?
				}
			?></div><?
			?><div class="col-xs-7 col-sm-8 col-md-10 news_text_sm"><?
				?><div class="news-item-name"><?
					if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS'])){
						?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
					}
					echo $arItem['NAME'];
					if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS'])){
						?></a><?
					}
				?></div><?
				if($arParams['DISPLAY_PREVIEW_TEXT']=="Y" && $arItem['PREVIEW_TEXT']){
					?><div class="news-item-prev_text"><?
						echo $arItem['PREVIEW_TEXT'];						
					?></div><?
					if(!$arParams['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $arResult['USER_HAVE_ACCESS'])){
						?><div class="news-item-link2detail">
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=GetMessage('LINK2DETAIL')?>
								<svg class="cmp_items__del icon-close icon-svg"><use xlink:href="#svg-right-quote"></use></svg>
							</a>
						</div><?
					}
				}
			?></div><?
		?></div><?
	?></div><?
}

if($arParams['DISPLAY_BOTTOM_PAGER']){
	?><br /><?=$arResult['NAV_STRING']?><?
}
?></div>