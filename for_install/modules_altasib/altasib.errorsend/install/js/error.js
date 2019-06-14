function ALXErrorSendClass()
{
	function strip_tags( str )
	{
	        return str.replace(/<\/?[^>]+>/gi, '');
	}
	var errorSendMessages = ALXerrorSendMessages;
	var errorLogoImgSrc = ALXerrorLogoImgSrc;
	var ErrorText = {'start':'','body':'','end':''};
	var BeforeChars = 50;
	var AfterChars = 50;
	var _select;

	function getSelectedText()
	{
	        if(window.getSelection)
	        {
	                str_select=window.getSelection();
	        }
	        else if (document.getSelection)
	        {
	                str_select=document.getSelection();
	        }
	        else if(document.selection)
	        {
	                str_select=document.selection;
	        }

	        if(str_select.getRangeAt)
	        {
	                if(document.getSelection())
	                {
	                        _select=document.getSelection();

	                }
	                else if (window.getSelection())
	                {
	                        _select=window.getSelection();
	                }
	                _select = _select.toString();
	                var _start=document.createRange();
	                _start.setStartBefore(str_select.getRangeAt(0).startContainer);
	                _start.setEnd(str_select.getRangeAt(0).startContainer,str_select.getRangeAt(0).startOffset);
	                text_start=_start.toString();

	                //end text
	                var _end=str_select.getRangeAt(0).cloneRange();
	                _end.setStart(str_select.getRangeAt(0).endContainer,str_select.getRangeAt(0).endOffset);
	                _end.setEndAfter(str_select.getRangeAt(0).endContainer);
	                text_end=_end.toString();
	                text_start=text_start.substr(text_start.length-BeforeChars,text_start.length);
	                text_end=text_end.substr(0,AfterChars);
	                //start text
	        }

	        if(str_select.createRange)
	        {
	                var _select=str_select.createRange().text;
	                //start text
	                var _start=str_select.createRange();
	                _start.moveStart("character",-BeforeChars);
	                _start.moveEnd("character",-_select.length);
	                var text_start=_start.text;

	                //end test
	                var _end=str_select.createRange();
	                _end.moveStart("character",_select.length);
	                _end.moveEnd("character",AfterChars);
	                var text_end=_end.text;
	                document.selection.empty();
	        }

	        if(!_select.length)
	        {
	                return false;
	        }
	        if(_select.length>250)
	        {

	        var content_LongText = '\
	        <div id="send-error_LongText">\
	        <table class="bx-width100">\
	               <tr>\
	                        <td class="no-bootom-border" valign="top" style="padding-top: 10px !important;">\
	                           <img src="/bitrix/images/altasib.errorsend/altasib.errorsend.error.gif" style="float: left; margin-right: 8px" />\
	                        </td>\
	                        <td width="90%" class="no-bootom-border" style="padding-top: 10px !important;"><font color="red"><b>'+errorSendMessages.LongText+'</b></font><br /><br />'+errorSendMessages.LongText2+'</td>\
	                </tr>\
	        </table>\
	        </div>';
	        var Dialog_LongText = new BX.CDialog({
	                title: errorSendMessages.TitleForm,
	                content: content_LongText,
	                resizable: false,
	                draggable: true,
	                height: '110',
	                width: '300'
	        });

	        if(/MSIE 6/i.test(navigator.userAgent) && !(/MSIE 7/i.test(navigator.userAgent)) && !(/MSIE 8/i.test(navigator.userAgent)))
	        {
	                Dialog_LongText.PARTS.CONTENT.className = "a-dialog-center";
	                Dialog_LongText.PARTS.TITLEBAR.className = "a-dialog-head";
	                Dialog_LongText.PARTS.TITLEBAR.style.width = (parseInt(Dialog_LongText.PARAMS.width) + 25) + "px";
	                Dialog_LongText.PARTS.FOOT.className = "a-dialog-foot";
	                Dialog_LongText.PARTS.FOOT.style.width = (parseInt(Dialog_LongText.PARAMS.width) + 25) + "px";
	                Dialog_LongText.PARTS.TITLEBAR_ICONS.className = "a-dialog-head-icons";
	        }
	        Dialog_LongText.SetButtons([Dialog_LongText.btnClose]);
	                Dialog_LongText.Show();
	                return false;
	        }
	        ErrorText.start = text_start;
	        ErrorText.body = _select;
	        ErrorText.end = text_end;

	}
	function trimLeft(str) {
	  var firstSimb;
	  str.substring(0 ,1) == " " ? firstSimb = " " : firstSimb = "";
	  return firstSimb + str.replace(/^\s+/, '');
	}

	function trimRight(str) {
	  var lastSimb;
	  str.substring(str.length-1, str.length) == " " ? lastSimb = " " : lastSimb = "";
	  return str.replace(/\s+$/, '') + lastSimb;
	}

	function trimBoth(str) {
	  return trimRight(trimLeft(str));
	}

	function trimSpaces(str) {
	  return str.replace(/\s{2,}/g, ' ');
	}

	function esErrorSend()
	{

	        new_href = window.location.href;
	        var hashpos = new_href.indexOf('#'), hash = '';
	        if (hashpos != -1)
	        {
	             hash = new_href.substr(hashpos);
	        new_href = new_href.substr(0, hashpos);
	        }
	        var url = new_href;

	        oData = {"AJAX_CALL" : "Y","ERROR_SEND" : "Y", "ERROR_TEXT_START": trimBoth(ErrorText.start),"ERROR_TEXT_BODY": document.getElementById('error_body').innerHTML,"ERROR_TEXT_END": trimBoth(ErrorText.end),"COMMENT" : document.getElementById("error-comment").value, "ERROR_URL": window.location.href};
	        BX.ajax.post(url,oData,function (res) {
	                Dialog.Close();
	                if(trim(res) != "OK!")
	                {
	                        Dialog_ok.SetContent(content_no.replace("#ERROR#", res));
	                }
	                else
	                {
	                        Dialog_ok.SetContent(content_ok);
	                }
	                Dialog_ok.Show();
	                });
	}
	function trim(string)
	{
	   return string.replace(/(^\s+)|(\s+$)/g, "");
	}

	        content_no = '\
	        <div id="send-error">\
	        <table class="bx-width100">\
	               <tr>\
	                        <td class="no-bootom-border" valign="top" style="padding-top: 10px !important;">\
	                        <img src="/bitrix/images/altasib.errorsend/altasib.errorsend.error.gif" style="float: left; margin-right: 8px" />\
	                        </td>\
	                        <td width="90%" class="no-bootom-border" style="padding-top: 8px !important;">#ERROR#</td>\
	                </tr>\
	        </table>\
	        </div>';
	        content_ok = '\
	        <div id="send-error">\
	        <table class="bx-width100">\
	               <tr>\
	                        <td class="no-bootom-border" valign="top" style="padding-top: 10px !important;">\
	                        <img src="/bitrix/images/altasib.errorsend/altasib.errorsend.ok.gif" style="float: left; margin-right: 8px" />\
	                        </td>\
	                        <td width="90%" class="no-bootom-border" style="padding-top: 8px !important;"><span style="color: green; font-size: 14px"><b>\
	                        '+errorSendMessages.text_ok+'</b></span><span style="font-size: 12px; color: #7d7d7d"><br /><br />'+errorSendMessages.text_ok2+'</span></td>\
	                </tr>\
	        </table>\
	        </div>';
	        Dialog_ok = new BX.CDialog({
	                title: errorSendMessages.TitleForm,
	                content: content_ok,
	                resizable: false,
	                draggable: true,
	                height: '130',
	                width: '350'
	        });
	        Dialog_ok.SetButtons([Dialog_ok.btnClose]);
	        if(/MSIE 6/i.test(navigator.userAgent) && !(/MSIE 7/i.test(navigator.userAgent)) && !(/MSIE 8/i.test(navigator.userAgent)))
	        {
	                Dialog_ok.PARTS.CONTENT.className = "a-dialog-center";
	                Dialog_ok.PARTS.TITLEBAR.className = "a-dialog-head";
	                Dialog_ok.PARTS.TITLEBAR.style.width = (parseInt(Dialog_ok.PARAMS.width) + 25) + "px";
	                Dialog_ok.PARTS.FOOT.className = "a-dialog-foot";
	                Dialog_ok.PARTS.FOOT.style.width = (parseInt(Dialog_ok.PARAMS.width) + 25) + "px";
	                Dialog_ok.PARTS.TITLEBAR_ICONS.className = "a-dialog-head-icons";
	        }


	        content = '\
	        <div id="send-error">\
	                        <img style="float: left; margin-right: 15px;" src="'+errorLogoImgSrc+'" /><br /><span style="font-size: 16px; color: #143f6d">'
	                        +errorSendMessages.footer+
	                        '</span><br style="clear: both" /><br /><span style="font-size: 13px; color: #000"><b>'+errorSendMessages.head+':</b></span>\
	                        <div style="border: 1px solid #d1d1d1; background-color: #fafafa; width: 470px; padding: 8px; margin: 7px 0px 13px 0px; height: 55px; color: #7d7d7d; font-size: 12px;">\
	                        <span id="error_start"></span><font color="red" id="error_body"></font><span id="error_end"></span>\
	                        </div>\
	                        <small style="color: #7d7d7d">'+errorSendMessages.comment+':</small>\
	                        <div style="width: 470px; height: 55px; padding: 8px; border: 1px solid #4b4b4b; margin: 3px 0px 3px 0px;"><textarea name="comment" id="error-comment" rows=3 cols=5 style="width: 100%; border: 0px;  height: 55px; padding: 0px"></textarea></div>\
	        </div>';


	        Dialog = new BX.CDialog({
	                title: errorSendMessages.TitleForm,
	                content: content,
	                resizable: false,
	                draggable: true,
	                height: '300',
	                width: '510'

	        });
	        if(/MSIE 6/i.test(navigator.userAgent) && !(/MSIE 7/i.test(navigator.userAgent)) && !(/MSIE 8/i.test(navigator.userAgent)))
	        {
	                Dialog.PARTS.CONTENT.className = "a-dialog-center";
	                Dialog.PARTS.TITLEBAR.className = "a-dialog-head";
	                Dialog.PARTS.TITLEBAR.style.width = (parseInt(Dialog.PARAMS.width) + 25) + "px";
	                Dialog.PARTS.FOOT.className = "a-dialog-foot";
	                Dialog.PARTS.FOOT.style.width = (parseInt(Dialog.PARAMS.width) + 25) + "px";
	                Dialog.PARTS.TITLEBAR_ICONS.className = "a-dialog-head-icons";
	        }
	        Dialog.SetButtons([new BX.CWindowButton({title:errorSendMessages.ButtonSend,id:'ok',name:'ok',action:function() {esErrorSend()}}),Dialog.btnCancel]);

                BX.bind(document, 'keydown', altasib_error);

	function altasib_error(event)
	{
	        var kCode = window.event ? window.event.keyCode : (event.keyCode ? event.keyCode : (event.which ? event.which : null));
	        var fCode = window.event ? window.event.ctrlKey : event.ctrlKey;

	        if(kCode == 13 && fCode)
	        {
	                if (Dialog.isOpen)
	                {

	                       esErrorSend();
	                }
	                else
	                {
	                       if(getSelectedText() !== false)
	                       {
	                               document.getElementById('error_start').innerHTML = ErrorText.start;
	                               document.getElementById('error_body').innerHTML = ErrorText.body;
	                               document.getElementById('error_end').innerHTML = ErrorText.end;
	                               Dialog.Show();
	                       }
	                }
	        return false;
	        }
	}
}
if(typeof ALXerrorSendMessages != 'undefined')ALXErrorSend =  new ALXErrorSendClass();