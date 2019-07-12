<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(count($arResult["SECTIONS"])>0){?>
<div class="services_list">
<ul>
<?
foreach($arResult["SECTIONS"] as $arSection):?>
	<li><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?><?if($arParams["COUNT_ELEMENTS"]):?>&nbsp;(<?=$arSection["ELEMENT_CNT"]?>)<?endif;?></a>
	<?if($arSection["ITEMS"] && count($arSection["ITEMS"])>0){?>
		<ul class = "border_color2">
		<?foreach($arSection["ITEMS"] as $element){?>
			<li>
			<?if(strlen($element["DETAIL_TEXT"])>0){?>
			<a href = "<?echo $element["DETAIL_PAGE_URL"];?>"><?echo $element["NAME"];?></a>
			<?}else{?>
			<?echo $element["NAME"];?>
			<?}?>
			</li>
		<?}?>
		</ul>
	<?}?>
	</li>
<?endforeach?>
</ul>
</div>
<?}?>
<?if(count($arResult["SECTION"]["ITEMS"])>0){?>
<div class = "section_items">
<ul class = "elements_list">
<?foreach($arResult["SECTION"]["ITEMS"] as $item){?>
<li class = "color1">
<?if(strlen($item["DETAIL_TEXT"])>0){?>
<a href = "<?echo $item["DETAIL_PAGE_URL"];?>"><?echo $item["NAME"];?></a>
<?}else{?>
<?echo $item["NAME"];?>
<?}?>
</li>
<?}?>
</ul>
</div>
<?}?>
<div class = "section_desc"><?echo $arResult["SECTION"]["DESCRIPTION"];?></div>
