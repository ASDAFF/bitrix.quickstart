var kdaIEModuleName = 'kda.exportexcel';
var kdaIEModuleFilePrefix = 'kda_export_excel';
var kdaIEModuleAddPath = '';
var kdaIEModuleUMClass = 'kda-ee-updates-message';
var EList = {
	Init: function()
	{
		if(!document.getElementById('kda-ee-sheet-list')) return;
		
		/*Bug fix with excess jquery*/
		var anySelect = $('select:eq(0)');
		if(typeof anySelect.chosen != 'function')
		{
			var jQuerySrc = $('script[src^="/bitrix/js/main/jquery/"]').attr('src');
			if(jQuerySrc)
			{
				$.getScript(jQuerySrc, function(){
					$.getScript('/bitrix/js/'+kdaIEModuleName+'/chosen/chosen.jquery.min.js');
				});
			}
		}
		/*/Bug fix with excess jquery*/
		
		$('#kda-ee-sheet-list .kda-ee-sheet').each(function(){
			EList.UpdateSheet($(this));
		});
		$(window).bind('resize', function(){
			EList.SetWidthList();
		});
		BX.addCustomEvent("onAdminMenuResize", function(json){
			$(window).trigger('resize');
		});
		//$(window).trigger('resize');
		
		$('.find_form_inner input[type="checkbox"].adm-designed-checkbox').each(function(){
			if(this.parentNode.tagName == 'LABEL')
			{
				var parent = $(this.parentNode);
				$(this).remove().insertAfter(parent);
			}
		});
	},
	
	InitLines: function(list)
	{
		var obj = this;
		
		var sandwichSelector = '.kda-ee-tbl .sandwich';
		if(typeof list!='undefined') sandwichSelector = '.kda-ee-tbl[data-list-index='+list+'] .sandwich';
		
		var titlesSelector = '.kda-ee-tbl tr.kda-ee-tbl-titles';
		if(typeof list!='undefined') titlesSelector = '.kda-ee-tbl[data-list-index='+list+'] tr.kda-ee-tbl-titles';
		
		$(sandwichSelector).unbind('click').bind('click', function(){
			obj.sandwichOpened = this;
			var key = $(this).attr('data-key');
			var type = $(this).attr('data-type');
			var listIndex = $(this).closest('.kda-ee-sheet').attr('data-sheet-index');
			
			var menuItems = [];
			if(key == 'COLUMN_TITLES')
			{
				menuItems.push({
					TEXT: BX.message("KDA_EE_ADD_ALL_ELEMENT_FIELDS"),
					ONCLICK: "EList.AddAllFields('"+key+"', '"+listIndex+"', 'IE_')"
				});
				menuItems.push({
					TEXT: BX.message("KDA_EE_ADD_ALL_PROPERTIES"),
					ONCLICK: "EList.AddAllFields('"+key+"', '"+listIndex+"', 'IP_PROP')"
				});
				menuItems.push({
					TEXT: BX.message("KDA_EE_ADD_ALL_ELEMENT_FIELDS_SEO"),
					ONCLICK: "EList.AddAllFields('"+key+"', '"+listIndex+"', 'IPROP_TEMP_')"
				});
				menuItems.push({
					TEXT: BX.message("KDA_EE_ADD_ALL_ELEMENT_FIELDS_CATALOG"),
					ONCLICK: "EList.AddAllFields('"+key+"', '"+listIndex+"', 'ICAT_')"
				});
				menuItems.push({
					TEXT: BX.message("KDA_EE_ADD_LINE_ABOVE"),
					ONCLICK: "EList.AddNewRow('TEXT_ROWS_TOP', 0)"
				});
				menuItems.push({
					TEXT: BX.message("KDA_EE_ADD_LINE_UNDER"),
					ONCLICK: "EList.AddNewRow('TEXT_ROWS_TOP2', 1)"
				});
			}
			
			if(key.indexOf('TEXT_ROWS_TOP_') == 0 || key.indexOf('TEXT_ROWS_TOP2_') == 0)
			{
				var textKey = key.replace(/_\d+$/, '');
				menuItems.push({
					TEXT: BX.message("KDA_EE_ADD_LINE_ABOVE"),
					ONCLICK: "EList.AddNewRow('"+textKey+"', 0)"
				});
				menuItems.push({
					TEXT: BX.message("KDA_EE_ADD_LINE_UNDER"),
					ONCLICK: "EList.AddNewRow('"+textKey+"', 1)"
				});
				menuItems.push({
					TEXT: BX.message("KDA_EE_REMOVE_LINE"),
					ONCLICK: "EList.RemoveRow('"+key+"', '"+listIndex+"')"
				});
				menuItems.push({
					TEXT: BX.message("KDA_EE_INSERT_PICTURE"),
					ONCLICK: "EList.InsertPicture('"+key+"', '"+listIndex+"')"
				});
			}
			
			menuItems.push({
				TEXT: BX.message("KDA_EE_DISPLAY_SETTINGS"),
				ONCLICK: "EList.SetLineDisplaySetting('"+key+"', '"+listIndex+"', '"+type+"')"
			});
			menuItems.push({
				TEXT: BX.message("KDA_EE_DISPLAY_SETTINGS_RESET"),
				ONCLICK: "EList.ResetLineDisplaySetting('"+key+"', '"+listIndex+"')"
			});
			BX.adminShowMenu(this, menuItems, {active_class: "bx-adm-scale-menu-butt-active"});
		});
		
		/*$(titlesSelector).unbind('mouseover').bind('mouseover', function(){
			var tbl = $(this).closest('table')[0];
			var top1 = this.offsetTop + tbl.offsetTop;
			var top2 = top1 + this.offsetHeight;
			var wrap = $(this).closest('.kda-ee-sheet');
			wrap.append('<div class="kda-ee-add-row-btn">+</div>');
			$('.kda-ee-add-row-btn', wrap).css({top: top1-26});
		});
		$(titlesSelector).unbind('mouseout').bind('mouseout', function(){
			var wrap = $(this).closest('.kda-ee-sheet');
			wrap.find('.kda-ee-add-row-btn').remove();
		});*/
	},
	
	AddNewRow: function(textKey, under)
	{
		var tr = $(this.sandwichOpened).closest('tr');
		var wrap = tr.closest('.kda-ee-sheet');
		var listIndex = wrap.attr('data-sheet-index');
		var tds = $('>td, >th', tr);
		var tdCount = 0;
		for(var i=0; i<tds.length; i++)
		{
			tdCount += (tds[i].colspan || 1);
		}
		
		var maxKey = 0;
		var inputs = $('[name^="SETTINGS['+textKey+']['+listIndex+']["]', wrap);
		for(var i=0; i<inputs.length; i++)
		{
			var curKey = parseInt(inputs[i].name.replace(/^.*\[(\d+)\]$/, '$1'));
			if(curKey+1 > maxKey) maxKey = curKey+1;
		}
		var newLine = '<tr style="display: none;"><td></td><td colspan="'+(tdCount-1)+'"><textarea class="kda-ee-text-block" name="SETTINGS['+textKey+']['+listIndex+']['+maxKey+']"></textarea></td></tr>';
		if(under) tr.after(newLine);
		else tr.before(newLine);
		EList.UpdateSheet(wrap);
	},
	
	RemoveRow: function(key, listIndex)
	{
		var tr = $(this.sandwichOpened).closest('tr');
		var wrap = tr.closest('.kda-ee-sheet');
		this.ResetLineDisplaySetting(key, listIndex, true);
		tr.remove();
		EList.UpdateSheet(wrap);
	},
	
	InsertPicture: function(key, listIndex)
	{
		var tr = $(this.sandwichOpened).closest('tr');
		var wrap = tr.closest('.kda-ee-sheet');
		var td = $('>td:last', tr);
		var input = $('input[type="file"]', td);
		if(input.length == 0)
		{
			var keywList = key.replace(/_(\d+)$/, '_'+listIndex+'_$1');
			td.append('<input type="file" name="NEW_PICTURE_'+keywList+'" style="position: absolute; left: -99999px;">');
			input = $('input[type="file"]', td);
			input.bind('change', function(){
				EList.UpdateSheet(wrap);
			});
			input[0].click();
		}
	},
	
	SetFieldValues: function(gParent)
	{
		//if(!gParent) gParent = $('.kda-ie-tbl');
		var sheetParent = gParent.closest('.kda-ee-sheet');
		$('.kda-ee-hidden-settings select[name^="FIELDS_LIST["]', sheetParent).each(function(){
			var pSelect = this;
			var parent = $('tr.kda-ee-tbl-titles', gParent);
			var arVals = [];
			var arValParents = [];
			for(var i=0; i<pSelect.options.length; i++)
			{
				arVals[pSelect.options.item(i).value] = pSelect.options.item(i).text;
				arValParents[pSelect.options.item(i).value] = pSelect.options.item(i).parentNode.getAttribute('label');
			}

			$('input[name^="SETTINGS[FIELDS_LIST]"]', parent).each(function(index){
				var input = this;
				var inputShow = $('input[name="'+input.name.replace('SETTINGS[FIELDS_LIST]', 'FIELDS_LIST_SHOW')+'"]', parent)[0];
				var inputShowExport = $('input[name="'+input.name.replace('FIELDS_LIST', 'FIELDS_LIST_NAMES')+'"]', parent)[0];
				inputShow.setAttribute('placeholder', arVals['']);
				
				if(!input.value || !arVals[input.value])
				{
					input.value = '';
					inputShow.value = '';
					if(inputShowExport.value.length==0) inputShowExport.value = '';
					return;
				}
				
				inputShow.value = arVals[input.value];
				inputShow.title = arVals[input.value];
				if(inputShowExport.value.length==0) inputShowExport.value = arVals[input.value];
			});
			
			EList.OnFieldFocus($('input[name^="FIELDS_LIST_SHOW["]', parent));
		});
	},
	
	OnFieldFocus: function(objInput)
	{
		var gobj = this;
		$(objInput).unbind('focus').bind('focus', function(){
			var input = this;
			var arKeys = input.name.substr(input.name.indexOf('[') + 1, input.name.length - input.name.indexOf('[') - 2).split('][');
			
			var parent = $(input).closest('tr');
			var parentTbl = parent.closest('table');
			var sheetParent = parentTbl.closest('.kda-ee-sheet');
			var pSelect = $('.kda-ee-hidden-settings select[name^="FIELDS_LIST["]', sheetParent);
			var inputVal = $('input[name="'+input.name.replace('FIELDS_LIST_SHOW', 'SETTINGS[FIELDS_LIST]')+'"]', parent)[0];
			var inputValExport = $('input[name="'+input.name.replace('FIELDS_LIST_SHOW', 'SETTINGS[FIELDS_LIST_NAMES]')+'"]', parent)[0];
			var select = $(pSelect).clone();
			var options = select[0].options;
			for(var i=0; i<options.length; i++)
			{
				if(inputVal.value==options.item(i).value) options.item(i).selected = true;
			}
			
			var chosenId = 'kda_select_chosen';
			$('#'+chosenId).remove();
			var offset = $(input).offset();
			var div = $('<div></div>');
			div.attr('id', chosenId);
			div.css({
				position: 'absolute',
				left: offset.left,
				top: offset.top,
				width: $(input).width() + 27
			});
			div.append(select);
			$('body').append(div);
			
			//select.insertBefore($(input));
			select.chosen({search_contains: true});
			select.bind('change', function(){
				var option = options.item(select[0].selectedIndex);
				if(option.value)
				{
					if(inputVal.value != option.value)
					{
						input.value = option.text;
						input.title = option.text;
						inputVal.value = option.value;
						inputValExport.value = option.text;
						gobj.SetNewColumnVal(arKeys[1], parentTbl);
					}
				}
				else
				{
					if(inputVal.value != '')
					{
						input.value = '';
						input.title = '';
						inputVal.value = '';
						inputValExport.value = '';
						gobj.SetNewColumnVal(arKeys[1], parentTbl);
					}
				}
				select.chosen('destroy');
				$('#'+chosenId).remove();
			});
			
			$('body').one('click', function(e){
				e.stopPropagation();
				return false;
			});
			var chosenDiv = select.next('.chosen-container')[0];
			$('a:eq(0)', chosenDiv).trigger('mousedown');
			
			var lastClassName = chosenDiv.className;
			var interval = setInterval( function() {   
				   var className = chosenDiv.className;
					if (className !== lastClassName) {
						select.trigger('change');
						lastClassName = className;
						clearInterval(interval);
					}
				},30);
		});
	},
	
	SetNewColumnVal: function(index, tbl)
	{
		tbl.closest('.kda-ee-sheet').each(function(){
			EList.UpdateSheet($(this));
		});
	},
	
	UpdateSheet: function(wrap)
	{
		var scrollLeft = $('.kda-ee-tbl-wrap', wrap).scrollLeft();
		if(scrollLeft)
		{
			var wrapWidth = $('.kda-ee-tbl-wrap', wrap).width();
			var innerWidth = $('.kda-ee-tbl-wrap .kda-ee-tbl', wrap).width();
			if(Math.abs(innerWidth - (wrapWidth + scrollLeft)) < 50)
			{
				scrollLeft += 250;
			}
		}
		
		var index = wrap.attr('data-sheet-index');
		wrap.append('<div class="kda-ee-sheet-preloader"></div>'+
			'<input type="hidden" name="ACTION" value="SHOW_PREVIEW">'+
			'<input type="hidden" name="SHEET_INDEX" value="'+index+'">');
		var form = wrap.closest('form');
		$.ajax({
			url: window.location.href,
			type: 'POST',
			data: (new FormData(form[0])),
			mimeType:"multipart/form-data",
			contentType: false,
			cache: false,
			processData:false,
			success: function(data, textStatus, jqXHR)
			{
				wrap.prev('.find_form_inner').show();
				wrap.html(data);
				var ptable = $('.kda-ee-tbl', wrap);
				EList.SetFieldValues(ptable);
				$('.kda-ee-sheet-preloader', wrap).remove();
				EList.SetWidthList();
				
				$('.kda-ee-tbl-wrap', wrap).bind('scroll', function(){
					$('#kda_select_chosen').remove();
					$(this).prev('.kda-ee-tbl-scroll').scrollLeft($(this).scrollLeft());
				});
				$('.kda-ee-tbl-scroll', wrap).bind('scroll', function(){
					$('#kda_select_chosen').remove();
					$(this).next('.kda-ee-tbl-wrap').scrollLeft($(this).scrollLeft());
				});
				EList.InitLines();
				
				if(scrollLeft)
				{
					setTimeout(function(){
						$('.kda-ee-tbl-wrap', wrap).scrollLeft(scrollLeft);
						$('.kda-ee-tbl-scroll', wrap).scrollLeft(scrollLeft);
					}, 100);
				}
			},
			error: function(data, textStatus, jqXHR)
			{
				
			}
		});
		
		/*
		var post = wrap.closest('form').serialize() + '&ACTION=SHOW_PREVIEW&SHEET_INDEX='+index;
		$.post(window.location.href, post, function(data){
			wrap.html(data);
			var ptable = $('.kda-ee-tbl', wrap);
			EList.SetFieldValues(ptable);
			$('.kda-ee-sheet-preloader', wrap).remove();
			EList.SetWidthList();
			
			$('.kda-ee-tbl-wrap', wrap).bind('scroll', function(){
				$('#kda_select_chosen').remove();
				$(this).prev('.kda-ee-tbl-scroll').scrollLeft($(this).scrollLeft());
			});
			$('.kda-ee-tbl-scroll', wrap).bind('scroll', function(){
				$('#kda_select_chosen').remove();
				$(this).next('.kda-ee-tbl-wrap').scrollLeft($(this).scrollLeft());
			});
			EList.InitLines();
			
			if(scrollLeft)
			{
				$('.kda-ee-tbl-wrap', wrap).scrollLeft(scrollLeft);
				setTimeout(function(){$('.kda-ee-tbl-scroll', wrap).scrollLeft(scrollLeft);}, 100);
			}
		});*/
	},
	
	AddColumn: function(btn)
	{
		var parent = $(btn).closest('div');
		var parentTr = parent.closest('tr');
		var wrap = parent.closest('.kda-ee-sheet');
		var input = $('input[name^="FIELDS_LIST_SHOW["]', parent)[0];
		var arKeys = input.name.substr(input.name.indexOf('[') + 1, input.name.length - input.name.indexOf('[') - 2).split('][');
		var colPosition = parseInt(arKeys[1]) + 1;
		$('input[name^="SETTINGS[FIELDS_LIST]"]', parentTr).each(function(){
			var input = this;
			var arKeys = input.name.substr(input.name.indexOf('[') + 1, input.name.length - input.name.indexOf('[') - 2).split('][');
			var key2 = parseInt(arKeys[2]);
			if(key2 >= colPosition)
			{
				arKeys[2] = key2 + 1;
				input.name = 'SETTINGS['+arKeys.join('][')+']';
				
				var parentTh = $(input).closest('th');
				var input2 = $('input[name^="SETTINGS[FIELDS_LIST_NAMES]"]', parentTh);
				if(input2.length > 0)
				{
					input2 = input2[0];
					input2.name = input.name.replace('[FIELDS_LIST]', '[FIELDS_LIST_NAMES]');
				}
				
				var input3 = $('input[name^="EXTRASETTINGS["]', parentTh);
				if(input3.length > 0)
				{
					input3 = input3[0];
					input3.name = input.name.replace('SETTINGS[FIELDS_LIST]', 'EXTRASETTINGS');
				}
			}
		});
		parent.append('<input type="hidden" name="SETTINGS[FIELDS_LIST]['+arKeys[0]+']['+colPosition+']" value="">');
		EList.UpdateSheet(wrap);
	},
	
	DeleteColumn: function(btn)
	{
		var parent = $(btn).closest('div');
		var parentTr = parent.closest('tr');
		var wrap = parent.closest('.kda-ee-sheet');
		var input = $('input[name^="FIELDS_LIST_SHOW["]', parent)[0];
		var arKeys = input.name.substr(input.name.indexOf('[') + 1, input.name.length - input.name.indexOf('[') - 2).split('][');
		var colPosition = parseInt(arKeys[1]);
		$('input[name^="SETTINGS[FIELDS_LIST]"]', parentTr).each(function(){
			var input = this;
			var parentTh = $(input).closest('th');
			var input2 = $('input[name^="SETTINGS[FIELDS_LIST_NAMES]"]', parentTh);
			var input3 = $('input[name^="EXTRASETTINGS"]', parentTh);

			var arKeys = input.name.substr(input.name.indexOf('[') + 1, input.name.length - input.name.indexOf('[') - 2).split('][');
			var key2 = parseInt(arKeys[2]);
			if(key2 == colPosition)
			{
				$(input).remove();
				if(input2.length > 0) input2[0].name = '';
			}
			else if(key2 > colPosition)
			{
				arKeys[2] = key2 - 1;
				input.name = 'SETTINGS['+arKeys.join('][')+']';
				if(input2.length > 0) input2[0].name = input.name.replace('[FIELDS_LIST]', '[FIELDS_LIST_NAMES]');
				if(input3.length > 0) input3[0].name = input.name.replace('SETTINGS[FIELDS_LIST]', 'EXTRASETTINGS');
			}
		});
		EList.UpdateSheet(wrap);
	},
	
	ShowLineActions: function(input)
	{
		var arKeys = input.name.substr(0, input.name.length - 1).split('][');
		var action = arKeys[arKeys.length - 1];
		var title = admKDAMessages.lineActions[action].title;
		if(action.indexOf('SET_SECTION_')==0)
		{
			var style = input.value;
			if(!style) return;
			var level = parseInt(action.substr(12));
			$(input).closest('.kda-ie-tbl').find('td.line-settings').each(function(){
				var td = $(this);
				if($('.cell_inner:not(:empty):eq(0)', td.closest('tr')).attr('data-style') == style)
				{
					var html = '<span class="slevel" data-level="'+level+'" title="'+title+'">P'+level+'</span>';
					if(td.find('.slevel').length > 0)
					{
						td.find('.slevel').replaceWith(html);
					}
					else
					{
						td.append(html);
					}
				}
				else
				{
					if(td.find('.slevel[data-level='+level+']').length > 0)
					{
						td.find('.slevel').remove();
					}
				}
			});
		}
	},
	
	SetWidthList: function()
	{
		$('.kda-ee-tbl-wrap').each(function(){
			var div = $(this);
			div.css('width', 0);
			div.prev('.kda-ee-tbl-scroll').css('width', 0);
			var timer = setInterval(function(){
				var width = div.parent().width();
				if(width > 0)
				{
					div.css('width', width);
					div.prev('.kda-ee-tbl-scroll').css('width', width).find('>div').css('width', div.find('>table').width());
					clearInterval(timer);
					//$('select[name^="SETTINGS[FIELDS_LIST]"]', div).chosen();
				}
			}, 100);
			setTimeout(function(){clearInterval(timer);}, 3000);
		});
	},
	
	ToggleSettings: function(btn)
	{
		var tr = $(btn).closest('.kda-ie-tbl').find('tr.settings');
		if(tr.is(':visible'))
		{
			tr.hide();
			$(btn).removeClass('open');
		}
		else
		{
			tr.show();
			$(btn).addClass('open');
		}
		$(window).trigger('resize');		
	},

	ShowFull: function(btn)
	{
		var tbl = $(btn).closest('.kda-ie-tbl');
		var list = tbl.attr('data-list-index');
		var colCount = Math.max(1, $('table.list tr:eq(0) > td', tbl).length - 1);
		var post = $(btn).closest('form').serialize() + '&ACTION=SHOW_FULL_LIST&LIST_NUMBER=' + list + '&COUNT_COLUMNS=' + colCount;
		var wait = BX.showWait();
		$.post(window.location.href, post, function(data){
			data = $(data);
			var chb = $('input[type=checkbox][name^="SETTINGS[CHECK_ALL]"]', tbl);
			/*if(chb.length > 0)
			{
				if(chb[0].checked)
				{
					data.find('input[type=checkbox]').attr('checked', true);
				}
				else
				{
					data.find('input[type=checkbox]').attr('checked', false);
				}
			}*/
			$('table.list', tbl).append(data);
			/*$('table.list input[type=checkbox]', tbl).each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});*/
			EList.InitLines(list);
			$(window).trigger('resize');
			BX.closeWait(null, wait);
		});
		$(btn).hide();
	},
	
	ApplyToAllLists: function(link)
	{
		var tbl = $(link).closest('.kda-ie-tbl');
		var tbls = tbl.parent().find('.kda-ie-tbl').not(tbl);
		var form = tbl.closest('form')[0];
		
		var post = {
			'MODE': 'AJAX',
			'ACTION': 'APPLY_TO_LISTS',
			'PROFILE_ID': form.PROFILE_ID.value,
			'LIST_FROM': tbl.attr('data-list-index')
		}
		post.LIST_TO = [];
		for(var i=0; i<tbls.length; i++)
		{
			post.LIST_TO.push($(tbls[i]).attr('data-list-index'));
		}
		$.post(window.location.href, post, function(data){});
		
		var ts = tbl.find('.kda-ie-field-select');
		for(var i=0; i<tbls.length; i++)
		{
			var tss = $('.kda-ie-field-select', tbls[i]);
			for(var j=0; j<ts.length; j++)
			{
				if(!tss[j]) continue;
				var c1 = $('input.fieldval', ts[j]).length;
				var c2 = $('input.fieldval', tss[j]).length;
				if(c2 < c1)
				{
					for(var k=0; k<c1-c2; k++)
					{
						$('.kda-ie-add-load-field', tss[j]).trigger('click');
					}
				}
				else if(c2 > c1)
				{
					for(var k=0; k<c2-c1; k++)
					{
						$('.field_delete:last', tss[j]).trigger('click');
					}
				}
				
				var fts = $('input[name^="SETTINGS[FIELDS_LIST]"]', ts[j]);
				var fts2 = $('input[name^="FIELDS_LIST_SHOW"]', ts[j]);
				var fts2s = $('a.field_settings', ts[j]);
				var ftss = $('input[name^="SETTINGS[FIELDS_LIST]"]', tss[j]);
				var ftss2 = $('input[name^="FIELDS_LIST_SHOW"]', tss[j]);
				var ftss2s = $('a.field_settings', tss[j]);
				for(var k=0; k<ftss.length; k++)
				{
					if(fts[k])
					{
						ftss[k].value = fts[k].value;
						ftss2[k].value = fts2[k].value;
						if($(fts2s[k]).hasClass('inactive')) $(ftss2s[k]).addClass('inactive');
						else $(ftss2s[k]).removeClass('inactive');
					}
				}
			}
		}
	},
	
	OnAfterAddNewProperty: function(fieldName, propId, propName, iblockId)
	{
		var field = $('input[name="'+fieldName+'"]');
		var form = field.closest('form')[0];
		var post = {
			'MODE': 'AJAX',
			'ACTION': 'GET_SECTION_LIST',
			'IBLOCK_ID': iblockId,
			'PROFILE_ID': form.PROFILE_ID.value
		}
		var ptable = $(field).closest('.kda-ie-tbl');
		$.post(window.location.href, post, function(data){			
			ptable.find('select[name^="FIELDS_LIST["]').each(function(){
				var fields = $(data).find('select[name=fields]');
				fields.attr('name', this.name);
				$(this).replaceWith(fields);
			});
		});
		field.val(propName);
		$('input[name="'+fieldName.replace('FIELDS_LIST_SHOW', 'SETTINGS[FIELDS_LIST]')+'"]', ptable).val(propId);
		
		BX.WindowManager.Get().Close();
	},
	
	ChooseIblock: function(select)
	{
		var form = $(select).closest('form')[0];
		var post = {
			'MODE': 'AJAX',
			'ACTION': 'GET_SECTION_LIST',
			'IBLOCK_ID': select.value,
			'PROFILE_ID': form.PROFILE_ID.value
		}
		$.post(window.location.href, post, function(data){
			var sections = $(data).find('select[name=sections]');
			var sectSelect = $(select).closest('table').find('select[name="'+select.name.replace('[IBLOCK_ID]', '[SECTION_ID]')+'"]');
			sections.attr('name', sectSelect.attr('name'));
			sectSelect.replaceWith(sections);
			
			var ptable = $(select).closest('.kda-ie-tbl');
			ptable.find('select[name^="FIELDS_LIST["]').each(function(){
				var fields = $(data).find('select[name=fields]');
				fields.attr('name', this.name);
				$(this).replaceWith(fields);
				EList.SetFieldValues(ptable);
			});
			
			ptable.find('table.list tbody, table.list tfoot').show();
			ptable.attr('data-iblock-id', select.value);
		});
	},
	
	OnChangeFieldHandler: function(select)
	{
		var val = select.value;
		var link = $(select).next('a.field_settings');
		/*if(val.indexOf("ICAT_PRICE")===0 || val=="ICAT_PURCHASING_PRICE")
		{
			link.removeClass('inactive');
		}
		else
		{
			link.addClass('inactive');
		}*/
	},
	
	AddUploadField: function(link)
	{
		var parent = $(link).closest('.kda-ie-field-select-btns');
		var div = parent.prev('div').clone();
		var input = $('input[name^="SETTINGS[FIELDS_LIST]"]', div)[0];
		var inputShow = $('input[name^="FIELDS_LIST_SHOW"]', div)[0];
		var a = $('a.field_settings', div)[0];
		$('.field_insert', div).remove();
		
		var sname = input.name;
		var index = sname.substr(0, sname.length-1).split('][').pop();
		var arIndex = index.split('_');
		if(arIndex.length==1) arIndex[1] = 1;
		else arIndex[1] = parseInt(arIndex[1]) + 1;
		
		input.name = input.name.replace(/\[[\d_]+\]$/, '['+arIndex.join('_')+']');
		inputShow.name = input.name.replace('SETTINGS[FIELDS_LIST]', 'FIELDS_LIST_SHOW')
		if(arIndex[1] > 1) a.id = a.id.replace(/\_\d+_\d+$/, '_'+arIndex.join('_'));
		else a.id = a.id.replace(/\_\d+$/, '_'+arIndex.join('_'));
		
		div.insertBefore(parent);
		EList.OnFieldFocus(inputShow);
	},
	
	DeleteUploadField: function(link)
	{
		var parent = $(link).closest('div');
		parent.remove();
	},
	
	ShowFieldSettings: function(btn)
	{
		//if($(btn).hasClass('inactive')) return;
		var input = $(btn).prevAll('input[name^="SETTINGS[FIELDS_LIST]"]');
		var input2 = $(btn).prevAll('input[name^="FIELDS_LIST_SHOW["]');
		var val = input.val();
		var name = input[0].name;
		var ptable = $(btn).closest('.kda-ee-tbl');
		var form = $(btn).closest('form')[0];
		
		var dialogParams = {
			'title':BX.message("KDA_EE_SETTING_UPLOAD_FIELD") + (input2.val() ? ' "'+input2.val()+'"' : ''),
			'content_url':'/bitrix/admin/'+kdaIEModuleFilePrefix+'_field_settings_highload.php?field='+val+'&field_name='+name+'&HLBL_ID='+ptable.attr('data-iblock-id')+'&PROFILE_ID='+form.PROFILE_ID.value,
			'width':'900',
			'height':'400',
			'resizable':true
		};
		if($('input', btn).length > 0)
		{
			dialogParams['content_url'] += '&return_data=1';
			dialogParams['content_post'] = {'POSTEXTRA': $('input', btn).val()};
		}
		var dialog = new BX.CAdminDialog(dialogParams);
			
		dialog.SetButtons([
			dialog.btnCancel,
			new BX.CWindowButton(
			{
				title: BX.message('JS_CORE_WINDOW_SAVE'),
				id: 'savebtn',
				name: 'savebtn',
				className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
				action: function () {
					this.disableUntilError();
					this.parentWindow.PostParameters();
					//this.parentWindow.Close();
				}
			})/*,
			dialog.btnSave*/
		]);
			
		BX.addCustomEvent(dialog, 'onWindowRegister', function(){
			$('input[type=checkbox]', this.DIV).each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
		});
			
		dialog.Show();
	},
	
	ShowListSettings: function(btn)
	{
		var tbl = $(btn).closest('.kda-ie-tbl');
		var post = 'list_index='+tbl.attr('data-list-index');
		var inputs = tbl.find('input[name^="SETTINGS[FIELDS_LIST]"], select[name^="SETTINGS[IBLOCK_ID]"], select[name^="SETTINGS[SECTION_ID]"], input[name^="SETTINGS[ADDITIONAL_SETTINGS]"]');
		for(var i in inputs)
		{
			post += '&'+inputs[i].name+'='+inputs[i].value;
		}
		
		var abtns = tbl.find('a.field_insert');
		var findFields = [];
		for(var i=0; i<abtns.length; i++)
		{
			findFields.push('FIND_FIELDS[]='+$(abtns[i]).attr('data-value'));
		}
		if(findFields.length > 0)
		{
			post += '&'+findFields.join('&');
		}
		
		var dialog = new BX.CAdminDialog({
			'title':'',
			'content_url':'/bitrix/admin/'+kdaIEModuleFilePrefix+'_list_settings.php',
			'content_post': post,
			'width':'900',
			'height':'400',
			'resizable':true});
			
		dialog.SetButtons([
			dialog.btnCancel,
			new BX.CWindowButton(
			{
				title: BX.message('JS_CORE_WINDOW_SAVE'),
				id: 'savebtn',
				name: 'savebtn',
				className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
				action: function () {
					this.disableUntilError();
					this.parentWindow.PostParameters();
					//this.parentWindow.Close();
				}
			})/*,
			dialog.btnSave*/
		]);
			
		BX.addCustomEvent(dialog, 'onWindowRegister', function(){
			$('input[type=checkbox]', this.DIV).each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
			$('select.kda-chosen-multi').chosen();
		});
			
		dialog.Show();
	},
	
	ApplyFilter: function(el)
	{
		/*BX.adminPanel.showWait(el);
		BX.adminPanel.closeWait(el);*/
		//var wrap = $(el).closest('.kda-ee-sheet');
		var wrap = $(el).closest('.kda-ee-sheet-wrap').find('.kda-ee-sheet');
		EList.UpdateSheet(wrap);
		return false;
	},
	
	DeleteFilter: function(el)
	{
		var formInner = $(el).closest('.find_form_inner');
		$('select, input[type="text"], textarea', formInner).val('');
		$('input[type="radio"], input[type="checkbox"]', formInner).removeAttr('checked');
		
		this.ApplyFilter(el);
		return false;
	},
	
	SetExtraParams: function(oid, returnJson)
	{
		var btn = $("#"+oid);
		if(typeof returnJson == 'object') returnJson = JSON.stringify(returnJson);
		if(returnJson.length > 0) btn.removeClass("inactive");
		else btn.addClass("inactive");
		$('input', btn).val(returnJson);
		if(BX.WindowManager.Get())
		{
			BX.WindowManager.Get().Close();
			var wrap = btn.closest('.kda-ee-sheet');
			EList.UpdateSheet(wrap);
		}
	},
	
	AddAllFields: function(key, listIndex, prefix)
	{
		if(!prefix) return;
		var wrap = $('.kda-ee-sheet[data-sheet-index='+listIndex+']');
		var setParent = $('.kda-ee-hidden-settings', wrap);
		
		var parent = $('.sandwich[data-key="'+key+'"]', wrap).closest('tr');
		var lastCell = $('th:last', parent);
		var inputs = $('input[name^="SETTINGS[FIELDS_LIST]"]', parent);
		var arFields = {};
		var maxKey = 0;
		for(var i=0; i<inputs.length; i++)
		{
			arFields[inputs[i].value] = inputs[i].value;
			maxKey = parseInt(inputs[i].name.replace(/^.*\[(\d+)\]$/, '$1'));
		}
		
		var options = $('select[name="FIELDS_LIST['+listIndex+']"] option', setParent);
		for(var i=0; i<options.length; i++)
		{
			if(options[i].value.indexOf(prefix)==0 && !arFields[options[i].value])
			{
				lastCell.append('<input type="hidden" name="SETTINGS[FIELDS_LIST]['+listIndex+']['+(++maxKey)+']" value="'+options[i].value+'">');
			}
		}
		
		EList.UpdateSheet(wrap);
	},
	
	SetLineDisplaySetting: function(key, listIndex, type)
	{
		var parent = $('.kda-ee-sheet[data-sheet-index='+listIndex+'] .kda-ee-hidden-settings');
		var input = $('input[name="SETTINGS[DISPLAY_PARAMS]['+listIndex+']"]', parent);
		var form = parent.closest('form')[0];
		var params = {};
		if(input.length > 0 && input.val().length > 0) params = JSON.parse(input.val());
		var dialogParams = {
			'title':BX.message("KDA_EE_DISPLAY_SETTINGS_TITLE"),
			'content_url':'/bitrix/admin/'+kdaIEModuleFilePrefix+'_display_settings.php?key='+key+'&list_index='+listIndex+'&type='+type+'&PROFILE_ID='+form.PROFILE_ID.value,
			'content_post': {'PARAMS': params},
			'width':'900',
			'height':'400',
			'resizable':true
		};
		var dialog = new BX.CAdminDialog(dialogParams);
			
		dialog.SetButtons([
			dialog.btnCancel,
			new BX.CWindowButton(
			{
				title: BX.message('JS_CORE_WINDOW_SAVE'),
				id: 'savebtn',
				name: 'savebtn',
				className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
				action: function () {
					this.disableUntilError();
					this.parentWindow.PostParameters();
				}
			})
		]);
			
		BX.addCustomEvent(dialog, 'onWindowRegister', function(){
			$('input[type=checkbox]', this.DIV).each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
		});
			
		dialog.Show();
	},
	
	SetDisplayParams: function(listIndex, returnJson)
	{
		var wrap = $('.kda-ee-sheet[data-sheet-index='+listIndex+']');
		var parent = $('.kda-ee-hidden-settings', wrap);
		var inputName = 'SETTINGS[DISPLAY_PARAMS]['+listIndex+']';
		var input = $('input[name="'+inputName+'"]', parent);
		if(input.length == 0)
		{
			parent.append('<input name="'+inputName+'" value="">');
			input = $('input[name="'+inputName+'"]', parent);
		}
		if(typeof returnJson == 'object') returnJson = JSON.stringify(returnJson);
		input.val(returnJson);

		if(BX.WindowManager.Get())
		{
			BX.WindowManager.Get().Close();
			EList.UpdateSheet(wrap);
		}
	},
	
	ResetLineDisplaySetting: function(key, listIndex, notReload)
	{
		var wrap = $('.kda-ee-sheet[data-sheet-index='+listIndex+']');
		var parent = $('.kda-ee-hidden-settings', wrap);
		var input = $('input[name="SETTINGS[DISPLAY_PARAMS]['+listIndex+']"]', parent);
		if(input.length > 0 && input.val().length > 0)
		{
			params = JSON.parse(input.val());
			if(params[key])
			{
				delete params[key];
				input.val(JSON.stringify(params));
			}
		}
		if(!notReload) EList.UpdateSheet(wrap);
	}
}

var EProfile = {
	Init: function()
	{
		var select = $('select#PROFILE_ID');
		if(select.length > 0)
		{
			select = select[0]
			/*this.Choose(select[0]);*/
			if(select.value=='new')
			{
				$('#new_profile_name').css('display', '');
			}
			else
			{
				$('#new_profile_name').css('display', 'none');
			}
		
			if(document.getElementById('kda-ee-file-extension'))
			{
				$('#kda-ee-file-extension').bind('change', function(){
					var ext = this.value;
					var arPath = $('#kda-ee-file-path').val().split('.');
					if(arPath.length > 1)
					{
						arPath[arPath.length - 1] = ext;
					}
					else
					{
						arPath.push(ext);
					}
					var path = arPath.join('.');
					$('#kda-ee-file-path').val(path);
					EProfile.ToggleCsvSettings();
				});
			}
		
			$('select.adm-detail-iblock-list').bind('change', function(){
				$.post(window.location.href, {'MODE': 'AJAX', 'IBLOCK_ID': this.value, 'ACTION': 'GET_UID'}, function(data){
					var fields = $(data).find('select[name="fields[]"]');
					var select = $('select[name="SETTINGS_DEFAULT[ELEMENT_UID][]"]');
					fields.val(select.val());
					fields.attr('name', select.attr('name'));
					//$('select.chosen').chosen('destroy');
					select.replaceWith(fields);
					//$('select.chosen').chosen();
					
					var fields2 = $(data).find('select[name="fields_sku[]"]');
					var select2 = $('select[name="SETTINGS_DEFAULT[ELEMENT_UID_SKU][]"]');
					if(select2.val()) fields2.val(select2.val());
					fields2.attr('name', select2.attr('name'));
					select2.replaceWith(fields2);
					if(fields2.length > 0 && fields2[0].options.length > 0) $('#element_uid_sku').show();
					else $('#element_uid_sku').hide();
					
					var fields = $(data).find('select[name="properties[]"]');
					var select = $('select[name="SETTINGS_DEFAULT[ELEMENT_PROPERTIES_REMOVE][]"]');
					fields.val(select.val());
					fields.attr('name', select.attr('name'));
					$('select.kda-chosen-multi').chosen('destroy');
					select.replaceWith(fields);
					$('select.kda-chosen-multi').chosen({width: '300px'});
				});
			});
			
			var select = $('select[name="SETTINGS_DEFAULT[ELEMENT_UID][]"]');
			if(select.length > 0 && !select.val()) select[0].options[0].selected = true;
			/*$('select.chosen').chosen();*/
			$('select.kda-chosen-multi').chosen({width: '300px'});
			this.ToggleAdditionalSettings();
			this.ToggleCsvSettings();
		}
	},
	
	Choose: function(select)
	{
		var id = (typeof select == 'object' ? select.value : select);
		var query = window.location.search.replace(/PROFILE_ID=[^&]*&?/, '');
		if(query.length < 2) query = '?';
		if(query.length > 1 && query.substr(query.length-1)!='&') query += '&';
		query += 'PROFILE_ID=' + id;
		window.location.href = query;
	},
	
	Delete: function()
	{
		var obj = this;
		var select = $('select#PROFILE_ID');
		var option = select[0].options[select[0].selectedIndex];
		var id = option.value;
		$.post(window.location.href, {'MODE': 'AJAX', 'ID': id, 'ACTION': 'DELETE_PROFILE'}, function(data){
			obj.Choose('');
		});
	},
	
	Copy: function()
	{
		var obj = this;
		var select = $('select#PROFILE_ID');
		var option = select[0].options[select[0].selectedIndex];
		var id = option.value;
		$.post(window.location.href, {'MODE': 'AJAX', 'ID': id, 'ACTION': 'COPY_PROFILE'}, function(data){
			eval('var res = '+data+';');
			obj.Choose(res.id);
		});
	},
	
	ShowRename: function()
	{
		var select = $('select#PROFILE_ID');
		var option = select[0].options[select[0].selectedIndex];
		var name = option.innerHTML;
		
		var tr = $('#new_profile_name');
		var input = $('input[type=text]', tr);
		input.val(name);
		if(!input.attr('init_btn'))
		{
			input.after('&nbsp;<input type="button" onclick="EProfile.Rename();" value="OK">');
			input.attr('init_btn', 1);
		}
		tr.css('display', '');
	},
	
	Rename: function()
	{
		var select = $('select#PROFILE_ID');
		var option = select[0].options[select[0].selectedIndex];
		var id = option.value;
		
		var tr = $('#new_profile_name');
		var input = $('input[type=text]', tr);
		var value = $.trim(input.val());
		if(value.length==0) return false;
		
		tr.css('display', 'none');
		option.innerHTML = value;
		
		$.post(window.location.href, {'MODE': 'AJAX', 'ID': id, 'NAME': value, 'ACTION': 'RENAME_PROFILE'}, function(data){});
	},
	
	ShowCron: function()
	{
		var dialog = new BX.CAdminDialog({
			'title':BX.message("KDA_EE_POPUP_CRON_TITLE"),
			'content_url':'/bitrix/admin/'+kdaIEModuleFilePrefix+'_cron_settings.php',
			'width':'800',
			'height':'350',
			'resizable':true});
			
		dialog.SetButtons([
			dialog.btnCancel/*,
			new BX.CWindowButton(
			{
				title: BX.message('JS_CORE_WINDOW_SAVE'),
				id: 'savebtn',
				name: 'savebtn',
				className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
				action: function () {
					this.disableUntilError();
					this.parentWindow.PostParameters();
					//this.parentWindow.Close();
				}
			})*/
		]);
			
		dialog.Show();
	},
	
	SaveCron: function(btn)
	{
		var form = $(btn).closest('form');
		$.post(form[0].getAttribute('action'), form.serialize()+'&subaction='+btn.name, function(data){
			$('#kda-ie-cron-result').html(data);
		});
	},
	
	RemoveProccess: function(link, id)
	{
		var post = {
			'MODE': 'AJAX',
			'PROCCESS_PROFILE_ID': id,
			'ACTION': 'REMOVE_PROCESS_PROFILE'
		};
		
		$.ajax({
			type: "POST",
			url: window.location.href,
			data: post,
			success: function(data){
				var parent = $(link).closest('.kda-proccess-item');
				if(parent.parent().find('.kda-proccess-item').length <= 1)
				{
					parent.closest('.adm-info-message-wrap').hide();
				}
				parent.remove();
			}
		});
	},
	
	ContinueProccess: function(link, id)
	{
		var parent = $(link).closest('div');
		parent.append('<form method="post" action="" style="display: none;">'+
						'<input type="hidden" name="PROFILE_ID" value="'+id+'">'+
						'<input type="hidden" name="STEP" value="3">'+
						'<input type="hidden" name="PROCESS_CONTINUE" value="Y">'+
						'<input type="hidden" name="sessid" value="'+$('#sessid').val()+'">'+
					  '</form>');
		parent.find('form')[0].submit();
	},
	
	ToggleAdditionalSettings: function(link)
	{
		if(!link) link = document.getElementById('kda-head-more-link');
		if(!link) return;
		var tr = $(link).closest('tr');
		var trs = tr.nextAll('tr:not(.heading)');
		if($(link).hasClass('show'))
		{
			trs.hide();
			$(link).removeClass('show');
		}
		else
		{
			trs.show();
			$(link).addClass('show');
		}
	},
	
	ToggleCsvSettings: function()
	{
		var bShow = ($('#kda-ee-file-extension').val()=='csv');
		var tr = $('#csv_settings_block');
		var i = 0;
		while(tr.length > 0 && (!tr.hasClass('heading') || i==0))
		{
			if(!bShow) tr.hide();
			else tr.show();
			tr = tr.next('tr');
			i++;
		}
	},
	
	RadioChb: function(chb1, chb2name)
	{
		if(chb1.checked)
		{
			var form = $(chb1).closest('form');
			form[0][chb2name].checked = false;
		}
	},
	
	ToggleSectionsSettings: function(chb)
	{
		var tr = $(chb).closest('tr').next('tr');
		while(tr.length > 0 && !tr.hasClass('heading'))
		{
			if(chb.checked) tr.show();
			else tr.hide();
			tr = tr.next('tr');
		}
	}
}

var EImport = {
	params: {},

	Init: function(post, params)
	{
		BX.scrollToNode($('#resblock .adm-info-message')[0]);
		this.wait = BX.showWait();
		this.post = post;
		if(typeof params == 'object') this.params = params;
		this.SendData();
		this.pid = post.PROFILE_ID;
		this.idleCounter = 0;
		this.errorStatus = false;
		var obj = this;
		setTimeout(function(){obj.SetTimeout();}, 3000);
	},
	
	SetTimeout: function()
	{
		if($('#progressbar').hasClass('end')) return;
		var obj = this;
		this.timer = setTimeout(function(){obj.GetStatus();}, 2000);
	},
	
	GetStatus: function()
	{
		var obj = this;
		$.ajax({
			type: "GET",
			url: '/upload/tmp/'+kdaIEModuleName+'/'+kdaIEModuleAddPath+this.pid+'.txt?hash='+(new Date()).getTime(),
			success: function(data){
				var finish = false;
				if(data && data.substr(0, 1)=='{' && data.substr(data.length-1)=='}')
				{
					try {
						eval('var result = '+data+';');
					} catch (err) {
						var result = false;
					}
				}
				else
				{
					var result = false;
				}
				
				if(typeof result == 'object')
				{
					if(result.action!='finish')
					{
						obj.UpdateStatus(result);
					}
					else
					{
						obj.UpdateStatus(result, true);
						var finish = true;
					}
				}
				if(!finish) obj.SetTimeout();
			},
			error: function(){
				obj.SetTimeout();
			},
			timeout: 5000
		});
	},
	
	UpdateStatus: function(result, end)
	{
		if($('#progressbar').hasClass('end')) return;
		if(end && this.timer) clearTimeout(this.timer);
		
		if(typeof result == 'object')
		{
			if(end && (parseInt(result.total_read_line) < parseInt(result.total_file_line)))
			{
				result.total_read_line = result.total_file_line;
			}
			
			$('#total_read_line').html(result.total_read_line);
			$('#element_added_line').html(result.element_added_line);
			$('#sku_added_line').html(result.sku_added_line);
			$('#section_added_line').html(result.section_added_line);
			
			var span = $('#progressbar .presult span');
			//span.html(span.attr('data-prefix')+': '+result.total_read_line+'/'+result.total_file_line);
			//span.css('visibility', 'hidden');
			if(result.curstep && span.attr('data-'+result.curstep))
			{
				span.html(span.attr('data-'+result.curstep));
			}
			if(end)
			{
				span.css('visibility', 'hidden');
				$('#progressbar .presult').removeClass('load');
				$('#kda_ee_ready_file').css('visibility', 'visible');
				$('#progressbar').addClass('end');
			}
			if(result.total_file_line > 0)
				var percent = Math.round((result.total_read_line / result.total_file_line) * 100);
			else
				var percent = 100;
			if(percent >= 100)
			{
				if(end) percent = 100;
				else percent = 99;
			}
			$('#progressbar .presult b').html(percent+'%');
			$('#progressbar .pline').css('width', percent+'%');
			
			if(this.tmpparams && this.tmpparams.total_read_line==result.total_read_line)
			{
				this.idleCounter++;
			}
			else
			{
				this.idleCounter = 0;
			}
			this.tmpparams = result;
		}
		
		/*if(this.idleCounter > 10 && this.errorStatus)
		{
			var obj = this;
			for(var i in obj.tmpparams)
			{
				obj.params[i] = obj.tmpparams[i];
			}
			obj.SendData();
		}*/
	},
	
	SendData: function()
	{
		var post = this.post;
		post.ACTION = 'DO_EXPORT';
		post.stepparams = this.params;
		var obj = this;
		
		$.ajax({
			type: "POST",
			url: window.location.href,
			data: post,
			success: function(data){
				obj.errorStatus = false;
				obj.OnLoad(data);
			},
			error: function(){
				obj.errorStatus = true;
				$('#block_error_import').show();
			},
			timeout: 180000
		});
	},
	
	OnLoad: function(data)
	{
		data = $.trim(data);
		if(data.indexOf('{')!=0)
		{
			this.SendData();
			return true;
		}
		try {
			eval('var result = '+data+';');
		} catch (err) {
			var result = false;
		}
		if(typeof result == 'object')
		{
			if(result.sessid)
			{
				$('#sessid').val(result.sessid);
			}
			
			if(typeof result.errors == 'object' && result.errors.length > 0)
			{
				$('#block_error').show();
				for(var i=0; i<result.errors.length; i++)
				{
					$('#res_error').append('<div>'+result.errors[i]+'</div>');
				}
			}
			
			if(result.action=='continue')
			{
				this.UpdateStatus(result.params);
				this.params = result.params;
				this.SendData();
				return true;
			}
		}
		else
		{
			this.SendData();
			return true;
		}

		this.UpdateStatus(result.params, true);
		BX.closeWait(null, this.wait);
		/*$('#res_continue').hide();
		$('#res_finish').show();*/
		
		if(result.params.redirect_url && result.params.redirect_url.length > 0)
		{
			$('#redirect_message').html($('#redirect_message').html() + result.params.redirect_url);
			$('#redirect_message').show();
			setTimeout(function(){window.location.href = result.params.redirect_url}, 3000);
		}
		return false;
	}
}

var ESettings = {
	AddValue: function(link)
	{
		var input = $(link).prev('div').find('input[type=text]');
		var name = (input.length > 0 ? input[0].name : '');
		$(link).before('<div><input type="text" name="'+name+'" value=""></div>');
	},
	
	AddMargin: function(link)
	{
		var div = $(link).closest('td').find('.kda-ie-settings-margin:eq(0)');
		if(!div.is(':visible'))
		{
			div.show();
		}
		else
		{
			var div2 = div.clone(true);
			$('select, input', div2).val('');
			$(link).before(div2);
		}
	},
	
	RemoveMargin: function(link)
	{
		var divs = $(link).closest('td').find('.kda-ie-settings-margin');
		if(divs.length > 1)
		{
			$(link).closest('.kda-ie-settings-margin').remove();
		}
		else
		{
			$('select, input', divs).val('');
			divs.hide();
		}
	},
	
	ShowMarginTemplateBlock: function(link)
	{
		var div = $('#margin_templates');
		div.toggle();
	},
	
	ShowMarginTemplateBlockLoad: function(link, action)
	{
		var div = $('#margin_templates_load');
		if(action == 'hide') div.hide();
		else div.toggle();
	},
	
	SaveMarginTemplate: function(input, message)
	{
		var div = $(input).closest('div');
		var tid = $('select[name=MARGIN_TEMPLATE_ID]', div).val();
		var tname = $('input[name=MARGIN_TEMPLATE_NAME]', div).val();
		if(tid.length==0 && tname.length==0) return false;
		
		var wm = BX.WindowManager.Get();
		var url = wm.PARAMS.content_url;
		var params = wm.GetParameters().replace(/(^|&)action=[^&]*($|&)/, '&').replace(/^&+/, '').replace(/&+$/, '')
		params += '&action=save_margin_template&template_id='+tid+'&template_name='+tname;
		$.post(url, params, function(data){
			var jData = $(data);
			$('#margin_templates').replaceWith(jData.find('#margin_templates'));
			$('#margin_templates_load').replaceWith(jData.find('#margin_templates_load'));
			alert(message);
		});
		
		return false;
	},
	
	LoadMarginTemplate: function(input)
	{
		var div = $(input).closest('div');
		var tid = $('select[name=MARGIN_TEMPLATE_ID]', div).val();
		if(tid.length==0) return false;
		
		var wm = BX.WindowManager.Get();
		var url = wm.PARAMS.content_url;
		var params = wm.GetParameters().replace(/(^|&)action=[^&]*($|&)/, '&').replace(/^&+/, '').replace(/&+$/, '')
		params += '&action=load_margin_template&template_id='+tid;
		var obj = this;
		$.post(url, params, function(data){
			var jData = $(data);
			$('#settings_margins').replaceWith(jData.find('#settings_margins'));
			obj.ShowMarginTemplateBlockLoad('hide');
		});
		
		return false;
	},
	
	RemoveMarginTemplate: function(input, message)
	{
		var div = $(input).closest('div');
		var tid = $('select[name=MARGIN_TEMPLATE_ID]', div).val();
		if(tid.length==0) return false;
		
		var wm = BX.WindowManager.Get();
		var url = wm.PARAMS.content_url;
		var params = wm.GetParameters().replace(/(^|&)action=[^&]*($|&)/, '&').replace(/^&+/, '').replace(/&+$/, '')
		params += '&action=delete_margin_template&template_id='+tid;
		$.post(url, params, function(data){
			var jData = $(data);
			$('#margin_templates').replaceWith(jData.find('#margin_templates'));
			$('#margin_templates_load').replaceWith(jData.find('#margin_templates_load'));
			alert(message);
		});
		
		return false;
	},
	
	AddConversion: function(link)
	{
		var prevDiv = $(link).prev('.kda-ee-settings-conversion');
		if(!prevDiv.is(':visible'))
		{
			prevDiv.show();
		}
		else
		{
			var div = prevDiv.clone();
			$('select, input', div).not('.choose_val').val('');
			$(link).before(div);
		}
	},
	
	RemoveConversion: function(link)
	{
		var div = $(link).closest('.kda-ee-settings-conversion');
		if($(link).closest('td').find('.kda-ee-settings-conversion').length > 1)
		{
			div.remove();
		}
		else
		{
			$('select, input', div).not('.choose_val').val('');
			div.hide();
		}
	},
	
	ShowChooseVal: function(btn)
	{
		var field = $(btn).prev('input')[0];
		this.focusField = field;
		var arLines = [];
		for(var k in admKDASettingMessages)
		{
			arLines.push({'TEXT':'<b>'+admKDASettingMessages[k].TITLE+'</b>', 'HTML':'<b>'+admKDASettingMessages[k].TITLE+'</b>', 'TITLE':'#'+k+'# - '+admKDASettingMessages[k].TITLE,'ONCLICK':'javascript:void(0)'});
			for(var k2 in admKDASettingMessages[k].FIELDS)
			{
				arLines.push({'TEXT':admKDASettingMessages[k].FIELDS[k2], 'TITLE':'#'+k2+'# - '+admKDASettingMessages[k].FIELDS[k2],'ONCLICK':'ESettings.SetUrlVar(\'#'+k2+'#\')'});
			}
		}
		BX.adminShowMenu(btn, arLines, '');
	},
	
	ShowPHPExpression: function(link)
	{
		var div = $(link).next('.kda-ie-settings-phpexpression');
		if(div.is(':visible')) div.hide();
		else div.show();
	},
	
	SetUrlVar: function(id)
	{
		var obj_ta = this.focusField;
		//IE
		if (document.selection)
		{
			obj_ta.focus();
			var sel = document.selection.createRange();
			sel.text = id;
			//var range = obj_ta.createTextRange();
			//range.move('character', caretPos);
			//range.select();
		}
		//FF
		else if (obj_ta.selectionStart || obj_ta.selectionStart == '0')
		{
			var startPos = obj_ta.selectionStart;
			var endPos = obj_ta.selectionEnd;
			var caretPos = startPos + id.length;
			obj_ta.value = obj_ta.value.substring(0, startPos) + id + obj_ta.value.substring(endPos, obj_ta.value.length);
			obj_ta.setSelectionRange(caretPos, caretPos);
			obj_ta.focus();
		}
		else
		{
			obj_ta.value += id;
			obj_ta.focus();
		}

		BX.fireEvent(obj_ta, 'change');
		obj_ta.focus();
	},
	
	AddDefaultProp: function(select)
	{
		if(!select.value) return;
		var parent = $(select).closest('tr');
		var inputName = 'ADDITIONAL_SETTINGS[ELEMENT_PROPERTIES_DEFAULT]['+select.value+']';
		if($(parent).closest('table').find('input[name="'+inputName+'"]').length > 0) return;
		var tmpl = parent.prev('tr.kda-ie-list-settings-defaults');
		var tr = tmpl.clone();
		tr.css('display', '');
		$('.adm-detail-content-cell-l', tr).html(select.options[select.selectedIndex].innerHTML+':');
		$('input[type=text]', tr).attr('name', inputName);
		tr.insertBefore(tmpl);
	},
	
	RemoveDefaultProp: function(link)
	{
		$(link).closest('tr').remove();
	},
	
	RemoveLoadingRange: function(link)
	{
		$(link).closest('div').remove();
	},
	
	AddNewLoadingRange: function(link)
	{
		var div = $(link).prev('div');
		var newRange = div.clone().insertBefore(div);
		newRange.show();
	},
	
	OnSettingsSave: function(btnId, active)
	{
		var btn = $("#"+btnId);
		if(active) btn.removeClass("inactive");
		else btn.addClass("inactive");
		BX.WindowManager.Get().Close();
		
		var wrap = btn.closest('.kda-ee-sheet');
		EList.UpdateSheet(wrap);
	},
	
	ToggleSubfields: function(input)
	{
		var tr = $(input).closest('tr').next('tr.subfield');
		while(tr.length > 0)
		{
			if(input.checked) tr.show();
			else tr.hide();
			tr = tr.next('tr.subfield');
		}
	}
}

var EHelper = {
	ShowHelp: function(index)
	{
		var dialog = new BX.CAdminDialog({
			'title':BX.message("KDA_EE_POPUP_HELP_TITLE"),
			'content_url':'/bitrix/admin/'+kdaIEModuleFilePrefix+'_popup_help.php',
			'width':'900',
			'height':'450',
			'resizable':true});
			
		BX.addCustomEvent(dialog, 'onWindowRegister', function(){
			$('#kda-ie-help-faq > li > a').bind('click', function(){
				var div = $(this).next('div');
				if(div.is(':visible')) div.stop().slideUp();
				else div.stop().slideDown();
				return false;
			});
			
			if(index > 0)
			{
				$('#kda-ie-help-tabs .kda-ie-tabs-heads a:eq('+parseInt(index)+')').trigger('click');
			}
		});
			
		dialog.Show();
	},
	
	SetTab: function(link)
	{
		var parent = $(link).closest('.kda-ee-tabs');
		var heads = $('.kda-ee-tabs-heads a', parent);
		var bodies = $('.kda-ee-tabs-bodies > div', parent);
		var index = 0;
		for(var i=0; i<heads.length; i++)
		{
			if(heads[i]==link)
			{
				index = i;
				break;
			}
		}
		heads.removeClass('active');
		$(heads[index]).addClass('active');
		
		bodies.removeClass('active');
		$(bodies[index]).addClass('active');
	}
}

$(document).ready(function(){
	EList.Init();
	EProfile.Init();
	
	if($('#'+kdaIEModuleUMClass).length > 0)
	{
		$.post('/bitrix/admin/'+kdaIEModuleFilePrefix+'.php?lang='+BX.message('LANGUAGE_ID'), 'MODE=AJAX&ACTION=SHOW_MODULE_MESSAGE', function(data){
			data = $(data);
			var inner = $('#'+kdaIEModuleUMClass+'-inner', data);
			if(inner.length > 0 && inner.html().length > 0)
			{
				$('#'+kdaIEModuleUMClass+'-inner').replaceWith(inner);
				$('#'+kdaIEModuleUMClass).show();
			}
		});
	}
});