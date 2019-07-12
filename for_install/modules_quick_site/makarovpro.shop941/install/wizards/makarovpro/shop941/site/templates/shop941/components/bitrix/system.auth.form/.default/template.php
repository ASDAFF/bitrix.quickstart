<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?if($arResult["FORM_TYPE"] == "login"):?>

<?
if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
	ShowMessage($arResult['ERROR_MESSAGE']);
?>
   <nav class="login-menu group">
      <ul>
        <li><a href="<?=$arResult["AUTH_URL"]?>"><?=GetMessage("AUTH_LOGIN")?></a></li>
        <li><a href="<?=$arResult["AUTH_REGISTER_URL"]?>"><?=GetMessage("AUTH_REGISTER")?></a></li>
      </ul>
  </nav>
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

<?
//if($arResult["FORM_TYPE"] == "login")
else:
?>
    <nav class="login-menu-2 group">
      <ul>
        <li><a href="<?=$arResult["PROFILE_URL"]?>"><img src="<?= SITE_TEMPLATE_PATH ?>/images/male.png" width="16" height="16" alt=""/><?=$arResult["USER_NAME"]?></a></li>
        <li><a href="<?=$APPLICATION->GetCurPageParam("logout=yes", Array("logout"))?>"><img src="<?= SITE_TEMPLATE_PATH ?>/images/locked.png" width="16" height="16" alt="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>"/></a></li>
      </ul>
    </nav>

<?endif?>