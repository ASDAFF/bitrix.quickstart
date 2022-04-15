<?php
/**
 * Copyright (c) 15/4/2022 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CHelper1C
{
    function agent1c(){ return "agent1c();";
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/upload/1c_catalog/';
        $files = scandirs($dir);
        CModule::IncludeModule('catalog');
        require_once '../components/devteam/catalog.import.1c/inc.php';
        foreach ($files as $file) {
            if($file == 'Goods.xml')
                continue;
            $ABS_FILE_NAME = $dir . $file;
            CIBlockXMLFile::DropTemporaryTables();
            CIBlockXMLFile::CreateTemporaryTables();
            $fp = fopen($ABS_FILE_NAME, "rb");
            $total = filesize($ABS_FILE_NAME);
            if(($total > 0) && is_resource($fp))
            {
                $obXMLFile = new CIBlockXMLFile;
                if($obXMLFile->ReadXMLToDatabase($fp, $NS, $arParams["INTERVAL"]))
                {
                    $NS["STEP"] = 3;
                    $strMessage = GetMessage("CC_BSC1_FILE_READ");
                }
                else
                {
                    $strMessage = GetMessage("CC_BSC1_FILE_PROGRESS", array("#PERCENT#"=>$total > 0? round($obXMLFile->GetFilePosition()/$total*100, 2): 0));
                }
                fclose($fp);
            }
            CIBlockXMLFile::IndexTemporaryTables();
            $obCatalog = new myCIBlockCMLImport;
            $result = $obCatalog->ImportSections();
            unlink($ABS_FILE_NAME);
        }
        return "agent1c();";
    }
}