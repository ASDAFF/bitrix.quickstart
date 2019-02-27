<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

if (CModule::IncludeModule("parnas.khayrcomment"))
{
	if (KhayRComment::GetRightsMax() >= "W")
	{
		$count = 0;
		$ibs = KhayRComment::GetIblocks();
		foreach ($ibs as $sid => $ib)
		{
			if ($ib["RIGHTS"] < "W")
				continue;
			$count += KhayRComment::GetCount(0, false, $sid);
		}
		$aMenu = Array(
			"parent_menu" => "global_menu_services",
			"sort"        => 100,
			"url"         => "parnas.khayrcomment_list.php?lang=".LANGUAGE_ID,
			"text"        => GetMessage("KHAYR_COMMENT").($count ? " (".$count.")" : ""),
			"title"       => GetMessage("KHAYR_COMMENT"),
			"icon"        => "forum_menu_icon",
			"page_icon"   => "forum_menu_icon",
			"items_id"    => "khayr_comment",
			"items"       => Array(),
		);
		return $aMenu;
	}
	else
		return false;
}
else
	return false;
?>