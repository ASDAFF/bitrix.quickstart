<?php

namespace Helper;
// Ищи "CMLNameConnector"
\Bitrix\Main\Loader::IncludeModule('iblock');

class CustomCML2 extends CIBlockCMLImport
{
    static function onSuccess()
    {
        MHT::activateIBlocks();
        file_put_contents(self::getTimeFileName(), microtime());
    }

    protected static function getTimeFileName()
    {
        return __DIR__ . '/last_update_time.txt';
    }

    static function getLastUpdateTime()
    {
        $file = self::getTimeFileName();
        return file_exists($file) ? file_get_contents($file) : null;
    }

    function ImportProperties($XML_PROPERTIES_PARENT, $IBLOCK_ID)
    {
        $obProperty = new CIBlockProperty;
        $sort = 100;

        $arElementFields = array(
            "CML2_ACTIVE" => $this->mess["IBLOCK_XML2_BX_ACTIVE"],
            "CML2_CODE" => $this->mess["IBLOCK_XML2_SYMBOL_CODE"],
            "CML2_SORT" => $this->mess["IBLOCK_XML2_SORT"],
            "CML2_ACTIVE_FROM" => $this->mess["IBLOCK_XML2_START_TIME"],
            "CML2_ACTIVE_TO" => $this->mess["IBLOCK_XML2_END_TIME"],
            "CML2_PREVIEW_TEXT" => $this->mess["IBLOCK_XML2_ANONS"],
            "CML2_DETAIL_TEXT" => $this->mess["IBLOCK_XML2_DETAIL"],
            "CML2_PREVIEW_PICTURE" => $this->mess["IBLOCK_XML2_PREVIEW_PICTURE"],
        );

        $rs = $this->_xml_file->GetList(
            array("ID" => "asc"),
            array("PARENT_ID" => $XML_PROPERTIES_PARENT),
            array("ID")
        );
        while ($ar = $rs->Fetch()) {
            $XML_ENUM_PARENT = false;
            $isExternal = false;
            $arProperty = array();
            $rsP = $this->_xml_file->GetList(
                array("ID" => "asc"),
                array("PARENT_ID" => $ar["ID"])
            );
            while ($arP = $rsP->Fetch()) {
                if (isset($arP["VALUE_CLOB"]))
                    $arP["VALUE"] = $arP["VALUE_CLOB"];

                if ($arP["NAME"] == $this->mess["IBLOCK_XML2_ID"]) {
                    $arProperty["XML_ID"] = $arP["VALUE"];
                    if (array_key_exists($arProperty["XML_ID"], $arElementFields))
                        break;
                } elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_NAME"])
                    $arProperty["NAME"] = $arP["VALUE"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_MULTIPLE"])
                    $arProperty["MULTIPLE"] = ($arP["VALUE"] == "true") || intval($arP["VALUE"]) ? "Y" : "N";
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_SORT"])
                    $arProperty["SORT"] = $arP["VALUE"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_CODE"])
                    $arProperty["CODE"] = $arP["VALUE"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_DEFAULT_VALUE"])
                    $arProperty["DEFAULT_VALUE"] = $arP["VALUE"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_SERIALIZED"])
                    $arProperty["SERIALIZED"] = ($arP["VALUE"] == "true") || intval($arP["VALUE"]) ? "Y" : "N";
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_PROPERTY_TYPE"]) {
                    $arProperty["PROPERTY_TYPE"] = $arP["VALUE"];
                    $arProperty["USER_TYPE"] = "";
                } elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_ROWS"])
                    $arProperty["ROW_COUNT"] = $arP["VALUE"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_COLUMNS"])
                    $arProperty["COL_COUNT"] = $arP["VALUE"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_LIST_TYPE"])
                    $arProperty["LIST_TYPE"] = $arP["VALUE"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_FILE_EXT"])
                    $arProperty["FILE_TYPE"] = $arP["VALUE"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_FIELDS_COUNT"])
                    $arProperty["MULTIPLE_CNT"] = $arP["VALUE"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_USER_TYPE"])
                    $arProperty["USER_TYPE"] = $arP["VALUE"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_WITH_DESCRIPTION"])
                    $arProperty["WITH_DESCRIPTION"] = ($arP["VALUE"] == "true") || intval($arP["VALUE"]) ? "Y" : "N";
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_SEARCH"])
                    $arProperty["SEARCHABLE"] = ($arP["VALUE"] == "true") || intval($arP["VALUE"]) ? "Y" : "N";
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_FILTER"])
                    $arProperty["FILTRABLE"] = ($arP["VALUE"] == "true") || intval($arP["VALUE"]) ? "Y" : "N";
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_LINKED_IBLOCK"])
                    $arProperty["LINK_IBLOCK_ID"] = $this->GetIBlockByXML_ID($arP["VALUE"]);
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_CHOICE_VALUES"])
                    $XML_ENUM_PARENT = $arP["ID"];
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_IS_REQUIRED"])
                    $arProperty["IS_REQUIRED"] = ($arP["VALUE"] == "true") || intval($arP["VALUE"]) ? "Y" : "N";
                elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_VALUES_TYPE"]) {
                    if (
                        $arP["VALUE"] == $this->mess["IBLOCK_XML2_TYPE_LIST"]
                        && !$isExternal
                    ) {
                        $arProperty["PROPERTY_TYPE"] = "L";
                        $arProperty["USER_TYPE"] = "";
                    } elseif ($arP["VALUE"] == $this->mess["IBLOCK_XML2_TYPE_NUMBER"]) {
                        $arProperty["PROPERTY_TYPE"] = "N";
                        $arProperty["USER_TYPE"] = "";
                    } elseif ($arP["VALUE"] == $this->mess["IBLOCK_XML2_TYPE_STRING"]) {
                        $arProperty["PROPERTY_TYPE"] = "S";
                        $arProperty["USER_TYPE"] = "";
                    }
                } elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_VALUES_TYPES"]) {
                    //This property metadata contains information about it's type
                    $rsTypes = $this->_xml_file->GetList(
                        array("ID" => "asc"),
                        array("PARENT_ID" => $arP["ID"]),
                        array("ID", "LEFT_MARGIN", "RIGHT_MARGIN", "NAME")
                    );
                    $arType = $rsTypes->Fetch();
                    //We'll process only properties with NOT composing types
                    //composed types will be supported only as simple string properties
                    if ($arType && !$rsTypes->Fetch()) {
                        $rsType = $this->_xml_file->GetList(
                            array("ID" => "asc"),
                            array("PARENT_ID" => $arType["ID"]),
                            array("ID", "LEFT_MARGIN", "RIGHT_MARGIN", "NAME", "VALUE")
                        );
                        while ($arType = $rsType->Fetch()) {
                            if ($arType["NAME"] == $this->mess["IBLOCK_XML2_TYPE"]) {
                                if ($arType["VALUE"] == $this->mess["IBLOCK_XML2_TYPE_LIST"])
                                    $arProperty["PROPERTY_TYPE"] = "L";
                                elseif ($arType["VALUE"] == $this->mess["IBLOCK_XML2_TYPE_NUMBER"])
                                    $arProperty["PROPERTY_TYPE"] = "N";
                            } elseif ($arType["NAME"] == $this->mess["IBLOCK_XML2_CHOICE_VALUES"]) {
                                $XML_ENUM_PARENT = $arType["ID"];
                            }
                        }
                    }
                } elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_BX_USER_TYPE_SETTINGS"]) {
                    $arProperty["USER_TYPE_SETTINGS"] = unserialize($arP["VALUE"]);
                } elseif ($arP["NAME"] == $this->mess["IBLOCK_XML2_EXTERNAL"]) {
                    $isExternal = ($arP["VALUE"] == "true") || intval($arP["VALUE"]) ? true : false;
                    if ($isExternal) {
                        $arProperty["PROPERTY_TYPE"] = "S";
                        $arProperty["USER_TYPE"] = "directory";
                    }
                }
            }

            if (array_key_exists($arProperty["XML_ID"], $arElementFields))
                continue;

            // Skip properties with no choice values
            // http://jabber.bx/view.php?id=30476
            $arEnumXmlNodes = array();
            if ($XML_ENUM_PARENT) {
                $rsE = $this->_xml_file->GetList(
                    array("ID" => "asc"),
                    array("PARENT_ID" => $XML_ENUM_PARENT)
                );
                while ($arE = $rsE->Fetch()) {
                    if (isset($arE["VALUE_CLOB"]))
                        $arE["VALUE"] = $arE["VALUE_CLOB"];
                    $arEnumXmlNodes[] = $arE;
                }

                if (empty($arEnumXmlNodes))
                    continue;
            }

            if ($arProperty["SERIALIZED"] == "Y")
                $arProperty["DEFAULT_VALUE"] = unserialize($arProperty["DEFAULT_VALUE"]);

            $arProperty['XML_ID'] = CMLNameConnector::xid(
                'prop',
                $arProperty['XML_ID'],
                $arProperty['NAME']
            );

            $rsProperty = $obProperty->GetList(array(), array("IBLOCK_ID" => $IBLOCK_ID, "XML_ID" => $arProperty["XML_ID"]));
            if ($arDBProperty = $rsProperty->Fetch()) {
                $bChanged = false;
                foreach ($arProperty as $key => $value) {
                    if ($arDBProperty[$key] !== $value) {
                        $bChanged = true;
                        break;
                    }
                }
                if (!$bChanged)
                    $arProperty["ID"] = $arDBProperty["ID"];
                elseif ($obProperty->Update($arDBProperty["ID"], $arProperty))
                    $arProperty["ID"] = $arDBProperty["ID"];
                else
                    return $obProperty->LAST_ERROR;
            } else {
                $arProperty["IBLOCK_ID"] = $IBLOCK_ID;
                $arProperty["ACTIVE"] = "Y";
                if (!array_key_exists("PROPERTY_TYPE", $arProperty))
                    $arProperty["PROPERTY_TYPE"] = "S";
                if (!array_key_exists("SORT", $arProperty))
                    $arProperty["SORT"] = $sort;
                if (!array_key_exists("CODE", $arProperty)) {
                    $arProperty["CODE"] = CUtil::translit($arProperty["NAME"], LANGUAGE_ID, array(
                        "max_len" => 50,
                        "change_case" => 'U', // 'L' - toLower, 'U' - toUpper, false - do not change
                        "replace_space" => '_',
                        "replace_other" => '_',
                        "delete_repeat_replace" => true,
                    ));
                    if (preg_match('/^[0-9]/', $arProperty["CODE"]))
                        $arProperty["CODE"] = '_' . $arProperty["CODE"];

                    $rsProperty = $obProperty->GetList(array(), array("IBLOCK_ID" => $IBLOCK_ID, "CODE" => $arProperty["CODE"]));
                    if ($arDBProperty = $rsProperty->Fetch()) {
                        $suffix = 0;
                        do {
                            $suffix++;
                            $rsProperty = $obProperty->GetList(array(), array("IBLOCK_ID" => $IBLOCK_ID, "CODE" => $arProperty["CODE"] . "_" . $suffix));
                        } while ($rsProperty->Fetch());
                        $arProperty["CODE"] .= '_' . $suffix;
                    }
                }
                $arProperty["ID"] = $obProperty->Add($arProperty);
                if (!$arProperty["ID"])
                    return $obProperty->LAST_ERROR;
            }

            if ($XML_ENUM_PARENT) {
                if ($isExternal)
                    $result = $this->ImportPropertyDirectory($arProperty, $arEnumXmlNodes);
                else
                    $result = $this->ImportPropertyEnum($arProperty, $arEnumXmlNodes);

                if ($result !== true)
                    return $result;
            }
            $sort += 100;
        }
        return true;
    }

    function ImportPropertyEnum($arProperty, $arEnumXmlNodes)
    {
        $arEnumMap = array();
        $arProperty["VALUES"] = array();
        $rsEnum = CIBlockProperty::GetPropertyEnum($arProperty["ID"]);
        while ($arEnum = $rsEnum->Fetch()) {
            $arProperty["VALUES"][$arEnum["ID"]] = $arEnum;
            $arEnumMap[$arEnum["XML_ID"]] = &$arProperty["VALUES"][$arEnum["ID"]];
        }

        $i = 0;
        foreach ($arEnumXmlNodes as $arE) {
            if (
                $arE["NAME"] == $this->mess["IBLOCK_XML2_CHOICE"]
                || $arE["NAME"] == $this->mess["IBLOCK_XML2_CHOICE_VALUE"]
            ) {
                $arE = $this->_xml_file->GetAllChildrenArray($arE);
                if (isset($arE[$this->mess["IBLOCK_XML2_ID"]])) {
                    $xml_id = $arE[$this->mess["IBLOCK_XML2_ID"]];
                    if (strlen($arE[$this->mess["IBLOCK_XML2_VALUE"]])) {
                        $xml_id = CMLNameConnector::xid(
                            'enum',
                            $xml_id,
                            $arE[$this->mess["IBLOCK_XML2_VALUE"]]
                        );
                    }
                    if (!array_key_exists($xml_id, $arEnumMap)) {
                        $arProperty["VALUES"]["n" . $i] = array();
                        $arEnumMap[$xml_id] = &$arProperty["VALUES"]["n" . $i];
                        $i++;
                    }
                    $arEnumMap[$xml_id]["CML2_EXPORT_FLAG"] = true;
                    $arEnumMap[$xml_id]["XML_ID"] = $xml_id;
                    if (isset($arE[$this->mess["IBLOCK_XML2_VALUE"]]))
                        $arEnumMap[$xml_id]["VALUE"] = $arE[$this->mess["IBLOCK_XML2_VALUE"]];
                    if (isset($arE[$this->mess["IBLOCK_XML2_BY_DEFAULT"]]))
                        $arEnumMap[$xml_id]["DEF"] = ($arE[$this->mess["IBLOCK_XML2_BY_DEFAULT"]] == "true") || intval($arE[$this->mess["IBLOCK_XML2_BY_DEFAULT"]]) ? "Y" : "N";
                    if (isset($arE[$this->mess["IBLOCK_XML2_SORT"]]))
                        $arEnumMap[$xml_id]["SORT"] = intval($arE[$this->mess["IBLOCK_XML2_SORT"]]);
                }
            } elseif (
                $arE["NAME"] == $this->mess["IBLOCK_XML2_TYPE_LIST"]
            ) {
                $arE = $this->_xml_file->GetAllChildrenArray($arE);
                if (isset($arE[$this->mess["IBLOCK_XML2_VALUE_ID"]])) {
                    $xml_id = $arE[$this->mess["IBLOCK_XML2_VALUE_ID"]];
                    if (strlen($arE[$this->mess["IBLOCK_XML2_VALUE"]])) {
                        $xml_id = CMLNameConnector::xid(
                            'enum',
                            $xml_id,
                            $arE[$this->mess["IBLOCK_XML2_VALUE"]]
                        );
                    }
                    if (!array_key_exists($xml_id, $arEnumMap)) {
                        $arProperty["VALUES"]["n" . $i] = array();
                        $arEnumMap[$xml_id] = &$arProperty["VALUES"]["n" . $i];
                        $i++;
                    }
                    $arEnumMap[$xml_id]["CML2_EXPORT_FLAG"] = true;
                    $arEnumMap[$xml_id]["XML_ID"] = $xml_id;
                    if (isset($arE[$this->mess["IBLOCK_XML2_VALUE"]]))
                        $arEnumMap[$xml_id]["VALUE"] = $arE[$this->mess["IBLOCK_XML2_VALUE"]];
                }
            }

        }


        $bUpdateOnly = array_key_exists("bUpdateOnly", $this->next_step) && $this->next_step["bUpdateOnly"];
        $sort = 100;

        foreach ($arProperty["VALUES"] as $id => $arEnum) {
            if (!isset($arEnum["CML2_EXPORT_FLAG"])) {
                //Delete value only when full exchange happened
                if (!$bUpdateOnly)
                    $arProperty["VALUES"][$id]["VALUE"] = "";
            } elseif (isset($arEnum["SORT"])) {
                if ($arEnum["SORT"] > $sort)
                    $sort = $arEnum["SORT"] + 100;
            } else {
                $arProperty["VALUES"][$id]["SORT"] = $sort;
                $sort += 100;
            }
        }

        $obProperty = new CIBlockProperty;
        $obProperty->UpdateEnum($arProperty["ID"], $arProperty["VALUES"], false);

        return true;
    }

    function ImportElement($arXMLElement, &$counter, $bWF, $arParent)
    {


        global $USER;
        $USER_ID = is_object($USER) ? intval($USER->GetID()) : 0;
        $arElement = array(
            "ACTIVE" => "Y",
            "PROPERTY_VALUES" => array(),
        );

        if (isset($arXMLElement[$this->mess["IBLOCK_XML2_VERSION"]]))
            $arElement["TMP_ID"] = $arXMLElement[$this->mess["IBLOCK_XML2_VERSION"]];
        else
            $arElement["TMP_ID"] = $this->GetElementCRC($arXMLElement);

        if (isset($arXMLElement[$this->mess["IBLOCK_XML2_ID"]]))
            $arElement["XML_ID"] = $arXMLElement[$this->mess["IBLOCK_XML2_ID"]];

        $obElement = new CIBlockElement;
        $obElement->CancelWFSetMove();
        $rsElement = $obElement->GetList(
            array("ID" => "asc"),
            array("=XML_ID" => $arElement["XML_ID"], "IBLOCK_ID" => $this->next_step["IBLOCK_ID"]),
            false, false,
            array("ID", "TMP_ID", "ACTIVE", "CODE", "PREVIEW_PICTURE", "DETAIL_PICTURE")
        );

        $bMatch = false;
        if ($arDBElement = $rsElement->Fetch())
            $bMatch = ($arElement["TMP_ID"] == $arDBElement["TMP_ID"]);

        if ($bMatch && $this->use_crc) {
            //Check Active flag in XML is not set to false
            if ($this->CheckIfElementIsActive($arXMLElement)) {
                //In case element is not active in database we have to activate it and its offers
                if ($arDBElement["ACTIVE"] != "Y") {
                    $obElement->Update($arDBElement["ID"], array("ACTIVE" => "Y"), $bWF);
                    $this->ChangeOffersStatus($arDBElement["ID"], "Y", $bWF);
                    $counter["UPD"]++;
                }
            }
            $arElement["ID"] = $arDBElement["ID"];
        } elseif (isset($arXMLElement[$this->mess["IBLOCK_XML2_NAME"]])) {
            if ($arDBElement) {
                if ($arDBElement["PREVIEW_PICTURE"] > 0)
                    $this->arElementFilesId["PREVIEW_PICTURE"] = array($arDBElement["PREVIEW_PICTURE"]);
                if ($arDBElement["DETAIL_PICTURE"] > 0)
                    $this->arElementFilesId["DETAIL_PICTURE"] = array($arDBElement["DETAIL_PICTURE"]);

                $rsProperties = $obElement->GetProperty($this->next_step["IBLOCK_ID"], $arDBElement["ID"], "sort", "asc");
                while ($arProperty = $rsProperties->Fetch()) {
                    if (!array_key_exists($arProperty["ID"], $arElement["PROPERTY_VALUES"]))
                        $arElement["PROPERTY_VALUES"][$arProperty["ID"]] = array(
                            "bOld" => true,
                        );

                    $arElement["PROPERTY_VALUES"][$arProperty["ID"]][$arProperty['PROPERTY_VALUE_ID']] = array(
                        "VALUE" => $arProperty['VALUE'],
                        "DESCRIPTION" => $arProperty["DESCRIPTION"]
                    );

                    if ($arProperty["PROPERTY_TYPE"] == "F" && $arProperty["VALUE"] > 0)
                        $this->arElementFilesId[$arProperty["ID"]][] = $arProperty["VALUE"];
                }
            }

            if ($this->bCatalog && $this->next_step["bOffer"]) {
                $p = strpos($arXMLElement[$this->mess["IBLOCK_XML2_ID"]], "#");
                if ($p !== false)
                    $link_xml_id = substr($arXMLElement[$this->mess["IBLOCK_XML2_ID"]], 0, $p);
                else
                    $link_xml_id = $arXMLElement[$this->mess["IBLOCK_XML2_ID"]];
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_LINK"]] = array(
                    "n0" => array(
                        "VALUE" => $this->GetElementByXML_ID($this->arProperties[$this->PROPERTY_MAP["CML2_LINK"]]["LINK_IBLOCK_ID"], $link_xml_id),
                        "DESCRIPTION" => false,
                    ),
                );
            }

            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_NAME"]]))
                $arElement["NAME"] = $arXMLElement[$this->mess["IBLOCK_XML2_NAME"]];

            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_DELETE_MARK"]])) {
                $value = $arXMLElement[$this->mess["IBLOCK_XML2_DELETE_MARK"]];
                $arElement["ACTIVE"] = ($value == "true") || intval($value) ? "N" : "Y";
            }

            if (array_key_exists($this->mess["IBLOCK_XML2_BX_TAGS"], $arXMLElement))
                $arElement["TAGS"] = $arXMLElement[$this->mess["IBLOCK_XML2_BX_TAGS"]];

            if (array_key_exists($this->mess["IBLOCK_XML2_DESCRIPTION"], $arXMLElement)) {
                if (strlen($arXMLElement[$this->mess["IBLOCK_XML2_DESCRIPTION"]]) > 0)
                    $arElement["DETAIL_TEXT"] = $arXMLElement[$this->mess["IBLOCK_XML2_DESCRIPTION"]];
                else
                    $arElement["DETAIL_TEXT"] = "";

                if (preg_match('/<[a-zA-Z0-9]+.*?>/', $arElement["DETAIL_TEXT"]))
                    $arElement["DETAIL_TEXT_TYPE"] = "html";
                else
                    $arElement["DETAIL_TEXT_TYPE"] = "text";
            }

            if (array_key_exists($this->mess["IBLOCK_XML2_FULL_TITLE"], $arXMLElement)) {
                if (strlen($arXMLElement[$this->mess["IBLOCK_XML2_FULL_TITLE"]]) > 0)
                    $arElement["PREVIEW_TEXT"] = $arXMLElement[$this->mess["IBLOCK_XML2_FULL_TITLE"]];
                else
                    $arElement["PREVIEW_TEXT"] = "";

                if (preg_match('/<[a-zA-Z0-9]+.*?>/', $arElement["PREVIEW_TEXT"]))
                    $arElement["PREVIEW_TEXT_TYPE"] = "html";
                else
                    $arElement["PREVIEW_TEXT_TYPE"] = "text";
            }

            if (array_key_exists($this->mess["IBLOCK_XML2_INHERITED_TEMPLATES"], $arXMLElement)) {
                $arElement["IPROPERTY_TEMPLATES"] = array();
                foreach ($arXMLElement[$this->mess["IBLOCK_XML2_INHERITED_TEMPLATES"]] as $TEMPLATE) {
                    $id = $TEMPLATE[$this->mess["IBLOCK_XML2_ID"]];
                    $template = $TEMPLATE[$this->mess["IBLOCK_XML2_VALUE"]];
                    if (strlen($id) > 0 && strlen($template) > 0)
                        $arElement["IPROPERTY_TEMPLATES"][$id] = $template;
                }
            }
            if (array_key_exists($this->mess["IBLOCK_XML2_BAR_CODE2"], $arXMLElement)) {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BAR_CODE"]] = array(
                    "n0" => array(
                        "VALUE" => $arXMLElement[$this->mess["IBLOCK_XML2_BAR_CODE2"]],
                        "DESCRIPTION" => false,
                    ),
                );
            } elseif (array_key_exists($this->mess["IBLOCK_XML2_BAR_CODE"], $arXMLElement)) {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BAR_CODE"]] = array(
                    "n0" => array(
                        "VALUE" => $arXMLElement[$this->mess["IBLOCK_XML2_BAR_CODE"]],
                        "DESCRIPTION" => false,
                    ),
                );
            }

            if (array_key_exists($this->mess["IBLOCK_XML2_ARTICLE"], $arXMLElement)) {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_ARTICLE"]] = array(
                    "n0" => array(
                        "VALUE" => $arXMLElement[$this->mess["IBLOCK_XML2_ARTICLE"]],
                        "DESCRIPTION" => false,
                    ),
                );
            }

            if (array_key_exists($this->mess["IBLOCK_XML2_MANUFACTURER"], $arXMLElement)) {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_MANUFACTURER"]] = array(
                    "n0" => array(
                        "VALUE" => $this->CheckManufacturer($arXMLElement[$this->mess["IBLOCK_XML2_MANUFACTURER"]]),
                        "DESCRIPTION" => false,
                    ),
                );
            }

            if (array_key_exists($this->mess["IBLOCK_XML2_PICTURE"], $arXMLElement)) {
                $rsFiles = $this->_xml_file->GetList(
                    array("ID" => "asc"),
                    array("PARENT_ID" => $arParent["ID"], "NAME" => $this->mess["IBLOCK_XML2_PICTURE"])
                );
                $arFile = $rsFiles->Fetch();
                if ($arFile) {
                    $description = "";
                    if (strlen($arFile["ATTRIBUTES"])) {
                        $arAttributes = unserialize($arFile["ATTRIBUTES"]);
                        if (is_array($arAttributes) && array_key_exists($this->mess["IBLOCK_XML2_DESCRIPTION"], $arAttributes))
                            $description = $arAttributes[$this->mess["IBLOCK_XML2_DESCRIPTION"]];
                    }

                    if (strlen($arFile["VALUE"]) > 0) {


                        $arElement["DETAIL_PICTURE"] = $this->ResizePicture($arFile["VALUE"], $this->detail, array("DETAIL_PICTURE", $this->PROPERTY_MAP["CML2_PICTURES"]));
                        $arFileBefore = CFile::GetFileArray($arDBElement["DETAIL_PICTURE"]);

                        //Если размер картинки не изменился не обновляем картинку
                        if (isset($arFileBefore['FILE_SIZE']) && isset($arElement["DETAIL_PICTURE"]['size']) && $arFileBefore['FILE_SIZE'] == $arElement["DETAIL_PICTURE"]['size']) {
                            unset($arElement["DETAIL_PICTURE"]);
                        }


                        if (is_array($arElement["DETAIL_PICTURE"])) {
                            $arElement["DETAIL_PICTURE"]["description"] = $description;
                            $this->arFileDescriptionsMap[$arFile["VALUE"]][] = &$arElement["DETAIL_PICTURE"]["description"];
                        }

                        if (is_array($this->preview)) {
                            $arElement["PREVIEW_PICTURE"] = $this->ResizePicture($arFile["VALUE"], $this->preview, "PREVIEW_PICTURE");
                            if (is_array($arElement["PREVIEW_PICTURE"])) {
                                $arElement["PREVIEW_PICTURE"]["description"] = $description;
                                $this->arFileDescriptionsMap[$arFile["VALUE"]][] = &$arElement["PREVIEW_PICTURE"]["description"];
                            }
                        }
                    } else {
                        $arElement["DETAIL_PICTURE"] = $this->MakeFileArray($this->_xml_file->GetAllChildrenArray($arFile["ID"]));

                        if (is_array($arElement["DETAIL_PICTURE"])) {
                            $arElement["DETAIL_PICTURE"]["description"] = $description;
                        }
                    }

                    $prop_id = $this->PROPERTY_MAP["CML2_PICTURES"];
                    if ($prop_id > 0) {
                        $i = 1;
                        while ($arFile = $rsFiles->Fetch()) {
                            $description = "";
                            if (strlen($arFile["ATTRIBUTES"])) {
                                $arAttributes = unserialize($arFile["ATTRIBUTES"]);
                                if (is_array($arAttributes) && array_key_exists($this->mess["IBLOCK_XML2_DESCRIPTION"], $arAttributes))
                                    $description = $arAttributes[$this->mess["IBLOCK_XML2_DESCRIPTION"]];
                            }

                            if (strlen($arFile["VALUE"]) > 0)
                                $arPropFile = $this->ResizePicture($arFile["VALUE"], $this->detail, $this->PROPERTY_MAP["CML2_PICTURES"], "DETAIL_PICTURE");
                            else
                                $arPropFile = $this->MakeFileArray($this->_xml_file->GetAllChildrenArray($arFile["ID"]));

                            if (is_array($arPropFile)) {
                                $arPropFile = array(
                                    "VALUE" => $arPropFile,
                                    "DESCRIPTION" => $description,
                                );
                            }
                            $arElement["PROPERTY_VALUES"][$prop_id]["n" . $i] = $arPropFile;
                            if (strlen($arFile["VALUE"]) > 0)
                                $this->arFileDescriptionsMap[$arFile["VALUE"]][] = &$arElement["PROPERTY_VALUES"][$prop_id]["n" . $i]["DESCRIPTION"];
                            $i++;
                        }

                        if (is_array($arElement["PROPERTY_VALUES"][$prop_id])) {
                            foreach ($arElement["PROPERTY_VALUES"][$prop_id] as $PROPERTY_VALUE_ID => $PROPERTY_VALUE) {
                                if (!$PROPERTY_VALUE_ID)
                                    unset($arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID]);
                                elseif (substr($PROPERTY_VALUE_ID, 0, 1) !== "n")
                                    $arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID] = array(
                                        "tmp_name" => "",
                                        "del" => "Y",
                                    );
                            }
                            unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                        }
                    }
                }
            }

            $cleanCml2FilesProperty = false;
            if (
                array_key_exists($this->mess["IBLOCK_XML2_FILE"], $arXMLElement)
                && strlen($this->PROPERTY_MAP["CML2_FILES"]) > 0
            ) {
                $prop_id = $this->PROPERTY_MAP["CML2_FILES"];
                $rsFiles = $this->_xml_file->GetList(
                    array("ID" => "asc"),
                    array("PARENT_ID" => $arParent["ID"], "NAME" => $this->mess["IBLOCK_XML2_FILE"])
                );
                $i = 1;
                while ($arFile = $rsFiles->Fetch()) {

                    if (strlen($arFile["VALUE"]) > 0)
                        $file = $this->MakeFileArray($arFile["VALUE"], array($prop_id));
                    else
                        $file = $this->MakeFileArray($this->_xml_file->GetAllChildrenArray($arFile["ID"]));

                    $arElement["PROPERTY_VALUES"][$prop_id]["n" . $i] = array(
                        "VALUE" => $file,
                        "DESCRIPTION" => $file["description"],
                    );
                    if (strlen($arFile["ATTRIBUTES"])) {
                        $desc = unserialize($arFile["ATTRIBUTES"]);
                        if (is_array($desc) && array_key_exists($this->mess["IBLOCK_XML2_DESCRIPTION"], $desc))
                            $arElement["PROPERTY_VALUES"][$prop_id]["n" . $i]["DESCRIPTION"] = $desc[$this->mess["IBLOCK_XML2_DESCRIPTION"]];
                    }
                    $i++;
                }
                $cleanCml2FilesProperty = true;
            }

            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_GROUPS"]])) {
                $arElement["IBLOCK_SECTION"] = array();
                foreach ($arXMLElement[$this->mess["IBLOCK_XML2_GROUPS"]] as $value) {
                    if (array_key_exists($value, $this->SECTION_MAP))
                        $arElement["IBLOCK_SECTION"][] = $this->SECTION_MAP[$value];
                }
            }

            if (array_key_exists($this->mess["IBLOCK_XML2_PRICES"], $arXMLElement)) {//Collect price information for future use
                $arElement["PRICES"] = array();
                if (is_array($arXMLElement[$this->mess["IBLOCK_XML2_PRICES"]])) {


                    foreach ($arXMLElement[$this->mess["IBLOCK_XML2_PRICES"]] as $price) {
                        if (isset($price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]]) && array_key_exists($price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]], $this->PRICES_MAP)) {
                            $price["PRICE"] = $this->PRICES_MAP[$price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]]];
                            $arElement["PRICES"][] = $price;
                        }
                    }
                }

                $arElement["DISCOUNTS"] = array();
                if (isset($arXMLElement[$this->mess["IBLOCK_XML2_DISCOUNTS"]])) {
                    foreach ($arXMLElement[$this->mess["IBLOCK_XML2_DISCOUNTS"]] as $discount) {
                        if (
                            isset($discount[$this->mess["IBLOCK_XML2_DISCOUNT_CONDITION"]])
                            && $discount[$this->mess["IBLOCK_XML2_DISCOUNT_CONDITION"]] === $this->mess["IBLOCK_XML2_DISCOUNT_COND_VOLUME"]
                        ) {
                            $discount_value = $this->ToInt($discount[$this->mess["IBLOCK_XML2_DISCOUNT_COND_VALUE"]]);
                            $discount_percent = $this->ToFloat($discount[$this->mess["IBLOCK_XML2_DISCOUNT_COND_PERCENT"]]);
                            if ($discount_value > 0 && $discount_percent > 0)
                                $arElement["DISCOUNTS"][$discount_value] = $discount_percent;
                        }
                    }
                }
            }

            if (array_key_exists($this->mess["IBLOCK_XML2_AMOUNT"], $arXMLElement)) {
                $arElementTmp = array();
                $arElement["QUANTITY_RESERVED"] = 0;
                if ($arElement["ID"])
                    $arElementTmp = CCatalogProduct::GetById($arElement["ID"]);
                if (is_array($arElementTmp) && !empty($arElementTmp) && isset($arElementTmp["QUANTITY_RESERVED"]))
                    $arElement["QUANTITY_RESERVED"] = $arElementTmp["QUANTITY_RESERVED"];
                $arElement["QUANTITY"] = $this->ToFloat($arXMLElement[$this->mess["IBLOCK_XML2_AMOUNT"]]) - doubleval($arElement["QUANTITY_RESERVED"]);
            }

            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_ITEM_ATTRIBUTES"]])) {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_ATTRIBUTES"]] = array();
                $i = 0;
                foreach ($arXMLElement[$this->mess["IBLOCK_XML2_ITEM_ATTRIBUTES"]] as $value) {
                    $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_ATTRIBUTES"]]["n" . $i] = array(
                        "VALUE" => $value[$this->mess["IBLOCK_XML2_VALUE"]],
                        "DESCRIPTION" => $value[$this->mess["IBLOCK_XML2_NAME"]],
                    );
                    $i++;
                }
            }

            $i = 0;
            $weightKey = false;

            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_TRAITS_VALUES"]])) {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TRAITS"]] = array();
                $i = 0;
                foreach ($arXMLElement[$this->mess["IBLOCK_XML2_TRAITS_VALUES"]] as $value) {
                    if (
                        !array_key_exists("PREVIEW_TEXT", $arElement)
                        && $value[$this->mess["IBLOCK_XML2_NAME"]] == $this->mess["IBLOCK_XML2_FULL_TITLE2"]
                    ) {
                        $arElement["PREVIEW_TEXT"] = $value[$this->mess["IBLOCK_XML2_VALUE"]];
                        if (strpos($arElement["PREVIEW_TEXT"], "<") !== false)
                            $arElement["PREVIEW_TEXT_TYPE"] = "html";
                        else
                            $arElement["PREVIEW_TEXT_TYPE"] = "text";
                    } elseif (
                        $value[$this->mess["IBLOCK_XML2_NAME"]] == $this->mess["IBLOCK_XML2_HTML_DESCRIPTION"]
                    ) {
                        if (strlen($value[$this->mess["IBLOCK_XML2_VALUE"]]) > 0) {
                            $arElement["DETAIL_TEXT"] = $value[$this->mess["IBLOCK_XML2_VALUE"]];
                            $arElement["DETAIL_TEXT_TYPE"] = "html";
                        }
                    } elseif (
                        $value[$this->mess["IBLOCK_XML2_NAME"]] == $this->mess["IBLOCK_XML2_FILE"]
                    ) {
                        if (strlen($value[$this->mess["IBLOCK_XML2_VALUE"]]) > 0) {
                            $prop_id = $this->PROPERTY_MAP["CML2_FILES"];

                            $j = 1;
                            while (isset($arElement["PROPERTY_VALUES"][$prop_id]["n" . $j]))
                                $j++;

                            $file = $this->MakeFileArray($value[$this->mess["IBLOCK_XML2_VALUE"]], array($prop_id));
                            if (is_array($file)) {
                                $arElement["PROPERTY_VALUES"][$prop_id]["n" . $j] = array(
                                    "VALUE" => $file,
                                    "DESCRIPTION" => "",
                                );
                                unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                                $this->arFileDescriptionsMap[$value[$this->mess["IBLOCK_XML2_VALUE"]]][] = &$arElement["PROPERTY_VALUES"][$prop_id]["n" . $j]["DESCRIPTION"];
                                $cleanCml2FilesProperty = true;
                            }
                        }
                    } elseif (
                        $value[$this->mess["IBLOCK_XML2_NAME"]] == $this->mess["IBLOCK_XML2_FILE_DESCRIPTION"]
                    ) {
                        if (strlen($value[$this->mess["IBLOCK_XML2_VALUE"]]) > 0) {
                            list($fileName, $description) = explode("#", $value[$this->mess["IBLOCK_XML2_VALUE"]]);
                            if (isset($this->arFileDescriptionsMap[$fileName])) {
                                foreach ($this->arFileDescriptionsMap[$fileName] as $k => $tmp)
                                    $this->arFileDescriptionsMap[$fileName][$k] = $description;
                            }
                        }
                    } else {
                        if ($value[$this->mess["IBLOCK_XML2_NAME"]] == $this->mess["IBLOCK_XML2_WEIGHT"]) {
                            $arElement["BASE_WEIGHT"] = $this->ToFloat($value[$this->mess["IBLOCK_XML2_VALUE"]]) * 1000;
                            $weightKey = "n" . $i;
                        }

                        $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TRAITS"]]["n" . $i] = array(
                            "VALUE" => $value[$this->mess["IBLOCK_XML2_VALUE"]],
                            "DESCRIPTION" => $value[$this->mess["IBLOCK_XML2_NAME"]],
                        );
                        $i++;
                    }
                }
            }

            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_WEIGHT"]])) {
                if ($weightKey !== false) {
                } elseif (!isset($arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TRAITS"]])) {
                    $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TRAITS"]] = array();
                    $weightKey = "n0";
                } else // $weightKey === false && isset($arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TRAITS"]])
                {
                    $weightKey = "n" . $i;
                }
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TRAITS"]][$weightKey] = array(
                    "VALUE" => $arXMLElement[$this->mess["IBLOCK_XML2_WEIGHT"]],
                    "DESCRIPTION" => $this->mess["IBLOCK_XML2_WEIGHT"],
                );
                $arElement["BASE_WEIGHT"] = $this->ToFloat($arXMLElement[$this->mess["IBLOCK_XML2_WEIGHT"]]) * 1000;
            }

            if ($cleanCml2FilesProperty) {
                $prop_id = $this->PROPERTY_MAP["CML2_FILES"];
                if (is_array($arElement["PROPERTY_VALUES"][$prop_id])) {
                    foreach ($arElement["PROPERTY_VALUES"][$prop_id] as $PROPERTY_VALUE_ID => $PROPERTY_VALUE) {
                        if (!$PROPERTY_VALUE_ID)
                            unset($arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID]);
                        elseif (substr($PROPERTY_VALUE_ID, 0, 1) !== "n")
                            $arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID] = array(
                                "tmp_name" => "",
                                "del" => "Y",
                            );
                    }
                    unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                }
            }

            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_TAXES_VALUES"]])) {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TAXES"]] = array();
                $i = 0;
                foreach ($arXMLElement[$this->mess["IBLOCK_XML2_TAXES_VALUES"]] as $value) {
                    $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_TAXES"]]["n" . $i] = array(
                        "VALUE" => $value[$this->mess["IBLOCK_XML2_TAX_VALUE"]],
                        "DESCRIPTION" => $value[$this->mess["IBLOCK_XML2_NAME"]],
                    );
                    $i++;
                }
            }

            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_BASE_UNIT"]])) {
                $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BASE_UNIT"]] = array(
                    "n0" => $this->convertBaseUnitFromXmlToPropertyValue($arXMLElement[$this->mess["IBLOCK_XML2_BASE_UNIT"]]),
                );
            }

            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_PROPERTIES_VALUES"]])) {
                foreach ($arXMLElement[$this->mess["IBLOCK_XML2_PROPERTIES_VALUES"]] as $value) {
                    if (!array_key_exists($this->mess["IBLOCK_XML2_ID"], $value))
                        continue;

                    $prop_id = $value[$this->mess["IBLOCK_XML2_ID"]];
                    $prop_id = CMLNameConnector::xid(
                        'prop',
                        $prop_id
                    );
                    unset($value[$this->mess["IBLOCK_XML2_ID"]]);

                    //Handle properties which is actually element fields
                    if (!array_key_exists($prop_id, $this->PROPERTY_MAP)) {
                        if ($prop_id == "CML2_CODE")
                            $arElement["CODE"] = isset($value[$this->mess["IBLOCK_XML2_VALUE"]]) ? $value[$this->mess["IBLOCK_XML2_VALUE"]] : "";
                        elseif ($prop_id == "CML2_ACTIVE") {
                            $value = array_pop($value);
                            $arElement["ACTIVE"] = ($value == "true") || intval($value) ? "Y" : "N";
                        } elseif ($prop_id == "CML2_SORT")
                            $arElement["SORT"] = array_pop($value);
                        elseif ($prop_id == "CML2_ACTIVE_FROM")
                            $arElement["ACTIVE_FROM"] = CDatabase::FormatDate(array_pop($value), "YYYY-MM-DD HH:MI:SS", CLang::GetDateFormat("FULL"));
                        elseif ($prop_id == "CML2_ACTIVE_TO")
                            $arElement["ACTIVE_TO"] = CDatabase::FormatDate(array_pop($value), "YYYY-MM-DD HH:MI:SS", CLang::GetDateFormat("FULL"));
                        elseif ($prop_id == "CML2_PREVIEW_TEXT") {
                            if (array_key_exists($this->mess["IBLOCK_XML2_VALUE"], $value)) {
                                if (isset($value[$this->mess["IBLOCK_XML2_VALUE"]]))
                                    $arElement["PREVIEW_TEXT"] = $value[$this->mess["IBLOCK_XML2_VALUE"]];
                                else
                                    $arElement["PREVIEW_TEXT"] = "";

                                if (isset($value[$this->mess["IBLOCK_XML2_TYPE"]]))
                                    $arElement["PREVIEW_TEXT_TYPE"] = $value[$this->mess["IBLOCK_XML2_TYPE"]];
                                else
                                    $arElement["PREVIEW_TEXT_TYPE"] = "html";
                            }
                        } elseif ($prop_id == "CML2_DETAIL_TEXT") {
                            if (array_key_exists($this->mess["IBLOCK_XML2_VALUE"], $value)) {
                                if (isset($value[$this->mess["IBLOCK_XML2_VALUE"]]))
                                    $arElement["DETAIL_TEXT"] = $value[$this->mess["IBLOCK_XML2_VALUE"]];
                                else
                                    $arElement["DETAIL_TEXT"] = "";

                                if (isset($value[$this->mess["IBLOCK_XML2_TYPE"]]))
                                    $arElement["DETAIL_TEXT_TYPE"] = $value[$this->mess["IBLOCK_XML2_TYPE"]];
                                else
                                    $arElement["DETAIL_TEXT_TYPE"] = "html";
                            }
                        } elseif ($prop_id == "CML2_PREVIEW_PICTURE") {
                            if (!is_array($this->preview) || !$arElement["PREVIEW_PICTURE"]) {
                                $arElement["PREVIEW_PICTURE"] = $this->MakeFileArray($value[$this->mess["IBLOCK_XML2_VALUE"]], array("PREVIEW_PICTURE"));
                                $arElement["PREVIEW_PICTURE"]["COPY_FILE"] = "Y";
                            }
                        }

                        continue;
                    }

                    $prop_id = $this->PROPERTY_MAP[$prop_id];
                    $prop_type = $this->arProperties[$prop_id]["PROPERTY_TYPE"];

                    if (!array_key_exists($prop_id, $arElement["PROPERTY_VALUES"]))
                        $arElement["PROPERTY_VALUES"][$prop_id] = array();

                    //check for bitrix extended format
                    if (array_key_exists($this->mess["IBLOCK_XML2_PROPERTY_VALUE"], $value)) {
                        $i = 1;
                        $strPV = $this->mess["IBLOCK_XML2_PROPERTY_VALUE"];
                        $lPV = strlen($strPV);
                        foreach ($value as $k => $prop_value) {
                            if (substr($k, 0, $lPV) === $strPV) {
                                if (array_key_exists($this->mess["IBLOCK_XML2_SERIALIZED"], $prop_value))
                                    $prop_value[$this->mess["IBLOCK_XML2_VALUE"]] = $this->Unserialize($prop_value[$this->mess["IBLOCK_XML2_VALUE"]]);
                                if ($prop_type == "F") {
                                    $prop_value[$this->mess["IBLOCK_XML2_VALUE"]] = $this->MakeFileArray($prop_value[$this->mess["IBLOCK_XML2_VALUE"]], array($prop_id));
                                } elseif ($prop_type == "G")
                                    $prop_value[$this->mess["IBLOCK_XML2_VALUE"]] = $this->GetSectionByXML_ID($this->arProperties[$prop_id]["LINK_IBLOCK_ID"], $prop_value[$this->mess["IBLOCK_XML2_VALUE"]]);
                                elseif ($prop_type == "E")
                                    $prop_value[$this->mess["IBLOCK_XML2_VALUE"]] = $this->GetElementByXML_ID($this->arProperties[$prop_id]["LINK_IBLOCK_ID"], $prop_value[$this->mess["IBLOCK_XML2_VALUE"]]);
                                elseif ($prop_type == "L")
                                    $prop_value[$this->mess["IBLOCK_XML2_VALUE"]] = $this->GetEnumByXML_ID($this->arProperties[$prop_id]["ID"], CMLNameConnector::xid(
                                        'enum',
                                        $prop_value[$this->mess["IBLOCK_XML2_VALUE"]]
                                    ));

                                if (array_key_exists("bOld", $arElement["PROPERTY_VALUES"][$prop_id])) {
                                    if ($prop_type == "F") {
                                        foreach ($arElement["PROPERTY_VALUES"][$prop_id] as $PROPERTY_VALUE_ID => $PROPERTY_VALUE)
                                            $arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID] = array(
                                                "tmp_name" => "",
                                                "del" => "Y",
                                            );
                                        unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                                    } else
                                        $arElement["PROPERTY_VALUES"][$prop_id] = array();
                                }

                                $arElement["PROPERTY_VALUES"][$prop_id]["n" . $i] = array(
                                    "VALUE" => $prop_value[$this->mess["IBLOCK_XML2_VALUE"]],
                                    "DESCRIPTION" => $prop_value[$this->mess["IBLOCK_XML2_DESCRIPTION"]],
                                );
                            }
                            $i++;
                        }
                    } else {
                        if ($prop_type == "L" && !array_key_exists($this->mess["IBLOCK_XML2_VALUE_ID"], $value))
                            $l_key = $this->mess["IBLOCK_XML2_VALUE"];
                        else
                            $l_key = $this->mess["IBLOCK_XML2_VALUE_ID"];

                        $i = 0;
                        foreach ($value as $k => $prop_value) {
                            if (array_key_exists("bOld", $arElement["PROPERTY_VALUES"][$prop_id])) {
                                if ($prop_type == "F") {
                                    foreach ($arElement["PROPERTY_VALUES"][$prop_id] as $PROPERTY_VALUE_ID => $PROPERTY_VALUE)
                                        $arElement["PROPERTY_VALUES"][$prop_id][$PROPERTY_VALUE_ID] = array(
                                            "tmp_name" => "",
                                            "del" => "Y",
                                        );
                                    unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                                } else {
                                    $arElement["PROPERTY_VALUES"][$prop_id] = array();
                                }
                            }

                            if ($prop_type == "L" && $k == $l_key) {
                                $prop_value = $this->GetEnumByXML_ID($this->arProperties[$prop_id]["ID"], CMLNameConnector::xid('enum', $prop_value));
                            } elseif ($prop_type == "N" && isset($this->next_step["sdp"])) {
                                if (strlen($prop_value) > 0)
                                    $prop_value = $this->ToFloat($prop_value);
                            }

                            $arElement["PROPERTY_VALUES"][$prop_id]["n" . $i] = array(
                                "VALUE" => $prop_value,
                                "DESCRIPTION" => false,
                            );
                            $i++;
                        }
                    }
                }
            }

            //If there is no BaseUnit specified check prices for it
            if (
                (
                    !array_key_exists($this->PROPERTY_MAP["CML2_BASE_UNIT"], $arElement["PROPERTY_VALUES"])
                    || (
                        is_array($arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BASE_UNIT"]])
                        && array_key_exists("bOld", $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BASE_UNIT"]])
                    )
                )
                && isset($arXMLElement[$this->mess["IBLOCK_XML2_PRICES"]])
            ) {
                foreach ($arXMLElement[$this->mess["IBLOCK_XML2_PRICES"]] as $price) {
                    if (
                        isset($price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]])
                        && array_key_exists($price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]], $this->PRICES_MAP)
                        && array_key_exists($this->mess["IBLOCK_XML2_MEASURE"], $price)
                    ) {
                        $arElement["PROPERTY_VALUES"][$this->PROPERTY_MAP["CML2_BASE_UNIT"]] = array(
                            "n0" => $this->convertBaseUnitFromXmlToPropertyValue($price[$this->mess["IBLOCK_XML2_MEASURE"]]),
                        );
                        break;
                    }
                }
            }

            if ($arDBElement) {
                foreach ($arElement["PROPERTY_VALUES"] as $prop_id => $prop) {
                    if (is_array($arElement["PROPERTY_VALUES"][$prop_id]) && array_key_exists("bOld", $arElement["PROPERTY_VALUES"][$prop_id])) {
                        if ($this->arProperties[$prop_id]["PROPERTY_TYPE"] == "F")
                            unset($arElement["PROPERTY_VALUES"][$prop_id]);
                        else
                            unset($arElement["PROPERTY_VALUES"][$prop_id]["bOld"]);
                    }
                }

                if (intval($arElement["MODIFIED_BY"]) <= 0 && $USER_ID > 0)
                    $arElement["MODIFIED_BY"] = $USER_ID;

                if (!array_key_exists("CODE", $arElement) && is_array($this->translit_on_update)) {
                    $arElement["CODE"] = CUtil::translit($arElement["NAME"], LANGUAGE_ID, $this->translit_on_update);
                    //Check if name was not changed in a way to update CODE
                    if (substr($arDBElement["CODE"], 0, strlen($arElement["CODE"])) === $arElement["CODE"])
                        unset($arElement["CODE"]);
                    else
                        $arElement["CODE"] = $this->CheckElementCode($this->next_step["IBLOCK_ID"], $arElement["CODE"]);
                }

                $obElement->Update($arDBElement["ID"], $arElement, $bWF, true, $this->iblock_resize);
                //In case element was not active in database we have to activate its offers
                if ($arDBElement["ACTIVE"] != "Y") {
                    $this->ChangeOffersStatus($arDBElement["ID"], "Y", $bWF);
                }
                $arElement["ID"] = $arDBElement["ID"];
                if ($arElement["ID"]) {
                    $counter["UPD"]++;
                } else {
                    $this->LAST_ERROR = $obElement->LAST_ERROR;
                    $counter["ERR"]++;
                }
            } else {
                if (!array_key_exists("CODE", $arElement) && is_array($this->translit_on_add))
                    $arElement["CODE"] = $this->CheckElementCode($this->next_step["IBLOCK_ID"], CUtil::translit($arElement["NAME"], LANGUAGE_ID, $this->translit_on_add));

                $arElement["IBLOCK_ID"] = $this->next_step["IBLOCK_ID"];
                $this->fillDefaultPropertyValues($arElement, $this->arProperties);

                $arElement["ID"] = $obElement->Add($arElement, $bWF, true, $this->iblock_resize);
                if ($arElement["ID"]) {
                    $counter["ADD"]++;
                } else {
                    $this->LAST_ERROR = $obElement->LAST_ERROR;
                    $counter["ERR"]++;
                }
            }
        } elseif (array_key_exists($this->mess["IBLOCK_XML2_PRICES"], $arXMLElement)) {
            //Collect price information for future use
            $arElement["PRICES"] = array();
            if (is_array($arXMLElement[$this->mess["IBLOCK_XML2_PRICES"]])) {
                foreach ($arXMLElement[$this->mess["IBLOCK_XML2_PRICES"]] as $price) {


                    if (isset($price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]]) && array_key_exists($price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]], $this->PRICES_MAP)) {
                        $price["PRICE"] = $this->PRICES_MAP[$price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]]];
                        $arElement["PRICES"][] = $price;
                    }
                }
            }

            $arElement["DISCOUNTS"] = array();
            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_DISCOUNTS"]])) {
                foreach ($arXMLElement[$this->mess["IBLOCK_XML2_DISCOUNTS"]] as $discount) {
                    if (
                        isset($discount[$this->mess["IBLOCK_XML2_DISCOUNT_CONDITION"]])
                        && $discount[$this->mess["IBLOCK_XML2_DISCOUNT_CONDITION"]] === $this->mess["IBLOCK_XML2_DISCOUNT_COND_VOLUME"]
                    ) {
                        $discount_value = $this->ToInt($discount[$this->mess["IBLOCK_XML2_DISCOUNT_COND_VALUE"]]);
                        $discount_percent = $this->ToFloat($discount[$this->mess["IBLOCK_XML2_DISCOUNT_COND_PERCENT"]]);
                        if ($discount_value > 0 && $discount_percent > 0)
                            $arElement["DISCOUNTS"][$discount_value] = $discount_percent;
                    }
                }
            }

            if ($arDBElement) {
                $arElement["ID"] = $arDBElement["ID"];
                $counter["UPD"]++;
            }
        }

        if (isset($arXMLElement[$this->mess["IBLOCK_XML2_STORE_AMOUNT_LIST"]])) {
            $arElement["STORE_AMOUNT"] = array();
            foreach ($arXMLElement[$this->mess["IBLOCK_XML2_STORE_AMOUNT_LIST"]] as $storeAmount) {
                if (isset($storeAmount[$this->mess["IBLOCK_XML2_STORE_ID"]])) {
                    $storeXMLID = $storeAmount[$this->mess["IBLOCK_XML2_STORE_ID"]];
                    $amount = $this->ToFloat($storeAmount[$this->mess["IBLOCK_XML2_AMOUNT"]]);
                    $arElement["STORE_AMOUNT"][$storeXMLID] = $amount;
                }
            }
        } elseif (
            array_key_exists($this->mess["IBLOCK_XML2_STORES"], $arXMLElement)
            || array_key_exists($this->mess["IBLOCK_XML2_STORE"], $arXMLElement)
        ) {
            $arElement["STORE_AMOUNT"] = array();
            $rsStores = $this->_xml_file->GetList(
                array("ID" => "asc"),
                array(
                    "><LEFT_MARGIN" => array($arParent["LEFT_MARGIN"], $arParent["RIGHT_MARGIN"]),
                    "NAME" => $this->mess["IBLOCK_XML2_STORE"],
                ),
                array("ID", "ATTRIBUTES")
            );
            while ($arStore = $rsStores->Fetch()) {
                if (strlen($arStore["ATTRIBUTES"]) > 0) {
                    $info = unserialize($arStore["ATTRIBUTES"]);
                    if (
                        is_array($info)
                        && array_key_exists($this->mess["IBLOCK_XML2_STORE_ID"], $info)
                        && array_key_exists($this->mess["IBLOCK_XML2_STORE_AMOUNT"], $info)
                    ) {
                        $arElement["STORE_AMOUNT"][$info[$this->mess["IBLOCK_XML2_STORE_ID"]]] = $this->ToFloat($info[$this->mess["IBLOCK_XML2_STORE_AMOUNT"]]);
                    }
                }
            }
        }

        if ($bMatch && $this->use_crc) {
            //nothing to do
        } elseif ($arElement["ID"] && $this->bCatalog && $this->next_step["bOffer"]) {
            $CML_LINK = $this->PROPERTY_MAP["CML2_LINK"];

            $arProduct = array(
                "ID" => $arElement["ID"],
            );

            if (isset($arElement["QUANTITY"]))
                $arProduct["QUANTITY"] = $arElement["QUANTITY"];
            elseif (isset($arElement["STORE_AMOUNT"]) && !empty($arElement["STORE_AMOUNT"]))
                $arProduct["QUANTITY"] = array_sum($arElement["STORE_AMOUNT"]);

            $CML_LINK_ELEMENT = $arElement["PROPERTY_VALUES"][$CML_LINK];
            if (is_array($CML_LINK_ELEMENT) && isset($CML_LINK_ELEMENT["n0"])) {
                $CML_LINK_ELEMENT = $CML_LINK_ELEMENT["n0"];
            }
            if (is_array($CML_LINK_ELEMENT) && isset($CML_LINK_ELEMENT["VALUE"])) {
                $CML_LINK_ELEMENT = $CML_LINK_ELEMENT["VALUE"];
            }

            if (isset($arElement["BASE_WEIGHT"])) {
                $arProduct["WEIGHT"] = $arElement["BASE_WEIGHT"];
            } elseif ($CML_LINK_ELEMENT > 0) {
                $rsWeight = CIBlockElement::GetProperty($this->arProperties[$CML_LINK]["LINK_IBLOCK_ID"], $CML_LINK_ELEMENT, array(), array("CODE" => "CML2_TRAITS"));
                while ($arWeight = $rsWeight->Fetch()) {
                    if ($arWeight["DESCRIPTION"] == $this->mess["IBLOCK_XML2_WEIGHT"])
                        $arProduct["WEIGHT"] = $this->ToFloat($arWeight["VALUE"]) * 1000;
                }
            }

            if ($CML_LINK_ELEMENT > 0) {
                $rsUnit = CIBlockElement::GetProperty($this->arProperties[$CML_LINK]["LINK_IBLOCK_ID"], $CML_LINK_ELEMENT, array(), array("CODE" => "CML2_BASE_UNIT"));
                while ($arUnit = $rsUnit->Fetch()) {
                    if ($arUnit["DESCRIPTION"] > 0)
                        $arProduct["MEASURE"] = $arUnit["DESCRIPTION"];
                }
            }

            if (isset($arElement["PRICES"])) {


                //Here start VAT handling

                //Check if all the taxes exists in BSM catalog
                $arTaxMap = array();
                $rsTaxProperty = CIBlockElement::GetProperty($this->arProperties[$CML_LINK]["LINK_IBLOCK_ID"], $arElement["PROPERTY_VALUES"][$CML_LINK], "sort", "asc", array("CODE" => "CML2_TAXES"));
                while ($arTaxProperty = $rsTaxProperty->Fetch()) {
                    if (
                        strlen($arTaxProperty["VALUE"]) > 0
                        && strlen($arTaxProperty["DESCRIPTION"]) > 0
                        && !array_key_exists($arTaxProperty["DESCRIPTION"], $arTaxMap)
                    ) {
                        $arTaxMap[$arTaxProperty["DESCRIPTION"]] = array(
                            "RATE" => $this->ToFloat($arTaxProperty["VALUE"]),
                            "ID" => $this->CheckTax($arTaxProperty["DESCRIPTION"], $this->ToFloat($arTaxProperty["VALUE"])),
                        );
                    }
                }

                //First find out if all the prices have TAX_IN_SUM true
                $TAX_IN_SUM = "Y";
                foreach ($arElement["PRICES"] as $price) {
                    if ($price["PRICE"]["TAX_IN_SUM"] !== "true") {
                        $TAX_IN_SUM = "N";
                        break;
                    }
                }
                //If there was found not included tax we'll make sure
                //that all prices has the same flag
                if ($TAX_IN_SUM === "N") {
                    foreach ($arElement["PRICES"] as $price) {
                        if ($price["PRICE"]["TAX_IN_SUM"] !== "false") {
                            $TAX_IN_SUM = "Y";
                            break;
                        }
                    }
                    //Check if there is a mix of tax in sum
                    //and correct it by recalculating all the prices
                    if ($TAX_IN_SUM === "Y") {
                        foreach ($arElement["PRICES"] as $key => $price) {
                            if ($price["PRICE"]["TAX_IN_SUM"] !== "true") {
                                $TAX_NAME = $price["PRICE"]["TAX_NAME"];
                                if (array_key_exists($TAX_NAME, $arTaxMap)) {
                                    $PRICE_WO_TAX = $this->ToFloat($price[$this->mess["IBLOCK_XML2_PRICE_FOR_ONE"]]);
                                    $PRICE = $PRICE_WO_TAX + ($PRICE_WO_TAX / 100.0 * $arTaxMap[$TAX_NAME]["RATE"]);
                                    $arElement["PRICES"][$key][$this->mess["IBLOCK_XML2_PRICE_FOR_ONE"]] = $PRICE;
                                }
                            }
                        }
                    }
                }
                foreach ($arElement["PRICES"] as $price) {
                    $TAX_NAME = $price["PRICE"]["TAX_NAME"];
                    if (array_key_exists($TAX_NAME, $arTaxMap)) {
                        $arProduct["VAT_ID"] = $arTaxMap[$TAX_NAME]["ID"];
                        break;
                    }
                }
                $arProduct["VAT_INCLUDED"] = $TAX_IN_SUM;
            }

            CCatalogProduct::Add($arProduct);


            if (isset($arElement["PRICES"]))
                $this->SetProductPrice($arElement["ID"], $arElement["PRICES"], $arElement["DISCOUNTS"]);

            if (isset($arElement["STORE_AMOUNT"]))
                $this->ImportStoresAmount($arElement["STORE_AMOUNT"], $arElement["ID"], $counter);
        }


        return $arElement["ID"];
    }

    function ImportSections()
    {


        //if($this->next_step["XML_SECTIONS_PARENT"])
        //{


        $rs = $this->_xml_file->GetList(
            array("ID" => "asc"),
            array("PARENT_ID" => $this->next_step["XML_SECTIONS_PARENT"]),
            array("ID", "NAME", "VALUE")
        );

        $arID = array();
        while ($ar = $rs->Fetch())
            $arID[] = $ar["ID"];


        if ($this->skip_root_section && (count($arID) == 1)) {
            $rs = $this->_xml_file->GetList(
                array("ID" => "asc"),
                array("PARENT_ID" => $arID[0]),
                array("ID", "NAME", "VALUE")
            );

            $XML_SECTIONS_PARENT = false;
            while ($ar = $rs->Fetch())
                if ($ar["NAME"] == $this->mess["IBLOCK_XML2_GROUPS"])
                    $XML_SECTIONS_PARENT = $ar["ID"];

            $arID = array();
            if ($XML_SECTIONS_PARENT > 0) {
                $rs = $this->_xml_file->GetList(
                    array("ID" => "asc"),
                    array("PARENT_ID" => $XML_SECTIONS_PARENT),
                    array("ID", "NAME", "VALUE")
                );
                while ($ar = $rs->Fetch())
                    $arID[] = $ar["ID"];
            }
        }

        foreach ($arID as $id) {
            $result = $this->ImportSection($id, $this->next_step["IBLOCK_ID"], false);
            if ($result !== true)
                return $result;
        }
        //}

        return true;
    }

    function ImportSection($xml_tree_id, $IBLOCK_ID, $parent_section_id)
    {

        /** @var CUserTypeManager $USER_FIELD_MANAGER */
        global $USER_FIELD_MANAGER;
        /** @var CDatabase $DB */
        global $DB;

        static $arUserFields;
        if ($parent_section_id === false) {
            $arUserFields = array();
            foreach ($USER_FIELD_MANAGER->GetUserFields("IBLOCK_" . $IBLOCK_ID . "_SECTION") as $FIELD_ID => $arField) {
                if (strlen($arField["XML_ID"]) <= 0)
                    $arUserFields[$FIELD_ID] = $arField;
                else
                    $arUserFields[$arField["XML_ID"]] = $arField;
            }
        }

        $this->next_step["section_sort"] += 10;
        $arSection = array(
            "IBLOCK_SECTION_ID" => $parent_section_id,
            "ACTIVE" => "Y",
        );
        $rsS = $this->_xml_file->GetList(
            array("ID" => "asc"),
            array("PARENT_ID" => $xml_tree_id)
        );
        $XML_SECTIONS_PARENT = false;
        $XML_PROPERTIES_PARENT = false;
        $XML_SECTION_PROPERTIES = false;
        $deletedStatus = false;
        while ($arS = $rsS->Fetch()) {
            if (isset($arS["VALUE_CLOB"]))
                $arS["VALUE"] = $arS["VALUE_CLOB"];

            if ($arS["NAME"] == $this->mess["IBLOCK_XML2_ID"])
                $arSection["XML_ID"] = $arS["VALUE"];
            elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_NAME"])
                $arSection["NAME"] = $arS["VALUE"];
            elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_DESCRIPTION"]) {
                $arSection["DESCRIPTION"] = $arS["VALUE"];
                $arSection["DESCRIPTION_TYPE"] = "html";
            } elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_GROUPS"])
                $XML_SECTIONS_PARENT = $arS["ID"];
            elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_PROPERTIES_VALUES"])
                $XML_PROPERTIES_PARENT = $arS["ID"];
            elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_BX_SORT"])
                $arSection["SORT"] = intval($arS["VALUE"]);
            elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_BX_CODE"])
                $arSection["CODE"] = $arS["VALUE"];
            elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_BX_PICTURE"]) {
                if (strlen($arS["VALUE"]) > 0)
                    $arSection["PICTURE"] = $this->MakeFileArray($arS["VALUE"]);
                else
                    $arSection["PICTURE"] = $this->MakeFileArray($this->_xml_file->GetAllChildrenArray($arS["ID"]));
            } elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_BX_DETAIL_PICTURE"]) {
                if (strlen($arS["VALUE"]) > 0)
                    $arSection["DETAIL_PICTURE"] = $this->MakeFileArray($arS["VALUE"]);
                else
                    $arSection["DETAIL_PICTURE"] = $this->MakeFileArray($this->_xml_file->GetAllChildrenArray($arS["ID"]));
            } elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_BX_ACTIVE"])
                $arSection["ACTIVE"] = ($arS["VALUE"] == "true") || intval($arS["VALUE"]) ? "Y" : "N";
            elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_SECTION_PROPERTIES"])
                $XML_SECTION_PROPERTIES = $arS["ID"];
            elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_STATUS"])
                $deletedStatus = $arS["VALUE"] === $this->mess["IBLOCK_XML2_DELETED"];
            elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_INHERITED_TEMPLATES"]) {
                $arSection["IPROPERTY_TEMPLATES"] = array();
                $arTemplates = $this->_xml_file->GetAllChildrenArray($arS["ID"]);
                foreach ($arTemplates as $TEMPLATE) {
                    $id = $TEMPLATE[$this->mess["IBLOCK_XML2_ID"]];
                    $template = $TEMPLATE[$this->mess["IBLOCK_XML2_VALUE"]];
                    if (strlen($id) > 0 && strlen($template) > 0)
                        $arSection["IPROPERTY_TEMPLATES"][$id] = $template;
                }
            } elseif ($arS["NAME"] == $this->mess["IBLOCK_XML2_DELETE_MARK"]) {
                $arSection["ACTIVE"] = ($arS["VALUE"] == "true") || intval($arS["VALUE"]) ? "N" : "Y";
            }
        }

        if ($deletedStatus) {
            $obSection = new CIBlockSection;
            $rsSection = $obSection->GetList(array(), array(
                "IBLOCK_ID" => $IBLOCK_ID,
                "XML_ID" => $arSection["XML_ID"],
            ), false, array("ID"));
            if ($arDBSection = $rsSection->Fetch()) {
                $obSection->Update($arDBSection["ID"], array(
                    "ACTIVE" => "N",
                ));
                $this->_xml_file->Add(array("PARENT_ID" => 0, "LEFT_MARGIN" => $arDBSection["ID"]));
            }
            return true;
        }

        if ($XML_PROPERTIES_PARENT) {
            $rs = $this->_xml_file->GetList(
                array("ID" => "asc"),
                array("PARENT_ID" => $XML_PROPERTIES_PARENT),
                array("ID")
            );
            while ($ar = $rs->Fetch()) {
                $arXMLProp = $this->_xml_file->GetAllChildrenArray($ar["ID"]);
                if (
                    array_key_exists($this->mess["IBLOCK_XML2_ID"], $arXMLProp)
                    && array_key_exists($arXMLProp[$this->mess["IBLOCK_XML2_ID"]], $arUserFields)
                ) {
                    $FIELD_NAME = $arUserFields[$arXMLProp[$this->mess["IBLOCK_XML2_ID"]]]["FIELD_NAME"];
                    $MULTIPLE = $arUserFields[$arXMLProp[$this->mess["IBLOCK_XML2_ID"]]]["MULTIPLE"];
                    $IS_FILE = $arUserFields[$arXMLProp[$this->mess["IBLOCK_XML2_ID"]]]["USER_TYPE"]["BASE_TYPE"] === "file";

                    unset($arXMLProp[$this->mess["IBLOCK_XML2_ID"]]);
                    $arProp = array();
                    $i = 0;
                    foreach ($arXMLProp as $value) {
                        if ($IS_FILE)
                            $arProp["n" . ($i++)] = $this->MakeFileArray($value);
                        else
                            $arProp["n" . ($i++)] = $value;
                    }

                    if ($MULTIPLE == "N")
                        $arSection[$FIELD_NAME] = array_pop($arProp);
                    else
                        $arSection[$FIELD_NAME] = $arProp;
                }
            }
        }

        $obSection = new CIBlockSection;
        $rsSection = $obSection->GetList(array(), array("IBLOCK_ID" => $IBLOCK_ID, "XML_ID" => $arSection["XML_ID"]), false);
        if ($arDBSection = $rsSection->Fetch()) {
            if (!array_key_exists("CODE", $arSection) && is_array($this->translit_on_update)) {
                $arSection["CODE"] = CUtil::translit($arSection["NAME"], LANGUAGE_ID, $this->translit_on_update);
                //Check if name was not changed in a way to update CODE
                if (substr($arDBSection["CODE"], 0, strlen($arSection["CODE"])) === $arSection["CODE"])
                    unset($arSection["CODE"]);
                else
                    $arSection["CODE"] = $this->CheckSectionCode($IBLOCK_ID, $arSection["CODE"]);
            }

            $bChanged = true;
            foreach ($arSection as $key => $value) {
                if (is_array($arDBSection[$key]) || ($arDBSection[$key] != $value)) {
                    $bChanged = true;
                    break;
                }
            }

            if ($bChanged) {
                foreach ($arUserFields as $arField1) {
                    if ($arField1["USER_TYPE"]["BASE_TYPE"] == "file") {
                        $sectionUF = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_" . $IBLOCK_ID . "_SECTION", $arDBSection["ID"]);
                        foreach ($sectionUF as $arField2) {
                            if (
                                $arField2["USER_TYPE"]["BASE_TYPE"] == "file"
                                && isset($arSection[$arField2["FIELD_NAME"]])
                            ) {
                                if ($arField2["MULTIPLE"] == "Y" && is_array($arField2["VALUE"]))
                                    foreach ($arField2["VALUE"] as $old_file_id)
                                        $arSection[$arField2["FIELD_NAME"]][] = array("del" => true, "old_id" => $old_file_id);
                                elseif ($arField2["MULTIPLE"] == "N" && $arField2["VALUE"] > 0)
                                    $arSection[$arField2["FIELD_NAME"]]["old_id"] = $arField2["VALUE"];
                            }
                        }
                        break;
                    }
                }

                $res = $obSection->Update($arDBSection["ID"], $arSection);
                if (!$res) {
                    $this->LAST_ERROR = $obSection->LAST_ERROR;
                    return $this->LAST_ERROR;
                }
            } else {
                $DB->Query("UPDATE b_iblock_section SET TIMESTAMP_X = " . $DB->CurrentTimeFunction() . " WHERE ID=" . $arDBSection["ID"]);
            }

            $arSection["ID"] = $arDBSection["ID"];
        } else {
            if (!array_key_exists("CODE", $arSection) && is_array($this->translit_on_add))
                $arSection["CODE"] = $this->CheckSectionCode($IBLOCK_ID, CUtil::translit($arSection["NAME"], LANGUAGE_ID, $this->translit_on_add));

            $arSection["IBLOCK_ID"] = $IBLOCK_ID;
            if (!isset($arSection["SORT"]))
                $arSection["SORT"] = $this->next_step["section_sort"];

            $arSection["ID"] = $obSection->Add($arSection);
            if (!$arSection["ID"]) {
                $this->LAST_ERROR = $obSection->LAST_ERROR;
                return $this->LAST_ERROR;
            }
        }

        if ($XML_SECTION_PROPERTIES) {
            $this->ImportSectionProperties($XML_SECTION_PROPERTIES, $IBLOCK_ID, $arSection["ID"]);
        }

        if ($arSection["ID"])
            $this->_xml_file->Add(array("PARENT_ID" => 0, "LEFT_MARGIN" => $arSection["ID"]));

        if ($XML_SECTIONS_PARENT) {
            $rs = $this->_xml_file->GetList(
                array("ID" => "asc"),
                array("PARENT_ID" => $XML_SECTIONS_PARENT),
                array("ID")
            );
            while ($ar = $rs->Fetch()) {
                $result = $this->ImportSection($ar["ID"], $IBLOCK_ID, $arSection["ID"]);
                if ($result !== true)
                    return $result;
            }
        }

        return true;
    }

    function ImportStores($XML_STORES_PARENT)
    {
        $ID = 0;
        $arXMLStores = $this->_xml_file->GetAllChildrenArray($XML_STORES_PARENT);
        foreach ($arXMLStores as $arXMLStore) {


            $arStore = array(
                "TITLE" => $arXMLStore[$this->mess["IBLOCK_XML2_NAME"]],
                "XML_ID" => $arXMLStore[$this->mess["IBLOCK_XML2_ID"]],
            );


            foreach ($arXMLStore['ЗначенияРеквизитов'] as $recvizit) {


                if ($recvizit['Наименование'] == 'Адрес' && isset($recvizit['Значение'])) {

                    $arStore["ADDRESS"] = $recvizit['Значение'];

                }

                if ($recvizit['Наименование'] == 'Описание' && isset($recvizit['Значение'])) {

                    $arStore["DESCRIPTION"] = $recvizit['Значение'];

                }

                if ($recvizit['Наименование'] == 'Телефон' && isset($recvizit['Значение'])) {

                    $arStore["PHONE"] = $recvizit['Значение'];

                }

                if ($recvizit['Наименование'] == 'ГрафикРаботы' && isset($recvizit['Значение'])) {

                    $arStore["SCHEDULE"] = $recvizit['Значение'];

                }

                if ($recvizit['Наименование'] == 'Самовывоз') {

                    if ($recvizit['Значение'] == 'true') {

                        $arStore["ISSUING_CENTER"] = 'Y';
                        $arStore['SHIPPING_CENTER'] = 'N';

                    } else {

                        $arStore["ISSUING_CENTER"] = 'N';
                        $arStore['SHIPPING_CENTER'] = 'Y';

                    }

                }

            }


            /*	if(isset($arXMLStore[$this->mess["IBLOCK_XML2_STORE_ADDRESS"]]))
                $arStore["ADDRESS"] = $arXMLStore[$this->mess["IBLOCK_XML2_STORE_ADDRESS"]][$this->mess["IBLOCK_XML2_VIEW"]];
            if(isset($arXMLStore[$this->mess["IBLOCK_XML2_STORE_DESCRIPTION"]]))
                $arStore["DESCRIPTION"] = $arXMLStore[$this->mess["IBLOCK_XML2_STORE_DESCRIPTION"]];

            if(
                isset($arXMLStore[$this->mess["IBLOCK_XML2_STORE_CONTACTS"]])
                && is_array($arXMLStore[$this->mess["IBLOCK_XML2_STORE_CONTACTS"]])
            )
            {
                $storeContact = array();
                foreach($arXMLStore[$this->mess["IBLOCK_XML2_STORE_CONTACTS"]] as $arContact)
                {
                    if(is_array($arContact))
                    {
                        $storeContact[] = $arContact[$this->mess["IBLOCK_XML2_VALUE"]];
                    }
                }

                if($storeContact)
                    $arStore["PHONE"] = implode(", ", $storeContact);
            }*/

            $rsStore = CCatalogStore::GetList(array(), array("XML_ID" => $arXMLStore[$this->mess["IBLOCK_XML2_ID"]]));
            $arIDStore = $rsStore->Fetch();

            if (!$arIDStore) {
                $ID = CCatalogStore::Add($arStore);
            } else {

                $ID = CCatalogStore::Update($arIDStore["ID"], $arStore);
            }
        }
        if (!$ID)
            return false;
        return true;
    }


    function ImportElementPrices($arXMLElement, &$counter, $arParent = false)
    {


        /** @global CMain $APPLICATION */
        global $APPLICATION;
        static $catalogs = array();

        $arElement = array(
            "ID" => 0,
            "XML_ID" => $arXMLElement[$this->mess["IBLOCK_XML2_ID"]],
        );

        $hashPosition = strrpos($arElement["XML_ID"], "#");
        if (
            $this->use_offers
            && $hashPosition === false && !$this->force_offers
            && isset($this->PROPERTY_MAP["CML2_LINK"])
            && isset($this->arProperties[$this->PROPERTY_MAP["CML2_LINK"]])
        ) {
            $IBLOCK_ID = $this->arProperties[$this->PROPERTY_MAP["CML2_LINK"]]["LINK_IBLOCK_ID"];
            if (!isset($catalogs[$IBLOCK_ID])) {
                $catalogs[$IBLOCK_ID] = true;

                $rs = CCatalog::GetList(array(), array("IBLOCK_ID" => $IBLOCK_ID));
                if (!$rs->Fetch()) {
                    $obCatalog = new CCatalog();
                    $boolFlag = $obCatalog->Add(array(
                        "IBLOCK_ID" => $IBLOCK_ID,
                        "YANDEX_EXPORT" => "N",
                        "SUBSCRIPTION" => "N",
                    ));
                    if (!$boolFlag) {
                        if ($ex = $APPLICATION->GetException())
                            $this->LAST_ERROR = $ex->GetString();
                        return 0;
                    }
                }
            }
        } else {
            $IBLOCK_ID = $this->next_step["IBLOCK_ID"];
        }

        $obElement = new CIBlockElement;
        $rsElement = $obElement->GetList(
            array("ID" => "asc"),
            array("=XML_ID" => $arElement["XML_ID"], "IBLOCK_ID" => $IBLOCK_ID),
            false, false,
            array("ID", "TMP_ID", "ACTIVE")
        );
        $arDBElement = $rsElement->Fetch();
        if ($arDBElement)
            $arElement["ID"] = $arDBElement["ID"];

        if (isset($arXMLElement[$this->mess["IBLOCK_XML2_STORE_AMOUNT_LIST"]])) {
            $arElement["STORE_AMOUNT"] = array();
            foreach ($arXMLElement[$this->mess["IBLOCK_XML2_STORE_AMOUNT_LIST"]] as $storeAmount) {
                if (isset($storeAmount[$this->mess["IBLOCK_XML2_STORE_ID"]])) {
                    $storeXMLID = $storeAmount[$this->mess["IBLOCK_XML2_STORE_ID"]];
                    $amount = $this->ToFloat($storeAmount[$this->mess["IBLOCK_XML2_AMOUNT"]]);
                    $arElement["STORE_AMOUNT"][$storeXMLID] = $amount;
                }
            }
        } elseif (isset($arXMLElement[$this->mess["IBLOCK_XML2_RESTS"]])) {
            $arElement["STORE_AMOUNT"] = array();
            foreach ($arXMLElement[$this->mess["IBLOCK_XML2_RESTS"]] as $xmlRest) {
                foreach ($xmlRest as $storeAmount) {
                    if (is_array($storeAmount)) {
                        if (isset($storeAmount[$this->mess["IBLOCK_XML2_ID"]])) {
                            $storeXMLID = $storeAmount[$this->mess["IBLOCK_XML2_ID"]];
                            $amount = $this->ToFloat($storeAmount[$this->mess["IBLOCK_XML2_AMOUNT"]]);
                            $arElement["STORE_AMOUNT"][$storeXMLID] = $amount;
                        }
                    } else {
                        if (strlen($storeAmount) > 0) {
                            $amount = $this->ToFloat($storeAmount);
                            $arElement["QUANTITY"] = $amount;
                        }
                    }
                }
            }
        } elseif (
            $arParent
            && (
                array_key_exists($this->mess["IBLOCK_XML2_STORES"], $arXMLElement)
                || array_key_exists($this->mess["IBLOCK_XML2_STORE"], $arXMLElement)
            )
        ) {
            $arElement["STORE_AMOUNT"] = array();
            $rsStores = $this->_xml_file->GetList(
                array("ID" => "asc"),
                array(
                    "><LEFT_MARGIN" => array($arParent["LEFT_MARGIN"], $arParent["RIGHT_MARGIN"]),
                    "NAME" => $this->mess["IBLOCK_XML2_STORE"],
                ),
                array("ID", "ATTRIBUTES")
            );
            while ($arStore = $rsStores->Fetch()) {
                if (strlen($arStore["ATTRIBUTES"]) > 0) {
                    $info = unserialize($arStore["ATTRIBUTES"]);
                    if (
                        is_array($info)
                        && array_key_exists($this->mess["IBLOCK_XML2_STORE_ID"], $info)
                        && array_key_exists($this->mess["IBLOCK_XML2_STORE_AMOUNT"], $info)
                    ) {
                        $arElement["STORE_AMOUNT"][$info[$this->mess["IBLOCK_XML2_STORE_ID"]]] = $this->ToFloat($info[$this->mess["IBLOCK_XML2_STORE_AMOUNT"]]);
                    }
                }
            }
        }

        if (isset($arElement["STORE_AMOUNT"]))
            $this->ImportStoresAmount($arElement["STORE_AMOUNT"], $arElement["ID"], $counter);

        if ($arDBElement) {
            $arProduct = array(
                "ID" => $arElement["ID"],
            );

            if (isset($arXMLElement[$this->mess["IBLOCK_XML2_PRICES"]])) {
                $arElement["PRICES"] = array();
                foreach ($arXMLElement[$this->mess["IBLOCK_XML2_PRICES"]] as $price) {
                    if (
                        isset($price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]])
                        && array_key_exists($price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]], $this->PRICES_MAP)
                    ) {
                        $price["PRICE"] = $this->PRICES_MAP[$price[$this->mess["IBLOCK_XML2_PRICE_TYPE_ID"]]];
                        $arElement["PRICES"][] = $price;

                        if (
                            array_key_exists($this->mess["IBLOCK_XML2_MEASURE"], $price)
                            && !isset($arProduct["MEASURE"])
                        ) {
                            $tmp = $this->convertBaseUnitFromXmlToPropertyValue($price[$this->mess["IBLOCK_XML2_MEASURE"]]);
                            if ($tmp["DESCRIPTION"] > 0)
                                $arProduct["MEASURE"] = $tmp["DESCRIPTION"];
                        }
                    }
                }

                $arElement["DISCOUNTS"] = array();
                if (isset($arXMLElement[$this->mess["IBLOCK_XML2_DISCOUNTS"]])) {
                    foreach ($arXMLElement[$this->mess["IBLOCK_XML2_DISCOUNTS"]] as $discount) {
                        if (
                            isset($discount[$this->mess["IBLOCK_XML2_DISCOUNT_CONDITION"]])
                            && $discount[$this->mess["IBLOCK_XML2_DISCOUNT_CONDITION"]] === $this->mess["IBLOCK_XML2_DISCOUNT_COND_VOLUME"]
                        ) {
                            $discount_value = $this->ToInt($discount[$this->mess["IBLOCK_XML2_DISCOUNT_COND_VALUE"]]);
                            $discount_percent = $this->ToFloat($discount[$this->mess["IBLOCK_XML2_DISCOUNT_COND_PERCENT"]]);
                            if ($discount_value > 0 && $discount_percent > 0)
                                $arElement["DISCOUNTS"][$discount_value] = $discount_percent;
                        }
                    }
                }
            }

            if (array_key_exists($this->mess["IBLOCK_XML2_AMOUNT"], $arXMLElement)) {
                $arElementTmp = array();
                $arElement["QUANTITY_RESERVED"] = 0;
                if ($arElement["ID"])
                    $arElementTmp = CCatalogProduct::GetById($arElement["ID"]);
                if (is_array($arElementTmp) && !empty($arElementTmp) && isset($arElementTmp["QUANTITY_RESERVED"]))
                    $arElement["QUANTITY_RESERVED"] = $arElementTmp["QUANTITY_RESERVED"];
                $arElement["QUANTITY"] = $this->ToFloat($arXMLElement[$this->mess["IBLOCK_XML2_AMOUNT"]]) - doubleval($arElement["QUANTITY_RESERVED"]);
            }

            if (isset($arElement["PRICES"]) && $this->bCatalog) {
                if (isset($arElement["QUANTITY"]))
                    $arProduct["QUANTITY"] = $arElement["QUANTITY"];
                elseif (isset($arElement["STORE_AMOUNT"]) && !empty($arElement["STORE_AMOUNT"]))
                    $arProduct["QUANTITY"] = array_sum($arElement["STORE_AMOUNT"]);

                $rsWeight = CIBlockElement::GetProperty($IBLOCK_ID, $arElement["ID"], array(), array("CODE" => "CML2_TRAITS"));
                while ($arWeight = $rsWeight->Fetch()) {
                    if ($arWeight["DESCRIPTION"] == $this->mess["IBLOCK_XML2_WEIGHT"])
                        $arProduct["WEIGHT"] = $this->ToFloat($arWeight["VALUE"]) * 1000;
                }

                $rsUnit = CIBlockElement::GetProperty($IBLOCK_ID, $arElement["ID"], array(), array("CODE" => "CML2_BASE_UNIT"));
                while ($arUnit = $rsUnit->Fetch()) {
                    if ($arUnit["DESCRIPTION"] > 0)
                        $arProduct["MEASURE"] = $arUnit["DESCRIPTION"];
                }

                //Here start VAT handling

                //Check if all the taxes exists in BSM catalog
                $arTaxMap = array();
                $rsTaxProperty = CIBlockElement::GetProperty($IBLOCK_ID, $arElement["ID"], array("sort" => "asc"), array("CODE" => "CML2_TAXES"));
                while ($arTaxProperty = $rsTaxProperty->Fetch()) {
                    if (
                        strlen($arTaxProperty["VALUE"]) > 0
                        && strlen($arTaxProperty["DESCRIPTION"]) > 0
                        && !array_key_exists($arTaxProperty["DESCRIPTION"], $arTaxMap)
                    ) {
                        $arTaxMap[$arTaxProperty["DESCRIPTION"]] = array(
                            "RATE" => $this->ToFloat($arTaxProperty["VALUE"]),
                            "ID" => $this->CheckTax($arTaxProperty["DESCRIPTION"], $this->ToFloat($arTaxProperty["VALUE"])),
                        );
                    }
                }

                //First find out if all the prices have TAX_IN_SUM true
                $TAX_IN_SUM = "Y";
                foreach ($arElement["PRICES"] as $price) {
                    if ($price["PRICE"]["TAX_IN_SUM"] !== "true") {
                        $TAX_IN_SUM = "N";
                        break;
                    }
                }
                //If there was found not included tax we'll make sure
                //that all prices has the same flag
                if ($TAX_IN_SUM === "N") {
                    foreach ($arElement["PRICES"] as $price) {
                        if ($price["PRICE"]["TAX_IN_SUM"] !== "false") {
                            $TAX_IN_SUM = "Y";
                            break;
                        }
                    }
                    //Check if there is a mix of tax in sum
                    //and correct it by recalculating all the prices
                    if ($TAX_IN_SUM === "Y") {
                        foreach ($arElement["PRICES"] as $key => $price) {
                            if ($price["PRICE"]["TAX_IN_SUM"] !== "true") {
                                $TAX_NAME = $price["PRICE"]["TAX_NAME"];
                                if (array_key_exists($TAX_NAME, $arTaxMap)) {
                                    $PRICE_WO_TAX = $this->ToFloat($price[$this->mess["IBLOCK_XML2_PRICE_FOR_ONE"]]);
                                    $PRICE = $PRICE_WO_TAX + ($PRICE_WO_TAX / 100.0 * $arTaxMap[$TAX_NAME]["RATE"]);
                                    $arElement["PRICES"][$key][$this->mess["IBLOCK_XML2_PRICE_FOR_ONE"]] = $PRICE;
                                }
                            }
                        }
                    }
                }


                $IBLOCK_ID = CIBlockElement::GetIBlockByID($arElement["ID"]);
                //AddMessage2Log($IBLOCK_ID, "ImportElementPrices");


                foreach ($arElement["PRICES"] as $price) {

                    //	AddMessage2Log($price, "ImportElementPrices");

                    $old_price = 0;
                    $old_price = $price['ЦенаСтарая'];

                    $price_type = $price['PRICE']['ID'];

                    $TAX_NAME = $price["PRICE"]["TAX_NAME"];
                    if (array_key_exists($TAX_NAME, $arTaxMap)) {
                        $arProduct["VAT_ID"] = $arTaxMap[$TAX_NAME]["ID"];
                        break;
                    }


                    //Прибавить к Олд_прайс ИД типа цены
                    $properties_fop = CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("CODE" => "OLD_PRICE_" . $price_type, "IBLOCK_ID" => $IBLOCK_ID));
                    if ($prop_fields_fop = $properties_fop->GetNext()) {
                        // AddMessage2Log($prop_fields_fop, "ImportElementPrices");

                        CIBlockElement::SetPropertyValuesEx($arElement["ID"], $IBLOCK_ID, array("OLD_PRICE_" . $price_type => $old_price));
                    } else {
                        $arFieldsOP = array(
                            "NAME" => "Старая цена " . $price_type,
                            "ACTIVE" => "Y",
                            "SORT" => "300000",
                            "CODE" => "OLD_PRICE_" . $price_type,
                            "PROPERTY_TYPE" => "S",
                            "IBLOCK_ID" => $IBLOCK_ID,
                            "FILTRABLE" => "Y",
                            "DEFAULT_VALUE" => 0,
                        );


                        $ibpOP = new CIBlockProperty;
                        $PropIDOP = $ibpOP->Add($arFieldsOP);

                        CIBlockElement::SetPropertyValuesEx($arElement["ID"], $IBLOCK_ID, array("OLD_PRICE_" . $price_type => $old_price));
                    }


                }


                $arProduct["VAT_INCLUDED"] = $TAX_IN_SUM;

                CCatalogProduct::Add($arProduct);

                $this->SetProductPrice($arElement["ID"], $arElement["PRICES"], $arElement["DISCOUNTS"]);
            } elseif (
                $this->bCatalog
                && isset($arElement["STORE_AMOUNT"])
                && !empty($arElement["STORE_AMOUNT"])
                && CCatalogProduct::GetById($arElement["ID"])
            ) {
                CCatalogProduct::Update($arElement["ID"], array(
                    "QUANTITY" => array_sum($arElement["STORE_AMOUNT"]),
                ));
            } elseif (
                $this->bCatalog
                && isset($arElement["QUANTITY"])
                && CCatalogProduct::GetById($arElement["ID"])
            ) {
                CCatalogProduct::Update($arElement["ID"], array(
                    "QUANTITY" => $arElement["QUANTITY"],
                ));
            }
        }

        $counter["UPD"]++;
        return $arElement["ID"];
    }


}

?>