Startshop.Catalog.Offers = function ($oStructure) {
    this.Offers = {};

    this.Offers.Current = null;
    this.Offers.List = $oStructure['OFFERS'];
    this.Offers.Properties = $oStructure['PROPERTIES'];

    this.Events = {};
    this.Events.OnOfferChange = function ($oParameters) {};
    this.Events = $.extend({}, this.Events, $oStructure['EVENTS'] || {});
};

Startshop.Catalog.Offers.prototype.GetParameters = function () {
    var $oParameters = {};
    var $oThat = this;
    $oParameters.Offer = this.GetCurrentOffer();

    $oParameters.Properties = {};
    $oParameters.Properties.Displayed = [];
    $oParameters.Properties.Disabled = [];
    $oParameters.Properties.Enabled = [];
    $oParameters.Properties.Selected = [];

    Startshop.Functions.forEach(this.Offers.List, function ($sOfferCode, $oOffer) {
        var $oOfferTree = $oThat.GetOfferPropertiesTree($oOffer['ID']);
        Startshop.Functions.forEach($oOfferTree, function ($sOfferPropertyCode, $cOfferPropertyValue) {
            var $oProperty = {'Key': $sOfferPropertyCode, 'Value': $cOfferPropertyValue};

            if (!Startshop.Functions.inArray($oProperty, $oParameters.Properties.Displayed))
                $oParameters.Properties.Displayed.push($oProperty);
        });
    });

    var $oCurrentOffer = this.GetCurrentOffer();
    var $oCurrentOfferTree = null;

    if ($oCurrentOffer != null)
        $oCurrentOfferTree = this.GetOfferPropertiesTree($oCurrentOffer['ID']);

    var $arProperties = [];
    var $arPropertiesSelected = [];

    Startshop.Functions.forEach($oThat.Offers.Properties, function ($iKey, $oProperty) {
        $arProperties.push($oProperty['CODE']);
    });

    Startshop.Functions.forEach($arProperties, function ($iPropertyIndex, $sProperty) {
        var $sCurrentProperty = $arProperties[$iPropertyIndex];

        Startshop.Functions.forEach($oThat.Offers.List, function ($iOfferCode, $oOffer) {
            var $bCompared = true;
            var $oOfferTree = $oThat.GetOfferPropertiesTree($oOffer['ID']);

            if ($oCurrentOffer != null) {
                Startshop.Functions.forEach($arPropertiesSelected, function ($iPropertySelectedIndex, $sPropertySelected) {
                    if ($oOfferTree[$sPropertySelected] != $oCurrentOfferTree[$sPropertySelected]) {
                        $bCompared = false;
                        return false;
                    }
                });
            } else if ($arPropertiesSelected.length > 0) {
                $bCompared = false;
            }

            if ($bCompared == true) {
                var $oProperty = {'Key':$sCurrentProperty, 'Value':$oOfferTree[$sCurrentProperty]};

                if (!Startshop.Functions.inArray($oProperty, $oParameters.Properties.Enabled))
                    $oParameters.Properties.Enabled.push($oProperty);
            }
        });

        $arPropertiesSelected.push($sCurrentProperty);
    });

    Startshop.Functions.forEach($oParameters.Properties.Displayed, function ($iPropertyIndex, $oProperty) {
        if (!Startshop.Functions.inArray($oProperty, $oParameters.Properties.Enabled))
            $oParameters.Properties.Disabled.push($oProperty);
    });

    if ($oCurrentOfferTree != null)
        Startshop.Functions.forEach($oCurrentOfferTree, function ($sPropertyCode, $cPropertyValue) {
            $oParameters.Properties.Selected.push({'Key': $sPropertyCode, 'Value': $cPropertyValue});
        });

    return $oParameters;
};

Startshop.Catalog.Offers.prototype.GetOfferByID = function ($iOfferID) {
    if (typeof $iOfferID === "string")
        $iOfferID = parseInt($iOfferID);

    if (isNaN($iOfferID))
        return null;

    if (typeof $iOfferID === 'number') {
        var $oCurrentOffer = this.Offers.List[$iOfferID];

        if ($oCurrentOffer !== undefined)
            return $oCurrentOffer;
    }

    return null;
};

Startshop.Catalog.Offers.prototype.GetOfferByPropertiesTree = function ($oProperties) {
    var $oFoundedOffer = null;
    Startshop.Functions.forEach(this.Offers.List, function ($sOfferCode, $oOffer) {
        var $bEqual = true;
        Startshop.Functions.forEach($oProperties, function($sPropertyCode, $cPropertyValue) {
            var $oOfferProperty = $oOffer['OFFER']['PROPERTIES'][$sPropertyCode];

            if ($cPropertyValue == '')
                $cPropertyValue = null;

            if ($cPropertyValue != null) {
                if ($oOfferProperty !== undefined) {
                    if ($oOfferProperty['VALUE']['CODE'] != $cPropertyValue)
                        $bEqual = false;
                } else {
                    $bEqual = false;
                }
            } else {
                if ($oOfferProperty !== undefined) {
                    $bEqual = false;
                }
            }

            return $bEqual;
        });

        if ($bEqual) {
            $oFoundedOffer = $oOffer;
            return false;
        }
    });

    return $oFoundedOffer;
};

Startshop.Catalog.Offers.prototype.GetCurrentOffer = function () {
    return this.GetOfferByID(this.Offers.Current);
};

Startshop.Catalog.Offers.prototype.SetCurrentOfferByID = function ($iOfferID) {
    if (this.GetOfferByID($iOfferID) != null) {
        this.Offers.Current = $iOfferID;

        if (Startshop.Functions.isFunction(this.Events.OnOfferChange))
            this.Events.OnOfferChange(this.GetParameters());

        return true;
    }

    return false;
};

Startshop.Catalog.Offers.prototype.GetOfferPropertiesTree = function ($iOfferID) {
    var $oTree = {};
    var $oOffer = this.GetOfferByID($iOfferID);

    if ($oOffer == null)
        return $oTree;

    Startshop.Functions.forEach(this.Offers.Properties, function ($iKey, $oProperty) {
        var $oOfferProperty = $oOffer['OFFER']['PROPERTIES'][$oProperty['CODE']];

        if ($oOfferProperty !== undefined) {
            $oTree[$oProperty['CODE']] = $oOfferProperty['VALUE']['CODE'];
        } else {
            $oTree[$oProperty['CODE']] = '';
        }
    });

    return $oTree;
};

Startshop.Catalog.Offers.prototype.SetCurrentOfferByPropertyValue = function ($sProperty, $cValue) {
    var $oCurrentOffer = this.GetCurrentOffer();
    var $oFoundedOffer = null;
    var $oChangeableProperty = null;

    if ($oCurrentOffer == null || typeof $sProperty != "string")
        return false;

    Startshop.Functions.forEach(this.Offers.Properties, function ($iKey, $oProperty) {
        if ($oProperty['CODE'] == $sProperty) {
            $oChangeableProperty = $oProperty;
            return false;
        }
    });

    if ($oChangeableProperty == null)
        return false;

    var $oCompareProperties = {};
    var $oCurrentOfferPropertyTree = this.GetOfferPropertiesTree($oCurrentOffer['ID']);

    Startshop.Functions.forEach($oCurrentOfferPropertyTree, function ($sPropertyCode, $sPropertyValue) {
        $oCompareProperties[$sPropertyCode] = $sPropertyValue;
        if ($sPropertyCode == $sProperty) {
            $oCompareProperties[$sPropertyCode] = $cValue;
        }
    });

    $oFoundedOffer = this.GetOfferByPropertiesTree($oCompareProperties);

    if ($oFoundedOffer != null) {
        this.SetCurrentOfferByID($oFoundedOffer['ID']);
        return true;
    }

    $oCompareProperties = {};

    Startshop.Functions.forEach($oCurrentOfferPropertyTree, function ($sPropertyCode, $sPropertyValue) {
        $oCompareProperties[$sPropertyCode] = $sPropertyValue;
        if ($sPropertyCode == $sProperty) {
            $oCompareProperties[$sPropertyCode] = $cValue;
            return false;
        }
    });

    $oFoundedOffer = this.GetOfferByPropertiesTree($oCompareProperties);

    if ($oFoundedOffer != null) {
        this.SetCurrentOfferByID($oFoundedOffer['ID']);
        return true;
    }
};

Startshop.Catalog.Offers.prototype.Initialize = function () {
    var $sThat = this;
    Startshop.Functions.forEach(this.Offers.List, function ($sOfferCode, $oOffer) {
        $sThat.SetCurrentOfferByID($oOffer['ID']);
        return false;
    });
};
