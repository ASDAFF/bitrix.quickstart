<?
$aMenuLinks = Array(
	Array(
		"�����������", 
		SITE_DIR."auth/index.php?login=yes", 
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"�����������", 
		SITE_DIR."auth/index.php?register=yes", 
		Array(), 
		Array(), 
		"COption::GetOptionString(\"main\", \"new_user_registration\") == \"Y\"" 
	),
	Array(
		"����� ������", 
		SITE_DIR."auth/index.php?forgot_password=yes", 
		Array(), 
		Array(), 
		"" 
	)
);
?>