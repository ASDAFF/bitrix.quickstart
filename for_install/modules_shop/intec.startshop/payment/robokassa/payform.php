<?
    // Доступные переменные
    // $arPayment
    // $arHandler
    // $arHandlerParameters
    // $arHandlerFields

    $arFormParameters = array(
        'SHOP' => $arHandlerFields['SHOP'],
        'PASSWORD_1' => $arHandlerFields['PASSWORD_1'],
        'ORDER_ID' => $arHandlerParameters['ORDER_ID'],
        'ORDER_DESCRIPTION' => $arHandlerParameters['ORDER_DESCRIPTION'],
        'ORDER_SUM' => $arHandlerParameters['ORDER_SUM'],
        'ORDER_ITEMS' => implode('_', $arHandlerParameters['ORDER_ITEMS']),
        'ORDER_CURRENCY' => $arHandlerParameters['ORDER_CURRENCY'],
        'CULTURE' => $arHandlerParameters['CULTURE'],
        'IS_TEST' => $arHandlerFields['IS_TEST'],
    );

    $CRC  = md5(
        $arFormParameters['SHOP'].':'.
        $arFormParameters['ORDER_SUM'].':'.
        $arFormParameters['ORDER_ID'].':'.
        $arFormParameters['PASSWORD_1'].':'.
        'Shp_item='.$arFormParameters['ORDER_ITEMS']
    );
?>
<html>
    <form action='https://merchant.roboxchange.com/Index.aspx' method=POST>
        <input type=hidden name=MrchLogin value="<?=htmlspecialcharsbx($arFormParameters['SHOP'])?>">
        <input type=hidden name=OutSum value="<?=htmlspecialcharsbx($arFormParameters['ORDER_SUM'])?>">
        <input type=hidden name=InvId value="<?=htmlspecialcharsbx($arFormParameters['ORDER_ID'])?>">
        <input type=hidden name=Desc value="<?=htmlspecialcharsbx($arFormParameters['ORDER_DESCRIPTION'])?>">
        <input type=hidden name=SignatureValue value="<?=$CRC?>">
        <input type=hidden name=Shp_item value="<?=htmlspecialcharsbx($arFormParameters['ORDER_ITEMS'])?>">
        <input type=hidden name=IncCurrLabel value="<?=htmlspecialcharsbx($arFormParameters['ORDER_CURRENCY'])?>">
        <input type=hidden name=Culture value="<?=htmlspecialcharsbx($arFormParameters['CULTURE'])?>">
        <?if ($arFormParameters['IS_TEST'] == "Y"):?>
            <input type=hidden name=IsTest value=1>
        <?endif;?>
        <input class="<?=$arHandlerParameters['BUTTON_CLASS']?>" type=submit value="<?=htmlspecialcharsbx($arHandlerParameters['BUTTON_NAME'])?>">
    </form>
</html>