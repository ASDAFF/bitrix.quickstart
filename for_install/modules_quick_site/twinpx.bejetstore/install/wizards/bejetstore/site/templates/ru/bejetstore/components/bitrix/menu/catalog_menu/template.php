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

$bOpenCol = false;
?>
<?if (!empty($arResult)):?>
<section class="bj-page-header__dropdown container-fluid">
	<div class="i-relative">
		<article>	
<?
$previousLevel = 0;
$counter = 0;$bOpenDiv = false;
foreach($arResult as $arItem):?>
<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
	<?for ($i=0; $i < ($previousLevel - $arItem["DEPTH_LEVEL"]); $i++) { 
		echo "</menu>";
		if($i != ($previousLevel - $arItem["DEPTH_LEVEL"])-1)
			echo "</li>";
	}?>
<?endif?>
<?if ($arItem["DEPTH_LEVEL"] == 1 && $previousLevel):?>
<?if($arItem["DEPTH_LEVEL"] == $previousLevel):?>
</menu>
<?endif;?>
</div><?$bOpenDiv = false;?><?$bOpenCol = false;?>
<?if($counter % 4 == 3 && $arItem["DEPTH_LEVEL"] == 1): $bOpenDiv = false;?></div><?endif;?>
<?endif?>
<?if($arItem["DEPTH_LEVEL"] == 1 && $previousLevel){$counter++;}?>
<?if ($arItem["IS_PARENT"]):?>
<?if ($arItem["DEPTH_LEVEL"] == 1):?>
<?if($counter % 4 == 0): $bOpenDiv = true;?><div class="row"><?endif;?>
<div class="col-md-3"><?$bOpenCol = true;?>
	<h3><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></h3>
	<menu>
<?else:?>
<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
<li><menu>
<?endif?>
<?else:?>
<?if ($arItem["PERMISSION"] > "D"):?>
<?if ($arItem["DEPTH_LEVEL"] == 1):?>
<?if($counter % 4 == 0): $bOpenDiv = true;?><div class="row"><?endif;?>
<div class="col-md-3"><?$bOpenCol = true;?>
	<h3><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></h3>
	<menu>
<?else:?>
<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
<?endif?>
<?else:?>
<?if ($arItem["DEPTH_LEVEL"] == 1):?>
<?if($counter % 4 == 0): $bOpenDiv = true;?><div class="row"><?endif;?>
<div class="col-md-3"><?$bOpenCol = true;?>
	<h3><a href=""><?=$arItem["TEXT"]?></a></h3>
	<menu>
<?else:?>
<li><a href=""><?=$arItem["TEXT"]?></a></li>
<?endif?>
<?endif?>
<?endif?>
<?$previousLevel = $arItem["DEPTH_LEVEL"];?>
<?endforeach?>
<?if ($previousLevel > 1):?>
	<?for ($i=0; $i < ($previousLevel-1); $i++) { 
		echo "</menu>";
		if($i != ($previousLevel-2))
			echo "</li>";
	}?>
</div>
<?if($bOpenDiv):?></div><?endif;?>

<?$p = $previousLevel;
while ($p > 2) {
?></div><?
$p--;
}?>

<?else:?>
<?if($bOpenDiv):?></div><?endif;?>

<?if($counter % 4 != 0):?>
</div>
<?else:?>
<?endif;?>

</menu></div>
<?endif?>
		<button class="up"></button>
		</article>
	</div>
</section>
<?endif?>