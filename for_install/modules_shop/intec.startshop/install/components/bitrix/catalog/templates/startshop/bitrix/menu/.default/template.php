<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
if (empty($arResult["ALL_ITEMS"]))
	return;

$menuBlockId = "catalog_menu_".$this->randString();
?>
<?$frame = $this->createFrame()->begin()?>
<div class="startshop-menu startshop-hover-shadow" id="<?=$menuBlockId?>">
	<ul id="ul_<?=$menuBlockId?>" class="startshop-standart-block <?=$arParams["HIDE_CATALOG"]=="Y"?"hide_catalog":""?>">
	<?foreach($arResult["MENU_STRUCTURE"] as $itemID => $arColumns):?>     <!-- first level-->
		<?$existPictureDescColomn = ($arResult["ALL_ITEMS"][$itemID]["PARAMS"]["picture_src"] || $arResult["ALL_ITEMS"][$itemID]["PARAMS"]["description"]) ? true : false;?>
		<li onmouseover="BX.CatalogVertMenu.itemOver(this);" onmouseout="BX.CatalogVertMenu.itemOut(this)" class="startshop-level-first <?if($arResult["ALL_ITEMS"][$itemID]["SELECTED"]):?>current<?endif?>">
			<a class="startshop-link startshop-link-hover-dark <?if($arResult["ALL_ITEMS"][$itemID]["SELECTED"]):?>startshop-status-focus<?endif?>" href="<?=$arResult["ALL_ITEMS"][$itemID]["LINK"]?>">
				<?=$arResult["ALL_ITEMS"][$itemID]["TEXT"]?>
				<span class="startshop-shadow-fix"></span>
			</a>
		<?if (is_array($arColumns) && count($arColumns) > 0):?>
			<div class="startshop-children-container startshop-hover-shadow b<?=($existPictureDescColomn) ? count($arColumns)+1 : count($arColumns)?>">
				<?foreach($arColumns as $key=>$arRow):?>
				<div class="startshop-children-block startshop-hover-shadow">
					<ul>
					<?foreach($arRow as $itemIdLevel_2=>$arLevel_3):?>  <!-- second level-->
						<li class="parent">
							<a class="startshop-link startshop-link-hover-dark" href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>">
								<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"]?>
							</a>
						<?if (is_array($arLevel_3) && count($arLevel_3) > 0):?>
							<ul>
							<?foreach($arLevel_3 as $itemIdLevel_3):?>	<!-- third level-->
								<li>
									<a class="startshop-link startshop-link-hover-dark" href="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["LINK"]?>" <?if ($existPictureDescColomn):?>ontouchstart="document.location.href = '<?=$arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"]?>';return false;" onmouseover="menuVertCatalogChangeSectionPicure(this);return false;"<?endif?> data-picture="<?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["PARAMS"]["picture_src"]?>" ><?=$arResult["ALL_ITEMS"][$itemIdLevel_3]["TEXT"]?></a>
								</li>
							<?endforeach;?>
							</ul>
						<?endif?>
						</li>
					<?endforeach;?>
					</ul>
				</div>
				<?endforeach;?>
				<div style="clear: both;"></div>
			</div>
		<?endif?>
		</li>
	<?endforeach;?>
	</ul>
	<div style="clear: both;"></div>
</div>
<?$frame->end()?>