<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul>

<?
$i=0;
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
	$i++;
	$style="";
	?>
	
	<?switch($i){
	   case 1: $style='style="width: 107px;" '; break;
	   case 2: $style='style="width: 70px;" '; break;
	   case 3: $style='style="width: 100px;" '; break;
	   case 3: $style='class="last" style="width: 65px;" '; break;
	}?>
	<?if($arItem["SELECTED"]):?>
		<li><span <?=$style?>><?=$arItem["TEXT"]?></span></li>
	<?else:?>
		<li><a href="<?=$arItem["LINK"]?>" <?=$style?>><?=$arItem["TEXT"]?></a></li>
	<?endif?>
	<!--<li><a href="<?=$arItem["LINK"]?>" <?=$style?>><?=$arItem["TEXT"]?></a></li>-->
	
<?endforeach?>

</ul>
<?endif?>