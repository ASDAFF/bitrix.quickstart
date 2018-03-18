<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (method_exists($this, 'setFrameMode')) {
	$this->setFrameMode(true);
}
?>

<?
/*
 * First you must determine next js-vars:
	var asdShareURL = '';
	var asdShareTitle = '';
	var asdSharePic = '';
	var asdShareText = '';
 */
?>

<div id="asd_share_buttons<?= $arParams['ASD_ID']>0 ? $arParams['ASD_ID'] : ''?>">
	<a href="#" class="asd_vk_share" title="<?= strlen($arParams["ASD_LINK_TITLE"]) ? str_replace("#SERVICE#", GetMessage("ASD_VK"), $arParams["ASD_LINK_TITLE"]) : GetMessage("ASD_VK_TITLE")?>" onclick="window.open('http://vkontakte.ru/share.php?url='+asdShareURL+'&amp;title='+asdShareTitle+'&amp;image='+asdSharePic+'&amp;description='+asdShareText, '', 'scrollbars=yes,resizable=no,width=560,height=350,top='+Math.floor((screen.height - 350)/2-14)+',left='+Math.floor((screen.width - 560)/2-5)); return false;"><img src="<?=$arResult["TPL_PATH"]?>/images/vkontakte.png" alt="<?= GetMessage("ASD_VK")?>" border="0" vspace="3" hspace="3" /></a>
	<a href="#" class="asd_fb_share" title="<?= strlen($arParams["ASD_LINK_TITLE"]) ? str_replace("#SERVICE#", GetMessage("ASD_FB"), $arParams["ASD_LINK_TITLE"]) : GetMessage("ASD_FB_TITLE")?>" onclick="window.open('http://www.facebook.com/sharer.php?u='+asdShareURL, '', 'scrollbars=yes,resizable=no,width=560,height=350,top='+Math.floor((screen.height - 350)/2-14)+',left='+Math.floor((screen.width - 560)/2-5)); return false;"><img src="<?=$arResult["TPL_PATH"]?>/images/facebook.png" alt="<?= GetMessage("ASD_FB")?>" border="0" vspace="3" hspace="3" /></a>
	<a href="#" class="asd_od_share" title="<?= strlen($arParams["ASD_LINK_TITLE"]) ? str_replace("#SERVICE#", GetMessage("ASD_OD"), $arParams["ASD_LINK_TITLE"]) : GetMessage("ASD_OD_TITLE")?>" onclick="window.open('http://www.odnoklassniki.ru/dk?st.cmd=addShare&amp;st._surl='+asdShareURL, '', 'scrollbars=yes,resizable=no,width=620,height=450,top='+Math.floor((screen.height - 450)/2-14)+',left='+Math.floor((screen.width - 620)/2-5)); return false;"><img src="<?=$arResult["TPL_PATH"]?>/images/odnoklassniki.png" alt="<?= GetMessage("ASD_OD")?>" border="0" vspace="3" hspace="3" /></a>
	<a href="#" class="asd_tw_share" title="<?= strlen($arParams["ASD_LINK_TITLE"]) ? str_replace("#SERVICE#", GetMessage("ASD_TW"), $arParams["ASD_LINK_TITLE"]) : GetMessage("ASD_TW_TITLE")?>" onclick="window.open('http://twitter.com/share?text='+asdShareTitle+'&amp;url='+asdShareURL, '', 'scrollbars=yes,resizable=no,width=560,height=350,top='+Math.floor((screen.height - 350)/2-14)+',left='+Math.floor((screen.width - 560)/2-5)); return false;"><img src="<?=$arResult["TPL_PATH"]?>/images/twitter.png" alt="<?= GetMessage("ASD_TW")?>" border="0" vspace="3" hspace="3" /></a>
	<a href="#" class="asd_ya_share" title="<?= strlen($arParams["ASD_LINK_TITLE"]) ? str_replace("#SERVICE#", GetMessage("ASD_YANDEX"), $arParams["ASD_LINK_TITLE"]) : GetMessage("ASD_YANDEX_TITLE")?>" onclick="window.open('http://wow.ya.ru/posts_share_link.xml?title='+asdShareTitle+'&amp;url='+asdShareURL, '', 'scrollbars=yes,resizable=no,width=560,height=450,top='+Math.floor((screen.height - 450)/2-14)+',left='+Math.floor((screen.width - 560)/2-5)); return false;"><img src="<?=$arResult["TPL_PATH"]?>/images/yandex.png" alt="<?= GetMessage("ASD_YANDEX")?>" border="0" vspace="3" hspace="3" /></a>
	<a href="#" class="asd_ma_share" title="<?= strlen($arParams["ASD_LINK_TITLE"]) ? str_replace("#SERVICE#", GetMessage("ASD_MAILRU"), $arParams["ASD_LINK_TITLE"]) : GetMessage("ASD_MAILRU_TITLE")?>" onclick="window.open('http://connect.mail.ru/share?share_url='+asdShareURL, '', 'scrollbars=yes,resizable=no,width=560,height=350,top='+Math.floor((screen.height - 350)/2-14)+',left='+Math.floor((screen.width - 560)/2-5)); return false;"><img src="<?=$arResult["TPL_PATH"]?>/images/mailru.png" alt="<?= GetMessage("ASD_MAILRU")?>" border="0" vspace="3" hspace="3" /></a>
</div>