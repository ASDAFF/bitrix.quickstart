<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

 	        
                    <?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
	<?if($arItem["SELECTED"]):?>
    					
		<li class="lil1"><a class="lil" href="<?=$arItem["LINK"]?>" id="itemselected"><?=$arItem["TEXT"]?></a>
	<?else:?>
    	<li class="lil1"><a class="lil" href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a>
	
    					
	<?endif?>
	
<?endforeach?>

            




<?endif?>