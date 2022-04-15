<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
 
$action = $_REQUEST['action'];
 
$id = intval($_REQUEST['id']);

if(!$id)
    return;

CModule::IncludeModule('sale'); 
switch ($action) {
    case 'delete_from_cart':
        CSaleBasket::Delete($id);
        break;
    case 'cart_change_count':
        $cnt = intval($_REQUEST['count']);
        if($cnt)
            CSaleBasket::Update($id, array(  "QUANTITY" => $cnt ));
        break;
   
    case 'cart_delay': 
        CSaleBasket::Update($id, array(   "DELAY" => "Y"  ));
        break;
    
    case 'cart_undelay': 
        CSaleBasket::Update($id, array(   "DELAY" => "N"  ));
        break;
    
    case 'add2compare':
        add2compare($id);
        break;
    case 'removeFromCompare':
        removeFromCompare($id);
        break;
    case 'add2compare_':
        add2compare($id);
        $APPLICATION->IncludeComponent( "devteam:compare.added", "", Array('AJAX'=>'Y')  );
    break;
    case 'removeFromCompare_':
        removeFromCompare($id); 
        $APPLICATION->IncludeComponent( "devteam:compare.added", "", Array('AJAX'=>'Y')  );
    break;
 
    default:
        break;
} 