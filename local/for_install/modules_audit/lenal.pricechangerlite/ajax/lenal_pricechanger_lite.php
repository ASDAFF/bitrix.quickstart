<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && CModule::IncludeModule("iblock") && $_REQUEST["action"] == 'GetIblockProp'):
    $iblock = intval($_REQUEST["id"]);
    $iblock_properties = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $iblock));
    while ($prop_fields = $iblock_properties->Fetch()):
        if ($prop_fields["PROPERTY_TYPE"] == 'L' && $prop_fields['CODE']):

            $opt .= '<option  value="' . $prop_fields['CODE'] . '" data-code="' . $prop_fields['CODE'] . '">' . $prop_fields['NAME'] . '</option>';
        elseif ($prop_fields["PROPERTY_TYPE"] == 'E'):

            $opt .= '<option data-iblock="' . $prop_fields["LINK_IBLOCK_ID"] . '" value="' . $prop_fields['CODE'] . '" data-code="' . $prop_fields['CODE'] . '">' . $prop_fields['NAME'] . '</option>';
        endif;
    endwhile;
    echo $opt;
//print_r($data);
//echo json_encode($data);
elseif ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && CModule::IncludeModule("iblock") && $_REQUEST["action"] == 'GetIblockPropVal'):
    $PROP_ID = $_REQUEST["code"];
    $IBLOCK_ID = $_REQUEST["iblock"];
    if (!$IBLOCK_ID):
        $property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC"), Array("CODE" => $PROP_ID));
        while ($prop_fields = $property_enums->Fetch()):
            ?>
            <option data-pre="<? //print_r($prop_fields);                             ?>" value="<?= $prop_fields['ID'] ?>" data-code="<?= $prop_fields['CODE'] ?>"><?= $prop_fields['VALUE'] ?></option>
            <?
        endwhile;
    else:
        $el = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $IBLOCK_ID), false, false, array("ID", "NAME"));
        while ($ob = $el->Fetch()):
            ?>
            <option data-pre="<? //print_r($prop_fields);                              ?>" value="<?= $ob['ID'] ?>" data-code="<?= $PROP_ID['CODE'] ?>"><?= $ob['NAME'] ?></option>
            <?
        endwhile;
    endif;
elseif ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && CModule::IncludeModule("iblock") && $_REQUEST["action"] == 'GetPriceProp'):
    $iblock = intval($_REQUEST["id"]);
    $iblock_properties = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $iblock));
    while ($prop_fields = $iblock_properties->Fetch()):
        if ($prop_fields["PROPERTY_TYPE"] == 'N' )
            $opt .= '<option  value="' . $prop_fields['CODE'] . '" data-code="' . $prop_fields['CODE'] . '">' . $prop_fields['NAME'] . '</option>';
    endwhile;
    echo $opt;
elseif ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && CModule::IncludeModule("iblock") && $_REQUEST["action"] == 'load_item'):
//print_r($_REQUEST);die;

    $iblock = intval($_REQUEST["iblock_id"]);
    $PROP_CODE = $_REQUEST["iblock_opt_prop"];
    $PROP_CODE_VAL = $_REQUEST["iblock_opt_prop_val"];
//    if ($_REQUEST["iblock_subdir"]>0)
    $section_id = $_REQUEST["iblock_subdir"];
//    else
//        $section_id = 0;
    if ($_REQUEST["subdir"])
        $include = 'Y';
    else
        $include = 'N';

    $arSelectLoad = Array(
        "PROPERTY_$PROP_CODE" => $PROP_CODE_VAL,
    );
    if ($_REQUEST["iblock_subdir"] > 0)
        $arSelectLoad["SECTION_ID"] = $_REQUEST["iblock_subdir"];

    if ($_REQUEST["subdir"] == 'Y')
        $arSelectLoad["INCLUDE_SUBSECTIONS"] = $_REQUEST["subdir"];
    else
        $arSelectLoad["INCLUDE_SUBSECTIONS"] = 'N';

    $el = CIBlockElement::GetList(array(), $arSelectLoad, false, false, array("ID"));
    echo $el->SelectedRowsCount();

elseif ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && CModule::IncludeModule("iblock") && $_REQUEST["action"] == 'GetSKU'):
    $iblock = intval($_REQUEST["id"]);
    $mxResult = CCatalogSKU::GetInfoByProductIBlock($iblock);
    if (is_array($mxResult)) {
        $data = $mxResult['IBLOCK_ID'];
    } else {
        $data = '';
    };
    echo $data;
elseif ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && CModule::IncludeModule("iblock") && $_REQUEST["action"] == 'GetSubdir'):
    //sssss;
    //$rsParentSection = CIBlockSection::GetByID();
    //if ($arParentSection = $rsParentSection->GetNext()) {
    $arFilter = array('IBLOCK_ID' => intval($_REQUEST["id"]), "ACTIVE" => 'Y'); // выберет потомков без учета активности 'DEPTH_LEVEL' => 3,
    $rsSect = CIBlockSection::GetList(array('sort' => 'asc'), $arFilter);
    while ($arSect = $rsSect->GetNext()) {
//        pre($arSect);
        $depth_level = $arSect["DEPTH_LEVEL"];
        switch ($depth_level):
            case 1: $dl = '. ';
                break;
            case 2: $dl = '.. ';
                break;
            case 3: $dl = '... ';
                break;
            case 4: $dl = '.... ';
                break;
            case 5: $dl = '..... ';
                break;
        endswitch;
        $data .= '<option value="' . $arSect["ID"] . '">' . $dl . '' . $arSect["NAME"] . ' [' . $arSect["ID"] . ']</option>'; // получаем подразделы
    }
    //}
    echo $data;
else:
/*   CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog");
    $PROP_CODE = $_REQUEST["prop"];
    $i = 1;
    $PROP_CODE_VAL = $_REQUEST["prop_val"];
    $percent = $_REQUEST["percent"];
    $el = CIBlockElement::GetList(array(), array("PROPERTY_$PROP_CODE" => $PROP_CODE_VAL), false, false, array("ID"));
    while ($ob = $el->Fetch()):
        $get_price = GetCatalogProductPrice($ob["ID"], 1);
        $price = $get_price["PRICE"];

        ($_REQUEST["znak"] == 'plus') ? $priceIpercent = $price + $price * $percent / 100 : $priceIpercent = $price - $price * $percent / 100;

        //print_r($get_price);
        $CURRENCY = $get_price["CURRENCY"];
        //echo $priceIpercent;

        $arFields = Array("PRODUCT_ID" => $ob["ID"], "CATALOG_GROUP_ID" => 1, "PRICE" => $priceIpercent, "CURRENCY" => $CURRENCY);
        $res = CPrice::GetList(array(), array("PRODUCT_ID" => $ob["ID"], "CATALOG_GROUP_ID" => 1));

        if ($arr = $res->Fetch()) {
            CPrice::Update($arr["ID"], $arFields);
        } else {
            CPrice::Add($arFields);
        }
        $name_res = CIBlockElement::GetByID($get_price["PRODUCT_ID"]);
        if ($ar_name_res = $name_res->GetNext()) {
            $name = $ar_name_res['NAME'];
            echo $i++ . '. ' . $name . ' [' . $get_price["PRODUCT_ID"] . '] <b>' . FormatCurrency($price, $CURRENCY) . '</b> => <b>' . FormatCurrency($priceIpercent, $CURRENCY) . '</b><br />';
        } else {
            echo 'error 701';
        }
endwhile;*/
endif;
?>