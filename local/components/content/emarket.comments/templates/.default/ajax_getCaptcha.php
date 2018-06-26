<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

error_reporting(0);
header('Content-Type: text/html; charset=utf-8');

if(
	isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
) {

	include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php"); 
	$cpt = new CCaptcha(); 
	$captchaPass = COption::GetOptionString("main", "captcha_password", ""); 
	if(strlen($captchaPass) <= 0) 
	{ 
		$captchaPass = randString(10); 
		COption::SetOptionString("main", "captcha_password", $captchaPass); 
	} 
	$cpt->SetCodeCrypt($captchaPass);
	?>
	<a href="#" id="ec_reload_captcha"></a>
	<input type="hidden" id="captcha_code" name="captcha_code" value="<?=htmlspecialchars($cpt->GetCodeCrypt());?>">
	<input type="text" class="ec-input-param" id="captcha_word" name="captcha_word">
	<img src="/bitrix/tools/captcha.php?captcha_code=<?=htmlspecialchars($cpt->GetCodeCrypt());?>">
	<?
}
?>