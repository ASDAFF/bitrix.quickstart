/*
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

var EAFunction = {
	
	FormResetValid : '',
	
	FormResetResultName : [],
	
	FormReset : function(Object) {
		var FormValid = Object.parents().map( function () {
			if(this.tagName == 'FORM') {
				$(this).find('select').each( function() {
					EAFunction.FormResetValid = 'true';
					$(this).change();
				});
				$(this).find('input').each( function() {
					if( $(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio' ) {
						if( $(this).attr('EAMODIFY') ) {
							EAFunction.FormResetResultName.push($(this).attr('name'));
							EAFunction.FormResetValid = 'true';
							$(this).change();
						}
					}
				});
				EAFunction.ValidEmptyResultName = [];
			}
		});
	},
		
	ValidModifyInterval : '',
	
	ValidModify : function(ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, SystemFunction) {
		ValidModifyInterval = setTimeout( function() {
			ModifyDesignForm (ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, SystemFunction);
			if( EAFunction.ValidModifyInterval == 'true' ) {
				EAFunction.ValidModify(ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, SystemFunction);
			}
		}, 100);
	},
	
	ValidSize : function(ObjectType, ObjectId, ObjectParent, ObjectDefaultWidth, ObjectDefaultHeight) {
		ValidSizeInterval = setTimeout( function() {
			if( ObjectType == 'SELECT' && parseInt(ObjectDefaultHeight) > 0 ) {
				if( ObjectParent.height() > ObjectDefaultHeight ) {
					PopupCurentHeight = ObjectParent.height() - ObjectDefaultHeight;
					PopupCurentHeight = ObjectId.height() - PopupCurentHeight;
					ObjectId.height( PopupCurentHeight );
				}
			}
			if( ObjectType == 'SELECT_MULTIPLE' || ObjectType == 'TEXTAREA' ) {
				ItemHeight = ObjectParent.children('div').height();
				if( ItemHeight < ObjectParent.height() ) {
					ObjectId.height( ObjectId.height() + ObjectParent.height() - ItemHeight );
				}
			}
			if( ObjectType == 'TEXTAREA' ) {
				ObjectId.width( ObjectId.parent().width() );
			}
			EAFunction.ValidSize(ObjectType, ObjectId, ObjectParent, ObjectDefaultWidth, ObjectDefaultHeight);
		}, 100);
	},
	
	ValidEmptyValid : '',
	
	ValidEmptyResultName : [],
	
	ValidEmptyFunction : function(Object, ErrorMessage, Type) {
		if( Type == 'INPUT' || Type == 'SELECT' ) {
			FieldValue = Object.val();
			if( FieldValue != null && Object.attr('multiple') == true ) {
				FieldValue = FieldValue.join('');
			}
		}else if( Type == 'TEXTAREA' ) {
			FieldValue = Object.html();
		}else if( Type == 'CHECK' ) {
			var FormValid = Object.parents().map( function () {
				if(this.tagName == 'FORM') {
					FieldValue = '';
					$(this).find('input[name="'+Object.attr('name')+'"]').each( function() {
						if( $(this).attr('checked') == true || $(this).attr('checked') == 'checked' ) {
							FieldValue = Object.val();
							EAFunction.ValidEmptyResultName.push(Object.attr('name'));
						}
					});
				}
			});
		}
		if( typeof(FieldValue) != 'string' ) {
			FieldValue = '';
		}
		if( jQuery.trim(FieldValue) == '' && jQuery.inArray(Object.attr('name'),EAFunction.ValidEmptyResultName) == -1 ) {
			if( Object.attr('title') != '' && Object.attr('title') != 'undefined' ) {
				FieldName = Object.attr('title');
			}else{
				FieldName = Object.attr('name');
			}
			ReturnMessage = ErrorMessage.split('#FIELD_NAME#').join('"'+FieldName+'"');
			alert(ReturnMessage);
			EAFunction.ValidEmptyResultName.push(Object.attr('name'));
			return false;
		}else{
			return true;
		}
	},
	
	ValidEmpty : function(Object, ErrorMessage){
		var FormValid = Object.parents().map( function () {
			if(this.tagName == 'FORM') {
				ValidEmptyResult = true; 
				$(this).find('input').each( function() {
					if( $(this).attr('ValidEmpty') != undefined && $(this).attr('type') != 'reset' && $(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio' && $(this).attr('type') != 'submit' && $(this).attr('type') != 'button' ) {
						if( ValidEmptyResult != false ) {
							ValidEmptyResult = EAFunction.ValidEmptyFunction($(this), ErrorMessage, 'INPUT');
							//return FormValidResult;
						}else{
							EAFunction.ValidEmptyFunction($(this), ErrorMessage, 'INPUT');
						}
					}
				});
				$(this).find('input').each( function() {
					if( $(this).attr('ValidEmpty') != undefined && ( $(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio' ) ) {
						if( ValidEmptyResult != false ) {
							ValidEmptyResult = EAFunction.ValidEmptyFunction($(this), ErrorMessage, 'CHECK');
							//return FormValidResult;
						}else{
							EAFunction.ValidEmptyFunction($(this), ErrorMessage, 'CHECK');
						}
					}
				});
				$(this).find('select').each( function() {
					if( $(this).attr('ValidEmpty') != undefined ) {
						if( ValidEmptyResult != false ) {
							ValidEmptyResult = EAFunction.ValidEmptyFunction($(this), ErrorMessage, 'SELECT');
							//return FormValidResult;
						}else{
							EAFunction.ValidEmptyFunction($(this), ErrorMessage, 'SELECT');
						}
					}
				});
				$(this).find('textarea').each( function() {
					if( $(this).attr('ValidEmpty') != undefined ) {
						if( ValidEmptyResult != false ) {
							ValidEmptyResult = EAFunction.ValidEmptyFunction($(this), ErrorMessage, 'TEXTAREA');
							//return FormValidResult;
						}else{
							EAFunction.ValidEmptyFunction($(this), ErrorMessage, 'TEXTAREA');
						}
					}
				});
				EAFunction.ValidEmptyResultName = [];
				
				return ValidEmptyResult;
			}
		}).get();
		if( ValidEmptyResult == false ) {
			return false;
		}else{
			return true;
		}
	},
	
	ValidEmailValid : '',
	
	ValidEmailFunction : function(email, ErrorMessage) {
		filter = /^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z])+$/;
		if ( filter.test( jQuery.trim(email) ) ) {
			return true;
		}else{
			alert(ErrorMessage);
			return false;
		}
	},
	
	ValidEmail : function(Object, ErrorMessage){
		var FormValid = Object.parents().map( function () {
			if(this.tagName == 'FORM') {
				ValidEmailResult = true;
				$(this).find('input[type="text"]').each( function() {
					if( $(this).attr('ValidEmail') != undefined ) {
						email = $(this).val();
						if( ValidEmailResult != false ) {
							ValidEmailResult = EAFunction.ValidEmailFunction(email, ErrorMessage);
						}else{
							EAFunction.ValidEmailFunction(email, ErrorMessage);
						}
					}
				});
				
				return ValidEmailResult;
			}
		}).get();
		if( ValidEmailResult == false ) {
			return false;
		}else{
			return true;
		}
	}
	
};