function AddTags(a)
{
	if (a && a.parentNode)
	{
		var
			div = a.parentNode.parentNode.previousSibling,
			switcher = a.parentNode.parentNode;
		BX.show(div);
		BX.remove(a.parentNode);
		if (switcher.innerHTML === '')
			BX.remove(switcher);

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
	}
	return false;
}

function CorrectTags(oObj)
{
	if (BX('TAGS_div_frame'))
		BX('TAGS_div_frame').id = oObj.id + "_div_frame";
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
	var arNote = BX.findChild(document, { className : 'forum-note-box'} , true, true), i;
	if (arNote)
	{
		for (i = 0; i < arNote.length; i++)
		{
			BX.remove(arNote[i]);
		}
	}

	var arMsgBox = BX.findChildren(document, { className : 'forum-block-container' } , true);
	if (!arMsgBox || arMsgBox.length < 1) return;
	var msgBox = arMsgBox[arMsgBox.length - 1];

	if (status.length < 1) return;

	var statusDIV = fTextToNode(status);
	if (!statusDIV) return;

	var beforeDivs = [ 'forum-info-box', 'forum-header-box', 'forum-reply-form' ];
	var tmp = msgBox;
	while ((tmp = tmp.nextSibling) && !!tmp)
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
	var navDIV = fTextToNode(navString), i;
	if (!navDIV) return;
	var navPlaceholders = BX.findChildren(document, { className : 'forum-navigation-box' } , true);
	if (!navPlaceholders) return;
	for (i = 0; i < navPlaceholders.length; i++)
		navPlaceholders[i].innerHTML = navDIV.innerHTML;
	window["oForum"]["page_number"] = pageNumber;
}

function SetForumAjaxPostTmp(text)
{
	window.forumAjaxPostTmp = text;
}

function fReplaceOrInsertNode(sourceNode, targetNode, parentTargetNode, beforeTargetNode)
{
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
	BX.ajax.processScripts(ob.SCRIPT, true);
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
	if (! arForumlist || arForumlist.length <1)
		BX.reload();
	var node, forumlist = arForumlist[arForumlist.length-1],
		formlist = BX.findChild(forumlist, {tagName: 'form', className: 'forum-form'}, true);
	forumlist = (!!formlist ? formlist : forumlist);

	if (result.status)
	{
		if (!!result.allMessages)
		{
			if (! result.message) return;

			var listparent = forumlist.parentNode;
			BX.remove(forumlist);
			listparent.innerHTML += result.message;

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
				if (footerActions)
					BX.remove(footerActions);
			}
			forumlist.innerHTML += result.message;
			ClearForumPostForm(postform);
			fRunScripts(result.message);
		}
		else if (!!result.previewMessage)
		{
			var previewDIV = BX.findChild(document, {className: 'forum-preview'}, true),
				previewParent = BX.findChild(document, {className : 'forum_post_form'}, true).parentNode;
			fReplaceOrInsertNode(result.previewMessage, previewDIV, previewParent, {className : 'forum_post_form'});

			PostFormAjaxStatus('');
			fRunScripts(result.previewMessage);
		}

		if (!!result.messageID)
			if ((node = BX('message'+result.messageID)) && !!node)
				BX.scrollToNode(node);
	}
	
	var arr = postform.getElementsByTagName("input");
	for (var i=0; i < arr.length; i++)
	{
		var butt = arr[i];
		if (butt.getAttribute("type") == "submit")
			butt.disabled = false;
	}

	BX.remove(BX.findChild(postform, { 'attr' : { 'name' : 'pageNumber' }}, true));

	if (result.statusMessage)
		PostFormAjaxStatus(result.statusMessage);
}

function ClearForumPostForm(form)
{
	var oLHE = window[form['jsObjName'].value], node;
	if (oLHE)
	{
		oLHE.ReInit('');
		if (oLHE.fAutosave)
			BX.bind(oLHE.pEditorDocument, 'keydown',
				BX.proxy(oLHE.fAutosave.Init, oLHE.fAutosave));

		for (var i = 0; i < oLHE.arFiles.length; i++)
		{
			if ((node = BX('file-doc'+oLHE.arFiles[i])) && !!node)
			{
				BX.remove(node);
				BX.hide(BX('wd-doc'+oLHE.arFiles[i]));
				BX.remove(BX('filetoupload' + oLHE.arFiles[i]));
			}
		}
		var files = form["UF_FORUM_MESSAGE_DOC[]"];
		if(files !== null && typeof files != 'undefined')
		{
			var end = false, file = false;
			do
			{
				if (!!form["UF_FORUM_MESSAGE_DOC[]"])
				{
					if (!!form["UF_FORUM_MESSAGE_DOC[]"][0]) {
						file = form["UF_FORUM_MESSAGE_DOC[]"][0];
					} else {
						file = form["UF_FORUM_MESSAGE_DOC[]"];
						end = true;
					}
					if (!!window.wduf_places && !!window.wduf_places[file.value])
						window.wduf_places[file.value] = null;
					while(BX('wd-doc' + file.value))
						BX.remove(BX('wd-doc' + file.value));
					BX.remove(file);
				}
				else {
					end = true;
				}
			} while (!end);
		}
	}

	if (!BX.type.isDomNode(form)) return;

	if ((node = BX.findChild(document, {'className' : 'forum-preview'}, true)) && !!node)
		BX.remove(node);

	var attachNodes = BX.findChild(form, {'tagName' : 'TR', 'className':"error-load"}, true, true),
		attachNode = null;
	if (attachNodes)
		while ((attachNode = attachNodes.pop()) && !!attachNode)
			BX.hide(attachNode);

	var captchaIMAGE = null,
		captchaHIDDEN = BX.findChild(form, {attr : {'name': 'captcha_code'}}, true),
		captchaINPUT = BX.findChild(form, {attr: {'name':'captcha_word'}}, true),
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
	var oLHE = window[form['jsObjName'].value];
	if (typeof form != "object" || !form.POST_MESSAGE || !oLHE)
		return false;
	if (typeof window["oForum"] == 'undefined')
		window["oForum"] = {};
	oLHE.SaveContent();
	var
		errors = "",
		Message = oLHE.GetContent(),
		MessageLength = Message.length,
		MessageMax = 64000;
	if (form.TITLE && (form.TITLE.value.length <= 0 ))
		errors += BX.message('no_topic_name');
	if (MessageLength <= 0)
		errors += BX.message('no_message');
	else if (MessageLength > MessageMax)
		errors += BX.message('max_len').replace(/\#MAX_LENGTH\#/gi, MessageMax).replace(/\#LENGTH\#/gi, MessageLength);

	if (errors !== "")
	{
		alert(errors);
		return false;
	}

	if (form['FILES[]'])
	{
		var
			oEls = [],
			oEl = BX.type.isDomNode(form['FILES[]']) ? form['FILES[]'] : form['FILES[]'][0],
			ii = BX.type.isDomNode(form['FILES[]']) ? false : 0;
		do
		{
			if (! BX('filetoupload' + oEl.value))
			{
				oEls.push(
					BX.adjust(
						BX.clone(oEl),
						{attrs : {name : 'FILES_TO_UPLOAD[]', id : ('filetoupload' + oEl.value)}}
					)
				);
			}
			oEl = (ii === false ? false : (ii <  form['FILES[]'].length ? form['FILES[]'][ii++] : false));
		} while (!!oEl);
		while (oEls.length > 0)
			form.appendChild(oEls.pop());
	}

	var arr = form.getElementsByTagName("input");
	for (var i=0; i < arr.length; i++)
	{
		var butt = arr[i];
		if (butt.getAttribute("type") == "submit")
			butt.disabled = true;
	}

	if (ajax_post == 'Y')
	{
		var postform = form;
		if (typeof window["oForum"] != 'undefined' && typeof window["oForum"]["page_number"] != 'undefined')
		{
			var pageNumberInput = BX.findChild(postform, {attr : {name : 'pageNumber'}});
			if (!pageNumberInput)
			{
				pageNumberInput = BX.create("input", {props : {type : "hidden", name : 'pageNumber'}});
				pageNumberInput.value = window["oForum"]["page_number"];
				postform.appendChild(pageNumberInput);
			} else {
				pageNumberInput.value = window["oForum"]["page_number"];
			}
		}
		setTimeout(function() { BX.ajax.submit(postform, function(response) {PostFormAjaxResponse(response, postform);}); }, 50);
		return false;
	}
	return true;
}

function ShowLastEditReason(checked, div)
{
	if (div && checked)
		BX.show(div);
	else if (div)
		BX.hide(div);
}
function ShowVote(oObj)
{
	var switcher = oObj.parentNode.parentNode;
	BX.remove(oObj.parentNode);
	if (switcher.innerHTML === '')
		BX.remove(switcher);
	BX.show(BX('vote_params'));
	return false;
}

function vote_remove_answer(obj)
{
	if (typeof obj != "object" || obj === null)
		return false;
	vote_add_answer(obj.parentNode.parentNode.parentNode, true);
	var
		answer = obj.parentNode.parentNode.firstChild,
		regexp = /ANS_(\d+)__(\d+)_/i,
		number = regexp.exec(answer.parentNode.id),
		q = parseInt(number[1]),
		a = parseInt(number[2]);
	if (answer.value !== '' && !confirm(BX.message('vote_drop_answer_confirm')))
		return false;

	if (answer.form['ANSWER_DEL[' + q + '][' + a+ ']'])
		answer.form['ANSWER_DEL[' + q + '][' + a+ ']'].value = "Y";

	answer.parentNode.parentNode.removeChild(answer.parentNode);
	return false;
}

function vote_add_answer(obj, bFromRemoveAnswerFunction)
{
	if (!obj || typeof obj != "object")
		return false;
	var
		ol = (bFromRemoveAnswerFunction !== true ? obj.parentNode.parentNode : obj),
		regexp = ol.lastChild.previousSibling ? /ANS_(\d+)__(\d+)_/i : /addA(\d+)/i,
		number = regexp.exec(ol.lastChild.previousSibling ? ol.lastChild.previousSibling.id : obj.name),
		q = parseInt(number[1]),
		a = parseInt(number[2]);
	if (!window["__fqan" + q])
		window["__fqan" + q] = a + 1;
	if (bFromRemoveAnswerFunction !== true)
	{
		a = window["__fqan" + q]++;
		var answer = BX.create('DIV', {'html' : window["arVoteParams"]['template_answer'].replace(/\#Q\#/g, q).replace(/\#A\#/g, a)});
		ol.insertBefore(answer.firstChild, ol.lastChild);
	}
	return false;
}

function vote_remove_question(anchor)
{
	if (typeof anchor != "object" || anchor === null)
		return false;
	var
		question = anchor.parentNode.previousSibling,
		q = parseInt(question.id.replace("QUESTION_", ""));
	if (question.value !== '' && !confirm(BX.message('vote_drop_question_confirm')))
		return false;
	if (question.form['QUESTION_DEL[' + q + ']'])
		question.form['QUESTION_DEL[' + q + ']'].value = "Y";
	question.parentNode.parentNode.parentNode.removeChild(question.parentNode.parentNode);
	return false;
}
function vote_add_question(oObj, iQuestion)
{
	if (!window["__fqn"])
		window["__fqn"] = parseInt(iQuestion) + 1;
	iQuestion = window["__fqn"]++;

	var question = BX.create('DIV', {'html' : window["arVoteParams"]['template_question'].replace(/\#Q\#/g, iQuestion)});
	oObj.parentNode.insertBefore(question.firstChild, oObj);
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

function quoteMessageEx(mid)
{
	var selection = GetSelection();
	mid = parseInt(mid);

	if (document.getSelection)
	{
		selection = selection.replace(/\r\n\r\n/gi, "_newstringhere_").replace(/\r\n/gi, " ");
		selection = selection.replace(/  /gi, "").replace(/_newstringhere_/gi, "\r\n\r\n");
	}

	if (selection !== "") {
		BX.DoNothing();
	} else if (mid > 0) {
		selection = (BX(('message_text_' + mid), true) ? BX(('message_text_' + mid), true).innerHTML : '');
	} else if (mid.length > 0) {
		selection = mid;
	}

	if (selection !== "")
	{
		selection = selection.replace(/[\n|\r]*<br(\s)*(\/)*>/gi, "\n");

		// Video
		var videoWMV = function(str, p1, offset, s)
		{
			var result = ' ',
				rWmv = /showWMVPlayer.*?bx_wmv_player.*?file:[\s'"]*([^"']*).*?width:[\s'"]*([^"']*).*?height:[\s'"]*([^'"]*).*?/gi,
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

		selection = selection.replace(/<script[^>]*>/gi, '\001').replace(/<\/script[^>]*>/gi, '\002');
		selection = selection.replace(/\001([^\002]*)\002/gi, videoWMV)
		selection = selection.replace(/<noscript[^>]*>/gi, '\003').replace(/<\/noscript[^>]*>/gi, '\004');
		selection = selection.replace(/\003([^\004]*)\004/gi, " ");

		// Quote & Code & Table
		selection = selection.replace(/<table class\=[\"]*forum-quote[\"]*>[^<]*<thead>[^<]*<tr>[^<]*<th>([^<]+)<\/th><\/tr><\/thead>[^<]*<tbody>[^<]*<tr>[^<]*<td>/gi, "\001");
		selection = selection.replace(/<table class\=[\"]*forum-code[\"]*>[^<]*<thead>[^<]*<tr>[^<]*<th>([^<]+)<\/th><\/tr><\/thead>[^<]*<tbody>[^<]*<tr>[^<]*<td>/gi, "\002");
		selection = selection.replace(/<table class\=[\"]*data-table[\"]*>[^<]*<tbody>/gi, "\004");
		selection = selection.replace(/<\/td>[^<]*<\/tr>(<\/tbody>)*<\/table>/gi, "\003");
		selection = selection.replace(/[\r|\n]{2,}([\001|\002])/gi, "\n$1");

		var ii = 0;
		while(ii++ < 50 && (selection.search(/\002([^\002\003]*)\003/gi) >= 0 || selection.search(/\001([^\001\003]*)\003/gi) >= 0))
		{
			selection = selection.replace(/\002([^\002\003]*)\003/gi, "[CODE]$1[/CODE]").replace(/\001([^\001\003]*)\003/gi, "[QUOTE]$1[/QUOTE]");
		}

		var regexReplaceTableTag = function(s, tag, replacement)
		{
			var re_match = new RegExp("\004([^\004\003]*)("+tag+")([^\004\003]*)\003", "i");
			var re_replace = new RegExp("((?:\004)(?:[^\004\003]*))("+tag+")((?:[^\004\003]*)(?:\003))", "i");
			var ij = 0;
			while((ij++ < 300) && (s.search(re_match) >= 0))
				s = s.replace(re_replace, "$1"+replacement+"$3");
			return s;
		}

		ii = 0;
		while(ii++ < 10 && (selection.search(/\004([^\004\003]*)\003/gi) >= 0))
		{
			selection = regexReplaceTableTag(selection, "<tr>", "[TR]");
			selection = regexReplaceTableTag(selection, "<\/tr>", "[/TR]");
			selection = regexReplaceTableTag(selection, "<td>", "[TD]");
			selection = regexReplaceTableTag(selection, "<\/td>", "[/TD]");
			selection = selection.replace(/\004([^\004\003]*)\003/gi, "[TABLE]$1[/TD][/TR][/TABLE]");
		}

		selection = selection.replace(/[\001\002\003\004]/gi, "");

		// Smiles
		if (BX.browser.IsIE())
			selection = selection.replace(/<img(?:(?:\s+alt\s*=\s*\"?smile([^\"\s]+)\"?)|(?:\s+\w+\s*=\s*[^\s>]*))*>/gi, "$1");
		else
			selection = selection.replace(/<img.*?alt=[\"]*smile([^\"\s]+)[\"]*[^>]*>/gi, "$1");

		// Hrefs
		selection = selection.replace(/<a[^>]+href=[\"]([^\"]+)\"[^>]+>([^<]+)<\/a>/gi, "[URL=$1]$2[/URL]").
			replace(/<a[^>]+href=[\']([^\']+)\'[^>]+>([^<]+)<\/a>/gi, "[URL=$1]$2[/URL]").
			replace(/<[^>]+>/gi, " ").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/&quot;/gi, "\"").
			replace(/(smile(?=[:;8]))/g, "").
			replace(/\&shy;/gi, "").
			replace(/\&nbsp;/gi, " ");

		if (!!window.oLHE && !!selection)
		{
			var author;
			if (mid > 0) {
				if (BX(('message_block_' + mid), true) && BX(('message_block_' + mid), true).hasAttribute("bx-author-name")) {
					author = {
						name : BX(('message_block_' + mid), true).getAttribute("bx-author-name"),
						id : BX(('message_block_' + mid), true).getAttribute("bx-author-id")
					}
				}
			}

			if (window.oLHE.sEditorMode == 'code' && window.oLHE.bBBCode) { // BB Codes
				if (!author)
					author = '';
				else if (author.id > 0)
					author = "[USER=" + author.id + "]" + author.name + "[/USER]";
				else
					author = author.name;
				author = (author !== '' ? (author + BX.message('author') + '\n') : '');

				window.oLHE.WrapWith("[QUOTE]", "[/QUOTE]", author + selection);
			} else if (window.oLHE.sEditorMode == 'html') { // WYSIWYG
				if (!author)
					author = '';
				else if (author.id > 0)
					author = '<span id="' + window.oLHE.SetBxTag(false, {'tag': "postuser", 'params': {'value' : author.id}}) +
						'" style="color: #2067B0; border-bottom: 1px dashed #2067B0;">' + author.name.replace(/</gi, '&lt;').replace(/>/gi, '&gt;') + '</span>';
				else
					author = '<span>' + author.name.replace(/</gi, '&lt;').replace(/>/gi, '&gt;') + '</span>';
				author = (author !== '' ? ( author + BX.message("author") + '<br/>') : '');

				var strId = (!window.oLHE.bBBCode ? " id\"=" + window.oLHE.SetBxTag(false, {tag: "quote"}) + "\"" : '');

				window.oLHE.InsertHTML('<blockquote class="bx-quote"' + strId + ">" + author +
					window.oLHE.ParseContent(selection, true) + "</blockquote><br/>");
			}

			window.oLHE.SetFocus();
			BX.defer(window.oLHE.SetFocus, window.oLHE)();

			if (window.oLHE.fAutosave)
				BX.bind(window.oLHE.pEditorDocument, 'keydown',
					BX.proxy(window.oLHE.fAutosave.Init, window.oLHE.fAutosave));

			return true;
		}
	}
	return false;
}

function reply2author(mid)
{
	var author = '';
	if (mid > 0 && BX(('message_block_' + mid), true) && BX(('message_block_' + mid), true).hasAttribute("bx-author-name")) {
		author = {
			name : BX(('message_block_' + mid), true).getAttribute("bx-author-name"),
			id : BX(('message_block_' + mid), true).getAttribute("bx-author-id")
		}
	}
	if (!!window.oLHE && !!author) {
		if (window.oLHE.sEditorMode == 'code' && window.oLHE.bBBCode) { // BB Codes
			author = (author.id > 0 ? "[USER=" + author.id + "]" + author.name + "[/USER]" : author.name);
			window.oLHE.WrapWith("", ", ", author);
		} else if (window.oLHE.sEditorMode == 'html') { // WYSIWYG
			author = (author.id > 0 ?
				('<span id="' + window.oLHE.SetBxTag(false, {'tag': "postuser", 'params': {'value' : author.id}}) +
					'" style="color: #2067B0; border-bottom: 1px dashed #2067B0;">' +
					author.name.replace(/</gi, '&lt;').replace(/>/gi, '&gt;') + '</span>'
				) : ('<span>' + author.name.replace(/</gi, '&lt;').replace(/>/gi, '&gt;') + '</span>'));
			window.oLHE.InsertHTML(author + ', ');
		}
		window.oLHE.SetFocus();
		BX.defer(window.oLHE.SetFocus, window.oLHE)();
	}
	return false;
}
