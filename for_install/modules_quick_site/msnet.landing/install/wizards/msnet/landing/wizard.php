<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/install/wizard_sol/wizard.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/wizard.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/install/wizard_sol/utils.php");

class SelectSiteStep extends CSelectSiteWizardStep
{
    function InitStep()
    {
        parent::InitStep();

        $wizard =& $this->GetWizard();
        $wizard->solutionName = "landing";
        $this->SetNextStep('data_install');
    }
}


class DataInstallStep extends CDataInstallWizardStep
{
    function CorrectServices(&$arServices)
    {
        $wizard =& $this->GetWizard();
        $wizard->SetVar('templateID', 'msnet');
        $wizard->SetVar($wizard->GetVar('templateID').'_themeID', 'dark');
        copyComponents();
        importIblocks($wizard->GetVar("siteID"));
        replaceMacros();
        if ($wizard->GetVar("installDemoData") != "Y") {
        }
    }
}

class FinishStep extends CFinishWizardStep
{
}


CModule::IncludeModule('iblock');

function createIblockTypes()
{
    global $DB;
    $arFields =
        [
            'ID' => 'msnet_content',
            'LANG' =>
                [
                    'ru' => ['NAME' => GetMessage('IB_CATALOG_NAME')],
                    'en' => ['NAME' => 'Content'],
                ]
        ];

    $obBlocktype = new CIBlockType;
    $DB->StartTransaction();
    $res = $obBlocktype->Add($arFields);
    if (!$res) {
        $DB->Rollback();
        echo 'Error: ' . $obBlocktype->LAST_ERROR . '<br>';
    } else
        $DB->Commit();

}

function importIblocks($siteID)
{
    createIblockTypes();

    $rootDir = __DIR__ . '/site/services/iblock/xml/ru/';
    $list =
        [
            [
                'FILE' => 'news.xml',
                'IB_CODE' => 'msnet_news',
                'IB_TYPE' => 'msnet_content',
                'SITE_ID' => $siteID,
            ],
            [
                'FILE' => 'videos.xml',
                'IB_CODE' => 'msnet_videos',
                'IB_TYPE' => 'msnet_content',
                'SITE_ID' => $siteID,
            ],
            [
                'FILE' => 'conserts.xml',
                'IB_CODE' => 'msnet_concerts',
                'IB_TYPE' => 'msnet_content',
                'SITE_ID' => $siteID,
            ],
        ];

    foreach ($list as $item)
        WizardServices::ImportIBlockFromXML(
            $rootDir . $item['FILE'],
            $item['IB_CODE'],
            $item['IB_TYPE'],
            $item['SITE_ID']
        );
}

function copyComponents()
{
    $from = __DIR__ . '/site/components/msnet/';
    $to = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/msnet/';
    CopyDirFiles($from, $to, true, true, false);
}

function replaceMacros()
{
    $sitePath = __DIR__ . '/site/';

    $ibNews = CIBlock::GetList([], ['CODE' => 'msnet_news'])->fetch();
    if ($ibNews['ID']) {
        CWizardUtil::ReplaceMacros(
            $sitePath . "/public/ru/_index.php", ["NEWS_IBLOCK_ID" => $ibNews['ID']]);
    }

    $ibNews = CIBlock::GetList([], ['CODE' => 'msnet_videos'])->fetch();
    if ($ibNews['ID']) {
        CWizardUtil::ReplaceMacros(
            $sitePath . "/public/ru/_index.php", ["VIDEO_IBLOCK_ID" => $ibNews['ID']]);
    }

    $ibNews = CIBlock::GetList([], ['CODE' => 'msnet_concerts'])->fetch();
    if ($ibNews['ID']) {
        CWizardUtil::ReplaceMacros(
            $sitePath . "/public/ru/_index.php", ["CONCERT_IBLOCK_ID" => $ibNews['ID']]);
    }

}