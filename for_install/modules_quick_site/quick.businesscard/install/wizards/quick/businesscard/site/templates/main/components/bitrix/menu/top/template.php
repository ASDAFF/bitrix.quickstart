<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!empty($arResult)):?>
<div class="main-navigation animated">
	<nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
					<span class="sr-only"><?=GetMessage("QUICK_BUSINESSCARD_MENU")?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="collapse navbar-collapse" id="navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">
					<?$previousLevel = 0;
					foreach($arResult as $arItem):?>
						<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
							<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
						<?endif?>
						
						<?if ($arItem["IS_PARENT"]):?>
							<?if ($arItem["DEPTH_LEVEL"] == 1):?>
								<li class="dropdown <?if($arItem["SELECTED"]):?>active<?endif?>"><a class="dropdown-toggle disabled" data-toggle="dropdown" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
									<ul class="dropdown-menu">
							<?else:?>
								<li class="dropdown <?if($arItem["SELECTED"]):?>active<?endif?>"><a class="dropdown-toggle disabled" data-toggle="dropdown" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
									<ul class="dropdown-menu to-left">
							<?endif?>
						<?else:?>
							<?if ($arItem["PERMISSION"] > "D"):?>
								<li class="<?if($arItem["SELECTED"]):?>active<?endif?>"><a class="dropdown-toggle disabled" data-toggle="dropdown" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
							<?else:?>
								<li class="<?if($arItem["SELECTED"]):?>active<?endif?>"><a class="dropdown-toggle disabled" data-toggle="dropdown" href="#" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
							<?endif?>
						<?endif?>
						<?$previousLevel = $arItem["DEPTH_LEVEL"];?>
					<?endforeach?>
					<?if ($previousLevel > 1):?>
						<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
					<?endif?>
				</ul>
			</div>
		</div>
	</nav>
</div>
<?endif?>