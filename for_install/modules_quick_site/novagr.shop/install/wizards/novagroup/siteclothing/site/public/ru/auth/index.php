<?
//define("NEED_AUTH", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// если пользователь попал на эту страницу с другой - заносим ее в сессию для последующего возврата-редиректа
if ($_REQUEST["wrong_pass"] == 1 && !CUser::IsAuthorized()) {


	if (!empty($_SERVER["HTTP_REFERER"]) && empty($_SESSION["REFFERER_FOR_AUTH"]) ) {

		$_SESSION["REFFERER_FOR_AUTH"] = $_SERVER["HTTP_REFERER"];

	}

}

//if (isset($_REQUEST["backurl"]) && strlen($_REQUEST["backurl"])>0) 
//	LocalRedirect($backurl);



if (!empty($_REQUEST["USER_CHECKWORD"])) {
	$APPLICATION->SetTitle("Восстановление пароля");
	$APPLICATION->IncludeComponent("bitrix:system.auth.changepasswd", "", array(
			"AUTH_RESULT" => "",
			"NOT_SHOW_LINKS" => "N",
	));
} else {
	$APPLICATION->SetTitle("Авторизация");
	$APPLICATION->IncludeComponent("bitrix:system.auth.form", "", Array(
		"REGISTER_URL" => "#SITE_DIR#auth/?register=yes",	// Страница регистрации
		"FORGOT_PASSWORD_URL" => "#SITE_DIR#auth/?forgot_password=yes",	// Страница забытого пароля
		"PROFILE_URL" => USER_PROFILE_URL,	// Страница профиля
		"SHOW_ERRORS" => "Y",	// Показывать ошибки
), false);
}
//deb($_SERVER["HTTP_REFERER"]);
//deb($_SESSION["REFFERER_FOR_AUTH"]);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>