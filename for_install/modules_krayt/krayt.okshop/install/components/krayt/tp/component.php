<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client.php");
/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CDatabase $DB
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponent $this
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$this->setFrameMode(false);

//
$listModul = array();
$arModules = array();
$folder_m = "/bitrix/modules/";
$arModulList = array();
$folders = array(

);

//список модулей от компании krayt
$rsInstalledModules = CModule::GetList();
while ($ar = $rsInstalledModules->Fetch())
{
    if(strpos($ar['ID'],'krayt') !== false)
    {
        $listModul[] = $ar;
        $arModulList[]= $ar['ID'];
    }
}
foreach($listModul as $k=>&$m)
{

    $folders[$m['ID']] = $folder_m.$m['ID'];

}

foreach($folders as $dir=>$folder)
{

    if(file_exists($_SERVER["DOCUMENT_ROOT"].$folder))
    {
        $handle = opendir($_SERVER["DOCUMENT_ROOT"].$folder);
        if($handle)
        {

                if(!isset($arModules[$dir]))
                {
                    $module_dir = $folder;
                    if($info = CModule::CreateModuleObject($dir))
                    {

                        $arModules[$dir]["MODULE_ID"] = $info->MODULE_ID;
                        $arModules[$dir]["MODULE_NAME"] = $info->MODULE_NAME;
                        $arModules[$dir]["MODULE_DESCRIPTION"] = $info->MODULE_DESCRIPTION;
                        $arModules[$dir]["MODULE_VERSION"] = $info->MODULE_VERSION;
                        $arModules[$dir]["MODULE_VERSION_DATE"] = $info->MODULE_VERSION_DATE;
                        $arModules[$dir]["MODULE_SORT"] = $info->MODULE_SORT;
                        $arModules[$dir]["MODULE_PARTNER"] = $info->PARTNER_NAME;
                        $arModules[$dir]["MODULE_PARTNER_URI"] = $info->PARTNER_URI;
                        $arModules[$dir]["IsInstalled"] = $info->IsInstalled();
                        if(defined(str_replace(".", "_", $info->MODULE_ID)."_DEMO"))
                        {
                            $arModules[$dir]["DEMO"] = "Y";
                            if($info->IsInstalled())
                            {
                                if(CModule::IncludeModuleEx($info->MODULE_ID) != MODULE_DEMO_EXPIRED)
                                {
                                    $arModules[$dir]["DEMO_DATE"] = ConvertTimeStamp($GLOBALS["SiteExpireDate_".str_replace(".", "_", $info->MODULE_ID)], "SHORT");
                                }
                                else
                                    $arModules[$dir]["DEMO_END"] = "Y";
                            }
                        }
                    }
                }

            closedir($handle);
        }
    }
}

$errorMessage = array();
$stableVersionsOnly = COption::GetOptionString("main", "stable_versions_only", "Y");
$arRequestedModules = CUpdateClientPartner::GetRequestedModules(implode(',',$arModulList));
$arUpdateList = CUpdateClientPartner::GetUpdatesList($errorMessage, LANG, $stableVersionsOnly, $arRequestedModules, Array("fullmoduleinfo" => "Y"));


$modulesNew = array();
if(!empty($arUpdateList["MODULE"]))
{
    foreach($arUpdateList["MODULE"] as $k => $v)
    {
        if(!array_key_exists($v["@"]["ID"], $arClientModules))
        {
            $bHaveNew = true;
            $modulesNew[$v["@"]["ID"]] = Array(
                "NAME" => htmlspecialcharsBack($v["@"]["NAME"]),
                "ID" => $v["@"]["ID"],
                "DESCRIPTION" => $v["@"]["DESCRIPTION"],
                "PARTNER" => $v["@"]["PARTNER_NAME"],
                "FREE_MODULE" => $v["@"]["FREE_MODULE"],
                "DATE_FROM" => $v["@"]["DATE_FROM"],
                "DATE_TO" => $v["@"]["DATE_TO"],
                "UPDATE_END" => $v["@"]["UPDATE_END"],
            );
        }
        else
        {
            $modules[$v["@"]["ID"]] = Array(
                "VERSION" => (isset($v["#"]["VERSION"]) ? $v["#"]["VERSION"][count($v["#"]["VERSION"]) - 1]["@"]["ID"] : ""),
                "FREE_MODULE" => $v["@"]["FREE_MODULE"],
                "DATE_FROM" => $v["@"]["DATE_FROM"],
                "DATE_TO" => $v["@"]["DATE_TO"],
                "UPDATE_END" => $v["@"]["UPDATE_END"],
            );
        }
    }
}

$arResult['newM'] = $modulesNew;
$arResult['modules'] = $arModules;

$stableVersionsOnly = 'Y';
$arUpdateListS = array();

if (!$bLockUpdateSystemKernel)
{
    if (CUpdateClient::Lock())
    {
        if ($arUpdateListS = CUpdateClient::GetUpdatesList($errorMessage, LANG, "Y"))
        {
            $refreshStep = intval($_REQUEST["refresh_step"]) + 1;
            if (isset($arUpdateListS["REPAIR"]))
            {
                if ($refreshStep < 5)
                {
                    CUpdateClient::Repair($arUpdateListS["REPAIR"][0]["@"]["TYPE"], $stableVersionsOnly, LANG);
                    CUpdateClient::UnLock();
                    LocalRedirect("/bitrix/admin/update_system.php?refresh=Y&refresh_step=".$refreshStep."&lang=".LANGUAGE_ID);
                }
                else
                {
                    $errorMessage .= "<br>".GetMessage("SUP_CANT_REPARE").". ";
                }
            }
        }
        else
        {
            $errorMessage .= "<br>".GetMessage("SUP_CANT_CONNECT").". ";
        }
        CUpdateClient::UnLock();
    }
    else
    {
        $errorMessage .= "<br>".GetMessage("SUP_CANT_LOCK_UPDATES").". ";
    }
}
else
{
    $errorMessage .= "<br>".GetMessage("SUP_CANT_CONTRUPDATE").". ";
}


$arResult['DATE_TO_SITE'] = $arUpdateListS['CLIENT'][0]['@']['DATE_TO'];
$arResult['LICENSE_SITE'] = $arUpdateListS['CLIENT'][0]['@']['LICENSE'];



$this->IncludeComponentTemplate();
