var obSearch;
function liveapi()
{
	if (obSearch = document.getElementById('bx-search-input'))
	{
		console.log('Live api started');
		obSearch.onkeydown = function(evt) 
		{
			var code = evt ? evt.which : window.event.keyCode;
			if (code == 13)
				document.location = '/bitrix/admin/bitrix.liveapi_live_api.php?search=' + obSearch.value; 
		}
	}
}
window.setTimeout(liveapi,100);
