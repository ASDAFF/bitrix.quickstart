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
$bOpenMenu = false;

function sumParent($carry, $item)
{
	if($item["DEPTH_LEVEL"] == 1){
		$carry ++;
	}
    return $carry;
}
$colSum = array_reduce($arResult, "sumParent");
if($colSum > 0){
	if($colSum > 6){
		$colSum = 6;
	}
	$colSmParam = 12 / $colSum;
}else{
	return;
}
$colCounter = 0;
$itemIndex = 0;
$prevParentIndex = 0;
$depthOne = 0;
?>
<div class="tab-pane" id="nav-type">
<?for($itemIndex = 0; $itemIndex <= count($arResult); $itemIndex++){
	if(isset($arResult[$itemIndex])){
		$arItem = $arResult[$itemIndex];
		//echo "DEPTH_LEVEL=".$arItem["DEPTH_LEVEL"]."@depthOne=".$depthOne."@";
		if($arItem["DEPTH_LEVEL"] == 1 && $prevParentIndex != $itemIndex && $bOpenMenu):$bOpenMenu = false;?></menu><?endif;?>
		<?/*if($depthOne % 6 == 5 && $bOpen && $arItem["DEPTH_LEVEL"] == 1){$bOpen = false;?>
		</div>
		<hr>
		<?
	}*/
		if($depthOne % 6 == 0){
			if($bOpen && $arItem["DEPTH_LEVEL"] == 1){
				$bOpen = false;	
		?></div>
		<hr><?
			}
			if(!$bOpen && $arItem["DEPTH_LEVEL"] == 1){
				$bOpen = true;
			?><div class="row"><?
			}
		}
		
		?>
		<?if($arItem["DEPTH_LEVEL"] == 1 && !$bOpenMenu):$bOpenMenu = true;?><menu class="col-sm-<?=$colSmParam?>"><?endif;?>
		<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
		<?if($arItem["DEPTH_LEVEL"] == 1){
			$prevParentIndex = $itemIndex;
			$colCounter++;
		}
		$previousLevel = $arItem["DEPTH_LEVEL"];
		if($arItem["DEPTH_LEVEL"] == 1){$depthOne++;}
	}
}
?>
<?if($bOpen):?></div><?endif;?>
<?if($bOpenMenu):?></menu><?endif;?>
</div>