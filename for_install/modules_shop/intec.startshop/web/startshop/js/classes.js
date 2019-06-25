Startshop.Classes.Events = function () {
    this.Events = {};
};

Startshop.Classes.Events.prototype.On = function ($sEvent, $fCallback) {
    if (Startshop.Functions.isFunction($fCallback)) {
        if (this.Events[$sEvent] === undefined)
            this.Events[$sEvent] = [];

        this.Events[$sEvent].push($fCallback);
    }
};

Startshop.Classes.Events.prototype.Call = function ($sEvent, $oParameters) {
    if (this.Events[$sEvent] !== undefined) {
        Startshop.Functions.forEach(this.Events[$sEvent], function ($sEvent, $fCallback) {
            if (Startshop.Functions.isFunction($fCallback))
                $fCallback($oParameters);
        });
    }
};

Startshop.Classes.Events.prototype.Off = function ($sEvent) {
    this.Events[$sEvent] = undefined;
};