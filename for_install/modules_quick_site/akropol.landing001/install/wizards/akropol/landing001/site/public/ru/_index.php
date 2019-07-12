<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "#SITE_SEO_KEYWORDS#");
$APPLICATION->SetPageProperty("description", "#SITE_SEO_DESCRIPTION#");
$APPLICATION->SetTitle("#SITE_SEO_TITLE#");
$APPLICATION->SetPageProperty("telephonetech", "#TECH_PHONE#");
$APPLICATION->SetPageProperty("telephone", "#PHONE#");
$APPLICATION->SetPageProperty("Headerlogo", "#SITE_TITLE#");
$APPLICATION->SetPageProperty("Imglogo", "#LOGO_IMG_URL#");
?>

<!-- =========================
     HEADER
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_1",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
    
<!-- =========================
     FEATURES
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_2",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
    
<!-- =========================
     ABOUT
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_3",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
    
<!-- =========================
     TESTIMONIALS
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_4",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
    
<!-- =========================
     CLIENTS LOGOS
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_5",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
    
<!-- =========================
     PRICING
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_6",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
    
<!-- =========================
     TEAM
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_7",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
    
<!-- =========================
     TABS
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_8",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
    
<!-- =========================
     GALLERY
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_9",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
    
<!-- =========================
     FUN FACTS
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_10",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
    
<!-- =========================
     CONTACT
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_11",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
      
<!-- =========================
     LOCALISATION & OTHER CONTACT DATA
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_12",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
      
<!-- =========================
     FORM MODALS
============================== -->
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
		"AREA_FILE_SHOW" => "sect",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "area_13",
		"AREA_FILE_RECURSIVE" => "Y"
		)
	);?>
	
<!-- =========================
     UP 
============================== -->
	<?$APPLICATION->IncludeComponent(
	"bitrix:main.include", 
	"", 
	array(
		"AREA_FILE_SHOW" => "file",
		"EDIT_TEMPLATE" => "",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"PATH" => "include/up.php"
	),
	false
);?>
   	
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>