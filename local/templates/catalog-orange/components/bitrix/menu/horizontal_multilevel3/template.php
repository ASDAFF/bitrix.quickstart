<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<div id="top-menu-layout" style="position: relative;">
<ul id="top-menu">
<?
$previousLevel = 0;
$isIndexPage = $APPLICATION->GetCurPage(false)==SITE_DIR;
?>
<?$count = 0;?>
<?
foreach($arResult as $key => $arItem):?>
<?if($count>0){?>&nbsp; <span style="float: left; padding: 0 5px; padding-top: 12px;">|</span>&nbsp;<?}?>

	<li class="root-item"<?if ($arItem["SELECTED"]):?> selected<?endif;?><?if (array_key_exists("ITEMS", $arItem) && count($arItem["ITEMS"]) > 0):?> onmouseover="BXMenuItemOver(this)" onmouseout="BXMenuItemOut(this)"<?endif?>>
<a href="<?=$arItem["LINK"]?>"><span class="root-item-text"><span class="root-item-text-line">
<?=$arItem["TEXT"]?>
</span></span></a><?
		if (array_key_exists("ITEMS", $arItem) && count($arItem["ITEMS"]) > 0):
			?>
			<div class="submenu<?if ($arItem["LARGE"]):?> submenu-two-columns<?endif;?>">
				<div class="submenu-top">
					<div class="right">
						<div class="left">
							<div class="center"></div>
						</div>
					</div>
				</div>
				<div class="content">
					<div class="content-inner">
					<?
					if ($arItem["LARGE"]):
						?>
						<table cellspacing="0">
							<tr>
								<td class="left">
						<?
					endif;
					?>
					<ul>
						<?
						$sub_counter = 1;
						$previousLevel = 2;
						$bFirst = true;
						foreach ($arItem["ITEMS"] as $key => $arSubItem):
							if($previousLevel - $arSubItem["DEPTH_LEVEL"] > 0)
								echo str_repeat("</ul></li>", ($previousLevel - $arSubItem["DEPTH_LEVEL"]));
							if ($arItem["LARGE"] && $sub_counter > ceil(count($arItem["ITEMS"]) / 2) && $arSubItem["DEPTH_LEVEL"] == 2):
								?>
									</ul>
								</td>
								<td class="center"></td>
								<td class="right">
									<ul>
								<?
								$sub_counter = 1;
								$previousLevel = 2;
								$bFirst = true;
							endif;

							if ($arSubItem["IS_PARENT"]):
								?><li class="<?if ($arSubItem["SELECTED"]):?>selected<?endif?><?if ($bFirst):?> first<?endif?>"><a href="<?=$arSubItem["LINK"]?>"><?=$arSubItem["TEXT"]?></a>
								<ul>
							<?else:
								if ($arSubItem["PERMISSION"] > "D"):
									?><li class="<?if ($arSubItem["SELECTED"]):?>selected<?endif?><?if ($bFirst):?> first<?endif?>"><a href="<?=$arSubItem["LINK"]?>"><?if($arSubItem["DEPTH_LEVEL"]==3){?>--<?}?> <?=$arSubItem["TEXT"]?></a></li><?
								endif;
							endif?>

							<?$previousLevel = $arSubItem["DEPTH_LEVEL"];?>
							<?
							$sub_counter++;
							$bFirst = false;
						endforeach;
						?>
						<?if ($previousLevel > 2)://close last item tags?>
							<?=str_repeat("</ul></li>", ($previousLevel-2) );?>
						<?endif?>
					</ul>
					<?
					if ($arItem["LARGE"]):
						?>
								</td>
							</tr>
						</table>
						<?
					endif;
					?>
					</div>
				</div>
				<div class="submenu-bottom">
					<div class="right">
						<div class="left">
							<div class="center"></div>
						</div>
					</div>
				</div>
			</div>
		<?endif;?>
	</li>
<?$count++;?>
<?endforeach;?>
</ul>
</div>
<?endif?>