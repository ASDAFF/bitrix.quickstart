<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

__IncludeLang($_SERVER["DOCUMENT_ROOT"]."/local/components/novagr.shop/catalog.element/templates/landing/lang/ru/ajax.php");

$siteID = htmlspecialchars($_REQUEST['siteID']);


$rsSites = CSite::GetByID($siteID);
$arSite = $rsSites->Fetch();

$arResult = array();
$arResult['result'] = 'ERROR';
$arResult['message'] = '';
global $USER;
$userID = $USER->GetID();

global $APPLICATION;

if ($_REQUEST["ADD2BASKET"] == 1 && !empty($_REQUEST["elemId"])) {


    CModule::IncludeModule('catalog');
    CModule::IncludeModule("sale");
    $productID = (int)$_REQUEST["elemId"];
    // получаем корзину для текущего пользователя
    $arBasketItems = array();

    $dbBasketItems = CSaleBasket::GetList(
        array(
            "NAME" => "ASC",
            "ID" => "ASC"
        ),
        array(
            "FUSER_ID" => CSaleBasket::GetBasketUserID(),
            "LID" => SITE_ID,
            "ORDER_ID" => "NULL"
        ),
        false,
        false,
        array()
    );
    // ищем в корзине товар с нужным ID
    while ($arItems = $dbBasketItems->Fetch())
    {

        if ($arItems["PRODUCT_ID"] == $productID and $arItems['SUBSCRIBE']!=="Y" and $arItems['DELAY']!=="Y" ) {

            // получаем остатки по товару
            $arProduct = CCatalogProduct::GetByID($productID);
            $quantityProduct = $arProduct["QUANTITY"];

            $mxResult = CCatalogSku::GetProductInfo(
                $productID
            );
            if (is_array($mxResult)) {
                $action = new Novagroup_Classes_General_TimeToBuy($mxResult['ID'], $mxResult['IBLOCK_ID']);
                if ($action->checkAction()) {
                    $getAction = $action->getAction();
                    $quantityProduct = $getAction['PROPERTY_QUANTITY_VALUE'];
                }
            }
            if($quantityProduct>0)
            {
                // в том случае если количество равно остаткам для товара - не даем положить его в корзину еще раз
                $quantityProductInBasket = $arItems["QUANTITY"];
                //deb($quantityProduct);
                if ($quantityProductInBasket >=$quantityProduct) {

                    $result = array();

                    global $APPLICATION;
                    $APPLICATION->RestartBuffer();
                    $result['status'] = 'ERROR';
                    $result['type'] = 'PRODUCT_EXCEEDED_LIMIT';

                    $resultJson = json_encode($result);
                    die($resultJson);
                }
            }
            break;
        } // if ($arItems["PRODUCT_ID"] == $_REQUEST["id"] ) {

    }

    //$productID = intval($_REQUEST[$arParams["PRODUCT_ID_VARIABLE"]]);

    //get props sku
    $product_properties = array();
    $arPropsSku = array();

    $arParentSku = CCatalogSku::GetProductInfo($productID);
    if ($arParentSku && count($arParentSku) > 0)
    {
        $dbProduct = CIBlockElement::GetList(array(), array("ID" => $productID), false, false, array('IBLOCK_ID', 'IBLOCK_SECTION_ID'));
        $arProduct = $dbProduct->Fetch();

        $dbOfferProperties = CIBlock::GetProperties($arProduct["IBLOCK_ID"], array(), array("!XML_ID" => "CML2_LINK"));
        while($arOfferProperties = $dbOfferProperties->Fetch())
            $arPropsSku[] = $arOfferProperties["CODE"];

        $product_properties = CIBlockPriceTools::GetOfferProperties(
            $productID,
            $arParentSku["IBLOCK_ID"],
            $arPropsSku
        );
    }

    $resultAdd2Basket = Add2BasketByProductID($productID, 1, array("PRODUCT_PROVIDER_CLASS"=>"Novagroup_Classes_General_CatalogProvider"), $product_properties);

    if($resultAdd2Basket === false){
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        $result = array();
        $result['status'] = 'ERROR';
        $result['type'] = 'PRODUCT_NOT_AVAILABLE';
        $resultJson = json_encode($result);
        die($resultJson);
    }

    if ($resultAdd2Basket > 0 && $_REQUEST["act"] == "addToShelve") {
        CSaleBasket::Update($resultAdd2Basket, Array("DELAY" => "Y"));
    }

    // update user field for smart site catalog
    if ($userID > 0) {

        Novagroup_Classes_General_Basket::updateSizesColorsUserField($userID, $arProduct["IBLOCK_ID"]);

    }
    //var_dump($resultAdd2Basket);exit();

    $params = array(
        "PATH_TO_BASKET"=>"/cabinet/cart/",
        "PATH_TO_PERSONAL"=>"/cabinet/",
        "SHOW_PERSONAL_LINK"=>"N",
        "CACHE_TYPE"=>"A",
        "BUY_URL_SIGN"=>false,
    );
    $APPLICATION->IncludeComponent("novagroup:sale.basket.basket.line", "", $params, false, array('HIDE_ICONS' => 'Y'));

    $buffer = ob_get_contents();
    ob_end_clean();
    //echo $buffer;
    $result['status'] = 'OK';
    $result['type'] = '';

    //корректируем результат в зависимости от кодировки
    $rsSites = CSite::GetByID(SITE_ID);
    $arSite = $rsSites->Fetch();
    //echo "<pre>"; print_r($arSite["CHARSET"]); echo "</pre>";

    if (strtolower($arSite["CHARSET"]) == "windows-1251") {
        $buffer = iconv('windows-1251', 'UTF-8', $buffer);
    }

    $result['html'] = $buffer;

    $resultJson = json_encode($result);
    die($resultJson);

}

$res = CIBlock::GetList(
    Array(),
    Array( "CODE"=>'comments', 'SITE_ID'=>$siteID), true
);
if ($ar_res = $res->Fetch())
{
    $iblockId = $ar_res['ID'];
} else {
    return;
}


if ($_REQUEST["action"] == 'getSubscribed') {
    CModule::IncludeModule("sale");

    $arResult['result'] = 'OK';
    $arResult['isAuthorized'] = 0;
    $arResult['userEmail'] = '';

    $arResult['offersSubsribed'] = array();
    if ($userID >0 ) {
        $arResult['isAuthorized'] = 1;
        $arResult['userEmail'] = $USER->GetEmail();

        $dbBasketItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL", "SUBSCRIBE" => "Y"
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE",
                "PRODUCT_ID", "QUANTITY", "DELAY", "SUBSCRIBE",
                "CAN_BUY", "PRICE", "WEIGHT")
        );
        while ($arItems = $dbBasketItems->Fetch())
        {

            $arResult['offersSubsribed'][] = $arItems["PRODUCT_ID"];
        }

    }

}
elseif ($_REQUEST["action"] == 'comment' &&
    !empty($_REQUEST["productId"]) &&
    !empty($_REQUEST["REVIEW_TEXT"]) &&
    !empty($_REQUEST["productCode"])
) {

    if (strtolower($arSite["CHARSET"]) == "windows-1251") {
        //$siteUTF8 = false;

        foreach ($_REQUEST as $key => $item) {

            if (!empty($_REQUEST[$key])) $_REQUEST[$key] = iconv('UTF-8', 'windows-1251', $_REQUEST[$key]);

        }
    }

    $filt = Array("IBLOCK_ID"=> $iblockId, "NAME" => $_REQUEST["productId"]);

    $secRes =CIBlockSection::GetList(Array("SORT"=>""), $filt,false, Array("ID", "NAME"));
    if ($res=$secRes->Fetch()){

        $IBLOCK_SECTION = $res["ID"];

    } else {

        $bs = new CIBlockSection;
        $arFields = Array(
            "ACTIVE" => "Y",
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => $iblockId,
            "NAME" => $_REQUEST["productId"]
        );
        $IBLOCK_SECTION = $bs->Add($arFields);
    }
    $el = new CIBlockElement;
    $arLoad = array();

    if ($userID>0) {

        $arLoad ["NAME"] =$USER->GetFullName();
        $email = $USER->GetEmail();;

    } else {
        $arLoad ["NAME"] = $_REQUEST["REVIEW_AUTHOR"];
        $email = $_REQUEST["REVIEW_EMAIL"];
    }


    $PROP = array(
        "PRODUCT_CODE" => array($_REQUEST["productCode"]),
        "EMAIL" => array($email),
        "USER" => array($userID)
    );

    $arLoad ["ACTIVE"] = "Y";
    $arLoad ["IBLOCK_ID"] = $iblockId;
    $arLoad ["MODIFIED_BY"] = $userID;
    $arLoad ["DETAIL_TEXT"] = $_REQUEST["REVIEW_TEXT"];
    $arLoad ["ACTIVE_FROM"] = date("d.m.Y H:i:s");
    $arLoad ["IBLOCK_SECTION"] = $IBLOCK_SECTION;
    $arLoad ["PROPERTY_VALUES"] = $PROP;

    if ($newElemId = $el->Add($arLoad)) {
        //echo "New ID: ".$newElemId;
        $arResult['message'] = GetMessage("ADDED_LABEL");
        $arResult['date'] = $arLoad ["ACTIVE_FROM"];
        $arResult['name'] = $arLoad ["NAME"];
        $arResult['text'] = $arLoad ["DETAIL_TEXT"];
        $arResult['result'] = 'OK';
    }
    else {
        $arResult['result'] = 'ERROR';
        $arResult['message'] =  $el->LAST_ERROR;
        //deb( $el->LAST_ERROR);
        //deb($arLoad);
    }

    if (empty($arLoad ["NAME"])) {
        $arResult['message'] = GetMessage("LABEL_NAME_AUTHOR");
    }

}

if ($_REQUEST['ELEMENT_ID'] > 0 && $_REQUEST['AJAX_REFRESH'] == 1) {

    $filt = Array("IBLOCK_CODE"=> 'comments', "NAME" => $_REQUEST['ELEMENT_ID']);
    $arResult['fields'] = '';
    $arResult['userAutorized'] = '0';
    $arResult['userEmail'] = '';

    $secRes = CIBlockSection::GetList(Array("SORT"=>""), $filt,false, Array("ID", "NAME"));

    $arResult["count"] = 0;
    if ($res=$secRes->Fetch()){

        $IBLOCK_SECTION = $res["ID"];
        $arFComments = array("IBLOCK_ID" => $iblockId, "SECTION_ID" => $IBLOCK_SECTION );
        $arSelect = array(
            'ID',
            'NAME',
            'DETAIL_TEXT',
            'PROPERTY_EMAIL',
            'PROPERTY_USER'
        );

        if (!empty($_REQUEST["iNumPage"])) {

            $pageNumCom = $_REQUEST["iNumPage"];
        }
        else {
            $pageNumCom = 1;
        }

        $arNavStartParamsCom = array(
            'iNumPage' => $pageNumCom,
            'nPageSize' => 10,
            'bShowAll' => false
        );
        $rsElements = CIBlockElement::GetList(
            array('ACTIVE_FROM' => "DESC"),$arFComments, false, $arNavStartParamsCom, $arSelect
        );
        $arResult["count"] = $count = $rsElements->SelectedRowsCount();

        //deb($count);
        $arResult['NAV_STRING_COMMENTS'] = $rsElements -> GetPageNavStringEx($navComponentObject, "", "bootstrap");
        //deb(htmlspecialchars($arResult['NAV_STRING_COMMENTS']));
        $smileArr = array(
            ':D',
            ':lol:',
            ':-)',
            ';-)',
            '8)',
            ':-|',
            ':oops:',
            ':sad:',
            ':roll:',

        );
        $smileArr2 = array(
            '<img src="'.SITE_DIR.'include/images/smiles/laugh.gif">',
            '<img src="'.SITE_DIR.'include/images/smiles/lol.gif" >',
            '<img src="'.SITE_DIR.'include/images/smiles/smile.gif">',
            '<img src="'.SITE_DIR.'include/images/smiles/wink.gif">',
            '<img src="'.SITE_DIR.'include/images/smiles/cool.gif">',
            '<img src="'.SITE_DIR.'include/images/smiles/normal.gif">',
            '<img src="'.SITE_DIR.'include/images/smiles/redface.gif">',
            '<img src="'.SITE_DIR.'include/images/smiles/sad.gif">',
            '<img src="'.SITE_DIR.'include/images/smiles/rolleyes.gif">',
        );

        $commentsText = '';
        while ($comment = $rsElements -> Fetch())
        {

            $text = $comment["DETAIL_TEXT"];
            $text = str_replace($smileArr, $smileArr2, $text);

            $commentsText .= '
			<div class="even">
			<div class="rbox">
			<div class="comment-box usertype-guest">
			<a href="#" class="comment-anchor"></a>
			<span class="comment-author">' . $comment["NAME"] . '</span>
			<span class="comment-date">' . $comment["ACTIVE_FROM"] . '</span>
			<div class="comment-body">' . $text . '</div>
			</div>
			<div class="clear"></div>
			</div>
			</div>
			';
        }

        //echo $commentsText;
        $commentsText .= $arResult["NAV_STRING_COMMENTS"];
        $arResult['commentsText'] = "<p>".GetMessage('COMMENTS_LABEL').":</p>".$commentsText;
        //deb($arResult['commentsText']);
    }
/*
    if ($userID > 0) {
        $arResult['userAutorized'] = 1;
        $arResult['userEmail'] = $USER->GetEmail();

    } else {
        $arResult['fields'] = '
		<label><p>'.GetMessage("LABEL_NAME").'<span class="starrequired">*</span></p>

		<input name="REVIEW_AUTHOR" id="REVIEW_AUTHOR" type="text" value="" >
		</label>
		';
        $arResult['fieldsEmail'] = '<label><p>'.GetMessage("LABEL_EMAIL").'</p>

		<input type="text" name="REVIEW_EMAIL" id="REVIEW_EMAIL" maxlength="50" value="" >
		</label>';
    }*/

    $arResult['result'] = 'OK';

    $arResult['userEmail'] = $USER->GetEmail();
    if (empty($arResult['userEmail'])) $arResult['userEmail'] = '';

    $arResult['result'] = 'OK';
    //$arResult['fields'] = 'LAST_ERROR';
    //$arResult['message'] =  $el->LAST_ERROR;
}

if (strtolower($arSite["CHARSET"]) == "windows-1251") {
    //$siteUTF8 = false;
    foreach ($arResult as $key => $item) {

        if (!empty($arResult[$key]) && !is_array($arResult[$key])) $arResult[$key] = iconv( 'windows-1251', 'UTF-8', $arResult[$key]);

    }
}

$arResultJson = json_encode($arResult);
die($arResultJson);
?>