<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if($arResult["SECTIONS_COUNT"] > 0 && !empty($arResult['~SECTIONS'])):?>
<div class="filters text-center">
	<ul class="nav nav-pills">
		<li class="active"><a href="#" data-filter="*"><?=GetMessage("QUICK_EFFORTLESS_FILTER_ALL")?></a></li>
		<?foreach ($arResult['SECTIONS'] as &$arSection):?>
			<?if($arSection["ELEMENT_CNT"] > 0 && $arResult['~SECTIONS'][$arSection["ID"]] > 0):?>
				<li><a href="#" data-filter=".<?=$arSection["ID"]?>"><?=$arSection["NAME"]?></a></li>
			<?endif?>
		<?endforeach?>
	</ul>
</div> 
<?endif?>