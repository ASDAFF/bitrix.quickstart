<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
	<div class="menu">
		<nav class="nav nav__primary clearfix">
			<ul id="topnav" class="sf-menu">

			<?
				$previousLevel = 0;
				foreach($arResult as $key => $arItem):?>

				<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
					<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
					<?endif?>

				<?if ($arItem["IS_PARENT"]):?>

					<?if ($arItem["DEPTH_LEVEL"] == 1):?>
						<li id="menu-item-<?=$key?>]" class="menu-item menu-item-type-post_type menu-item-object-page <?if ($arItem["SELECTED"]):?>current-menu-item page_item current_page_item<?endif?>"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
						<ul>
						<?else:?>
						<li id="menu-item-<?=$key?>]" class="menu-item menu-item-type-post_type menu-item-object-page <?if ($arItem["SELECTED"]):?>current-menu-item page_item current_page_item<?endif?>"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
					<ul>
						<?endif?>

						<?else:?>

						<?if ($arItem["PERMISSION"] > "D"):?>

							<?if ($arItem["DEPTH_LEVEL"] == 1):?>
								<li id="menu-item-<?=$key?>]" class="menu-item menu-item-type-post_type menu-item-object-page <?if ($arItem["SELECTED"]):?>current-menu-item page_item current_page_item<?endif?>"><a href="<?=$arItem["LINK"]?>" class=""><?=$arItem["TEXT"]?></a></li>
							<?else:?>
							<li id="menu-item-<?=$key?>]" class="menu-item menu-item-type-post_type menu-item-object-page <?if ($arItem["SELECTED"]):?>current-menu-item page_item current_page_item<?endif?>"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
						<?endif?>

						<?else:?>

						<?if ($arItem["DEPTH_LEVEL"] == 1):?>
							<li id="menu-item-<?=$key?>]" class="menu-item menu-item-type-post_type menu-item-object-page <?if ($arItem["SELECTED"]):?>current-menu-item page_item current_page_item<?endif?>"><a href="" class="<?if ($arItem["SELECTED"]):?>root-item-selected<?else:?>root-item<?endif?>" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
						<?else:?>
						<li id="menu-item-<?=$key?>]" class="menu-item menu-item-type-post_type menu-item-object-page <?if ($arItem["SELECTED"]):?>current-menu-item page_item current_page_item<?endif?>"><a href="" class="denied" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
					<?endif?>

					<?endif?>

					<?endif?>

					<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

					<?endforeach?>

				<?if ($previousLevel > 1)://close last item tags?>
					<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
					<?endif?>

			</ul>
		</nav>
	</div>
	<?endif?>