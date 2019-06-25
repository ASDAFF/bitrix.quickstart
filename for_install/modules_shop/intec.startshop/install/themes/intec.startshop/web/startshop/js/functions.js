Startshop.Functions.isFunction = function ($oFunction) {
    return Object.prototype.toString.call($oFunction) === '[object Function]';
};

Startshop.Functions.isArray = function ($oArray) {
    return Object.prototype.toString.call($oArray) === '[object Array]';
};

Startshop.Functions.isObject = function ($oObject) {
    return Object.prototype.toString.call($oObject) === '[object Object]';
};

Startshop.Functions.isEqual = function ($oFirstObject, $oSecondObject) {
    if (Startshop.Functions.isObject($oFirstObject) && Startshop.Functions.isObject($oSecondObject)) {
        var $arFirstObjectKeys = Object.keys($oFirstObject);
        var $arSecondObjectKeys = Object.keys($oSecondObject);

        if ($arFirstObjectKeys.length != $arSecondObjectKeys.length ) return false;

        return !$arFirstObjectKeys.filter(function($sKey){
            if (Startshop.Functions.isObject($oFirstObject[$sKey]) ||  Startshop.Functions.isArray($oFirstObject[$sKey])) {
                return !Startshop.Functions.isEqual($oFirstObject[$sKey], $oSecondObject[$sKey]);
            } else {
                return $oFirstObject[$sKey] !== $oSecondObject[$sKey];
            }
        }).length;
    }

    return false;
};

Startshop.Functions.forEach = function ($oObject, $fCallback) {
    if (Startshop.Functions.isFunction($fCallback)) {
        if (Startshop.Functions.isArray($oObject)) {
            var $iArrayIndex = 0;
            var $iArrayLength = $oObject.length;

            for ($iArrayIndex = 0; $iArrayIndex < $iArrayLength; $iArrayIndex++) {
                var $oArrayEntry = $oObject[$iArrayIndex];

                if ($fCallback($iArrayIndex, $oArrayEntry) == false)
                    break;
            }
        } else if (Startshop.Functions.isObject($oObject)) {
            for (var $sObjectIndex in $oObject) {
                var $oObjectEntry = $oObject[$sObjectIndex];

                if ($fCallback($sObjectIndex, $oObjectEntry) == false)
                    break;
            }
        }
    }
};

Startshop.Functions.inArray = function ($oNeedle, $oHaystack, $bStrict) {
    var $bFounded = false; $bStrict = !!$bStrict;

    Startshop.Functions.forEach($oHaystack, function($oKey, $oValue) {
        if (Startshop.Functions.isObject($oNeedle) || Startshop.Functions.isArray($oNeedle)) {
            if (Startshop.Functions.isEqual($oNeedle, $oValue)) {
                $bFounded = true;
                return false;
            }
        } else {
            if ($bStrict)
                if ($oValue === $oNeedle) {
                    $bFounded = true;
                    return false;
                }

            if (!$bStrict)
                if ($oValue == $oNeedle) {
                    $bFounded = true;
                    return false;
                }
        }

    });

    return $bFounded;
};

Startshop.Functions.stringReplace = function ($oReplaceable, $sString) {
    if (!Startshop.Functions.isObject($oReplaceable))
        return $sString;

    Startshop.Functions.forEach($oReplaceable, function ($sKey, $sValue) {
        $sString = $sString.replace(new RegExp(RegExp.escape($sKey),'g'), $sValue);
    });

    return $sString;
};

Startshop.Functions.numberFormat = function ( number, decimals, dec_point, thousands_sep ) {
    var i, j, kw, kd, km;

    if( isNaN(decimals = Math.abs(decimals)) ){
        decimals = 2;
    }
    if( dec_point == undefined ){
        dec_point = ",";
    }
    if( thousands_sep == undefined ){
        thousands_sep = ".";
    }

    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

    if( (j = i.length) > 3 ){
        j = j % 3;
    } else{
        j = 0;
    }

    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

    return km + kw + kd;
};

Startshop.Functions.GetElementSize = function ($oElements) {
    var $oSize = {};

    $oSize.Width = 0;
    $oSize.Height = 0;

    if ($oElements[0] !== undefined)
        if (this.isFunction($oElements[0].getBoundingClientRect)){
            var $oRectangle = $oElements[0].getBoundingClientRect();
            $oSize.Width = parseFloat($oRectangle.right - $oRectangle.left);
            $oSize.Height = parseFloat($oRectangle.bottom - $oRectangle.top);
        }

    if ($oSize.Width == 0 && $oSize.Height == 0)
        if ($oElements.css('box-sizing') == 'border-box') {
            $oSize.Width = parseFloat($oElements.outerWidth(false));
            $oSize.Height = parseFloat($oElements.outerHeight(false));
        } else {
            $oSize.Width = parseFloat($oElements.width());
            $oSize.Height = parseFloat($oElements.height());
        }

    return $oSize;
}

RegExp.escape = function ($sString) {
    return $sString.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
};