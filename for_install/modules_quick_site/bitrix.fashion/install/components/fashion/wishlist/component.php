<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}
/*************************************************************************
	Processing of received parameters
*************************************************************************/

/*if($USER->IsAuthorized())
{*/
    if ($_POST["ACTION_REMOVE"] == "Y" && !empty($_POST["ID"]))
    {
        $productId = $_POST["ID"];
        $usr = CUser::GetByID($USER->GetID());
        $userData = $usr->Fetch();
        $product = CIBlockElement::GetByID($productId);
        $product = $product->GetNext();

        if (in_array($productId, $userData["UF_WISHLIST"]))
        {
            if(($key = array_search($productId, $userData["UF_WISHLIST"])) !== false)
            {
                unset($userData["UF_WISHLIST"][$key]);
            }
            $usr = new CUser();
            $res = $usr->Update($USER->GetID(), $userData);
        }
    }

    if($USER->IsAuthorized()&&!empty($_POST["send"]) && !empty($_POST["email"])) { 
        $email = trim($_POST["email"]);
        $arEventFields = array(
            "LOGIN" => $USER->GetLogin(),
            "URL" => "http://".$_SERVER["HTTP_HOST"].SITE_DIR."wishlist/".$USER->GetLogin()."/",
            "EMAILTO" => $email,
			"EMAILFROM" => $USER->GetEmail(),
			"NAME" => $USER->GetFullName()
        );

        CEvent::Send("UF_WISHLIST_SEND", SITE_ID, $arEventFields, "N", "");
		
		$tourl = $APPLICATION->GetCurPageParam("result=Y", array(
			"login",
			"logout",
			"register",
			"forgot_password",
			"change_password"
		));
		LocalRedirect($tourl);
    }

    $arResult["CURRENT_USER_ID"] = $USER->GetID();
    if(!empty($arParams["LOGIN"]))
    {
        $user = CUser::GetByLogin($arParams["LOGIN"]);
        if($arParams["LOGIN"] == $USER->GetLogin()) {
            $arResult["MY_WISHLIST"] = true;
        }
    }
    else
    {
        $arResult["MY_WISHLIST"] = true;
        $user = CUser::GetByID($arResult["CURRENT_USER_ID"]);
    }
	
    $user = $user->Fetch();
    CModule::IncludeModule("catalog");
    foreach($user["UF_WISHLIST"] as $productId)
    {
        $item = array();
        $item["PRODUCT_ID"] = $productId;
        $resGetByID = CIBlockElement::GetByID($item["PRODUCT_ID"]);
        $arPrice = CCatalogProduct::GetOptimalPrice($item["PRODUCT_ID"], 1, $USER->GetUserGroupArray(), "N");
        $item["PRICE"] = $arPrice["DISCOUNT_PRICE"];

        if ($arGetByID = $resGetByID->GetNext()) {

            $item["BASE_PRICE"] = CPrice::GetBasePrice($item["PRODUCT_ID"]);
            $item["NAME"] = $arGetByID["NAME"];
            $resGetList = CIBlockElement::GetList(
                array(),
                array("IBLOCK_ID"=>$arGetByID["IBLOCK_ID"], "ID"=>$item["PRODUCT_ID"], "ACTIVE"=>"Y"),
                false,
                array("nTopCount"=>1),
                array("ID", "IBLOCK_ID", "PROPERTY_item_color.NAME", "PROPERTY_item_color.CODE", "PROPERTY_item_color.DETAIL_PICTURE", "PROPERTY_item_color.PROPERTY_hex", "PROPERTY_item_size.NAME")
            );
            if ($ob = $resGetList->GetNextElement()) {
                $arFields = $ob->GetFields();
                $item["OFFER"] = $arFields;

                $arProps = $ob->GetProperties();
                $item["OFFER"]["PROPERTY_ITEM_MORE_PHOTO_VALUE"] = $arProps["item_more_photo"]["VALUE"][0];

                $arResGetByIDEx = CCatalogProduct::GetByIDEx($arProps["model"]["VALUE"]);
                $item["OFFER"]["models_hit"]  = (strlen($arResGetByIDEx["PROPERTIES"]["models_hit"]["VALUE"]) > 0 ? 1 : 0);
                $item["OFFER"]["models_new"]  = (strlen($arResGetByIDEx["PROPERTIES"]["models_new"]["VALUE"]) > 0 ? 1 : 0);
                $item["OFFER"]["models_sale"] = ($item["BASE_PRICE"]["PRICE"] > $item["PRICE"] ? 1 : 0);

                $item["DETAIL_PAGE_URL"] = rtrim($arResGetByIDEx["DETAIL_PAGE_URL"], "/") . "/" . $arFields["PROPERTY_ITEM_COLOR_CODE"] . "/";
            }
        }
        $arResult["ITEMS"][] = $item;
    }

    if(!empty($_POST["buyAll"])) {
        foreach($user["UF_WISHLIST"] as $productId) {
            $productProperties = array();
            $rsMainBID = CIBlockElement::GetByID($productId);
            if($arMainBID = $rsMainBID->GetNext()){
                $dbLink = CIBlockElement::GetProperty($arMainBID['IBLOCK_ID'], $productId, array("sort" => "asc"), Array("CODE"=>"model"));
                if($arLink = $dbLink->Fetch()){
                    $productProperties = CIBlockPriceTools::GetOfferProperties(
                        $productId,
                        $arLink['LINK_IBLOCK_ID'],
                        array("item_color", "item_size", "item_article")
                    );
                }
            }

            if(CSiteFashionStore::DVSAdd2BasketByProductID($productId, 1, array(), $productProperties)){
				LocalRedirect($arParams["BASKET_URL"]);
            }
        }        
    }

    $this->IncludeComponentTemplate();
/*}*/
?>