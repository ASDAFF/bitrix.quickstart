BX.namespace("BX.Iblock.Catalog");

BX.Iblock.Catalog.CompareClass = (function()
{
	var CompareClass = function(wrapObjId)
	{
		this.wrapObjId = wrapObjId;
	};

	CompareClass.prototype.MakeAjaxAction = function(url, refresh)
	{
		BX.showWait(BX(this.wrapObjId));
		BX.ajax.post(
			url,
			{
				ajax_action: 'Y'
			},
			BX.proxy(function(result)
			{
				BX(this.wrapObjId).innerHTML = result;
				if(typeof refresh !== undefined){
					getActualBasket();
					jsAjaxUtil.InsertDataToNode(arOptimusOptions['SITE_DIR'] + 'ajax/show_compare_preview_top.php', 'compare_line', false);
					if($('#compare_fly').length){
						jsAjaxUtil.InsertDataToNode(arOptimusOptions['SITE_DIR'] + 'ajax/show_compare_preview_fly.php', 'compare_fly', false);
					}
				}
				BX.closeWait();
			}, this)
		);
	};

	return CompareClass;
})();