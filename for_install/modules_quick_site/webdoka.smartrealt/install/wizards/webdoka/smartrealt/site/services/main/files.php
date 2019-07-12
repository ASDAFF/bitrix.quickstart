<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();

if (!defined("WIZARD_SITE_ID"))
    return;

if (!defined("WIZARD_SITE_DIR"))
    return;

//������� ������ ������
unset($_SESSION['SMARTREALT_WIZARD']);

// ��������� ������ smartrealt
$moduleId = 'webdoka.smartrealt';
global $DB;
if (!CModule::IncludeModule($moduleId))
{
    if ($Module = CModule::CreateModuleObject($moduleId))
    {
        if (strtolower($DB->type)=="mysql" && defined("MYSQL_TABLE_TYPE") && strlen(MYSQL_TABLE_TYPE)>0)
        {
            $DB->Query("SET table_type = '".MYSQL_TABLE_TYPE."'", true);
        }

        //OnModuleInstalledEvent('webdoka.smartrealt');
        $Module->DoInstallAuto();
    }
}

// ��������� ������������� �����
$wizard =& $this->GetWizard();
$licenseKey = $wizard->GetVar('license_key');
COption::SetOptionString($moduleId, "TOKEN", $licenseKey);

// ��������� � ���������� �������� ����� ��� ���
COption::SetOptionString($moduleId, "SEF_FOLDER", WIZARD_SITE_DIR);

// �������� ����� ��������� �����
$install_news = $wizard->GetVar('install_news');
$install_pages = $wizard->GetVar('install_pages');

$path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/");

$handle = @opendir($path);
if ($handle)
{
    $arExcludeFiles = array(); //����� � �������� ������� ������� ���������

    if ($install_news == 'Y')
    {
        if (file_exists(WIZARD_SITE_PATH."/news/") && is_dir(WIZARD_SITE_PATH."/news/"))
        {
            $arExcludeFiles[] = 'news';
            // � ���� ������ ����� � �������� �� ����� ���������
            $wizard->SetVar('install_news', 'N');
        }
    }
    else
    {
        $arExcludeFiles[] = 'news';
    }
    if ($install_pages != 'Y')
    {
        $arExcludeFiles[] = 'about';
        $arExcludeFiles[] = 'contacts';
        $arExcludeFiles[] = 'services';
    }

    while ($file = readdir($handle))
    {
        if (in_array($file, array(".", "..")))
            continue;
        if (in_array($file, $arExcludeFiles))
            continue;
        CopyDirFiles(
            $path.$file,
            WIZARD_SITE_PATH."/".$file,
            $rewrite = true,
            $recursive = true,
            $delete_after_copy = false
        );
    }
    
    if (CModule::IncludeModule("search"))
    {
        CSearch::ReIndexAll(false, 0, Array(WIZARD_SITE_ID, WIZARD_SITE_DIR));
    }
}

WizardServices::PatchHtaccess(WIZARD_SITE_PATH);

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("SITE_DIR" => WIZARD_SITE_DIR));


// ��������� urlrewrite
$arUrlRewrite = array();
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
    include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}
$arNewUrlRewrite = array();
$install_news = $wizard->GetVar('install_news');
if ($install_news == 'Y')
{
    $arNewUrlRewrite[] = array(
        "CONDITION"    =>    "#^".WIZARD_SITE_DIR."news/#",
        "RULE"    =>    "",
        "ID"    =>    "bitrix:news",
        "PATH"    =>    WIZARD_SITE_DIR."news/index.php",
    );
}
$arNewUrlRewrite[] =
    array(
        "CONDITION"    =>    "#^".WIZARD_SITE_DIR."#",
        "RULE"    =>    "",
        "ID"    =>    "smartrealt:catalog",
        "PATH"    =>    WIZARD_SITE_DIR."catalog/index.php",
    );

foreach ($arNewUrlRewrite as $arUrl)
{
    if (!in_array($arUrl, $arUrlRewrite))
    {
        CUrlRewriter::Add($arUrl);
    }
}     
?>