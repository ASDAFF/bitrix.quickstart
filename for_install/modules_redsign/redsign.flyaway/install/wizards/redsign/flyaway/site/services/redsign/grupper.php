<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$module_id = 'redsign.grupper';

if($obModule = CModule::CreateModuleObject($module_id)){
    if(!$obModule->IsInstalled()){
        $obModule->InstallDB();
        $obModule->InstallEvents();
        $obModule->InstallOptions();
        $obModule->InstallFiles();
        $obModule->InstallPublic();
    }
}

if (
    WIZARD_INSTALL_DEMO_DATA &&
    CModule::IncludeModule('iblock') &&
    CModule::IncludeModule('redsign.grupper')
) {
    WizardServices::IncludeServiceLang("grupper.php", $lang);
    
    // take some N iblock_properties
    $arrFilter1 = array(
        array(
            "IBLOCK_TYPE" => "catalog",
            "IBLOCK_CODE" => "catalog",
            "IBLOCK_XML_ID" => "catalog_".WIZARD_SITE_ID,
        ),
    );
    
    foreach($arrFilter1 as $filter1)
    {
        $rsIBlock = CIBlock::GetList(array(), array( "TYPE" => $filter1["IBLOCK_TYPE"], "CODE" => $filter1["IBLOCK_CODE"], "XML_ID" => $filter1["IBLOCK_XML_ID"] ));
        if ($arIBlock = $rsIBlock->Fetch())
        {
            $code1 = $filter1["IBLOCK_CODE"];
            $arrIBlockIDs[$code1] = $arIBlock["ID"];
        }
    }
    
    $arrGroups = array(
        array(
            "NAME" => GetMessage("GROUP_NAME_1"),
            "CODE" => "OSNOVNOE",
            "SORT" => "101",
            "BINDS" => array("MAKER"),
        ),
        array(
            "NAME" => GetMessage("GROUP_NAME_2"),
            "CODE" => "OTHER",
            "SORT" => "201",
            "BINDS" => array("VILKA","OBODA","RAMA","TORMOZA"),
        ),
    );
    
    foreach($arrGroups as $arGroup)
    {
        $arFields = array(
            "NAME" => trim(htmlspecialchars($arGroup["NAME"])),
            "CODE" => trim(htmlspecialchars($arGroup["CODE"])),
            "SORT" => trim(htmlspecialchars($arGroup["SORT"])),
        );
        $ID = CRSGGroups::Add($arFields);
        if(IntVal($ID)>0)
        {
            foreach($arGroup["BINDS"] as $propCode)
            {
                $arOrder = array("sort"=>"asc","name"=>"asc");
                $arFilter = array("ACTIVE"=>"Y","IBLOCK_ID"=>$arrIBlockIDs["catalog"],"CODE"=>$propCode);
                $resProp = CIBlockProperty::GetList($arOrder,$arFilter);
                if($arProperty = $resProp->GetNext())
                {
                    //CRSGBinds::DeleteBindsForGroupID($ID);
                    $arFieldsBind = array(
                        "IBLOCK_PROPERTY_ID" => $arProperty["ID"],
                        "GROUP_ID" => $ID,
                    );
                    $BIND_ID = CRSGBinds::Add($arFieldsBind);
                }
            }
        }
    }
}
