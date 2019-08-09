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
		
		$('#kda-ee-sheet-list .kda-ee-new-list-wrap input[type="button"]').bind('click', function(e){
			EList.AddNewList(this, e);
		});
		
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
					TEXT: BX.message("KDA_EE_CUSTOMIZE_FIELDS_LIST"),
					ONCLICK: "EList.ShowFieldsListSettings('"+listIndex+"')"
				});
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
			
			if(type=='text')
			{
				menuItems.push({
					TEXT: BX.message("KDA_EE_INSERT_DATE"),
					ONCLICK: "EList.OpenAddTextBlock('DATE')"
				});
				menuItems.push({
					TEXT: BX.message("KDA_EE_INSERT_CURRENCY_RATE"),
					ONCLICK: "EList.OpenAddTextBlock('CURRENCY_RATE')"
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
			var showCodes = (sheetParent.attr('data-show-field-codes') == 1);
			var arVals = [];
			var arValsShow = [];
			var arValParents = [];
			for(var i=0; i<pSelect.options.length; i++)
			{
				arVals[pSelect.options.item(i).value] = pSelect.options.item(i).text;
				arValsShow[pSelect.options.item(i).value] = pSelect.options.item(i).text + (showCodes ? ' {'+pSelect.options.item(i).value+'}' : '');
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
				if(inputShowExport.value.length==0) inputShowExport.value = arValsShow[input.value];
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
			var showCodes = (sheetParent.attr('data-show-field-codes') == 1);
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
			if(typeof select.chosen == 'function') select.chosen({search_contains: true});
			select.bind('change', function(){
				var option = options.item(select[0].selectedIndex);
				if(option.value)
				{
					if(inputVal.value != option.value)
					{
						input.value = option.text;
						input.title = option.text;
						inputVal.value = option.value;
						inputValExport.value = option.text + (showCodes ? ' {'+option.value+'}' : '');
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
				if(typeof select.chosen == 'function') select.chosen('destroy');
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
	
	UpdateSheet: function(wrap, params)
	{
		params = params || {};
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
				var findForm = wrap.prev('.find_form_inner');
				findForm.show();
				$('select[name*="find_el_vtype_"]', findForm).bind('change', function(){
					var div = $(this.parentNode).next();
					if(this.value.length > 0) div.hide();
					else div.show();
				}).trigger('change');
				$('select[name$="_comp]"]', findForm).bind('change', function(){
					var div = $(this.parentNode).next();
					if(this.value.indexOf('empty')!=-1) div.hide();
					else div.show();
				}).trigger('change');
				$('select[name$="_FILTER_PERIOD"], select[name$="_FILTER_DIRECTION"]', findForm).each(function(){
					this.name = this.name.replace(/\]([^\]]+)$/, '$1]');
					if(this.name.substr(this.name.length - 15, 14)=='_FILTER_PERIOD')
					{
						$(this).append('<option value="last_days">'+BX.message("KDA_EE_FILTER_LAST_DAYS")+'</option>');
						$(this).closest('.adm-filter-box-sizing').append('<div class="adm-input-wrap adm-calendar-last-days" style="display: none;"><input class="adm-input adm-calendar-last-days adm-calendar-inp-setted" name="'+this.name.replace('_FILTER_PERIOD', '_FILTER_LAST_DAYS')+'" size="15" value="" type="text"></div>');
						$(this).bind('change', function(){
							$(this).closest('.adm-filter-box-sizing').find('div.adm-calendar-last-days').css('display', (this.value=="last_days" ? 'inline-block' : 'none'));
						});
						
						var parentTd = $(this).closest('td');
						var valPeriod = parentTd.data('filter-period');
						var valLastDays = parentTd.data('filter-last-days');
						if((typeof valLastDays == 'string' && valLastDays.length > 0) || (typeof valLastDays == 'number'))
						{
							$(this).closest('.adm-filter-box-sizing').find('div.adm-calendar-last-days input[type=text]').val(valLastDays);
						}
						if(valPeriod=='last_days')
						{
							$(this).val(valPeriod).trigger('change');
						}
					}
				});
				
				$('select[multiple]', findForm).each(function(){
					var parentDiv = $(this).closest('div, td');
					if(parentDiv.find('.kda-ee-select-view-mode').length == 0)
					{
						parentDiv.prepend('<a href="javascript:void(0)" onclick="EList.ChangeSelectViewMode(this)" class="kda-ee-select-view-mode" title="'+BX.message("KDA_EE_SELECT_FAST_VIEW")+'"></a>');
					}
				});
				
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
				
				if(params.show_additional)
				{
					var alink = $('.addsettings_link', wrap);
					alink.addClass('open');
					alink.next('div').show();
				}
				
				var parentWrap = wrap.closest('.kda-ee-sheet-wrap');
				$('.kda-ee-new-list-wrap', parentWrap).show();
				var btnUp = (parentWrap.prev('.kda-ee-sheet-wrap').length > 0);
				var btnDown = (parentWrap.next('.kda-ee-sheet-wrap').length > 0);
				var btnBoth = (btnUp && btnDown);
				if(btnUp) $('.kda-ee-title', wrap).append('<a href="javascript:void(0)" onclick="EList.MoveList(this, -1)" class="kda-ee-list-up '+(!btnBoth ? 'kda-ee-list-single' : '')+'" title="'+BX.message("KDA_EE_MOVE_LIST_UP")+'"></a>');
				if(btnDown) $('.kda-ee-title', wrap).append('<a href="javascript:void(0)" onclick="EList.MoveList(this, 1)" class="kda-ee-list-down '+(!btnBoth ? 'kda-ee-list-single' : '')+'" title="'+BX.message("KDA_EE_MOVE_LIST_DOWN")+'"></a>');
				
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
	
	ChangeSelectViewMode: function(a)
	{
		var select = $(a).parent().find('select:eq(0)');
		if(select.length > 0 && typeof select.chosen == 'function')
		{
			if($(a).attr('mode')!='chosen')
			{
				select.chosen({search_contains: true});
				$(a).attr('title', BX.message("KDA_EE_SELECT_STANDARD_VIEW"));
				$(a).attr('mode', 'chosen');
			}
			else
			{
				select.chosen('destroy');
				$(a).attr('title', BX.message("KDA_EE_SELECT_FAST_VIEW"));
				$(a).attr('mode', '');
			}
		}
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
		var form = $(select).closest('form');
		this.ReloadWithoutSave(form);
	},
	
	ChooseChangeIblock: function(chb)
	{
		if(!chb.checked)
		{
			var form = $(chb).closest('form');
			this.ReloadWithoutSave(form);
		}
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
			'content_url':'/bitrix/admin/'+kdaIEModuleFilePrefix+'_field_settings.php?field='+val+'&field_name='+name+'&IBLOCK_ID='+ptable.attr('data-iblock-id')+'&PROFILE_ID='+form.PROFILE_ID.value,
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
			ESettings.BindConversionEvents();
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
			if(typeof $('select.kda-chosen-multi').chosen == 'function')
			{
				$('select.kda-chosen-multi').chosen();
			}
		});
			
		dialog.Show();
	},
	
	SetSectionProperties: function(el)
	{
		var wrap = $(el).closest('.kda-ee-sheet-wrap').find('.kda-ee-sheet');
		EList.UpdateSheet(wrap, {show_additional: true});
		return false;
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
	
	ShowFieldsListSettings: function(listIndex)
	{
		var table = $('.kda-ee-sheet[data-sheet-index='+listIndex+']');
		var form = table.closest('form')[0];
		var params = {fields: {}};
		var fieldInputs = $('input[name^="SETTINGS[FIELDS_LIST]['+listIndex+']["]', table);
		for(var i=0; i<fieldInputs.length; i++)
		{
			params.fields[fieldInputs[i].name.replace(/^.*\[(\d+)\]$/, '$1')] = fieldInputs[i].value;
			fieldInputs[i];
		}
		var filterWrap = table.prev('.find_form_inner');
		var onlySectionProps = $('input[type=checkbox][name="SETTINGS[SHOW_ONLY_SECTION_PROPERTY]['+listIndex+']"]:checked', table);
		var sectOptions = $('select[name="SETTINGS[FILTER]['+listIndex+'][find_section_section][]"] option', filterWrap);
		if(onlySectionProps.length > 0 && sectOptions.length > 0)
		{
			params.onlysectionprops = 1;
			params.sections = [];
			for(var i=0; i<sectOptions.length; i++)
			{
				if(sectOptions[i].selected) params.sections.push(sectOptions[i].value);
			}
			params.issubsections = ($('input[name="SETTINGS[FILTER]['+listIndex+'][find_el_subsections]"]', filterWrap).is(':checked') ? 1 : 0);
		}
		
		var dialogParams = {
			'title':BX.message("KDA_EE_FIELDS_LIST_SETTINGS_TITLE"),
			'content_url':'/bitrix/admin/'+kdaIEModuleFilePrefix+'_fields_list_settings.php?list_index='+listIndex+'&PROFILE_ID='+form.PROFILE_ID.value,
			'content_post': params,
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
			
			var parent = $('.kda-ee-fl-settings-select', this.DIV);
			$('input[type=button]', parent).attr('disabled', true);
			$('select[name="FIELDS_LIST[]"]', parent).bind('change', function(){
				if(this.value) $('input[name=add]', parent).removeAttr('disabled');
				else $('input[name=add]', parent).attr('disabled', true);
			});
			$('select[name="SHOW_FIELDS_LIST[]"]', parent).bind('change', function(){
				if(this.value) $('input.button', parent).removeAttr('disabled');
				else $('input.button', parent).attr('disabled', true);
				
				var options = $('option', this);
				var values = [];
				for(var i=0; i<options.length; i++)
				{
					values.push(options[i].value);
				}
				$('input[name=NEW_FIELDS_LIST]', parent).val(values.join(';'));
			});
			
			var select2 = $('select[name="SHOW_FIELDS_LIST[]"]', parent);
			var oldFields = $('input[name=OLD_FIELDS_LIST]', parent).val().split(';');
			for(var i=0; i<oldFields.length; i++)
			{
				var option = $('select[name="FIELDS_LIST[]"] option[value="'+oldFields[i]+'"]', parent);
				if(option.length > 0)
				{
					var option2 = $('<option></option>');
					option2.val(option.val());
					option2.html(option.html() + ' - ' + option.closest('optgroup').attr('label'));
					select2.append(option2);
				}
			}
			
			$('input[name=add]', parent).bind('click', function(){
				var selOption = $('option:selected:last', select2);
				var options = $('select[name="FIELDS_LIST[]"] option', parent);
				if(selOption.length > 0) options = $(options.get().reverse());
				options.each(function(){
					if(!this.selected) return;
					var option = $(this);
					var option2 = $('<option></option>');
					option2.val(option.val());
					option2.html(option.html() + ' - ' + option.closest('optgroup').attr('label'));
					if(selOption.length > 0)
					{
						option2.insertAfter(selOption);
					}
					else
					{
						select2.append(option2);
					}
					this.selected = false;
					select2.trigger('change');
				});
			});
			
			$('input[name=del]', parent).bind('click', function(){
				$('select[name="SHOW_FIELDS_LIST[]"] option', parent).each(function(){
					if(!this.selected) return;
					$(this).remove();
					select2.trigger('change');
				});
			});
			
			$('input[name=up]', parent).bind('click', function(){
				$('select[name="SHOW_FIELDS_LIST[]"] option', parent).each(function(){
					if(!this.selected) return;
					var prevOption = $(this).prev('option');
					if(prevOption.length > 0 && !prevOption[0].selected)
					{
						$(this).insertBefore(prevOption);
					}
					select2.trigger('change');
				});
			});
			
			$('input[name=down]', parent).bind('click', function(){
				$($('select[name="SHOW_FIELDS_LIST[]"] option', parent).get().reverse()).each(function(){
					if(!this.selected) return;
					var nextOption = $(this).next('option');
					if(nextOption.length > 0 && !nextOption[0].selected)
					{
						$(this).insertAfter(nextOption);
					}
					select2.trigger('change');
				});
			});
		});
			
		dialog.Show();
	},
	
	SetFieldsListSettings: function(listIndex, fields)
	{
		var wrap = $('.kda-ee-sheet[data-sheet-index='+listIndex+']');
		var setParent = $('.kda-ee-hidden-settings', wrap);
		var parent = $('tr.kda-ee-tbl-titles', wrap);
		var lastCell = $('th:last', parent);
		
		if(fields.length > 0)
		{
			var showCodes = (wrap.attr('data-show-field-codes') == 1);
			var arFields = fields.split(';');
			var allFields = {};
			var options = $('select[name="FIELDS_LIST['+listIndex+']"] option', wrap);
			for(var i=0; i<options.length; i++)
			{
				if(options[i].value.length==0) continue;
				allFields[options[i].value] = $(options[i]).html();
			}
			
			var newFields = [];
			for(var i=0; i<arFields.length; i++)
			{
				if(allFields[arFields[i]])
				{
					var newField = {'name': arFields[i], 'extrasettings': '', 'value': allFields[arFields[i]]};
					var inputs = $('input[name^="SETTINGS[FIELDS_LIST]['+listIndex+']["]', wrap);
					var find = false, j = 0;
					while(j<inputs.length && !find)
					{
						if(inputs[j].value == arFields[i])
						{
							var key = inputs[j].name.replace(/^.*\[(\d+)\]$/, '$1');
							var extraInput = $('input[name="EXTRASETTINGS['+listIndex+']['+key+']"]', wrap);
							if(extraInput.length > 0)
							{
								newField.extrasettings = extraInput.val();
								extraInput[0].name = 'none';
							}
							var nameInput = $('input[name="SETTINGS[FIELDS_LIST_NAMES]['+listIndex+']['+key+']"]', wrap);
							if(nameInput.length > 0)
							{
								newField.value = nameInput.val();
								nameInput[0].name = 'none';
							}
							inputs[j].name = 'none';
							find = true;
						}
						j++;
					}
					newFields.push(newField);
				}
			}
			
			if(newFields.length > 0)
			{
				$('input[name^="SETTINGS[FIELDS_LIST]['+listIndex+']["]', wrap).attr('name', 'none');
				$('input[name^="EXTRASETTINGS['+listIndex+']["]', wrap).attr('name', 'none');
				$('input[name^="SETTINGS[FIELDS_LIST_NAMES]['+listIndex+']["]', wrap).attr('name', 'none');
				for(var i=0; i<newFields.length; i++)
				{
					var input = $('<input type="hidden" name="SETTINGS[FIELDS_LIST]['+listIndex+']['+i+']" value="">');
					input.val(newFields[i].name);
					lastCell.append(input);
					var input2 = $('<input type="hidden" name="EXTRASETTINGS['+listIndex+']['+i+']" value="">');
					input2.val(newFields[i].extrasettings);
					lastCell.append(input2);
					var input3 = $('<input type="hidden" name="SETTINGS[FIELDS_LIST_NAMES]['+listIndex+']['+i+']" value="">');
					var code = '{'+newFields[i].name+'}';
					input3.val(newFields[i].value + (showCodes && newFields[i].value.indexOf(code)==-1 ? ' '+code : ''));
					lastCell.append(input3);
				}
			}
		}

		if(BX.WindowManager.Get())
		{
			BX.WindowManager.Get().Close();
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
	},
	
	Sort: function(link)
	{
		var wrap = $(link).closest('.kda-ee-sheet');
		var setParent = $('.kda-ee-hidden-settings', wrap);
		var th = $(link).closest('th');
		var fieldName = $('input[name^="SETTINGS[FIELDS_LIST]["]', th).val();
		var sortOrder = 'ASC';
		if($(link).hasClass('sort_down')) sortOrder = 'DESC';
		$('input[name^="SETTINGS[SORT]["]', setParent).val(fieldName + '=>' + sortOrder);
		EList.UpdateSheet(wrap);
	},
	
	AddNewList: function(btn, event)
	{
		var copySettings = ((typeof event == 'object') && (event.ctrlKey || event.shiftKey));
		var form = $(btn).closest('form');
		var i = 0;
		while(document.getElementById('kda-ee-sheet-'+i)){i++;}
		//form.append('<input type="hidden" name="SETTINGS[LIST_NAME]['+i+']" value="">');
		if(copySettings)
		{
			var parent = $(btn).closest('.kda-ee-sheet-wrap');
			//var inputs = $('input[name^="SETTINGS["], input[name^="EXTRASETTINGS["]', parent).not('[name^="SETTINGS[FILTER]"]');
			var div = $('<div></div>');
			$('input[name^="SETTINGS["], textarea[name^="SETTINGS["], input[name^="EXTRASETTINGS["]', parent).not('[name^="SETTINGS[FILTER]"], [type="checkbox"]:not(:checked)').each(function(){
				var input = $('<input type="hidden">');
				input.attr('name', $(this).attr('name').replace(/\[\d+\]/, '['+i+']'));
				input.val($(this).val());
				if(input.attr('name')=='SETTINGS[LIST_NAME]['+i+']') input.val('');
				input.appendTo(div);
			});
			parent.after(div);
		}
		else
		{
			$(btn).closest('.kda-ee-sheet-wrap').after('<input type="hidden" name="SETTINGS[LIST_NAME]['+i+']" value="">');
		}
		this.ReloadWithoutSave(form);
	},
	
	MoveList: function(link, direction)
	{
		var wrap = $(link).closest('.kda-ee-sheet-wrap');
		if(direction < 0) var wrap2 = wrap.prev('.kda-ee-sheet-wrap');
		else var wrap2 = wrap.next('.kda-ee-sheet-wrap');
		if(wrap2.length > 0)
		{
			var title1 = $('.kda-ee-title', wrap);
			var inputTitle1 = $('input[name^="SETTINGS[LIST_NAME]"]', title1);
			var title2 = $('.kda-ee-title', wrap2);
			var inputTitle2 = $('input[name^="SETTINGS[LIST_NAME]"]', title2);
			title1.prepend(inputTitle2);
			title2.prepend(inputTitle1);
		}
		form = wrap.closest('form');
		this.ReloadWithoutSave(form);
	},
	
	RemoveList: function(link)
	{
		$(link).closest('.kda-ee-sheet-wrap').remove();
	},
	
	ReloadWithoutSave: function(form)
	{
		$('input[name=saveConfigButton]', form).trigger('click');
	},
	
	ToggleAddSettings: function(input)
	{
		var display = (input.checked ? '' : 'none');
		var tr = $(input).closest('tr');
		var next;
		while((next = tr.next('tr.subfield')) && next.length > 0)
		{
			tr = next;
			if(display=='' && $('select', tr).length > 0 && $('select option', tr).length==0) continue;
			tr.css('display', display);
		}
	},
	
	ToggleAddSettingsBlock: function(link)
	{
		if($(link).hasClass('open'))
		{
			$(link).removeClass('open');
			$(link).next('div').slideUp();
		}
		else
		{
			$(link).addClass('open');
			$(link).next('div').slideDown();
		}
	},
	
	ShowAddTextMenu: function(btn)
	{
		var field = $(btn).prev('textarea');
		this.focusField = field;
		var arLines = [];
		arLines.push({'TEXT':BX.message("KDA_EE_ADD_TEXT_DATE"), 'TITLE':BX.message("KDA_EE_ADD_TEXT_DATE"), 'ONCLICK':'EList.OpenAddTextBlock("DATE")'});
		BX.adminShowMenu(btn, arLines, '');
	},
	
	OpenAddTextBlock: function(code)
	{
		var dialogParams = {
			'title':BX.message("KDA_EE_INSERT_PARAMS"),
			'content_url':'/bitrix/admin/'+kdaIEModuleFilePrefix+'_insert_params.php?code='+code,
			'width':'700',
			'height':'300',
			'resizable':true
		};
		var dialog = new BX.CAdminDialog(dialogParams);
			
		dialog.SetButtons([
			dialog.btnCancel,
			new BX.CWindowButton(
			{
				title: BX.message('KDA_EE_INSERT_BTN'),
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
	
	SetAddTextVal: function(json)
	{
		this.focusField = $(this.sandwichOpened).closest('tr').find('.cell textarea');
		this.focusField.val(this.focusField.val() + json.val);
		
		if(BX.WindowManager.Get())
		{
			BX.WindowManager.Get().Close();
		}
	},
	
	CsvAdaptYandex: function(chb)
	{
		var form = $(chb).closest('form');
		if(chb.checked)
		{
			$('select[name="SETTINGS_DEFAULT[CSV_ENCLOSURE]"]').val('"').attr('disabled', true);
			$('select[name="SETTINGS_DEFAULT[CSV_ENCLOSURE]"]').after('<input type="hidden" name="SETTINGS_DEFAULT[CSV_ENCLOSURE]" value=\'"\'>');
			$('select[name="SETTINGS_DEFAULT[CSV_ENCODING]"]').val('UTF-8').attr('disabled', true);
			$('select[name="SETTINGS_DEFAULT[CSV_ENCODING]"]').after('<input type="hidden" name="SETTINGS_DEFAULT[CSV_ENCODING]" value="UTF-8">');
		}
		else
		{
			$('select[name="SETTINGS_DEFAULT[CSV_ENCLOSURE]"]').removeAttr('disabled');
			$('select[name="SETTINGS_DEFAULT[CSV_ENCLOSURE]"]').next('input[name="SETTINGS_DEFAULT[CSV_ENCLOSURE]"]').remove();
			$('select[name="SETTINGS_DEFAULT[CSV_ENCODING]"]').removeAttr('disabled');
			$('select[name="SETTINGS_DEFAULT[CSV_ENCODING]"]').next('input[name="SETTINGS_DEFAULT[CSV_ENCODING]"]').remove();
		}
	}
}

var EProfile = {
	Init: function()
	{
		var select = $('select#PROFILE_ID');
		if(select.length > 0)
		{
			if(typeof select.chosen == 'function')
			{
				setTimeout(function(){$('select#PROFILE_ID').chosen({search_contains: true})}, 500);
			}
			if(select.val().length > 0)
			{
				$.post(window.location.href, {'MODE': 'AJAX', 'ACTION': 'DELETE_TMP_DIRS'}, function(data){});
			}
			
			select = select[0]
			/*this.Choose(select[0]);*/
			$('#new_profile_name').css('display', (select.value=='new' ? '' : 'none'));
		
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
					eval('var res = '+data+';');
					if(res.isOffers && parseInt(res.isOffers) > 0)
					{
						$('.kda-sku-block.heading').show();
					}
					else
					{
						$('.kda-sku-block').hide();
						$('.kda-sku-block.heading .kda-head-more').removeClass('show');
					}
				});
			});
			
			var select = $('select[name="SETTINGS_DEFAULT[ELEMENT_UID][]"]');
			if(select.length > 0 && !select.val()) select[0].options[0].selected = true;
			if(typeof $('select.kda-chosen-multi').chosen == 'function')
			{
				$('select.kda-chosen-multi').chosen({width: '300px'});
			}
			this.ToggleAdditionalSettings();
			this.ToggleCsvSettings();
			KdaEeBx24.Init();
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
		if(typeof select.chosen == 'function')
		{
			$('select#PROFILE_ID').trigger("chosen:updated");;
		}
		
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
		if(link) link = $(link);
		else link = $('.kda-head-more');
		if(link.length==0) return;
		$(link).each(function(){
			var tr = $(this).closest('tr');
			var show = $(this).hasClass('show');
			while((tr = tr.next('tr:not(.heading)')) && tr.length > 0)
			{
				if(show) tr.hide();
				else tr.show();
			}
			if(show) $(this).removeClass('show');
			else $(this).addClass('show');
		});
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

var KdaEeBx24 = {
	url: '',
	inputId: 'bx24_folder_id',
	pathInputId: 'bx24_folder_path',
	parentId: 'bx24_folder_struct',	
	
	Init: function()
	{
		if(!document.getElementById('bx24_rest_url')) return;
		var obj = this;
		$('#bx24_help_link').bind('click', function(){
			var title = $(this).text();
			title = title.substr(0, 1).toUpperCase() + title.substr(1);
			var dialog = new BX.CAdminDialog({
				'title':title,
				'content':$('#bx24_help').html(),
				'width':'900',
				'height':'450',
				'resizable':true});				
			dialog.Show();
		});
		$('#bx24_rest_url').bind('change', function(){
			$('#'+obj.parentId).html('<select></select>');
			var url = $.trim(this.value);
			obj.url = url;
			if(url.length > 0)
			{
				$('#'+obj.parentId).html('');
				if(url.substr(url.length-1)!='/') url = url+'/';
				$.ajax({
					url: url+'disk.storage.getlist/',
					dataType: 'json',
					data: {'filter': {'MODULE_ID': 'disk', 'ENTITY_TYPE': 'common'}},
					success: function(data){
						if(typeof data!='object' || !data.result) return;
						var inp = $('#'+obj.inputId);
						var name = inp.attr('name');
						var value = inp.val();
						for(var i=0; i<data.result.length; i++)
						{
							if(data.result[i].ENTITY_TYPE!='common') continue;
							$('#'+obj.parentId).attr('data-storage-id', data.result[i].ID);
							var curValue = 'storage_'+data.result[i].ID;
							var item = $('<div class="kda-ee-bx24-struct-item"><input type="checkbox" name="bx24_folder_chb" value="'+curValue+'" onclick="KdaEeBx24.SetFolder(this)" '+(curValue==value ? 'checked' : '')+'><a href="javascript:void(0)" onclick="KdaEeBx24.ShowNextLevel(this)" data-folder-id="'+curValue+'" data-folder-path="'+curValue+'"><span>'+data.result[i].NAME+'</span></a></div>');
							$('#'+obj.parentId).append(item);
							$('a', item).trigger('click');
						}
					}
				});
			}
		}).trigger('change');
	},
	
	SetFolder: function(chb)
	{
		$('#'+this.parentId+' input[type=checkbox]').not(chb).removeAttr('checked');
		$('#'+this.inputId).val(chb.value);
		$('#'+this.pathInputId).val($(chb).closest('.kda-ee-bx24-struct-item').find('a').attr('data-folder-path'));
	},
	
	ShowNextLevel: function(link)
	{
		var obj = this;
		link = $(link);
		var fullId = link.attr('data-folder-id');
		var fullPath = link.attr('data-folder-path');
		//$('#'+this.inputId).val(fullId);
		
		var parentWrap = link.closest('.kda-ee-bx24-struct-item');
		var parentDiv = $('>.kda-ee-bx24-struct-list', parentWrap);
		if(parentDiv.length > 0)
		{
			parentDiv.toggleClass('kda-ee-bx24-struct-list-hidden');
			return;
		}
		parentDiv = $('<div class="kda-ee-bx24-struct-list"></div>');
		parentDiv.appendTo(parentWrap);
		
		var id = fullId.substr(fullId.indexOf('_')+1);
		var methodPath = this.url+'disk.storage.getchildren/';
		if(fullId.indexOf('folder_')==0)
		{
			methodPath = this.url+'disk.folder.getchildren/';
		}
		$.ajax({
			url: methodPath,
			dataType: 'json',
			data: {'id': id, 'filter': {'TYPE': 'folder'}},
			success: function(data){
				if(typeof data!='object' || !data.result) return;
				var item, curValue, fullCurValue, 
					fullValue = $('#'+obj.pathInputId).val(),
					value = $('#'+obj.inputId).val();
				for(var i=0; i<data.result.length; i++)
				{
					curValue = 'folder_'+data.result[i].ID;
					fullCurValue = fullPath+','+curValue;
					item = $('<div class="kda-ee-bx24-struct-item"><input type="checkbox" name="bx24_folder_chb" value="'+curValue+'" onclick="KdaEeBx24.SetFolder(this)" '+(curValue==value ? 'checked' : '')+'><a href="javascript:void(0)" onclick="KdaEeBx24.ShowNextLevel(this)" data-folder-id="'+curValue+'" data-folder-path="'+fullCurValue+'">'+data.result[i].NAME+'</a></div>');
					$(parentDiv).append(item);
					if(fullValue.indexOf(fullCurValue+',')==0 && fullCurValue!=fullValue)
					{
						$('a', item).trigger('click');
					}
				}

				$('#'+obj.parentId+' input[type=checkbox]:not([data-init])').each(function(){
					$(this).attr('data-init', 1);
					BX.adminFormTools.modifyCheckbox(this);
				});
			}
		});
	}
}

var EProfileList = {
	ShowRestoreWindow: function()
	{
		var dialogParams = {
			'title':BX.message("KDA_EE_POPUP_RESTORE_PROFILES_TITLE"),
			'content_url':'/bitrix/admin/'+kdaIEModuleFilePrefix+'_restore_profiles.php',
			'width':'700',
			'height':'300',
			'resizable':true
		};
		var dialog = new BX.CAdminDialog(dialogParams);
		this.restoreDialog = dialog;
		this.RestoreDialogButtonsSet();		
			
		BX.addCustomEvent(dialog, 'onWindowRegister', function(){
			$('input[type=checkbox]', this.DIV).each(function(){
				BX.adminFormTools.modifyCheckbox(this);
			});
		});
			
		dialog.Show();
	},
	
	RestoreDialogButtonsSet: function(fireEvents)
	{
		var dialog = this.restoreDialog;
		dialog.SetButtons([
			dialog.btnCancel,
			new BX.CWindowButton(
			{
				title: BX.message('KDA_EE_POPUP_RESTORE_PROFILES_SAVE_BTN'),
				id: 'savebtn',
				name: 'savebtn',
				className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
				action: function () {
					var btn = this;
					btn.disable();
					
					$.ajax({
						url: '/bitrix/admin/'+kdaIEModuleFilePrefix+'_restore_profiles.php',
						type: 'POST',
						data: (new FormData(document.getElementById('restore_profiles'))),
						mimeType:"multipart/form-data",
						contentType: false,
						cache: false,
						processData:false,
						success: function(data, textStatus, jqXHR)
						{
							if(data && data.substr(0, 1)=='{' && data.substr(data.length-1)=='}')
							{
								eval('var result = '+data+';');
							}
							else
							{
								var result = false;
							}
							
							if(typeof result == 'object')
							{
								if(result.MESSAGE) alert(result.MESSAGE);
								if(result.TYPE=='SUCCESS')
								{
									setTimeout(function(){
										window.location.href = window.location.href;
									}, 3000);
								}
							}
							btn.enable();
						},
						error: function(data, textStatus, jqXHR)
						{
							btn.enable();
						}
					});
				}
			})
		]);
		
		if(fireEvents)
		{
			BX.onCustomEvent(dialog, 'onWindowRegister');
		}
	},
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
				//result.total_read_line = result.total_file_line;
				result.total_file_line = result.total_read_line;
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
				var timeBlock = document.getElementById('kda_ee_auto_continue_time');
				if(timeBlock)
				{
					timeBlock.innerHTML = '';
					obj.TimeoutOnAutoConinue();
				}
			},
			timeout: (post.STEPS_TIME ? ((Math.min(3600, post.STEPS_TIME) + 120) * 1000) : 180000)
		});
	},
	
	TimeoutOnAutoConinue: function()
	{
		var obj = this;
		var timeBlock = document.getElementById('kda_ee_auto_continue_time');
		var time = timeBlock.innerHTML;
		if(time.length==0)
		{
			timeBlock.innerHTML = 30;
		}
		else
		{
			time = parseInt(time) - 1;
			timeBlock.innerHTML = time;
			if(time < 1)
			{
				//$('#kda_ie_continue_link').trigger('click');

				$.ajax({
					type: "POST",
					url: window.location.href,
					data: {'MODE': 'AJAX', 'PROCCESS_PROFILE_ID': obj.pid, 'ACTION': 'GET_PROCESS_PARAMS'},
					success: function(data){
						if(data && data.substr(0, 1)=='{' && data.substr(data.length-1)=='}')
						{
							try {
								eval('var params = '+data+';');
							} catch (err) {
								var params = false;
							}
							if(typeof params == 'object')
							{
								obj.params = params;
							}
						}
						$('#block_error_import').hide();
						obj.errorStatus = false;
						obj.SendDataSecondary();
					},
					error: function(){
						timeBlock.innerHTML = '';
						obj.TimeoutOnAutoConinue();
					}
				});
				return;
			}
		}
		setTimeout(function(){obj.TimeoutOnAutoConinue();}, 1000);
	},
	
	SendDataSecondary: function()
	{
		var obj = this;
		if(this.post.STEPS_DELAY)
		{
			setTimeout(function(){
				obj.SendData();
			}, parseInt(this.post.STEPS_DELAY) * 1000);
		}
		else
		{
			obj.SendData();
		}
	},
	
	OnLoad: function(data)
	{
		data = $.trim(data);
		if(data.indexOf('{')!=0)
		{
			if(data.indexOf("'bitrix_sessid':'")!=-1)
			{
				var sessid = data.substr(data.indexOf("'bitrix_sessid':'") + 17);
				sessid = sessid.substr(0, sessid.indexOf("'"));
				if(sessid.length > 0) this.post.sessid = sessid;
			}
			else if(data.indexOf(".settings.php")!=-1 || data.indexOf("[Error]")!=-1 || data.indexOf("MySQL Query Error")!=-1)
			{
				$('#block_error').show();
				$('#res_error').append('<div>'+data+'</div>');
			}
			var obj = this;
			setTimeout(function(){obj.SendDataSecondary();}, 5000);
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
				this.post.sessid = result.sessid;
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
				this.SendDataSecondary();
				return true;
			}
		}
		else
		{
			this.SendDataSecondary();
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
	
	BindConversionEvents: function()
	{
		$('.kda-ee-settings-conversion').each(function(){
			var parent = this;
			$('select.field_cell', parent).bind('change', function(){
				if(this.value=='ELSE')
				{
					$('select.field_when', parent).hide();
					$('input.field_from', parent).hide();
				}
				else
				{
					$('select.field_when', parent).show();
					$('input.field_from', parent).show();
				}
			}).trigger('change');
		});
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
		ESettings.BindConversionEvents();
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