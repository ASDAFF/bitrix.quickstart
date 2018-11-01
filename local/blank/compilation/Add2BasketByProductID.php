<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

$pid    	= (int)$_REQUEST['idProduct'];
$quantity   = (!empty($_REQUEST['quantity'])) ? $_REQUEST['quantity'] : 1 ;


function AddToBasket( $pid,$quantity )
{
    if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") )
    {
        $ar_res = CCatalogProduct::GetByIDEx($pid);
        if($ar_res){
            $result =  Add2BasketByProductID($pid,$quantity); // если ошибки -  $ex = $APPLICATION->GetException();
            if($result){
                $arrSendResult['ERROR'] = 'N';
                $arrSendResult['TEXT'] = 'OK';
                echo json_encode($arrSendResult);
            }else{
                $arrSendResult['ERROR'] = 'Y';
                $arrSendResult['TEXT'] = 'Error';
                echo json_encode($arrSendResult);
            }
        }else{
            if( Add2BasketByProductID($pid,$quantity) )
            {
                $arrSendResult['ERROR'] = 'N';
                $arrSendResult['TEXT'] = 'OK';
                echo json_encode($arrSendResult);
            }
        }
    }
}



if( !empty($pid)  && !empty($quantity) )
{
    AddToBasket( $pid,$quantity );
}
else
{
    $arrSendResult['ERROR'] = 'Y';
    $arrSendResult['TEXT'] = 'Error with parameters';
    echo json_encode($arrSendResult);
}


