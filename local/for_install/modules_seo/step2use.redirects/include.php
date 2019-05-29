<?
global $DB, $APPLICATION, $MESS, $DBType;
CModule::AddAutoloadClasses(
	"step2use.redirects",
	array(
		"S2uRedirectsRulesDB" => "classes/$DBType/s2u_redirects_rules.php",
        "S2uRedirects404DB" => "classes/$DBType/s2u_redirects_404.php",
        'S2uRedirects' => "classes/general/s2u_redirects.php",
        "S2uRedirects404IgnoreDB" => "classes/$DBType/s2u_redirects_404_ignore.php",
        'phpQuery' => "classes/general/phpQuery/phpQuery/phpQuery.php",
    )
);

?>