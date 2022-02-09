<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($this->__page == "user_files_menu" || $this->__page == "group_files_menu")
{
	return true;
}
elseif (strpos($this->__page, "user_files") !== false || strpos($this->__page, "group_files") !== false)
{
	$prefix = (strpos($this->__page, "user_files") !== false ? "user_files" : "group_files"); 
	$page_name = substr($this->__page, strlen($prefix) + 1); 
	
	$this->__component->__count_chain_item = count($APPLICATION->arAdditionalChain); 
	$this->__component->__buffer_template = false; 
	$this->__component->__template_html = ""; 
	
	if (in_array($page_name, array("section_edit_simple", "element_upload", "webdav_bizproc_workflow_edit", "webdav_bizproc_log")))
	{
		$sTempatePage = $this->__page;
		$sTempateFile = $this->__file;
		$this->__component->IncludeComponentTemplate($prefix."_menu");
		$this->__page = $sTempatePage;
		$this->__file = $sTempateFile;
	}
	else 
	{
		$this->__component->__socnet_page = $this->__page; 
		$this->__component->__buffer_template = true; 
		ob_start(); 
	}
}

$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
?>
<script type="text/javascript">
var phpVars;
if (typeof(phpVars) != "object")
	var phpVars = {};
phpVars.cookiePrefix = '<?=CUtil::JSEscape(COption::GetOptionString("main", "cookie_name", "BITRIX_SM"))?>';
phpVars.titlePrefix = '<?=CUtil::JSEscape(COption::GetOptionString("main", "site_name", $_SERVER["SERVER_NAME"]))?> - ';
phpVars.messLoading = '<?=CUtil::JSEscape(GetMessage("SONET_LOADING"))?>';
phpVars.LANGUAGE_ID = '<?=CUtil::JSEscape(LANGUAGE_ID)?>';
phpVars.bitrix_sessid = '<?=bitrix_sessid()?>';
if (!phpVars.ADMIN_THEME_ID)
	phpVars.ADMIN_THEME_ID = '.default';
var photoVars = {'templatePath' : '/bitrix/components/bitrix/photogallery/templates/.default/'};
if (typeof oObjectWD != "object")
	var oObjectWD = {};
</script>
