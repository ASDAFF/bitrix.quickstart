/* Caching */
try {document.execCommand("BackgroundImageCache", false, true);} catch(err) {}

function is_defined(variable)
{
	return (typeof(window[variable]) == "undefined") ? false: true;
}
function isset(variable)
{
	return (typeof(variable) == "undefined") ? false: true;
}
function is_empty(variable)
{
	if ( variable == null || ! isset(variable) )	{return true;}
	if ( variable == 0 || variable == '' )			{return true;}
	return false;
}
var app = {
	closeBox: function( element ) {
		$(element).closest('.annotation').remove();
		return false;
	},
	showDialog: function(content, title, width, height)
	{
		var width = width ||  '300';
		var height = height || '150'
		
		var Dialog = new BX.CDialog({
			title: title,
			content: content,
			icon: 'head-block',
			resizable: false,
			draggable: false,
			height: height,
			width: width,
			buttons: [BX.CDialog.btnClose]
		});
		Dialog.Show();
	},
	annotation: function( element ) {
		var div = '', text = '', intext = '', addclass = '', count = 0, flag = 0;
		
		$(element).closest('.library-products').find('.item-col').removeClass('flag-mark');
		
		$(element).closest('.item-col').addClass('flag-mark');
		$(element).closest('.library-products').find('.item-col').each(function(){
			if( !$(this).hasClass('flag-mark') )
			{
				count ++;
			}
			else {
				flag = count;
			}
		});
		
		if( flag == 3 )
		{
			addclass = ' annotation-rotate';
		}
		
		
		intext = $(element).closest('.item').find('.annotation_content').html();
		text += '<p>'+intext+'</p>';

		
		div += '<div class="annotation' + addclass + '"><div class="annotation-inner">';
		
		div += '<a href="#" onclick="return app.closeBox(this)" class="close"></a>';
		div += '<div class="scrollable" id="scrollbarY">';
		div += '<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>';
		
		div += '<div class="viewport"><div class="overview">';
		div += text;
		
		
		div += '</div></div></div>';
		div += '</div></div>';
		
		$('.library-products').find('.annotation').remove();
		$(element).closest('.group').append( div );
		
		$('#scrollbarY').tinyscrollbar({
			axis: 'y',
			sizethumb: 24,
			scroll: true
		});
		
		return false;
	},
	initTab: function() {
		if( location.hash != '' ) {
			var element = {};
			element.href = location.hash;
			this.opentab( element );
		}
	},
	opentab: function( element ) {
		var tab = element.href.split('#')[1];
	
		if( !$('#tab-'+tab).hasClass('tab-active') ) {
			location.hash = tab;
			
			$('#tab-conteiner').find('.index').removeClass('index-active');
			
			$('#tab-conteiner').find('.tab').removeClass('tab-current');
			$('#tab-conteiner').find('.tab-content').removeClass('tab-active');
			
			$('#tab-group').find('a[href="#' + tab + '"]').addClass('tab-current');
			$('#tab-'+tab).addClass('tab-active');
		}
		return false;
	},
	checkbox: function( element )
	{
		if( $(element).find('input').attr('checked') === true || $(element).find('input').attr('checked') == 'checked' )
		{
			$(element).find('.checkbox').addClass('checkbox-checked');
			$(element).addClass('checkbox-label-checked');
			$(element).find(':checkbox').attr('checked', "checked").val("1");
		}
		else
		{
			$(element).find('.checkbox').removeClass('checkbox-checked');
			$(element).removeClass('checkbox-label-checked');
			$(element).find(':checkbox').val("0");
		}
		
	},
	onCheckbox: function( id )
	{
		if( $('#' + id).find('.checkbox-label').length > 0 )
		{
			$('#' + id).find('.checkbox-label').each(function(){
				if( $(this).find('input').attr('checked') === true || $(this).find('input').attr('checked') == 'checked' )
				{
					$(this).find('.checkbox').addClass('checkbox-checked');
					$(this).addClass('checkbox-label-checked');
				}
				else
				{
					$(this).find('.checkbox').removeClass('checkbox-checked');
					$(this).removeClass('checkbox-label-checked');
				}
			});
		}
	},
	loadAsync: function(url, params, container, callback)
	{
		$("#"+container).prepend('<div id="loader" class="loader">'+BX.message('ML_LIB_JS_LOADING')+'</div>').show();
		$.ajax({
			type: "GET",
			url: url,
			data: params,
			dataType: "json",
			success: function(data, textStatus) { 
				//$('#'+container).html(msg);
				//console.log(typeof data);
				//console.log(data.message);
				if(!is_empty(data.new_html))
				{
					$("#"+container).html(data.new_html);
				}	
				if(!is_empty(data.message))
				{
					app.showDialog(data.message, BX.message('ML_LIB_JS_BOOKING'), '300', '50');
				}
				$("#"+container).find("#loader").remove();
				
			},
			error: function(msg, why) 	{alert (BX.message('ML_LIB_JS_REASON')+' '+msg+'. '+BX.message('ML_LIB_JS_ERROR')+': '+why);}
		});

		if (typeof(callback) != 'undefined')	{eval(callback);}
		return false;
	}
	
};

$(document).ready(function(){

	app.onCheckbox('sort-1');
	app.onCheckbox('sort-2');
	app.initTab();
	
	$('.library').mouseenter(function(){
		$(this).addClass('library-hover');
	});
	
	$('.library').mouseleave(function(){
		$(this).removeClass('library-hover');
	});
	
	if( $('.selectable').length > 0 ) {
		$('.selectable').selectbox();
	}
	$('form.liberiya_filter select, form.liberiya_filter :checkbox').bind('change', function(){
		console.log('change');
		$(this).closest('form').submit();
	});
	
});