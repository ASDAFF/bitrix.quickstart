<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<span class="social_icons_wrapp">
	<span><?=GetMessage("STAY_IN_TOUCH")?></span>
	<?if( !empty( $arParams["VK"] ) ){?><a href="<?=$arParams["VK"]?>" target="_blank" class="vkontakte" alt="<?=GetMessage("VKONTAKTE")?>" title="<?=GetMessage("VKONTAKTE")?>"></a><?}?>
	<?if( !empty( $arParams["FACE"] ) ){?><a href="<?=$arParams["FACE"]?>" target="_blank" class="facebook" alt="<?=GetMessage("FACEBOOK")?>" title="<?=GetMessage("FACEBOOK")?>"></a><?}?>
	<?if( !empty( $arParams["TWIT"] ) ){?><a href="<?=$arParams["TWIT"]?>" target="_blank" class="twitter" alt="<?=GetMessage("TWITTER")?>" title="<?=GetMessage("TWITTER")?>"></a><?}?>
</span>