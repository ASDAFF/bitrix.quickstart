<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

global $DB, $APPLICATION, $MESS, $DBType;

CModule::AddAutoloadClasses(
	"asdaff.redirects",
	array(
		"seo2RedirectsRulesDB" => "classes/$DBType/seo2_redirects_rules.php",
        "seo2Redirects404DB" => "classes/$DBType/seo2_redirects_404.php",
        'seo2Redirects' => "classes/general/seo2_redirects.php",
        "seo2Redirects404IgnoreDB" => "classes/$DBType/seo2_redirects_404_ignore.php",
        'phpQuery' => "classes/general/phpQuery/phpQuery/phpQuery.php",
    )
);

?>