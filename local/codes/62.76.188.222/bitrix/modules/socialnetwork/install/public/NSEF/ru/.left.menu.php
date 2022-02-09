<?
$aMenuLinks = Array(
	Array(
		"Живая лента", 
		"#SEF_FOLDER#index.php?page=log", 
		Array("#SEF_FOLDER#index.php?page=index"), 
		Array(), 
		"" 
	),
	Array(
		"Личная страница", 
		"#SEF_FOLDER#index.php?page=user_current", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Мои сообщения", 
		"#SEF_FOLDER#index.php?page=messages_users", 
		Array(), 
		Array(), 
		"\$GLOBALS['USER']->IsAuthorized()" 
	),
	Array(
		"Найти людей", 
		"#SEF_FOLDER#index.php?page=search", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Найти группу", 
		"#SEF_FOLDER#index.php?page=group_search", 
		Array(), 
		Array(), 
		"" 
	)
);
?>