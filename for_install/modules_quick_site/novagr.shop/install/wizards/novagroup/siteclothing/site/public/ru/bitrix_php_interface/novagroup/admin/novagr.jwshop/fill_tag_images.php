<?
if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true ) return;

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("NOVAGROUP_FILL_IMAGES_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("NOVAGROUP_FILL_IMAGES_TITLE")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>
<?php
/**
 *  скрипт заполняет поле TAGS у нужных образов
 *
 */
CModule::IncludeModule("iblock");
//deb($_REQUEST);

$arFilter = array('IBLOCK_ID' =>  (int)$_REQUEST['NOVAGROUP_FILL_IBLOCK_ID'] );

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

// получаем образы


$arSelect = array(
    'ID',
    'NAME',
    'IBLOCK_ID', 'PROPERTY_GROUP',
    'PROPERTY_GENDER', 'PROPERTY_EVENT.NAME', 'PROPERTY_STYLE.NAME',
    'PROPERTY_PRODUCTS'
    //, 'PROPERTY_MODEL'
);
//deb($arFilter);
$rsSubElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
if (intval($rsSubElement->SelectedRowsCount())>0) {
    // массив для ID моделей
    //$arModelsIDS = array();
    // массив для ID товаров
    $arProductsIDS = array();

    $i =0;
    $images = array();
    $sectionIDS = array();
    while($data = $rsSubElement -> Fetch())
    {
        //deb($data);
        //$arModelsIDS = array_merge($arModelsIDS, $data["PROPERTY_MODEL_VALUE"]);
        $arProductsIDS = array_merge($arProductsIDS, $data["PROPERTY_PRODUCTS_VALUE"]);
        $images[$data["ID"]] = $data;
        $sectionIDS[] = $data["PROPERTY_GROUP_VALUE"];
        $i++;
    }



    $sectionIDS =  array_unique($sectionIDS);
    // получаем названия секций
    $arSections = array();
    if (!empty($sectionIDS[0])) {
        //deb($sectionIDS);

        if (count($sectionIDS)) {

            $arSelectS = array( 'ID', 'NAME', 'SORT', 'IBLOCK_ID' );
            $arFilterS = array("ID" => $sectionIDS, "IBLOCK_ID" => (int)$_REQUEST['NOVAGROUP_FILL_IBLOCK_ID']  );
            $rsSection = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilterS, false, $arSelectS);
            while($data = $rsSection -> Fetch())
            {
                //deb($data);
                $arSections[$data["ID"]] = $data["NAME"];

            }
        }
        //deb($arSections);
    }
    //$arModelsIDS = array_unique($arModelsIDS);
    $arProductsIDS = array_unique($arProductsIDS);
    //deb($i);
    //deb($arModelsIDS);
    //deb($arProductsIDS);
    // получаем названия моделей
    /*if (count($arModelsIDS)) {
        $arFilterModels = array('IBLOCK_CODE' => 'models', "ID" => $arModelsIDS);
        $arSelectModels = array('ID', 'NAME', 'IBLOCK_ID');
        //deb($arFilterModels);
        $rsModels = CIBlockElement::GetList(false, $arFilterModels, false, false, $arSelectModels);

        $arModels = array();
        while($data = $rsModels -> Fetch())
        {
            //deb($data);
            $arModels[$data["ID"]] = $data["NAME"];
        }
    }*/
    //deb($arModels);
    // получаем названия товаров
    if (count($arProductsIDS)) {
        $arFilterProducts = array("ID" => $arProductsIDS);
        $arSelectProducts = array('ID', 'NAME', 'IBLOCK_ID');
        //deb($arFilterProducts);
        $rsProducts = CIBlockElement::GetList(false, $arFilterProducts, false, false, $arSelectProducts);

        $arProducts = array();
        while($data = $rsProducts -> Fetch())
        {
            //deb($data);
            $arProducts[$data["ID"]] = $data["NAME"];
        }
    }
    //deb($arProducts);
    $j=0;
    if (count($images)) {

        foreach ($images as $key => $value) {

            $tagsContent = array();
            /*foreach ($value["PROPERTY_MODEL_VALUE"] as $item) {

                if (!empty($arModels[$item])) $tagsContent[] =$arModels[$item];
            }*/
            foreach ($value["PROPERTY_PRODUCTS_VALUE"] as $item) {

                if (!empty($arProducts[$item])) $tagsContent[] =$arProducts[$item];
            }

            /*if ($value["PROPERTY_GENDER_VALUE"] == 'W') {
                $tagsContent[] = 'женский';

            } elseif ($value["PROPERTY_GENDER_VALUE"] == 'M') {
                $tagsContent[] = 'мужской';
            } */
            if (!empty($value["PROPERTY_EVENT_NAME"])) $tagsContent[] = $value["PROPERTY_EVENT_NAME"];
            if (!empty($value["PROPERTY_STYLE_NAME"])) $tagsContent[] = $value["PROPERTY_STYLE_NAME"];

            if (
                !empty($value["PROPERTY_GROUP_VALUE"]) &&
                !empty($arSections[$value["PROPERTY_GROUP_VALUE"]])
            ) {
                $tagsContent[] = $arSections[$value["PROPERTY_GROUP_VALUE"]];
            }

            foreach ($tagsContent as $key => $item)
                $tagsContent[$key] = trim($item);
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
    echo CAdminMessage::ShowNote(GetMessage("NOVAGROUP_FILL_IMAGES_REFRESH",array('#COUNT#'=>$j)));
}
//get iblock list
$res = CIBlock::GetList(
    Array(),
    Array(
        'CODE'=>'collections',
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

            <div >
                <label>
                    <?php
                    echo GetMessage('NOVAGROUP_FILL_IBLOCK'); echo SelectBoxFromArray("NOVAGROUP_FILL_IBLOCK_ID", $NOVAGROUP_FILL_IBLOCK_ID, (int)$_REQUEST['NOVAGROUP_FILL_IBLOCK_ID'] );
                    ?>
                </label>
                <label>
                    <?=GetMessage('NOVAGROUP_FILL_ID_IMAGES_FROM')?><input type="text" name="value_min" value="<?=( $_REQUEST["value_min"] ? $_REQUEST["value_min"] : '')?>" />
                </label>
                <label>
                    <?=GetMessage('NOVAGROUP_FILL_ID_IMAGES_TO')?><input type="text" name="value_max" value="<?=( $_REQUEST["value_max"] ? $_REQUEST["value_max"] : '')?>" />
                </label>

            </div>
        </td>

    </tr>

    <?
            $tabControl->Buttons();
    ?>

    <input type="submit" name="fill_tag_sbm" value="<?echo GetMessage("NOVAGROUP_FILL_TAG_IMAGES_BUTTON_VALUE")?>" title="<?echo GetMessage("NOVAGROUP_FILL_TAG_IMAGES_BUTTON_TITLE")?>" />
    <input type="submit" name="fill_tag_all_brands_sbm" value="<?echo GetMessage("NOVAGROUP_FILL_ALL_TAG_IMAGES_BUTTON_VALUE")?>" title="<?echo GetMessage("NOVAGROUP_FILL_ALL_TAG_IMAGES_BUTTON_TITLE")?>" />

    <?
            $tabControl->End();
    ?>
</form>

<?echo BeginNote();?>
<?= GetMessage("NOVAGROUP_FILL_TAG_IMAGES_NOTE"); ?>
<?echo EndNote(); ?>
