<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); 
if(($_REQUEST['action']=='ADD2BASKET' || $_REQUEST['action']=='ADD_DELAY2BASKET') && intval($_REQUEST['id'])>0){
    CModule::includeModule('catalog');
    CModule::includeModule('iblock');
    $id=$_REQUEST['id'];
    $mxResult = CCatalogSku::GetProductInfo($id);
    if (is_array($mxResult))$ib=$mxResult['IBLOCK_ID']; 
    else $ib=CIBlockElement::GetIBlockByID($id);
    $quantity=intval($_REQUEST['quantity'])>0?intval($_REQUEST['quantity']):'1';
    if($_REQUEST['prop']){
        $propsTemp=json_decode(urldecode($_REQUEST['prop']));
        $product_properties = CIBlockPriceTools::GetOfferProperties($id,$ib,$propsTemp,array()); 
    }
    Add2BasketByProductID($id, $quantity, array(), $product_properties);
   
}
include 'basket.php';

?>