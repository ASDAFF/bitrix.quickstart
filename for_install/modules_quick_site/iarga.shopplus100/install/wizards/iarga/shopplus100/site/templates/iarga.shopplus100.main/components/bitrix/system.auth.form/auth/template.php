<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["FORM_TYPE"] == "login"):?>
	<p class="link-login"><a href="<?=SITE_DIR?>auth/"><?=GetMessage('AUTH_LOGIN_BUTTON')?></a></p>

<?
//if($arResult["FORM_TYPE"] == "login")
else:
?>

	<a href='<?=SITE_DIR?>auth/'><?=$arResult["USER_NAME"]?></a>
				
<?endif?>