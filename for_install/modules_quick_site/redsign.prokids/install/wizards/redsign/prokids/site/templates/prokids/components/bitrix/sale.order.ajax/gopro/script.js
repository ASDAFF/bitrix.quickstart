BX.saleOrderAjax = {

	BXCallAllowed: false,

	options: {},
	indexCache: {},
	controls: {},

	modes: {},
	properties: {},

	// called once, on component load
	init: function(options)
	{
		var ctx = this;
		this.options = options;

		window.submitFormProxy = BX.proxy(function(){
			ctx.submitFormProxy.apply(ctx, arguments);
		}, this);

		BX(function(){
			ctx.initDeferredControl();
		});
		BX(function(){
			ctx.BXCallAllowed = true; // unlock form refresher
		});

		this.controls.scope = BX('order_form_div');

		// user presses "add location" when he cannot find location in popup mode
		BX.bindDelegate(this.controls.scope, 'click', {className: '-bx-popup-set-mode-add-loc'}, function(){

			var input = BX.create('input', {
				attrs: {
					type: 'hidden',
					name: 'PERMANENT_MODE_STEPS',
					value: '1'
				}
			});

			BX.prepend(input, BX('ORDER_FORM'));

			ctx.BXCallAllowed = false;
			submitForm();
		});
	},

	cleanUp: function(){

		for(var k in this.properties){
			if(typeof this.properties[k].input != 'undefined'){
				BX.unbindAll(this.properties[k].input);
				this.properties[k].input = null;
			}

			if(typeof this.properties[k].control != 'undefined'){
				BX.unbindAll(this.properties[k].control);
			}
		}

		this.properties = {};
	},

	addPropertyDesc: function(desc){
		this.properties[desc.id] = desc.attributes;
		this.properties[desc.id].id = desc.id;
	},

	// called each time form refreshes
	initDeferredControl: function()
	{
		var ctx = this;

		// first, init all controls
		if(typeof window.BX.locationsDeferred != 'undefined'){

			this.BXCallAllowed = false;

			for(var k in window.BX.locationsDeferred){

				window.BX.locationsDeferred[k].call(this);
				window.BX.locationsDeferred[k] = null;
				delete(window.BX.locationsDeferred[k]);

				this.properties[k].control = window.BX.locationSelectors[k];
				delete(window.BX.locationSelectors[k]);
			}
		}

		for(var k in this.properties){

			// zip input handling
			if(this.properties[k].isZip){
				var row = this.controls.scope.querySelector('[data-property-id-row="'+k+'"]');
				if(BX.type.isElementNode(row)){

					var input = row.querySelector('input[type="text"]');
					if(BX.type.isElementNode(input)){
						this.properties[k].input = input;

						// set value for the first "location" property met
						var locPropId = false;
						for(var m in this.properties){
							if(this.properties[m].type == 'LOCATION'){
								locPropId = m;
								break;
							}
						}

						if(locPropId !== false){
							BX.bindDebouncedChange(input, function(value){

								input = null;
								row = null;

								if(/^\s*\d{6}\s*$/.test(value)){

									ctx.getLocationByZip(value, function(locationId){
										ctx.properties[locPropId].control.setValueById(locationId);
									}, function(){
										try{
											ctx.properties[locPropId].control.clearSelected(locationId);
										}catch(e){}
									});
								}
							});
						}
					}
				}
			}

			if(this.checkAbility(k, 'canHaveAltLocation')){

				//this.checkMode(k, 'altLocationChoosen');

				var control = this.properties[k].control;

				// control can have "select other location" option
				control.setOption('pseudoValues', ['other']);

				// when control tries to search for items
				control.bindEvent('before-control-item-discover-done', function(knownItems, adapter){

					control = null;

					var parentValue = adapter.getParentValue();

					// you can choose "other" location only if parentNode is not root and is selectable
					if(parentValue == this.getOption('rootNodeValue') || !this.checkCanSelectItem(parentValue))
						return;

					knownItems.unshift({DISPLAY: ctx.options.messages.otherLocation, VALUE: 'other', CODE: 'other', IS_PARENT: false});
				});

				// currently wont work for initially created controls, so commented out
				/*
				// when control is being created with knownItems
				control.bindEvent('before-control-placed', function(adapter){
					if(typeof adapter.opts.knownItems != 'undefined')
						adapter.opts.knownItems.unshift({DISPLAY: so.messages.otherLocation, VALUE: 'other', CODE: 'other', IS_PARENT: false});

				});
				*/

				// add special value "other", if there is "city" input
				if(this.checkMode(k, 'altLocationChoosen')){
					
					var altLocProp = this.getAltLocPropByRealLocProp(k);
					this.toggleProperty(altLocProp.id, true);

					var adapter = control.getAdapterAtPosition(control.getStackSize() - 1);

					// also restore "other location" label on the last control
					if(typeof adapter != 'undefined' && adapter !== null)
						adapter.setValuePair('other', ctx.options.messages.otherLocation); // a little hack
				}else{

					var altLocProp = this.getAltLocPropByRealLocProp(k);
					this.toggleProperty(altLocProp.id, false);

				}
			}else{

				var altLocProp = this.getAltLocPropByRealLocProp(k);
				if(altLocProp !== false){

					// replace default boring "nothing found" label for popup with "-bx-popup-set-mode-add-loc" inside
					if(this.properties[k].type == 'LOCATION' && typeof this.properties[k].control != 'undefined' && this.properties[k].control.getSysCode() == 'sls')
						this.properties[k].control.replaceTemplate('nothing-found', this.options.messages.notFoundPrompt);

					this.toggleProperty(altLocProp.id, false);
				}
			}

			if(typeof this.properties[k].control != 'undefined' && this.properties[k].control.getSysCode() == 'slst'){

				var control = this.properties[k].control;

				// if a children of CITY is shown, we must replace label for 'not selected' variant
				var adapter = control.getAdapterAtPosition(control.getStackSize() - 1);
				var node = this.getPreviousAdapterSelectedNode(control, adapter);

				if(node !== false && node.TYPE_ID == ctx.options.cityTypeId){

					var selectBox = adapter.getControl();
					if(selectBox.getValue() == false){

						adapter.getControl().replaceMessage('notSelected', ctx.options.messages.moreInfoLocation);
						adapter.setValuePair('', ctx.options.messages.moreInfoLocation);
					}
				}
			}

		}

		this.BXCallAllowed = true;
	},

	checkMode: function(propId, mode){

		//if(typeof this.modes[propId] == 'undefined')
		//	this.modes[propId] = {};

		//if(typeof this.modes[propId] != 'undefined' && this.modes[propId][mode])
		//	return true;

		if(mode == 'altLocationChoosen'){

			if(this.checkAbility(propId, 'canHaveAltLocation')){

				var input = this.getInputByPropId(this.properties[propId].altLocationPropId);
				var altPropId = this.properties[propId].altLocationPropId;

				if(input !== false && input.value.length > 0 && !input.disabled && this.properties[altPropId].valueSource != 'default'){

					//this.modes[propId][mode] = true;
					return true;
				}
			}
		}

		return false;
	},

	checkAbility: function(propId, ability){

		if(typeof this.properties[propId] == 'undefined')
			this.properties[propId] = {};

		if(typeof this.properties[propId].abilities == 'undefined')
			this.properties[propId].abilities = {};

		if(typeof this.properties[propId].abilities != 'undefined' && this.properties[propId].abilities[ability])
			return true;

		if(ability == 'canHaveAltLocation'){

			if(this.properties[propId].type == 'LOCATION'){

				// try to find corresponding alternate location prop
				if(typeof this.properties[propId].altLocationPropId != 'undefined' && typeof this.properties[this.properties[propId].altLocationPropId]){

					var altLocPropId = this.properties[propId].altLocationPropId;

					if(typeof this.properties[propId].control != 'undefined' && this.properties[propId].control.getSysCode() == 'slst'){

						if(this.getInputByPropId(altLocPropId) !== false){
							this.properties[propId].abilities[ability] = true;
							return true;
						}
					}
				}
			}

		}

		return false;
	},

	getInputByPropId: function(propId){
		if(typeof this.properties[propId].input != 'undefined')
			return this.properties[propId].input;

		var row = this.getRowByPropId(propId);
		if(BX.type.isElementNode(row)){
			var input = row.querySelector('input[type="text"]');
			if(BX.type.isElementNode(input)){
				this.properties[propId].input = input;
				return input;
			}
		}

		return false;
	},

	getRowByPropId: function(propId){

		if(typeof this.properties[propId].row != 'undefined')
			return this.properties[propId].row;

		var row = this.controls.scope.querySelector('[data-property-id-row="'+propId+'"]');
		if(BX.type.isElementNode(row)){
			this.properties[propId].row = row;
			return row;
		}

		return false;
	},

	getAltLocPropByRealLocProp: function(propId){
		if(typeof this.properties[propId].altLocationPropId != 'undefined')
			return this.properties[this.properties[propId].altLocationPropId];

		return false;
	},

	toggleProperty: function(propId, way, dontModifyRow){

		var prop = this.properties[propId];

		if(typeof prop.row == 'undefined')
			prop.row = this.getRowByPropId(propId);

		if(typeof prop.input == 'undefined')
			prop.input = this.getInputByPropId(propId);

		if(!way){
			if(!dontModifyRow)
				BX.hide(prop.row);
			prop.input.disabled = true;
		}else{
			if(!dontModifyRow)
				BX.show(prop.row);
			prop.input.disabled = false;
		}
	},

	submitFormProxy: function(item, control)
	{
		var propId = false;
		for(var k in this.properties){
			if(typeof this.properties[k].control != 'undefined' && this.properties[k].control == control){
				propId = k;
				break;
			}
		}

		if(item != 'other'){

			if(this.BXCallAllowed){

				// drop mode "other"
				if(propId != false){
					if(this.checkAbility(propId, 'canHaveAltLocation')){

						if(typeof this.modes[propId] == 'undefined')
							this.modes[propId] = {};

						this.modes[propId]['altLocationChoosen'] = false;

						var altLocProp = this.getAltLocPropByRealLocProp(propId);
						if(altLocProp !== false){

							this.toggleProperty(altLocProp.id, false);
						}
					}
				}

				this.BXCallAllowed = false;
				submitForm();
			}

		}else{ // only for sale.location.selector.steps

			if(this.checkAbility(propId, 'canHaveAltLocation')){

				var adapter = control.getAdapterAtPosition(control.getStackSize() - 2);
				if(adapter !== null){
					var value = adapter.getValue();
					control.setTargetInputValue(value);

					// set mode "other"
					if(typeof this.modes[propId] == 'undefined')
						this.modes[propId] = {};
						
					this.modes[propId]['altLocationChoosen'] = true;

					var altLocProp = this.getAltLocPropByRealLocProp(propId);
					if(altLocProp !== false){

						this.toggleProperty(altLocProp.id, true, true);
					}

					this.BXCallAllowed = false;
					submitForm();
				}
			}
		}
	},

	getPreviousAdapterSelectedNode: function(control, adapter){

		var index = adapter.getIndex();
		var prevAdapter = control.getAdapterAtPosition(index - 1);

		if(typeof prevAdapter !== 'undefined' && prevAdapter != null){
			var prevValue = prevAdapter.getControl().getValue();

			if(typeof prevValue != 'undefined'){
				var node = control.getNodeByValue(prevValue);

				if(typeof node != 'undefined')
					return node;

				return false;
			}
		}

		return false;
	},
	getLocationByZip: function(value, successCallback, notFoundCallback)
	{
		if(typeof this.indexCache[value] != 'undefined')
		{
			successCallback.apply(this, [this.indexCache[value]]);
			return;
		}

		ShowWaitWindow();

		var ctx = this;

		BX.ajax({

			url: this.options.source,
			method: 'post',
			dataType: 'json',
			async: true,
			processData: true,
			emulateOnload: true,
			start: true,
			data: {'ACT': 'GET_LOC_BY_ZIP', 'ZIP': value},
			//cache: true,
			onsuccess: function(result){

				//try{

				CloseWaitWindow();
				if(result.result){

					ctx.indexCache[value] = result.data.ID;

					successCallback.apply(ctx, [result.data.ID]);

				}else
					notFoundCallback.call(ctx);

				//}catch(e){console.dir(e);}

			},
			onfailure: function(type, e){

				CloseWaitWindow();
				// on error do nothing
			}

		});
	}

}

$(document).ready(function(){
	
	// props
	$(document).on('focus','.f_text input[type="text"], .f_textarea input[type="text"], .f_text textarea, .f_textarea textarea',function(){
		$vl = $(this).parents('.vl');
		if( $vl.find('.description').length>0 ) {
			$vl.find('.description').fadeIn(150);
		}
	}).on('blur','.f_text input[type="text"], .f_textarea input[type="text"], .f_text textarea, .f_textarea textarea',function(){
		$vl = $(this).parents('.vl');
		if( $vl.find('.description').length>0 ) {
			$vl.find('.description').fadeOut(150);
		}
	});
	
	if( $('.line.f_location').find('.search-suggest').val()!='' && parseInt($('.line.f_location').find('input[type="hidden"]').val())>0 ) {
		if( $('.section.paysystem').length<1 || $('.section.delivery').length<1 ) {
			submitForm();
		}
	}

	RSGoPro_InitMaskPhone();
	
});