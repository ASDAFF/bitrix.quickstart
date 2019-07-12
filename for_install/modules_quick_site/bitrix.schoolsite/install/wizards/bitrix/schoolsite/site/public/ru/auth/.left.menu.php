<?
$aMenuLinks = Array(
	Array(
		"Авторизация", 
		SITE_DIR."auth/index.php?login=yes", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Регистрация", 
		SITE_DIR."auth/index.php?register=yes", 
		Array(), 
		Array(), 
		"COption::GetOptionString(\"main\", \"new_user_registration\") == \"Y\"" 
	),
	Array(
		"Забыл пароль", 
		SITE_DIR."auth/index.php?forgot_password=yes", 
		Array(), 
		Array(), 
		"" 
	)
);
?>