if (typeof oObjectForum != "object")
{
	var oObjectForum = {};
}
if (typeof oForum != "object")
{
	var oForum = {};
}
/* AJAX */


function ForumReplaceNoteError(data, not_follow_url)
{
	follow_url = (not_follow_url == true ? false : true);
	eval('result = ' + data + ';');
	if (typeof(result) == "object")
	{
		for (id in {"error" : "", "note" : ""})
		{
			if (result[id])
			{
				document.getElementById("forum_" + id + "s_top").innerHTML = "";
				document.getElementById("forum_" + id + "s_bottom").innerHTML = "";
				if (result[id]["title"])
				{
					document.getElementById("forum_" + id + "s_top").innerHTML = result[id]["title"];
					document.getElementById("forum_" + id + "s_bottom").innerHTML = result[id]["title"];
				}
				if (result[id]["link"] && result[id]["link"].length > 0)
				{
					var url = result[id]["link"];
					if (url.lastIndexOf("?") == -1)
						url += "?"
					else
						url += "&";
					url += "result=" + result[id]["code"];
					document.location.href = url;
				}
			}
		}
	}
	FCloseWaitWindow('send_message');
	return;
}