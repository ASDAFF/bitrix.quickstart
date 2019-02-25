<?
IncludeModuleLangFile(__FILE__);

/**
 * В классе собраны обработчики ajax запросов модуля.
 *
 * Class DefaToolsController
 */
class DefaToolsController
{
	public static function OnAdminContextMenuShowHandler()
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;
		/** @global $USER CUser */
		global $USER;

		if (!CModule::IncludeModule("iblock") || !$USER->IsAdmin()) {
			return false;
		}

		$action = $_REQUEST['defa_custom_action'];
		$group_action = $_REQUEST["action"];

		// DANGER!!! Exceptions
		try {
			switch ($action) {
				case 'ib_copy_ib':
					self::_CopyIB();
					break;
				case "ib_add_demo_content":
					self::_AddDemoContent();
					break;
				case "ib_delete_demo_content":
					self::_DeleteDemoContent();
					break;
				default:
					break;
			}
		} catch (DefaToolsException $e) {
			$APPLICATION->ThrowException($e->getMessage());
		}

		// DANGER!!! Exceptions
		try {
			switch ($group_action) {
				case "ib_copy_ib_section_to_new_ib":
					self::_CopySectionToIB();
					break;
				default:
					break;
			}
		} catch (DefaToolsException $e) {
			$APPLICATION->ThrowException($e->getMessage());
		}

		return true;
	}

	private static function _CopyIB()
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;

		$APPLICATION->RestartBuffer();

		$ib = intval($_REQUEST["defa_custom_id"]) ? intval($_REQUEST["defa_custom_id"]) : intval($_REQUEST['IBLOCK_ID']);

		if (!$ib) {
			return false;
		}

		$new_id = DefaToolsCopy::iblock($ib)
			->WithContent(($_REQUEST["defa_custom_param"]["copy_content"] == "Y"))
			->ToType($_REQUEST["defa_custom_param"]["type"]);

		$APPLICATION->RestartBuffer();

		$new_url = preg_replace("/(iblock_admin)/i", "iblock_list_admin", $APPLICATION->GetCurPageParam("IBLOCK_ID=" . $new_id . "&type="
		. $_REQUEST["defa_custom_param"]["type"], array("defa_custom_action", "IBLOCK_ID", "type", "find_section_section", "admin")));
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		echo "<div align=\"center\"><a style=\"font-size: 20px\" href=\"javascript:window.location='"
			. CUtil::JSEscape($new_url)
			. "'\">" . GetMessage("DEFATOOLS_IB_DEMO_GOTO_NEW_IB") . "</a></div>";
		die();
	}

	private static function _DeleteDemoContent()
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;

		$ib = intval($_REQUEST["defa_custom_id"]) ? intval($_REQUEST["defa_custom_id"]) : intval($_REQUEST['IBLOCK_ID']);

		if (DefaToolsDemo::DeleteFromIBlock($ib)) {
			LocalRedirect($APPLICATION->GetCurPageParam("", array("defa_custom_action")));
		}

	}

	private static function _AddDemoContent()
	{
		$ib = intval($_REQUEST["defa_custom_id"]) ? intval($_REQUEST["defa_custom_id"]) : intval($_REQUEST['IBLOCK_ID']);
		DefaToolsDemo::AddToIBlock($ib, $_REQUEST["defa_custom_param"]);
	}

	private static function _CopySectionToIB()
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;

		$sectionIds = array();

		foreach ($_REQUEST["ID"] as $mixedID) {
			if (substr($mixedID, 0, 1) == "S") {
				$sectionIds[] = substr($mixedID, 1);
			}
		}

		foreach ($sectionIds as $id) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$rsSections = CIBlockSection::GetList(array(), array("SECTION_ID" => $id), true, array("ID", "LEFT_MARGIN", "RIGHT_MARGIN"));
			while ($arSection = $rsSections->GetNext()) {
				$sectionIds[] = $arSection['ID'];
			}
		}

		$new_id = DefaToolsCopy::sections($sectionIds)->WithContent()->ToType($_REQUEST["type"]);

		if ($new_id > 0) {
			?>
			<script>top.location.href = '<?=$APPLICATION->GetCurPageParam("IBLOCK_ID=".$new_id, array("IBLOCK_ID", "mode", "find_section_section"))?>';</script><?
		}
	}
}