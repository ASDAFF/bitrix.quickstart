<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
    global $APPLICATION;
	global $options;

    $bBasketActionComplete = (bool)CStartShopBasket::HandleRequestActions(
        $_REQUEST['CatalogBasketAction'],
        $_REQUEST['CatalogBasketItem'],
        $_REQUEST['CatalogBasketQuantity'],
        $_REQUEST['CatalogBasketPrice'],
        SITE_ID,
        array(
            'Add' => 'Add',
            'Delete' => 'Delete',
            'SetQuantity' => 'SetQuantity'
        )
    );

    if ($bBasketActionComplete) {
        LocalRedirect($APPLICATION->GetCurPageParam('', array('CatalogBasketAction', 'CatalogBasketItem', 'CatalogBasketQuantity')));
        die();
    }
?>