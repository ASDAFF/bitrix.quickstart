<?
abstract class Novagroup_Classes_Abstract_Import1C
{
    protected $iBlockID;
    protected $exchange1cGroup;
    protected $type; // 1 - catalog, 2 - offer
    protected $arFields = array();

    function __construct($exchange1cGroup, $arFields, $type = 1)
    {
        if( !CModule::IncludeModule("iblock") ) die("iblock module is not installed");

        $this->iBlockID = (int)$arFields["IBLOCK_ID"];
        $this->exchange1cGroup = (int)$exchange1cGroup;
        $this->type	= $type;
        $this->arFields = $arFields;
    }

    function defineValue($value)
    {
        $returnValue = '';
        foreach ($value as $key => $item) {

            if (is_array($item) && isset($item["VALUE"])) {

                $returnValue = $item["VALUE"];
            } elseif (!empty($item)) {

                $returnValue = $item;
            }
            break;
        }
        return $returnValue;
    }

    /**
     * устанавливает значение свойства список
     * @param array $params
     */
    function setEnumProperty($params = array())
    {
        $value = $this->defineValue($params["VALUE"]);


        $dbEnumList = CIBlockProperty::GetPropertyEnum(
            $params["PROP_CODE_DESTINATION"], Array(), Array("IBLOCK_ID" => $params["IBLOCK_CATALOG"])
        );
        $newValue = "";
        if ($params["COMPARE_VALUE"] == $value) {
            if ($arEnumList = $dbEnumList->GetNext()) {

                $newValue = $arEnumList["ID"];
            }
        }

        $resSaveProp = CIBlockElement::SetPropertyValuesEx(
            $params["CATALOG_ELEMENT_ID"], false, array($params["PROP_CODE_DESTINATION"] => $newValue)
        );

    }

    /**
     * записывает в массив $arrPictures цвета и пути к фото
     * @param array $params
     * @param $arrPictures
     */
    function processMorePhoto($params = array(), &$arrPictures) {

        $picPath = $params["picPath"];
        $description = $params["description"];
        $pos = strpos($description, "ItemsColor-");
        if ($pos !== false) {

            $arrPicDescr = explode("ItemsColor-", $description);

            if (!empty($arrPicDescr[1])) {

                $nameColor = $arrPicDescr[1];
                $colorIB = $params["COLOR_IB"];

                $colorId = $this->getColorID($nameColor, $colorIB);

                $arrPictures[$colorId][] = $picPath;
            }
        }
    }

    function getSizeID($nameSize, $sizesIblockId) {

        $sizeId = 0;

        $arFilter = array('IBLOCK_ID' => $sizesIblockId, "NAME" => $nameSize);
        $arSelect = array('ID', 'NAME');
        $rsElement = CIBlockElement::GetList(array('NAME'), $arFilter, false, false, $arSelect);
        if ($arElement = $rsElement->Fetch()) {

            $sizeId = $arElement["ID"];
        } else {

            $el = new CIBlockElement;

            $arLoad = Array(

                "IBLOCK_ID" => $sizesIblockId,
                "IBLOCK_SECTION" => false,
                "NAME" => $nameSize,
                "ACTIVE" => "Y"
            );

            if ($sizeId = $el->Add($arLoad)) {

            }
        }
        return $sizeId;
    }

    /**
     * вовзращает ID цвета из справочника цветов по названию, если цвета нет - то создает его
     * @param $nameColor
     * @param $colorIB
     * @return bool|int
     */
    function getColorID($nameColor, $colorIB) {

        //"белый/голубой полоса" привяжется к "белый"
        $pos = strpos($nameColor, "/");
        if ($pos !== false) {

            $arrNameColor = explode("/", $nameColor);
            if (!empty($arrNameColor[0])) $nameColor = $arrNameColor[0];
        }

        $arFilter = array('IBLOCK_ID' => $colorIB, "NAME" => $nameColor);
        $arSelect = array('ID', 'NAME');
        $rsElement = CIBlockElement::GetList(array('NAME'), $arFilter, false, false, $arSelect);
        if ($arElement = $rsElement->Fetch()) {

            $colorId = $arElement["ID"];
        } else {
            // создаем цвет
            $el = new CIBlockElement;

            $arLoad = Array(

                "IBLOCK_ID" => $colorIB,
                "IBLOCK_SECTION" => false,
                "NAME" => $nameColor,
                "PREVIEW_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]. "/local/images/novagr.shop/color-miss.jpg"),
                "ACTIVE" => "Y"
            );

            if ($colorId = $el->Add($arLoad)) {

            }

        }
        return $colorId;
    }

    /**
     * заполняет св.во типа привязка к элементу
     * @param array $params
     */
    function setReferenceProperty($params = array())
    {
        $propID = $this->defineValue($params["VALUE"]);

        if (empty($propID)) {
            // пустое значение
            $resSaveProp = CIBlockElement::SetPropertyValuesEx(
                $params["CATALOG_ELEMENT_ID"], false, array($params["PROP_CODE_DESTINATION"] => "")
            );
            return;
        }

        $db_enum_list = CIBlockProperty::GetPropertyEnum(
            $params["PROP_CODE_SOURCE"], Array(), Array("IBLOCK_ID" => $params["IBLOCK_CATALOG"])
        );

        while ($arRes = $db_enum_list->GetNext()) {

            if ($propID == $arRes["ID"]) {
                $propName = $arRes["VALUE"];

                $arFilter = array('IBLOCK_ID' => $params["IBLOCK_ID_DESTINATION"], "NAME" => $propName);
                $arSelect = array('ID', 'NAME');

                $rsElement = CIBlockElement::GetList(array('NAME'), $arFilter, false, false, $arSelect);

                if ($arElement = $rsElement->Fetch()) {

                    $classificatorElementID = $arElement["ID"];
                } else {
                    // создаем элемент
                    $el = new CIBlockElement;

                    $arLoad = Array(

                        "IBLOCK_ID" => $params["IBLOCK_ID_DESTINATION"],
                        "IBLOCK_SECTION" => false,
                        "NAME" => $propName,
                        "ACTIVE" => "Y"
                    );

                    if ($classificatorElementID = $el->Add($arLoad)) {

                    } else {
                        //echo "Error: " . $el->LAST_ERROR;
                        //deb($arFields);
                        //die();
                    }
                }

                if ($classificatorElementID > 0) {

                    $resSaveProp = CIBlockElement::SetPropertyValuesEx(
                        $params["CATALOG_ELEMENT_ID"],
                        false,
                        array($params["PROP_CODE_DESTINATION"] => $classificatorElementID)
                    );
                }
                break;
            }
        }
    }

    function processElement() {
        // get props
        $res = CIBlock::GetProperties($this->iBlockID, Array(), Array());

        if ($this->type == 2) {
            // process offer

            $requiredProps = array();
            while ($resArr = $res->Fetch()) {

                //определяем ИБ справочников
                if ($resArr["CODE"] == "STD_SIZE") {
                    $sizesIblockId = $resArr["LINK_IBLOCK_ID"]; // ИБ Размеры

                } elseif ($resArr["CODE"] == "COLOR") {
                    $colorIblockId = $resArr["LINK_IBLOCK_ID"]; // ИБ Цвета
                }

                // получаем обязательные для заполнения свойства
                if ($resArr["IS_REQUIRED"] == "Y") {
                    $requiredProps[$resArr["ID"]] = $resArr;
                }
            }

            $colorFound = false;
            $sizeFound = false;

            foreach ($this->arFields["PROPERTY_VALUES"] as $key => $value) {

                if (is_array($value)) {
                    foreach ($value as $key2 => $item) {
                        if ($item["DESCRIPTION"] == "Размер" && !empty($item["VALUE"]) && $sizesIblockId>0)
                        {
                            // определяем есть ли такой размер в справочнике размеров
                            $nameSize = $item["VALUE"];
                            $sizeId = $this->getSizeID($nameSize, $sizesIblockId);

                            if ($sizeId > 0) {
                                $sizeFound = true;
                                $resSaveProp = CIBlockElement::SetPropertyValuesEx($this->arFields["ID"], false, array("STD_SIZE" => $sizeId));

                            }
                        }
                        if ($item["DESCRIPTION"] == "Цвет" && !empty($item["VALUE"]) && $colorIblockId > 0)
                        {
                            // определяем есть ли такой цвет в справочнике цветов
                            $nameColor = $item["VALUE"];

                            $colorId = $this->getColorID($nameColor, $colorIblockId);

                            if ($colorId > 0) {
                                $colorFound = true;
                                $resSaveProp = CIBlockElement::SetPropertyValuesEx($this->arFields["ID"], false, array("COLOR" => $colorId));

                            }
                        }
                    }
                }
            } // end foreach ($arFields["PROPERTY_VALUES"] as $key => $value) {

            if ($colorFound == false && $colorIblockId > 0) {
                $colorId = $this->getColorID("Цвет отсутствует", $colorIblockId);
                if ($colorId > 0) {

                    $resSaveProp = CIBlockElement::SetPropertyValuesEx(
                        $this->arFields["ID"], false, array("COLOR" => $colorId)
                    );
                }
            }
            if ($sizeFound == false && $sizesIblockId > 0) {
                $sizeId = $this->getSizeID("Нет", $sizesIblockId);
                if ($sizeId > 0) {

                    $resSaveProp = CIBlockElement::SetPropertyValuesEx(
                        $this->arFields["ID"], false, array("STD_SIZE" => $sizeId)
                    );
                }
            }

            // если не заполнено обяз. свойство - делаем элемент неактивным
            $elemInActive = false;
            if (count($requiredProps) > 0) {
                foreach ($requiredProps as $value) {
                    $db_props = CIBlockElement::GetProperty(
                        $this->arFields["IBLOCK_ID"],
                        $this->arFields["ID"],
                        array("sort" => "asc"), Array("CODE" => $value["CODE"])
                    );
                    if ($arProps = $db_props->Fetch()) {

                        if (empty($arProps["VALUE"])) {
                            $elemInActive = true;
                            break;
                        }
                    }
                }
                if ($elemInActive == true) {

                    $el = new CIBlockElement;
                    $arLoad = Array("ACTIVE" => "N");
                    global $updateHandlerFlag;
                    $updateHandlerFlag = true;
                    $res = $el->Update($this->arFields["ID"], $arLoad);
                }
            }

        } elseif ($this->type == 1) {
            //end process catalog element
            while ($resArr = $res->Fetch()) {

                //определяем ИБ справочников
                if ($resArr["CODE"] == "VENDOR") {
                    $brandIblockId = $resArr["LINK_IBLOCK_ID"];

                } elseif ($resArr["CODE"] == "SAMPLES") {
                    $sampleIblockId = $resArr["LINK_IBLOCK_ID"]; // ИБ Образцы

                } elseif ($resArr["CODE"] == "COUNTRY") {
                    $countyIblockId = $resArr["LINK_IBLOCK_ID"]; // ИБ Страны

                } elseif ($resArr["CODE"] == "MATERIAL_LIST") {
                    $materialIblockId = $resArr["LINK_IBLOCK_ID"]; // ИБ Материалы

                }elseif ($resArr["CODE"] == "PHOTONAME_COLOR_1") {
                    $colorIblockId = $resArr["LINK_IBLOCK_ID"];  // ИБ Цвета
                }

                if ($resArr["XML_ID"] == '8f4b0221-62f5-11e3-97e2-50465db6ef12') {
                    $brandPropID = $resArr["ID"]; // BREND
                    $brandPropCode = $resArr["CODE"];
                }
                elseif ($resArr["XML_ID"] == '8f4b0222-62f5-11e3-97e2-50465db6ef12') {
                    $samplePropID = $resArr["ID"]; // RISUNOK
                    $samplePropCode = $resArr["CODE"];
                }
                elseif ($resArr["XML_ID"] == '8f4b021e-62f5-11e3-97e2-50465db6ef12') {

                    $specialPropID = $resArr["ID"]; // SPETSPREDLOZHENIE

                }
                elseif ($resArr["XML_ID"] == '8f4b021f-62f5-11e3-97e2-50465db6ef12') {

                    $newPropID = $resArr["ID"]; // NOVINKA

                }
                elseif ($resArr["XML_ID"] == '8f4b0220-62f5-11e3-97e2-50465db6ef12') {
                    $saleLeaderPropID = $resArr["ID"]; // LIDER_PRODAZH

                }
                elseif ($resArr["XML_ID"] == 'CML2_MANUFACTURER') {

                    $manufacturerPropID = $resArr["ID"]; // CML2_MANUFACTURER
                    $manufacturerPropCode = $resArr["CODE"];
                }
                elseif ($resArr["XML_ID"] == '8f4b0223-62f5-11e3-97e2-50465db6ef12') {
                    $materialPropID = $resArr["ID"]; // MATERIAL
                    $materialPPropCode = $resArr["CODE"];
                }
                elseif ($resArr["XML_ID"] == 'd8ca97fc-5e63-11e3-b21d-50465db6ef12') {
                    $compositionPropID = $resArr["ID"]; // SOSTAV
                    $compositionPropCode = $resArr["CODE"];
                }
                elseif ($resArr["XML_ID"] == 'CML2_PICTURES') {
                    $morePhotoPropID = $resArr["ID"]; // MORE_PHOTO
                }
            }

            $params = array();
            // обрабатываем картинки
            $params["COLOR_IB"] = $colorIblockId;
            $arrPictures = array();
            if (!empty($this->arFields["DETAIL_PICTURE"]["tmp_name"]) && !empty($this->arFields["DETAIL_PICTURE"]["description"])) {

                $params["description"] = $this->arFields["DETAIL_PICTURE"]["description"];
                $params["picPath"] = $this->arFields["DETAIL_PICTURE"]["tmp_name"];
                $this->processMorePhoto($params, $arrPictures);
            }

            $params["CATALOG_ELEMENT_ID"] = $this->arFields["ID"];
            $params["IBLOCK_CATALOG"] = $this->arFields["IBLOCK_ID"];
            foreach ($this->arFields["PROPERTY_VALUES"] as $key => $value) {

                if (is_array($value)) {
                    if ($samplePropID > 0 && $key == $samplePropID) {
                        //property RISUNOK
                        $params["VALUE"] = $value;
                        $params["PROP_CODE_SOURCE"] = $samplePropCode; //"RISUNOK";
                        $params["PROP_CODE_DESTINATION"] = "SAMPLES";
                        $params["IBLOCK_ID_DESTINATION"] = $sampleIblockId;
                        $this->setReferenceProperty($params);
                    } elseif ($brandPropID > 0 && $key == $brandPropID) {

                        $params["VALUE"] = $value;
                        $params["PROP_CODE_SOURCE"] = $brandPropCode; //"BREND";
                        $params["PROP_CODE_DESTINATION"] = "VENDOR";
                        $params["IBLOCK_ID_DESTINATION"] = $brandIblockId;
                        $this->setReferenceProperty($params);
                    } elseif ($manufacturerPropID > 0 && $key == $manufacturerPropID) {

                        $params["VALUE"] = $value;
                        $params["PROP_CODE_SOURCE"] = $manufacturerPropCode; //"CML2_MANUFACTURER";
                        $params["PROP_CODE_DESTINATION"] = "COUNTRY";
                        $params["IBLOCK_ID_DESTINATION"] = $countyIblockId;

                        $this->setReferenceProperty($params);

                    } elseif ($materialPropID > 0 && $key == $materialPropID) {

                        $params["VALUE"] = $value;
                        $params["PROP_CODE_SOURCE"] = $materialPPropCode; //"MATERIAL";
                        $params["PROP_CODE_DESTINATION"] = "MATERIAL_LIST";
                        $params["IBLOCK_ID_DESTINATION"] = $materialIblockId;
                        $this->setReferenceProperty($params);

                    } elseif ($specialPropID > 0 && $key == $specialPropID) {

                        $params["VALUE"] = $value;
                        $params["COMPARE_VALUE"] = "Да";
                        $params["PROP_CODE_DESTINATION"] = "SPECIALOFFER";

                        $this->setEnumProperty($params);
                    } elseif ($newPropID > 0 && $key == $newPropID) {

                        $params["VALUE"] = $value;
                        $params["COMPARE_VALUE"] = "Да";
                        $params["PROP_CODE_DESTINATION"] = "NEWPRODUCT";

                        $this->setEnumProperty($params);
                    } elseif ($saleLeaderPropID > 0 && $key == $saleLeaderPropID) {

                        $params["VALUE"] = $value;
                        $params["COMPARE_VALUE"] = "Да";
                        $params["PROP_CODE_DESTINATION"] = "SALELEADER";

                        $this->setEnumProperty($params);
                    } elseif ($compositionPropID > 0 && $key == $compositionPropID) {

                        $propID = $this->defineValue($value);

                        if (empty($propID)) {

                            $resSaveProp = CIBlockElement::SetPropertyValuesEx(
                                $params["CATALOG_ELEMENT_ID"], false,
                                array("MATERIAL_DESC" => array('VALUE' => array('TYPE' => 'TEXT', 'TEXT' => "")))
                            );

                        } else {

                            $db_enum_list = CIBlockProperty::GetPropertyEnum(
                                $compositionPropCode,
                                Array(),
                                Array("IBLOCK_ID" => $params["IBLOCK_CATALOG"])
                            );
                            while ($arRes = $db_enum_list->GetNext()) {

                                if ($propID == $arRes["ID"]) {
                                    $prop_value = $arRes["VALUE"];

                                    $resSaveProp = CIBlockElement::SetPropertyValuesEx(
                                        $params["CATALOG_ELEMENT_ID"], false,
                                        array("MATERIAL_DESC" => array('VALUE' => array('TYPE' => 'TEXT', 'TEXT' => $prop_value)))
                                    );
                                    break;
                                }
                            }
                        }

                    } elseif ($morePhotoPropID > 0 && $key == $morePhotoPropID) {

                        if (is_array($value)) {
                            foreach ($value as $k => $v) {
                                if (!empty($v["VALUE"]["tmp_name"]) && !empty($v["DESCRIPTION"])) {
                                    $params["description"] = $v["DESCRIPTION"];
                                    $params["picPath"] = $v["VALUE"]["tmp_name"];
                                    $this->processMorePhoto($params, $arrPictures);
                                }
                            }

                            // сохраняем картинки по цветам
                            if (count($arrPictures)) {

                                $j = 1;
                                $props = array();
                                foreach ($arrPictures as $color => $photos) {
                                    $arFile = array();
                                    foreach ($photos as $photo) {

                                        $arFile[] = array(
                                            "VALUE" => CFile::MakeFileArray($photo),
                                            "DESCRIPTION"=>""
                                        );
                                    }
                                    $props["PHOTO_COLOR_".$j] = $arFile;
                                    $props["PHOTONAME_COLOR_".$j] = $color;
                                    $j++;
                                }

                                CIBlockElement::SetPropertyValuesEx(
                                    $params["CATALOG_ELEMENT_ID"], $params["IBLOCK_CATALOG"], $props
                                );
                                // удаляем остальные свойства
                                $props = array();
                                for ($i = $j; $i <= 10; $i++) {
                                    $props["PHOTO_COLOR_".$i] = array("VALUE" => "","DESCRIPTION"=>"");
                                    $props["PHOTONAME_COLOR_".$i] = "";
                                }
                                CIBlockElement::SetPropertyValuesEx(
                                    $params["CATALOG_ELEMENT_ID"], $params["IBLOCK_CATALOG"], $props
                                );
                            }
                        }
                    }
                }
            }
        } // end process catalog element
    }
}
?>