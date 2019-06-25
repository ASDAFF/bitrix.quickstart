<?
    // Доступные переменные
    // $arPayment
    // $arHandler
    // $arHandlerFields



    $arFields = array();
    $arFields['PASSWORD_2'] = $arHandlerFields['PASSWORD_2'];
    $arFields['ORDER_SUMM'] = $_REQUEST["OutSum"];
    $arFields['ORDER_ID'] = $_REQUEST["InvId"];
    $arFields['ORDER_ITEMS'] = $_REQUEST["Shp_item"];

    $iOrderID = $arFields['ORDER_ID'];

    $CRC = $_REQUEST["SignatureValue"];
    $CRC = strtoupper($CRC);

    $CRC_CHECK = strtoupper(md5(
        $arFields['ORDER_SUMM'].':'.
        $arFields['ORDER_ID'].':'.
        $arFields['PASSWORD_2'].':'.
        'Shp_item='.$arFields['ORDER_ITEMS']
    ));

    if ($CRC != $CRC_CHECK)
        $iOrderID = false;

    return $iOrderID;
?>