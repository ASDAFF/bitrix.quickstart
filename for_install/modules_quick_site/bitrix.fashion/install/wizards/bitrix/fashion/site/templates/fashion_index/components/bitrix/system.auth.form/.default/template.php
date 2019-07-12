<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["FORM_TYPE"] == "login"):?>
<div class="auth"><span><span><?=GetMessage("AUTH_TITLE")?></span></span></div>
<div id="auth">
<?
if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
    ShowMessage($arResult['ERROR_MESSAGE']);
?>

    <div class="close"><a href="#">&times;</a></div>
    <p><big><?=GetMessage("AUTH_TITLE")?></big></p>
    <form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<?if($arResult["BACKURL"] <> ''):?>
    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?endif?>
<?foreach ($arResult["POST"] as $key => $value):?>
    <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
<?endforeach?>
    <input type="hidden" name="AUTH_FORM" value="Y" />
    <input type="hidden" name="TYPE" value="AUTH" />
    <p>
        <label for="email" class="hover"><?=GetMessage("AUTH_LOGIN")?></label>
        <input type="text" id="email" class="default-input hide-label" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" />
    </p>
    <p>
        <label for="password" class="hover"><?=GetMessage("AUTH_PASSWORD")?></label>
        <input type="password" id="password" class="default-input hide-label" name="USER_PASSWORD" maxlength="50" />
    </p>
    <?if ($arResult["STORE_PASSWORD"] == "Y"):?>
    <p>
        <label for="USER_REMEMBER_frm" title="<?=GetMessage("AUTH_REMEMBER_ME")?>"><input type="checkbox" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" /> <?=GetMessage("AUTH_REMEMBER_SHORT")?></label>
    </p>
    <?endif?>
    <p>
        <input type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" />
    </p>
    </form>

    <ul class="login-links">
        <li><noindex><a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></noindex></li>
        <?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
        <li><noindex><strong><a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER")?></a></strong></noindex></li>
        <?endif?>
    </ul>

<?if($arResult["AUTH_SERVICES"]):?>
    <p>
        <?=GetMessage("socserv_as_user_form")?>
<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons", 
    array(
        "AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
        "SUFFIX"=>"form",
    ), 
    $component, 
    array("HIDE_ICONS"=>"Y")
);?>
    </p>
<?endif?>

<?if($arResult["AUTH_SERVICES"]):?>
<?
$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "", 
    array(
        "AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
        "AUTH_URL"=>$arResult["AUTH_URL"],
        "POST"=>$arResult["POST"],
        "POPUP"=>"Y",
        "SUFFIX"=>"form",
    ), 
    $component, 
    array("HIDE_ICONS"=>"Y")
);
?>
<?endif?>
</div>
<script>$('#wrapper').prepend($('#auth'));</script>
<?else:?>
<div class="auth in"><a href="<?=$arResult["PROFILE_URL"]?>" title="<?=GetMessage("AUTH_PROFILE")?>"><?=$arResult["USER_NAME"]?></a>&nbsp;&nbsp;&nbsp;<strong><a href="<?=$APPLICATION->GetCurPageParam('logout=yes', array('logout'))?>"><?=GetMessage("AUTH_LOGOUT_BUTTON")?></a></strong></div>
<?endif?>