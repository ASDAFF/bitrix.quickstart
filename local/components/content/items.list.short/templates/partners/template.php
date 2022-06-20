<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?
if( !$arResult['ITEMS_EXISTS'] ){
    return;
}

?><section class="partners-sec">
	<div class="container">
		<h2 class="community__title community__title_near-part">партнеры</h2><?
		foreach($arResult['ITEMS'] as $arSection){
			?><h3 class="community__title_ifopart"><?=$arSection['NAME']?></h3>
			<div class="partners partners_<?=$arSection['CODE']?>"><?
				$i = 1;
				?><div class="partners__betwin <?=( $arSection['NAME'] == 'Информационные партнеры' ) ? 'partners__center parners__inf' : ''?>"><?
					foreach($arSection['ITEMS'] as $arItem){
						$this->AddEditAction($arItem['ID'], "/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . $arParams['IBLOCK_ID'] . "&type=Content&ID=" . $arItem['ID'] . "&bxpublic=Y", "Изменить элемент");

						if( $arItem['CODE'] == 'netapp' ){
							?><br><?
						}
						?><a id="<?=$this->GetEditAreaId($arItem['ID']);?>" href="<?=$arItem['PROPERTY_PARTNER_LINK_VALUE']?>" href="https://abbyy.com/ru-ru/" rel="nofollow noopener" target="_blank" class="partners__link partners__link_<?=$arItem['CODE']?>">
							<img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" class="community__img">
						</a><?

						$i++;
					}
				?></div>
			</div><?
		}
	?></div>
</section>

