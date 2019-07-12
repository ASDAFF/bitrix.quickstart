<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?echo "<pre>".print_r($arResult,true)."</pre>"?>
<div class="filter">
<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">

<?
foreach($arResult["ITEMS"] as $arItem)
	if(array_key_exists("HIDDEN", $arItem))
		echo $arItem["INPUT"];
?>


	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?if(!array_key_exists("HIDDEN", $arItem)):?>
			<!--<?=$arItem["NAME"]?>:-->
			<div class="<?=$arItem["TYPE"]?>"><?=$arItem["INPUT"]?></div>
		<?endif?>
	<?endforeach;?>

	<div class="button"><input type="submit" name="set_filter" value="" /></div>
	<input type="hidden" name="set_filter" value="Y" />
	<?/*&nbsp;<input type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" />*/?>

</form>
</div>

<div class="filter">
	<form name="filter" action="" method="post">
    <div class="input"><input name="Name" type="text" value="Название" onblur="javascript:if(this.value == '') { this.value = 'Название';}" onfocus="javascript:if(this.value == 'Название') this.value = '';" /></div>
	<div class="select">
		<select size="1" name="Name">
			<option value="value1">Производитель</option>
		</select>
	</div>
							<div class="select">
								<select size="1" name="Name">
									<option value="value1">Пол ребенка</option>
								</select>
							</div>
							<div class="select">
								<select size="1" name="Name">
									<option value="value1">По любой цене</option>
								</select>
							</div>
							<div class="button"><input type="submit" value="" /></div>
						</form>
	            	</div>