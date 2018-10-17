function ModifyDesignForm (ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, SystemFunction) {

	if(ModifyFormTemplate != '' && ModifyFormTag != '' && ModifyFormParam != '' && ModifyFormValue != '' && ModifyFormParamMore != '') {

		i = 0;
		$('form').each( function () {
			if($(this).attr('id') == ''){
				$(this).attr('id','EAFormAutoID'+i);
			}
			++i;
		});

		i = 0;
		$('input[type="text"],input[type="password"],input[type="file"],input[type="checkbox"],input[type="radio"],input[type="button"],input[type="submit"],input[type="reset"],textarea,select,button').each( function () {
			if($(this).attr('id') == '') {
				$(this).attr('id','EAFormInputAutoID'+i);
			}
			++i;
		});
		var ValidObject = {};
		var all = 0;
		
		i = 0;
		$('input[type="text"],input[type="password"]').each( function () {
			MoreValid = '';
			ModifyFormParamMoreValue = ModifyFormParamMore.split('||');
			for(k=0;k<ModifyFormParamMoreValue.length;++k) {
				ModifyFormParamMoreValueData = ModifyFormParamMoreValue[k];
				if($(this).attr('name') != '') {
					TagValueThis = 'TEXT#name#'+$(this).attr('name');
				}else{
					TagValueThis = 'TEXT#id#'+$(this).attr('id');
				}
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData.split('#');
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData[0]+'#'+ModifyFormParamMoreValueData[1]+'#'+ModifyFormParamMoreValueData[2];
				if(TagValueThis != '' && TagValueThis == ModifyFormParamMoreValueData) {
					MoreValid = 'true';
					MoreValidNum = k;
				}
			}
			FormValid = $(this).parents().map( function () {
				if(this.tagName == ModifyFormTag && $(this).attr(ModifyFormParam) == ModifyFormValue) { 
					return "true";
				}
			}).get();
			if(FormValid == 'true' && MoreValid == 'true' && $(this).attr('EAMODIFY') == undefined) {
				$(this).show();
				$(this).attr('EAMODIFY',true);
				$(this).attr('class','');
				
				var FormNewItemID = 'EAFormInputText_'+ModifyFormTag+'_'+ModifyFormParam+'_'+ModifyFormValue+'_'+$(this).attr('id')+'_'+i;
				var FormNewItemID = FormNewItemID.split('[').join('');
				var FormNewItemID = FormNewItemID.split(']').join(''); 
				var ItemParam = ModifyFormParamMoreValue[MoreValidNum].split('#');
				
				var FormDateInputValid = $(this).attr('name');
				FormDateInputValid = FormDateInputValid.split('_');
				if(FormDateInputValid[2] != false) { FormDateIDInputValid = FormDateInputValid[2]; } else { FormDateIDInputValid = ''; }
				if(FormDateInputValid[1] != false) { FormDateInputValid = FormDateInputValid[1]; } else { FormDateInputValid = FormDateInputValid[0]; }

				$(this).wrap('<div id="'+FormNewItemID+'" class="'+ModifyFormTemplate+'_EAFormInputText"></div>');
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputText2"></div>');
				
				if( ItemParam[6] == 'small' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.TextSmallDefaultWidth);
				}else if( ItemParam[6] == 'big' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.TextBigDefaultWidth);
				}else if( ItemParam[6] == 'custom' && ItemParam[7] != '' ) {
					if( ItemParam[7] != '0' ) {
						$('#'+FormNewItemID).css('width',ItemParam[7]);
					}
				}else{
					$('#'+FormNewItemID).css('width',EAFormParams.TextDefaultWidth);
				}
				
				if( ItemParam[4] == 'left' ) {
					$('#'+FormNewItemID).css('float','left');
				}else if( ItemParam[4] == 'right' ){
					$('#'+FormNewItemID).css('float','right');
				}
				
				if( ItemParam[5] == 'Y' ) {
					$('#'+FormNewItemID).after('<div class="clear"></div>');
				}
				
				if( ItemParam[3] == 'date' || FormDateInputValid == 'date' ) {
					$('#'+FormNewItemID).addClass(''+ModifyFormTemplate+'_EAFormInputTextDate');
					$(this).before(EAFormParams.TextDateBefore);
					$(this).after(EAFormParams.TextDateAfter);
					$(this).after('<div class="'+ModifyFormTemplate+'_EAFormInputDateBtn"><div></div></div>');
				}else if( ItemParam[3] == 'password' || $(this).attr('type') == 'password' ) {
					$(this).before(EAFormParams.TextPasswordBefore);
					$(this).after(EAFormParams.TextPasswordAfter);
				}else{
					$(this).before(EAFormParams.TextBefore);
					$(this).after(EAFormParams.TextAfter);
				}
				
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputTextBlock"></div>');
				
				if( ItemParam[3] == 'date' || FormDateInputValid == 'date' ) {
					for(x=0;x < EAFormParams.TextDateWrap.length;++x){
						$(this).wrap(EAFormParams.TextDateWrap[x]);
					}
				}else if( ItemParam[3] == 'password' || $(this).attr('type') == 'password' ) {
					for(x=0;x < EAFormParams.TextPasswordWrap.length;++x){
						$(this).wrap(EAFormParams.TextPasswordWrap[x]);
					}
				}else{
					for(x=0;x < EAFormParams.TextWrap.length;++x){
						$(this).wrap(EAFormParams.TextWrap[x]);
					}
				}
				
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputTextBox"></div>');
				
				$('#'+FormNewItemID).live('mouseover', function(){
					if( $(this).find('input').attr('EAFOCUS') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputText_Hover');
						if( ItemParam[3] == 'date' || FormDateInputValid == 'date' ) {
							$('#'+FormNewItemID).addClass(''+ModifyFormTemplate+'_EAFormInputTextDate');
						}
					}
				});
				$('#'+FormNewItemID).live('mouseout', function(){
					if( $(this).find('input').attr('EAFOCUS') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputText');
						if( ItemParam[3] == 'date' || FormDateInputValid == 'date' ) {
							$('#'+FormNewItemID).addClass(''+ModifyFormTemplate+'_EAFormInputTextDate');
						}
					}
				});
				$('#'+FormNewItemID).find('input').live('focus', function(){
					$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputText_Selected');
					$(this).attr('EAFOCUS',true);
					if( ItemParam[3] == 'date' || FormDateInputValid == 'date' ) {
						$('#'+FormNewItemID).addClass(''+ModifyFormTemplate+'_EAFormInputTextDate');
					}
				});
				$('#'+FormNewItemID).find('input').live('blur', function(){
					$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputText');
					$(this).removeAttr('EAFOCUS');
					if( ItemParam[3] == 'date' || FormDateInputValid == 'date' ) {
						$('#'+FormNewItemID).addClass(''+ModifyFormTemplate+'_EAFormInputTextDate');
					}
				});
				
				if( ItemParam[3] == 'date' || FormDateInputValid == 'date' ) {
					Onclick = '';
					jsdate = new Date();
					
					$('#'+FormNewItemID).live('click',function(){
						FormDateIDInputValid2 = $(this).find('input').attr('name');
						jsCalendar.Show(this, FormDateIDInputValid2, FormDateIDInputValid2, '', false, ( jsdate.getTime()/1000 ), '', false);
					});
					$('#'+FormNewItemID).find('.'+ModifyFormTemplate+'_EAFormInputDateBtn').live('mouseover', function(){
						$(this).css('cursor','pointer');
					});
					$('#'+FormNewItemID).find('.'+ModifyFormTemplate+'_EAFormInputDateBtn').live('mouseout', function(){
						$(this).css('cursor','default');
					});

					$('img[class="calendar-icon"]').each( function () {
						var FormValidDate = $(this).parents().map( function () {
							if(this.tagName == ModifyFormTag && $(this).attr(ModifyFormParam) == ModifyFormValue) { 
								return "true";
							}
						}).get();
						if(FormValidDate == 'true') {
							var FormDateIDImgValid = ''+$(this).attr('onclick');
							FormDateIDImgValid = FormDateIDImgValid.split('\'');
							if( FormDateIDImgValid[1] == undefined ) {
								FormDateIDImgValid = FormDateIDImgValid[0].split('"');
							}
							FormDateIDImgValid = FormDateIDImgValid[1].split('_');
							if(FormDateIDInputValid == FormDateIDImgValid[2]) {
								$(this).hide();
							}
						}
					});
				}
				
				ItemFunction = ItemParam[8].split(';');
				for(f=0;f<ItemFunction.length;++f) { 
					if( ItemFunction[f] == 'ValidModify' && SystemFunction != 'ClearValidModify' && EAFunction.ValidModifyInterval != 'true' ) {
						EAFunction.ValidModifyInterval = 'true';
						EAFunction.ValidModify(ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, 'ClearValidModify');
					}
					if( ItemFunction[f] == 'ValidEmpty' ) {
						$(this).attr('ValidEmpty',true);
						if( EAFunction.ValidEmptyValid != 'true' ) {
							EAFunction.ValidEmptyValid = 'true';
							var FormValid = $(this).parents().map( function () {
								if(this.tagName == 'FORM') {
									$(this).find('input[type="submit"]').click( function() {
										return EAFunction.ValidEmpty($(this), EAFormParams.ValidEmptyErrorMessage);
									});
								}
							}).get();
						}
					}
					if( ItemFunction[f] == 'ValidEmail' ) {
						$(this).attr('ValidEmail',true);
						if( EAFunction.ValidEmailValid != 'true' ) {
							EAFunction.ValidEmailValid = 'true';
							var FormValid = $(this).parents().map( function () {
								if(this.tagName == 'FORM') {
									$(this).find('input[type="submit"]').click( function() {
										return EAFunction.ValidEmail($(this), EAFormParams.ValidEmailErrorMessage);
									});
								}
							}).get();
						}
					}
				}
				
				++all;
				
			}
			++i;
		});

		i = 0;
		$('textarea').each( function () {
			MoreValid = '';
			ModifyFormParamMoreValue = ModifyFormParamMore.split('||');
			for(k=0;k<ModifyFormParamMoreValue.length;++k) {
				var ModifyFormParamMoreValueData = ModifyFormParamMoreValue[k];
				if($(this).attr('name') != '') {
					TagValueThis = 'TEXTAREA#name#'+$(this).attr('name');
				}else{
					TagValueThis = 'TEXTAREA#id#'+$(this).attr('id');
				}
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData.split('#');
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData[0]+'#'+ModifyFormParamMoreValueData[1]+'#'+ModifyFormParamMoreValueData[2];
				if(TagValueThis != '' && TagValueThis == ModifyFormParamMoreValueData){
					MoreValid = 'true';
					MoreValidNum = k;
				}
			}
			var FormValid = $(this).parents().map( function () {
				if(this.tagName == ModifyFormTag && $(this).attr(ModifyFormParam) == ModifyFormValue) { 
					return "true";
				}
			}).get();
			if(FormValid == 'true' && MoreValid == 'true' && $(this).attr('EAMODIFY') == undefined) {
				$(this).show();
				$(this).attr('EAMODIFY',true);
				$(this).attr('class','');

				var FormNewItemID = 'EAFormInputTextarea_'+ModifyFormTag+'_'+ModifyFormParam+'_'+ModifyFormValue+'_'+$(this).attr('id')+'_'+i;
				var FormNewItemID = FormNewItemID.split('[').join('');
				var FormNewItemID = FormNewItemID.split(']').join('');
				var ItemParam = ModifyFormParamMoreValue[MoreValidNum].split('#');
				
				$(this).wrap('<div id="'+FormNewItemID+'" class="'+ModifyFormTemplate+'_EAFormInputTextarea"></div>');
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputTextarea2"></div>');
				
				if( ItemParam[6] == 'small' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.TextareaSmallDefaultWidth);
					$('#'+FormNewItemID).css('height',EAFormParams.TextareaSmallDefaultHeight);
				}else if( ItemParam[6] == 'big' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.TextareaBigDefaultWidth);
					$('#'+FormNewItemID).css('height',EAFormParams.TextareaBigDefaultHeight);
				}else if( ItemParam[6] == 'custom' && ItemParam[7] != '' ) {
					size = ItemParam[7].split(';');
					if( size[0] != '' ) {
						if( size[0] != '0' ) {
							$('#'+FormNewItemID).css('width',size[0]);
						}
					}else{
						$('#'+FormNewItemID).css('width',EAFormParams.TextareaDefaultWidth);
					}
					if( size[1] != '' && size[1] != '0') {
						$('#'+FormNewItemID).css('height',size[1]);
					}else{
						$('#'+FormNewItemID).css('height',EAFormParams.TextareaDefaultHeight);
					}
				}else{
					$('#'+FormNewItemID).css('width',EAFormParams.TextareaDefaultWidth);
					$('#'+FormNewItemID).css('height',EAFormParams.TextareaDefaultHeight);
				}
				
				if( ItemParam[4] == 'left' ) {
					$('#'+FormNewItemID).css('float','left');
				}else if( ItemParam[4] == 'right' ){
					$('#'+FormNewItemID).css('float','right');
				}
				
				if( ItemParam[5] == 'Y' ) {
					$('#'+FormNewItemID).after('<div class="clear"></div>');
				}
				
				$(this).before(EAFormParams.TextareaBefore);
				$(this).after(EAFormParams.TextareaAfter);
				
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputTextareaBlock"></div>');
				
				for(x=0;x < EAFormParams.TextareaWrap.length;++x){
					$(this).wrap(EAFormParams.TextareaWrap[x]);
				}
				
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputTextareaBox"></div>');
				
				$('#'+FormNewItemID).find('textarea').height( 0 );
				ItemHeight = $('#'+FormNewItemID+' div').height(); 
				if( ItemHeight < $('#'+FormNewItemID).height() ) {
					$('#'+FormNewItemID).find('textarea').height( $('#'+FormNewItemID).find('textarea').height() + $('#'+FormNewItemID).height() - ItemHeight );
				}
				
				$('#'+FormNewItemID).find('textarea').width( 0 );
				$('#'+FormNewItemID).find('textarea').width( $('#'+FormNewItemID).find('textarea').parent().width() );
				
				$('#'+FormNewItemID).live('mouseover', function(){
					if( $(this).find('textarea').attr('EAFOCUS') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputTextarea_Hover');
					}
				});
				$('#'+FormNewItemID).live('mouseout', function(){
					if( $(this).find('textarea').attr('EAFOCUS') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputTextarea');
					}
				});
				$('#'+FormNewItemID).find('textarea').live('focus', function(){
					$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputTextarea_Selected');
					$(this).attr('EAFOCUS',true);
				});
				$('#'+FormNewItemID).find('textarea').live('blur', function(){
					$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputTextarea');
					$(this).removeAttr('EAFOCUS');
				});
				
				ItemFunction = ItemParam[8].split(';');
				for(f=0;f<ItemFunction.length;++f) { 
					if( ItemFunction[f] == 'ValidModify' && SystemFunction != 'ClearValidModify' && EAFunction.ValidModifyInterval != 'true' ) {
						EAFunction.ValidModifyInterval = 'true';
						EAFunction.ValidModify(ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, 'ClearValidModify');
					}
					if( ItemFunction[f] == 'ValidSize' ) {
						EAFunction.ValidSize('TEXTAREA',$('#'+FormNewItemID).find('textarea'),$('#'+FormNewItemID),'','');
					}
					if( ItemFunction[f] == 'ValidEmpty' ) {
						$(this).attr('ValidEmpty',true);
						if( EAFunction.ValidEmptyValid != 'true' ) {
							EAFunction.ValidEmptyValid = 'true';
							var FormValid = $(this).parents().map( function () {
								if(this.tagName == 'FORM') {
									$(this).find('input[type="submit"]').click( function() {
										return EAFunction.ValidEmpty($(this), EAFormParams.ValidEmptyErrorMessage);
									});
								}
							}).get();
						}
					}
				}
				
				++all;
			}
			++i;
		});

		var i = 0;
		$('input[type="file"]').each( function () {
			var MoreValid = '';
			var ModifyFormParamMoreValue = ModifyFormParamMore.split('||');
			for(k=0;k<ModifyFormParamMoreValue.length;++k) {
				var ModifyFormParamMoreValueData = ModifyFormParamMoreValue[k];
				if($(this).attr('name') != '') {
					var TagValueThis = 'FILE#name#'+$(this).attr('name');
				}else{
					var TagValueThis = 'FILE#id#'+$(this).attr('id');
				}
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData.split('#');
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData[0]+'#'+ModifyFormParamMoreValueData[1]+'#'+ModifyFormParamMoreValueData[2];
				if(TagValueThis != '' && TagValueThis == ModifyFormParamMoreValueData){
					var MoreValid = 'true';
					MoreValidNum = k;
				}
			}
			var FormValid = $(this).parents().map( function () {
				if(this.tagName == ModifyFormTag && $(this).attr(ModifyFormParam) == ModifyFormValue) { 
					return "true";
				}
			}).get();
			if(FormValid == 'true' && MoreValid == 'true' && $(this).attr('EAMODIFY') == undefined) {
				$(this).show();
				$(this).attr('EAMODIFY',true);
				$(this).attr('class','');
				
				var FormNewItemID = 'EAFormInputFile_'+ModifyFormTag+'_'+ModifyFormParam+'_'+ModifyFormValue+'_'+$(this).attr('id')+'_'+i;
				var FormNewItemID = FormNewItemID.split('[').join('');
				var FormNewItemID = FormNewItemID.split(']').join('');
				var ItemParam = ModifyFormParamMoreValue[MoreValidNum].split('#');
				
				$(this).wrap('<div id="'+FormNewItemID+'" class="'+ModifyFormTemplate+'_EAFormInputFile"></div>');
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputFile2"></div>');
				
				if( ItemParam[6] == 'small' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.FileSmallDefaultWidth);
				}else if( ItemParam[6] == 'big' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.FileBigDefaultWidth);
				}else if( ItemParam[6] == 'custom' && ItemParam[7] != '' ) {
					if( ItemParam[7] != '0' ) {
						$('#'+FormNewItemID).css('width',ItemParam[7]);
					}
				}else{
					$('#'+FormNewItemID).css('width',EAFormParams.FileDefaultWidth);
				}
				
				if( ItemParam[4] == 'left' ) {
					$('#'+FormNewItemID).css('float','left');
				}else if( ItemParam[4] == 'right' ){
					$('#'+FormNewItemID).css('float','right');
				}
				
				if( ItemParam[5] == 'Y' ) {
					$('#'+FormNewItemID).after('<div class="clear"></div>');
				}
				
				$(this).before(EAFormParams.FileBefore);
				$(this).after(EAFormParams.FileAfter);
				
				$('#'+FormNewItemID+' div:first').after('<div class="'+ModifyFormTemplate+'_EAFormInputFileBtn">'+EAFormParams.FileBtn+'</div>');
				
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputFileBlock"></div>');
				
				for(x=0;x < EAFormParams.FileWrap.length;++x){
					$(this).wrap(EAFormParams.FileWrap[x]);
				}
					
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputFileBox"></div>');
				
				$(this).before('<input readonly type="text" id="'+FormNewItemID+'_text" class="'+ModifyFormTemplate+'_EAFormInputFileText">');

				$(this).appendTo( $('#'+FormNewItemID) );

				$(this).wrap('<div id="'+FormNewItemID+'_select" class="'+ModifyFormTemplate+'_EAFormInputFileSelect"></div>');
				
				$('#'+FormNewItemID+'_select input').change( function() {
					$('#'+FormNewItemID+'_text').val( $(this).val().replace("C:\\fakepath\\","") );
				});
				
				$('#'+FormNewItemID).live('mouseover', function(){
					if( $(this).find('input').attr('EAFOCUS') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputFile_Hover');
					}
				});
				$('#'+FormNewItemID).live('mouseout', function(){
					if( $(this).find('input').attr('EAFOCUS') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputFile');
					}
				});
				$('#'+FormNewItemID).find('input').live('focus', function(){
					$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputFile_Selected');
					$(this).attr('EAFOCUS',true);
				});
				$('#'+FormNewItemID).find('input').live('blur', function(){
					$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputFile');
					$(this).removeAttr('EAFOCUS');
				});
				
				$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputFileBtn').live('mouseover', function() {
					$(this).css('cursor','pointer');
				});
				$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputFileBtn').live('mouseout', function() {
					$(this).css('cursor','default');
				});
				$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputFileBtn').live('click', function() {
					$('#'+FormNewItemID+'_select input').click();
					$('#'+FormNewItemID+'_select input').change();
				});
				
				ItemFunction = ItemParam[8].split(';');
				for(f=0;f<ItemFunction.length;++f) { 
					if( ItemFunction[f] == 'ValidModify' && SystemFunction != 'ClearValidModify' && EAFunction.ValidModifyInterval != 'true' ) {
						EAFunction.ValidModifyInterval = 'true';
						EAFunction.ValidModify(ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, 'ClearValidModify');
					}
					if( ItemFunction[f] == 'ValidEmpty' ) {
						$(this).attr('ValidEmpty',true);
						if( EAFunction.ValidEmptyValid != 'true' ) {
							EAFunction.ValidEmptyValid = 'true';
							var FormValid = $(this).parents().map( function () {
								if(this.tagName == 'FORM') {
									$(this).find('input[type="submit"]').click( function() {
										return EAFunction.ValidEmpty($(this), EAFormParams.ValidEmptyErrorMessage);
									});
								}
							}).get();
						}
					}
				}
				
				++all;
			}
			++i;
		});

		var i = 0;
		$('select').each( function () {
			var MoreValid = '';
			var ModifyFormParamMoreValue = ModifyFormParamMore.split('||');
			for(k=0;k<ModifyFormParamMoreValue.length;++k) {
				var ModifyFormParamMoreValueData = ModifyFormParamMoreValue[k];
				if($(this).attr('name') != '') {
					var TagValueThis = 'SELECT#name#'+$(this).attr('name');
				}else{
					var TagValueThis = 'SELECT#id#'+$(this).attr('id');
				}
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData.split('#');
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData[0]+'#'+ModifyFormParamMoreValueData[1]+'#'+ModifyFormParamMoreValueData[2];
				if(TagValueThis != '' && TagValueThis == ModifyFormParamMoreValueData){
					var MoreValid = 'true';
					MoreValidNum = k;
				}
			}
			var FormValid = $(this).parents().map( function () {
				if(this.tagName == ModifyFormTag && $(this).attr(ModifyFormParam) == ModifyFormValue) { 
					return "true";
				}
			}).get();
			if(FormValid == 'true' && MoreValid == 'true' && $(this).attr('EAMODIFY') == undefined) {
				$(this).attr('EAMODIFY',true);
				$(this).attr('class','');
				
				var FormNewItemID = 'EAFormInputSelect_'+ModifyFormTag+'_'+ModifyFormParam+'_'+ModifyFormValue+'_'+$(this).attr('id')+'_'+i;
				var FormNewItemID = FormNewItemID.split('[').join('');
				var FormNewItemID = FormNewItemID.split(']').join(''); 
				var ItemParam = ModifyFormParamMoreValue[MoreValidNum].split('#');
				
				if($(this).attr('multiple') == false) {
					
					if( $(this).attr('id') == '' ) {
						var FormInputSelect = 'EAFormInputSelect_'+ModifyFormTag+'_'+ModifyFormParam+'_'+ModifyFormValue+'_'+i;
					} else {
						var FormInputSelect = $(this).attr('id');
					}
					var FormInputSelectText = 'EAFormInputSelectText_'+ModifyFormTag+'_'+ModifyFormParam+'_'+ModifyFormValue+'_'+i;
					var FormInputSelectPopup = 'EAFormInputSelectPopup_'+ModifyFormTag+'_'+ModifyFormParam+'_'+ModifyFormValue+'_'+i;
					
					$(this).attr('id',FormInputSelect);
					$(this).hide();
					
					$(this).wrap('<div id="'+FormNewItemID+'" class="'+ModifyFormTemplate+'_EAFormInputSelect"></div>');
					$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputSelect2"></div>');
					
					if( ItemParam[6] == 'small' ) {
						$('#'+FormNewItemID).css('width',EAFormParams.SelectSmallDefaultWidth);
					}else if( ItemParam[6] == 'big' ) {
						$('#'+FormNewItemID).css('width',EAFormParams.SelectBigDefaultWidth);
					}else if( ItemParam[6] == 'custom' && ItemParam[7] != '' ) {
						if( ItemParam[7] != '0' ) {
							$('#'+FormNewItemID).css('width',ItemParam[7]);
						}
					}else{
						$('#'+FormNewItemID).css('width',EAFormParams.SelectDefaultWidth);
					}
					
					if( ItemParam[4] == 'left' ) {
						$('#'+FormNewItemID).css('float','left');
					}else if( ItemParam[4] == 'right' ){
						$('#'+FormNewItemID).css('float','right');
					}
					
					if( ItemParam[5] == 'Y' ) {
						$('#'+FormNewItemID).after('<div class="clear"></div>');
					}
					
					
					var InputCode = '';
					InputCode += '<div id="'+FormInputSelectPopup+'" class="'+ModifyFormTemplate+'_EAFormInputSelectPopup" >';
					InputCode += '<div id="'+FormInputSelectPopup+'2" class="'+ModifyFormTemplate+'_EAFormInputSelectPopup2">';
					InputCode += EAFormParams.SelectPopupBefore;
					InputCode += '<ul>';
					var y = 0;
					$('#'+FormInputSelect+' > option').each( function () {
						InputCode += '<li value="'+$(this).val()+'" text="'+$(this).html()+'">';
						InputCode += EAFormParams.SelectPopupListItemBefore;
						if( jQuery.trim( $(this).html() ) != '' ) {
							InputCode += $(this).html();
						}else{
							InputCode += '&nbsp;';
						}
						InputCode += EAFormParams.SelectPopupListItemAfter;
						InputCode += '</li>';
						++y;
					});
					InputCode += '</ul>';
					InputCode += EAFormParams.SelectPopupAfter;
					InputCode += '</div>';
					InputCode += '</div>';
					$(this).after(InputCode);
					$('#'+FormNewItemID).find('#'+FormInputSelectPopup).find('li').each( function() {
						$(this).click( function(){
							if( $(this).attr('selected') == undefined ) {
								$(this).parent().children('li').removeAttr('selected');
								$(this).parent().children('li').attr('className','');
								$(this).attr('class', ModifyFormTemplate+'_EAFormInputSelectPopupBox_Selected');
								$(this).attr('selected',true);
								$('#'+FormNewItemID).find('option[value='+$(this).attr('value')+']').attr('selected',true);
								$('#'+FormNewItemID).find('select').change();
								$('#'+FormNewItemID).find('#'+FormInputSelectText+'_text').val( $(this).attr('text') );
								$('#'+FormInputSelectPopup).hide();
							}
						});
						$(this).mouseover( function(e){
							if( $(this).attr('selected') == undefined ) {
								$(this).attr('class', ModifyFormTemplate+'_EAFormInputSelectPopupBox_Hover');
							}
						});
						$(this).mouseout( function(e){
							if( $(this).attr('selected') == undefined && ( e.pageX >= Number($(this).position().left) + Number($('#'+FormInputSelectPopup).position().left) + 5 && e.pageX <= Number($(this).position().left)+Number($('#'+FormInputSelectPopup).position().left)+Number($(this).width()) - 5 && e.pageY >= Number($(this).position().top)+Number($('#'+FormInputSelectPopup).position().top) && e.pageY <= Number($(this).position().top)+Number($('#'+FormInputSelectPopup).position().top)+Number($(this).height()) ) != true ) {
								$(this).attr('class', '');
							}
						});
					});
					$('#'+FormInputSelectPopup).find('ul').wrap('<div class="'+ModifyFormTemplate+'_EAFormInputSelectPopupBlock"></div>');
					
					for(x=0;x < EAFormParams.SelectPopupWrap.length;++x){
						$('#'+FormInputSelectPopup).find('ul').wrap(EAFormParams.SelectPopupWrap[x]);
					}
					
					$('#'+FormInputSelectPopup).find('ul').wrap('<div id="'+FormInputSelectPopup+'_box" class="'+ModifyFormTemplate+'_EAFormInputSelectPopupBox"></div>');
					
					if( ItemParam[6] == 'small' ) {
						$('#'+FormInputSelectPopup).css('width',EAFormParams.SelectPopupSmallDefaultWidth);
						$('#'+FormInputSelectPopup).css('height',EAFormParams.SelectPopupSmallDefaultHeight);
					}else if( ItemParam[6] == 'big' ) {
						$('#'+FormInputSelectPopup).css('width',EAFormParams.SelectPopupBigDefaultWidth);
						$('#'+FormInputSelectPopup).css('height',EAFormParams.SelectPopupBigDefaultHeight);
					}else if( ItemParam[6] == 'custom' && ItemParam[7] != '' ) {
						size = ItemParam[7].split(';');
						if( size[0] != '' ) {
							if( size[0] != '0' ) {
								$('#'+FormInputSelectPopup).css('width',size[0]);
							}
						}else{
							$('#'+FormInputSelectPopup).css('width',EAFormParams.SelectPopupDefaultWidth);
						}
						if( size[1] != '' && size[1] != '0' ) {
							$('#'+FormInputSelectPopup).css('height',size[1]);
						}else{
							$('#'+FormInputSelectPopup).css('height',EAFormParams.SelectPopupDefaultHeight);
						}
					}else{
						$('#'+FormInputSelectPopup).css('width',EAFormParams.SelectPopupDefaultWidth);
						$('#'+FormInputSelectPopup).css('height',EAFormParams.SelectPopupDefaultHeight);
					}
					
					PopupDefaultHeight = parseInt(EAFormParams.SelectPopupDefaultHeight);
					if( $('#'+FormInputSelectPopup+'2').height() > PopupDefaultHeight ) {
						
						PopupCurentHeight = $('#'+FormInputSelectPopup+'2').height() - PopupDefaultHeight;
						PopupCurentHeight = $('#'+FormInputSelectPopup+'_box').height() - PopupCurentHeight;
						
						$('#'+FormInputSelectPopup+'_box').height( PopupCurentHeight );
						
					}
					
					$('#'+FormInputSelectPopup).hide();
					
					$(this).before(EAFormParams.SelectBefore);
					$(this).after(EAFormParams.SelectAfter);

					$('#'+FormNewItemID+' div:first').after('<div class="'+ModifyFormTemplate+'_EAFormInputSelectBtn">'+EAFormParams.SelectBtn+'</div>');
					
					$(this).wrap('<div id="'+FormInputSelectText+'_block" class="'+ModifyFormTemplate+'_EAFormInputSelectBlock"></div>');
					
					for(x=0;x < EAFormParams.SelectWrap.length;++x){
						$(this).wrap(EAFormParams.SelectWrap[x]);
					}
					
					$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputSelectBox"></div>');
					
					$(this).before('<input readonly type="text" id="'+FormInputSelectText+'_text" class="'+ModifyFormTemplate+'_EAFormInputSelectText">');
					
					x = 0;
					$(this).children('option').each( function() {
						if( $(this).attr('selected') == true ) {
							c = 0;
							$('#'+FormNewItemID).find('#'+FormInputSelectPopup).find('li').each( function() {
								if( c == x ) {
									$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputSelectPopupBox_Selected');
									$(this).attr('selected',true);
									$(this).attr('default',true);
								}
								c = c+1;
							});
							$('#'+FormNewItemID).find('#'+FormInputSelectText+'_text').val( $(this).html() );
						}
						
						x = x+1;
					});
					
					$(this).change( function() {
						if( EAFunction.FormResetValid == 'true' ) {
							setTimeout( function(){
								$('#'+FormNewItemID).find('#'+FormInputSelectPopup).find('li').each( function() {
									if( $(this).attr('default') == 'true' ) {
										$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputSelectPopupBox_Selected');
										$(this).attr('selected',true);
										$('#'+FormNewItemID).find('#'+FormInputSelectText+'_text').val( $(this).attr('text') );
									}else{
										$(this).removeAttr('selected');
										$(this).attr('className','');
										$(this).attr('class', ModifyFormTemplate+'_EAFormInputSelectPopupBox');
									}
								});
							},100);
							EAFunction.FormResetValid = '';
						}
					});
					
					$('#'+FormNewItemID).live('mouseover', function(){
						if( $(this).find('select').attr('EAFOCUS') == undefined && $('#'+FormInputSelectPopup).css('display') == 'none' ) {
							$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputSelect_Hover');
						}
						$(this).attr('EAOVER',true);
					});
					$('#'+FormNewItemID).live('mouseout', function(){
						if( $(this).find('select').attr('EAFOCUS') == undefined && $('#'+FormInputSelectPopup).css('display') == 'none' ) {
							$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputSelect');
						}
						$(this).removeAttr('EAOVER');
					});
					$('#'+FormNewItemID).find('select').live('focus', function(){
						$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputSelect_Selected');
						$(this).attr('EAFOCUS',true);
					});
					$('#'+FormNewItemID).find('select').live('blur', function(){
						$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputSelect');
						$(this).removeAttr('EAFOCUS');
					});
					
					$('#'+FormInputSelectText+'_text').live('mouseover', function() {
						$(this).css('cursor','pointer');
					});
					$('#'+FormInputSelectText+'_text').live('mouseout', function() {
						$(this).css('cursor','default');
					});
					$('#'+FormInputSelectText+'_text').click( function() {
						if( $('#'+FormInputSelectPopup).css('display') == 'none' ) {
							$('#'+FormInputSelectPopup).show();
						}else{
							$('#'+FormInputSelectPopup).hide();
						}
					});
					
					$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputSelectBtn').live('mouseover', function() {
						$(this).css('cursor','pointer');
					});
					$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputSelectBtn').live('mouseout', function() {
						$(this).css('cursor','default');
					});
					$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputSelectBtn').click( function() {
						if( $('#'+FormInputSelectPopup).css('display') == 'none' ) {
							$('#'+FormInputSelectPopup).show();
						}else{
							$('#'+FormInputSelectPopup).hide();
						}
					});
					$(document).click(function(e){
						if ($(e.target).parents().filter('#'+FormNewItemID+':visible').length != 1) {
							$('#'+FormInputSelectPopup).hide();
							if( $('#'+FormNewItemID).attr('EAOVER') != true ){
								$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputSelect');
							}
						}
					});
					
					ItemFunction = ItemParam[8].split(';');
					for(f=0;f<ItemFunction.length;++f) { 
						if( ItemFunction[f] == 'ValidModify' && SystemFunction != 'ClearValidModify' && EAFunction.ValidModifyInterval != 'true' ) {
							EAFunction.ValidModifyInterval = 'true';
							EAFunction.ValidModify(ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, 'ClearValidModify');
						}
						if( ItemFunction[f] == 'ValidSize' ) {
							EAFunction.ValidSize('SELECT',$('#'+FormInputSelectPopup+'_box'),$('#'+FormInputSelectPopup),'',PopupDefaultHeight);
						}
						if( ItemFunction[f] == 'ValidEmpty' ) {
							$(this).attr('ValidEmpty',true);
							if( EAFunction.ValidEmptyValid != 'true' ) {
								EAFunction.ValidEmptyValid = 'true';
								var FormValid = $(this).parents().map( function () {
									if(this.tagName == 'FORM') {
										$(this).find('input[type="submit"]').click( function() {
											return EAFunction.ValidEmpty($(this), EAFormParams.ValidEmptyErrorMessage);
										});
									}
								}).get();
							}
						}
					}
					
				}else{
					
					$(this).wrap('<div id="'+FormNewItemID+'" class="'+ModifyFormTemplate+'_EAFormInputSelectMultiple"></div>');
					$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputSelectMultiple2"></div>');
					
					if( ItemParam[6] == 'small' ) {
						$('#'+FormNewItemID).css('width',EAFormParams.SelectMultipleSmallDefaultWidth);
						$('#'+FormNewItemID).css('height',EAFormParams.SelectMultipleSmallDefaultHeight);
					}else if( ItemParam[6] == 'big' ) {
						$('#'+FormNewItemID).css('width',EAFormParams.SelectMultipleBigDefaultWidth);
						$('#'+FormNewItemID).css('height',EAFormParams.SelectMultipleBigDefaultHeight);
					}else if( ItemParam[6] == 'custom' && ItemParam[7] != '' ) {
						size = ItemParam[7].split(';');
						if( size[0] != '' ) {
							if( size[0] != '0' ) {
								$('#'+FormNewItemID).css('width',size[0]);
							}
						}else{
							$('#'+FormNewItemID).css('width',EAFormParams.SelectMultipleDefaultWidth);
						}
						if( size[1] != '' && size[1] != '0' ) {
							$('#'+FormNewItemID).css('height',size[1]);
						}else{
							$('#'+FormNewItemID).css('height',EAFormParams.SelectMultipleDefaultHeight);
						}
					}else{
						$('#'+FormNewItemID).css('width',EAFormParams.SelectMultipleDefaultWidth);
						$('#'+FormNewItemID).css('height',EAFormParams.SelectMultipleDefaultHeight);
					}
					
					if( ItemParam[4] == 'left' ) {
						$('#'+FormNewItemID).css('float','left');
					}else if( ItemParam[4] == 'right' ){
						$('#'+FormNewItemID).css('float','right');
					}
					
					if( ItemParam[5] == 'Y' ) {
						$('#'+FormNewItemID).after('<div class="clear"></div>');
					}
					
					$(this).before(EAFormParams.SelectMultipleBefore);
					$(this).after(EAFormParams.SelectMultipleAfter);
					
					$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputSelectMultipleBlock"></div>');
					
					for(x=0;x < EAFormParams.SelectMultipleWrap.length;++x){
						$(this).wrap(EAFormParams.SelectMultipleWrap[x]);
					}
					
					$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputSelectMultipleBox"></div>');
					
					var option = '';
					$(this).children('option').each( function(){
						option += '<li value="'+$(this).val()+'" text="'+$(this).html()+'">';
						option += EAFormParams.SelectMultipleListItemBefore;
						if( jQuery.trim( $(this).html() ) != '' ) {
							option += $(this).html();
						}else{
							option += '&nbsp;';
						}
						option += EAFormParams.SelectMultipleListItemAfter;
						option += '</li>';
					});
					$(this).before('<ul>'+option+'</ul>');
					
					$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputSelectMultipleBox').find('li').each( function() {
						$(this).click( function(){
							if( $(this).attr('selected') == undefined ) {
								$(this).attr('class', ModifyFormTemplate+'_EAFormInputSelectMultipleBox_Selected');
								$(this).attr('selected',true);
								$(this).parent().parent().find('option[value='+$(this).attr('value')+']').attr('selected',true);
							}else{
								$(this).attr('class', ModifyFormTemplate+'_EAFormInputSelectMultipleBox_Hover');
								$(this).removeAttr('selected');
								$(this).parent().parent().find('option[value='+$(this).attr('value')+']').removeAttr('selected');
							}
						});
						$(this).mouseover( function(e){
							if( $(this).attr('selected') == undefined ) {
								$(this).attr('class', ModifyFormTemplate+'_EAFormInputSelectMultipleBox_Hover');
							}
						});
						$(this).mouseout( function(e){
							if( $(this).attr('selected') == undefined && ( e.pageX >= Number($(this).position().left) + 5 && e.pageX <= Number($(this).position().left)+Number($(this).width()) - 5 && e.pageY >= $(this).position().top && e.pageY <= Number($(this).position().top)+Number($(this).height()) ) != true ) {
								$(this).attr('class', '');
							}
						});
					});
					
					ItemHeight = $('#'+FormNewItemID+' div').height();
					if( ItemHeight < $('#'+FormNewItemID).height() ) {
						$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputSelectMultipleBox').height( $('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputSelectMultipleBox').height() + $('#'+FormNewItemID).height() - ItemHeight );
					}
					
					x = 0;
					$(this).children('option').each( function() {
						if( $(this).attr('selected') == true ) {
							c = 0;
							$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputSelectMultipleBox').find('li').each( function() {
								if( c == x ) {
									$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputSelectMultipleBox_Selected');
									$(this).attr('selected',true);
									$(this).attr('default',true);
								}
								c = c+1;
							});
						}
						x = x+1;
					});
					
					$(this).change( function() {
						if( EAFunction.FormResetValid == 'true' ) {
							setTimeout( function(){
								$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputSelectMultipleBox').find('li').each( function() {
									if( $(this).attr('default') == 'true' ) {
										$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputSelectMultipleBox_Selected');
										$(this).attr('selected',true);
									}else{
										$(this).removeAttr('selected');
										$(this).attr('className','');
										$(this).attr('class', ModifyFormTemplate+'_EAFormInputSelectMultipleBox');
									}
								});
							},100);
							EAFunction.FormResetValid = '';
						}
					});
					
					$('#'+FormNewItemID).live('mouseover', function(){
						if( $(this).find('select').attr('EAFOCUS') == undefined ) {
							$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputSelectMultiple_Hover');
						}
						$(this).attr('EAOVER',true);
					});
					$('#'+FormNewItemID).live('mouseout', function(e){
						if( $(this).find('select').attr('EAFOCUS') == undefined && ( e.pageX >= $(this).position().left && e.pageX <= Number($(this).position().left)+Number($(this).width()) && e.pageY >= $(this).position().top && e.pageY <= Number($(this).position().top)+Number($(this).height()) ) != true ) {
							$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputSelectMultiple');
						}
						$(this).removeAttr('EAOVER');
					});
					$('#'+FormNewItemID).find('select').live('focus', function(){
						$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputSelectMultiple_Selected');
						$(this).attr('EAFOCUS',true);
					});
					$('#'+FormNewItemID).find('select').live('blur', function(){
						$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputSelectMultiple');
						$(this).removeAttr('EAFOCUS');
					});
					
					ItemFunction = ItemParam[8].split(';');
					for(f=0;f<ItemFunction.length;++f) { 
						if( ItemFunction[f] == 'ValidModify' && SystemFunction != 'ClearValidModify' && EAFunction.ValidModifyInterval != 'true' ) {
							EAFunction.ValidModifyInterval = 'true';
							EAFunction.ValidModify(ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, 'ClearValidModify');
						}
						if( ItemFunction[f] == 'ValidSize' ) {
							EAFunction.ValidSize('SELECT_MULTIPLE',$('#'+FormNewItemID).find('div.'+ModifyFormTemplate+'_EAFormInputSelectMultipleBox'),$('#'+FormNewItemID),'','');
						}
						if( ItemFunction[f] == 'ValidEmpty' ) {
							$(this).attr('ValidEmpty',true);
							if( EAFunction.ValidEmptyValid != 'true' ) {
								EAFunction.ValidEmptyValid = 'true';
								var FormValid = $(this).parents().map( function () {
									if(this.tagName == 'FORM') {
										$(this).find('input[type="submit"]').click( function() {
											return EAFunction.ValidEmpty($(this), EAFormParams.ValidEmptyErrorMessage);
										});
									}
								}).get();
							}
						}
					}
					
				}
				++all;
			}
            ++i;
		});

		var i = 0;
		$('input[type="checkbox"]').each( function () {
			var MoreValid = '';
			var ModifyFormParamMoreValue = ModifyFormParamMore.split('||');
			for(k=0;k<ModifyFormParamMoreValue.length;++k) {
				var ModifyFormParamMoreValueData = ModifyFormParamMoreValue[k];
				if($(this).attr('name') != '') {
					var TagValueThis = 'CHECKBOX#name#'+$(this).attr('name');
				}else{
					var TagValueThis = 'CHECKBOX#id#'+$(this).attr('id');
				}
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData.split('#');
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData[0]+'#'+ModifyFormParamMoreValueData[1]+'#'+ModifyFormParamMoreValueData[2];
				if(TagValueThis != '' && TagValueThis == ModifyFormParamMoreValueData){
					var MoreValid = 'true';
					MoreValidNum = k;
				}
			}
			var FormValid = $(this).parents().map( function () {
				if(this.tagName == ModifyFormTag && $(this).attr(ModifyFormParam) == ModifyFormValue) { 
					return "true";
				}
			}).get();
			if(FormValid == 'true' && MoreValid == 'true' && $(this).attr('EAMODIFY') == undefined) {
				$(this).attr('EAMODIFY',true);
				$(this).attr('class','');
				
				var FormNewItemID = 'EAFormInputCheckbox_'+ModifyFormTag+'_'+ModifyFormParam+'_'+ModifyFormValue+'_'+$(this).attr('id')+'_'+i;
				var FormNewItemID = FormNewItemID.replace('[','');
				var FormNewItemID = FormNewItemID.replace('[',''); 
				var ItemParam = ModifyFormParamMoreValue[MoreValidNum].split('#');
				
				$(this).hide();
				$('label[for="'+$(this).attr('id')+'"]').hide();
				
				var EAFormInputCheckboxText = $('label[for="'+$(this).attr('id')+'"]').html();
				if(EAFormInputCheckboxText == null || EAFormInputCheckboxText == undefined) {
					var EAFormInputCheckboxText = '';
				}
				
				$('label[for="'+$(this).attr('id')+'"] + br').remove();
				
				$(this).wrap('<div id="'+FormNewItemID+'" class="'+ModifyFormTemplate+'_EAFormInputCheckbox"></div>');
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputCheckbox2"></div>');
					
				if( ItemParam[6] == 'small' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.CheckboxSmallDefaultWidth);
					$('#'+FormNewItemID).css('height',EAFormParams.CheckboxSmallDefaultHeight);
				}else if( ItemParam[6] == 'big' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.CheckboxBigDefaultWidth);
					$('#'+FormNewItemID).css('height',EAFormParams.CheckboxBigDefaultHeight);
				}else if( ItemParam[6] == 'custom' && ItemParam[7] != '' ) {
					if( ItemParam[7] != '0' ) {
						$('#'+FormNewItemID).css('width',ItemParam[7]);
					}
				}else{
					$('#'+FormNewItemID).css('width',EAFormParams.CheckboxDefaultWidth);
				}
				
				if( ItemParam[4] == 'left' ) {
					$('#'+FormNewItemID).css('float','left');
				}else if( ItemParam[4] == 'right' ){
					$('#'+FormNewItemID).css('float','right');
				}
				
				if( ItemParam[5] == 'Y' ) {
					$('#'+FormNewItemID).after('<div class="clear"></div>');
				}
				
				$(this).before(EAFormParams.CheckboxBefore);
				$(this).after(EAFormParams.CheckboxAfter);
					
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputCheckboxBlock"></div>');
					
				for(x=0;x < EAFormParams.CheckboxWrap.length;++x){
					$(this).wrap(EAFormParams.CheckboxWrap[x]);
				}
					
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputCheckboxBox"></div>');
				
				$(this).before('<div class="'+ModifyFormTemplate+'_EAFormInputCheckboxBtn"></div>');
				$(this).before('<div class="'+ModifyFormTemplate+'_EAFormInputCheckboxText">'+EAFormInputCheckboxText+'</div>');
				
				$('#'+FormNewItemID).live('mouseover', function(){
					if( $(this).find('input').attr('EACHECKED') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputCheckbox_Hover');
					}
					$(this).find('input').attr('EAOVER',true);
					$(this).css('cursor','pointer');
				});
				$('#'+FormNewItemID).live('mouseout', function(){
					if( $(this).find('input').attr('EACHECKED') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputCheckbox');
					}
					$(this).find('input').removeAttr('EAOVER');
					$(this).css('cursor','default');
				});
				
				if( $(this).attr('checked') == true ) {
					$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputCheckbox_Selected');
					$(this).attr('EACHECKED',true);
					$(this).attr('default',true);
				}else{
					$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputCheckbox');
					$(this).removeAttr('EACHECKED');
				}
				
				$('#'+FormNewItemID).click( function () {
					if( $(this).find('input').attr('EAOVER') != undefined ) {
						if( $('#'+FormNewItemID).find('input').attr('checked') == true ) {
							$('#'+FormNewItemID).find('input').attr('checked',false);
						}else{
							$('#'+FormNewItemID).find('input').attr('checked',true);
						}
					}
					$('#'+FormNewItemID).find('input').change();
				});

				$('#'+FormNewItemID).find('input').change( function() {
					
					if( EAFunction.FormResetValid == 'true' ) {
						
						if( $(this).attr('default') == 'true' ) {
							$(this).attr('EACHECKED',true);
							setTimeout( function(){
								$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputCheckbox_Selected');
							},100);
						}else{
							$(this).removeAttr('EACHECKED');
							setTimeout( function(){
								$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputCheckbox');
							},100);
						}
						EAFunction.FormResetValid = '';
						
					}else{
					
						if( $(this).attr('checked') == true ) {
							$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputCheckbox_Selected');
							$(this).attr('EACHECKED',true);
						}else{
							if( $(this).attr('EAOVER') == undefined ) {
								$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputCheckbox');
							}else{
								$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputCheckbox_Hover');
							}
							$(this).removeAttr('EACHECKED');
						}
					
					}
					
				});
				
				ItemFunction = ItemParam[8].split(';');
				for(f=0;f<ItemFunction.length;++f) { 
					if( ItemFunction[f] == 'ValidModify' && SystemFunction != 'ClearValidModify' && EAFunction.ValidModifyInterval != 'true' ) {
						EAFunction.ValidModifyInterval = 'true';
						EAFunction.ValidModify(ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, 'ClearValidModify');
					}
					if( ItemFunction[f] == 'ValidEmpty' ) {
						$(this).attr('ValidEmpty',true);
						if( EAFunction.ValidEmptyValid != 'true' ) {
							EAFunction.ValidEmptyValid = 'true';
							var FormValid = $(this).parents().map( function () {
								if(this.tagName == 'FORM') {
									$(this).find('input[type="submit"]').click( function() {
										return EAFunction.ValidEmpty($(this), EAFormParams.ValidEmptyErrorMessage);
									});
								}
							}).get();
						}
					}
				}
				
				++all;
			}
			++i;
		});

		var i = 0;
		$('input[type="radio"]').each( function () {
			var MoreValid = '';
			var ModifyFormParamMoreValue = ModifyFormParamMore.split('||');
			for(k=0;k<ModifyFormParamMoreValue.length;++k) {
				var ModifyFormParamMoreValueData = ModifyFormParamMoreValue[k];
				if($(this).attr('name') != '') {
					var TagValueThis = 'RADIO#name#'+$(this).attr('name');
				}else{
					var TagValueThis = 'RADIO#id#'+$(this).attr('id');
				}
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData.split('#');
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData[0]+'#'+ModifyFormParamMoreValueData[1]+'#'+ModifyFormParamMoreValueData[2];
				if(TagValueThis != '' && TagValueThis == ModifyFormParamMoreValueData) {
					var MoreValid = 'true';
					MoreValidNum = k;
				}
			}
			var FormValid = $(this).parents().map( function () {
				if(this.tagName == ModifyFormTag && $(this).attr(ModifyFormParam) == ModifyFormValue) { 
					return "true";
				}
			}).get();
			if(FormValid == 'true' && MoreValid == 'true' && $(this).attr('EAMODIFY') == undefined) {
				$(this).attr('EAMODIFY',true);
				$(this).attr('class','');
				
				var FormNewItemID = 'EAFormInputRadio_'+ModifyFormTag+'_'+ModifyFormParam+'_'+ModifyFormValue+'_'+$(this).attr('id')+'_'+i;
				var FormNewItemID = FormNewItemID.replace('[','');
				var FormNewItemID = FormNewItemID.replace('[','');
				
				var EAFormInputRadioName = $(this).attr('name');
				var EAFormInputRadioId = $(this).attr('id');
				
				var ItemParam = ModifyFormParamMoreValue[MoreValidNum].split('#');
				
				$(this).hide();
				$('label[for="'+$(this).attr('id')+'"]').hide();
				
				var EAFormInputRadioText = $('label[for="'+$(this).attr('id')+'"]').html();
				if(EAFormInputRadioText == null || EAFormInputRadioText == undefined) {
					var EAFormInputRadioText = '';
				}
				
				$('label[for="'+$(this).attr('id')+'"] + br').remove();
				
				$(this).wrap('<div id="'+FormNewItemID+'" class="'+ModifyFormTemplate+'_EAFormInputRadio"></div>');
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputRadio2"></div>');
					
				if( ItemParam[6] == 'small' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.RadioSmallDefaultWidth);
					$('#'+FormNewItemID).css('height',EAFormParams.RadioSmallDefaultHeight);
				}else if( ItemParam[6] == 'big' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.RadioBigDefaultWidth);
					$('#'+FormNewItemID).css('height',EAFormParams.RadioBigDefaultHeight);
				}else if( ItemParam[6] == 'custom' && ItemParam[7] != '' ) {
					if( ItemParam[7] != '0' ) {
						$('#'+FormNewItemID).css('width',ItemParam[7]);
					}
				}else{
					$('#'+FormNewItemID).css('width',EAFormParams.RadioDefaultWidth);
				}
				
				if( ItemParam[4] == 'left' ) {
					$('#'+FormNewItemID).css('float','left');
				}else if( ItemParam[4] == 'right' ){
					$('#'+FormNewItemID).css('float','right');
				}
				
				if( ItemParam[5] == 'Y' ) {
					$('#'+FormNewItemID).after('<div class="clear"></div>');
				}
				
				$(this).before(EAFormParams.RadioBefore);
				$(this).after(EAFormParams.RadioAfter);
					
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputRadioBlock"></div>');
					
				for(x=0;x < EAFormParams.RadioWrap.length;++x){
					$(this).wrap(EAFormParams.RadioWrap[x]);
				}
					
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputRadioBox"></div>');
				
				$(this).before('<div class="'+ModifyFormTemplate+'_EAFormInputRadioBtn"></div>');
				$(this).before('<div class="'+ModifyFormTemplate+'_EAFormInputRadioText">'+EAFormInputRadioText+'</div>');
				
				$('#'+FormNewItemID).live('mouseover', function(){
					if( $(this).find('input').attr('EACHECKED') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputRadio_Hover');
					}
					$(this).attr('EAOVER',true);
					$(this).css('cursor','pointer');
				});
				$('#'+FormNewItemID).live('mouseout', function(){
					if( $(this).find('input').attr('EACHECKED') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputRadio');
					}
					$(this).removeAttr('EAOVER');
					$(this).css('cursor','default');
				});
				
				if( $(this).attr('checked') == true ) {
					$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputRadio_Selected');
					$(this).attr('EACHECKED',true);
					$(this).attr('default',true);
				}else{
					$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputRadio');
					$(this).removeAttr('EACHECKED');
				}
				
				$('#'+FormNewItemID).live('click', function () {
					$('#'+FormNewItemID).find('input').attr('checked',true);
					$('#'+FormNewItemID).find('input').change();
				});
				
				$('#'+FormNewItemID).find('input').change( function() {
					
					$(this).parents().find('div.'+ModifyFormTemplate+'_EAFormInputRadio_Selected').each( function(){
						if( $(this).find('input').attr('name') == EAFormInputRadioName ) {
							$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputRadio');
						}
					});
					
					if( EAFunction.FormResetValid == 'true' ) {
						
						if( $(this).attr('default') == 'true' ) {
							$(this).attr('EACHECKED',true);
							setTimeout( function(){
								$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputRadio_Selected');
							},100);
						}
						EAFunction.FormResetValid = '';
						
					}else{
					
						if( $(this).attr('checked') == true ) {
							$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputRadio_Selected');
							$(this).attr('EACHECKED',true);
						}else{
							
							if( $(this).find('input').attr('EAOVER') == undefined ) {
								$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputRadio');
							}else{
								$('#'+FormNewItemID).attr('className',''+ModifyFormTemplate+'_EAFormInputRadio_Hover');
							}
							$(this).removeAttr('EACHECKED');
						}
					
					}
					
				});
				
				ItemFunction = ItemParam[8].split(';');
				for(f=0;f<ItemFunction.length;++f) { 
					if( ItemFunction[f] == 'ValidModify' && SystemFunction != 'ClearValidModify' && EAFunction.ValidModifyInterval != 'true' ) {
						EAFunction.ValidModifyInterval = 'true';
						EAFunction.ValidModify(ModifyFormTemplate, ModifyFormTag, ModifyFormParam, ModifyFormValue, ModifyFormParamMore, 'ClearValidModify');
					}
					if( ItemFunction[f] == 'ValidEmpty' ) {
						$(this).attr('ValidEmpty',true);
						if( EAFunction.ValidEmptyValid != 'true' ) {
							EAFunction.ValidEmptyValid = 'true';
							var FormValid = $(this).parents().map( function () {
								if(this.tagName == 'FORM') {
									$(this).find('input[type="submit"]').click( function() {
										return EAFunction.ValidEmpty($(this), EAFormParams.ValidEmptyErrorMessage);
									});
								}
							}).get();
						}
					}
				}
				
				++all;
			}
			++i;
		});

		var i = 0;
		$('button,input[type="button"],input[type="submit"],input[type="reset"]').each( function () {
			var MoreValid = '';
			var ModifyFormParamMoreValue = ModifyFormParamMore.split('||');
			for(k=0;k<ModifyFormParamMoreValue.length;++k) {
				var ModifyFormParamMoreValueData = ModifyFormParamMoreValue[k];
				if($(this).attr('name') != '') {
					var TagValueThis = 'BUTTON#name#'+$(this).attr('name');
				}else{
					var TagValueThis = 'BUTTON#id#'+$(this).attr('id');
				}
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData.split('#');
				ModifyFormParamMoreValueData = ModifyFormParamMoreValueData[0]+'#'+ModifyFormParamMoreValueData[1]+'#'+ModifyFormParamMoreValueData[2];
				if(TagValueThis != '' && TagValueThis == ModifyFormParamMoreValueData){
					var MoreValid = 'true';
					MoreValidNum = k;
				}
			}
			var FormValid = $(this).parents().map( function () {
				if(this.tagName == ModifyFormTag && $(this).attr(ModifyFormParam) == ModifyFormValue) { 
					return "true";
				}
			}).get();
			if(FormValid == 'true' && MoreValid == 'true' && $(this).attr('EAMODIFY') == undefined) {
				$(this).attr('EAMODIFY',true);
				$(this).attr('class','');
				
				var FormNewItemID = 'EAFormInputButton_'+ModifyFormTag+'_'+ModifyFormParam+'_'+ModifyFormValue+'_'+$(this).attr('id')+'_'+i;
				var FormNewItemID = FormNewItemID.replace('[','');
				var FormNewItemID = FormNewItemID.replace('[','');
				
				var ItemParam = ModifyFormParamMoreValue[MoreValidNum].split('#');
				
				$(this).hide();
				
				$(this).wrap('<div id="'+FormNewItemID+'" class="'+ModifyFormTemplate+'_EAFormInputButton"></div>');
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputButton2"></div>');
					
				if( ItemParam[6] == 'small' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.ButtonSmallDefaultWidth);
					$('#'+FormNewItemID).css('height',EAFormParams.ButtonSmallDefaultHeight);
				}else if( ItemParam[6] == 'big' ) {
					$('#'+FormNewItemID).css('width',EAFormParams.ButtonBigDefaultWidth);
					$('#'+FormNewItemID).css('height',EAFormParams.ButtonBigDefaultHeight);
				}else if( ItemParam[6] == 'custom' && ItemParam[7] != '' ) {
					if( ItemParam[7] != '0' ) {
						$('#'+FormNewItemID).css('width',ItemParam[7]);
					}
				}else{
					$('#'+FormNewItemID).css('width',EAFormParams.ButtonDefaultWidth);
				}
				
				if( ItemParam[4] == 'left' ) {
					$('#'+FormNewItemID).css('float','left');
				}else if( ItemParam[4] == 'right' ){
					$('#'+FormNewItemID).css('float','right');
				}
				
				if( ItemParam[5] == 'Y' ) {
					$('#'+FormNewItemID).after('<div class="clear"></div>');
				}
				
				$(this).before(EAFormParams.ButtonBefore);
				$(this).after(EAFormParams.ButtonAfter);
					
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputButtonBlock"></div>');
					
				for(x=0;x < EAFormParams.ButtonWrap.length;++x){
					$(this).wrap(EAFormParams.ButtonWrap[x]);
				}
					
				$(this).wrap('<div class="'+ModifyFormTemplate+'_EAFormInputButtonBox">'+$(this).val()+'</div>');
				
				$('#'+FormNewItemID).live('mouseover', function(){
					$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputButton_Hover');
					$(this).attr('EAOVER',true);
					$(this).css('cursor','pointer');
				});
				$('#'+FormNewItemID).live('mouseout', function(){
					$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputButton');
					$(this).removeAttr('EAOVER');
					$(this).css('cursor','default');
				});
				$('#'+FormNewItemID).live('mousedown', function(){
					$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputButton_Selected');
				});
				$('#'+FormNewItemID).live('mouseup', function(){
					if( $(this).attr('EAOVER') == undefined ) {
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputButton');
					}else{
						$(this).attr('className',''+ModifyFormTemplate+'_EAFormInputButton_Hover');
					}
					
				});
				
				$('#'+FormNewItemID).find('input,button').appendTo('#'+FormNewItemID);
				
				$('#'+FormNewItemID+' div').live('click', function(){
					$('#'+FormNewItemID).find('input,button').click();
				});
				
				if( $(this).attr('type') == 'reset' ) {
					$(this).click( function() {
						EAFunction.FormReset( $(this) );
					});
				}
				
				++all;
			}
			++i;
		});
		
		if( SystemFunction == 'ClearValidModify' && all > 0 ) {
			//setTimeout( function () { EAFunction.ValidModifyInterval = 'false'; }, 1000);
		}

	}

}