
; /* Start:/bitrix/templates/marketplace-1c-v3/components/bx/marketplace.catalog.detail/.default/script.js*/
var Opener = {
	btn: null,
	box: null,
	content: null,
	duration: 400,
	init: function (btn, box, content) {
		this.btn = BX(btn);
		this.box = BX(box);
		this.content = BX(content);
		BX.bind(this.btn, 'click', BX.proxy(function () {
			this.btn.style.display = 'none';
			var _this = this;
			(new BX.easing({
				duration: this.duration,
				start: {minHeight: this.box.offsetHeight},
				finish: {minHeight: this.content.offsetHeight},
				transition: BX.easing.makeEaseOut(BX.easing.transitions.quart),
				step: function (state) {
					_this.box.style.minHeight = state.minHeight + 'px';
				}
			})).animate();
		}, this));
	}
}

BX.ready(function(){
	var desc = Object.create(Opener);
	desc.init(BX('description-open-btn'), BX('description-block'), BX('description-block-inner'));
	var news = Object.create(Opener);
	news.init(BX('news-open-btn'), BX('news-block'), BX('news-block-inner'));
	var technicaldata = Object.create(Opener);
	technicaldata.init(BX('technicaldata-btn'), BX('technicaldata-block'), BX('technicaldata-block-inner'));


	var curTab = localStorage.getItem('MPCurTab');
	var tabs = BX('feedback-tabs');
	var tabBtns = BX.findChildren(tabs, {class: 'feedback-title'}, true);
	BX.bindDelegate(tabs, 'click', {class: 'feedback-title'}, function () {
		for (var i = 0; i < tabBtns.length; i++)
		{
			BX.removeClass(tabBtns[i], 'feedback-title-active');
			BX(tabBtns[i].id.replace('-btn', '')+'-block').style.display = 'none';
		}
		BX.addClass(this, 'feedback-title-active');
		BX(this.id.replace('-btn', '')+'-block').style.display = 'block';
		curTab = this.id;
		localStorage.setItem('MPCurTab', curTab);
	});
	if (curTab)
	{
		BX(curTab).click();
	}

	changePrice = function (value)
	{
		var val = value;
		total_bndl_sum.innerHTML = bndl_price['prd'+val];
		if (total_bndl_sum_old)
		{
			total_bndl_sum_old.innerHTML = bndl_price['prd'+val+'old'];
		}
		buy_bndl_btn.href = bndl_url.replace(/&PRD=[0-9]+/, '')+(val != '0' ? '&PRD='+val : '');
	}

	var buy_bndl_btn = BX('buy_bndl_btn');
	if (buy_bndl_btn != null)
	{
		var bndl_url = buy_bndl_btn.href;
	}
	var total_bndl_sum = BX('total_bndl_sum');
	var total_bndl_sum_old = BX('total_bndl_sum_old');
	BX.bind(BX('bundles'), 'change', function(){
		changePrice(this.value);
	})
});
var changePrice;

function Answer(id, name)
{
	BX('discuss-form-btn').click();
	BX('module_COMMENT').value = name+', ';
	BX('id_com').value = id;
}





var enterUrlWindow = {
    form_window: null,
    form_window_id : "enter-site-url",
    login_field_id : "enterURL",

    ShowLoginForm : function()
    {
        if (!this.form_window)
        {
            this.form_window = document.getElementById(this.form_window_id);
            if (!this.form_window)
                return false;

            try {document.body.appendChild(this.form_window);}
            catch (e){}
        }

        authFormWindow.CreateOverlay();
        if(authFormWindow.overlay)
            authFormWindow.overlay.onclick = function() {enterUrlWindow.CloseLoginForm()};
        this.form_window.style.display = "block";

        var res = jsUtils.GetWindowSize();
        this.form_window.style.top = parseInt(res['scrollTop'] + this.form_window.offsetHeight) + 'px';

        var loginField = document.getElementById(this.login_field_id);
        if (loginField)
        {
            loginField.focus();
            loginField.select();
        }

        return false;
    },

    CloseLoginForm : function()
    {
        authFormWindow.CloseLoginForm();

        if (this.form_window)
            this.form_window.style.display = "none";
        return false;
    },

    GetWindowScrollSize : function(pDoc)
    {
        var width, height;
        if (!pDoc)
            pDoc = document;

        if ( (pDoc.compatMode && pDoc.compatMode == "CSS1Compat"))
        {
            width = pDoc.documentElement.scrollWidth;
            height = pDoc.documentElement.scrollHeight;
        }
        else
        {
            if (pDoc.body.scrollHeight > pDoc.body.offsetHeight)
                height = pDoc.body.scrollHeight;
            else
                height = pDoc.body.offsetHeight;

            if (pDoc.body.scrollWidth > pDoc.body.offsetWidth ||
                (pDoc.compatMode && pDoc.compatMode == "BackCompat") ||
                (pDoc.documentElement && !pDoc.documentElement.clientWidth)
                )
                width = pDoc.body.scrollWidth;
            else
                width = pDoc.body.offsetWidth;
        }
        return {scrollWidth : width, scrollHeight : height};
    }
}

var enterURL1 = '';
function AddModuleEx()
{
    enterU = document.getElementById('enterURL').value;
    var module = document.getElementById('module').value;
    if(enterU)
    {
        var enterURL1 = enterU + "/bitrix/admin/update_system_partner.php?addmodule=#MODULE#";
        enterURL1 = enterURL1.replace("#MODULE#", module);
        window.open(enterURL1);
        enterUrlWindow.CloseLoginForm();
    }
}

function expand (obj)
{
    BX.findParent(BX.findParent(obj)).style.maxHeight = 'none';
    obj.style.display = 'none';
}

var imagesSlider = function(code) {
    this.slide_move_left = function (e) { // right click
        stopSliding = true;
        BX.PreventDefault(e);
        if (counter == (slides.length-3))
        {
            return false;
        }
        else
        {
            counter = counter + 1;
        }
        if (!sliding && counter < slides.length)
        {
            return this.view_slide(counter);
        }
        else
        {
            counter = counter - 1;
            return false;
        }
    }

    this.slide_move_right = function (e) { // left click
        stopSliding = true;
        BX.PreventDefault(e);
        if (counter == 0)
        {
            return false;
        }
        else
        {
            counter = counter - 1;
        }
        if (!sliding && counter >= 0)
        {
            this.view_slide(counter)
        }
        else
        {
            counter = counter + 1;
            return false;
        }
    }

    this.view_slide = function (slide) {
        var startPos = parseFloat(container.style.left.replace('%', ''), 10);
        var endPos = -1*slide*offset;
        if (sliding)
            return false;
        sliding = true;
        if (startPos <= minPos && startPos >= maxPos)
        {
            nextPos = endPos;
            (new BX.easing({
                duration : 400,
                start:{left: startPos},
                finish:{left: endPos},
                transition : BX.easing.makeEaseOut(BX.easing.transitions.linear),
                step : function(state){
                    container.style.left = state.left+'%';
                },
                complete: function () {
                    sliding = false;
                    container.style.left = -1*counter*offset+'%';
                }
            })).animate();
            return true;
        }
        return false;
    }

    this.pageClick = function (i, e) {
        return function(e){
            BX.PreventDefault(e);
            if (!sliding)
            {
                counter = i;
                this.view_slide(i);
                stopSliding = true;
            }
        };
    }

    var counter = 0; // current slide
    var offset = 33.33333;
    var container = BX('imagesSlider_'+code);
    var slides = BX.findChildren(container, {tagName: 'LI'});
    var minPos = offset;
    var maxPos = -1*(slides.length+1)*offset;
    var stopSliding = false;
    var nextPos = 0;
    var sliding = false;

    BX.bind(BX('imagesSliderLeft_'+code), 'click', BX.delegate(this.slide_move_right, this));
    BX.bind(BX('imagesSliderRight_'+code), 'click', BX.delegate(this.slide_move_left, this));
}
/* End */
;
; /* Start:/bitrix/templates/marketplace-1c-v3/components/bx/marketplace.catalog.list/other_apps/script.js*/
var otherAppsSlider = function (box_id, leftBtn, rightBtn) {
	this.box = BX(box_id);
	this.container = BX.findChild(this.box, {class: 'others-apps-slider-ovh'}, true);
	this.slides = BX.findChildren(this.box, {class: 'others-apps-item-wrap'}, true);
	this.counter = 0;
	this.nextSlide = null;
	this.sliding = false;

	this.offset = 25;
	this.maxSlide = this.slides.length-4;

	this._easing =  function () {
		var _this = this;
		this.sliding = true;
		console.log(-1*this.offset*this.counter, -1*this.offset*this.nextSlide);
		var easing = new BX.easing({
			duration : 300,
			start : {
				left : -1*this.offset*this.counter
			},
			finish : {
				left : -1*this.offset*this.nextSlide
			},
			transition : BX.easing.makeEaseOut(BX.easing.transitions.circ),
			step : function(state){
				_this.container.style.left = state.left + '%'
			},
			complete: function () {
				_this.sliding = false;
				_this.counter = _this.nextSlide;
			}
		})
		easing.animate();
	};

	this.rightClick = function () {
		if (this.sliding)
		{
			return false;
		}

		this.nextSlide = this.counter + 1;
		if (this.nextSlide <= this.maxSlide)
		{
			this._easing();
		}
		else
		{
			this.nextSlide = this.counter;
		}
	}

	this.leftClick = function () {
		if (this.sliding)
		{
			return false;
		}

		this.nextSlide = this.counter - 1;
		if (this.nextSlide >= 0)
		{
			this._easing();
		}
		else
		{
			this.nextSlide = this.counter;
		}
	}

	BX.bind(BX(leftBtn), 'click', BX.proxy(this.leftClick, this));
	BX.bind(BX(rightBtn), 'click', BX.proxy(this.rightClick, this));
}
BX.ready(function(){
	var oas = new otherAppsSlider('other_apps', 'other-apps-left-arrow', 'other-apps-right-arrow');
});
/* End */
;; /* /bitrix/templates/marketplace-1c-v3/components/bx/marketplace.catalog.detail/.default/script.js*/
; /* /bitrix/templates/marketplace-1c-v3/components/bx/marketplace.catalog.list/other_apps/script.js*/
