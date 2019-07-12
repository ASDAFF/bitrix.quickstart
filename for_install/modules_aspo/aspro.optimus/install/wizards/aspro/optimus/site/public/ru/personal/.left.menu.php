<?
$aMenuLinks = Array(
	Array(
		"Мой кабинет", 
		"#SITE_DIR#personal/index.php", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Текущие заказы", 
		"#SITE_DIR#personal/orders/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Личный счет", 
		"#SITE_DIR#personal/account/", 
		Array(), 
		Array(), 
		"CBXFeatures::IsFeatureEnabled('SaleAccounts')" 
	),
	Array(
		"Личные данные", 
		"#SITE_DIR#personal/private/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Сменить пароль", 
		"#SITE_DIR#personal/change-password/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"История заказов", 
		"#SITE_DIR#personal/orders/?filter_history=Y", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Профили заказов", 
		"#SITE_DIR#personal/profiles/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Корзина", 
		"#SITE_DIR#basket/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Подписки", 
		"#SITE_DIR#personal/subscribe/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Контакты", 
		"#SITE_DIR#contacts/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Выйти", 
		"?logout=yes&login=yes", 
		Array(), 
		Array("class"=>"exit"), 
		"\$GLOBALS[\"USER\"]->isAuthorized()" 
	)
);
?>