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

if (empty($arResult["BRAND_BLOCKS"]))
	return;
$strRand = $this->randString();
$strObName = 'obIblockBrand_'.$strRand;
$blockID = 'bx_IblockBrand_'.$strRand;
$mouseEvents = 'onmouseover="'.$strObName.'.itemOver(this);" onmouseout="'.$strObName.'.itemOut(this)"';
$i = 0;
?>
<?
$handlerIDS = array();
foreach ($arResult["BRAND_BLOCKS"] as $blockId => $arBB)
{?>
<?if($i % 2 == 0):?><div class="row" id="<? echo $blockID; ?>"><?endif;?>
<div class="text-center bj-catalogue-label col-xs-6 col-sm-12 no-float-sm"><?
	$brandID = 'brand_'.$arResult['ID'].'_'.$strRand;
	$popupID = $brandID.'_popup';

	$usePopup = $arBB['FULL_DESCRIPTION'] !== false;
	$useLink = $arBB['LINK'] !== false;
	if ($useLink)
	{
		$arBB['LINK'] = htmlspecialcharsbx($arBB['LINK']);
	}?><?if(!empty($arBB['PICT'])):?>
	<div id="<? echo $brandID; ?>" class="bj-catalogue-label__icon">
		<img src="<? echo $arBB['PICT']['SRC']; ?>" class="img-responsive center-block">
	</div><?endif;?>
	<?if ($arBB['NAME'] !== false)
	{
		$arBB['NAME'] = htmlspecialcharsbx($arBB['NAME']);
		echo $arBB['NAME'];
	}
	if ($usePopup)
	{
		$handlerIDS[] = $brandID;
	}
	?></div>
<?if($i % 2 == 1):?></div><?endif;?><?$i++;?>
<hr class="hidden-xs"><?
}
?></div><?
if (!empty($handlerIDS))
{
	$jsParams = array(
		'blockID' => $blockID
	);
?><script type="text/javascript">
var <? echo $strObName; ?> = new JCIblockBrands(<? echo CUtil::PhpToJSObject($jsParams); ?>);
</script><?
}