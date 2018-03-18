<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

<?
$LEVEL2 = false;
$SELECTED = false;
foreach($arResult as $arItem):
	if($arItem["DEPTH_LEVEL"]==1)
		$LEVEL2 = $arItem["SELECTED"] ? true : false;
	elseif($LEVEL2)
		$SELECTED = true;
endforeach;
?>

<?foreach($arResult as $arItem):?>
	
	<?if($arItem["DEPTH_LEVEL"]==1):?>
	
		<?if($arItem["SELECTED"]):?>
			<div class="lev1-selected">
			<div class="box-c"> <em class="ctl"><b>&bull;</b></em> <em class="ctr"><b>&bull;</b></em></div> 
			<div class="box-inner">
			<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
			</div>
			<div class="box-c"><em class="cbl"><b>&bull;</b></em><em class="cbr"><b>&bull;</b></em></div>
			</div>
			<?$LEVEL2 = true;?>
		<?else:?>
			<div class="lev1"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></div>
			<?$LEVEL2 = ($arItem["PARAMS"]["OPEN_DEFAULT"]=="Y" && !$SELECTED) ?  true : false;?>
		<?endif?>
	
	<?elseif($LEVEL2):?>
	
		<?if($arItem["SELECTED"]):?>
			<div class="lev2-selected">&#150; <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></div>
		<?else:?>
			<div class="lev2">&#150; <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></div>
		<?endif?>
	
	<?endif?>
<?endforeach?>

<?endif?>