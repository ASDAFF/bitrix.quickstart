<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<nav class="navbar navbar-default" role="navigation">  
	<div class="collapse navbar-collapse" id="navbar-collapse-2">
		<ul class="nav navbar-nav">
		<?foreach($arResult as $arItem):?>
			<?if ($arItem["PERMISSION"] > "D"):?>
				<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
			<?else:?>
				<li><a href="#" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
			<?endif?>
		<?endforeach?>
		</ul>
	</div>
</nav>
<?endif?>