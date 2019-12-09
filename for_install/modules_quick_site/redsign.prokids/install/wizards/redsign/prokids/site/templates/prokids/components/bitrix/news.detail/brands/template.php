<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
$show_sections = false;
if( $arParams['SECTIONS_CODE']!='' && is_array($arResult['SECTIONS']) && count($arResult['PROPERTIES'][$arParams['SECTIONS_CODE']]['VALUE'])>0 && IntVal($arResult['SECOND_IBLOCK_ID'])>0)
{
	$show_sections = true;
	?><?$APPLICATION->IncludeComponent(
		'bitrix:catalog.section.list',
		'brand_menu',
		array(
			'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
			'IBLOCK_ID' => $arResult['SECOND_IBLOCK_ID'],
			'CACHE_TYPE' => $arParams['CACHE_TYPE'],
			'CACHE_TIME' => $arParams['CACHE_TIME'],
			'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
			'COUNT_ELEMENTS' => 'N',
			'TOP_DEPTH' => '10',
			'SECTION_URL' => '',
			'IDS' => $arResult['SECTIONS'],
			'FILTER_CONTROL_NAME' => $arResult['FILTER_CONTROL_NAME'],
		),
		$component,
		array('HIDE_ICONS'=>'Y')
	);?><?

?><div class="pcontent"><?
}
	
	?><div class="brandsdetail"><?
		?><div class="clearfix"><?
			if( isset($arResult['DETAIL_PICTURE']) )
			{
				?><div class="img"><?
					?><img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" alt="<?=$arResult['DETAIL_PICTURE']['ALT']?>" title="<?=$arResult['DETAIL_PICTURE']['TITLE']?>" /><?
				?></div><?
			}
			?><div class="description"><?
				?><?=$arResult['DETAIL_TEXT']?><?
			?></div><?
		?></div><?
		?><div class="bot clearfix"><?
			?><div class="back"><?
				?><a class="fullback" href="<?=$arParams['IBLOCK_URL']?>"><i class="icon pngicons"></i><?=GetMessage('GO_BACK')?></a><?
			?></div><?
		?></div><?
	?></div><?
	
if($show_sections) {
	if($arParams['SHOW_BOTTOM_SECTIONS']=='Y') {
		?><?$APPLICATION->IncludeComponent(
			'bitrix:catalog.section.list',
			'brand_big',
			array(
				'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
				'IBLOCK_ID' => $arResult['SECOND_IBLOCK_ID'],
				'CACHE_TYPE' => $arParams['CACHE_TYPE'],
				'CACHE_TIME' => $arParams['CACHE_TIME'],
				'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
				'COUNT_ELEMENTS' => 'N',
				'TOP_DEPTH' => '10',
				'SECTION_URL' => '',
				'IDS' => $arResult['SECTIONS'],
				'FILTER_CONTROL_NAME' => $arResult['FILTER_CONTROL_NAME'],
			),
			$component,
			array('HIDE_ICONS'=>'Y')
		);?><?
	}
?></div><?
}