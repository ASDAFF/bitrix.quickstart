<?
class DefaTools_Typograf
{
	function addEditorScriptsHandler($editorName, $arEditorParams) {
		return array( "JS" => array("typograf.js"));
	}
	
	function OnIncludeHTMLEditorHandler() {
		$GLOBALS['APPLICATION']->AddHeadScript("/bitrix/admin/defa_tools_js.php?lang=".LANGUAGE_ID);
		$GLOBALS['APPLICATION']->AddHeadScript("/bitrix/js/main/ajax.js");
	}
}

