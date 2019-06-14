function OnApiReviewsSettingsEdit(arParams) {
	if (null != window.jsApiReviewsOpener) {
		try {window.jsApiReviewsOpener.Close();} catch (e) {}
		window.jsApiReviewsOpener = null;
	}
	window.jsApiReviewsOpener = new JCEditorOpener(arParams);
}

function JCEditorOpener(arParams) {
	//console.log('arParams', arParams);
	//this.jsOptions = arParams.data.split('||');
	this.arParams = arParams;

	//Создаём текстовое поле
	var obInput = document.createElement('INPUT');
	obInput.id = "CURRENT_PROPERTY_FILE_TYPE";
	obInput.type = "text";
	obInput.value = this.arParams.oInput.value;

	// добавляем в контейнер
	this.arParams.oCont.appendChild(obInput);

	//Создаём список параметров
	var obSelect = document.createElement('select');

	for (var i in this.arParams.data) {
		var option = document.createElement("option");
		option.value = i;
		option.text = this.arParams.data[i];

		if(obInput.value && obInput.value == i){
			option.selected = true;
		}

		obSelect.appendChild(option);
	}

	// добавляем в контейнер
	this.arParams.oCont.appendChild(obSelect);

	//obButton.innerHTML = this.jsOptions[1];//текст из JS_DATA

	obInput.onchange = function () {
		arParams.oInput.value = this.value;
	};

	obSelect.onchange = function () {
		//document.getElementById('CURRENT_PROPERTY_FILE_TYPE').value = this[this.selectedIndex].value;
		document.getElementById('CURRENT_PROPERTY_FILE_TYPE').value = this.options[this.selectedIndex].value;
		arParams.oInput.value = this[this.selectedIndex].value;
	};
}