<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if($arParams["TITLE_BLOCK"]){?>
	<div class="small_title"><?=$arParams["TITLE_BLOCK"];?></div>
<?}?>
<div class="links rows_block soc_icons">
	<?if( !empty( $arParams["VK"] ) ){?>
		<div class="item_block">
			<a href="<?=$arParams["VK"]?>" target="_blank" title="<?=GetMessage("VKONTAKTE")?>" class="vk"></a>
		</div>
	<?}?>
	<?if( !empty( $arParams["ODN"] ) ){?>
		<div class="item_block">
			<a href="<?=$arParams["ODN"]?>" target="_blank" title="<?=GetMessage("ODN")?>" class="odn"></a>
		</div>
	<?}?>
	<?if( !empty( $arParams["FACE"] ) ){?>
		<div class="item_block">
			<a href="<?=$arParams["FACE"]?>" target="_blank" title="<?=GetMessage("FACEBOOK")?>" class="fb"></a>
		</div>
	<?}?>
	<?if( !empty( $arParams["TWIT"] ) ){?>
		<div class="item_block">
			<a href="<?=$arParams["TWIT"]?>" target="_blank" title="<?=GetMessage("TWITTER")?>" class="tw"></a>
		</div>
	<?}?>
	<?if( !empty( $arParams["INST"] ) ){?>
		<div class="item_block">
			<a href="<?=$arParams["INST"]?>" target="_blank" title="<?=GetMessage("INST")?>" class="inst"></a>
		</div>
	<?}?>
	<?if( !empty( $arParams["MAIL"] ) ){?>
		<div class="item_block">
			<a href="<?=$arParams["MAIL"]?>" target="_blank" title="<?=GetMessage("MAIL")?>" class="mail"></a>
		</div>
	<?}?>
	<?if( !empty( $arParams["YOUTUBE"] ) ){?>
		<div class="item_block">
			<a href="<?=$arParams["YOUTUBE"]?>" target="_blank" title="<?=GetMessage("YOUTUBE")?>" class="youtube"></a>
		</div>
	<?}?>
	<?if( !empty( $arParams["GOOGLE_PLUS"] ) ){?>
		<div class="item_block">
			<a href="<?=$arParams["GOOGLE_PLUS"]?>" target="_blank" title="<?=GetMessage("GOOGLE_PLUS")?>" class="google_plus"></a>
		</div>
	<?}?>
</div>