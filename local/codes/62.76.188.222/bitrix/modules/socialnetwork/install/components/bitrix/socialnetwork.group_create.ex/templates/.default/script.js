function BXOnInviteListChange()
{
	window.arInvitationUsersList = arguments[0];
	BX.onCustomEvent('onInvitationUsersListChange', [BX.util.array_values(window.arInvitationUsersList)]);
}

function BXSwitchExtranet(isChecked)
{
	if (BX("EMAILS_block"))
	{
		if (isChecked)
			BX("EMAILS_block").style.display = "block";
		else
			BX("EMAILS_block").style.display = "none";
	}

	if (BX("GROUP_OPENED_block") && BX("GROUP_OPENED"))
	{
		if (isChecked)
		{
			BX("GROUP_OPENED").checked = false;
			BX("GROUP_OPENED").disabled = true;
			BX.addClass(BX('GROUP_OPENED_block'), 'sonet-group-create-popup-checkbox-disabled');
		}
		else
		{
			BX("GROUP_OPENED").disabled = true;
			BX("GROUP_OPENED_block").style.display = "block";
			BX.addClass(BX('GROUP_OPENED_block'), 'sonet-group-create-popup-checkbox-disabled');
		}
	}

	if (BX("GROUP_VISIBLE_block") && BX("GROUP_VISIBLE"))
	{
		if (isChecked)
		{
			BX("GROUP_VISIBLE").checked = false;
			BX("GROUP_VISIBLE").disabled = true;
			BX.addClass(BX('GROUP_VISIBLE_block'), 'sonet-group-create-popup-checkbox-disabled');
		}
		else
		{
			BX("GROUP_VISIBLE").disabled = false;
			BX("GROUP_VISIBLE_block").style.display = "block";
			BX.removeClass(BX('GROUP_VISIBLE_block'), 'sonet-group-create-popup-checkbox-disabled');
		}
	}

	if (BX("GROUP_INITIATE_PERMS") && BX("GROUP_INITIATE_PERMS_OPTION_E") && BX("GROUP_INITIATE_PERMS_OPTION_K"))
	{
		if (isChecked)
			BX("GROUP_INITIATE_PERMS_OPTION_E").selected = true;
		else
			BX("GROUP_INITIATE_PERMS_OPTION_K").selected = true;
	}
	
	if (BX("USERS_employee_section_extranet"))
	{
		if (isChecked)
			BX("USERS_employee_section_extranet").style.display = "inline-block";
		else
			BX("USERS_employee_section_extranet").style.display = "none";
	}

}

function BXSwitchNotVisible(isChecked)
{
	if (isChecked)
	{
		BX("GROUP_OPENED").disabled = false;
		BX.removeClass(BX('GROUP_OPENED_block'), 'sonet-group-create-popup-checkbox-disabled');
	}
	else
	{
		BX("GROUP_OPENED").disabled = true;
		BX("GROUP_OPENED").checked = false;
		BX.addClass(BX('GROUP_OPENED_block'), 'sonet-group-create-popup-checkbox-disabled');
	}
}

function BXDeleteImage()
{
	if (BX("sonet_group_create_tabs_image_block") && BX("GROUP_IMAGE_ID_DEL"))
	{
		BX("sonet_group_create_tabs_image_block").style.visibility = "hidden";
		BX("GROUP_IMAGE_ID_DEL").value = "Y";
		if (BX("file_input_GROUP_IMAGE_ID"))
			BX("file_input_GROUP_IMAGE_ID").value = "";
		if (BX("file_input_upload_list_GROUP_IMAGE_ID"))
		{
			var tmpNode = BX.findChild(BX("file_input_upload_list_GROUP_IMAGE_ID"), { tagName: 'input', attr: { name: 'GROUP_IMAGE_ID' } }, true, false);
			if (tmpNode)
				tmpNode.value = "";
		}


	}
}

function BXGCESwitchTabs()
{
	var tabs = BX.findChildren(BX("sonet_group_create_popup"), { className: "sonet-group-create-popup-tab" }, true);
	var blockList = BX.findChildren(BX("sonet_group_create_tabs_content"), { tagName: "div" }, false);

	BX.bind(BX.findChild(BX("sonet_group_create_popup"), { className: "sonet-group-create-popup-tabs-block" }, true, false), "click", function(event) {
		event = event || window.event;
		var target = event.target || event.srcElement;

		if (BX.hasClass(BX(target), 'sonet-group-create-popup-tab') || BX.hasClass(BX(target.parentNode), 'sonet-group-create-popup-tab'))
		{
			for(var i=0; i<tabs.length; i++){
				BX.removeClass(tabs[i], "sonet-group-create-popup-tab-active");
				blockList[i].style.display = "none";
				if(tabs[i] == target || tabs[i] == target.parentNode){
					BX.addClass(tabs[i], "sonet-group-create-popup-tab-active");
					blockList[i].style.display = 'block';
				}
			}
		}
	})

}

function BXGCESwitchFeatures(){
	var servBlock = BX("sonet_group_create_tabs_features");
	if (servBlock)
	{
		var servList = BX.findChildren(servBlock, { className: "sonet-group-create-popup-feature"}, true);
		var inputList = BX.findChildren(servBlock, { className: "sonet-group-create-popup-feature-hidden"}, true);

		BX.bind(servBlock, "click", function(event){
			event = event || window.event;
			var target = event.target || event.srcElement;
			for(var i=0; i<servList.length; i++){
				if(target == servList[i] || target.parentNode == servList[i]){
					BX.toggleClass(servList[i], 'sonet-group-create-popup-feature-active');
					if (BX.hasClass(servList[i], "sonet-group-create-popup-feature-active"))
						inputList[i].value = "Y";
					else
						inputList[i].value = "";
					break;
				}
			}

		});
	}

}

function BXGCESubmitForm(e)
{
	BX.submit(BX("sonet_group_create_popup_form"));
	BX.unbind(BX("sonet_group_create_popup_form_button_submit"), "click", BXGCESubmitForm);
	BX.PreventDefault(e);
};

function onCancelClick(e)
{
	top.BX.onCustomEvent('onSonetIframeCancelClick');
	return BX.PreventDefault(e);
}

function __addExtranetEmail(){

	var inputMail = BX('sonet_group_create_popup_form_email_input');

	if(inputMail.value == 'e-mail' || inputMail.value == '')
		return;

	var emailPattern = /^[a-zA-Z0-9._\-+~'=]+@[a-zA-Z0-9._-]+\.[a-zA-Z]{2,9}$/;

	if(emailPattern.test(inputMail.value))
	{
		if(top.BXExtranetMailList.length > 0)
		{
			for(var i=0; i < top.BXExtranetMailList.length; i++)
			{
				if(top.BXExtranetMailList[i] == inputMail.value)
				{
					BX('sonet_group_create_popup_form_email_' + (i + 1)).style.background = 'none';
					setTimeout(function(){BX('sonet_group_create_popup_form_email_'+(i+1)).style.backgroundColor = '#E1E9F6'}, 150);
					setTimeout(function(){BX('sonet_group_create_popup_form_email_'+(i+1)).style.background = 'none'}, 300);
					setTimeout(function(){BX('sonet_group_create_popup_form_email_'+(i+1)).style.backgroundColor = '#E1E9F6'}, 450);
					return;
				}
			}
		}

		var link = BX.create('a', {
			props:{
				className: 'sonet-group-create-popup-form-email',
				id: 'sonet_group_create_popup_form_email_' + (top.BXExtranetMailList.length + 1),
				href: 'javascript:void(0)'
			},
			children: [
					BX('sonet_group_create_popup_form_email_input').value,
					BX.create('a', {
						props:{
							className: 'sonet-group-create-popup-del',
							href: 'javascript:void(0)'
						},
						events: { click: __deleteExtranetEmail }
					})
			]
		});

		BX('sonet_group_create_popup_form_email_bl').appendChild(link);
		if (BX('EMAILS').value.length > 0)
			BX('EMAILS').value += ', ';
		BX('EMAILS').value += BX('sonet_group_create_popup_form_email_input').value;

		BX.removeClass(inputMail, 'sonet-group-create-popup-form-email-error');
		inputMail.value = '';

		top.BXExtranetMailList.push(inputMail.value);

	}
	else
	{
		if(BX.browser.IsIE())
		{
			inputMail.focus();
			inputMail.value = inputMail.value;
		}
		inputMail.focus();
		BX.addClass(inputMail, 'sonet-group-create-popup-form-email-error')
	}
}

function __deleteExtranetEmail(item)
{
	var flag = false;

	if (!item || !BX.type.isDomNode(item))
		item = this;

	if (item)
	{
		BX(item).parentNode.parentNode.removeChild(BX(item).parentNode);
		var num = parseInt(BX(item).parentNode.id.substring(36));
		top.BXExtranetMailList[num-1] = '';

		BX('EMAILS').value = '';
		for(var i=0; i<top.BXExtranetMailList.length; i++)
		{
			if (top.BXExtranetMailList[i].length > 0)
			{
				if (flag)
					BX('EMAILS').value += ', ';

				BX('EMAILS').value += top.BXExtranetMailList[i];
				var flag = true;
			}
		}
	}
}

function BXGCEEmailKeyDown(event)
{
	event = event || window.event;
	BX.removeClass(this, 'sonet-group-create-popup-form-email-error');
	if(event.keyCode == 13)
		__addExtranetEmail();
};
