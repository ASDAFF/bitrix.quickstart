<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');

    $arOptions = array(
        "IBLOCK_SECTION_SYNCHRONIZE_NAME" => CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_SECTION_SYNCHRONIZE_NAME"),
        "IBLOCK_SECTION_ACTION" => CStartShopVariables::Get("1C_EXCHANGE_IBLOCK_SECTION_ACTION"),
    );

    $fConvert = function ($sText) {
        global $APPLICATION;
        return $APPLICATION->ConvertCharset($sText, "UTF-8", "CP1251");
    };

    function CheckSectionsByNodes ($arIBlock, $arIBlockSections, $oNodes, $arNodeParent = null) {
        global $fConvert, $arOptions;

        $arIBlockSectionsChecked = array();

        foreach ($oNodes as $oNode) {
            $oNodesChildren = $oNode->children();
            $oNodeID = $oNodesChildren[0];
            $oNodeName = $oNodesChildren[1];
            $oNodeGroups = $oNodesChildren[2];

            $arNode = array(
                "ID" => $fConvert($oNodeID->textContent()),
                "NAME" => $fConvert($oNodeName->textContent())
            );

            if (!empty($arNodeParent))
                $arNode["PARENT"] = $arNodeParent["ID"];

            $oIBlockSection = new CIBlockSection();
            $arIBlockSection = $arIBlockSections[$arNode["ID"]];

            if (!empty($arIBlockSection) && $arOptions["IBLOCK_SECTION_SYNCHRONIZE_NAME"] == "Y") {
                if ($arNode["NAME"] != $arIBlockSection["NAME"])
                    $oIBlockSection->Update($arIBlockSection["ID"], array(
                        "NAME" => $arNode["NAME"]
                    ));
            } else {
                $arIBlockSectionFields = array(
                    "IBLOCK_ID" => $arIBlock["ID"],
                    "CODE" => $arNode["ID"],
                    "EXTERNAL_ID" => $arNode["ID"],
                    "NAME" => $arNode["NAME"]
                );

                if (!empty($arNodeParent))
                    if (!empty($arIBlockSections[$arNodeParent["ID"]]))
                        $arIBlockSection["IBLOCK_SECTION_ID"] = $arIBlockSections[$arNodeParent["ID"]]["ID"];

                $oIBlockSection = new CIBlockSection();
                $oIBlockSection->Add($arIBlockSectionFields, false, false);

                unset($arIBlockSectionFields);
            }

            $arIBlockSectionsChecked[$arNode["ID"]] = $arNode;

            unset($oIBlockSection, $arIBlockSection);

            if (!empty($oNodeGroups)) {
                $arIBlockSections = CStartShopUtil::DBResultToArray(CIBlockSection::GetList(
                    array(),
                    array("IBLOCK_ID" => $arIBlock["ID"])
                ), 'EXTERNAL_ID');

                $arIBlockSectionsChecked = array_merge(
                    $arIBlockSectionsChecked,
                    CheckSectionsByNodes($arIBlock, $arIBlockSections, $oNodeGroups->children(), $arNode)
                );
            }
        }

        return $arIBlockSectionsChecked;
    }

    $sUploadDirectory = $_SERVER['DOCUMENT_ROOT'].'/upload/startshop';

    $sFileName = $_GET['filename'];
    $sFilePath = $sUploadDirectory.'/'.$sFileName;
    $sFileDirectory = dirname($sFilePath);
    $sFileData = file_get_contents($sFilePath);

    $sLog = "";
    $sLog .= print_r($arOptions, true);

    if (is_file($sFilePath)) {
        $oXml = new CDataXML();
        $oXml->LoadString($sFileData);

        $arSites = CStartShopUtil::DBResultToArray(CSite::GetList($by = "sort", $order = "asc"), "ID");
        $arIBlock = array("IBLOCK_TYPE_ID" => "catalogs", "SITE_ID" => array(), "ACTIVE" => "Y");

        foreach ($arSites as $arSite)
            $arIBlock["SITE_ID"][] = $arSite["ID"];

        if ($oNode = $oXml->SelectNodes("/КоммерческаяИнформация/Классификатор/Ид"))
            $arIBlock['EXTERNAL_ID'] = $fConvert($oNode->textContent());

        if ($oNode = $oXml->SelectNodes("/КоммерческаяИнформация/Классификатор/Наименование"))
            $arIBlock['NAME'] = $fConvert($oNode->textContent());

        if (!empty($arIBlock["IBLOCK_TYPE_ID"]) && !empty($arIBlock["SITE_ID"]) && !empty($arIBlock["EXTERNAL_ID"]) && !empty($arIBlock["NAME"])) {
            $arIBlockExisted = CIBlock::GetList(array(), array("EXTERNAL_ID" => $arIBlock["EXTERNAL_ID"]))->Fetch();

            if (empty($arIBlockExisted)) {
                $oIBlock = new CIBlock();
                $iIBlockID = $oIBlock->Add($arIBlock);

                if (!empty($iIBlockID))
                    $arIBlock = CIBlock::GetByID($iIBlockID)->Fetch();

                unset($iIBlockID);
            } else {
                $arIBlock = $arIBlockExisted;
            }

            unset($arIBlockExisted);
        } else {
            $arIBlock = null;
        }

        if (!empty($arIBlock)) {
            $arIBlockSectionsNodes = array();
            $arIBlockSections = CStartShopUtil::DBResultToArray(CIBlockSection::GetList(
                array(),
                array("IBLOCK_ID" => $arIBlock["ID"])
            ), 'EXTERNAL_ID');

            if ($oNode = $oXml->SelectNodes("/КоммерческаяИнформация/Классификатор/Группы"))
                $arIBlockSectionsNodes = CheckSectionsByNodes($arIBlock, $arIBlockSections, $oNode->children());

            if ($arOptions["IBLOCK_SECTION_ACTION"] != "NOTHING") {
                $arIBlockSections = CStartShopUtil::DBResultToArray(CIBlockSection::GetList(
                    array(),
                    array("IBLOCK_ID" => $arIBlock["ID"])
                ), 'ID');

                foreach ($arIBlockSections as $arIBlockSection) {
                    if (!empty($arIBlockSection["EXTERNAL_ID"]))
                        if (array_key_exists($arIBlockSection["EXTERNAL_ID"], $arIBlockSectionsNodes)) continue;

                    $oIBlockSection = new CIBlockSection();

                    if ($arOptions["IBLOCK_SECTION_ACTION"] == "DELETE") {
                        $oIBlockSection->Delete($arIBlockSection["ID"]);
                    } else {
                        $oIBlockSection->Update($arIBlockSection["ID"], array("ACTIVE" => "N"));
                    }

                    unset($oIBlockSection);
                }
            }

            $oIBlockSection = new CIBlockSection();
            $oIBlockSection->ReSort($arIBlock["ID"]);
            $oIBlockSection->TreeReSort($arIBlock["ID"]);
            unset($oIBlockSection);

            echo "success\n";
        } else {
            echo "failure\n";
        }
    } else {
        echo "failure\n";
    }

    unlink($sFilePath);

    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/exchange_debug.txt', $sLog);


?>