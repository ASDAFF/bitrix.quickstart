<?
global $APPLICATION, $DB, $USER, $CACHE_MANAGER;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/parnas.khayrcomment/include.php");
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("parnas.khayrcomment");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (isset($_GET["action"]))
{
	if ($_GET["action"] == "deletesections")
	{
		$IBLOCK_ID = intval($_GET["IBLOCK_ID"]);
		if (CModule::IncludeModule("iblock") && $IBLOCK_ID > 0)
		{
			$ob = CIBlockElement::GetList(Array(), Array("IBLOCK_ID" => $IBLOCK_ID, "!SECTION_ID" => false), false, false, Array("ID"));
			while ($item = $ob->GetNext())
			{
				$el = new CIBlockElement;
				$res = $el->Update($item["ID"], Array("IBLOCK_SECTION_ID" => false));
				//CIBlockElement::SetElementSection($item["ID"], Array());
			}
			$ob = CIBlockSection::GetList(Array(), Array("IBLOCK_ID" => $IBLOCK_ID, "CODE" => "ob_%"), false, Array("ID"), false);
			while ($item = $ob->GetNext())
			{
				CIBlockSection::Delete($item["ID"]);
			}
		}
	}
}
LocalRedirect("parnas.khayrcomment_list.php?lang=".LANGUAGE_ID);
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>