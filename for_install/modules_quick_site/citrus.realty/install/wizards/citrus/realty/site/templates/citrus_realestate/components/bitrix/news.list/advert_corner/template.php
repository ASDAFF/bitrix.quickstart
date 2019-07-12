<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var CBitrixComponent $this **/

if (empty($arResult["ITEMS"]))
	return;

if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
	return;

$rootPath = CComponentEngine::makePathFromTemplate($arResult["LIST_PAGE_URL"]);
$title = GetMessage("CITRUS_REALTY_ADVERT_TITLE");
if (is_array($arResult["SECTION"]) && is_array($arResult["SECTION"]["PATH"]) && $section = array_shift($arResult["SECTION"]["PATH"]))
{
	$title = $section["NAME"];
	$rootPath = $section["SECTION_PAGE_URL"];
}
$rootPath = str_replace('//', '/', $rootPath);

?>

<a href="<?=$rootPath?>" class="realty-detailed"><?=GetMessage("CITRUS_REALTY_ADVERT_DETAILS")?></a>
<h3><?=$title?></h3>
<div class="object-items">
	<?
	foreach ($arResult["ITEMS"] as $item)
	{
		$this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		$preview = \Citrus\Realty\Helper::resizeOfferImage($item, 236, 111);
		?>
		<div class="object-item" id="<?=$this->getEditAreaId($item['ID'])?>">
			<div class="object-item-picture"><a href="<?=$item["DETAIL_PAGE_URL"]?>"><?

					if (is_array($preview))
					{
						?><img src="<?=$preview["src"]?>" width="<?=$preview["width"]?>"
							   height="<?=$preview["height"]?>" alt="<?=$item["NAME"]?>" title="<?=$item["NAME"]?>"><?
					}

					?></a></div>
			<div class="object-item-name"><a href="<?=$item["DETAIL_PAGE_URL"]?>"><?=$item["NAME"]?></a></div>
			<div class="object-item-text"><?=$item["PREVIEW_TEXT"]?></div>
		</div>
		<?
		}
	?>
	<a class="object-more" href="<?=$rootPath?>"><?=GetMessage("CITRUS_REALTY_ADVERT_ALL_OFFERS")?></a>
</div>
<div class="shadow"></div>
