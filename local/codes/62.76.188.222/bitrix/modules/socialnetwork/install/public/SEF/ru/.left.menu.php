<?
$aMenuLinks = Array(
	Array(
		"Живая лента", 
		"#SEF_FOLDER#log/", 
		Array("#SEF_FOLDER#index.php"), 
		Array(), 
		"" 
	),
	Array(
		"Личная страница", 
		"#SEF_FOLDER#user/index.php",
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Мои сообщения", 
		"#SEF_FOLDER#messages/", 
		Array(), 
		Array(), 
		"\$GLOBALS['USER']->IsAuthorized()" 
	),
	Array(
		"Найти людей", 
		"#SEF_FOLDER#search/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Найти группу", 
		"#SEF_FOLDER#group/search/", 
		Array(), 
		Array(), 
		"" 
	)
);
?>