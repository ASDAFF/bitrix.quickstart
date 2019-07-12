<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

if (empty($arResult))
	return;

$bOpen = false;

$count = count($arResult);
$colSum = ceil($count / 5);
if($colSum > 0){
	if($colSum > 6){
		$colSum = 6;
	}
	$colSmParam = 12 / $colSum;
}else{
	$colSum = 1;
	$colSmParam = 12;
}
$colCounter = 0;
$itemIndex = 0;
$prevParentIndex = 0;

//echo "<pre>";print_r($arResult);echo "</pre>";
?>
<div class="tab-pane active row" id="nav-brands">
	<?while ($colCounter < $colSum) {
	$arItem = $arResult[$itemIndex];
	if(isset($arResult[$itemIndex])){?>		
		<?if($itemIndex % 5 == 0):$bOpen = true;?><menu class="col-sm-<?=$colSmParam?>"><?endif;?>
		<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
		<?if($itemIndex % 5 == 4):$bOpen = false;$colCounter++;?></menu><?endif;?>
		<?$itemIndex++;?>
	<?}else{
		$colCounter = $colSum;
	}
	}?>
	<?if($bOpen):?></menu><?endif;?>
</div>