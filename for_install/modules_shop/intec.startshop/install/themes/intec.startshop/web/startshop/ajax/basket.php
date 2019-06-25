<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
    if (!CModule::IncludeModule('intec.startshop'))
        return false;

    $arActions = array(
        'Add',
        'Delete',
        'Update',
        'SetQuantity'
    );

    $sAction = $_POST['Action'];
    $sSiteID = !empty($_POST['SiteID']) ? $_POST['SiteID'] : SITE_ID;

    if (!in_array($sAction, $arActions))
        return false;

    echo CStartShopBasket::HandleRequestActions($sAction, $_POST['Item'], $_POST['Quantity'], $sSiteID);

    /*if ($sAction == 'Update')
        CStartShopBasket::Delete($_POST['Item'], $sSiteID);*/
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
