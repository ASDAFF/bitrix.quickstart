<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$aMenuLinks = Array(
	Array(
		"Как купить", 
		SITE_DIR."about/howto/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Доставка", 
		SITE_DIR."about/delivery/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"О магазине", 
		SITE_DIR."about/", 
		Array(), 
		Array(), 
		"" 
	),	
	Array(
		"Гарантия", 
		SITE_DIR."about/guaranty/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Контакты",
		SITE_DIR."about/contacts/",
		Array(),
		Array(),
		""
	),
	Array(
		"Мой кабинет",
		SITE_DIR."personal/",
		Array(),
		Array(),
		"CUser::IsAuthorized()"
	),
);
?>