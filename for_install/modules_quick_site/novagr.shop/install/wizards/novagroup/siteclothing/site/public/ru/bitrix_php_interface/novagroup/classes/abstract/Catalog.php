<?php

/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 13.07.13
 * Time: 19:49
 * To change this template use File | Settings | File Templates.
 */
abstract class Novagroup_Classes_Abstract_Catalog extends Novagroup_Classes_Abstract_IBlock
{

    protected $catalogPath = "#SITE_DIR#catalog/", $catalogName = "Каталог", $catalogIBlockCode = "products";

    function getCatalogPath()
    {
        return str_replace('#SITE_DIR#', SITE_DIR, $this->catalogPath);
    }

    function getCatalogName()
    {
        return $this->catalogName;
    }

    function getCatalogIBlockCode()
    {
        return $this->catalogIBlockCode;
    }

    function setPageProperties()
    {
        Novagroup_Classes_General_Main::setTitle($this->getCatalogName());
    }

    function addChainItems()
    {
        Novagroup_Classes_General_Main::AddChainItem($this->getCatalogName(), $this->getCatalogPath());
    }

    function checkInstalledModule()
    {
        parent::checkInstalledModule();
        if (!CModule::IncludeModule("catalog")) die("catalog module is not installed");
        if (!CModule::IncludeModule("sale")) die("sale module is not installed");
    }

    static function showIndexCatalog()
    {
        global $APPLICATION;
        include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . "include/catalog/inc.products.php");
        if (!empty($_REQUEST["q"])) {
            $APPLICATION->AddChainItem("Поиск");
        }
    }

    static function showSectionsCatalog($show = true)
    {
        global $APPLICATION, $USER;
        $settings = CUserOptions::GetOption('admin_panel', 'settings');

        $lm = new CMenu("left");
        $lm->Init($APPLICATION->GetCurDir(), true);
        foreach ($lm->arMenu as $menu) {
            if ($menu[3]['DEPTH_LEVEL'] == '2') {
                if ($menu[3]['SECTION']['CODE'] == $_REQUEST['secid'] and strlen($menu[3]['SECTION']['CODE']) > 0) {
                    if (($settings['edit']=='on' and $_GET['bitrix_include_areas']<>"N") or ($USER->IsAuthorized() and $_GET['bitrix_include_areas']=="Y") or is_file($sectionfile = getenv("DOCUMENT_ROOT") . SITE_DIR . "catalog/sect_" . $menu[3]['SECTION']['CODE'] . ".php")) {
                        if ($show === true) {
                            $APPLICATION->IncludeComponent("bitrix:main.include", "", Array(
                                    "AREA_FILE_SHOW" => "sect",
                                    "AREA_FILE_SUFFIX" => $menu[3]['SECTION']['CODE'],
                                    "AREA_FILE_RECURSIVE" => "N",
                                    "EDIT_TEMPLATE" => "products.php"
                                )
                            );
                        }
                        return true;
                    }
                }
            }
        }
        return false;
    }

    static function showCatalog()
    {
        if (self::showSectionsCatalog() === false) {
            self::showIndexCatalog();
        }
    }

    static function showFilter()
    {
        global $APPLICATION;
        if (self::showSectionsCatalog(false) === false) {
            if (strpos($APPLICATION->GetCurPage(), SITE_DIR . 'imageries') === 0) {
                $APPLICATION->IncludeFile(SITE_DIR . "include/filter/fashion.php");
            } else {
                $APPLICATION->IncludeFile(SITE_DIR . "include/filter/catalog.php");
            }
        }
    }
}