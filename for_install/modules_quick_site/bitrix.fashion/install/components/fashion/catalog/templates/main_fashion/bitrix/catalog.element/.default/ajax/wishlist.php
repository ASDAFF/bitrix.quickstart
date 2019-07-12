<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

if(isset($_REQUEST['productId'])&&intval($_REQUEST['productId'])>0){
    if(isset($_REQUEST['status']) && !empty($_REQUEST['status'])) {
        $productId = $_REQUEST["productId"];
        $status = $_REQUEST["status"];

        $rsUser = CUser::GetByID($USER->GetID());
        $arUser = $rsUser->Fetch();
        $mergedFavourites = array();
        foreach($arUser["UF_WISHLIST"] as $favouriteProductId) {
            $mergedFavourites[] = $favouriteProductId;
        }

        if ($status == "add") {
            $mergedFavourites[] = $productId;
            $mergedFavourites = array_unique($mergedFavourites);
            $fields = array(
                "UF_WISHLIST" => $mergedFavourites
            );
            $USER->Update($USER->GetID(), $fields);
        }
        elseif($status == "remove") {
            $mergedFavourites = array_diff($mergedFavourites, array($productId));
            $fields = array(
                "UF_WISHLIST" => $mergedFavourites
            );
            $USER->Update($USER->GetID(), $fields);
        }
        else
        {
            if(in_array($productId, $mergedFavourites))
            {
                echo json_encode(array("success" => true, "isinwishlist" => true));
                exit;
            }
            else
            {
                echo json_encode(array("success" => true, "isinwishlist" => false));
                exit;
            }
        }

        echo json_encode(array("success" => true));

    }
}
else
{
    echo json_encode(array("success" => false));
}