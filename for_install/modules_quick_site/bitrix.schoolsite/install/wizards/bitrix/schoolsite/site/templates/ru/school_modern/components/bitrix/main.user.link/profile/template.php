<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(strlen($arResult["FatalError"])==0)
{
?>
<div class="user">
    <div class="blockInner clearfix">
        <? if ($arParams["USE_THUMBNAIL_LIST"] == "Y"): ?>
            <div class="ava darkBorder">
                <?=$arResult["User"]["PersonalPhotoImgThumbnail"]?>
                <!--<img src="img/ava.jpg" alt="">-->
                <div class="c tl"></div>
                <div class="c tr"></div>
                <div class="c bl"></div>
                <div class="c br"></div>
            </div>
        <?php endif; ?>
        <div class="userInfo">
            <h5>
                <? if (strlen($arResult["User"]["HREF"]) > 0):?>
                    <a href="<?=$arResult["User"]["HREF"]?>"<?=($arParams["SEO_USER"] == "Y" ? ' rel="nofollow"' : '')?>><?=$arResult["User"]["NAME_FORMATTED"]?></a>
                <? elseif (strlen($arResult["User"]["DETAIL_URL"]) > 0 && $arResult["CurrentUserPerms"]["Operations"]["viewprofile"]):?>
                    <a href="<?=$arResult["User"]["DETAIL_URL"]?>"<?=($arParams["SEO_USER"] == "Y" ? ' rel="nofollow"' : '')?>><?=$arResult["User"]["NAME_FORMATTED"]?></a>
                <?else:?>
                    <?=$arResult["User"]["NAME_FORMATTED"]?>
                <?endif?>
                <?=(strlen($arResult["User"]["NAME_DESCRIPTION"]) > 0 ? " (".$arResult["User"]["NAME_DESCRIPTION"].")": "")?>
            </h5>
            <a class="logout" href="<?=$APPLICATION->GetCurPageParam("logout=yes", Array("login"))?>"><?=GetMessage("MAIN_UL_TPL_LOGOUT")?></a>
        </div>
    </div>
</div>
<?}?>
