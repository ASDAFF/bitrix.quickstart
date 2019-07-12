<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if(!empty($arResult)):?>
<ul class="dropdown right-menu">
	<li><a href="#"><span><i class="fa fa-bars"></i></span></a>
		<ul>
			<?$previousLevel = 0; $selected = false;
			foreach($arResult as $arItem):

			if($arItem["SELECTED"]) 
				$selected = $arItem["SELECTED"]
			?>

				<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
					<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
				<?endif?>
				<?if ($arItem["IS_PARENT"]):?>
					<?if ($arItem["DEPTH_LEVEL"] == 1):?>
						<li<?if($arItem["SELECTED"]):?> class="current-menu-ancestor"<?endif?>><a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a>
							<ul>
					<?else:?>
						<li<?if($arItem["SELECTED"]):?> class="current-menu-ancestor"<?endif?>><a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a>
							<ul>
					<?endif?>
				<?else:?>
					<?if ($arItem["PERMISSION"] > "D"):?>
						<?if ($arItem["DEPTH_LEVEL"] == 1):?>
							<li<?if($arItem["SELECTED"]):?> class="current-menu-ancestor"<?endif?>><a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a></li>
						<?else:?>
							<li<?if($arItem["SELECTED"]):?> class="current-menu-ancestor"<?endif?>><a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a></li>
						<?endif?>
					<?else:?>
						<?if ($arItem["DEPTH_LEVEL"] == 1):?>
							<li<?if($arItem["SELECTED"]):?> class="current-menu-ancestor"<?endif?>><a href="" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><span><?=$arItem["TEXT"]?></span></a></li>
						<?else:?>
							<li<?if($arItem["SELECTED"]):?> class="current-menu-ancestor"<?endif?>><a href="" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><span><?=$arItem["TEXT"]?></span></a></li>
						<?endif?>
					<?endif?>
				<?endif?>

				<?$previousLevel = $arItem["DEPTH_LEVEL"];?>
			<?endforeach?>

			<?if ($previousLevel > 1)://close last item tags?>
				<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
			<?endif?>
		</ul>			
	</li>
</ul>
<?if($selected):?>
	<script>
		jQuery(function(){
			$("ul.dropdown.right-menu").children("li").eq(0).addClass("current-menu-ancestor");
		});
	</script>
<?endif?>
<?endif?>