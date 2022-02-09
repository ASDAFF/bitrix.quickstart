<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if (!is_array($arAuthResult))
	$arAuthResult = array("TYPE" => "ERROR", "MESSAGE" => $arAuthResult);

$arAuthResult['CAPTCHA'] = $APPLICATION->NeedCAPTHAForLogin($last_login);
if ($arAuthResult['CAPTCHA'])
{
	$arAuthResult['CAPTCHA_CODE'] = $APPLICATION->CaptchaGetCode();
}

if ($bOnHit):
?>
<script type="text/javascript">
BX.ready(function(){BX.defer(BX.adminLogin.setAuthResult, BX.adminLogin)(<?=CUtil::PhpToJsObject($arAuthResult)?>);});
</script>
<?
else:
?>
<script type="text/javascript" bxrunfirst="true">
top.BX.adminLogin.setAuthResult(<?=CUtil::PhpToJsObject($arAuthResult)?>);
</script>
<?
endif;
?>