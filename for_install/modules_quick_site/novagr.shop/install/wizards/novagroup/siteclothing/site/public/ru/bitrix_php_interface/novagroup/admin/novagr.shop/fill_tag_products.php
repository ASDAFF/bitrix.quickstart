<?
if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true ) return;

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("NOVAGROUP_FILL_PRODUCTS_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("NOVAGROUP_FILL_PRODUCTS_TITLE")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>
<?php
/**
 *  скрипт заполняет поле TAGS у нужных товаров
 *
 */
CModule::IncludeModule("iblock");
CModule::IncludeModule("search");
//deb($_REQUEST);
$arParams = array();
$arParams["CATALOG_IBLOCK_ID"] = (int)$_REQUEST['NOVAGROUP_FILL_IBLOCK_ID'];
// получаем цвета

$arFilterM = array(
    'ACTIVE' => "Y",
    'IBLOCK_CODE' => array("COLORS")
);

$arSelectM = array(
    'ID',
    'NAME',
    'IBLOCK_CODE',
    'PREVIEW_PICTURE',
    'DETAIL_TEXT',
    'SORT'
);
$rsElement = CIBlockElement::GetList(false, $arFilterM, false, false, $arSelectM);
while($data = $rsElement -> GetNext())
{
    $arResult['mixData'][ $data['ID'] ] = $data;

}
//deb($arResult['mixData']);



$arFilter = array();

// заполняем теги у определенных ID
if ($_REQUEST["fill_tag_sbm"] and $_REQUEST['NOVAGROUP_FILL_IBLOCK_ID'] > 0 ) {

    $minId = (int)$_REQUEST["value_min"];
    $maxId = (int)$_REQUEST["value_max"];
    if ($minId && $maxId) {
        $arFilter[">=ID"] = $minId;
        $arFilter["<=ID"] = $maxId;
    } elseif ($minId)  {
        $arFilter["ID"] = $minId;
    } elseif ($maxId) {
        $arFilter["ID"] = $maxId;
    } else {
        $arFilter["ID"] = '-1';
    }

} elseif ($_REQUEST["fill_tag_all_brands_sbm"] and $_REQUEST['NOVAGROUP_FILL_IBLOCK_ID'] > 0 ) {
    // заполняем теги у всех ID

} else {
    // иначе просто выводим форму
    $arFilter["ID"] = '-1';
}


//обходим все элементы в полученных иб
$arFilter['IBLOCK_ID' ] = (int)$_REQUEST['NOVAGROUP_FILL_IBLOCK_ID'];

// получаем товары
$arSelect = array(
    'ID',
    'NAME',
    'IBLOCK_ID', 'IBLOCK_SECTION_ID','CATALOG_GROUP_1'
);
//deb($arFilter);
//deb($arSelect);

$sectionIDS = array();

$rsSubElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
$countElems = $rsSubElement->SelectedRowsCount();

//deb($countElems);
if (intval($countElems)>0) {
    // массив для ID образцов
    $arSamplesIDS = array();
    // массив для ID цветов
    $arColorsIDS = array();

    $i =0;
    $products = array();
    $arSections = array();
    while ($data = $rsSubElement -> GetNextElement())
    {
        $elem = array();
        $arFields = $data->GetFields();
        $arProps = $data->GetProperties();
        //deb($arProps);
        //deb($arFields);
        $elem["IBLOCK_SECTION_ID"] = $arFields["IBLOCK_SECTION_ID"];
        $sectionIDS[] = $arFields["IBLOCK_SECTION_ID"];
        // массив для цветов
        $arColors = $arSizes = array();

        // TODO в настройки параметры
        $arParams["PRODUCT_ID_VARIABLE"] = 'id';
        $arParams["ACTION_VARIABLE"] = 'action';

        $arParams['ELEMENT_ID'] =  $arFields['ID'];

        // обрабатываем товарные предложения
        // TODO настройки вынести  CML2_LINK
        $arParams["OFFERS_FIELD_CODE"] = array("NAME");
        $arParams["OFFERS_PROPERTY_CODE"] = array("COLOR","STD_SIZE");

        $arResult["CURRENT_ELEMENT"]["COLORS"] = array();

        $arResult["OFFERS"] = array();

        //This function returns array with prices description and access rights
        //in case catalog module n/a prices get values from element properties

        $catalogPrices = new Novagroup_Classes_General_CatalogPrice(0, $arParams["CATALOG_IBLOCK_ID"]);
        $arResultPrices = $catalogPrices->getCatalogPrices();
        $arResult["CAT_PRICES"] = $arResultPrices;
        $arConvertParams = array();

        $arOffers = CIBlockPriceTools::GetOffersArray(
            $arParams["CATALOG_IBLOCK_ID"]
            ,array($arParams['ELEMENT_ID'])
            ,array(
                $arParams["OFFERS_SORT_FIELD"] => $arParams["OFFERS_SORT_ORDER"],
                "ID" => "DESC",
            )
            ,$arParams["OFFERS_FIELD_CODE"]
            ,$arParams["OFFERS_PROPERTY_CODE"]
            ,0 // $arParams["OFFERS_LIMIT"]
            ,$arResult["CAT_PRICES"]
            ,1 // $arParams['PRICE_VAT_INCLUDE']
            ,$arConvertParams
        );

        //deb($arOffers);

        // перебираем ТП - получаем цвета
        $elem["CML2_BAR_CODE"] =  array();
        $elem["CML2_ARTICLE"] = array();
        foreach($arOffers as $arOffer)
        {
            //deb($arOffer["DISPLAY_PROPERTIES"]["COLOR"]);
            $arColors[] = $arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"];
            $arSizes[] = strip_tags($arOffer["DISPLAY_PROPERTIES"]["STD_SIZE"]["DISPLAY_VALUE"]);
            if (!empty($arOffer["PROPERTIES"]["CML2_BAR_CODE"]["VALUE"])) $elem["CML2_BAR_CODE"][] = $arOffer["PROPERTIES"]["CML2_BAR_CODE"]["VALUE"];
            if (!empty($arOffer["PROPERTIES"]["CML2_ARTICLE"]["VALUE"])) $elem["CML2_ARTICLE"][] = $arOffer["PROPERTIES"]["CML2_ARTICLE"]["VALUE"];
        }
        $arSizes = array_unique($arSizes);
        $elem["COLORS"] = array_unique($arColors);
        $elem["STD_SIZE"] = implode(", ",$arSizes);

        $elem["ID"] = $arFields["ID"];
        $elem["NAME"] = $arFields["NAME"];
        $elem["IBLOCK_ID"] = $arFields["IBLOCK_ID"];

        $arSamplesIDS = array_merge($arSamplesIDS, $arProps["SAMPLES"]["VALUE"]);
        $elem["SAMPLES"] = $arProps["SAMPLES"]["VALUE"];

        // материал
        $elem["MATERIAL"] = '';

        if (!empty($arProps["MATERIAL"]["VALUE"])) {

            $ar_res  = CIBlockElement::GetByID($arProps["MATERIAL"]["VALUE"])->GetNext();

            $elem["MATERIAL"] = $ar_res["NAME"];
        }

        // бренд
        $elem["VENDOR"] = '';

        if (!empty($arProps["VENDOR"]["VALUE"])) {

            $ar_res  = CIBlockElement::GetByID($arProps["VENDOR"]["VALUE"])->GetNext();

            $elem["VENDOR"] = $ar_res["NAME"];
        }

        // получаем названия секций
        if($arFields["IBLOCK_SECTION_ID"]>0 and !isset($arSections[$arFields["IBLOCK_SECTION_ID"]]))
        {
            $rsSection = CIBlockSection::GetNavChain(false, $arFields["IBLOCK_SECTION_ID"]);
            while ($rsSection->ExtractFields("SECTION_")) {
                $arSections[$arFields["IBLOCK_SECTION_ID"]][] = $SECTION_NAME;
            }
        }

        // Цена
        $elem["PRICE"] = '';
        if (!empty($arFields["CATALOG_PRICE_1"])) {
            // убираем последние нули .00
            if (endsWith($arFields["CATALOG_PRICE_1"], '.00')) {
                $arFields["CATALOG_PRICE_1"] = substr($arFields["CATALOG_PRICE_1"],0, strlen($arFields["CATALOG_PRICE_1"])-3);
            }

            $elem["PRICE"] = $arFields["CATALOG_PRICE_1"];
        }

        $products[$arFields["ID"]] = $elem;
        $i++;
        //if ($i>20) break;

    } // end while

    $sectionIDS =  array_unique($sectionIDS);
    $arSamplesIDS = array_unique($arSamplesIDS);


    $arSelectProp = array('ID', 'NAME', 'IBLOCK_ID');

    // получаем названия образцов
    if (count($arSamplesIDS)) {
        $arFilterSamples = array('IBLOCK_CODE' => 'samples', "ID" => $arSamplesIDS);

        //deb($arFilterSamples);
        $rsSamples = CIBlockElement::GetList(false, $arFilterSamples, false, false, $arSelectProp);

        $arSamples = array();
        while($data = $rsSamples -> Fetch())
        {
            //deb($data);
            $arSamples[$data["ID"]] = $data["NAME"];
        }
    }
    //deb($arSamples);

    $j=0;
    if (count($products)) {

        foreach ($products as $key => $value) {

            $tagsContent = array();
            foreach ($value["SAMPLES"] as $item) {

                if (!empty($arSamples[$item])) $tagsContent[] =$arSamples[$item];
            }
            foreach ($value["COLORS"] as $item) {
                //$elem["COLORS"]
                if (!empty($arResult['mixData'][$item]["NAME"])) {
                    $tagsContent[] = $arResult['mixData'][$item]["NAME"];
                        $ar = stemming($arResult['mixData'][$item]["NAME"], "ru");
                        if(is_array($ar)){
                            foreach($ar as $morphItem=>$priority)
                            {
                                if(mb_strtolower(mb_substr($arResult['mixData'][$item]["NAME"],-2,1,LANG_CHARSET))==GetMessage("Y"))
                                    $manyColors = $morphItem.GetMessage("YI");
                                else
                                    $manyColors = $morphItem.GetMessage("II");
                                $tagsContent[] = mb_strtolower($manyColors);
                            }
                        }
                }
            }

            //if (!empty($value["SELLER"])) $tagsContent[] = $value["SELLER"];
            if (!empty($value["MATERIAL"])) $tagsContent[] = $value["MATERIAL"];
            if (!empty($value["SILHOUETTE"])) $tagsContent[] = $value["SILHOUETTE"];
            if (!empty($value["VENDOR"])) $tagsContent[] = $value["VENDOR"];
            if (!empty($value["STD_SIZE"])) $tagsContent[] = $value["STD_SIZE"];
            //if (!empty($value["PRICE_CATEGORY"])) $tagsContent[] = $value["PRICE_CATEGORY"];
            if (!empty($value["PRICE"])) $tagsContent[] = $value["PRICE"];

            if (
                !empty($value["IBLOCK_SECTION_ID"]) &&
                !empty($arSections[$value["IBLOCK_SECTION_ID"]]) &&
                is_array($arSections[$value["IBLOCK_SECTION_ID"]])
            ) {
                foreach($arSections[$value["IBLOCK_SECTION_ID"]] as $arSection)
                    $tagsContent[] = $arSection;
            }

            foreach ($value["CML2_BAR_CODE"] as $item) {
                $tagsContent[] = ($item);
            }
            foreach ($value["CML2_ARTICLE"] as $item) {
                $tagsContent[] = ($item);
            }
            //if (!empty($value["PA_LENGTH"])) $tagsContent[] = $value["PA_LENGTH"];
            //if (!empty($value["PA_CUT"])) $tagsContent[] = $value["PA_CUT"];
            $tagsString = implode(",", $tagsContent);
            // устанавливаем св. во тег

            $el = new CIBlockElement;
            //deb($tagsString);
            $arLoadProductArray = Array(
                "TAGS"    => $tagsString,
            );

            $res = $el->Update($value["ID"], $arLoadProductArray);
            $error = $el->LAST_ERROR;
            if (!empty($error)) echo "<p style='color:red'>Error:  $error</p>";
            $j++;
            //deb($value);
            //deb($tagsContent);
        }
    }
}
if ($j>0) {
    echo CAdminMessage::ShowNote(GetMessage("NOVAGROUP_FILL_PRODUCTS_REFRESH",array('#COUNT#'=>$j)));
}
//get iblock list
$res = CIBlock::GetList(
    Array(),
    Array(
        'CODE'=>'novagr_standard_products',
        'TYPE'=>'catalog',
    )
);
$arIBlockReference = $arIBlockReferenceID = $NOVAGROUP_FILL_IBLOCK_ID =array();
while($ar_res = $res->Fetch()){ // цикл по информационным блокам
    $arIBlockReference[] = $ar_res['NAME'];
    $arIBlockReferenceID[] = $ar_res['ID'];
}
$NOVAGROUP_FILL_IBLOCK_ID['REFERENCE'] = $arIBlockReference;
$NOVAGROUP_FILL_IBLOCK_ID['REFERENCE_ID'] = $arIBlockReferenceID;
?>
<form method="GET" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo htmlspecialcharsbx(LANG)?>" name="fs1">
    <?
            $tabControl->Begin();
            $tabControl->BeginNextTab();   
    ?>
    <tr>
        <td colspan="2" width="100%">

            <div>
                <label>
                    <?php
                    echo GetMessage('NOVAGROUP_FILL_IBLOCK'); echo SelectBoxFromArray("NOVAGROUP_FILL_IBLOCK_ID", $NOVAGROUP_FILL_IBLOCK_ID, (int)$_REQUEST['NOVAGROUP_FILL_IBLOCK_ID'] );
                    ?>
                </label>
                <label>
                    <?=GetMessage('NOVAGROUP_FILL_ID_PRODUCTS_FROM')?><input type="text" name="value_min" value="<?=( $_REQUEST["value_min"] ? $_REQUEST["value_min"] : '')?>" />
                </label>
                <label>
                    <?=GetMessage('NOVAGROUP_FILL_ID_PRODUCTS_TO')?><input type="text" name="value_max" value="<?=( $_REQUEST["value_max"] ? $_REQUEST["value_max"] : '')?>" />
                </label>
            </div>
        </td>

    </tr>

    <?
            $tabControl->Buttons();
    ?>

    <input type="submit" name="fill_tag_sbm" value="<?echo GetMessage("NOVAGROUP_FILL_TAG_PRODUCTS_BUTTON_VALUE")?>" title="<?echo GetMessage("NOVAGROUP_FILL_TAG_PRODUCTS_BUTTON_TITLE")?>" />
    <input type="submit" name="fill_tag_all_brands_sbm" value="<?echo GetMessage("NOVAGROUP_FILL_ALL_TAG_PRODUCTS_BUTTON_VALUE")?>" title="<?echo GetMessage("NOVAGROUP_FILL_ALL_TAG_PRODUCTS_BUTTON_TITLE")?>" />

    <?
            $tabControl->End();
    ?>
</form>

<?echo BeginNote();?>
<?= GetMessage("NOVAGROUP_FILL_TAG_PRODUCTS_NOTE"); ?>
<?echo EndNote(); ?>
