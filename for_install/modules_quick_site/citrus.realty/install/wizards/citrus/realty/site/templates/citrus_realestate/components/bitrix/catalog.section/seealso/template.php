<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

if (empty($arResult['ITEMS']))
	return;

$this->SetViewTarget('footer-block');

$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage("CITRUS_REALTY_DELETE_CONFIRMATION"));

?>
<div class="column-similar-outter"><div class="column column-similar">
	<div class="block">
		<div class="similar">
			<p class="similar-offers"><?=GetMessage("CITRUS_REALTY_SIMILAR_OFFERS")?></p>
			<?
			$printProp = function ($propertyCode, $emptyPlaceholder = '&mdash;') use (&$arItem)
			{
				return empty($arItem["PROPERTIES"][$propertyCode]["VALUE"])
					? $emptyPlaceholder
					: $arItem["PROPERTIES"][$propertyCode]["VALUE"];
			};

			foreach ($arResult['ITEMS'] as $key => $arItem)
			{
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);

				$preview = \Citrus\Realty\Helper::resizeOfferImage($arItem, 162, 121);

				?>
				<div class="similar-sites" id="<? $this->GetEditAreaId($arItem["ID"]) ?>">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=CFile::ShowImage($preview["src"])?></a>

					<p><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$printProp("address")?></a></p>

					<p class="similar-sites-price"><?=number_format($printProp("cost", 0), 0, ',', ' ')?> <span
							class="similar-rub"><?=GetMessage("CITRUS_REALTY_CURRENCY")?></span></p>

					<div class="similar-sites-shadow"></div>
				</div>
				<?
			}
			?>
		</div>
	</div>
</div></div>
