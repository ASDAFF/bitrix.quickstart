<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($arParams["REDIRECT_TO_FIRST_SECTION"] == 'Y')
{
	$arSection = array_shift($arResult['SECTIONS']);
	$arResult["REDIRECT_TO"] = $arSection['SECTION_PAGE_URL'];
	$component->SetResultCacheKeys(Array("REDIRECT_TO"));
}

$arResult['TEMPLATE_PATH'] = $this->GetFolder();
$component->SetResultCacheKeys(Array("TEMPLATE_PATH"));

?>
<div class="questions-section-list"><ul>
<?
	foreach($arResult["SECTIONS"] as $arSection)
	{
		$bCurrent = $arParams['CURRENT_SECTION_ID'] == $arSection['ID'] || $arParams['CURRENT_SECTION_CODE'] == $arSection['CODE'];
		$padding = substr(str_repeat(' . ', $arSection['DEPTH_LEVEL']), 2);
		?><li><a href="<?=$arSection['SECTION_PAGE_URL']?>"<?=($bCurrent ? ' class="selected"' : '')?>><?=($padding.$arSection['NAME'])?></a></li><?
	}
?>
</ul></div>