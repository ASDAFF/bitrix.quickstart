/***********************************************************************/
/******************************* custom ********************************/
/***********************************************************************/
var RSGoPro_tamautID = 0,
	RSGoPro_timeoutDelay = 1200,
	RSGoPro_inputter,
	RSGoPro_modef_delay_hide = 4000,
	RSGoPro_modef_posFix = 0;
var RSGoPro_filtren,
	RSGoPro_offsetTopFilter = 0,
	RSGoPro_offsetTopFilterH = 0,
	RSGoPro_content,
	RSGoPro_offsetTopContent = 0,
	RSGoPro_offsetTopContentH = 0;

function RSGoPro_priceGoupClick() {
	if( $('.filtren').hasClass('ajaxfilter') ) {
		RSGoPro_FilterAjax();
	}
}

function RSGoPro_SeachProp($inputObj) {
	var value = $inputObj.val();
	var $lvl1 = $inputObj.parents('.lvl1');
	
	if(value.length<1) {
		$lvl1.find('.lvl2').css('display','block');
	} else {
		$lvl1.find('.lvl2').each(function(){
			var p_value = $(this).find('label').html().substr(0,value.length);
			if( value.toLowerCase()==p_value.toLowerCase() ) {
				$(this).css('display','block');
			} else {
				$(this).css('display','none');
			}
		});
	}
	
	// reinitialize jScrollPane
	if($inputObj.parents('.lvl1').hasClass('scrolable')) {
		RSGoPro_FilterJScrollPaneReinitialize();
	}
}

function RSGoPro_FilterSetPropHide() {
	// main
	if( $.cookie(BX_COOKIE_PREFIX+'RSGOPRO_SMARTFILTER_SHOW_ALL')=='Y' ) {
		$('.filtren').addClass('opened');
	}
	// props
	var propcode = '';
	$('.filtren').find('li.lvl1').removeClass('closed');
	$('.filtren').find('li.lvl1').each(function(i){
		propcode = $(this).data('propcode');
		if( $.cookie(BX_COOKIE_PREFIX+'RSGOPRO_SMARTFILTER_HIDE_'+propcode)=='Y' )
		{
			$(this).addClass('closed');
		}
	});
}

function RSGoPro_FilterJScrollPaneReinitialize() {
	var pane2api;
	$('.f_jscrollpane').each(function(i){
		pane2api = $(this).data('jsp');
		pane2api.reinitialise();
	});
}

function RSGoPro_BeforeSend(action) {
	if(action=='disable') {
		$('.filtren').find('.min, .max').each(function(i) {
			if( parseFloat($(this).data('startvalue')) == parseFloat($(this).val().replace(/[,]/g, '.').replace(/[ ]+/g, '')) ) {
				$(this).attr('disabled','disabled');
			}
		});
	} else {
		$('.filtren').find('.min, .max').removeAttr('disabled');
	}
}
function RSGoPro_ReplaceProbel() {
	$('.filtren').find('.min, .max').each(function(i){
		$(this).val( $(this).val().replace(/[ ]+/g, '') );
	});
}
function RSGoPro_ReturnProbel() {
	$('.filtren').find('.min, .max').each(function(i){
		$(this).val( RSDevFunc_NumberFormat( $(this).val() ) );
	});
}

function RSGoPro_FixedFilterWinScroll() {
	if( RSGoPro_filtren && RSGoPro_filtren.length>0 ) {
		RSGoPro_offsetTopFilterH = RSGoPro_offsetTopFilter + RSGoPro_filtren.outerHeight(true);
		RSGoPro_offsetTopContentH = RSGoPro_content.offset().top + RSGoPro_content.outerHeight(true)
		if( window.pageYOffset > RSGoPro_offsetTopFilter ) {
			RSGoPro_filtren.addClass('fixed');
		} else {
			RSGoPro_filtren.removeClass('fixed');
		}
		if( window.pageYOffset + RSGoPro_filtren.outerHeight(true) > RSGoPro_content.offset().top + RSGoPro_content.outerHeight(true) ) {
			RSGoPro_filtren.addClass('stop').css('top',(RSGoPro_offsetTopContentH - RSGoPro_offsetTopFilterH)+'px');
		} else {
			RSGoPro_filtren.removeClass('stop').css('top','0px');
		}
	}
}
function RSGoPro_FixedFilter() {
	if( RSGoPro_filtren && RSGoPro_filtren.length>0 ) 	{
		RSGoPro_offsetTopFilter = RSGoPro_filtren.offset().top;
		RSGoPro_offsetTopFilterH = RSGoPro_offsetTopFilter + RSGoPro_filtren.outerHeight(true);
		RSGoPro_offsetTopContent = RSGoPro_content.offset().top;
		RSGoPro_offsetTopContentH = RSGoPro_offsetTopContent + RSGoPro_content.outerHeight(true);
		window.onscroll = RSGoPro_FixedFilterWinScroll;
	}
}

function RSGoPro_FilterAjax() {
	clearTimeout(RSGoPro_tamautID);
	RSGoPro_tamautID = setTimeout(function(){
		RSGoPro_Area2Darken( $('#catalog'), 'animashka' );
		RSGoPro_ReplaceProbel();
		RSGoPro_BeforeSend('disable');
		var $formObj = $('form.smartfilter');
		var seriData = $formObj.serialize(),
			url = $formObj.attr('action');
		if(url.indexOf("?")<1) 		{
			url = url + '?' + seriData + '&AJAX_CALL=Y&get=catalog&set_filter=Y';
		} else {
			url = url + '&' + seriData + '&AJAX_CALL=Y&get=catalog&set_filter=Y';
		}
		BX.ajax({
			url				: url,
			method			: 'GET',
			dataType		: 'html',
			scriptsRunFirst	: false,
			emulateOnload	: false,
			start			: true,
			cache			: false,
			onsuccess: function(data){
				$('#catalog').html( data );
				RSGoPro_ScrollInit('.prices_jscrollpane');
				RSGoPro_Area2Darken( $('#catalog') );
				RSGoPro_FilterOnDocumentReady();
			},
			onfailure: function(){
				RSGoPro_Area2Darken( $('#catalog') );
				RSGoPro_FilterOnDocumentReady();
				console.warn( 'FILTER -> ajax load failed' );
			}
		});
	},RSGoPro_timeoutDelay);
}

function RSGoPro_FilterOnDocumentReady() {
	RSGoPro_FilterSetPropHide();
	
	// shiw/hide filter
	$(document).on('click','.filtren .title a.shhi',function(){
		if($('.filtren').hasClass('opened')) { // was opened
			$.removeCookie(BX_COOKIE_PREFIX+'RSGOPRO_SMARTFILTER_SHOW_ALL');
			$('.filtren').removeClass('opened');
		} else { // was closed
			$.cookie(BX_COOKIE_PREFIX+'RSGOPRO_SMARTFILTER_SHOW_ALL','Y','/');
			$('.filtren').addClass('opened');
		}
		RSGoPro_FilterJScrollPaneReinitialize();
		RSGoPro_FixedFilterWinScroll();
		return false;
	});
	
	// shiw/hide property
	$(document).on('click','.filtren .showchild',function(){
		var $li = $(this).parents('li.lvl1');
		var propcode = $li.data('propcode');
		if($li.hasClass('closed')) { // was closed
			$.removeCookie(BX_COOKIE_PREFIX+'RSGOPRO_SMARTFILTER_HIDE_'+propcode);
			$li.removeClass('closed');
		} else { // was opened
			$.cookie(BX_COOKIE_PREFIX+'RSGOPRO_SMARTFILTER_HIDE_'+propcode,'Y','/');
			$li.addClass('closed');
		}
		RSGoPro_FilterJScrollPaneReinitialize();
		RSGoPro_FixedFilterWinScroll();
		return false;
	});
	
	// disable click on disabled property
	$(document).on('click', '.lvl2_disabled input, .lvl2_disabled label', function(e){
		e.stopPropagation();
		return false;
	});
	
	// format number in inputs
	setTimeout(function(){
		RSGoPro_ReplaceProbel();
		var timeoutId;
		$('.filtren').find('.min, .max').on('keyup',function(e){
			clearTimeout(timeoutId);
			if( e.which!=8 && e.which!=37 && e.which!=39 && e.which!=191 && e.which!=190 && e.which!=188 ) 		{
				var $input = $(this);
				timeoutId = setTimeout(function(){
					$(this).val( RSDevFunc_NumberFormat( $input.val() ) );
					smartFilter.keyup( BX( $input.attr('id') ) );
				},1500);
			}
		}).each(function(){
			$(this).val( RSDevFunc_NumberFormat($(this).val()) );
		});
	},25); // fix for slow browsers
	
	// jScrollPane
	$('.f_jscrollpane').jScrollPane();
	RSGoPro_FilterJScrollPaneReinitialize();
	$(window).resize(function(){
		RSGoPro_FilterJScrollPaneReinitialize();
	});
	
	// search
	$(document).on('keyup', '.f_search', function(){
		var $inputObj = $(this);
		RSGoPro_SeachProp($inputObj);
	});
	
	// buttons setFilter and resetFilter
	$(document).on('click','.filtren .buttons .set_filter, .filtren .buttons .del_filter',function(){
		RSGoPro_BeforeSend('disable');
		RSGoPro_ReplaceProbel()
		if($(this).hasClass('set_filter')) 		{
			$("#set_filter").click();
		} else {
			$("#del_filter").click();
		}
		return false;
	});
	
	// modef link click
	$(document).on('click','#modef a',function(){
		RSGoPro_BeforeSend('disable');
		RSGoPro_ReplaceProbel();
		$("#set_filter").click();
		return false;
	});
	
	// fixed filter on scrolling
	if(!RSDevFunc_PHONETABLET)
	{
		RSGoPro_filtren = $('.filtren.filterfixed'),
		RSGoPro_offsetTopFilter = 0,
		RSGoPro_offsetTopFilterH = 0,
		RSGoPro_content = $('.content'),
		RSGoPro_offsetTopContent = 0,
		RSGoPro_offsetTopContentH = 0;
		
		RSGoPro_FixedFilter();
	}
}

function RSGoPro_FilterOnSubmitForm() {
	RSGoPro_ReplaceProbel();
	RSGoPro_BeforeSend('disable');
	return true;
}

/* bitrix */
function JCSmartFilter(ajaxURL)
{
	this.ajaxURL = ajaxURL;
	this.form = null;
	this.timer = null;
}

JCSmartFilter.prototype.keyup = function(input)
{
	if( $('.filtren').hasClass('ajaxfilter') )
	{
		RSGoPro_FilterAjax();
	} else {
		if(this.timer)
			clearTimeout(this.timer);
		this.timer = setTimeout(BX.delegate(function(){
			this.reload(input);
		}, this), RSGoPro_timeoutDelay);
	}
}

JCSmartFilter.prototype.click = function(checkbox)
{
	if( $('.filtren').hasClass('ajaxfilter') )
	{
		RSGoPro_FilterAjax();
	} else {
		if( $(checkbox).is(':checked') )
		{
			$('.filtren').find('label[for="'+$(checkbox).attr('id')+'"]').addClass('checked');
		} else {
			$('.filtren').find('label[for="'+$(checkbox).attr('id')+'"]').removeClass('checked');
		}
		if(this.timer)
			clearTimeout(this.timer);
		this.timer = setTimeout(BX.delegate(function(){
			this.reload(checkbox);
		}, this), RSGoPro_timeoutDelay);
	}
}

JCSmartFilter.prototype.reload = function(input)
{
	if(!RSDevFunc_PHONETABLET)
	{
		/* GoPro */
		RSGoPro_inputter = input;
		
		var lvl1 = BX.pos(BX.findParent(input, {'tag':'ul'}), true);
		RSGoPro_Area2Darken( $(input).closest('.filtren'), 'animashka', {'progressTop': lvl1.top + lvl1.height/2});
		
		this.position = BX.pos(input, true);
		this.form = BX.findParent(input, {'tag':'form'});
		if(this.form)
		{
			RSGoPro_ReplaceProbel();
			RSGoPro_BeforeSend('disable');
			var values = new Array;
			values[0] = {name: 'ajax', value: 'y'};
			this.gatherInputsValues(values, BX.findChildren(this.form, {'tag':'input'}, true));
			BX.ajax.loadJSON(
				this.ajaxURL,
				this.values2post(values),
				BX.delegate(this.postHandler, this)
			);
			RSGoPro_ReturnProbel();
			RSGoPro_BeforeSend();
		}
	}
}

JCSmartFilter.prototype.postHandler = function (result)
{
	/* GoPro */
	clearInterval(RSGoPro_tamautID);
	RSGoPro_Area2Darken( $('.filtren') );
	/* /GoPro */
	if(result.ITEMS)
	{
		for(var PID in result.ITEMS)
		{
			var arItem = result.ITEMS[PID];
			if(arItem.PROPERTY_TYPE == 'N' || arItem.PRICE)
			{
			}
			else if(arItem.VALUES)
			{
				for(var i in arItem.VALUES)
				{
					var ar = arItem.VALUES[i];
					var control = BX(ar.CONTROL_ID);
					if(control)
					{
						control.parentNode.className = ar.DISABLED? 'lvl2 lvl2_disabled': 'lvl2';
					}
				}
			}
		}
		var modef = BX('modef');
		var modef_num = BX('modef_num');
		if(modef && modef_num)
		{
			modef_num.innerHTML = result.ELEMENT_COUNT;
			var hrefFILTER = BX.findChildren(modef, {tag: 'A'}, true);

			if(result.FILTER_URL && hrefFILTER)
				hrefFILTER[0].href = BX.util.htmlspecialcharsback(result.FILTER_URL);

			if(result.FILTER_AJAX_URL && result.COMPONENT_CONTAINER_ID)
			{
				BX.bind(hrefFILTER[0], 'click', function(e)
				{
					var url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
					BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
					return BX.PreventDefault(e);
				});
			}

			if (result.INSTANT_RELOAD && result.COMPONENT_CONTAINER_ID)
			{
				var url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
				BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
			}
			else
			{
				if(modef.style.display == 'none')
					modef.style.display = 'block';
				/* GoPro */
				var NewPoStop = this.position.top;
				if($(RSGoPro_inputter).hasClass('min') || $(RSGoPro_inputter).hasClass('max')){
					NewPoStop = NewPoStop + RSGoPro_modef_posFix + 1;
				}
				else if($(RSGoPro_inputter).parents('.f_jscrollpane').length>0){
					var id = $(RSGoPro_inputter).parents('.f_jscrollpane').attr('id');
					NewPoStop = NewPoStop + RSGoPro_modef_posFix + BX(id).offsetTop - $('#'+id).data('jsp').getContentPositionY();
				}
				else{
					NewPoStop = NewPoStop + RSGoPro_modef_posFix;
				}
				modef.style.top = NewPoStop + 'px';
				/* /GoPro */
			}
			/* GoPro */
			RSGoPro_tamautID = setInterval(function(){
				modef.style.display = 'none';
				clearInterval(RSGoPro_tamautID);
			},RSGoPro_modef_delay_hide);
			/* /GoPro */
		}
	}
}

JCSmartFilter.prototype.gatherInputsValues = function (values, elements)
{
	if(elements)
	{
		for(var i = 0; i < elements.length; i++)
		{
			var el = elements[i];
			if (el.disabled || !el.type)
				continue;

			switch(el.type.toLowerCase())
			{
				case 'text':
				case 'textarea':
				case 'password':
				case 'hidden':
				case 'select-one':
					if(el.value.length)
						values[values.length] = {name : el.name, value : el.value};
					break;
				case 'radio':
				case 'checkbox':
					if(el.checked)
						values[values.length] = {name : el.name, value : el.value};
					break;
				case 'select-multiple':
					for (var j = 0; j < el.options.length; j++)
					{
						if (el.options[j].selected)
							values[values.length] = {name : el.name, value : el.options[j].value};
					}
					break;
				default:
					break;
			}
		}
	}
}

JCSmartFilter.prototype.values2post = function (values)
{
	var post = new Array;
	var current = post;
	var i = 0;
	while(i < values.length)
	{
		var p = values[i].name.indexOf('[');
		if(p == -1)
		{
			current[values[i].name] = values[i].value;
			current = post;
			i++;
		}
		else
		{
			var name = values[i].name.substring(0, p);
			var rest = values[i].name.substring(p+1);
			if(!current[name])
				current[name] = new Array;

			var pp = rest.indexOf(']');
			if(pp == -1)
			{
				//Error - not balanced brackets
				current = post;
				i++;
			}
			else if(pp == 0)
			{
				//No index specified - so take the next integer
				current = current[name];
				values[i].name = '' + current.length;
			}
			else
			{
				//Now index name becomes and name and we go deeper into the array
				current = current[name];
				values[i].name = rest.substring(0, pp) + rest.substring(pp+1);
			}
		}
	}
	return post;
}

/***********************************************************************/
/******************************* custom ********************************/
/***********************************************************************/
$(document).ready(function(){
	
	RSGoPro_FilterOnDocumentReady();
	
});