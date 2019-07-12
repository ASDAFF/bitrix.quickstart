<?


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

    CModule::IncludeModule('iblock');

    //if ($this->StartResultCache(3600)) {

        $iblock_id = $arParams['CODE'];
        //var_dump($iblock_id);exit();

        $arFilter = array(
            'IBLOCK_CODE' => $iblock_id, 
            //'IBLOCK_TYPE' => "credit_elem", 
            "INCLUDE_SUBSECTIONS" => "Y",
            'ACTIVE' => "Y"
        );

        if($arParams['PRODUCT_SECTION']) {
            $arFilter['SECTION_ID'] = $arParams['PRODUCT_SECTION'];
        }

        $db_list = CIBlockElement::GetList(array('NAME' => 'ASC'), $arFilter, false, false, array(
            "ID",
            "NAME",
            "CODE",
            "IBLOCK_SECTION_ID",
            "DETAIL_PICTURE",
            "PREVIEW_PICTURE",
            "PROPERTY_PRICE",
            "PROPERTY_PHOTO",
            "PROPERTY_SHORT_NAME",
            "PROPERTY_PARAMS",
            "DETAIL_TEXT")
        );

        $ids = array();
        
        while ($ar_result = $db_list->GetNext()) {

            if (!in_array($ar_result['ID'], $ids)) {

                $ids[] = $ar_result['ID'];
                $list_param = null;
                $params = null;
                $section_name = "";

                $section = CIBlockSection::GetByID($ar_result['IBLOCK_SECTION_ID']);
                if ($ar_res = $section->GetNext()) {
                    $section_name = $ar_res['NAME'];
                }

                $arFilter_section = array("SECTION_ID" => $ar_res['ID'], 'IBLOCK_CODE' => "credit_elem", 'ACTIVE' => "Y");

                $db_child_list = CIBlockElement::GetList(array('NAME' => 'ASC'), $arFilter_section, false, false, array(
                            "ID",
                            "NAME",
                            "CODE",
                            "IBLOCK_SECTION_ID",
                            "PROPERTY_PRICE",
                            "PROPERTY_PHOTO",
                            "PROPERTY_SHORT_NAME",
                            "PROPERTY_PARAMS",
                            "DETAIL_TEXT"));

                $ids_param = array();
                while ($ar_result_temp = $db_child_list->GetNext()) {

                    if (!in_array($ar_result_temp['ID'], $ids_param)) {
                        $ids_param[] = $ar_result_temp['ID'];
                        $list_param[] = $ar_result_temp;
                    }
                }

                for ($i = 0; $i < count($list_param); $i++) {
                    for ($j = 0; $j < count($list_param[$i]["PROPERTY_PARAMS_DESCRIPTION"]); $j++) {
                        $params[$list_param[$i]["PROPERTY_PARAMS_DESCRIPTION"][$j]][$list_param[$i]["ID"]] = $list_param[$i]["PROPERTY_PARAMS_VALUE"][$j];
                    }
                }

                $arResult[] = array(
                    "ID" => $ar_result['ID'],
                    "CODE" => $ar_result['CODE'],
                    "NAME" => $ar_result['NAME'],
                    "DESC" => $ar_result['DETAIL_TEXT'],
                    "IBLOCK_SECTION_ID" => $ar_result['IBLOCK_SECTION_ID'],
                    "PRICE" => $ar_result['PROPERTY_PRICE_VALUE'],
                    "SHORT_NAME" => $ar_result['PROPERTY_SHORT_NAME_VALUE'],
                    "PARAMS" => $ar_result['PROPERTY_PARAMS_VALUE'],
                    "PHOTO" => CFile::GetPath($ar_result['PROPERTY_PHOTO_VALUE']),
                    "DETAIL_PICTURE" => CFile::GetPath($ar_result['DETAIL_PICTURE']),
                    "PREVIEW_PICTURE" => CFile::GetPath($ar_result['PREVIEW_PICTURE']),
                    "PATH" => $this->getPath(),
                    "SECTION_NAME" => $section_name,
                    "ALL_PARAMS" => $list_param,
                    "VAL_PARAMS" => $params
                );
            }
        }
    //}

    //print_r($arResult);
    //exit();

    if (isset($_POST['credit'])) {
        $el = new CIBlockElement;

        $PROP = array();
        $PROP["model"] = $_POST["model"];
        $PROP["age"] = $_POST["age"];
        $PROP["region"] = $_POST["region"];
        $PROP["fio"] = $_POST["fio"];
        $PROP["price"] = $_POST["price"];
        $PROP["model"] = $_POST["model"];
        $PROP["month"] = $_POST["month"];
        $PROP["phone"] = $_POST["phone"];
        $PROP["first_pay"] = $_POST["fpay"];

        //$iblock = CIBlock::GetList(array('NAME' => 'ASC'), array('CODE' => "cars_result", ), false, false, array("ID"));    
        $iblock = CIBlock::GetList(array('NAME' => 'ASC'), array('CODE' => "credit_result", ), false, false, array("ID"));    
        
         
        $iblock = $iblock->GetNext();
       
        $iblock = $iblock["ID"];
      
        $arLoadProductArray = Array(
            "MODIFIED_BY" => $USER->GetID(), 
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => $iblock,
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $PROP["model"],
            "ACTIVE" => "Y",
            "PREVIEW_TEXT" => "",
            "DETAIL_TEXT" => ""
        );

        if ($PRODUCT_ID = $el->Add($arLoadProductArray))
            echo " ";
        else
            echo "Error: " . $el->LAST_ERROR;
    }

    $this->IncludeComponentTemplate();

?>
