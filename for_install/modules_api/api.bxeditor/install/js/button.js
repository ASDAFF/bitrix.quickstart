(function () {
	//bitrix/js/fileman/html_editor/html-editor.js

	// 1. Create Button
	/*BX.addCustomEvent(this, "OnEditorInitedBefore", function(Editor){
		//Editor.bbCode = true
		//console.log(window.BXHtmlEditor.Controls);
		//Editor.AddButton({id: 'Quote', name: 'name', compact: false, sort: 132});
	});*/

	// 2. Add button to controls map
	BX.addCustomEvent(this, "GetControlsMap", function (controlsMap) {
		controlsMap.push({id: 'Code', compact: false, hidden: false, sort: 131});
		controlsMap.push({id: 'Quote', compact: false, hidden: false, sort: 132});
		//controlsMap.push({id: 'Smile',compact: false,hidden: false,sort: 133});
		//controlsMap.push({id: 'insertSmile',compact: false,hidden: false,sort: 133});
	});

})();