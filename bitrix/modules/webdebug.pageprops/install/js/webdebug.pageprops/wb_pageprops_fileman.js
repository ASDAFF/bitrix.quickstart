function WD_OnReady(callback){
	var addListener = document.addEventListener || document.attachEvent,
			removeListener = document.removeEventListener || document.detachEvent,
			eventName = document.addEventListener ? "DOMContentLoaded" : "onreadystatechange";
	addListener.call(document, eventName, function(){
		if (document.removeEventListener) {
			document.removeEventListener(eventName,arguments.callee,false);
		} else if (document.detachEvent) {
			document.detachEvent(eventName,arguments.callee,false);
		}
		callback();
	}, false);
}

if (!String.WD_Prop_Format) {
  String.WD_Prop_Format = function(format) {
    var args = Array.prototype.slice.call(arguments, 1);
    return format.replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[number] != 'undefined' ? args[number]  : match ;
    });
  };
}

var WD_Prop_Popup;
function WD_Prop_ShowPopup(Site, PropCode, PropName) {
	BX.showWait();
	var PopupHead = String.WD_Prop_Format(WD_Prop_Mess['POPUP_HEAD_1'], PropCode, PropName, Site);
	if(Site!='com') {
		PopupHead += String.WD_Prop_Format(WD_Prop_Mess['POPUP_HEAD_2'], PropCode, PropName, Site);
	}
	WD_Prop_Popup = new BX.CDialog({
		title: WD_Prop_Mess['POPUP_TITLE'],
		head: PopupHead,
		content: '',
		width: 900,
		height: 400,
		resizable: true,
		draggable: true
	});
	BX.addCustomEvent(WD_Prop_Popup, 'onWindowClose', function(){
		$(this.DIV).remove();
	});
	WD_Prop_Popup.SetButtons([{
			title: WD_Prop_Mess['POPUP_SAVE'],
			id: 'wd_pageprops_save_form_button',
			name: 'action_send',
			className: 'adm-btn-save',
			action: function(){
				BX.showWait();
				BX.ajax.submit(BX('wd_pageprops_settings_form'), function(data){
					if (data.indexOf('#WD_PAGEPROPS_SAVE_ERROR#')>-1) {
						alert(WD_Prop_Mess['SAVE_ERROR']);
					} else {
						WD_Prop_Popup.Close();
					}
				});
			}
		}, {
			title: WD_Prop_Mess['POPUP_CANCEL'],
			id: 'wd_pageprops_save_form_cancel',
			name: 'cancel',
			action: function(){
				WD_Prop_Popup.Close();
			}
		}
	]);
	WD_Prop_Popup.SetContent('');
	jsAjaxUtil.LoadData('/bitrix/admin/wd_pageprops_edit.php?&lang='+phpVars['LANGUAGE_ID']+'&prop_site='+Site+'&prop_code='+PropCode+'&' + Math.random(), function(data){
		WD_Prop_Popup.SetContent(data);
		WD_Prop_Popup.Show();
		BX.closeWait();
	});
}

function WD_Init_PagePropsManagement() {
	$('#edit1_edit_table tr[id$=Propery]').each(function(){
		var SiteID = $(this).attr('id').substr(0,3);
		if (SiteID.substr(2,1)=='_') {
			SiteID = SiteID.substr(0,2);
		}
		$(this).find('td.adm-detail-content-cell-r > table').each(function(){
			if ($(this).find('input[name^=propstypes]').size()>0) {
				var Table = $(this);
				Table.find('tr').append('<td class="wd_pageprops_customize"></td>').not('.heading').each(function(){
					var PropCode = $.trim($(this).find('td').eq(0).find('input[type=text]').eq(0).val());
					if (PropCode!='') {
						$(this).find('td input[type=text]').each(function(){
							$(this).attr('data-value',$(this).val());
						});
						$(this).find('.wd_pageprops_customize').append('<a href="#" data-site="'+SiteID+'" title="'+WD_Prop_Mess['EDIT_LINK_TITLE']+'"></a>').find('a').click(function(Event){
							
							Event.preventDefault();
							var PropCode = $.trim($(this).parent().parent().find('td').eq(0).find('input[type=text]').eq(0).attr('data-value'));
							var PropName = $.trim($(this).parent().parent().find('td').eq(1).find('input[type=text]').eq(0).attr('data-value'));
							var Site = $(this).attr('data-site');
							if (PropCode!='') {
								WD_Prop_ShowPopup(Site, PropCode, PropName);
							}
						});
					}
				});
			}
		});
	});
}

WD_OnReady(function(){
	if(!window.jQuery) {
		 var script = document.createElement('script');
		 script.type = "text/javascript";
		 script.src = "/bitrix/js/main/jquery/jquery-1.8.3.min.js";
		 document.getElementsByTagName('head')[0].appendChild(script);
	}
	if ('jQuery' in window) {
		WD_Init_PagePropsManagement();
	} else {
		var t = setInterval(function() {
			if ('jQuery' in window) {
				WD_Init_PagePropsManagement();
				clearInterval(t);
			}
		}, 50);
	}
});

