Startshop.Basket.Add = function($iItemID, $fQuantity, $sSiteID, $fCallback) {
    $.post(
        Startshop.Constants.MODULE_DIR + '/web/startshop/ajax/basket.php',
        {
            'Action': 'Add',
            'Item': $iItemID,
            'Quantity': $fQuantity,
            'SiteID': $sSiteID
        },
        function ($sResponse) {
            if (Startshop.Functions.isFunction($fCallback))
                $fCallback($sResponse);
        }
    );
};

Startshop.Basket.Delete = function($iItemID, $sSiteID, $fCallback) {
    $.post(
        Startshop.Constants.MODULE_DIR + '/web/startshop/ajax/basket.php',
        {
            'Action': 'Delete',
            'Item': $iItemID,
            'SiteID': $sSiteID
        },
        function ($sResponse) {
            if (Startshop.Functions.isFunction($fCallback))
                $fCallback($sResponse);
        }
    );
};

Startshop.Basket.Update = function($sSiteID, $fCallback) {
    $.post(
        Startshop.Constants.MODULE_DIR + '/web/startshop/ajax/basket.php',
        {
            'Action': 'Update',
            'SiteID': $sSiteID
        },
        function ($sResponse) {
            if (Startshop.Functions.isFunction($fCallback))
                $fCallback($sResponse);
        }
    );
};

Startshop.Basket.SetQuantity = function($iItemID, $fQuantity, $sSiteID, $fCallback) {
    $.post(
        Startshop.Constants.MODULE_DIR + '/web/startshop/ajax/basket.php',
        {
            'Action': 'SetQuantity',
            'Quantity': $fQuantity,
            'SiteID': $sSiteID
        },
        function ($sResponse) {
            if (Startshop.Functions.isFunction($fCallback))
                $fCallback($sResponse);
        }
    );
};