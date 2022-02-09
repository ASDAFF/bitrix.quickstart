__logOnDateChange = function(sel)
{
	var bShowFrom=false, bShowTo=false, bShowHellip=false, bShowDays=false, bShowBr=false;

	if(sel.value == 'interval')
		bShowBr = bShowFrom = bShowTo = bShowHellip = true;
	else if(sel.value == 'before')
		bShowTo = true;
	else if(sel.value == 'after' || sel.value == 'exact')
		bShowFrom = true;
	else if(sel.value == 'days')
		bShowDays = true;
	
	BX.findNextSibling(sel, {'tag':'span', 'class':'sonet-log-filter-date-from-span'}).style.display = (bShowFrom? '':'none');
	BX.findNextSibling(sel, {'tag':'span', 'class':'sonet-log-filter-date-to-span'}).style.display = (bShowTo? '':'none');
	BX.findNextSibling(sel, {'tag':'span', 'class':'sonet-log-filter-date-hellip-span'}).style.display = (bShowHellip? '':'none');
	BX.findNextSibling(sel, {'tag':'span', 'class':'sonet-log-filter-date-days-span'}).style.display = (bShowDays? '':'none');
	var span = BX.findNextSibling(sel, {'tag':'span', 'class':'sonet-log-filter-date-br-span'});
	if(span)
		span.style.display = (bShowBr? '':'none');
}

function onFilterGroupSelect(arGroups)
{
	if (arGroups[0])
	{
		document.forms["log_filter"]["flt_group_id"].value = arGroups[0].id;
		BX.removeClass(BX("filter-field-group").parentNode, "webform-field-textbox-empty");
	}
}

function __logFilterClick(featureId)
{
	var chkbx = document.getElementById("flt_event_id_"+featureId);
	var chkbx_tmp = false;

	var bIsAllChecked = true;
	
	for(var flt_cnt in arFltFeaturesID)
	{
		chkbx_tmp = document.getElementById("flt_event_id_"+arFltFeaturesID[flt_cnt]);
		if (null != chkbx_tmp)
		{
			if (chkbx_tmp.checked == false)
			{
				bIsAllChecked = false;
				break;
			}
		}
	}

	chkbx_tmp = document.getElementById("flt_event_id_all");	
	if (bIsAllChecked)
		chkbx_tmp.value = "Y";
	else
		chkbx_tmp.value = "";	
}

function __logFilterShow()
{
	if (BX('bx_sl_filter').style.display == 'none')
	{
		BX('bx_sl_filter').style.display = 'block';
		BX('bx_sl_filter_hidden').style.display = 'none';
	}
	else
	{
		BX('bx_sl_filter').style.display = 'none';
		BX('bx_sl_filter_hidden').style.display = 'block';
	}
}

function __logFilterCreatedByChange(event)
{
	event = event || window.event;
	var target = event.target || event.srcElement;

	if (BX("flt_comments_cont"))
	{
		setTimeout(function() {
			if (target.value.length <= 0)	
				BX("flt_comments_cont").style.visibility = "hidden";
			else
				BX("flt_comments_cont").style.visibility = "visible";
		}, 150);
	}
}