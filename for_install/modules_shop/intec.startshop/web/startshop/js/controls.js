/* Numeric UpDown */
Startshop.Controls.NumericUpDown = function ($oSettings) {
    var $oDefaults = {
        'Value': 1,
        'Minimum': 1,
        'Maximum': 1,
        'Ratio': 1,
        'Unlimited': false,
        'Events': {
            'OnValueChange': function () {}
        },
        'ValueType': 'Integer'
    };

    this.Settings = $.extend({}, $oDefaults, $oSettings || {});
};

Startshop.Controls.NumericUpDown.prototype.HandleValue = function ($nValue) {
    if (this.Settings.ValueType == 'Float') {
        return parseFloat(parseFloat($nValue).toFixed(2));
    } else {
        return parseInt($nValue);
    }
};

Startshop.Controls.NumericUpDown.prototype.Increase = function () {
    return this.SetValue(this.HandleValue(this.Settings.Value) + this.HandleValue(this.Settings.Ratio));
};

Startshop.Controls.NumericUpDown.prototype.Decrease = function () {
    return this.SetValue(this.HandleValue(this.Settings.Value) - this.HandleValue(this.Settings.Ratio));
};

Startshop.Controls.NumericUpDown.prototype.GetValue = function () {
    if (parseInt(this.Settings.Value * 100) % 100 == 0) {
        return this.HandleValue(this.Settings.Value).toFixed(0);
    } else {
        if (parseInt(this.Settings.Value * 100) % 10 == 0) {
            return this.HandleValue(this.Settings.Value).toFixed(1);
        } else {
            return this.HandleValue(this.Settings.Value).toFixed(2);
        }
    }
};

Startshop.Controls.NumericUpDown.prototype.SetValue = function ($nValue) {
    $nValue = this.HandleValue($nValue);
    var $nNewValue = this.Settings.Value;

    if (this.HandleValue(this.Settings.Minimum) >= this.HandleValue(this.Settings.Maximum) && this.Settings.Unlimited == false) {
        $nNewValue = this.HandleValue(this.Settings.Maximum);
    } else if (isNaN($nValue) || $nValue <= this.HandleValue(this.Settings.Minimum)) {
        $nNewValue = this.HandleValue(this.Settings.Minimum);
    } else if ($nValue >= this.HandleValue(this.Settings.Maximum) && this.Settings.Unlimited == false) {
        $nNewValue = this.HandleValue(this.Settings.Maximum);
    } else {
        if ((Math.round($nValue * 100) % Math.round(this.Settings.Ratio * 100)) == 0) {

            $nNewValue = $nValue;
        } else {
            $nNewValue = this.HandleValue($nValue - ($nValue % this.Settings.Ratio));
        }
    }

    this.Settings.Value = $nNewValue;

    if (Startshop.Functions.isFunction(this.Settings.Events.OnValueChange))
        this.Settings.Events.OnValueChange(this);
};

/* Slider */
Startshop.Controls.Slider = function ($oSettings) {
    var $oThat = this;
    var $oDefaults = {
        "Container": ".slider",
        "Element": ".slide",
        "Count": 4,
        "Animation": {
            "Enabled": true,
            "Speed": 400
        },
        "Custom": null
    };
    this.Settings = $.extend({}, $oDefaults, $oSettings || {});
    this.Events = new Startshop.Classes.Events();

    this.Current = 1;

    $(window).resize(function () {
        $oThat.Refresh();
    });
};

Startshop.Controls.Slider.prototype.__SlideTo = function ($iSlideNumber) {
    var $oSettings = {};
    var $oThat = this;

    $oSettings.Slider = this;
    $oSettings.Container = this.GetContainer();
    $oSettings.Elements = this.GetElements();
    $oSettings.Element = {};
    $oSettings.Element.Current = {};
    $oSettings.Element.Current.Number = this.Current;
    $oSettings.Element.Next = {};
    $oSettings.Element.Next.Number = $iSlideNumber;
    $oSettings.Boundaries = {};
    $oSettings.Boundaries.Minimum = 1;
    $oSettings.Boundaries.Maximum = this.GetMaximumElementNumber();
    $oSettings.Animation = {};
    $oSettings.Animation.Enabled = this.Settings.Animation.Enabled;
    $oSettings.Animation.Speed = this.Settings.Animation.Speed;
    $oSettings.Slided = true;

    this.Events.Call("BeforeSlide", $oSettings);

    if ($oSettings.Element.Next.Number > $oSettings.Boundaries.Maximum)
        $oSettings.Element.Next.Number = $oSettings.Boundaries.Maximum;

    if ($oSettings.Element.Next.Number < $oSettings.Boundaries.Minimum)
        $oSettings.Element.Next.Number = $oSettings.Boundaries.Minimum;

    $oSettings.Element.Next.Object = this.GetElement($oSettings.Element.Next.Number);
    $oSettings.Element.Current.Object = this.GetElement($oSettings.Element.Current.Number);

    if (Startshop.Functions.isFunction(this.Settings.Custom)) {
        this.Settings.Custom($oSettings);
    } else {
        var $oSize = Startshop.Functions.GetElementSize($oSettings.Elements);

        if ($oSettings.Animation.Enabled) {
            $oSettings.Container.stop().animate({'scrollLeft': $oSize.Width * ($oSettings.Element.Next.Number - 1)}, $oSettings.Animation.Speed, function () {
                $oThat.Events.Call("AfterSlide", $oSettings);
            });
        } else {
            $oSettings.Container.stop().scrollLeft($oSize.Width * ($oSettings.Element.Next.Number - 1));
            this.Events.Call("AfterSlide", $oSettings);
        }
    }


    this.Current = $oSettings.Element.Next.Number;
};

Startshop.Controls.Slider.prototype.SlideTo = function ($iSlideNumber) {
    this.__SlideTo($iSlideNumber);
};

Startshop.Controls.Slider.prototype.SlideNext = function () {
    this.__SlideTo(this.GetCurrentElementNumber() + 1);
};

Startshop.Controls.Slider.prototype.SlidePrev = function () {
    this.__SlideTo(this.GetCurrentElementNumber() - 1);
};

Startshop.Controls.Slider.prototype.GetCurrentElementNumber = function () {
    return this.Current;
};

Startshop.Controls.Slider.prototype.GetCurrentElement = function () {
    return $(this.GetElement(this.GetCurrentElementNumber()));
};

Startshop.Controls.Slider.prototype.GetContainer = function () {
    return $(this.Settings.Container);
};

Startshop.Controls.Slider.prototype.GetElements = function () {
    return this.GetContainer().find(this.Settings.Element);
};

Startshop.Controls.Slider.prototype.GetElement = function ($iSlideNumber) {
    return this.GetElements().eq($iSlideNumber - 1);
};

Startshop.Controls.Slider.prototype.GetMaximumElementNumber = function () {
    var $iMaximumElementNumber = (this.GetElements().size() - this.Settings.Count + 1);

    if ($iMaximumElementNumber < 1)
        $iMaximumElementNumber = 1;

    return $iMaximumElementNumber;
};

Startshop.Controls.Slider.prototype.Refresh = function () {
    var $oAnimationState = this.Settings.Animation.Enabled;
    this.Settings.Animation.Enabled = false;
    this.Events.Call("BeforeAdaptability", this);
    this.SlideTo(this.GetCurrentElementNumber());
    this.Events.Call("AfterAdaptability", this);
    this.Settings.Animation.Enabled = $oAnimationState;
};

Startshop.Controls.Slider.prototype.Initialize = function () {
    this.Refresh();
    this.SlideTo(1);
}
