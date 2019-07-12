<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$count=count($arResult);?>
<div class="count <?=($count ? '' : 'empty_items');?>">
	<span>
		<div class="items">
			<div><?=$count;?></div>
		</div>
	</span>
</div>