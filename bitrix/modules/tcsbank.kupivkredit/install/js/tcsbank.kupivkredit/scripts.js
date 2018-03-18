function TKSSendRequest(sClass)
{
	var arTR = $("."+sClass+".data");
	var trMessage = $("."+sClass+".message");
	var Data = [];
	arTR.find("input,textarea").each(function()
	{
		Data[Data.length] = {"name":$(this).attr("name"), "value":$(this).val()};
	});

	$.ajax({
		url:"/bitrix/admin/tcsbank_send.php",
		type:"post",
		data: Data,
		dataType: "json",
		beforeSend:function()
		{
			arTR.find("input,textarea").attr("disabled","y");
		},
		success: function(data)
		{
			arTR.find("input,textarea").removeAttr("disabled");
			trMessage.removeClass("error success hidden").addClass(data.status).find("td div").html(data.message);
			if(data.status=="success")
			{
				arTR.addClass("hidden");
			}
		}
	});
}

jQuery.download = function(url, data, method){
	if( url && data ){ 
		data = typeof data == 'string' ? data : jQuery.param(data);
		var inputs = '';
		jQuery.each(data.split('&'), function(){ 
			var pair = this.split('=');
			inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
		.appendTo('body').submit().remove();
	};
};	
$.fn.tcs_block = function(options)
{
	var def = {};
	def.background = "#FFF";
	def.message = "<span class = 'dTCSLoader'></span>";
	def.duration = 100;
	def.opacity = 0.65;
	
	if(options)
	{
		for(i in options) def[i] = options[i];
	}
	var Wrapper = $(".dBlockWrapper");
	if(!Wrapper.length) Wrapper = $("<div class = 'dBlockWrapper'><div class = 'dBlockBody'></div></div>");
	var dBody = Wrapper.find(".dBlockBody");
	dBody.empty();
	dBody.append(def.message);
	var ToWrap = $(this);
	dBody.css({
		"width":ToWrap.outerWidth(),
		"height":ToWrap.outerHeight(),
		"text-align":"center",
		"vertical-align":"middle",
		"display":"table-cell"
	});
	Wrapper.css({
		"background":def.background,
		"z-index":"100",
		"opacity":0,
		"position":"absolute",
		"width":ToWrap.outerWidth(),
		"height":ToWrap.outerHeight()
	});
	ToWrap.prepend(Wrapper);
	Wrapper.animate({opacity:def.opacity}, def.duration);
	return Wrapper;
}
$.fn.tcs_unblock = function(options)
{
	var Wrapper = $(".dBlockWrapper");
	var duration = 100;
	if(options)
	{
		if(options.duration) duration = options.duration;
	}
	if(Wrapper.length)
	{
		Wrapper.animate({opacity:0},duration,function()
		{
			$(this).remove();
		});
	}
}


function ReformOrder(iOrderID,iDownPayment, iPaymentCount, aLink)
{
	
	data = {"ID":iOrderID, "DOWN_PAYMENT":iDownPayment, "PAYMENT_COUNT":iPaymentCount, "TYPE":"reform"};
	if(window.confirm($(aLink).attr("confirm")))
	{
		MakeRequest(data);
	}
	return false;
}

function ReformRequest(obj)
{
	Form = $(this).parents("form:first");
	MakeRequest(Form);

}

function UpdateOrder()
{
	$.ajax({
		type:"post",
		url:"/bitrix/admin/tcsbank_iframe.php",
		data:$(".fUpdateOrder").serialize(),
		beforeSend:function()
		{
			ShowWaitWindow();
		},
		success:function(data)
		{
			CloseWaitWindow();
			//dOrderFull = $("<div/>").append(data).find(".dOrderFull");
			$(".dOrderFull").html(data);
		}
	
	});
	

}
var ASD;
function MakeRequest(arData, addon)
{

	$.ajax({
		type:"post",
		url:"/bitrix/admin/tcsbank_request.php",
		data:arData,
		dataType:"json",
		beforeSend:function()
		{
			ShowWaitWindow();
			$(".dOrderDetail."+arData.ID).tcs_block();
		},
		success:function(data)
		{
			/* ASD=data;
			return; */
			CloseWaitWindow();
			$(".dOrderDetail."+data.ID).tcs_unblock();
			if(data.status=="ok")
			{
				
				if(data.show_document)
				{
					$.download("/bitrix/admin/tcsbank_get_file.php",data, "post");
					window.setTimeout(function(){ ShowOrder(data.ID); }, 2000);
				}
				else 
				{
					ShowOrder(data.ID, null, true);
					$(".bx-core-dialog-overlay, .bx-core-dialog").remove();		
				}
			}
			else
			{
				iOrderID = (data.ID);
				if(!iOrderID)
				{
					iOrderID = addon;
				}
				$(".fUpdateOrder .dError").html(data.message);
				var dOrderDetail = $(".dOrderDetail."+iOrderID);
				dOrderDetail.find(".errortext").html(data.message);
			}
			
		}
	});

}

function ApplyContract(arData)
{
	var iOrderID = arData.ID;
	var dOrderDetail = $(".dOrderDetail."+iOrderID);
	var iInputCount = dOrderDetail.find("input[name=courier_mode]").length;
	var sCourierMode="";
	if(iInputCount==1)
	{
		sCourierMode = dOrderDetail.find("input[name=courier_mode]").val();
	}
	else
	{
		if(iInputCount==2)
		{
			sCourierMode = dOrderDetail.find("input[name=courier_mode]:checked").val();
		}
	}
	
	if(!sCourierMode.length)
	{
		alert(TCSAlerts.TCS_NO_COURIER);
		return false;
	}
	arData.COURIER_MODE=sCourierMode;
	MakeRequest(arData);
}
function ChooseCourier(obj, mode)
{
	dLabels = $(obj).parents(".dLabels:first");

	dLabels.find(".dLinks a").removeClass("active");
	dLabels.find(".dLinks span[courier_mode="+mode+"] a").addClass("active");
	dLabels.parents(".dOrderDetail:first").find(".dApplyContract a.choose").addClass("show");
	dLabels.parents(".dOrderDetail:first").find(".dApplyContract span.choose").removeClass("show");
}
function ReturnOrder(iOrderID, aLink)
{
	var Div = $(aLink).parents(".dReturnForm:first");
	iReturnedAmount = Div.find("input[name=RETURNED_AMOUNT]").val();
	iCashReturnedToCustomer = Div.find("input[name=CASH_RETURNED_TO_CUSTOMER]").val();
	MakeRequest({"TYPE":"return","ID":iOrderID, "RETURNED_AMOUNT":iReturnedAmount, "CASH_RETURNED_TO_CUSTOMER":iCashReturnedToCustomer});
	Div.hide();
}

function CancelDocument(iOrderID, obj)
{
	var Div = $(obj).parents(".dDeclineForm:first");
	var Input = Div.find("input:checked");
	sReason = Div.find("select option:selected").val();
	MakeRequest({"TYPE":"subscribe","ID":iOrderID,"SUBSCRIBE":0, "REASON":sReason});
	Div.hide();
}

function SubscribeDocument(iOrderID, obj)
{
	var Div = $(obj).parents(".dContractResult:first");
	var Input = Div.find("input:checked");
	if(parseInt(Input.val()))
	{
		MakeRequest({"TYPE":"subscribe","ID":iOrderID,"SUBSCRIBE":1});
	}
	else
	{
		sReason = Div.find("select option:selected").val();
		MakeRequest({"TYPE":"subscribe","ID":iOrderID,"SUBSCRIBE":0, "REASON":sReason});
	}
}

function ShowDecline(bShow, obj)
{
	dDecline = $(obj).parents("td:first").find(".dDecline");
	if(bShow)
	{
		dDecline.slideDown(150);
	}
	else dDecline.slideUp(150);
}

function RefreshRow(iOrderID)
{
	$.ajax({
		type:"post",
		url:"/bitrix/admin/tcsbank_get_order.php",
		data:{"ID":iOrderID},
		beforeSend:function()
		{
			$(".dOrderDetail."+iOrderID).tcs_block();
			ShowWaitWindow();
		},
		success:function(data)
		{
			$(".dOrderDetail."+iOrderID).tcs_unblock();
			Tr = $("#iOrderID"+iOrderID).parents("tr:first");
			newTr = $(data).find(".dAjaxTr #iOrderID"+iOrderID).parents("tr:first");
			Tr.find("td").each(function(a,b)
			{
				$(b).html(newTr.find("td:eq("+a+")").html());
			});
			var dOrderDetail = $(".dOrderDetail."+iOrderID);
			if(dOrderDetail.length)
			{
				dOrderDetail.html($(data).find(".dAjaxDiv").html());
			}
			CloseWaitWindow();
		}
	});	

}
function CloseDiv(iOrderID)
{
	var dOrderDetail = $(".dOrderDetail."+iOrderID);
	if(dOrderDetail.length)
	{
		var Tr = dOrderDetail.parents("tr:first");
		Tr.remove();
		//dOrderDetail.slideUp(400,function(){Tr.remove});
	}
}
function ShowOrder(iOrderID)
{
	var Tr=$("#iOrderID"+iOrderID).parents("tr:first");
	$.ajax({
		type:"post",
		url:"/bitrix/admin/tcsbank_get_order.php",
		data:{"ID":iOrderID},
		beforeSend:function()
		{
			$(".dOrderDetail."+iOrderID).tcs_block();
			ShowWaitWindow();
		},
		success:function(data)
		{
			var dOrderDetail = $(".dOrderDetail."+iOrderID);
			dOrderDetail.tcs_unblock();
			if(!dOrderDetail.length)
			{
				var Table = Tr.parents("table:first");
				var Colspan = Tr.find(">td").length-2;
				Tr.after("<tr><td colspan='2'><a class = 'aClose' href = 'javascript:CloseDiv("+iOrderID+")'>"+sCloseText+"</a></td><td class = 'left right' colspan='"+Colspan+"' style = 'padding:0px!important'><div class = 'dOrderDetail "+iOrderID+"'></div></td></tr>");
				var dOrderDetail = Tr.next().find(".dOrderDetail");
			}			
			dOrderDetail.html($(data).find(".dAjaxDiv").html());
			newTr = $(data).find(".dAjaxTr #iOrderID"+iOrderID).parents("tr:first");;
			Tr.find("td").each(function(a,b)
			{
				$(b).html(newTr.find("td:eq("+a+")").html());
			});
			CloseWaitWindow();
		}
	});
}

function FillComment(button, iOrderID)
{
	var bSend = $(button);
	var sText = bSend.parents(".dComment:first").find("textarea").val();
	var arData = {"TYPE":"comment","ID":iOrderID,"TEXT":sText};
	$.ajax({
		type:"post",
		url:"/bitrix/admin/tcsbank_request.php",
		data:arData,
		dataType:"json",
		beforeSend:function()
		{
			ShowWaitWindow();
			bSend.attr("disabled","disabled");
		},
		success:function(data)
		{
			CloseWaitWindow();
			if(data.status=="ok")
			{
				bSend.removeAttr("disabled");
			}
			else
			{
				alert(data.message);
			}
		}
	});
	return false;
}
function CleanSelect(Select)
{
	count = Select.options.length;
	for(i=0; i<count; i++) Select.removeChild(Select.options[0]);
}

function PropertyTypeChange(Select,iPersonType)
{
	
	ID = Select.id;
	ChildSelect = document.getElementById(ID.replace("[TYPE]","[VALUE]"));
	InputAnother = document.getElementById(ID.replace("[TYPE]","[VALUE_ANOTHER]"));
	Options = Select.options;
	for(i = 0; i < Options.length; i++)
	{
		if(Options[i].selected)
		{
			SelectedOption = Options[i];
			break;
		}
	}
	CleanSelect(ChildSelect);
	InputAnother.value = "";
	Type = SelectedOption.value;
	switch(Type)
	{
		case "ANOTHER":
			ChildSelect.style.display="none";
			InputAnother.style.display="";	
		break;
		case "PROPERTY":
			for (sSiteID in arFields[Type][iPersonType] )
			{
				NewOption = new Option;
				NewOption.value = sSiteID;
				NewOption.text = arFields[Type][iPersonType][sSiteID];
				ChildSelect.appendChild(NewOption);
			}
			ChildSelect.style.display="";
			InputAnother.style.display="none";								
		break;
		case "USER":
		case "ORDER":
			for (sSiteID in arFields[Type] )
			{
				NewOption = new Option;
				NewOption.value = sSiteID;
				NewOption.text = arFields[Type][sSiteID];
				ChildSelect.appendChild(NewOption);
			}
			ChildSelect.style.display="";
			InputAnother.style.display="none";									
		break;
		
	}
}	
var A;
function GenerateButton(obj)
{
	var iWidth = $(obj).prev().val();
	var Table = $(obj).parents("table:first");
	$.ajax({
		type:"post",
		url:"/bitrix/admin/tcsbank_buttons.php",
		data:{"BUTTON_WIDTH":iWidth},
		beforeSend:function()
		{
			ShowWaitWindow();
			$(obj).attr("disabled","disabled");
		},
		success:function(data)
		{
			CloseWaitWindow();
			Table.replaceWith(data);
		}		
	});
}
function SelectHost(obj)
{
	Select = $(obj);
	Input = Select.parents("td:first").find(".iHostAddress");
	Option = Select.find("option:selected");
	if(Option.val()=="another")
	{
		Input.removeAttr("disabled");
	}
	else
	{
		Input.attr("disabled","disabled");
	}
	Input.val(Option.attr("url"));
	Select.parents("td:first").find(".iHostAddress.iApi").val(Option.attr("api_url"));
}