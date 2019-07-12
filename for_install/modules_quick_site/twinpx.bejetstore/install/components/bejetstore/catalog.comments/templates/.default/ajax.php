<?
define("NO_KEEP_STATISTIC", true);
define('NO_AGENT_CHECK', true);
define("NO_AGENT_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('DisableEventsCheck', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (check_bitrix_sessid())
{
	if(
		isset($_REQUEST["IBLOCK_ID"])
		&& isset($_REQUEST["ELEMENT_ID"])
		&& isset($_SESSION["IBLOCK_CATALOG_COMMENTS_PARAMS_".$_REQUEST["IBLOCK_ID"]."_".$_REQUEST["ELEMENT_ID"]])
	)
	{
		$commParams = $_SESSION["IBLOCK_CATALOG_COMMENTS_PARAMS_".$_REQUEST["IBLOCK_ID"]."_".$_REQUEST["ELEMENT_ID"]];
	}
	else
	{
		$commParams = array();
	}
	
	/*if(SITE_ID != "s1"){
		$commParams["BLOG_URL"] = "catalog_comments_".SITE_ID;
	}*/

	if(isset($_REQUEST["SITE_ID"])){
		$commParams["SITE_ID"] = $_REQUEST["SITE_ID"];
	}

	if(!empty($commParams) && is_array($commParams))
	{
		$APPLICATION->IncludeComponent(
			"bejetstore:catalog.comments",
			"",
			$commParams,
			false
		);
	}
}

die();
?>