if (typeof oForumForm != "object")	
	var oForumForm = {};

var MessageMax = 64000;

function quoteMessageEx(author, mid)
{
	if (typeof document.forms["REPLIER"] == "object")
	{
		init_form(document.forms["REPLIER"]);
		oForumForm[document.forms["REPLIER"].id].quote(author, mid);
	}
}

function CreatePalette()
{
	if (oForumForm['PALETTE'])
		return oForumForm['PALETTE'];
	var color_range = ["00","33","66","99","BB","FF"];
	var rgb = {'R' : 0, 'G' : 0, 'B' : 0, 'color' : ''};
	oDiv = document.body.appendChild(document.createElement("DIV"));
	oDiv.id = 'palette';
	oDiv.className = 'palette';
	oDiv.style.position = 'absolute';
	oDiv.style.width = '199px';
	oDiv.style.height = '133px';
	oDiv.style.border = 'none';
	oDiv.style.visibility = 'hidden';
	text = "<table cellspacing='0' cellpadding='0' border='0' class='palette'><tr>";
	for (var ii = 0; ii < 216; ii++)
	{
		rgb['R'] = ii%6; rgb['G'] = Math.round(ii/36)%6; rgb['B'] = Math.round(ii/6)%6;
		rgb['color'] = '#' + color_range[rgb['R']] + '' + color_range[rgb['G']] + color_range[rgb['B']];
		if (ii%18 == 0 && ii > 0)
			text += '</tr><tr>';
		text += ('<td style="background-color:' + rgb['color'] + ';" '+
			'onmouseup="window.color_palette=\'#' + color_range[rgb['R']] + '' + color_range[rgb['G']] + color_range[rgb['B']] + '\'">'+
			'<div></div></td>');
	}
	text += "</tr></table>";
	oDiv.innerHTML = text;
	oForumForm['PALETTE'] = oDiv;
	return oForumForm['PALETTE'];
}

function emoticon(theSmilie) // 
{
	return;
}

/* Form functions */
function init_form(form)
{
	if (typeof(form) != "object")
		return false;
	if (typeof(oForumForm[form.id]) != "object")
	{
		oForumForm[form.id] = new PostForm(form);
		oForumForm[form.id].Init(form);
		form.onkeydown = function(){};
		form.onmouseover = function(){};
	}
	return;
}

function PostForm()
{
	this.open = {"B" : 0, "I" : 0, "U" : 0, "CODE" : 0, "QUOTE" : 0, "FONT" : 0, "COLOR" : 0};
	this.tags =  {
		"B" : "simple_tag", "I" : "simple_tag", "U" : "simple_tag", 
		"CODE" : "simple_tag", "QUOTE" : "simple_tag", 
		"FONT" : "simple_tag", "PALETTE" : "show_palette", "COLOR" : "simple_tag", 
		"CLOSEALL" : "closeall",
		"URL" : "tag_url", "IMG" : "tag_image", "LIST" : "tag_list",
		"TRANSLIT" : "translit"};
	this.b = {"translit" : 0};
	this.str = {"translit" : ""};
	this.stack = [];
	this.form = false;
	this.tools = {};
	this.nav = 'none';
	var t = this;
	this.popupMenu = false;
	this.now = {};
	
	this.Init = function(form)
	{
		if (this.form)
			return true;
		if (typeof(form) != "object")
			return false;
		this.form = form;
		/* Simple tags */ 
		oDivs = this.form.getElementsByTagName('DIV');
		if (oDivs && oDivs.length > 0)
		{
			for (var ii = 0; ii < oDivs.length; ii++)
			{
				if (!(oDivs[ii] && oDivs[ii].id && oDivs[ii].id.substring(0, 5) == "form_"))
					continue;
				oDiv = oDivs[ii];
				id = oDiv.id.substring(5).toUpperCase();
				if (id == 'QUOTE')
					oDiv.onmousedown = function(){t.quote(false, false);};
				else
					oDiv.onmousedown = function(){
						var id = this.id.substring(5).toUpperCase();
						var objTextarea = t.form['REVIEW_TEXT'];
						var selected = false;
						if ((jsUtils.IsIE() || jsUtils.IsOpera()) && (objTextarea.isTextEdit))
						{
							objTextarea.focus();
							var sel = document.selection;
							var rng = sel.createRange();
							rng.colapse;
							if (sel.type=="Text" && rng != null)
							{
								selected = true;
							}
						}
						else if (document.getElementById && (objTextarea.selectionEnd > objTextarea.selectionStart))
						{
							selected = true;
						}
						t.now[id] = false;
						if (!selected)
						{
							return true;
						}
						t.format_text(this, 'onmousedown');
						t.now[id] = true;};
				oDiv.onclick = function(){t.format_text(this, 'onclick')};
				oDiv.onmouseover = function(){this.className += ' marked';};
				oDiv.onmouseout = function(){this.className = this.className.replace(/marked/, '').replace('  ', ' ');};
				if (jsUtils.IsOpera() && oDiv.title)
					oDiv.title = oDiv.title.replace(/\(alt+([^)])+\)/gi, '');
				this.tools[id] = oDiv;
				
			}
		}
		if (this.form['FONT'])
		{
			this.form['FONT'].onchange = function(){t.format_text(this)};
			this.form['FONT'].onmouseover = function(){this.className += ' marked';};
			this.form['FONT'].onmouseout = function(){this.className = this.className.replace(/marked/, '').replace('  ', ' ');};
		}
		
		var image = this.form.getElementsByTagName("img");
		if (image && image.length > 0)
		{
			for (var ii = 0; ii < image.length; ii++ )
			{
				if (image[ii].className == "smiles" || image[ii].className == "smiles-list")
					image[ii].onclick = function(){t.emoticon(this)};
			}
		}
		
		if (this.form["REVIEW_TEXT"])
		{
			this.form["REVIEW_TEXT"].onselect = function(){t.store_caret(this)};
			this.form["REVIEW_TEXT"].onclick = function(){t.store_caret(this)};
			this.form["REVIEW_TEXT"].onkeyup = function(e){t.OnKeyPress(e); t.store_caret(this)};
			this.form["REVIEW_TEXT"].onkeypress = t.check_ctrl_enter;
		}
		
		return true;
	}, 
	
	this.OnKeyPress = function(e)
	{
		if(!e) e = window.event
		if(!e) return;
		if(!e.altKey) return;
		if(e.keyCode == 73)
			this.format_text({'id' : 'form_i', 'value' : ''});
		else if(e.keyCode == 85)
			this.format_text({'id' : 'form_u', 'value' : ''});
		else if(e.keyCode == 66)
			this.format_text({'id' : 'form_b', 'value' : ''});
		else if(e.keyCode == 81)
			this.format_text({'id' : 'form_quote', 'value' : ''});
		else if(e.keyCode == 80)
			this.format_text({'id' : 'form_code', 'value' : ''});
		else if(e.keyCode == 71)
			this.tag_image();
		else if(e.keyCode == 72)
			this.tag_url();
		else if(e.keyCode == 76)
			this.tag_list();
	},
	
	this.Insert = function (ibTag, ibClsTag, isSingle, postText)
	{
		if (!this.form || !this.form["REVIEW_TEXT"])
			return false;
		var textarea = this.form["REVIEW_TEXT"];
		var isClose = (isSingle ? true : false);
		postText = (postText == null ? "" : postText);
		this.form["REVIEW_TEXT"].focus();
		if (jsUtils.IsIE() || jsUtils.IsOpera())
		{
			this.form["REVIEW_TEXT"].focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.collapse;
			var parent_name = "";
			if (rng.parentElement && rng.parentElement() && rng.parentElement().name)
			{
				parent_name = rng.parentElement().name;
			}
			if (parent_name != "REVIEW_TEXT")
			{
				textarea.value += ibTag
			}
			else if ((sel.type == "Text" || sel.type == "None") && rng != null)
			{
				if (ibClsTag != "" && rng.text.length > 0)
				{
					ibTag += rng.text + ibClsTag;
					isClose = false;
				}
				else if (postText.length > 0)
				{
					ibTag += postText + ibClsTag;
					isClose = false;
				}
				rng.text = ibTag;
			}
		}
		else if (document.getElementById)
		{
			var text = {"start" : ibTag, "end" : ""};
			if (ibClsTag != "" && textarea.selectionEnd > textarea.selectionStart)
			{
				text["end"] = ibClsTag;
				isClose = false;
			}
			else if (postText.length > 0)
			{
				text["start"] = text["start"] + "" + postText + "" + ibClsTag;
				isClose = false;
			}
			text["start"] = (!text["start"] ? "" : text["start"]);
			text["end"] = (!text["end"] ? "" : text["end"]);
			var sel = {
				"start" : textarea.selectionStart,
				"end" : textarea.selectionEnd};
			
			if (sel["end"] == 1 || sel["end"] == 2)
				sel["end"] = textarea.textLength;
		
			var s1 = (textarea.value).substring(0, sel["start"]);
			var s2 = (textarea.value).substring(sel["start"], sel["end"])
			var s3 = (textarea.value).substring(sel["end"], textarea.textLength);
			textarea.value = s1 + text["start"] + s2 + text["end"] + s3;
			if (sel["start"] != sel["end"])
			{
				textarea.selectionStart = sel["start"];
				textarea.selectionEnd = sel["end"] + text["start"].length + text["end"].length;
			}
			else if (text["start"].length > 0 || text["end"].length > 0)
			{
				textarea.selectionStart = sel["end"] + text["start"].length + text["end"].length;
				textarea.selectionEnd = textarea.selectionStart;
			}
		} 
		else
		{
			textarea.value += ibTag;
		}
		textarea.focus();
		return isClose;
	},
	
	this.format_text = function(oObj, event_name)
	{
		if (!oObj || !oObj.id)
			return false;
		var id = oObj.id.substring(5).toUpperCase();
		if (this.now[id] == true)
		{
			this.now[id] = false;
			return;
		}
		
		if (this.tags[id] == 'simple_tag')
		{
			var tag_start = tag_name = id;
			if (tag_name == 'FONT' || tag_name == 'COLOR')
				tag_start += "=" + oObj.value;

			if ((this.open[tag_name] == 0 || (tag_name == 'FONT' || tag_name == 'COLOR')) && 
				this.Insert("[" + tag_start + "]", "[/" + tag_name + "]", true))
			{
				this.open[tag_name]++;
				if (this.tools[id])
					this.tools[id].className += ' opened';
				this.stack.push(tag_name);
			}
			else
			{
				var stack_need_insert = [];
				var tag_is_open = false;
				var res = false;
				while (res = this.stack.pop())
				{
					stack_need_insert.unshift(res);
					if (res == tag_name)
					{
						tag_is_open = true;
						break;
					}
				}
				if (!tag_is_open)
					this.stack = stack_need_insert;
				var res = false;
				while (res = stack_need_insert.pop())
				{
					this.Insert("[/" + res + "]", "", false);
					if (this.tools[id])
						this.tools[id].className = this.tools[id].className.replace(/opened/, '').replace('  ', ' ');
					
					this.open[res]--;
				}
			}
			
			this.tools['CLOSEALL'].style.display = (this.stack <= 0 ? 'none' : '');
		}
		else if (this.tags[id] == 'show_palette')
		{
			this.store_caret();
			this.show_palette(oObj);
		}
		else if (this.tags[id] == 'translit')
		{
			if (this.b["translit"] != true)
			{
				if (this.tools[id])
					this.tools[id].className += ' opened translited';
				this.b["translit"] = true;
			}
			else
			{
				if (this.tools[id])
					this.tools[id].className = this.tools[id].className.replace(/opened/, '').replace(/translited/, '').replace('  ', ' ');
				this.b["translit"] = false;
			}
			this.translit();
		}
		else if (this.tags[id])
		{
			this[this.tags[id]]();
		}
	}, 
	
	this.check_ctrl_enter = function(e)
	{
		if(!e) 
			e = window.event;
		if((e.keyCode == 13 || e.keyCode == 10) && e.ctrlKey && ValidateForm(t.form))
		{
			t.form.submit();
		}
			
		return;
	},
	
	this.store_caret = function()
	{
		if (this.form["REVIEW_TEXT"].createTextRange) 
			this.form["REVIEW_TEXT"].caretPos = document.selection.createRange().duplicate();
	},
	
	this.emoticon = function(element)
	{
		this.Insert(" " + element.id + " ", "", false);
	},
	
	this.tag_image = function()
	{
		var need_loop = true;
		do 
		{
			var res = prompt(oText['enter_image'], "http://");
			if (res == null)
			{
				need_loop = false;
				return false;
			}
			else if (res.length <= 0)
			{
				alert("Error! " + oErrors['no_url']);
			}
			else
			{
				need_loop = false;
			}
		}
		while(need_loop);
		this.Insert("[IMG]" + res + "[/IMG]", "", false);
	},
	
	this.tag_list = function()
	{ 
		var thelist = "[LIST]\n";
		
		var need_loop = true;
		do 
		{
			var res = prompt(oText['list_prompt'], "");
			if (res == null)
			{
				need_loop = false;
				return false;
			}
			else if (res.length <= 0)
			{
				need_loop = false;
			}
			else
			{
				thelist = thelist + "[*]" + res + "\n";
			}
		}
		while(need_loop);
		this.Insert(thelist + "[/LIST]\n", "", false);
	},
	
	this.closeall = function()
	{
		var res = false;
		while(res = this.stack.pop())
		{
			this.Insert("[/" + res + "]");
			if (this.form[res])
				this.form[res].value = res;
			if (this.tools[res])
			{
				this.tools[res].className = this.tools[res].className.replace(/opened/, '').replace('  ', ' ');
			}
			this.open[res]--;
		}
		this.tools['CLOSEALL'].style.display = (this.stack <= 0 ? 'none' : '');
	},
	
	this.tag_url = function( )
	{
		var FoundErrors = '';
		var need_loop = true;
		var oFields = {
			"URL" : {
				"text" : oText['enter_url'],
				"default" : "http://",
				"error" : oErrors['no_url'],
				"value" : ""}, 
			"TITLE" : {
				"text" : oText['enter_url_name'],
				"default" : "My Webpage",
				"error" : oErrors['no_title'],
				"value" : ""}};

		for (var ii in oFields)
		{
			need_loop = true;
			do 
			{
				var res = prompt(oFields[ii]["text"], oFields[ii]["default"]);
				if (res == null)
				{
					need_loop = false;
					return false;
				}
				else if (res.length <= 0)
				{
					alert("Error! " + oFields[ii]["error"]);
				}
				else
				{
					oFields[ii]["value"] = res;
					need_loop = false;
				}
			}
			while(need_loop);
		}
		
		this.Insert("[URL=" + oFields["URL"]["value"] + "]" + oFields["TITLE"]["value"] + "[/URL]", "", false);
	},
	
	this.storeCaret = function (textEl)
	{
		if (textEl.createTextRange) 
			textEl.caretPos = document.selection.createRange().duplicate();
	},

	this.translit = function()
	{
		var i;
		var objTextarea = this.form['REVIEW_TEXT'];
		var textbody = objTextarea.value;
		var selected = false;
		
		if ((jsUtils.IsIE() || jsUtils.IsOpera()) && (objTextarea.isTextEdit))
		{
			objTextarea.focus();
			var sel = document.selection;
			var rng = sel.createRange();
			rng.colapse;
			if (sel.type=="Text" && rng != null)
			{
				textbody = rng.text;
				selected = true;
			}
		}
		else if (document.getElementById)
		{
			if (objTextarea.selectionEnd > objTextarea.selectionStart)
			{
				var selEnd = objTextarea.selectionEnd;
				if (selEnd == 0)
					selEnd = objTextarea.textLength;
				var startText = (objTextarea.value).substring(0, objTextarea.selectionStart);
				textbody = (objTextarea.value).substring(objTextarea.selectionStart, selEnd);
				var endText = (objTextarea.value).substring(selEnd, objTextarea.textLength);
				selected = true;
			}
		}
		
		if (textbody)
		{
			if (this.b["translit"] == 0)
			{
				for (i=0; i<capitEngLettersReg.length; i++) textbody = textbody.replace(capitEngLettersReg[i], capitRusLetters[i]);
				for (i=0; i<smallEngLettersReg.length; i++) textbody = textbody.replace(smallEngLettersReg[i], smallRusLetters[i]);
			}
			else
			{
				for (i=0; i<capitRusLetters.length; i++) textbody = textbody.replace(capitRusLettersReg[i], capitEngLetters[i]);
				for (i=0; i<smallRusLetters.length; i++) textbody = textbody.replace(smallRusLettersReg[i], smallEngLetters[i]);
			}
			if (!selected) 
			{
				objTextarea.value = textbody;
			}
			else 
			{
				if ((jsUtils.IsIE() || jsUtils.IsOpera()) && (objTextarea.isTextEdit))
				{
					rng.text = textbody;
				}
				else
				{
					objTextarea.value = startText + textbody + endText;
					objTextarea.selectionEnd = startText.length + textbody.length;
				}
			}
		}
		objTextarea.focus();	
	},
	
	this.quote = function (author, mid)
	{
		var selection = "";
		var message_id = 0;
		if (document.getSelection)
		{
			selection = document.getSelection();
			selection = selection.replace(/\r\n\r\n/gi, "_newstringhere_").replace(/\r\n/gi, " ");
			selection = selection.replace(/  /gi, "").replace(/_newstringhere_/gi, "\r\n\r\n");
		}
		else if (document.selection)
		{
			selection = document.selection.createRange().text;
		}
		
		if (selection == "" && mid)
		{
			message = mid.replace(/message_text_/gi, "");
			if (parseInt(message) > 0)
			{
				message = document.getElementById(mid);
				if (typeof(message) == "object" && message)
				{
					selection = message.innerHTML;
					selection = selection.replace(/\<br(\s)*(\/)*\>/gi, "\n").replace(/\<script[^\>]*>/gi, '\001').replace(/\<\/script[^\>]*>/gi, '\002');
					selection = selection.replace(/\<noscript[^\>]*>/gi, '\003').replace(/\<\/noscript[^\>]*>/gi, '\004');
					selection = selection.replace(/\001([^\002]*)\002/gi, " ").replace(/\003([^\004]*)\004/gi, " ").replace(/\<[^\>]+\>/gi, " ");
					selection = selection.replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/&quot;/gi, "\"");
				}
				else
				{
					selection = 'is not object';
				}
			}
			else if (mid.Length() > 0)
			{
				selection = mid;
			}
		}
		
		if (selection != "")
		{
			selection = selection.replace(/\&shy;/gi, "");
			if (author != null && author)
				selection = author + oText['author'] + selection;
			this.Insert("[QUOTE]", "[/QUOTE]", false, selection);
			this.now['QUOTE']=true;
		}
	}, 
	
	this.show_palette = function(oObj)
	{
		if (!oObj){return false};
		var oPalette = CreatePalette();
		if (!this.popupMenu)
		{
			window.ForumPopupMenu.prototype.ShowMenu = function(control, div)
			{
				var pos = {"top" : 20, "left" : 20};
				this.PopupHide();
				if (typeof(control) == "object")
				{
					id = control.id;
					pos = jsUtils.GetRealPos(control);
					this.ControlPos = pos;
					this.oControl = control;
				}
				
				this.oDiv = div;
				if (this.oDiv)
				{
					this.PopupShow(pos, this.oDiv);
				}
			}
			window.ForumPopupMenu.prototype.CheckClick = function(e)
			{
				if(!this.oDiv){return;}
				if (this.oDiv.style.visibility != 'visible' || this.oDiv.style.display == 'none')
					return;
		        var windowSize = jsUtils.GetWindowSize();
		        var x = e.clientX + windowSize.scrollLeft;
		        var y = e.clientY + windowSize.scrollTop;
		
				/*menu region*/
				pos = jsUtils.GetRealPos(this.oDiv);
				var posLeft = parseInt(pos["left"]);
				var posTop = parseInt(pos["top"])
				var posRight = posLeft + this.oDiv.offsetWidth;
				var posBottom = posTop + this.oDiv.offsetHeight;
				if(x >= posLeft && x <= posRight && y >= posTop && y <= posBottom)
				{
					if (window.color_palette)
					{
						t.format_text({'id' : 'form_color', 'value' : window.color_palette, 'className' : ''});
						this.PopupHide();
					}
				}
		
				if(this.ControlPos)
				{
					var pos = this.ControlPos;
					if(x >= pos['left'] && x <= pos['right'] && y >= pos['top'] && y <= pos['bottom'])
						return;
				}
				this.PopupHide();
			}
			
			this.popupMenu = new ForumPopupMenu();
		}
		this.popupMenu.ShowMenu(oObj, oPalette);
	}
}

function ValidateForm(form, ajax_type)
{
	if (typeof form != "object" || typeof form.REVIEW_TEXT != "object")
		return false;
		
	var errors = "";
	var MessageLength = form.REVIEW_TEXT.value.length;

	if (form.TITLE && (form.TITLE.value.length < 2))
		errors += oErrors['no_topic_name'];

	if (MessageLength < 2)
		errors += oErrors['no_message'];
    else if ((MessageMax != 0) && (MessageLength > MessageMax))
		errors += oErrors['max_len1'] + MessageMax + oErrors['max_len2'] + MessageLength;

	if (errors != "")
	{
		alert(errors);
		return false;
	}
	
	var arr = form.getElementsByTagName("submit")
	for (var butt in arr)
		butt.disabled = true;
		
	if (ajax_type == 'Y' && window['ForumPostMessage'])
	{
		ForumPostMessage(form);
	}
	return true;
}