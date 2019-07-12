BX.namespace('BX.Sale.PersonalProfileComponent');

(function() {
	BX.Sale.PersonalProfileComponent.PersonalProfileDetail = {
		init: function ()
		{
			var propertyFileList = document.getElementsByClassName('sale-personal-profile-detail-property-file');
			Array.prototype.forEach.call(propertyFileList, function(propertyFile)
			{
				var deleteFileElement = propertyFile.getElementsByClassName('profile-property-input-delete-file')[0];
				var inputFile = propertyFile.getElementsByClassName('sale-personal-profile-detail-input-file')[0];
				var labelFileInfo = propertyFile.getElementsByClassName('sale-personal-profile-detail-load-file-info')[0];
				var cancelButton = propertyFile.getElementsByClassName('sale-personal-profile-detail-load-file-cancel')[0];

				BX.bindDelegate(propertyFile, 'click', { 'class': 'profile-property-check-file' }, BX.proxy(function(event)
				{
					if (deleteFileElement.value != "")
					{
						idList = deleteFileElement.value.split(';');
						if (idList.indexOf(event.target.value) === -1)
						{
							deleteFileElement.value = deleteFileElement.value + ";" + event.target.value;
						}
						else
						{
							idList.splice(idList.indexOf(event.target.value), 1);
							deleteFileElement.value = idList.join(";");
						}
					}
					else
					{
						deleteFileElement.value = event.target.value;
					}
				}, this));

				BX.bind(inputFile, 'change', BX.delegate(
					function(event)
					{
						if (event.target.files.length > 1)
						{
							labelFileInfo.innerHTML = BX.message('SPPD_FILE_COUNT') + event.target.files.length;
							cancelButton.classList.remove("sale-personal-profile-hide");
						}
						else if (event.target.files.length == 1)
						{
							fileName = event.target.files[0].name;
							if (fileName.length > 40)
							{
								labelFileInfo.innerHTML = fileName.substr(0,9) + "..." + fileName.substr(-9);
							}
							else
							{
								labelFileInfo.innerHTML = event.target.files[0].name;
							}
							cancelButton.classList.remove("sale-personal-profile-hide");
						}
						else
						{
							cancelButton.classList.add("sale-personal-profile-hide");
							labelFileInfo.innerHTML = BX.message('SPPD_FILE_NOT_SELECTED');
						}
					}, this)
				);
				BX.bind(cancelButton, 'click', BX.delegate(
					function()
					{
						cancelButton.classList.add("sale-personal-profile-hide");
						labelFileInfo.innerHTML = BX.message('SPPD_FILE_NOT_SELECTED');
						inputFile.value = "";
						inputFile.files = [];
					}, this)
				);
			});
		}
	}
})();
