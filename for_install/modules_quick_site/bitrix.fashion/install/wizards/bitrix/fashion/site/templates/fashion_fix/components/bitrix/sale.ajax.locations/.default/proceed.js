function loadCitiesList(country_id, arParams, path)
{
	property_id = arParams.CITY_INPUT_NAME;

	function __handlerCitiesList(data)
	{
		//alert(data);
		var obContainer = document.getElementById('LOCATION_' + property_id);
		if (obContainer)
		{
			obContainer.innerHTML = data;
			PCloseWaitMessage('wait_container_' + property_id, true);
		}
        
        $(".page-limit-select").selectBox();
	}

	arParams.COUNTRY = parseInt(country_id);
	
	if (arParams.COUNTRY <= 0) return;

	PShowWaitMessage('wait_container_' + property_id, true);
	
	var TID = CPHttpRequest.InitThread();
	CPHttpRequest.SetAction(TID,__handlerCitiesList);
	CPHttpRequest.Post(TID, path + '/components/bitrix/sale.ajax.locations/.default/ajax.php', arParams);
}