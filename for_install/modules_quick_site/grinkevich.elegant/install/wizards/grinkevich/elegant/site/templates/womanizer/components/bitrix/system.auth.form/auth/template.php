<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arResult["FORM_TYPE"] == "login"):?>
	<span id="reg-lnk"><a href="<?=SITE_DIR?>register/">Регистрация</a></span>
	<span id="lnk-cab"><i></i><a href="<?=SITE_DIR?>personal/" id="loginFancy">Войти</a></span>
<?
else:
?>
<a href="<?=SITE_DIR?>personal/" class="auth"><?=GetMessage("AUTH_PROFILE")?></a> <a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array(
     "login",
     "logout",
     "register",
     "forgot_password",
     "change_password"));?>" style="background-image: none;">[<?=GetMessage("AUTH_LOGOUT_BUTTON")?>]</a><br />
<a href="<?=SITE_DIR?>personal/basket/?show=2" id="favoritesFancy"><?=GetMessage("AUTH_FAVORITES")?></a>


<?endif?>
