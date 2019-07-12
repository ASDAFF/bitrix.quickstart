<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();
    
if (!defined("WIZARD_SITE_ID"))
    return;

if (!defined("WIZARD_SITE_DIR"))
    return;
 
if (WIZARD_INSTALL_DEMO_DATA)
{

    CopyDirFiles(
        WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/",
        WIZARD_SITE_PATH,
        $rewrite = true, 
        $recursive = true,
        $delete_after_copy = false,
        $exclude = "bitrix"
    );

    CopyDirFiles(
        WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/bitrix/",
        $_SERVER['DOCUMENT_ROOT']."/bitrix/",
        $rewrite = false, 
        $recursive = true,
        $delete_after_copy = false
    );
    
    CopyDirFiles(
        WIZARD_ABSOLUTE_PATH."/site/indexes/".LANGUAGE_ID."/".WIZARD_TEMPLATE_ID."/",
        WIZARD_SITE_PATH,
        $rewrite = true, 
        $recursive = true,
        $delete_after_copy = false
    );

       
    $arUrlRewrite = array(); 

    if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
    {
        include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
    }

    $arNewUrlRewrite = array(
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."admission/apply_for_admission/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:iblock.element.add.form",
            "PATH"  =>  WIZARD_SITE_DIR."admission/apply_for_admission/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."teachers/teaching_materials/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:catalog",
            "PATH"  =>  WIZARD_SITE_DIR."parents/documents/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."students/teaching_materials/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:catalog",
            "PATH"  =>  WIZARD_SITE_DIR."students/teaching_materials/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."teachers/teaching_materials/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:catalog",
            "PATH"  =>  WIZARD_SITE_DIR."teachers/teaching_materials/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."parents/teaching_materials/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:catalog",
            "PATH"  =>  WIZARD_SITE_DIR."parents/teaching_materials/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."about/mediagallery/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:gallery",
            "PATH"  =>  WIZARD_SITE_DIR."about/mediagallery/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."students/question/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:iblock.element.add.form",
            "PATH"  =>  WIZARD_SITE_DIR."students/question/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."parents/question/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:iblock.element.add.form",
            "PATH"  =>  WIZARD_SITE_DIR."parents/question/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."about/subjects/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:catalog",
            "PATH"  =>  WIZARD_SITE_DIR."about/subjects/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."about/audience/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:catalog",
            "PATH"  =>  WIZARD_SITE_DIR."about/audience/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."about/events/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:news",
            "PATH"  =>  WIZARD_SITE_DIR."about/events/index.php",
        ),
        array(
            "CONDITION" =>  "#^".WIZARD_SITE_DIR."about/news/#",
            "RULE"  =>  "",
            "ID"    =>  "bitrix:news",
            "PATH"  =>  WIZARD_SITE_DIR."about/news/index.php",
        ),            
    ); 
        
    foreach ($arNewUrlRewrite as $arUrl)
    {
        if (!in_array($arUrl, $arUrlRewrite))
        {
            CUrlRewriter::Add($arUrl);
        }
    }
}


WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));

if($wizard->GetVar("siteName")) {
    $site_name = $wizard->GetVar("siteName")."<br>";
} else {
    $site_name = "";
}

if($wizard->GetVar("schoolAddress")) {
    $school_address = $wizard->GetVar("schoolAddress")."<br>";
} else {
    $school_address = "";
}

if($wizard->GetVar("schoolPhone")) {
    $school_phone = $wizard->GetVar("schoolPhone")."<br>";
} else {
    $school_phone = "";
}

if($wizard->GetVar("schoolEmail")) {
    $school_email = '<a href="mailto:'.$wizard->GetVar("schoolEmail").'">'.$wizard->GetVar("schoolEmail").'</a>';
    $school_email_top = $wizard->GetVar("schoolEmail");
} else {
    $school_email = "";
}

CWizardUtil::ReplaceMacros(
    WIZARD_SITE_PATH.'/about/contacts/index.php',
    array(
        "SCHOOL_NAME" => $site_name,
        "SCHOOL_ADDRESS" => $school_address,
        "SCHOOL_PHONE" => $school_phone,
        "SCHOOL_EMAIL" => $school_email,
    )
);
CWizardUtil::ReplaceMacros(
    WIZARD_SITE_PATH.'/include_areas/schooladdress.php',
    array(
        "SCHOOL_ADDRESS" => $school_address,
        "SCHOOL_PHONE" => $school_phone,
    )
);
CWizardUtil::ReplaceMacros(
    WIZARD_SITE_PATH.'/include_areas/schooltop_links.php',
    array(
        "SCHOOL_EMAIL" => $school_email_top,
    )
);
CWizardUtil::ReplaceMacros(
    WIZARD_SITE_PATH.'/include_areas/school_name.php',
    array(
        "SCHOOL_NAME" => $site_name,
    )
);
CWizardUtil::ReplaceMacros(
    WIZARD_SITE_PATH.'/include_areas/footer_name.php',
    array(
        "SCHOOL_NAME" => $site_name,
    )
);

if (WIZARD_INSTALL_DEMO_DATA)
{ 
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_DESCRIPTION" => $wizard->GetVar("siteMetaDescription")));
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.section.php", array("SITE_KEYWORDS" => $wizard->GetVar("siteMetaKeywords")));
}
?>
