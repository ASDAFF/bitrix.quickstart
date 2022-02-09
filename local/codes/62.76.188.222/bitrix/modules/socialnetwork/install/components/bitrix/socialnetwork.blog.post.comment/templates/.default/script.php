<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if($arResult["CanUserComment"])
{
	?>
<div id="form_comment_" style="display:none;">
<div id="form_c_del" style="display:none;">
<div class="blog-comment-edit feed-com-add-block blog-post-edit">
<?
$arSmiles = array();
if(!empty($arResult["Smiles"]))
{
	foreach($arResult["Smiles"] as $arSmile)
	{
		$arSmiles[] = array(
			'name' => $arSmile["~LANG_NAME"],
			'path' => "/bitrix/images/blog/smile/".$arSmile["IMAGE"],
			'code' => str_replace("\\\\","\\",$arSmile["TYPE"]),
			'codes' => str_replace("\\\\","\\",$arSmile["TYPING"])
		);
	}
}
	$arParams["FORM_ID"] = "blogCommentForm".randString(4);
	$formParams = Array(
		"FORM_ID" => $arParams["FORM_ID"],
		"SHOW_MORE" => "Y",
		"PARSER" => Array(
			"Bold", "Italic", "Underline", "Strike", "ForeColor",
			"FontList", "FontSizeList", "RemoveFormat", "Quote",
			"Code", ((!$arResult["NoCommentUrl"]) ? 'CreateLink' : ''),
			"Image", (($arResult["allowImageUpload"] == "Y") ? 'UploadImage' : ''),
			(($arResult["allowVideo"] == "Y") ? "InputVideo" : ""),
			"Table", "Justify", "InsertOrderedList",
			"InsertUnorderedList",
			"MentionUser", "Source"),
		"BUTTONS" => Array(
			((in_array("UF_BLOG_COMMENT_FILE", $arParams["COMMENT_PROPERTY"]) || in_array("UF_BLOG_COMMENT_DOC", $arParams["COMMENT_PROPERTY"])) ? "UploadFile" : ""),
			((!$arResult["NoCommentUrl"]) ? 'CreateLink' : ''),
			(($arResult["allowImageUpload"] == "Y") ? 'UploadImage' : ''),
			(($arResult["allowVideo"] == "Y") ? "InputVideo" : ""),
			"MentionUser"/*, "BlogTag"*/
			),
		"TEXT" => Array(
			"NAME" => "comment",
			"VALUE" => "",
			"HEIGHT" => "80px"
		),
		"DESTINATION" => Array(
			"VALUE" => $arResult["FEED_DESTINATION"],
			"SHOW" => "N",
		),
		"UPLOAD_FILE" => $arResult["COMMENT_PROPERTIES"]["DATA"]["UF_BLOG_COMMENT_DOC"],
		"UPLOAD_WEBDAV_ELEMENT" => $arResult["COMMENT_PROPERTIES"]["DATA"]["UF_BLOG_COMMENT_FILE"],
		"UPLOAD_FILE_PARAMS" => array("width" => 400, "height" => 400),
		"FILES" => Array(
//			"VALUE" => $arResult["Images"],
			"VALUE" => array(),
			"DEL_LINK" => $arResult["urlToDelImage"],
			"SHOW" => "N",
			"POSTFIX" => "file"
		),

		"SMILES" => Array("VALUE" => $arSmiles),
		"LHE" => array(
			"documentCSS" => "body {color:#434343; font-size: 12px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 20px;}",
			"ctrlEnterHandler" => "submitComment",
			"jsObjName" => "oPostFormLHE_".$arParams["FORM_ID"]
		)
	);
?>
	<form action="/bitrix/urlrewrite.php?SEF_APPLICATION_CUR_PAGE_URL=<?=urlencode($arResult["urlToPost"])?>" id="<?=$formParams["FORM_ID"]?>" name="<?=$formParams["FORM_ID"]?>" <?
		?>method="POST" enctype="multipart/form-data" target="_self">
		<input type="hidden" name="comment_post_id" id="postId" value="" />
		<input type="hidden" name="parentId" id="parentId" value="" />
		<input type="hidden" name="edit_id" id="edit_id" value="" />
		<input type="hidden" name="act" id="act" value="add" />
		<input type="hidden" name="post" id="" value="Y" />
		<?=bitrix_sessid_post();?>
<?
if(empty($arResult["User"]))
{
?>
	<div class="blog-comment-field blog-comment-field-user">
		<div class="blog-comment-field blog-comment-field-author"><div class="blog-comment-field-text"><?
			?><label for="user_name"><?=GetMessage("B_B_MS_NAME")?></label><?
			?><span class="blog-required-field">*</span></div><span><?
			?><input maxlength="255" size="30" tabindex="3" type="text" name="user_name" id="user_name" value="<?=htmlspecialcharsEx($_SESSION["blog_user_name"])?>"></span></div>
		<div class="blog-comment-field-user-sep">&nbsp;</div>
		<div class="blog-comment-field blog-comment-field-email"><div class="blog-comment-field-text"><label for="">E-mail</label></div><span><input maxlength="255" size="30" tabindex="4" type="text" name="user_email" id="user_email" value="<?=htmlspecialcharsEx($_SESSION["blog_user_email"])?>"></span></div>
		<div class="blog-clear-float"></div>
	</div>
<?
}
?>
		<div id="blog-post-autosave-hidden" <?/*?>style="display:none;"<?*/?>></div>
<?$APPLICATION->IncludeComponent("bitrix:main.post.form", "", $formParams, false, Array("HIDE_ICONS" => "Y"));?>
<?
if($arResult["use_captcha"]===true)
{
?>
		<div class="blog-comment-field blog-comment-field-captcha">
		<div class="blog-comment-field-captcha-label">
			<label for="captcha_word"><?=GetMessage("B_B_MS_CAPTCHA_SYM")?></label><span class="blog-required-field">*</span><br>
			<input type="hidden" name="captcha_code" id="captcha_code" value="<?=$arResult["CaptchaCode"]?>">
			<input type="text" size="30" name="captcha_word" id="captcha_word" value=""  tabindex="7">
			</div>
		<div class="blog-comment-field-captcha-image"><div id="div_captcha"></div></div>
	</div>
	<div id="captcha_del">
	<script>
		<!--
		var cc;
		if(document.cookie.indexOf('<?=session_name()?>=') == -1)
			cc = Math.random();
		else
			cc ='<?=$arResult["CaptchaCode"]?>';

		document.write('<img src="/bitrix/tools/captcha.php?captcha_code='+cc+'" width="180" height="40" id="captcha" style="display:none;">');
		document.getElementById('captcha_code').value = cc;
		//-->
	</script>
	</div>
<?
}
?>
			<a class="feed-add-button feed-add-com-button" href="javascript:void(0)" id="blog-submit-button-save_comment" <?
				?>onmousedown="BX.addClass(this, 'feed-add-button-press')" onmouseup="BX.removeClass(this,'feed-add-button-press')" <?
				?>onclick="submitComment();"><?
				?><span class="feed-add-button-left"></span><?
				?><span class="feed-add-button-text"><?=GetMessage("BLOG_C_BUTTON_SEND")?></span><span class="feed-add-button-right"></span></a>
			<a class="feed-cancel-com" href="javascript:void(0)" id="blog-submit-button-cancel_comment" <?
				?>onclick="cancelComment();"><?=GetMessage("BLOG_C_BUTTON_CANCEL")?></a>
</form>
</div>
</div>
</div>
<?}?>
<script>
var lastPostComment;
var lastPostCommentId;
function showComment(key, postId, error, userName, userEmail, needData, bEdit)
{
	if(lastPostComment > 0)
	{
		BX.show(BX.findChild(BX('blg-post-'+lastPostComment), {className: 'feed-com-footer'}, true, false));
		if(BX('err_comment_'+lastPostComment+'_0'))
			BX.hide(BX('err_comment_'+lastPostComment+'_0'));
	}
	if(lastPostCommentId > 0)
	{
		BX.hide(BX('err_comment_'+lastPostCommentId));
		if(BX('err_comment_'+lastPostCommentId+'_0'))
			BX.hide(BX('err_comment_'+lastPostCommentId+'_0'));
	}
	<?
	if($arResult["use_captcha"]===true)
	{
		?>
		var im = BX('captcha');
		BX('captcha_del').appendChild(im);
		<?
	}
	?>
	comment = '';

	if(needData == "Y" || bEdit == "Y")
	{
		comment = window["text"+key];
	}
	
	var pFormCont = BX('form_c_del');
	form_comment_id = 'form_comment_' + key;
	if(key == 0)
		form_comment_id = 'form_comment_'+ postId + '_' + key;
	
	BX(form_comment_id).appendChild(pFormCont); // Move form
	pFormCont.style.display = "block";

	BX('parentId').value = key;
	BX('postId').value = postId;
	BX('edit_id').value = '';
	BX('act').value = 'add';

	if(bEdit == 'Y')
	{
		BX('edit_id').value = key;
		BX('act').value = 'edit';
	}
	<?
	if($arResult["use_captcha"]===true)
	{
		?>
		var im = BX('captcha');
		BX('div_captcha').appendChild(im);
		im.style.display = "block";
		<?
	}
	?>

	if(error == "Y")
	{
		if(comment.length > 0)
		{
			comment = comment.replace(/\/</gi, '<');
			comment = comment.replace(/\/>/gi, '>');
		}
		if(userName.length > 0)
		{
			userName = userName.replace(/\/</gi, '<');
			userName = userName.replace(/\/>/gi, '>');
			BX('user_name').value = userName;
		}
		if(userEmail.length > 0)
		{
			userEmail = userEmail.replace(/\/</gi, '<');
			userEmail = userEmail.replace(/\/>/gi, '>');
			BX('user_email').value = userEmail;
		}
	}

	files = BX('<?=$formParams["FORM_ID"]?>')["UF_BLOG_COMMENT_FILE[]"];
	if(files !== null && typeof files != 'undefined')
	{
		if(!files.length)
		{
			BX.remove(files);
		}
		else
		{
			for(i = 0; i < files.length; i++)
				BX.remove(BX(files[i]));
		}
	}

	filesForm = BX.findChild(BX('<?=$formParams["FORM_ID"]?>'), {'className': 'wduf-placeholder-tbody' }, true, false);
	if(filesForm !== null && typeof filesForm != 'undefined')
		BX.cleanNode(filesForm, false);

	filesForm = BX.findChild(BX('<?=$formParams["FORM_ID"]?>'), {'className': 'wduf-selectdialog' }, true, false)
	if(filesForm !== null && typeof filesForm != 'undefined')
		BX.hide(filesForm);

	files = BX('<?=$formParams["FORM_ID"]?>')["UF_BLOG_COMMENT_DOC[]"];
	if(files !== null && typeof files != 'undefined')
	{
		if(!files.length)
		{
			BX.remove(files);
		}
		else
		{
			for(i = 0; i < files.length; i++)
				BX.remove(BX(files[i]));
		}
	}
	filesForm = BX.findChild(BX('<?=$formParams["FORM_ID"]?>'), {'className': 'file-placeholder-tbody' }, true, false);
	if(filesForm !== null && typeof filesForm != 'undefined')
		BX.cleanNode(filesForm, false);

	filesForm = BX.findChild(BX('<?=$formParams["FORM_ID"]?>'), {'className': 'feed-add-photo-block' }, true, true);
	if(filesForm !== null && typeof filesForm != 'undefined')
	{
		for(i = 0; i < filesForm.length; i++)
		{
			if(BX(filesForm[i]).parentNode.id != 'file-image-template')
				BX.remove(BX(filesForm[i]));
		}
	}

	filesForm = BX.findChild(BX('<?=$formParams["FORM_ID"]?>'), {'className': 'file-selectdialog' }, true, false)
	if(filesForm !== null && typeof filesForm != 'undefined')
		BX.hide(filesForm);

	onLightEditorShow(comment);
	
	BX.hide(BX.findChild(BX('blg-post-'+postId), {className: 'feed-com-footer'}, true, false));
	lastPostComment = postId;
	lastPostCommentId = key;
	return false;
}

function waitResult(id)
{
	ob = BX('new_comment_' + id); 
	if(ob.innerHTML.length > 0)
	{
		var obNew = BX.processHTML(ob.innerHTML, true);
		scripts = obNew.SCRIPT;
		BX.ajax.processScripts(scripts, true);
		if(window.commentEr && window.commentEr == "Y")
		{
			BX('err_comment_'+id).innerHTML = ob.innerHTML;
			ob.innerHTML = '';
			BX.show(BX('err_comment_'+id));
		}
		else
		{
			if(BX('edit_id').value > 0)
			{
				if(BX('blg-comment-'+id))
				{
					BX('blg-comment-'+id+'old').innerHTML = BX('blg-comment-'+id).innerHTML;
					BX('blg-comment-'+id+'old').id = 'blg-comment-'+id;
					if(BX.browser.IsIE()) //for IE, numbered list not rendering well
						setTimeout(function (){BX('blg-comment-'+id).innerHTML = BX('blg-comment-'+id).innerHTML}, 10);
				}
				else
				{
					BX('blg-comment-'+id+'old').innerHTML = ob.innerHTML;
					if(BX.browser.IsIE()) //for IE, numbered list not rendering well
						setTimeout(function (){BX('blg-comment-'+id+'old').innerHTML = BX('blg-comment-'+id+'old').innerHTML}, 10);

				}
			}
			else
			{
				BX('new_comment_cont_'+id).innerHTML += ob.innerHTML;
				if(BX.browser.IsIE()) //for IE, numbered list not rendering well
					setTimeout(function (){BX('new_comment_cont_'+id).innerHTML = BX('new_comment_cont_'+id).innerHTML}, 10);
			}
			ob.innerHTML = '';
			BX.hide(BX('form_c_del'));
			if(lastPostComment > 0)
			{
				var el = BX.findChild(BX('blg-post-'+lastPostComment), {className: 'feed-com-footer'}, true, false);
				BX.show(el);
				BX.findChild(el, {tag: 'a'}, true, false).focus();
			}
		}
		window.commentEr = false;
		__blogCloseWait();
		bCommentSubmit = false;
		var but = BX.findChild(BX('<?=$formParams["FORM_ID"]?>'), {'attr': {id: 'blog-submit-button-save_comment'}}, true, false);
		BX.removeClass(but, 'feed-add-button-press');

		BX(ob).removeAttribute('data-send');
	}
	else
		setTimeout("waitResult('"+id+"')", 500);
}

var bCommentSubmit = false;
function submitComment()
{
	if(bCommentSubmit)
		return false;
	bCommentSubmit = true;
	obForm = BX('<?=$formParams["FORM_ID"]?>');
	var but = BX.findChild(obForm, {'attr': {id: 'blog-submit-button-save_comment'}}, true, false);
	BX.addClass(but, 'feed-add-button-press');
	
	if(BX('edit_id').value > 0)
	{
		val = BX('edit_id').value;
		if(BX('blg-comment-'+val))
			BX('blg-comment-'+val).id = 'blg-comment-'+val+'old';
	}
	else
		val = BX('parentId').value
		
	prefix = val;
	if(val == 0)
		prefix = BX('postId').value + '_' + val;

	id = 'new_comment_' + prefix;
	if(BX('err_comment_'+prefix))
		BX('err_comment_'+prefix).innerHTML = '';
		
	__blogShowWait('form_c_del');
	obForm.target = '';
	BX(id).setAttribute('data-send', 'Y');
	BX.ajax.submitComponentForm(obForm, id);
	setTimeout("waitResult('"+prefix+"')", 100);
	BX.submit(obForm);
}

function hideShowComment(id, postId, hide)
{
	urlToHide = '<?=CUtil::JSEscape($arResult["urlToHide"])?>';
	urlToShow = '<?=CUtil::JSEscape($arResult["urlToShow"])?>';
	if(hide)
		url = urlToHide;
	else
		url = urlToShow;
	url = url.replace(/#comment_id#/, id);
	url = url.replace(/#post_id#/, postId);
	
	var bcn = BX('blg-comment-'+id);
	__blogShowWait('blg-comment-'+id);
	bcn.id = 'blg-comment-'+id+'old';
	BX('err_comment_'+id).innerHTML = '';

	BX.ajax.get(url, function(data) {
		var obNew = BX.processHTML(data, true);
		scripts = obNew.SCRIPT;
		BX.ajax.processScripts(scripts, true);
		var nc = BX('new_comment_'+id);
		var bc = BX('blg-comment-'+id+'old');
		nc.style.display = "none";
		nc.innerHTML = data;
		
		if(BX('blg-comment-'+id))
		{
			bc.innerHTML = BX('blg-comment-'+id).innerHTML;
		}
		else
		{
			BX('err_comment_'+id).innerHTML = nc.innerHTML;
		}
		BX('blg-comment-'+id+'old').id = 'blg-comment-'+id;
		__blogCloseWait();
	});

	return false;
}

function deleteComment(id, postId)
{
	__blogShowWait('blg-comment-'+id);
	urlToDelete = '<?=CUtil::JSEscape($arResult["urlToDelete"])?>';
	url = urlToDelete.replace(/#comment_id#/, id);
	url = url.replace(/#post_id#/, postId);

	BX.ajax.get(url, function(data) {
		var obNew = BX.processHTML(data, true);
		scripts = obNew.SCRIPT;
		BX.ajax.processScripts(scripts, true);

		var nc = BX('new_comment_'+id);
		nc.style.display = "none";
		nc.innerHTML = data;

		if(BX('blg-com-err'))
		{
			BX('err_comment_'+id).innerHTML = nc.innerHTML;
		}
		else
		{
			var el = BX('blg-comment-'+id);
			el.innerHTML = nc.innerHTML;
			el.onmouseout = null;
			el.onmouseover = null;
		}
		nc.innerHTML = '';
		__blogCloseWait();
	});

	return false;
}

var waitDiv = null;
var waitPopup<?=$formParams["FORM_ID"]?> = null;
function __blogShowWait(comments_block)
{
	waitDiv = waitDiv || comments_block;
	comments_block = BX(comments_block || waitDiv);

	if (!waitPopup<?=$formParams["FORM_ID"]?>)
	{
		waitPopup<?=$formParams["FORM_ID"]?> = new BX.PopupWindow('blog_comment_wait<?=$formParams["FORM_ID"]?>', comments_block, {
			autoHide: true,
			lightShadow: true,
			zIndex: 2,
			content: BX.create('DIV', {props: {className: 'blog-comment-wait'}})
		});
	}
	else
		waitPopup<?=$formParams["FORM_ID"]?>.setBindElement(comments_block);

	var height = comments_block.offsetHeight, width = comments_block.offsetWidth;
	if (height > 0 && width > 0)
	{
		waitPopup<?=$formParams["FORM_ID"]?>.setOffset({
			offsetTop: -parseInt(height/2+15),
			offsetLeft: parseInt(width/2-15)
		});

		waitPopup<?=$formParams["FORM_ID"]?>.show();
	}
	return waitPopup<?=$formParams["FORM_ID"]?>;
}

function __blogCloseWait()
{
	if (waitPopup<?=$formParams["FORM_ID"]?>)
	{
		waitPopup<?=$formParams["FORM_ID"]?>.close();
	}
}

function showHiddenComments(id, source, comment, startHeight)
{
	if(comment)
	{
		var el = BX.findChild(BX('blg-comment-' + comment), {className: 'feed-com-text-inner'}, true, false);
		el2 = BX.findChild(BX('blg-comment-' + comment), {className: 'feed-com-text-inner-inner'}, true, false);
		var heightFull = el2.offsetHeight;
		BX.remove(source);
		var el3 = BX.findParent(BX('blg-comment-' + comment), {attr: {id: 'blog-comment-hidden-'+id}}, true, false);
		if(!!el3)
			el3.style.maxHeight = (el3.offsetHeight+heightFull - 200)+'px';
	}
	else
	{
		var el = BX('blog-comment-hidden-' + id);
	}
	if(el)
	{
		var elC = 'N';
		if(BX('comshowend-'+id))
			elC = BX('comshowend-'+id).value;
		if(el.style.display == "none" || elC == "N" || comment)
		{
			if(!comment)
			{
				el.style.maxHeight = '100%';
				el.style.display = "block";
				var heightFull = el.offsetHeight;
			}
			if(!startHeight)
				startHeight = 0;

			var fxStart = startHeight;
			var fxFinish = heightFull;

			if(comment)
			{
				var fxStart = 200;
				var start1 = {height:fxStart};
				var finish1 = {height:fxFinish};
			}
			else
			{
				var start1 = {height:fxStart, opacity:0};
				var finish1 = {height:fxFinish, opacity:100};
			}

			var time = 1.0 * (fxFinish - fxStart) / (2000 - fxStart);
			if(time < 0.3)
				time = 0.3;			
			if(time > 0.8)
				time = 0.8;
			
			(new BX.fx({
				time: time,
				step: 0.05,
				type: 'linear',
				start: start1,
				finish: finish1,
				callback:BX.delegate(__blogCommentExpandSetHeight, el),
				callback_complete: BX.delegate(function() {if(comment) { el.style.maxHeight = 'none';}})
			})).start();		

			if(!comment && BX('comshowend-'+id).value == "Y")						
			{
				BX.findChild(source, {'className': 'feed-com-all-hide' }, true, false).style.display = "inline-block";
				BX.findChild(source, {'className': 'feed-com-all-text' }, true, false).style.display = "none";
				BX.addClass(source.parentNode, "feed-com-all-expanded");
			}
		}
		else
		{
			if(!comment)
			{
				var heightFull = el.offsetHeight;
				BX.removeClass(source, "feed-com-all-expanded");
			}
			var fxStart = heightFull;
			var fxFinish = 0;
			var time = 1.0 * fxStart / 2000;
			if(time < 0.3)
				time = 0.3;			
			if(time > 0.8)
				time = 0.8;

			(new BX.fx({
				time: time,
				step: 0.05,
				type: 'linear',
				start: {height:fxStart, opacity:100},
				finish: {height:fxFinish, opacity:0},
				callback: BX.delegate(__blogCommentExpandSetHeight, el),
				callback_complete: BX.delegate(function() 
				{
					if(!comment)
					{
						el.style.maxHeight = fxStart+'px';
						el.style.display = "none";
					}
				})
			})).start();								
			
			if(!comment)
			{
				BX.findChild(source, {'className': 'feed-com-all-text' }, true, false).style.display = "inline-block";
				BX.findChild(source, {'className': 'feed-com-all-hide' }, true, false).style.display = "none";
				BX.removeClass(source.parentNode, "feed-com-all-expanded");
			}
		}
	}
}

function __blogCommentExpandSetHeight(state)
{
	if(state.opacity)
	{
		if (BX.browser.IsIE9())
			this.style.filter = 'alpha(opacity='+state.opacity+')';
		else
			this.style.opacity =  state.opacity/100;
	}
	this.style.maxHeight = state.height + 'px';
}

function onLightEditorShow(content)
{
	if (!window.<?=$formParams["LHE"]["jsObjName"]?>)
		return BX.addCustomEvent(window, 'LHE_OnInit', function(){setTimeout(function(){onLightEditorShow(content);}, 500);});

	<?=$formParams["LHE"]["jsObjName"]?>.SetContent(content || '');
	<?=$formParams["LHE"]["jsObjName"]?>.CreateFrame(); // We need to recreate editable frame after reappending editor container
	<?=$formParams["LHE"]["jsObjName"]?>.SetEditorContent(<?=$formParams["LHE"]["jsObjName"]?>.content);
	<?=$formParams["LHE"]["jsObjName"]?>.pFrame.style.height = <?=$formParams["LHE"]["jsObjName"]?>.arConfig.height;
	<?=$formParams["LHE"]["jsObjName"]?>.ResizeFrame();
	<?=$formParams["LHE"]["jsObjName"]?>.AutoResize();
	<?=$formParams["LHE"]["jsObjName"]?>.SetFocus();
}

function cancelComment()
{
	BX.hide(BX('form_c_del'));	
	if(lastPostComment > 0)
	{
		BX.show(BX.findChild(BX('blg-post-'+lastPostComment), {className: 'feed-com-footer'}, true, false));
		if(!BX.findChild(BX('blg-post-'+lastPostComment), {className: 'feed-com-block'}, true, false))
		{
			BX.hide(BX.findChild(BX('blg-post-'+lastPostComment), {className: 'feed-comments-block'}, true, false));
		}
		BX.hide(BX('err_comment_'+lastPostComment+'_0'));
	}
}

function showMoreComments(id, source)
{
	var lastComment = BX('comcntshow-'+id).value;
	var el = BX('blog-comment-hidden-' + id);
	if(lastComment == 0 && el.innerHTML.length > 0)
	{
		showHiddenComments(id, source, false)
	}
	else
	{
		__blogShowWait(BX.findChild(BX('blg-post-'+id), {className: 'feed-com-header'}, true, false));
			
		urlToMore = '<?=CUtil::JSEscape($arResult["urlToMore"])?>';
		url = urlToMore.replace(/#comment_id#/, lastComment);
		url = url.replace(/#post_id#/, id);

		BX.ajax.get(url, function(data) {
			var obNew = BX.processHTML(data, true);
			scripts = obNew.SCRIPT;
			BX.ajax.processScripts(scripts, true);

			var el = BX('blog-comment-hidden-' + id);
			var height = el.offsetHeight;
			el.innerHTML = data + el.innerHTML;
			__blogCloseWait();
			showHiddenComments(id, source, false, height)
		});
	}
}

bShow<?=$formParams["FORM_ID"]?> = false;

function mpfReInitLHE<?=$formParams["FORM_ID"]?>(p, p1)
{
	if(bShow<?=$formParams["FORM_ID"]?>)
	{
		if(p1 == 'log_external_container' && p.substr(0, 17) == 'sonet_log_content')
		{
			window.<?=$formParams["LHE"]["jsObjName"]?>.ReInit('<?=CUtil::JSEscape($formParams["TEXT"]["VALUE"])?>');
			BX.removeCustomEvent(window, 'onSocNetLogMoveBody', mpfReInitLHE<?=$formParams["FORM_ID"]?>);
		}
	}
	else
		setTimeout(function(){mpfReInitLHE<?=$formParams["FORM_ID"]?>(p, p1);}, 50);
}
BX.addCustomEvent(window, 'onSocNetLogMoveBody', mpfReInitLHE<?=$formParams["FORM_ID"]?>);	


<?if(!$arParams["bFromList"] && CModule::IncludeModule("pull") && IntVal($arResult["userID"]) > 0):?>
function showNewComment(id, postId)
{
	if(!BX('blg-comment-'+id))
	{
		urlToNew = '<?=CUtil::JSEscape($arResult["urlToNew"])?>';
		url = urlToNew.replace(/#comment_id#/, id);
		url = url.replace(/#post_id#/, postId);

		BX.ajax.get(url, function(data) {
			BX.show(BX.findChild(BX('blg-post-'+postId), {className: 'feed-comments-block'}, true, false));
			var obNew = BX.processHTML(data, true);
			scripts = obNew.SCRIPT;
			BX.ajax.processScripts(scripts, true);
			dataDiv = document.createElement('div');
			dataDiv.innerHTML = data;
			BX('new_comment_cont_' + postId + '_0').appendChild(dataDiv);
			BX.fx.colorAnimate.addRule('animationRule',"#FFF","#fbf2c8", "background-color", 100, 20, false);
			BX.fx.colorAnimate.addRule('animationRule2',"#fbf2c8","#FFF", "background-color", 100, 20, false);

			BX.fx.colorAnimate(BX.findChild(BX('blg-comment-'+id), {className: 'feed-com-block'}, true, false), 'animationRule');
			setTimeout(function(){BX.fx.colorAnimate(BX.findChild(BX('blg-comment-'+id), {className: 'feed-com-block'}, true, false), 'animationRule2');}, 30000);

		});

	}
}

BX.PULL.extendWatch('BLOG_POST_<?=$arParams["ID"]?>');
BX.addCustomEvent("onPullEvent", function(module_id,command,params) {
	if (module_id == "blog" && command == 'comment' && params["POST_ID"] == '<?=$arParams["ID"]?>')
	{
		if(
				!BX('blg-comment-'+params["ID"]) && 
				(
					!BX('new_comment_'+params["POST_ID"]+'_0') ||
					(BX('new_comment_'+params["POST_ID"]+'_0') && BX('new_comment_'+params["POST_ID"]+'_0').getAttribute('data-send') != "Y")
				)
			)
		{
			showNewComment(params["ID"], params["POST_ID"]);
		}

	}
});
<?endif;?>
<?
if (IsModuleInstalled("webdav"))
{
?>
	BX.addCustomEvent(
	BX.findChild(BX('<?=$arParams["FORM_ID"]?>'), {'className': 'feed-add-post' }, true, false), 'BFileDLoadFormController', function(){BX.findChild(BX('<?=$arParams["FORM_ID"]?>'), {'className': 'file-label' }, true, false).innerHTML = '<?=GetMessageJS("BLOG_P_PHOTO")?>';});
<?
}
?>
</script>