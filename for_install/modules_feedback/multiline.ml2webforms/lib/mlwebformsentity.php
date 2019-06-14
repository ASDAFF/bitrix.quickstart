<?php
/**
 * Multiline
 * @package    multiline
 * @subpackage mlrealestate
 * @copyright  multiline
 */

namespace Ml2WebForms;

use Bitrix\Main\Application;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);

/**
 * Configure entities for \Ml2WebForms\MlAdminPanelBuilderEntity
 * settings for \Ml2WebForms\MlAdminPanelBuilder
 * @package    multiline
 * @subpackage ml2webforms
 */
class Ml2WebFormsEntity extends MlAdminPanelBuilderEntity
{
    public static function getFormsList()
    {
        $arForms = array();
        $folders = array(
            "/local/modules",
            "/bitrix/modules",
        );
        $moduleFolder = strpos(__DIR__, "local" . DIRECTORY_SEPARATOR . "modules") !== false ? $folders[0] : $folders[1];
        $handle = @opendir($_SERVER["DOCUMENT_ROOT"] . $moduleFolder . DIRECTORY_SEPARATOR . "multiline.ml2webforms" . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "forms");
        if($handle)
        {
            while (false !== ($dir = readdir($handle)))
            {
                if(!isset($arForms[$dir]) && is_dir($_SERVER["DOCUMENT_ROOT"]. $moduleFolder . DIRECTORY_SEPARATOR . "multiline.ml2webforms" . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR . $dir) && $dir != "." && $dir != ".." && strpos($dir, ".") === false)
                {
                    $arForms[$dir]["ID"] = $dir;
                    $name = include $_SERVER["DOCUMENT_ROOT"]. $moduleFolder . DIRECTORY_SEPARATOR . "multiline.ml2webforms" . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . "name.php";
                    $arForms[$dir]["NAME"] = $name[LANGUAGE_ID];
                }
            }
            closedir($handle);
        }
        uasort($arForms, create_function('$a, $b', 'if($a["NAME"] == $b["NAME"]) return strcasecmp($a["NAME"], $b["NAME"]); return ($a["NAME"] < $b["NAME"])? -1 : 1;'));        

        return $arForms;
    }

    public static function getConfig()
    {
        return array(
        );
    }

    public static function getMenu()
    {
        $arForms = self::getFormsList();

        $items = array();

        foreach ($arForms as $id=>$arForm)
        {
            $items[] = array(
                "text" => $arForm["NAME"],
                "url" => "ml2webforms_results.php?id=$id&lang=" . LANG
            );
        }
        $menu = array(
            'module' => 'ml2webforms',
            'page' => 'ml2webforms_admin.php',
            'menu' => $items,
        );

        return $menu;
    }

    public static function OnReindex($NS = array(), $oCallback = NULL, $callback_method = "") {

    }
}