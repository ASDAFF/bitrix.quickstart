<?
$aMenuLinks = Array(
	Array(
		"Activity Stream", 
		"#SEF_FOLDER#index.php?page=log", 
		Array("#SEF_FOLDER#index.php?page=index"), 
		Array(), 
		"" 
	),
	Array(
		"My profile", 
		"#SEF_FOLDER#index.php?page=user_current",
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"My messages", 
		"#SEF_FOLDER#index.php?page=messages_users", 
		Array(), 
		Array(), 
		"\$GLOBALS['USER']->IsAuthorized()" 
	),
	Array(
		"Search users", 
		"#SEF_FOLDER#index.php?page=search", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Search groups", 
		"#SEF_FOLDER#index.php?page=group_search", 
		Array(), 
		Array(), 
		"" 
	)
);
?>