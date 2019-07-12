<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация");
?> <?if(!$USER->GetID()){?> <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", ".default", array(
	"REGISTER_URL" => "/auth/registration.php",
	"PROFILE_URL" => "/personal/",
	"SHOW_ERRORS" => "Y"
	),
	false
);?> <?}else{?> 
<p class="notetext">Вы зарегистрированы и успешно авторизовались.</p>
 
<p><a href="/" >Вернуться на главную страницу</a></p>
 <?}?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>