<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(strlen($arResult["FatalError"])==0)
{
?>
<div class="user">
    <?if($arParams["USE_THUMBNAIL_LIST"] == "Y"):?>
        <div class="userpic"><?=$arResult["User"]["PersonalPhotoImgThumbnail"]?></div>
    <?endif?>
    <? if (strlen($arResult["User"]["HREF"]) > 0):?>
        <a href="<?=$arResult["User"]["HREF"]?>"<?=($arParams["SEO_USER"] == "Y" ? ' rel="nofollow"' : '')?> class="username"><?=$arResult["User"]["NAME_FORMATTED"]?></a>
    <? elseif (strlen($arResult["User"]["DETAIL_URL"]) > 0 && $arResult["CurrentUserPerms"]["Operations"]["viewprofile"]):?>
        <a href="<?=$arResult["User"]["DETAIL_URL"]?>"<?=($arParams["SEO_USER"] == "Y" ? ' rel="nofollow"' : '')?> class="username"><?=$arResult["User"]["NAME_FORMATTED"]?></a>
    <?else:?>
        <span class="username"><?=$arResult["User"]["NAME_FORMATTED"]?></span>
    <?endif?>
</div>
<?}?>
