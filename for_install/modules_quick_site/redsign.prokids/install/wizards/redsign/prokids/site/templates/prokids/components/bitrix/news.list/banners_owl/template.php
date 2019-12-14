<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0)
{
	?><div class="aroundowlslider1"><?
		?><div id="owl_slider1" <?if(count($arResult['ITEMS'])==1):?> class="hidecontrols"<?endif;?> style="max-height:<?=$arParams['RSGOPRO_BANNER_HEIGHT']?>px;"><?
			foreach($arResult['ITEMS'] as $arItem)
			{
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				if($arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_TYPE']]['VALUE_XML_ID']=='text')
				{
					?><div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
						?><a href="<?=$arItem['PROPERTIES'][$arParams['RSGOPRO_LINK']]['VALUE']?>"<?if($arItem['PROPERTIES'][$arParams['RSGOPRO_BLANK']]['VALUE']!=''):?> target="_blank"<?endif;?>><?
							?><div class="banner"><?
								?><img u="image" src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" border="0" alt="<?=$arItem['DETAIL_PICTURE']['ALT']?>" title="<?=$arItem['DETAIL_PICTURE']['TITLE']?>" /><?
								?><div class="tmsg"><?
									if(isset($arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_TITLE1']]['VALUE']))
										?><div class="title1"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_TITLE1']]['DISPLAY_VALUE']?></div><?
									if(isset($arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_TITLE2']]['VALUE']))
										?><div class="title2"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_TITLE2']]['DISPLAY_VALUE']?></div><?
									if(isset($arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_TEXT']]['VALUE']))
										?><div class="message"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_TEXT']]['DISPLAY_VALUE']?></div><?
								?></div><?
							?></div><?
						?></a><?
					?></div><?
				} elseif($arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_TYPE']]['VALUE_XML_ID']=='banner') {
					?><div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
						?><img src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" border="0" alt="<?=$arItem['DETAIL_PICTURE']['ALT']?>" title="<?=$arItem['DETAIL_PICTURE']['TITLE']?>" /><?
					?></div><?
				} elseif($arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_TYPE']]['VALUE_XML_ID']=='product') {
					?><div class="item product" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
						?><a href="<?=$arItem['PROPERTIES'][$arParams['RSGOPRO_LINK']]['VALUE']?>"<?if($arItem['PROPERTIES'][$arParams['RSGOPRO_BLANK']]['VALUE']!=''):?> target="_blank"<?endif;?>><?
							?><div class="banner"><?
								?><div class="text"><?
									if(isset($arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_TITLE1']]['VALUE']))
										?><div class="name"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_TITLE1']]['DISPLAY_VALUE']?></div><?
									?><div class="line"><span></span></div><?
									if(isset($arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_TEXT']]['VALUE']))
										?><div class="description"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_TEXT']]['DISPLAY_VALUE']?></div><?
									if(isset($arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_PRICE']]['VALUE']))
										?><div class="price new"><?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSGOPRO_PRICE']]['DISPLAY_VALUE']?></div><?
								?></div><?
								?><div class="image"><?
									?><img u="image" src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" border="0" alt="<?=$arItem['DETAIL_PICTURE']['ALT']?>" title="<?=$arItem['DETAIL_PICTURE']['TITLE']?>" /><?
								?></div><?
							?></div><?
						?></a><?
					?></div><?
				} elseif(
					$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_TYPE']]['VALUE_XML_ID']=='video' &&
					$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_MP4']]['FILE_PATH_MP4']!='' &&
					$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_WEBM']]['FILE_PATH_WEBM']!='' &&
					$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_PIC']]['FILE_PATH_PIC']!=''
				) {
					?><div class="item video" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?
						?><a href="<?=$arItem['PROPERTIES'][$arParams['RSGOPRO_LINK']]['VALUE']?>"<?if($arItem['PROPERTIES'][$arParams['RSGOPRO_BLANK']]['VALUE']!=''):?> target="_blank"<?endif;?>><?
							?><video id="video<?=$arItem['ID']?>" autoplay="autoplay" muted="muted" poster="<?=$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_PIC']]['FILE_PATH_PIC']?>" loop="loop"><?
								?><source src="<?=$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_MP4']]['FILE_PATH_MP4']?>" type="video/mp4"><?
								?><source src="<?=$arItem['PROPERTIES'][$arParams['RSGOPRO_BANNER_VIDEO_WEBM']]['FILE_PATH_WEBM']?>" type="video/webm"><?
							?></video><?
						?></a><?
					?></div><?
				}
			}
		?></div><?
	?></div><?
	?><script>
		var RSGOPRO_change_speed = <?if(IntVal($arParams["RSGOPRO_CHANGE_SPEED"])<1):?>2000<?else:?><?=$arParams["RSGOPRO_CHANGE_SPEED"]?><?endif;?>;
		var RSGOPRO_change_delay = <?if(IntVal($arParams["RSGOPRO_CHANGE_DELAY"])<1):?>8000<?else:?><?=$arParams["RSGOPRO_CHANGE_DELAY"]?><?endif;?>;
	</script><?
}