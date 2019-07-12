<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!defined("WIZARD_SITE_ID"))
	return;

if (!defined("WIZARD_SITE_DIR"))
	return;

$copyPublicFiles = function($fromPath)
{
	$handle = @opendir($fromPath);
	if ($handle)
	{
		while ($file = readdir($handle))
		{
			if (in_array($file, array(".", "..")))
				continue;
	
			CopyDirFiles(
				$fromPath.$file,
				str_replace('//', '/', WIZARD_SITE_PATH."/".$file),
				$rewrite = true,
				$recursive = true,
				$delete_after_copy = false
			);
		}
	} else {
		echo "Error installing public files.";
	}
};
//Копирование публиных файлов
$sitePublicPath = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/");
$copyPublicFiles($sitePublicPath);

$arPhone = array();
$str = htmlspecialchars($wizard->GetVar("siteTelephone"));
$arPhone = explode(")",$str);

$arContacts = Array(
	"SITE_DIR" => WIZARD_SITE_DIR,
	"SITE_NAME" => htmlspecialcharsbx($wizard->GetVar("siteName")),
	"SITE_EMAIL" => htmlspecialcharsbx($wizard->GetVar("siteEMail")),
	"SITE_PHONE" => htmlspecialcharsbx($wizard->GetVar("siteTelephone")),
	"SITE_ADDRESS" => htmlspecialcharsbx($wizard->GetVar("siteAddress")),
	"SITE_PHONE_CODE" => $arPhone[0],
	"SITE_PHONE_NUMBER" => $arPhone[1],
);
$siteNameHtml = preg_replace('#^([^\s]+?)\s(.*)$#', '<b>$1</b> $2', htmlspecialcharsbx($wizard->GetVar("siteName")));
$arContacts["SITE_NAME_HTML"] = $siteNameHtml;

WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH, $arContacts);

$arUrlRewrite = array();
if (file_exists(WIZARD_SITE_ROOT_PATH."/urlrewrite.php"))
{
	include(WIZARD_SITE_ROOT_PATH."/urlrewrite.php");
}

$arNewUrlRewrite = array(
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."company/testimonials/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."company/testimonials/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."company/vacancies/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."company/vacancies/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."info/articles/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."info/articles/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."company/news/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."company/news/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."info/faq/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."info/faq/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."services/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => WIZARD_SITE_DIR."services/index.php",
	),
	array(
		"CONDITION" => "#^".WIZARD_SITE_DIR."offers/#",
		"RULE" => "",
		"ID" => "citrus:realty.catalog",
		"PATH" => WIZARD_SITE_DIR."offers/index.php",
	),
);

foreach ($arNewUrlRewrite as $arUrl)
	if (!in_array($arUrl, $arUrlRewrite))
		CUrlRewriter::Add($arUrl);



