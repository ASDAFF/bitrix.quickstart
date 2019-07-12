<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!defined("WIZARD_SITE_ID"))
    return;

if (!defined("WIZARD_SITE_DIR"))
    return;

$file = fopen(WIZARD_SITE_ROOT_PATH . "/bitrix/php_interface/init.php", "a+");
if (!class_exists('INNETQuestions')) {
    fwrite($file, file_get_contents(WIZARD_ABSOLUTE_PATH . "/site/services/main/bitrix/php_interface/init.php"));
}
fclose($file);

$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH . "/site/public/" . LANGUAGE_ID . "/");

$handle = @opendir($path);
if ($handle) {
    while ($file = readdir($handle)) {
        if (in_array($file, array(".", "..")))
            continue;

        $to = ($file == 'upload' ? $_SERVER['DOCUMENT_ROOT'] . '/upload' : WIZARD_SITE_PATH . "/" . $file);

        CopyDirFiles(
            $path . $file,
            $to,
            $rewrite = true,
            $recursive = true,
            $delete_after_copy = false
        );
    }
}


COption::SetOptionString("main", "email_from", $wizard->GetVar("company_mail"));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/include/contacts/address.php", Array("COMPANY_ADDRESS" => $wizard->GetVar("company_address")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/include/contacts/phone_1.php", Array("COMPANY_PHONE" => $wizard->GetVar("company_phone")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/include/contacts/phone_2.php", Array("COMPANY_PHONE" => $wizard->GetVar("company_phone")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/include/contacts/email_1.php", Array("COMPANY_EMAIL" => $wizard->GetVar("company_mail")));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/include/contacts/email_2.php", Array("COMPANY_EMAIL" => $wizard->GetVar("company_mail")));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/_index.php", Array("COMPANY_NAME" => str_replace(array('"', "'"), '', $wizard->GetVar("company_name"))));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/articles/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/catalog/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/company/partners/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/news/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/projects/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/services/index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/_index.php", Array("SITE_DIR" => WIZARD_SITE_DIR));

CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/" . WIZARD_TEMPLATE_ID . "_" . WIZARD_THEME_ID . "/header.php", array('SITE_DIR' => WIZARD_SITE_DIR));

CWizardUtil::ReplaceMacros(WIZARD_SITE_ROOT_PATH . "/bitrix/php_interface/init.php", array('SITE_ID' => WIZARD_SITE_ID));


$arUrlRewrite = array();
if (file_exists(WIZARD_SITE_ROOT_PATH . "/urlrewrite.php")) {
    include(WIZARD_SITE_ROOT_PATH . "/urlrewrite.php");
}

$arNewUrlRewrite = array(
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "articles/#",
        "RULE" => "",
        "ID" => "bitrix:news",
        "PATH" => WIZARD_SITE_DIR . "articles/index.php",
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "catalog/#",
        "RULE" => "",
        "ID" => "bitrix:catalog",
        "PATH" => WIZARD_SITE_DIR . "catalog/index.php",
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "company/partners/#",
        "RULE" => "",
        "ID" => "bitrix:news",
        "PATH" => WIZARD_SITE_DIR . "company/partners/index.php",
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "news/#",
        "RULE" => "",
        "ID" => "bitrix:news",
        "PATH" => WIZARD_SITE_DIR . "news/index.php",
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "projects/#",
        "RULE" => "",
        "ID" => "bitrix:news",
        "PATH" => WIZARD_SITE_DIR . "projects/index.php",
    ),
    array(
        "CONDITION" => "#^" . WIZARD_SITE_DIR . "services/#",
        "RULE" => "",
        "ID" => "bitrix:news",
        "PATH" => WIZARD_SITE_DIR . "services/index.php",
    ),
);

foreach ($arNewUrlRewrite as $arUrl) {
    if (!in_array($arUrl, $arUrlRewrite)) {
        CUrlRewriter::Add($arUrl);
    }
}


CopyDirFiles(str_replace("//", "/", WIZARD_ABSOLUTE_PATH . "/site/services/main/bitrix/components/"), $_SERVER['DOCUMENT_ROOT'] . "/bitrix/components", true, true, false);
?>