<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/jivosite.jivosite/config.php');

class JivoSiteClass{
	
	public function addScriptTag(){
		global $APPLICATION;
		$widget_id = COption::GetOptionString("jivosite.jivosite", "widget_id");
		//$APPLICATION->AddHeadScript("//".JIVO_CODE_URL."/script/widget/$widget_id");
		$APPLICATION->AddHeadString("\n<!-- BEGIN JIVOSITE CODE -->
<script type='text/javascript'>
(function(){ var widget_id = '$widget_id';
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();</script>
<!-- END JIVOSITE CODE -->\n");
	}
	
}

?>