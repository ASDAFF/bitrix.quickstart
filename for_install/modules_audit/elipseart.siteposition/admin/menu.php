<?
IncludeModuleLangFile(__FILE__);

if(
	CModule::IncludeModule("elipseart.siteposition")
	&& $APPLICATION->GetGroupRight("elipseart.siteposition") != "D"
)
{
	$aMenu = Array(
		array(
			"parent_menu" => "global_menu_statistics",
			"sort" => 1300,
			"text" => GetMessage("SITE_POSITION"),
			"title"=> GetMessage("SITE_POSITION_ALT"),
			"url" => "elipseart.siteposition.position.php?lang=".LANGUAGE_ID."&set_default=Y",
			"icon" => "elipseart_siteposition_icon_mnu_position",
			"page_icon" => "elipseart_siteposition_icon_page_position",
			"items_id" => "elipseart_sp0",
			"items" => array(
				array(
					
					"text" => GetMessage("SITE_POSITION_ALL"),
					"title"=> GetMessage("SITE_POSITION_ALL_ALT"),
					"url" => "elipseart.siteposition.position.php?lang=".LANGUAGE_ID."&set_default=Y",
					"more_url" => array(
						"elipseart.siteposition.position.php",
						"elipseart.siteposition.position.php?lang=".LANGUAGE_ID,
					),
					"items_id" => "elipseart_sp1",
					"items" => array(
						array(
							"text" => GetMessage("SITE_POSITION_STAT"),
							"title"=> GetMessage("SITE_POSITION_STAT_ALT"),
							"url" => "elipseart.siteposition.stat.php?lang=".LANGUAGE_ID."&set_default=Y",
							"more_url" => array(
								"elipseart.siteposition.stat.php",
								"elipseart.siteposition.stat.php?lang=".LANGUAGE_ID,
							),
							"items_id" => "elipseart_sp1_1",
						),
					),
				),
				/*
				array(
					"text" => GetMessage("SITE_POSITION_DOMAIN"),
					"title"=> GetMessage("SITE_POSITION_DOMAIN_ALT"),
					"url" => "elipseart.siteposition.domain.php?lang=".LANGUAGE_ID,
					"more_url" => "elipseart.siteposition.domain.php?lang=".LANGUAGE_ID,
					"items_id" => "elipseart_sp2",
				),
				*/
				array(
					"text" => GetMessage("SITE_POSITION_KEYWORD"),
					"title"=> GetMessage("SITE_POSITION_KEYWORD_ALT"),
					"url" => "elipseart.siteposition.keyword.php?lang=".LANGUAGE_ID."&set_default=Y",
					"more_url" => array(
						"elipseart.siteposition.keyword.php",
						"elipseart.siteposition.keyword.php?lang=".LANGUAGE_ID,
						"elipseart.siteposition.keyword_edit.php",
						"elipseart.siteposition.keyword_edit.php?lang=".LANGUAGE_ID,
					),
					"items_id" => "elipseart_sp3",
				),
			),
		),
	);
	return $aMenu;
}

return false;
?>
