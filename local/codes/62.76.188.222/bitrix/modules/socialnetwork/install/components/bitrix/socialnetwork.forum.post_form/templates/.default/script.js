function AttachFile(iNumber, iCount, sIndex, oObj)
{
	var element = null;
	var bFined = false;
	iNumber = parseInt(iNumber);
	iCount = parseInt(iCount);

	document.getElementById('upload_files_info_' + sIndex).style.display = 'block';
	for (var ii = iNumber; ii < (iNumber + iCount); ii++)
	{
		element = document.getElementById('upload_files_' + ii + '_' + sIndex);
		if (!element || typeof(element) == null)
			break;
		if (element.style.display == 'none')
		{
			bFined = true;
			element.style.display = 'block';
			break;
		}
	}
	var bHide = (!bFined ? true : (ii >= (iNumber + iCount - 1)));
	if (bHide == true)
		oObj.style.display = 'none';
}

function AddTags(a)
{
	if (a != null)
	{
		var div = a.parentNode.previousSibling.previousSibling;
		div.style.display = "block";
		a.parentNode.style.display = "none";

		var inputs = div.getElementsByTagName("INPUT");
		for (var i = 0 ; i < inputs.length ; i++ )
		{
			if (inputs[i].type.toUpperCase() == "TEXT")
			{
				CorrectTags(inputs[i]);
				inputs[i].focus();
				break;
			}
		}
		if (a.parentNode.lastChild && a.parentNode.lastChild.name == 'from_tag'){
			a.parentNode.lastChild.style.display = 'none';
			if (document.getElementById('vote_switcher')) {
				document.getElementById('vote_switcher').style.display = '';}
		}
	}
	return false;
}


function CorrectTags(oObj)
{
	if (document.getElementById('TAGS_div_frame'))
		document.getElementById('TAGS_div_frame').id = oObj.id + "_div_frame";
}

function fTextToNode(text)
{
	var tmpdiv = BX.create('div');
	tmpdiv.innerHTML = text;
	if (tmpdiv.childNodes.length > 0)
		return tmpdiv.childNodes[0];
	else
		return null;
}

function PostFormAjaxStatus(status)
{
	var arNote = BX.findChild(document, { className : 'forum-note-box'} , true, true);
	if (arNote)
		for (i in arNote)
			BX.remove(arNote[i]);

	var arMsgBox = BX.findChildren(document, { className : 'forum-block-container' } , true);
	if (!arMsgBox || arMsgBox.length < 1) return;
	var msgBox = arMsgBox[arMsgBox.length - 1];

	if (status.length < 1) return;

	var statusDIV = fTextToNode(status);
	if (!statusDIV) return;

	var beforeDivs = [ 'forum-info-box', 'forum-header-box', 'forum-reply-form', 'forum_post_form'  ];
	var tmp = msgBox;
	while (tmp = tmp.nextSibling)
	{
		if (tmp.nodeType == 1)
		{
			var insert = false;
			for (i in beforeDivs)
			{
				if (BX.hasClass(tmp, beforeDivs[i]))
				{
					insert = true;
					break;
				}
			}
			if (insert)
			{
				tmp.parentNode.insertBefore(statusDIV, tmp);
				break;
			}
		}
	}
}


function PostFormAjaxNavigation(navString, pageNumber)
{
	var navDIV = fTextToNode(navString);
	if (!navDIV) return;
	var navPlaceholders = BX.findChildren(document, { className : 'forum-navigation-box' } , true);
	if (!navPlaceholders) return;
	for (i in navPlaceholders)
		navPlaceholders[i].innerHTML = navDIV.innerHTML;
	oForum.page_number = pageNumber;
}

function SetForumAjaxPostTmp(text)
{
	window.forumAjaxPostTmp = text;
}

function fReplaceOrInsertNode(sourceNode, targetNode, parentTargetNode, beforeTargetNode)
{
	var parentNode = null;
	var nextNode = null;

	if (!BX.type.isDomNode(parentTargetNode)) return false;

	if (!BX.type.isDomNode(sourceNode) && !BX.type.isArray(sourceNode) && sourceNode.length > 0)
		if (! (sourceNode = fTextToNode(sourceNode))) return false;

	if (BX.type.isDomNode(targetNode)) // replace
	{
		nextNode = targetNode.nextSibling;
		targetNode.parentNode.removeChild(targetNode);
	}

	if (!nextNode)
		nextNode = BX.findChild(parentTargetNode, beforeTargetNode, true);

	if (nextNode)
	{
		nextNode.parentNode.insertBefore(sourceNode, nextNode);
	} else {
		parentTargetNode.appendChild(sourceNode);
	}

	return true;
}

function fRunScripts(msg)
{
	var ob = BX.processHTML(msg, true);
	scripts = ob.SCRIPT;
	BX.ajax.processScripts(scripts, true);
}

function PostFormAjaxResponse(response, postform)
{
	postform['BXFormSubmit_save'] = null;
	var result = window.forumAjaxPostTmp;
	if (typeof result == 'undefined') 
	{
		BX.reload();
		return;
	}

	var arForumlist = BX.findChildren(document, {className: 'forum-block-inner'}, true);
	if (! arForumlist || arForumlist.length <1) BX.reload();
	var forumlist = arForumlist[arForumlist.length-1];
	//if (formlist = BX.findChild(forumlist, {tagName: 'form', className: 'forum-form'}, true))
		//forumlist = formlist;

	if (result.status)
	{
		if (!!result.allMessages)
		{
			var messagesNode = fTextToNode(result.message);
			if (! messagesNode) return;

			var listparent = forumlist.parentNode;
			BX.remove(forumlist);
			listparent.appendChild(messagesNode);

			if (!!result.navigation && !!result.pageNumber)
			{
				PostFormAjaxNavigation(result.navigation, result.pageNumber);
			}
			ClearForumPostForm(postform);
			fRunScripts(result.message);
		}
		else if (typeof result.message != 'undefined')
		{
			var allMessages = BX.findChildren(forumlist, {tagName: 'table', className: 'forum-post-table'}, true);
			if (allMessages.length > 0)
			{
				var lastMessage = allMessages[allMessages.length - 1];
				var footerActions = BX.findChild(lastMessage, { tagName : 'tfoot' }, true);
				if (footerActions) BX.remove(footerActions);
			}
			if (msgNode = fTextToNode(result.message))
				forumlist.appendChild(msgNode);
			ClearForumPostForm(postform);
			fRunScripts(result.message);
		}
		else if (!!result.previewMessage)
		{
			previewDIV = BX.findChild(document, {className: 'forum-preview'}, true);
			previewParent = BX.findChild(document, {className : 'forum_post_form'}, true).parentNode;
			fReplaceOrInsertNode(result.previewMessage, previewDIV, previewParent, {className : 'forum_post_form'});

			PostFormAjaxStatus('');
			fRunScripts(result.previewMessage);
		}

		if (!!result.messageID)
			if (message = BX('message'+result.messageID))
				BX.scrollToNode(message);
	}
	
	var arr = postform.getElementsByTagName("input");
	for (var i=0; i < arr.length; i++)
	{
		var butt = arr[i];
		if (butt.getAttribute("type") == "submit")
			butt.disabled = false;
	}

	if (input_pageno = BX.findChild(postform, { 'attr' : { 'name' : 'pageNumber' }}, true))
		BX.remove(input_pageno);

	if (result.statusMessage)
		PostFormAjaxStatus(result.statusMessage);
}

function ClearForumPostForm(form)
{
	if (window.oLHE)
	{
		if (window.oLHE.sEditorMode == 'code')
			window.oLHE.SetContent('');
		else
		window.oLHE.SetEditorContent('');
	}

	if (window.oLHE.fAutosave)
		BX.bind(window.oLHE.pEditorDocument, 'keydown', 
			BX.proxy(window.oLHE.fAutosave.Init, window.oLHE.fAutosave));

	if (!BX.type.isDomNode(form)) return;

	if (previewDIV = BX.findChild(document, {'className' : 'forum-preview'}, true))
		BX.remove(previewDIV);

	var i = 0;
	while (fileDIV = BX('upload_files_'+(i++)+'_'))
	{
		if (fileINPUT = BX.findChild(fileDIV, {'tag':'input'}))
			fileINPUT.value = '';
		BX.hide(fileDIV);
	}
	var attachLink = BX.findChild(form, {'className':"forum-upload-file-attach"}, true);
	if (attachLink)
		BX.show(attachLink);
	var attachNote = BX.findChild(form, {'className':"forum-upload-info"}, true);
	if (attachNote)
		BX.hide(attachNote);

	captchaIMAGE = null;
	captchaHIDDEN = BX.findChild(form, {attr : {'name': 'captcha_code'}}, true);
	captchaINPUT = BX.findChild(form, {attr: {'name':'captcha_word'}}, true);
	captchaDIV = BX.findChild(form, {'className':'forum-reply-field-captcha-image'}, true);
	if (captchaDIV)
		captchaIMAGE = BX.findChild(captchaDIV, {'tag':'img'});
	if (captchaHIDDEN && captchaINPUT && captchaIMAGE)
	{
		captchaINPUT.value = '';
		BX.ajax.getCaptcha(function(result) {
			captchaHIDDEN.value = result.captcha_sid;
			captchaIMAGE.src = '/bitrix/tools/captcha.php?captcha_code='+result.captcha_sid;
		});
	}
}

function ValidateForm(form, ajax_type, ajax_post)
{
	if (form['BXFormSubmit_save']) return true; // ValidateForm may be run by BX.submit one more time
	if (typeof form != "object" || typeof form.POST_MESSAGE != "object")
		return false;
	if (typeof oForum == 'undefined')
		oForum = {};
	MessageMax = 64000;

	var errors = "";
	var MessageLength = form.POST_MESSAGE.value.length;

	if (form.TITLE && (form.TITLE.value.length < 2))
		errors += oErrors['no_topic_name'];

	if (MessageLength < 2)
		errors += oErrors['no_message'];
    else if ((MessageMax != 0) && (MessageLength > MessageMax))
		errors += oErrors['max_len'].replace(/\#MAX_LENGTH\#/gi, MessageMax).replace(/\#LENGTH\#/gi, MessageLength);

	if (errors != "")
	{
		alert(errors);
		return false;
	}
	
	var arr = form.getElementsByTagName("input");
	for (var i=0; i < arr.length; i++)
	{
		var butt = arr[i];
		if (butt.getAttribute("type") == "submit")
			butt.disabled = true;
	}
		
	if (ajax_type == 'Y' && window['ForumPostMessage'])
	{
		ForumPostMessage(form);
	}

	if (form.MESSAGE_MODE.value == 'VIEW') return true;

	if (ajax_post == 'Y')
	{
		var postform = form;
		if (typeof oForum != 'undefined' && typeof oForum.page_number != 'undefined')
		{
			var pageNumberInput = BX.findChild(postform, {attr : {name : 'pageNumber'}});
			if (!pageNumberInput)
			{
				pageNumberInput = BX.create("input", {props : {type : "hidden", name : 'pageNumber'}});
				pageNumberInput.value = oForum.page_number;
				postform.appendChild(pageNumberInput);
			} else {
				pageNumberInput.value = oForum.page_number;
			}
		}
		setTimeout(function() { BX.ajax.submit(postform, function(response) {PostFormAjaxResponse(response, postform);}); }, 50);
		return false;
	}
	return true;
}

function ShowLastEditReason(checked, div)
{
	if (div)
	{
		if (checked)
			div.style.display = 'block';
		else
			div.style.display = 'none';
	}
}
function ShowVote(oObj)
{
	if (oObj)
	{
		if (oObj.name == 'from_tag')
		{
			oObj.parentNode.removeChild(oObj);
			document.getElementById('vote_switcher').parentNode.removeChild(document.getElementById('vote_switcher'));
		}
		else
		{
			oObj.parentNode.parentNode.removeChild(oObj.parentNode);
		}
		document.getElementById('vote_params').style.display = '';
	}
	return false;
}

function vote_remove_answer(anchor, iQuestion, permanent)
{
	if (typeof anchor != "object" || anchor == null)
		return false;
	else if (!confirm(oText['vote_drop_answer_confirm']))
		return false;
	iQuestion = parseInt(iQuestion);
	vote_init_question(iQuestion);
	arVoteParams[iQuestion]['count_a']--;
	if (arVoteParams[iQuestion]['count_a'] < arVoteParams['count_max_a'])
	{
		anchor.parentNode.parentNode.parentNode.lastChild.style.display = '';
	}
	permanent = (permanent == "Y" ? "Y" : "N");
	if (permanent == "Y")
	{
		anchor.parentNode.parentNode.parentNode.removeChild(anchor.parentNode.parentNode);
	}
	else
	{
		anchor.parentNode.previousSibling.value = 'Y';
		anchor.parentNode.parentNode.style.display = 'none';
	}
	return false;
}

function vote_add_answer(oLi, iQuestion, iAnswer)
{
	iQuestion = parseInt(iQuestion);
	iAnswer = parseInt(iAnswer);
	vote_init_question(iQuestion);
	
	iAnswer = (arVoteParams[iQuestion]['max_a'] > iAnswer ? arVoteParams[iQuestion]['max_a'] : iAnswer);
	iAnswer++;
	arVoteParams[iQuestion]['max_a'] = iAnswer;
	arVoteParams[iQuestion]['count_a']++;
	
	var answer = document.createElement('LI');
	
	answer.innerHTML = arVoteParams['template_answer'].replace(/\#Q\#/g, iQuestion).replace(/\#A\#/g, iAnswer);
	oLi.parentNode.insertBefore(answer, oLi);
	
	if (arVoteParams[iQuestion]['count_a'] >= arVoteParams['count_max_a'])
	{
		oLi.style.display = 'none';
	}
	return false;
}
function vote_init_question(iQuestion, oData)
{
	if (typeof arVoteParams[iQuestion] == "object" && arVoteParams[iQuestion] != null) {
		return true; }
	else if (typeof oData == "object" && oData != null) {
		arVoteParams[iQuestion] = oData;
		return true;}
	arVoteParams[iQuestion] = {'count_a' : 0, 'max_a' : 0};
	try
	{
		arVoteParams[iQuestion]['count_a'] = document.getElementById('MULTI_' + iQuestion).parentNode.nextSibling.getElementsByTagName('li').length;
		arVoteParams[iQuestion]['count_a']--;
	}
	catch(e){}
	return true;
}
function vote_remove_question(anchor, permanent)
{
	if (typeof anchor != "object" || anchor == null)
		return false;
	else if (!confirm(oText['vote_drop_question_confirm']))
		return false;
	permanent = (permanent == "Y" ? "Y" : "N");
/*	var input = jsUtils.CreateElement("INPUT", {'type': 'hidden', 'name': anchor.parentNode.previousSibling.id + '_DEL', 'value': 'Y'});*/
	if (permanent == "Y")
	{
		anchor.parentNode.parentNode.parentNode.parentNode.removeChild(anchor.parentNode.parentNode.parentNode);
	}
	else
	{
		anchor.parentNode.previousSibling.value = 'Y';
		anchor.parentNode.parentNode.parentNode.style.display = 'none';
	}
	arVoteParams['count_q']--;
	if (arVoteParams['count_q'] < arVoteParams['coun_max_q'])
	{
		document.getElementById("vote_question_add").style.display = 'block';
	}
	return false;
}
function vote_add_question(iQuestion, oObj)
{
	iQuestion = parseInt(iQuestion);
	iQuestion = (arVoteParams['max_q'] > iQuestion ? arVoteParams['max_q'] : iQuestion);
	iQuestion++;
	arVoteParams['max_q'] = iQuestion;
	var question = BX.create("DIV", {attrs:{"class": "forum-reply-field-vote-question"}});
	//var question = jsUtils.CreateElement("DIV", {"class": "forum-reply-field-vote-question"});
	question.innerHTML = arVoteParams['template_question'].replace(/\#Q\#/g, iQuestion);
	oObj.parentNode.insertBefore(question, oObj);
	arVoteParams['count_q']++;
	if (arVoteParams['count_q'] >= arVoteParams['coun_max_q'])
	{
		document.getElementById("vote_question_add").style.display = 'none';
	}
	return false;
}

var GetSelection = function()
{
	var t = '';
	if (typeof window.getSelection == 'function')
	{
		try 
		{
			var sel = window.getSelection().getRangeAt(0).cloneContents();
			var e = BX.create('div');
			e.appendChild(sel);
			t = e.innerHTML;
		} catch (e) {}
	}
	else if (document.selection && document.selection.createRange)
		t = document.selection.createRange().htmlText;
	return t;
}

function quoteMessageEx(author, mid)
{
	var selection = "";
	var message_id = 0;
	selection = GetSelection();
	
	if (document.getSelection)
	{
		selection = selection.replace(/\r\n\r\n/gi, "_newstringhere_").replace(/\r\n/gi, " ");
		selection = selection.replace(/  /gi, "").replace(/_newstringhere_/gi, "\r\n\r\n");
	}

	if (selection == "" && mid)
	{
		message_id = parseInt(mid.replace(/message_text_/gi, ""));
		if (message_id > 0)
		{
			var message = document.getElementById(mid);
			if (typeof(message) == "object" && message)
			{
				selection = message.innerHTML;
			}
		}
		else if (mid.length > 0)
		{
			selection = mid;
		}
	}

	if (selection != "")
	{
		selection = selection.replace(/[\n|\r]*\<br(\s)*(\/)*\>/gi, "\n");

		// Video
		var videoWMV = function(str, p1, offset, s)
		{
			var result = ' ';
			var rWmv = /showWMVPlayer.*?bx_wmv_player.*?file:[\s'"]*([^"']*).*?width:[\s'"]*([^"']*).*?height:[\s'"]*([^'"]*).*?/gi;
			res = rWmv.exec(p1);
			if (res)
				result = "[VIDEO WIDTH="+res[2]+" HEIGHT="+res[3]+"]"+res[1]+"[/VIDEO]";
			if (result == ' ')
			{
				var rFlv = /bxPlayerOnload[\s\S]*?[\s'"]*file[\s'"]*:[\s'"]*([^"']*)[\s\S]*?[\s'"]*height[\s'"]*:[\s'"]*([^"']*)[\s\S]*?[\s'"]*width[\s'"]*:[\s'"]*([^"']*)/gi;
				res = rFlv.exec(p1);
				if (res)
					result = "[VIDEO WIDTH="+res[3]+" HEIGHT="+res[2]+"]"+res[1]+"[/VIDEO]";
			}
			return result;
		}

		selection = selection.replace(/\<script[^\>]*>/gi, '\001').replace(/\<\/script[^\>]*>/gi, '\002');
		selection = selection.replace(/\001([^\002]*)\002/gi, videoWMV)
		selection = selection.replace(/\<noscript[^\>]*>/gi, '\003').replace(/\<\/noscript[^\>]*>/gi, '\004');
		selection = selection.replace(/\003([^\004]*)\004/gi, " ");

		// Quote & Code & Table
		selection = selection.replace(/\<table class\=[\"]*forum-quote[\"]*\>[^<]*\<thead\>[^<]*\<tr\>[^<]*\<th\>([^<]+)\<\/th\>\<\/tr\>\<\/thead\>[^<]*\<tbody\>[^<]*\<tr\>[^<]*\<td\>/gi, "\001");
		selection = selection.replace(/\<table class\=[\"]*forum-code[\"]*\>[^<]*\<thead\>[^<]*\<tr\>[^<]*\<th\>([^<]+)\<\/th\>\<\/tr\>\<\/thead\>[^<]*\<tbody\>[^<]*\<tr\>[^<]*\<td\>/gi, "\002");
		selection = selection.replace(/\<table class\=[\"]*data-table[\"]*\>[^<]*\<tbody\>/gi, "\004");
		selection = selection.replace(/\<\/td\>[^<]*\<\/tr\>(\<\/tbody\>)*\<\/table\>/gi, "\003");
		selection = selection.replace(/[\r|\n]{2,}([\001|\002])/gi, "\n$1");

		var ii = 0;
		while(ii++ < 50 && (selection.search(/\002([^\002\003]*)\003/gi) >= 0 || selection.search(/\001([^\001\003]*)\003/gi) >= 0))
		{
			selection = selection.replace(/\002([^\002\003]*)\003/gi, "[CODE]$1[/CODE]").replace(/\001([^\001\003]*)\003/gi, "[QUOTE]$1[/QUOTE]");
		}

		function regexReplaceTableTag(s, tag, replacement)
		{
			var re_match = new RegExp("\004([^\004\003]*)("+tag+")([^\004\003]*)\003", "i");
			var re_replace = new RegExp("((?:\004)(?:[^\004\003]*))("+tag+")((?:[^\004\003]*)(?:\003))", "i");
			var ij = 0;
			while((ij++ < 300) && (s.search(re_match) >= 0))
				s = s.replace(re_replace, "$1"+replacement+"$3");
			return s;
		}

		var ii = 0;
		while(ii++ < 10 && (selection.search(/\004([^\004\003]*)\003/gi) >= 0))
		{
			selection = regexReplaceTableTag(selection, "\<tr\>", "[TR]");
			selection = regexReplaceTableTag(selection, "\<\/tr\>", "[/TR]");
			selection = regexReplaceTableTag(selection, "\<td\>", "[TD]");
			selection = regexReplaceTableTag(selection, "\<\/td\>", "[/TD]");
			selection = selection.replace(/\004([^\004\003]*)\003/gi, "[TABLE]$1[/TD][/TR][/TABLE]");
		}

		selection = selection.replace(/[\001\002\003\004]/gi, "");

		// Smiles
		if (BX.browser.IsIE())
			selection = selection.replace(/\<img(?:(?:\s+alt\s*=\s*\"?smile([^\"\s]+)\"?)|(?:\s+\w+\s*=\s*[^\s\>]*))*\>/gi, "$1");
		else
			selection = selection.replace(/\<img.*?alt=[\"]*smile([^\"\s]+)[\"]*[^>]*\>/gi, "$1");

		// Hrefs
		selection = selection.replace(/\<a[^>]+href=[\"]([^\"]+)\"[^>]+\>([^<]+)\<\/a\>/gi, "[URL=$1]$2[/URL]");
		selection = selection.replace(/\<a[^>]+href=[\']([^\']+)\'[^>]+\>([^<]+)\<\/a\>/gi, "[URL=$1]$2[/URL]");
		selection = selection.replace(/\<[^\>]+\>/gi, " ").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/&quot;/gi, "\"");

		selection = selection.replace(/(smile(?=[:;8]))/g, "");

		selection = selection.replace(/\&shy;/gi, "");
		selection = selection.replace(/\&nbsp;/gi, " ");
		if (author != null && author)
			selection = author + oText['author'] + selection;

		if (window.oLHE)
		{
			var content = '';
			if (window.oLHE.sEditorMode == 'code')
				content = window.oLHE.GetCodeEditorContent();
			else
				content = window.oLHE.GetEditorContent();
			content += "[QUOTE]"+selection+"[/QUOTE]";
			if (window.oLHE.sEditorMode == 'code')
				window.oLHE.SetContent(content);
			else
				window.oLHE.SetEditorContent(content);

			if (window.oLHE.fAutosave)
				BX.bind(window.oLHE.pEditorDocument, 'keydown', 
					BX.proxy(window.oLHE.fAutosave.Init, window.oLHE.fAutosave));

			setTimeout(function() { window.oLHE.SetFocusToEnd();}, 300);
			return true;
		}
	}
	return false;
}
