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

if ($arResult["DESCRIPTION"])
{
	?><div class="section-description"><?=$section["DESCRIPTION"]?></div><?
}

if (empty($arResult['ITEMS']))
{
	ShowNote(GetMessage("CITRUS_REALTY_NO_OFFERS"));
	return;
}

if ($arParams["DISPLAY_TOP_PAGER"])
{
	?><?=$arResult["NAV_STRING"]?><?
}

$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage("CITRUS_REALTY_DELETE_CONFIRM"));

?><div class="spec-object">
	<?

	$printProp = function($propertyCode, $emptyPlaceholder = '&mdash;') use (&$arItem)
	{
		return empty($arItem["PROPERTIES"][$propertyCode]["VALUE"])
			? $emptyPlaceholder
			: $arItem["PROPERTIES"][$propertyCode]["VALUE"];
	};

	foreach ($arResult['ITEMS'] as $key => $arItem)
	{
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);

		$preview = \Citrus\Realty\Helper::resizeOfferImage($arItem, 180, 110);
		$id = $this->GetEditAreaId($arItem["ID"]);

		?>
		<div class="object-item" id="<?=$id?>">
			<div class="object-item-picture"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=CFile::ShowImage($preview["src"])?></a></div>
			<div class="object-item-name"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></div>
			<div class="object-item-text"><?=$arItem["PREVIEW_TEXT"]?></div>
			<?

        if (!$arResult["DISPLAY_COLUMNS_DEFAULT"]) {
            foreach ($arResult["DISPLAY_COLUMNS"] as $propertyCode => $column) {
                if (substr($propertyCode, 0, 1) == '~') {
                    // поле элемента
                    switch ($propertyCode) {
                        case "~DETAIL_PICTURE":
                            break;

                        case "~NAME":
                            break;

                        default:
                            echo '<div>' . $arItem[substr($propertyCode, 1)] . '</div>';
                            break;
                    }
                } else {
                    $arProp = $arItem['PROPERTIES'][$propertyCode];
                    if ($propertyCode == 'cost') {
                        echo '<div><b>' . ($printProp("cost", 0) ? number_format($printProp("cost", 0), 0, ',', ' ') . GetMessage("CITRUS_REALTY_CURRENCY") : '') . '</b></div>';
                    }
                    else {
                        echo '<div><b>' . $printProp($propertyCode) . '</b></div>';
                    }
                }
            }
        }

        if ($address = $printProp("address",false))
        {
            ?><div class="on-map"><a href="javascript:void(0)" class="map-link" data-address="<?=$address?>"><?=GetMessage("CITRUS_REALTY_VIEW_ON_MAP")?></a></div><?
        }
        if ($printProp("layouts",false))
        {
            ?><a href="<?=$arItem["DETAIL_PAGE_URL"]?>#layouts" class="object-item-layout"><?=GetMessage("CITRUS_REALTY_LAYOUTS")?></a><?
        }

		?>

			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="object-item-more"><?=GetMessage("CITRUS_REALTY_MORE")?></a>
		</div>
		<?
		if ($key%3 == 2)
		{
			?><div class="clear"></div><?
		}
	}
	?>
</div>
<?

if ($arParams["DISPLAY_BOTTOM_PAGER"])
{
	?><?=$arResult["NAV_STRING"]?><?
}

?>