<?

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule("iblock");

/**
 * В классе содержатся визуальные элементы администртивных меню.
 *
 * Class DefaToolsGetMenu
 */
class DefaToolsGetMenu
{
	/**
	 * Top level keys of this array are names of function, that use it
	 * @var array
	 */
	private static $urlPatterns = array(
		'GetActionsMenu' => array(
			'url' => array("/bitrix/admin/iblock_admin.php"),
		),
		'GetTopMenu' => array(
			'url' => array("/bitrix/admin/iblock_element_admin.php", "/bitrix/admin/iblock_section_admin.php", "/bitrix/admin/iblock_list_admin.php"),
		),
		'GetGroupActions' => array(
			'url' => array("/bitrix/admin/iblock_element_admin.php", "/bitrix/admin/iblock_section_admin.php", "/bitrix/admin/iblock_list_admin.php"),
		)
	);

	public static function getUrlPatterns()
	{
		return self::$urlPatterns;
	}

	/**
	 * Используется для добавления кнопки модуля в верхнее меню на странице просмотра инфоблока.
	 * Генерация каждой кнопки выделена в отдельный приватный метод.
	 *
	 * @param $topMenu
	 * @return array|null
	 */
	public function GetTopMenu(&$topMenu)
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;
		/** @global $USER CUser */
		global $USER;

		if (!CModule::IncludeModule("iblock") || !$USER->IsAdmin()) {
			return false;
		}

		if (
			in_array(
				$APPLICATION->GetCurPage(),
				self::$urlPatterns[__FUNCTION__]['url']
			)
			&& intval($_REQUEST['IBLOCK_ID']) > 0
		) {
			$topMenu[] = array("SEPARATOR" => "1");
			$topMenu[] = array(
				"TEXT" => GetMessage("DEFATOOLS_IB_DEMO_IB_TOOLS"),
				"TITLE" => GetMessage("DEFATOOLS_IB_DEMO_IB_TOOLS"),
				"ICON" => "btn_new_defatools",
				"MENU" => array_merge(
					self::_GetCopyTopMenu(),
					self::_GetDemoTopMenu()
				)
			);
			$APPLICATION->AddHeadString('<style> table.contextmenu #btn_new_defatools {  background-image:'
			. ' url("/bitrix/tools/defatools/menu/images/defatools_menu_icon.gif"); }  </style>');
		}

		return true;
	}

	/**
	 * Используется для изменения контекстного меню на странице редактирования типа инфоблоков.
	 *
	 * @param $list CAdminList
	 * @return array|null
	 */
	public function GetActionsMenu(&$list)
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;
		/** @global $USER CUser */
		global $USER;

		if (!CModule::IncludeModule("iblock") || !$USER->IsAdmin()) {
			return false;
		}

		if (in_array(
				$APPLICATION->GetCurPage(),
				self::$urlPatterns[__FUNCTION__]['url']
			)
			&& $_REQUEST['admin'] == "Y"
		) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$arTypesEx = CIBlockParameters::GetIBlockTypes();
			$strSelect = '<select name="defa_custom_param[type]">';
			foreach ($arTypesEx as $type => $name)
				$strSelect .= "<option value=\"" . $type . "\" " . ($type == $_REQUEST['type'] ? "selected=true" : "") .
					">" . $name . "</option>";

			$strSelect .= "</select>";
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$menu = array(
				array(
					"ICON" => "copy",
					"DEFAULT" => true,
					"TEXT" => GetMessage("DEFATOOLS_IB_DEMO_COPYIB"),
					"ACTION" => self::htmlspecialchars("javascript:(new BX.CDialog({
									content_url: '" . $APPLICATION->GetCurPageParam("", array("mode", "table_id", "defa_custom_action")) . "',
									width: 500,
									height: 140,
									resizable: false,
									draggable: false,
									title: '" . GetMessage("DEFATOOLS_IB_DEMO_COPYIB") . "',
									head: '" . GetMessage("DEFATOOLS_IB_DEMO_CHOOSE_COPY_PARAM") . "',
									content: '<form action=\"\" name=\"defa_custom_action_form\"><input type=\"hidden\" name=\"defa_custom_action\" value=\"ib_copy_ib\"><table><tr><td>"
					. GetMessage("DEFATOOLS_IB_DEMO_COPYIB_TO_TYPE") . ": </td><td>" . $strSelect . "</td></tr><tr><td>"
					. GetMessage("DEFATOOLS_IB_DEMO_COPYIB_CONTENT") . ": </td><td><input name=\"defa_custom_param[copy_content]\" "
					. (COption::GetOptionString("main", "_demo_content_copy_content", "Y") == "Y" ? "checked" : "")
					. " type=\"checkbox\" value=\"Y\" /></td></tr>"))
			);

			if (!strncmp($list->table_id, 'tbl_iblock_admin', 16 && false) && count($menu)) {
				foreach ($list->aRows as $row) {
					$row_menu = $menu;
					$row_menu[0]['ACTION'] .= self::htmlspecialchars("<input type=\"hidden\" name=\"defa_custom_id\" value=\"" . $row->id . "\">"
						. "</table></form>',
                 buttons: [BX.CDialog.btnSave, BX.CDialog.btnCancel]})).Show()");
					$row->addActions(array_merge($row->aActions, $row_menu));
				}
			}

		}  elseif (in_array($APPLICATION->GetCurPage(), self::$urlPatterns['GetGroupActions']['url'])) {
			$list->arActions["ib_copy_ib_section_to_new_ib"] = GetMessage("DEFATOOLS_IB_DEMO_COPY_SECTION_TO_THE_NEW_IB");
		}

		return true;
	}



	/**
	 * Верхнее меню с кнопками копирования инфоблока
	 */
	private static function _GetCopyTopMenu()
	{
		/** @global $APPLICATION CMain */
		global $APPLICATION;

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$arTypesEx = CIBlockParameters::GetIBlockTypes();
		/** @var $rs CDBResult */
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$rs        = CIBlock::GetById(intval($_REQUEST['IBLOCK_ID']));
		$resIblock = $rs->GetNext();
		$strSelect = '<select name="defa_custom_param[type]">';
		foreach ($arTypesEx as $type => $name)
			$strSelect .= "<option value=\"" . $type . "\" " . ($type == $resIblock["IBLOCK_TYPE_ID"] ? "selected=true" : "") .
				">" . $name . "</option>";
		$strSelect .= "</select>";
		$menu = array();
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$menu[] = array(
			"TEXT" => GetMessage("DEFATOOLS_IB_DEMO_COPYIB"),
			"TITLE" => GetMessage("DEFATOOLS_IB_DEMO_COPYIB"),
			"ACTION" => self::htmlspecialchars("javascript:(new BX.CDialog({
					content_url: '" . $APPLICATION->GetCurPageParam("", array("mode", "table_id", "defa_custom_action")) . "',
					width: 500,
					height: 140,
					resizable: false,
					draggable: false,
					title: '" . GetMessage("DEFATOOLS_IB_DEMO_COPYIB") . "',
					head: '" . GetMessage("DEFATOOLS_IB_DEMO_CHOOSE_COPY_PARAM") . "',
					content: '<form action=\"\" name=\"defa_custom_action_form\"><input type=\"hidden\" name=\"defa_custom_action\" value=\"ib_copy_ib\"><table><tr><td>"
			. GetMessage("DEFATOOLS_IB_DEMO_COPYIB_TO_TYPE") . ": </td><td>" . $strSelect . "</td></tr><tr><td>"
			. GetMessage("DEFATOOLS_IB_DEMO_COPYIB_CONTENT") . ": </td><td><input name=\"defa_custom_param[copy_content]\" "
			. (COption::GetOptionString("main", "_demo_content_copy_content", "Y") == "Y" ? "checked" : "")
			. " type=\"checkbox\" value=\"Y\" /></td></tr></table></form>',
					buttons: [BX.CDialog.btnSave, BX.CDialog.btnCancel]
				})).Show()"),
			"ICON" => "copy",
		);

		return $menu;
	}

	/**
	 * Верхнее меню с кнопками наполнения инфоблока демо элементами
	 */
	private static function _GetDemoTopMenu()
	{

		/** @global $APPLICATION CMain */
		global $APPLICATION;

		/** @var $rs CDBResult */
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$rs        = CIBlock::GetById(intval($_REQUEST['IBLOCK_ID']));
		$resIblock = $rs->GetNext();
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$resIblockType = CIBlockType::GetById($resIblock["IBLOCK_TYPE_ID"])->GetNext();

		$demoParams       = array(
			"cnt" => array(
				"TYPE" => "T",
				"NAME" => GetMessage("DEFATOOLS_IB_DEMO_ADD_ELS"),
				"DEFAULT" => 15
			),
			"notactive" => array(
				"TYPE" => "B", "NAME" => GetMessage("DEFATOOLS_IB_DEMO_CREATE_ACT_NONACT"), "DEFAULT" => "Y")
		);
		$arIblockSections = array();
		if ($resIblockType["SECTIONS"] == "Y") {
			$arPopupDemoParams = array(
				"notactive" => array(
					"TYPE" => "B",
					"NAME" => GetMessage("DEFATOOLS_IB_DEMO_CREATE_ACT_NONACT_SECT"),
					"DEFAULT" => "Y",
				),
				"sections_cnt" => array(
					"TYPE" => "T",
					"NAME" => GetMessage("DEFATOOLS_IB_DEMO_ADD_SECTS"),
					"DEFAULT" => 15,
				),
				"create_sections_depth_level" => array(
					"TYPE" => "T",
					"NAME" => GetMessage("DEFATOOLS_IB_DEMO_SECTS_MAX_DEPTH"),
					"DEFAULT" => "2",
				),
				"elements2last_depth_level" => array(
					"TYPE" => "B",
					"NAME" => GetMessage("DEFATOOLS_IB_DEMO_ADD_ELS_IN_LAST_SEC"),
					"DEFAULT" => "Y",
				),
			);

			if (count($arIblockSections)) {
				$arPopupDemoParams["add_els_in_existed_sections"] = array(
					"TYPE" => "B",
					"NAME" => GetMessage("DEFATOOLS_IB_DEMO_ADD_ELS_IN_EXISTED_SEC"),
					"DEFAULT" => "Y",
				);
			}
			$demoParams += $arPopupDemoParams;
		}

		$strParams = "<table>";
		foreach ($demoParams as $code => $val) {

			$strParams .= "<tr><td>" . $val["NAME"] . ": </td><td>";

			switch ($val["TYPE"]) {
				case "T":
				case "S":
					/** @noinspection PhpDynamicAsStaticMethodCallInspection */
					$strParams .= "<input name=\"defa_custom_param[" . $code . "]\" " . ($val["TYPE"] == "T" ? "size=\"4\"" : "") . " type=\"text\" value=\""
						. COption::GetOptionString(DefaTools::MODULE_ID, "_demo_content_" . $code, $val["DEFAULT"]) . "\" />";
					break;
				case "B":
					/** @noinspection PhpDynamicAsStaticMethodCallInspection */
					$strParams .= "<input name=\"defa_custom_param[" . $code . "]\" "
						. (COption::GetOptionString(DefaTools::MODULE_ID, "_demo_content_" . $code, $val["DEFAULT"]) == "Y" ? "checked" : "") . " type=\"checkbox\" value=\"Y\" />";
					break;
			}

			$strParams .= "</td></tr>";
		}

		$strParams .= "</table>";

		$menu   = array();
		$menu[] = array(
			"TEXT" => GetMessage("DEFATOOLS_IB_DEMO_FILL_IBLOCK"),
			"TITLE" => GetMessage("DEFATOOLS_IB_DEMO_FILL_IBLOCK"),
			"ACTION" => self::htmlspecialchars("javascript:(new BX.CDialog({
					content_url: '" . $APPLICATION->GetCurPageParam("", array("mode", "table_id", "defa_custom_action")) . "',
					width: 500,
					height: 300,
					resizable: false,
					draggable: false,
					title: '" . GetMessage("DEFATOOLS_IB_DEMO_FILL_IBLOCK") . "',
					head: '" . GetMessage("DEFATOOLS_IB_DEMO_CHOOSE_FILL_PARAM") . "',
					content: '<form action=\"\" name=\"defa_custom_action_form\"><input type=\"hidden\" name=\"defa_custom_action\" value=\"ib_add_demo_content\">"
			. $strParams . "</form>',
					buttons: [BX.CDialog.btnSave, BX.CDialog.btnCancel]
				})).Show()"),
			"ICON" => "copy",
		);
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		$menu[] = array(
			"TEXT" => GetMessage("DEFATOOLS_IB_DEMO_DEL_DATA"),
			"TITLE" => GetMessage("DEFATOOLS_IB_DEMO_DEL_DATA"),
			"ACTION" => self::htmlspecialchars("javascript:if(confirm('" . GetMessage("DEFATOOLS_IB_DEMO_CONFIRM_DEL_DATA") . "')) window.location='"
			. CUtil::JSEscape($APPLICATION->GetCurPageParam("defa_custom_action=ib_delete_demo_content",
				array("mode", "table_id", "defa_custom_action"))) . "'"),
			"ICON" => "delete",
		);

		return $menu;
	}

	/**
	 * HACK: для версия ниже 11.5.9.
	 *
	 * http://dev.1c-bitrix.ru/community/blogs/product_features/6197.php
	 *
	 * @param $string
	 * @param int $flags
	 * @return string
	 */
	public static function htmlspecialchars($string, $flags=ENT_COMPAT)
	{
		return htmlspecialchars($string, $flags, (defined("BX_UTF")? "UTF-8" : "ISO-8859-1"));
	}
}
