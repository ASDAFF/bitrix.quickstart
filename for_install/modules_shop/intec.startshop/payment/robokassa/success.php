<?
    // Доступные переменные
    // $arPayment
    // $arHandler
    // $arHandlerFields

    $arFields = array();
    $arFields['PASSWORD_1'] = $arHandlerFields['PASSWORD_1'];
    $arFields['ORDER_SUMM'] = $_REQUEST["OutSum"];
    $arFields['ORDER_ID'] = $_REQUEST["InvId"];
    $arFields['ORDER_ITEMS'] = $_REQUEST["Shp_item"];

    $iOrderID = $arFields['ORDER_ID'];

    $CRC = $_REQUEST["SignatureValue"];
    $CRC = strtoupper($CRC);

    $CRC_CHECK = strtoupper(md5(
        $arFields['ORDER_SUMM'].':'.
        $arFields['ORDER_ID'].':'.
        $arFields['PASSWORD_1'].':'.
        'Shp_item='.$arFields['ORDER_ITEMS']
    ));

    if (!(!empty($arFields['PASSWORD_1']) && !empty($arFields['ORDER_SUMM']) && !empty($arFields['ORDER_ID']) && !empty($arFields['ORDER_ITEMS'])))
        $iOrderID = false;

    if ($CRC != $CRC_CHECK)
        $iOrderID = false;

    return $iOrderID
?>


