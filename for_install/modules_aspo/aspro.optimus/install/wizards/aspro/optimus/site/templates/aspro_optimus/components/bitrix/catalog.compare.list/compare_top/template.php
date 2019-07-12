<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!--noindex-->
	<?$count=count($arResult);?>
	<div class="wraps_icon_block <?=($count ? "fill" : "");?>">
		<a href="<?=$arParams["COMPARE_URL"]?>" class="link" title="<?=GetMessage("CATALOG_COMPARE_ELEMENTS");?>"></a>
		<?if($count){?>
			<div class="count">
				<span>
					<div class="items">
						<a href="<?=$arParams["COMPARE_URL"]?>"><?=$count;?></a>
					</div>
				</span>
			</div>
		<?}?>
	</div>
	<div class="clearfix"></div>
<!--/noindex-->