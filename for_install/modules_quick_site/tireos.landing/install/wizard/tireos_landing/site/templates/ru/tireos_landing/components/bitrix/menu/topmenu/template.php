<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<ul class="list-unstyled">
<?foreach($arResult["ALL_ITEMS"] as $arItem):?>
	<?if($arItem["PARAMS"]["TYPE"]=="S"):?>
	<li>
		<a href="#" data-scroll="<?=$arItem["PARAMS"]["SPECIAL_LINK"]?>">
			<div class="inside">
				<div class="backside"> <?=$arItem["TEXT"]?> </div>
				<div class="frontside"> <?=$arItem["TEXT"]?> </div>
			</div>
		</a>
	</li>
	<?elseif($arItem["PARAMS"]["TYPE"]=="P"):?>
	<li>
		<a class="openform" role="button" href="#<?=$arItem["PARAMS"]["SPECIAL_LINK"]?>">
			<div class="inside">
				<div class="backside"> <?=$arItem["TEXT"]?> </div>
				<div class="frontside"> <?=$arItem["TEXT"]?> </div>
			</div>
		</a>
	</li>
    <?else:?>
	<li>
		<a href="<?=$arItem["LINK"]?>">
			<div class="inside">
				<div class="backside"> <?=$arItem["TEXT"]?> </div>
				<div class="frontside"> <?=$arItem["TEXT"]?> </div>
			</div>
		</a>
	</li>
	<?endif?>
	
<?endforeach?>
</ul>		
<?endif?>