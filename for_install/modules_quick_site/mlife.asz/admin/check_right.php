<?
$POST_RIGHT = $APPLICATION->GetGroupRight("mlife.asz");
$FilterSiteId = false;
if($POST_RIGHT == "D") {
	$arSites = \Mlife\Asz\Functions::GetGroupRightSiteId();
	if(count($arSites)>0) $FilterSiteId = $arSites;
	if($FilterSiteId) $POST_RIGHT = "W";
}
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
?>