$(function() {
	KHAYR_MAIN_COMMENT_ShowMessage();
	$("body").on("click", "#KHAYR_MAIN_COMMENT_container .nav a", function() {
		BX.showWait();
		$.post($(this).attr("href"), {"ACTION": "nav"}, function(result) {
			$("#KHAYR_MAIN_COMMENT_container").html(result);
			BX.closeWait();
		});
		return false;
	});
});

function KHAYR_MAIN_COMMENT_getUrl(url, newParams)
{
	var link = document.createElement('a');
	link.href = url;
	//console.log(link.search);
	if (newParams)
	{
		if (link.search)
			link.search += '&'+newParams;
		else
			link.search = '?'+newParams;
	}
	var query = {};
	link.search.substring(1).split('&').forEach(function(value) {
		value = value.split('=');
		if (value[0] in query)
		{
			if (!(query[value[0]] instanceof Array))
				query[value[0]] = [query[value[0]]];
			query[value[0]].push(value[1]);
		}
		else
			query[value[0]] = value[1];
	});
	//console.log(query);
	var out = new Array();
	for (key in query)
		out.push(key + '=' + encodeURIComponent(query[key]));
	out = out.join('&');
	//console.log(query);
	if (out)
		link.search = "?"+out;
	else
		link.search = "";
	console.log(link.href);
	return link.href;
}

function KHAYR_MAIN_COMMENT_validate(_this, pagen)
{
	if (!pagen)
		pagen = '';
	else
		pagen = 'PAGEN_'+pagen;
	BX.showWait();
	$.ajax({
        url: KHAYR_MAIN_COMMENT_getUrl($(_this).attr("action"), pagen),
        type: 'POST',
		data: new FormData(_this),
		processData: false,
		contentType: false,
        success: function(result) {
			$("#KHAYR_MAIN_COMMENT_container").html(result);
			KHAYR_MAIN_COMMENT_ShowMessage();
		},
        error: function() {
		},
		complete: function() {
			BX.closeWait();
		}
    });
	return false;
}

function KHAYR_MAIN_COMMENT_delete(_this, id, message, pagen)
{
	if (!pagen)
		pagen = '';
	else
		pagen = 'PAGEN_'+pagen;
	if (!message)
		var message = "DELETE?";
	if (confirm(message))
	{
		BX.showWait();
		$(_this).parents(".stock:first").hide("slow");
		$.ajax({
			url: KHAYR_MAIN_COMMENT_getUrl(window.location.href, pagen),
			type: 'POST',
			data: {"ACTION": "delete", "COM_ID": id},
			success: function(result) {
				$("#KHAYR_MAIN_COMMENT_container").html(result);
				KHAYR_MAIN_COMMENT_ShowMessage();
			},
			error: function() {
			},
			complete: function() {
				BX.closeWait();
			}
		});
	}
	return false;
}
function KHAYR_MAIN_COMMENT_edit(_this, id)
{
	$(".main_form").hide();
	$(".form_for").hide();
	$("#edit_form_"+id).show();
}
function KHAYR_MAIN_COMMENT_add(_this, id)
{
	$(".main_form").hide();
	$(".form_for").hide();
	$("#add_form_"+id).show();
}
function KHAYR_MAIN_COMMENT_back()
{
	$(".main_form").show();
	$(".form_for").hide();
}

var KHAYR_MAIN_COMMENT_action = false;
function KHAYR_MAIN_COMMENT_ShowMessage()
{
	$(".khayr_main_comment_suc_exp, .khayr_main_comment_err_exp").remove();
	var err = $(".err").text();
	var suc = $(".suc").text();
	clearTimeout(KHAYR_MAIN_COMMENT_action);
	if (err.length > 0)
	{
		var exp = "<div onclick='KHAYR_MAIN_COMMENT_exp_close()' class='khayr_main_comment_err_exp'>"+err+"</div>";
		$("body").prepend(exp);
		$(".khayr_main_comment_err_exp").fadeIn(500);
	}
	else if (suc.length > 0)
	{
		var exp = "<div onclick='KHAYR_MAIN_COMMENT_exp_close()' class='khayr_main_comment_suc_exp'>"+suc+"</div>";
		$("body").prepend(exp);
		$(".khayr_main_comment_suc_exp").fadeIn(500);
	}
	
	KHAYR_MAIN_COMMENT_action = setTimeout(function() {
		KHAYR_MAIN_COMMENT_exp_close();
	}, 5000);
}
function KHAYR_MAIN_COMMENT_exp_close()
{
	$(".khayr_main_comment_suc_exp, .khayr_main_comment_err_exp").fadeOut(1000);
	setTimeout(function() {
		$(".khayr_main_comment_suc_exp, .khayr_main_comment_err_exp").remove();
	}, 1000);
}