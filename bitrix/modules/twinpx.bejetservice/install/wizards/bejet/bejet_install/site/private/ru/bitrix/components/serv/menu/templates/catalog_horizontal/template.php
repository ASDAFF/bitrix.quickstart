<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult["ALL_ITEMS"]))
	return;

$menuBlockId = "catalog_menu_".rand();
?>
<div class="bx_horizontal_menu_advaced" id="<?=$menuBlockId?>">
	<ul id="ul_<?=$menuBlockId?>">
	<?foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns):?>     <!-- first level-->
		<?$existPictureDescColomn = ($arResult["ALL_ITEMS"][$itemID]["PARAMS"]["picture_src"] || $arResult["ALL_ITEMS"][$itemID]["PARAMS"]["description"]) ? true : false;?>
		<li onmouseover="BX.CatalogMenu.itemOver(this);" onmouseout="BX.CatalogMenu.itemOut(this)" class="bx_hma_one_lvl <?if($arResult["ALL_ITEMS"][$itemID]["SELECTED"]):?>current<?endif?><?if (is_array($arColumns) && count($arColumns) > 0):?> dropdown<?endif?>">
			<a href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>" data-description="<?=$arResult["ALL_ITEMS"][$itemID]["PARAMS"]["description"]?>" <?if (is_array($arColumns) && count($arColumns) > 0 && $existPictureDescColomn):?>onmouseover="menuCatalogChangeSectionPicure(this);"<?endif?>>
				<?=$arResult["ALL_ITEMS"][$itemID]["TEXT"]?>
			</a>
			<span class="bx_children_advanced_panel animate">
				<img src="<?=$arResult["ALL_ITEMS"][$itemID]["PARAMS"]["picture_src"]?>" alt="">
			</span>
		<?if (is_array($arColumns) && count($arColumns) > 0):?>
			<div class="bx_children_container b<?=($existPictureDescColomn) ? count($arColumns)+1 : count($arColumns)?> animate">
				<?foreach($arColumns as $key=>$arRow):?>
				<div class="bx_children_block">
					<ul>
					<?foreach($arRow as $itemIdLevel_2=>$arLevel_3):?>  <!-- second level-->
						<li class="parent">
							<a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>" <?if ($existPictureDescColomn):?>onmouseover="if ('ontouchstart' in document.documentElement) document.location.href = '<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>';else menuCatalogChangeSectionPicure(this);"<?endif?> data-picture="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["PARAMS"]["picture_src"]?>" data-description="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["PARAMS"]["description"]?>">
								<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"]?>
							</a>
							<span class="bx_children_advanced_panel animate">
								<img src="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["PARAMS"]["picture_src"]?>" alt="">
							</span>
						<?if (is_array($arLevel_3) && count($arLevel_3) > 0):?>
							<ul>
							<?foreach($arLevel_3 as $itemIdLevel_3):?>	<!-- third level-->
								<li>
									<a href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["LINK"]?>" <?if ($existPictureDescColomn):?>onmouseover="if ('ontouchstart' in document.documentElement) document.location.href = '<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>';else menuCatalogChangeSectionPicure(this);return false;"<?endif?> data-picture="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["PARAMS"]["picture_src"]?>" data-description="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["PARAMS"]["description"]?>"><?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["TEXT"]?></a>
									<span class="bx_children_advanced_panel animate">
										<img src="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["PARAMS"]["picture_src"]?>" alt="">
									</span>
								</li>
							<?endforeach;?>
							</ul>
						<?endif?>
						</li>
					<?endforeach;?>
					</ul>
				</div>
				<?endforeach;?>
				<?if ($existPictureDescColomn):?>
				<div class="bx_children_block advanced">
					<div class="bx_children_advanced_panel">
						<span class="bx_children_advanced_panel animate">
							<a href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>"><span class="bx_section_picture">
								<img src="<?=$arResult["ALL_ITEMS"][$itemID]["PARAMS"]["picture_src"]?>"  alt="">
							</span></a>
							<img src="<?=$this->GetFolder()?>/images/spacer.png" alt="" style="border: none;">
							<strong><?=$arResult["ALL_ITEMS"][$itemID]["TEXT"]?></strong><span class="bx_section_description bx_item_description"><?=$arResult["ALL_ITEMS"][$itemID]["PARAMS"]["description"]?></span>
						</span>
					</div>
				</div>
				<?endif?>
				<div style="clear: both;"></div>
			</div>
		<?endif?>
		</li>
	<?endforeach;?>
	</ul>
	<div style="clear: both;"></div>
</div>

<script>
	window.catalogMenuFirstWidth_<?=$menuBlockId?> = 0;

	BX.ready(function () {
		window.catalogMenuFirstWidth_<?=$menuBlockId?> = menuCatalogResize("<?=$menuBlockId?>") + 20;
		if (window.catalogMenuFirstWidth_<?=$menuBlockId?> > 640)
			menuCatalogAlign("<?=$menuBlockId?>");
		else
			menuCatalogPadding("<?=$menuBlockId?>");

		menuCatalogResize("<?=$menuBlockId?>", window.catalogMenuFirstWidth_<?=$menuBlockId?>);

		if (!window.catalogMenuIDs)
			window.catalogMenuIDs = [{'<?=$menuBlockId?>' : window.catalogMenuFirstWidth_<?=$menuBlockId?>}];
		else
			window.catalogMenuIDs.push({'<?=$menuBlockId?>' : window.catalogMenuFirstWidth_<?=$menuBlockId?>});
	});

	window.onresize = function()
	{
		if (window.catalogMenuIDs)
		{
			for(var obj in window.catalogMenuIDs)
			{
				for(var obj2 in window.catalogMenuIDs[obj])
				{
					menuCatalogResize(obj2, window.catalogMenuIDs[obj][obj2]);
				}
			}
		}
	}
</script>