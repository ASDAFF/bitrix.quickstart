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
$component->setResultCacheKeys(array("UF_TYPE_XML_ID", "SORT_FIELDS"));

$showFilter = in_array($arResult["UF_TYPE_XML_ID"], array(false, "list")) && (!array_key_exists("CALLED_FROM", $arParams) || $arParams["CALLED_FROM"] !== "favourites");
$this->SetViewTarget('sidebar');
require(__DIR__ . '/sidebar.php');
$this->EndViewTarget();

switch ($arResult["UF_TYPE_XML_ID"])
{
	case "only_text":
		return;
	case "cards":
		require(__DIR__ . "/template_cards.php");
		return;
}


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

?><table>
	<tbody>
	<tr>
		<?foreach ($arResult["DISPLAY_COLUMNS"] as $code=>$col):?>
			<th><?=$col?></th>
		<?endforeach?>
	</tr>
<?

$printProp = function($propertyCode, $emptyPlaceholder = '&mdash;') use (&$arItem)
{
	return empty($arItem["PROPERTIES"][$propertyCode]["VALUE"])
		? $emptyPlaceholder
		: $arItem["PROPERTIES"][$propertyCode]["VALUE"];
};

foreach ($arResult['ITEMS'] as $arItem)
{
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);

	if (is_array($arItem["PREVIEW_PICTURE"]) || is_array($arItem["DETAIL_PICTURE"]))
		$preview = \Citrus\Realty\Helper::resizeOfferImage($arItem, 98, 78);
	else
		$preview["src"] = $templateFolder . "/images/no_photo_small.jpg";


	?><tr id="<?=$this->GetEditAreaId($arItem["ID"])?>"><?
	foreach ($arResult["DISPLAY_COLUMNS"] as $propertyCode => $column)
	{
		if (substr($propertyCode,0,1) == '~')
		{
			// поле элемента
			switch ($propertyCode)
			{
				case "~DETAIL_PICTURE":
					?><td class="td-img"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=CFile::ShowImage($preview["src"])?></a></td><?
					break;
				case "~NAME":
					?><td class="name"><?=$arItem["NAME"]?></td><?
					break;
				default:
					?><td><?=$arItem[substr($propertyCode, 1)]?></td><?
					break;
			}
		}
		else
		{
			$arProp = $arItem['PROPERTIES'][$propertyCode];

			// TODO оформить быдлокод нормально
			if ($propertyCode == "cost")
			{
				?>
				<td class="td-price">
					<span  class="table-price"><?=($printProp("cost", 0) ? number_format($printProp("cost", 0), 0, ',', ' ') : '&mdash;')?></span>
					<?
					if (!array_key_exists("CALLED_FROM", $arParams) || $arParams["CALLED_FROM"] != "favourites")
					{
						?><div class="table-favorites"><a href="javascript:void(0);" data-id="<?=$arItem["ID"]?>" class="add2favourites"><?=GetMessage("CITRUS_REALTY_2FAV")?></a></div><?
					}
					?>
				</td>
				<?
			}
			elseif ($propertyCode == "address" || $propertyCode == "district")
			{
				?>
				<td class="td-adress">
					<?
					if ($propertyCode == "district")
					{
						?><span class="table-address"><?=$printProp("district", '')?></span><?
					}
					?>
					<span class="table-address"><?=$printProp("address", '')?></span>
					<div class="on-map on-map-td"><a href="javascript:void(0)" class="map-link" data-address="<?=$printProp('address','')?>"><?=GetMessage("CITRUS_REALTY_VIEW_ON_MAP")?></a></div>
				</td>
				<?
			}
			else
			{
				?><td class="table-center"><b><?=$printProp($propertyCode)?></b></td><?
			}
		}
	}
	?></tr><?
}
	?>
	</tbody>
</table>
<?

if ($arParams["DISPLAY_BOTTOM_PAGER"])
{
	?><?=$arResult["NAV_STRING"]?><?
}

?>