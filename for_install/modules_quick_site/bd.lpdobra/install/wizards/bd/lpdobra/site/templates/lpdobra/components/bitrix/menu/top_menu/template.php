<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<div class="menu">
<?if (!empty($arResult)):?>
<a class="toggleMenu" href="#"><img src="<?=SITE_TEMPLATE_PATH?>/images/nav.png" alt="" /></a>
<ul class="nav" id="nav">

<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
		<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
	
<?endforeach?>

</ul>
<?endif?>
</div>
