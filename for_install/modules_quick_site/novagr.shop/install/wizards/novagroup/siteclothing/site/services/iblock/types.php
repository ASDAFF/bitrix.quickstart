<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	
	if(!CModule::IncludeModule("iblock")) return;
	
	if(COption::GetOptionString("siteclothing", "wizard_installed", "N", WIZARD_SITE_ID) == "Y") return;
	
	$arTypes = Array(
		Array(
			"ID"		=> "articles",
			"SECTIONS"	=> "Y",
			"IN_RSS"	=> "N",
			"SORT"		=> 100,
			"LANG"		=> array(),
		),
		Array(
			"ID"		=> "banners",
			"SECTIONS"	=> "Y",
			"IN_RSS"	=> "N",
			"SORT"		=> 100,
			"LANG"		=> array(),
		),
		Array(
			"ID"		=> "catalog",
			"SECTIONS"	=> "Y",
			"IN_RSS"	=> "N",
			"SORT"		=> 100,
			"LANG"		=> array(),
		),
		Array(
			"ID"		=> "news",
			"SECTIONS"	=> "Y",
			"IN_RSS"	=> "N",
			"SORT"		=> 200,
			"LANG"		=> Array(),
		),
		Array(
			"ID"		=> "offers",
			"SECTIONS"	=> "Y",
			"IN_RSS"	=> "N",
			"SORT"		=> 200,
			"LANG"		=> Array(),
		),
/*		Array(
			"ID"		=> "products_photos",
			"SECTIONS"	=> "Y",
			"IN_RSS"	=> "N",
			"SORT"		=> 200,
			"LANG"		=> Array(),
		),*/
		Array(
			"ID"		=> "references",
			"SECTIONS"	=> "Y",
			"IN_RSS"	=> "N",
			"SORT"		=> 200,
			"LANG"		=> Array(),
		),
        Array(
            "ID"		=> "comments",
            "SECTIONS"	=> "Y",
            "IN_RSS"	=> "N",
            "SORT"		=> 200,
            "LANG"		=> Array(),
        ),
        /*Array(
            "ID"		=> "seo",
            "SECTIONS"	=> "Y",
            "IN_RSS"	=> "N",
            "SORT"		=> 200,
            "LANG"		=> Array(),
        ),*/
		Array(
			"ID"		=> "services",
			"SECTIONS"	=> "Y",
			"IN_RSS"	=> "N",
			"SORT"		=> 200,
			"LANG"		=> Array(),
		),
        Array(
            "ID"		=> "quickbuy",
            "SECTIONS"	=> "Y",
            "IN_RSS"	=> "N",
            "SORT"		=> 500,
            "LANG"		=> array(),
        ),
        Array(
            "ID"		=> "LandingPages",
            "SECTIONS"	=> "Y",
            "IN_RSS"	=> "N",
            "SORT"		=> 500,
            "LANG"		=> array(),
        ),
		Array(
			"ID"		=> "system",
			"SECTIONS"	=> "Y",
			"IN_RSS"	=> "N",
			"SORT"		=> 200,
			"LANG"		=> Array(),
		)
	);
	
	$arLanguages = Array();
	$rsLanguage = CLanguage::GetList($by, $order, array());
	while($arLanguage = $rsLanguage -> Fetch())
		$arLanguages[] = $arLanguage["LID"];
	
	$iblockType = new CIBlockType;
	
	foreach($arTypes as $arType)
	{
		$dbType = CIBlockType::GetList(array(), array("=ID" => $arType["ID"]));
		if( $dbType -> Fetch() )
			continue;
		
		foreach($arLanguages as $languageID)
		{
			WizardServices::IncludeServiceLang("type.php", $languageID);
			
			$code = strtoupper($arType["ID"]);
			$arType["LANG"][$languageID]["NAME"] = GetMessage($code."_TYPE_NAME");
			$arType["LANG"][$languageID]["ELEMENT_NAME"] = GetMessage($code."_ELEMENT_NAME");
	
			if ($arType["SECTIONS"] == "Y")
				$arType["LANG"][$languageID]["SECTION_NAME"] = GetMessage($code."_SECTION_NAME");
		}
	
		$res = $iblockType -> Add($arType);
		
		//if( !empty($res -> LAST_ERROR) )
		//{
		//	echo $res -> LAST_ERROR;
		//	exit;
		//}
	}
	
	COption::SetOptionString('iblock','combined_list_mode','Y');
?>