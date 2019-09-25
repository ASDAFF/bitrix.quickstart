function ForumSearchTopic(oObj, bSetControl)
{
	oForum['topic_search']['object'] = oObj = (typeof(oForum['topic_search']['object']) == "object" ? oForum['topic_search']['object'] : oObj);
	if (typeof(oObj) != "object" || oObj == null)
		return false;
	bSetControl = (bSetControl == "Y" || bSetControl == "N" ? bSetControl : "U");
	oForum['topic_search']['action'] = (bSetControl == "N" ? "dont_search" : (bSetControl == "Y" ? "search" : oForum['topic_search']['action']));

	var res = parseInt(oObj.value);
	if (res <= 0 || !parseInt(res))
		BX('TOPIC_INFO').innerHTML = BX.message('topic_bad');
	else if (parseInt(oForum['topic_search']['value']) != res)
	{
		BX('TOPIC_INFO').innerHTML = BX.message('topic_wait');
		oForum['topic_search']['value'] = oObj.value;
		ForumSendMessage(oObj.value, oForum['topic_search']['url']);
	}
	if (oForum['topic_search']['action'] == "search")
		setTimeout(ForumSearchTopic, 1000);
	return false;
}

function ForumSendMessage(id, url)
{
	id = (parseInt(id) > 0 ? parseInt(id) : false);
	url = (typeof url == "string" && url.length > 0 ? url : false);
	if (!id || !url)
		return false;
	BX.ajax.get(url, {AJAX_CALL : "Y", TID : id}, function(data)
		{
			var result = false;
			try { eval('result = ' + data + ';'); } catch(e) { result = false; }
			BX('TOPIC_INFO').innerHTML = ((typeof(result) == "object" && result != null) ? result['TOPIC_TITLE'] : BX.message('topic_not_found'));
		});
}