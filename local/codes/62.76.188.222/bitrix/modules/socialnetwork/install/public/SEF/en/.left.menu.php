<?
$aMenuLinks = Array(
	Array(
		"Activity Stream", 
		"#SEF_FOLDER#log/", 
		Array("#SEF_FOLDER#index.php"), 
		Array(), 
		"" 
	),
	Array(
		"My profile", 
		"#SEF_FOLDER#user/index.php", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"My messages", 
		"#SEF_FOLDER#messages/", 
		Array(), 
		Array(), 
		"\$GLOBALS['USER']->IsAuthorized()" 
	),
	Array(
		"Search users", 
		"#SEF_FOLDER#search/", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Search groups", 
		"#SEF_FOLDER#group/search/", 
		Array(), 
		Array(), 
		"" 
	)
);
?>