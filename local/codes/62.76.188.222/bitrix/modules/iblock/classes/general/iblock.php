<?
IncludeModuleLangFile(__FILE__);

class CAllIBlock
{
	function ShowPanel($IBLOCK_ID=0, $ELEMENT_ID=0, $SECTION_ID="", $type="news", $bGetIcons=false, $componentName="", $arLabels=array())
	{

		global $APPLICATION, $USER;
		if(!(($USER->IsAuthorized() || $APPLICATION->ShowPanel===true) && $APPLICATION->ShowPanel!==false))
			return;

		if(!CModule::IncludeModule("iblock") || !strlen($type))
			return;

		$arButtons = CIBlock::GetPanelButtons($IBLOCK_ID, $ELEMENT_ID, $SECTION_ID, array(
			"LABELS" => $arLabels,
		));

		$mode = $APPLICATION->GetPublicShowMode();

		if($bGetIcons)
		{
			return CIBlock::GetComponentMenu($mode, $arButtons);
		}
		else
		{
			CIBlock::AddPanelButtons($mode, $componentName, $arButtons);
		}
	}

	function AddPanelButtons($mode, $componentName, $arButtons)
	{
		global $APPLICATION;

		$arImages = array(
			"add_element" => (defined("PANEL_ADD_ELEMENT_BTN")) ? PANEL_ADD_ELEMENT_BTN : "/bitrix/images/iblock/icons/new_element.gif",
			"edit_element" => (defined("PANEL_EDIT_ELEMENT_BTN")) ? PANEL_EDIT_ELEMENT_BTN : "/bitrix/images/iblock/icons/edit_element.gif",
			"edit_iblock" => (defined("PANEL_EDIT_IBLOCK_BTN")) ? PANEL_EDIT_IBLOCK_BTN : "/bitrix/images/iblock/icons/edit_iblock.gif",
			"history_element" => (defined("PANEL_HISTORY_ELEMENT_BTN")) ? PANEL_HISTORY_ELEMENT_BTN : "/bitrix/images/iblock/icons/history.gif",
			"edit_section" => (defined("PANEL_EDIT_SECTION_BTN")) ? PANEL_EDIT_SECTION_BTN : "/bitrix/images/iblock/icons/edit_section.gif",
			"add_section" => (defined("PANEL_ADD_SECTION_BTN")) ? PANEL_ADD_SECTION_BTN : "/bitrix/images/iblock/icons/new_section.gif",
			"element_list" => "/bitrix/themes/.default/icons/iblock/mnu_iblock_el.gif",
			"section_list" => "/bitrix/themes/.default/icons/iblock/mnu_iblock_sec.gif",
		);

		if(count($arButtons[$mode]) > 0)
		{
			//Try to detect component via backtrace
			if(strlen($componentName) <= 0 && function_exists("debug_backtrace"))
			{
				$arTrace = debug_backtrace();
				foreach($arTrace as $i => $arCallInfo)
				{
					if(array_key_exists("file", $arCallInfo))
					{
						$file = strtolower(str_replace("\\", "/", $arCallInfo["file"]));
						if(preg_match("#.*/bitrix/components/(.+?)/(.+?)/#", $file, $match))
						{
							$componentName = $match[1].":".$match[2];
							break;
						}
					}
				}
			}
			if(strlen($componentName))
			{
				$arComponentDescription = CComponentUtil::GetComponentDescr($componentName);
				if(is_array($arComponentDescription) && strlen($arComponentDescription["NAME"]))
					$componentName = $arComponentDescription["NAME"];
			}
			else
			{
				$componentName = GetMessage("IBLOCK_PANEL_UNKNOWN_COMPONENT");
			}

			$arPanelButton = array(
				"SRC" => "/bitrix/images/iblock/icons/iblock.gif",
				"ALT" => $componentName,
				"TEXT" => $componentName,
				"MAIN_SORT" => 300,
				"SORT" => 30,
				"MENU" => array(),
				"MODE" => $mode,
			);

			foreach($arButtons[$mode] as $i=>$arSubButton)
			{
				$arSubButton['IMAGE'] = $arImages[$i];

				if($arSubButton["DEFAULT"])
					$arPanelButton["HREF"] = $arSubButton["ACTION"];

				$arPanelButton["MENU"][] = $arSubButton;
			}

			if(count($arButtons["submenu"]) > 0)
			{
				$arSubMenu = array(
					"SRC" => "/bitrix/images/iblock/icons/iblock.gif",
					"ALT" => GetMessage("IBLOCK_PANEL_CONTROL_PANEL_ALT"),
					"TEXT" => GetMessage("IBLOCK_PANEL_CONTROL_PANEL"),
					"MENU" => array(),
					"MODE" => $mode,
				);

				foreach($arButtons["submenu"] as $i=>$arSubButton)
				{
					$arSubButton['IMAGE'] = $arImages[$i];
					$arSubMenu["MENU"][] = $arSubButton;
				}

				$arPanelButton["MENU"][] = array("SEPARATOR" => "Y");
				$arPanelButton["MENU"][] = $arSubMenu;
			}
			$APPLICATION->AddPanelButton($arPanelButton);
		}

		if(count($arButtons["intranet"]) > 0 && CModule::IncludeModule("intranet"))
		{
			global $INTRANET_TOOLBAR;
			foreach($arButtons["intranet"] as $arButton)
				$INTRANET_TOOLBAR->AddButton($arButton);
		}
	}

	function GetComponentMenu($mode, $arButtons)
	{
		$arImages = array(
			"add_element" => "/bitrix/images/iblock/icons/new_element.gif",
			"edit_element" => "/bitrix/images/iblock/icons/edit_element.gif",
			"edit_iblock" => "/bitrix/images/iblock/icons/edit_iblock.gif",
			"history_element" => "/bitrix/images/iblock/icons/history.gif",
			"edit_section" => "/bitrix/images/iblock/icons/edit_section.gif",
			"add_section" => "/bitrix/images/iblock/icons/new_section.gif",
			"element_list" => "/bitrix/themes/.default/icons/iblock/mnu_iblock_el.gif",
			"section_list" => "/bitrix/themes/.default/icons/iblock/mnu_iblock_sec.gif",
		);

		$arResult = array();
		foreach($arButtons[$mode] as $i=>$arButton)
		{
			$arButton['URL'] = $arButton['ACTION'];
			unset($arButton['ACTION']);
			$arButton['IMAGE'] = $arImages[$i];
			$arResult[] = $arButton;
		}
		return $arResult;
	}

	function GetPanelButtons($IBLOCK_ID=0, $ELEMENT_ID=0, $SECTION_ID=0, $arOptions=array())
	{
		global $APPLICATION, $USER;

		$arButtons = array(
			"view" => array(),
			"edit" => array(),
			"configure" => array(),
			"submenu" => array(),
		);

		if(array_key_exists("SECTION_BUTTONS", $arOptions) && $arOptions["SECTION_BUTTONS"] === false)
			$bSectionButtons = false;
		else
			$bSectionButtons = true;

		if(array_key_exists("SESSID", $arOptions) && $arOptions["SESSID"] === false)
			$bSessID = false;
		else
			$bSessID = true;

		$IBLOCK_ID = intval($IBLOCK_ID);
		$ELEMENT_ID = intval($ELEMENT_ID);
		$SECTION_ID = intval($SECTION_ID);

		if(($ELEMENT_ID > 0) && (($IBLOCK_ID <= 0) || ($bSectionButtons && $SECTION_ID == 0)))
		{
			$rsIBlockElement = CIBlockElement::GetList(array(), array(
				"ID" => $ELEMENT_ID,
				"ACTIVE_DATE" => "Y",
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
			), false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
			if($arIBlockElement = $rsIBlockElement->Fetch())
			{
				$IBLOCK_ID = $arIBlockElement["IBLOCK_ID"];
				$SECTION_ID = $arIBlockElement["IBLOCK_SECTION_ID"];
			}
		}

		if($IBLOCK_ID <= 0)
			return $arButtons;

		$bCatalog = false;
		if(isset($arOptions["CATALOG"]) && $arOptions["CATALOG"] == true)
		{
			if(CModule::IncludeModule('catalog'))
				$bCatalog = true;
		}

		$return_url = array(
			"add_element" => "",
			"edit_element" => "",
			"edit_iblock" => "",
			"history_element" => "",
			"edit_section" => "",
			"add_section" => "",
			"delete_section" => "",
			"delete_element" => "",
			"element_list" => "",
			"section_list" => "",
		);

		if(array_key_exists("RETURN_URL", $arOptions))
		{
			if(is_array($arOptions["RETURN_URL"]))
			{
				foreach($arOptions["RETURN_URL"] as $key => $url)
					if(!empty($url) && array_key_exists($key, $return_url))
						$return_url[$key] = $url;
			}
			elseif(!empty($arOptions["RETURN_URL"]))
			{
				foreach($return_url as $key => $url)
					$return_url[$key] = $arOptions["RETURN_URL"];
			}
		}

		$str = "";
		foreach($return_url as $key => $url)
		{
			if(empty($url))
			{
				if(empty($str))
				{
					if(defined("BX_AJAX_PARAM_ID"))
						$str = $APPLICATION->GetCurPageParam("", array(BX_AJAX_PARAM_ID));
					else
						$str = $APPLICATION->GetCurPageParam();
				}

				$return_url[$key] = $str;
			}
		}

		$arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);
		$bWorkflow = CModule::IncludeModule("workflow") && ($arIBlock["WORKFLOW"] !== "N");
		$s = $bWorkflow? "&WF=Y": "";

		$arLabels = $arOptions["LABELS"];

		if($ELEMENT_ID > 0 && CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ELEMENT_ID, "element_edit"))
		{
			$url = "/bitrix/admin/".CIBlock::GetAdminElementEditLink($IBLOCK_ID, $ELEMENT_ID, array(
				"force_catalog" => $bCatalog,
				"filter_section" => $SECTION_ID,
				"bxpublic" => "Y",
				"from_module" => "iblock",
				"return_url" => $return_url["edit_element"],
			));

			$action = $APPLICATION->GetPopupLink(
				array(
					"URL" => $url,
					"PARAMS" => array(
						"width" => 700, 'height' => 400, 'resize' => false,
					),
				)
			);

			$arButton = array(
				"TEXT" => (strlen($arLabels["ELEMENT_EDIT_TEXT"])? $arLabels["ELEMENT_EDIT_TEXT"]: $arIBlock["ELEMENT_EDIT"]),
				"TITLE" => (strlen($arLabels["ELEMENT_EDIT_TITLE"])? $arLabels["ELEMENT_EDIT_TITLE"]: $arIBlock["ELEMENT_EDIT"]),
				"ACTION" => 'javascript:'.$action,
				"ACTION_URL" => $url,
				"ONCLICK" => $action,
				"DEFAULT" => ($APPLICATION->GetPublicShowMode() != 'configure'? true: false),
				"ICON" => "bx-context-toolbar-edit-icon",
				"ID" => "bx-context-toolbar-edit-element"
			);
			$arButtons["edit"]["edit_element"] = $arButton;
			$arButtons["configure"]["edit_element"] = $arButton;

			$url = str_replace("&bxpublic=Y&from_module=iblock", "", $url);
			$arButton["ACTION"] = "javascript:jsUtils.Redirect([], '".CUtil::JSEscape($url)."')";
			unset($arButton["ONCLICK"]);
			$arButtons["submenu"]["edit_element"] = $arButton;

			if($bWorkflow)
			{
				$url = "/bitrix/admin/iblock_history_list.php?type=".$arIBlock["IBLOCK_TYPE_ID"]."&lang=".LANGUAGE_ID."&IBLOCK_ID=".$IBLOCK_ID."&ELEMENT_ID=".$ELEMENT_ID."&filter_section=".$SECTION_ID."&return_url=".UrlEncode($return_url["history_element"]);
				$arButton = array(
					"TEXT" => GetMessage("IBLOCK_PANEL_HISTORY_BUTTON"),
					"TITLE" => GetMessage("IBLOCK_PANEL_HISTORY_BUTTON"),
					"ACTION" => "javascript:jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
					"ONCLICK" => "jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
					"ID" => "bx-context-toolbar-history-element"
				);
				$arButtons["submenu"]["history_element"] = $arButton;
			}
		}

		if(CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $SECTION_ID, "section_element_bind"))
		{
			$url = "/bitrix/admin/".CIBlock::GetAdminElementEditLink($IBLOCK_ID, null, array(
				"force_catalog" => $bCatalog,
				"filter_section" => $SECTION_ID,
				"IBLOCK_SECTION_ID" => $SECTION_ID,
				"bxpublic" => "Y",
				"from_module" => "iblock",
				"return_url" => $return_url["add_element"],
			));

			$action = $APPLICATION->GetPopupLink(
				array(
					"URL" => $url,
					"PARAMS" => array(
						"width" => 700,
						'height' => 400,
						'resize' => false,
					),
				)
			);
			$arButton = array(
				"TEXT" => (strlen($arLabels["ELEMENT_ADD_TEXT"])? $arLabels["ELEMENT_ADD_TEXT"]: $arIBlock["ELEMENT_ADD"]),
				"TITLE" => (strlen($arLabels["ELEMENT_ADD_TITLE"])? $arLabels["ELEMENT_ADD_TITLE"]: $arIBlock["ELEMENT_ADD"]),
				"ACTION" => 'javascript:'.$action,
				"ACTION_URL" => $url,
				"ONCLICK" => $action,
				"ICON" => "bx-context-toolbar-create-icon",
				"ID" => "bx-context-toolbar-add-element",
			);
			$arButtons["edit"]["add_element"] = $arButton;
			$arButtons["configure"]["add_element"] = $arButton;
			$arButtons["intranet"][] = array(
				'TEXT' => $arButton["TEXT"],
				'TITLE' => $arButton["TITLE"],
				'ICON'	=> 'add',
				'ONCLICK' => $arButton["ACTION"],
				'SORT' => 1000,
			);

			$url = str_replace("&bxpublic=Y&from_module=iblock", "", $url);
			$arButton["ACTION"] = "javascript:jsUtils.Redirect([], '".CUtil::JSEscape($url)."')";
			unset($arButton["ONCLICK"]);
			$arButtons["submenu"]["add_element"] = $arButton;
		}

		if($ELEMENT_ID > 0 && CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ELEMENT_ID, "element_delete"))
		{
			//Delete Element
			if(!empty($arButtons["edit"]))
				$arButtons["edit"][] = array("SEPARATOR" => "Y", "HREF" => "");
			if(!empty($arButtons["configure"]))
				$arButtons["configure"][] = array("SEPARATOR" => "Y", "HREF" => "");
			if(!empty($arButtons["submenu"]))
				$arButtons["submenu"][] = array("SEPARATOR" => "Y", "HREF" => "");

			$url = CIBlock::GetAdminElementListLink($IBLOCK_ID, array('action'=>'delete'));
			if($bSessID)
				$url .= '&'.bitrix_sessid_get();
			$url .= '&ID='.(preg_match('/^iblock_list_admin\.php/', $url)? "E": "").$ELEMENT_ID."&return_url=".UrlEncode($return_url["delete_element"]);
			$url = "/bitrix/admin/".$url;
			$arButton = array(
				"TEXT" => (strlen($arLabels["ELEMENT_DELETE_TEXT"])? $arLabels["ELEMENT_DELETE_TEXT"]: $arIBlock["ELEMENT_DELETE"]),
				"TITLE" => (strlen($arLabels["ELEMENT_DELETE_TITLE"])? $arLabels["ELEMENT_DELETE_TITLE"]: $arIBlock["ELEMENT_DELETE"]),
				"ACTION"=>"javascript:if(confirm('".GetMessage("IBLOCK_PANEL_ELEMENT_DEL_CONF")."'))jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
				"ACTION_URL" => $url,
				"ONCLICK"=>"if(confirm('".GetMessage("IBLOCK_PANEL_ELEMENT_DEL_CONF")."'))jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
				"ICON" => "bx-context-toolbar-delete-icon",
				"ID" => "bx-context-toolbar-delete-element"
			);
			$arButtons["edit"]["delete_element"] = $arButton;
			$arButtons["configure"]["delete_element"] = $arButton;
			$arButtons["submenu"]["delete_element"] = $arButton;
		}

		if($ELEMENT_ID <= 0 && $bSectionButtons)
		{
			$rsIBTYPE = CIBlockType::GetByID($arIBlock["IBLOCK_TYPE_ID"]);
			if(($arIBTYPE = $rsIBTYPE->Fetch()) && ($arIBTYPE["SECTIONS"] == "Y"))
			{
				if($SECTION_ID > 0 && CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $SECTION_ID, "section_edit"))
				{
					if(!empty($arButtons["edit"]))
						$arButtons["edit"][] = array("SEPARATOR" => "Y", "HREF" => "");
					if(!empty($arButtons["configure"]))
						$arButtons["configure"][] = array("SEPARATOR" => "Y", "HREF" => "");
					if(!empty($arButtons["submenu"]))
						$arButtons["submenu"][] = array("SEPARATOR" => "Y", "HREF" => "");

					$url = "/bitrix/admin/".CIBlock::GetAdminSectionEditLink($IBLOCK_ID, $SECTION_ID, array(
						"force_catalog" => $bCatalog,
						"filter_section" => $SECTION_ID,
						"bxpublic" => "Y",
						"from_module" => "iblock",
						"return_url" => $return_url["edit_section"],
					));

					$action = $APPLICATION->GetPopupLink(
						array(
							"URL" => $url,
							"PARAMS" => array(
								"width" => 700, 'height' => 400, 'resize' => false,
							),
						)
					);

					$arButton = array(
						"TEXT" => (strlen($arLabels["SECTION_EDIT_TEXT"])? $arLabels["SECTION_EDIT_TEXT"]: $arIBlock["SECTION_EDIT"]),
						"TITLE" => (strlen($arLabels["SECTION_EDIT_TITLE"])? $arLabels["SECTION_EDIT_TITLE"]: $arIBlock["SECTION_EDIT"]),
						"ACTION" => 'javascript:'.$action,
						"ACTION_URL" => $url,
						"ICON" => "bx-context-toolbar-edit-icon",
						"ONCLICK" => $action,
						"DEFAULT" => ($APPLICATION->GetPublicShowMode() != 'configure'? true: false),
						"ID" => "bx-context-toolbar-edit-section"
					);
					$arButtons["edit"]["edit_section"] = $arButton;
					$arButtons["configure"]["edit_section"] = $arButton;

					$url = str_replace("&bxpublic=Y&from_module=iblock", "", $url);
					$arButton["ACTION"] = "javascript:jsUtils.Redirect([], '".CUtil::JSEscape($url)."')";
					unset($arButton["ONCLICK"]);
					$arButtons["submenu"]["edit_section"] = $arButton;
				}

				if(CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $SECTION_ID, "section_section_bind"))
				{
					$url = "/bitrix/admin/".CIBlock::GetAdminSectionEditLink($IBLOCK_ID, null, array(
						"force_catalog" => $bCatalog,
						"IBLOCK_SECTION_ID" => $SECTION_ID,
						"filter_section" => $SECTION_ID,
						"bxpublic" => "Y",
						"from_module" => "iblock",
						"return_url" => $return_url["add_section"],
					));

					$action = $APPLICATION->GetPopupLink(
						array(
							"URL" => $url,
							"PARAMS" => array(
								"width" => 700, 'height' => 400, 'resize' => false,
							),
						)
					);

					$arButton = array(
						"TEXT" => (strlen($arLabels["SECTION_ADD_TEXT"])? $arLabels["SECTION_ADD_TEXT"]: $arIBlock["SECTION_ADD"]),
						"TITLE" => (strlen($arLabels["SECTION_ADD_TITLE"])? $arLabels["SECTION_ADD_TITLE"]: $arIBlock["SECTION_ADD"]),
						"ACTION" => 'javascript:'.$action,
						"ACTION_URL" => $url,
						"ICON" => "bx-context-toolbar-create-icon",
						"ID" => "bx-context-toolbar-add-section",
						"ONCLICK" => $action
					);

					$arButtons["edit"]["add_section"] = $arButton;
					$arButtons["configure"]["add_section"] = $arButton;

					$url = str_replace("&bxpublic=Y&from_module=iblock", "", $url);
					$arButton["ACTION"] = "javascript:jsUtils.Redirect([], '".CUtil::JSEscape($url)."')";
					unset($arButton["ONCLICK"]);
					$arButtons["submenu"]["add_section"] = $arButton;
				}

				//Delete section
				if($SECTION_ID > 0 && CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $SECTION_ID, "section_delete"))
				{
					$url = CIBlock::GetAdminSectionListLink($IBLOCK_ID, Array('action'=>'delete'));
					if($bSessID)
						$url .= '&'.bitrix_sessid_get();
					$url .= '&ID[]='.(preg_match('/^iblock_list_admin\.php/', $url)? "S": "").$SECTION_ID."&return_url=".UrlEncode($return_url["delete_section"]);
					$url = "/bitrix/admin/".$url;

					$arButton = array(
						"TEXT" => (strlen($arLabels["SECTION_DELETE_TEXT"])? $arLabels["SECTION_DELETE_TEXT"]: $arIBlock["SECTION_DELETE"]),
						"TITLE" => (strlen($arLabels["SECTION_DELETE_TITLE"])? $arLabels["SECTION_DELETE_TITLE"]: $arIBlock["SECTION_DELETE"]),
						"ACTION" => "javascript:if(confirm('".GetMessage("IBLOCK_PANEL_SECTION_DEL_CONF")."'))jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
						"ACTION_URL" => $url,
						"ONCLICK" => "if(confirm('".GetMessage("IBLOCK_PANEL_SECTION_DEL_CONF")."'))jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
						"ICON" => "bx-context-toolbar-delete-icon",
						"ID" => "bx-context-toolbar-delete-section"
					);
					$arButtons["edit"]["delete_section"] = $arButton;
					$arButtons["configure"]["delete_section"] = $arButton;
					$arButtons["submenu"]["delete_section"] = $arButton;
				}
			}
		}

		if( ($IBLOCK_ID > 0) && CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_admin_display") )
		{
			if(!empty($arButtons["submenu"]))
				$arButtons["submenu"][] = array("SEPARATOR" => "Y", "HREF" => "");

			if($SECTION_ID > 0)
				$url = "/bitrix/admin/".CIBlock::GetAdminElementListLink($IBLOCK_ID , array('find_section_section'=>$SECTION_ID));
			else
				$url = "/bitrix/admin/".CIBlock::GetAdminElementListLink($IBLOCK_ID , array('find_el_y'=>'Y'));

			$arButton = array(
				"TEXT" => (strlen($arLabels["ELEMENTS_NAME_TEXT"])? $arLabels["ELEMENTS_NAME_TEXT"]: $arIBlock["ELEMENTS_NAME"]),
				"TITLE" => (strlen($arLabels["ELEMENTS_NAME_TITLE"])? $arLabels["ELEMENTS_NAME_TITLE"]: $arIBlock["ELEMENTS_NAME"]),
				"ACTION" => "javascript:jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
				"ACTION_URL" => $url,
				"ONCLICK" => "jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
				"ID" => "bx-context-toolbar-elements-list"
			);
			$arButtons["submenu"]["element_list"] = $arButton;

			$arButtons["intranet"]["element_list"] = array(
				'TEXT' => $arButton["TEXT"],
				'TITLE' => $arButton["TITLE"],
				'ICON' => 'settings',
				'ONCLICK' => $arButton["ACTION"],
				'SORT' => 1010,
			);

			$url = "/bitrix/admin/".CIBlock::GetAdminSectionListLink($IBLOCK_ID, array('find_section_section'=>$SECTION_ID));
			$arButton = array(
				"TEXT" => (strlen($arLabels["SECTIONS_NAME_TEXT"])? $arLabels["SECTIONS_NAME_TEXT"]: $arIBlock["SECTIONS_NAME"]),
				"TITLE" => (strlen($arLabels["SECTIONS_NAME_TITLE"])? $arLabels["SECTIONS_NAME_TITLE"]: $arIBlock["SECTIONS_NAME"]),
				"ACTION" => "javascript:jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
				"ACTION_URL" => $url,
				"ONCLICK" => "jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
				"ID" => "bx-context-toolbar-sections-list"
			);
			$arButtons["submenu"]["section_list"] = $arButton;

			if(CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_edit"))
			{
				$url = "/bitrix/admin/iblock_edit.php?type=".$arIBlock["IBLOCK_TYPE_ID"]."&lang=".LANGUAGE_ID."&ID=".$IBLOCK_ID."&return_url=".UrlEncode($return_url["edit_iblock"]);
				$arButton = array(
					"TEXT" => GetMessage("IBLOCK_PANEL_EDIT_IBLOCK_BUTTON", array("#IBLOCK_NAME#"=>$arIBlock["NAME"])),
					"TITLE" => GetMessage("IBLOCK_PANEL_EDIT_IBLOCK_BUTTON", array("#IBLOCK_NAME#"=>$arIBlock["NAME"])),
					"ACTION" => "javascript:jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
					"ACTION_URL" => $url,
					"ONCLICK" => "jsUtils.Redirect([], '".CUtil::JSEscape($url)."')",
					"ID" => "bx-context-toolbar-edit-iblock"
				);
				$arButtons["submenu"]["edit_iblock"] = $arButton;
			}
		}

		return $arButtons;
	}

	function GetSite($iblock_id)
	{
		global $DB;
		$strSql = "SELECT L.*, BS.* FROM b_iblock_site BS, b_lang L WHERE L.LID=BS.SITE_ID AND BS.IBLOCK_ID=".IntVal($iblock_id);
		return $DB->Query($strSql);
	}

	///////////////////////////////////////////////////////////////////
	// Block by ID
	///////////////////////////////////////////////////////////////////
	function GetByID($ID)
	{
		return CIBlock::GetList(Array(), Array("ID"=>$ID));
	}

	function GetArrayByID($ID, $FIELD = "")
	{
		global $DB;
		$ID = intval($ID);

		if(CACHED_b_iblock === false)
		{
			$res = $DB->Query("
				SELECT b_iblock.*,".$DB->DateToCharFunction("TIMESTAMP_X")." TIMESTAMP_X
				from  b_iblock
				WHERE ID = ".$ID
			);
			$arResult = $res->Fetch();
			if($arResult)
			{
				$arMessages = CIBlock::GetMessages($ID);
				$arResult = array_merge($arResult, $arMessages);
				$arResult["FIELDS"] = CIBlock::GetFields($ID);
			}
		}
		else
		{
			global $CACHE_MANAGER;

			$bucket_size = intval(CACHED_b_iblock_bucket_size);
			if($bucket_size<=0) $bucket_size = 20;

			$bucket = intval($ID/$bucket_size);
			$cache_id = $bucket_size."iblock".$bucket;

			if($CACHE_MANAGER->Read(CACHED_b_iblock, $cache_id, "b_iblock"))
			{
				$arIBlocks = $CACHE_MANAGER->Get($cache_id);
			}
			else
			{
				$arIBlocks = array();
				$res = $DB->Query("
					SELECT b_iblock.*,".$DB->DateToCharFunction("TIMESTAMP_X")." TIMESTAMP_X
					from  b_iblock
					WHERE ID between ".($bucket*$bucket_size)." AND ".(($bucket+1)*$bucket_size-1)
				);
				while($arIBlock = $res->Fetch())
				{
					$arMessages = CIBlock::GetMessages($arIBlock["ID"]);
					$arIBlock = array_merge($arIBlock, $arMessages);
					$arIBlock["FIELDS"] = CIBlock::GetFields($arIBlock["ID"]);
					$arIBlocks[$arIBlock["ID"]] = $arIBlock;
				}

				$CACHE_MANAGER->Set($cache_id, $arIBlocks);
			}

			if(isset($arIBlocks[$ID]))
			{
				$arResult = $arIBlocks[$ID];

				if(!array_key_exists("ELEMENT_DELETE", $arResult))
				{
					$arMessages = CIBlock::GetMessages($ID);
					$arResult = array_merge($arResult, $arMessages);
					CIBlock::CleanCache($ID);
				}

				if(!array_key_exists("FIELDS", $arResult))
				{
					$arResult["FIELDS"] = CIBlock::GetFields($ID);
					CIBlock::CleanCache($ID);
				}
			}
			else
			{
				$arResult = false;
			}
		}

		if($FIELD)
			return $arResult[$FIELD];
		else
			return $arResult;
	}

	function CleanCache($ID)
	{
		$ID = intval($ID);
		if(CACHED_b_iblock !== false)
		{
			$bucket_size = intval(CACHED_b_iblock_bucket_size);
			if($bucket_size<=0) $bucket_size = 20;

			$bucket = intval($ID/$bucket_size);
			$cache_id = $bucket_size."iblock".$bucket;

			$GLOBALS["CACHE_MANAGER"]->Clean($cache_id, "b_iblock");
		}
	}

	///////////////////////////////////////////////////////////////////
	// New block
	///////////////////////////////////////////////////////////////////
	function Add($arFields)
	{
		global $DB;

		//Default Yes
		$arFields["ACTIVE"] = isset($arFields["ACTIVE"]) && $arFields["ACTIVE"] === "N"? "N": "Y";
		$arFields["WORKFLOW"] = isset($arFields["WORKFLOW"]) && $arFields["WORKFLOW"] === "N"? "N": "Y";
		$arFields["INDEX_ELEMENT"] = isset($arFields["INDEX_ELEMENT"]) && $arFields["INDEX_ELEMENT"] === "N"? "N": "Y";
		//Default No
		$arFields["BIZPROC"] = isset($arFields["BIZPROC"]) && $arFields["BIZPROC"] === "Y"? "Y": "N";
		$arFields["INDEX_SECTION"] = isset($arFields["INDEX_SECTION"]) && $arFields["INDEX_SECTION"] === "Y"? "Y": "N";

		if(!isset($arFields["SECTION_CHOOSER"]))
			$arFields["SECTION_CHOOSER"] = "L";
		elseif($arFields["SECTION_CHOOSER"] !== "D" && $arFields["SECTION_CHOOSER"] !== "P")
			$arFields["SECTION_CHOOSER"] = "L";

		if(!isset($arFields["DESCRIPTION_TYPE"]) || $arFields["DESCRIPTION_TYPE"] !== "html")
			$arFields["DESCRIPTION_TYPE"] = "text";

		$arFields["VERSION"] = isset($arFields["VERSION"]) && intval($arFields["VERSION"]) === 2? "2": "1";

		if(isset($arFields["RIGHTS_MODE"]))
			$arFields["RIGHTS_MODE"] =  $arFields["RIGHTS_MODE"] === "E"? "E": "S";
		elseif(isset($arFields["RIGHTS"]))
			$arFields["RIGHTS_MODE"] = "E";
		else
			$arFields["RIGHTS_MODE"] = "S";

		if(array_key_exists("PICTURE", $arFields))
		{
			if(
				!is_array($arFields["PICTURE"])
				|| (
					strlen($arFields["PICTURE"]["name"]) <= 0
					&& strlen($arFields["PICTURE"]["del"]) <= 0
				)
			)
				unset($arFields["PICTURE"]);
			else
				$arFields["PICTURE"]["MODULE_ID"] = "iblock";
		}

		if(array_key_exists("SITE_ID", $arFields))
		{
			$arFields["LID"] = $arFields["SITE_ID"];
			unset($arFields["SITE_ID"]);
		}

		if(array_key_exists("EXTERNAL_ID", $arFields))
		{
			$arFields["XML_ID"] = $arFields["EXTERNAL_ID"];
			unset($arFields["EXTERNAL_ID"]);
		}

		if(array_key_exists("SECTION_PROPERTY", $arFields))
			$arFields["SECTION_PROPERTY"] = "Y";

		unset($arFields["ID"]);

		if(!$this->CheckFields($arFields))
		{
			$Result = false;
			$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
		}
		else
		{
			$arLID = array();
			if(array_key_exists("LID", $arFields))
			{
				if(is_array($arFields["LID"]))
				{
					foreach($arFields["LID"] as $site_id)
						$arLID[$site_id] = $DB->ForSQL($site_id);
				}
				else
				{
					$arLID[$arFields["LID"]] = $DB->ForSQL($arFields["LID"]);
				}
			}

			if(empty($arLID))
				unset($arFields["LID"]);
			else
				$arFields["LID"] = end($arLID);

			if(array_key_exists("PICTURE", $arFields))
			{
				$SAVED_PICTURE = $arFields["PICTURE"];
				CFile::SaveForDB($arFields, "PICTURE", "iblock");
			}

			$ID = $DB->Add("b_iblock", $arFields, array("DESCRIPTION"), "iblock");

			if(array_key_exists("PICTURE", $arFields))
			{
				$arFields["PICTURE"] = $SAVED_PICTURE;
			}

			$this->SetMessages($ID, $arFields);

			if(array_key_exists("FIELDS", $arFields) && is_array($arFields["FIELDS"]))
				$this->SetFields($ID, $arFields["FIELDS"]);

			if($arFields["RIGHTS_MODE"] === "E")
			{
				if(
					!array_key_exists("RIGHTS", $arFields)
					&& array_key_exists("GROUP_ID", $arFields)
					&& is_array($arFields["GROUP_ID"])
				)
				{
					$obIBlockRights = new CIBlockRights($ID);
					$obIBlockRights->SetRights($obIBlockRights->ConvertGroups($arFields["GROUP_ID"]));
				}
				elseif(
					array_key_exists("RIGHTS", $arFields)
					&& is_array($arFields["RIGHTS"])
				)
				{
					$obIBlockRights = new CIBlockRights($ID);
					$obIBlockRights->SetRights($arFields["RIGHTS"]);
				}
			}
			else
			{
				if(array_key_exists("GROUP_ID", $arFields) && is_array($arFields["GROUP_ID"]))
					$this->SetPermission($ID, $arFields["GROUP_ID"]);
			}

			if(!empty($arLID))
			{
				$DB->Query("
					DELETE FROM b_iblock_site WHERE IBLOCK_ID = ".$ID."
				", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

				$DB->Query("
					INSERT INTO b_iblock_site(IBLOCK_ID, SITE_ID)
					SELECT ".$ID.", LID
					FROM b_lang
					WHERE LID IN ('".implode("', '", $arLID)."')
				", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			}

			if($arFields["VERSION"] == 2)
			{
				if($this->_Add($ID))
				{
					$Result = $ID;
					$arFields["ID"] = &$ID;
				}
				else
				{
					$this->LAST_ERROR = GetMessage("IBLOCK_TABLE_CREATION_ERROR");
					$Result = false;
					$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
				}
			}
			else
			{
				$Result = $ID;
				$arFields["ID"] = &$ID;
			}

			$_SESSION["SESS_RECOUNT_DB"] = "Y";
			$this->CleanCache($ID);
		}

		$arFields["RESULT"] = &$Result;

		foreach(GetModuleEvents("iblock", "OnAfterIBlockAdd", true)  as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$arFields));

		if(defined("BX_COMP_MANAGED_CACHE"))
			$GLOBALS["CACHE_MANAGER"]->ClearByTag("iblock_id_new");

		return $Result;
	}

	///////////////////////////////////////////////////////////////////
	// Update
	///////////////////////////////////////////////////////////////////
	function Update($ID, $arFields)
	{
		global $DB;
		$ID = IntVal($ID);

		if(is_set($arFields, "EXTERNAL_ID"))
			$arFields["XML_ID"] = $arFields["EXTERNAL_ID"];

		if(is_set($arFields, "PICTURE"))
		{
			if(strlen($arFields["PICTURE"]["name"])<=0 && strlen($arFields["PICTURE"]["del"])<=0)
			{
				unset($arFields["PICTURE"]);
			}
			else
			{
				$pic_res = $DB->Query("SELECT PICTURE FROM b_iblock WHERE ID=".$ID);
				if($pic_res = $pic_res->Fetch())
					$arFields["PICTURE"]["old_file"]=$pic_res["PICTURE"];
				$arFields["PICTURE"]["MODULE_ID"] = "iblock";
			}
		}

		if(is_set($arFields, "ACTIVE") && $arFields["ACTIVE"]!="Y")
			$arFields["ACTIVE"]="N";

		if(is_set($arFields, "WORKFLOW") && $arFields["WORKFLOW"]!="N")
			$arFields["WORKFLOW"]="Y";

		if(is_set($arFields, "BIZPROC") && $arFields["BIZPROC"]!="Y")
			$arFields["BIZPROC"]="N";

		if(is_set($arFields, "SECTION_CHOOSER") && $arFields["SECTION_CHOOSER"]!="D" && $arFields["SECTION_CHOOSER"]!="P")
			$arFields["SECTION_CHOOSER"]="L";

		if(is_set($arFields, "INDEX_SECTION") && $arFields["INDEX_SECTION"]!="Y")
			$arFields["INDEX_SECTION"]="N";

		if(is_set($arFields, "INDEX_ELEMENT") && $arFields["INDEX_ELEMENT"]!="Y")
			$arFields["INDEX_ELEMENT"]="N";

		if(is_set($arFields, "DESCRIPTION_TYPE") && $arFields["DESCRIPTION_TYPE"]!="html")
			$arFields["DESCRIPTION_TYPE"] = "text";

		if(is_set($arFields, "SITE_ID"))
			$arFields["LID"] = $arFields["SITE_ID"];

		if(is_set($arFields, "SECTION_PROPERTY"))
			$arFields["SECTION_PROPERTY"] = "Y";

		$RIGHTS_MODE = CIBlock::GetArrayByID($ID, "RIGHTS_MODE");

		if(!$this->CheckFields($arFields, $ID))
		{
			$Result = false;
			$arFields["RESULT_MESSAGE"] = &$this->LAST_ERROR;
		}
		else
		{
			$arLID = Array();
			if(is_set($arFields, "LID"))
			{
				if(is_array($arFields["LID"]))
					$arLID = $arFields["LID"];
				else
					$arLID[] = $arFields["LID"];

				$arFields["LID"] = false;
				$str_LID = "''";
				foreach($arLID as $v)
				{
					$arFields["LID"] = $v;
					$str_LID .= ", '".$DB->ForSql($v)."'";
				}
			}

			unset($arFields["ID"]);
			unset($arFields["VERSION"]);

			if(array_key_exists("PICTURE", $arFields))
			{
				$SAVED_PICTURE = $arFields["PICTURE"];
				CFile::SaveForDB($arFields, "PICTURE", "iblock");
			}

			$strUpdate = $DB->PrepareUpdate("b_iblock", $arFields, "iblock");

			if(array_key_exists("PICTURE", $arFields))
				$arFields["PICTURE"] = $SAVED_PICTURE;

			$arBinds=Array();
			if(is_set($arFields, "DESCRIPTION"))
				$arBinds["DESCRIPTION"] = $arFields["DESCRIPTION"];

			if(strlen($strUpdate) > 0)
			{
				$strSql = "UPDATE b_iblock SET ".$strUpdate." WHERE ID=".$ID;
				$DB->QueryBind($strSql, $arBinds);
			}

			$this->SetMessages($ID, $arFields);
			if(isset($arFields["FIELDS"]) && is_array($arFields["FIELDS"]))
				$this->SetFields($ID, $arFields["FIELDS"]);

			if(array_key_exists("RIGHTS_MODE", $arFields))
			{
				if($arFields["RIGHTS_MODE"] === "E" && $RIGHTS_MODE !== "E")
				{
					CIBlock::SetPermission($ID, array());
				}
				elseif($arFields["RIGHTS_MODE"] !== "E" && $RIGHTS_MODE === "E")
				{
					$obIBlockRights = new CIBlockRights($ID);
					$obIBlockRights->DeleteAllRights();
				}

				if($arFields["RIGHTS_MODE"] === "E")
					$RIGHTS_MODE = "E";
			}

			if($RIGHTS_MODE === "E")
			{
				if(
					!array_key_exists("RIGHTS", $arFields)
					&& array_key_exists("GROUP_ID", $arFields)
					&& is_array($arFields["GROUP_ID"])
				)
				{
					$obIBlockRights = new CIBlockRights($ID);
					$obIBlockRights->SetRights($obIBlockRights->ConvertGroups($arFields["GROUP_ID"]));
				}
				elseif(
					array_key_exists("RIGHTS", $arFields)
					&& is_array($arFields["RIGHTS"])
				)
				{
					$obIBlockRights = new CIBlockRights($ID);
					$obIBlockRights->SetRights($arFields["RIGHTS"]);
				}
			}
			else
			{
				if(array_key_exists("GROUP_ID", $arFields) && is_array($arFields["GROUP_ID"]))
					CIBlock::SetPermission($ID, $arFields["GROUP_ID"]);
			}

			if(count($arLID)>0)
			{
				$strSql = "DELETE FROM b_iblock_site WHERE IBLOCK_ID=".$ID;
				$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

				$strSql =
					"INSERT INTO b_iblock_site(IBLOCK_ID, SITE_ID) ".
					"SELECT ".$ID.", LID ".
					"FROM b_lang ".
					"WHERE LID IN (".$str_LID.") ";
				$DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
			}

			if(CModule::IncludeModule("search"))
			{
				$dbafter = $DB->Query("SELECT ACTIVE FROM b_iblock WHERE ID=".$ID);
				$arAfter = $dbafter->Fetch();
				if($arAfter["ACTIVE"] != "Y")
					CSearch::DeleteIndex("iblock", false, false, $ID);
			}

			$_SESSION["SESS_RECOUNT_DB"] = "Y";
			$Result = true;
		}

		$this->CleanCache($ID);

		$arFields["ID"] = $ID;
		$arFields["RESULT"] = &$Result;

		$events = GetModuleEvents("iblock", "OnAfterIBlockUpdate");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array(&$arFields));

		if(defined("BX_COMP_MANAGED_CACHE"))
			$GLOBALS["CACHE_MANAGER"]->ClearByTag("iblock_id_".$ID);

		return $Result;
	}

	///////////////////////////////////////////////////////////////////
	// Function deletes iblock by ID
	///////////////////////////////////////////////////////////////////
	function Delete($ID)
	{
		$err_mess = "FILE: ".__FILE__."<br>LINE: ";
		global $DB, $APPLICATION, $USER_FIELD_MANAGER, $CACHE_MANAGER;

		$ID = IntVal($ID);

		$APPLICATION->ResetException();
		$db_events = GetModuleEvents("iblock", "OnBeforeIBlockDelete");
		while($arEvent = $db_events->Fetch())
		{
			if(ExecuteModuleEventEx($arEvent, array($ID)) === false)
			{
				$err = GetMessage("MAIN_BEFORE_DEL_ERR").' '.$arEvent['TO_NAME'];
				$ex = $APPLICATION->GetException();
				if(is_object($ex))
					$err .= ': '.$ex->GetString();
				$APPLICATION->throwException($err);
				return false;
			}
		}

		$events = GetModuleEvents("iblock", "OnIBlockDelete");
		while($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array($ID));

		$iblocksections = CIBlockSection::GetList(Array(), Array(
			"IBLOCK_ID" => $ID,
			"DEPTH_LEVEL" => 1,
			"CHECK_PERMISSIONS" => "N",
		), false, Array("ID"));
		while($iblocksection = $iblocksections->Fetch())
		{
			if(!CIBlockSection::Delete($iblocksection["ID"], false))
				return false;
		}

		$iblockelements = CIBlockElement::GetList(Array(), Array(
			"IBLOCK_ID" => $ID,
			"SHOW_NEW" => "Y",
			"CHECK_PERMISSIONS" => "N",
		), false, false, array("IBLOCK_ID", "ID"));
		while($iblockelement = $iblockelements->Fetch())
		{
			if(!CIBlockElement::Delete($iblockelement["ID"]))
				return false;
		}

		$props = CIBlockProperty::GetList(array(), array(
				"IBLOCK_ID" => $ID,
				"CHECK_PERMISSIONS" =>"N",
		));
		while($property = $props->Fetch())
		{
			if(!CIBlockProperty::Delete($property["ID"]))
				return false;
		}

		$seq = new CIBlockSequence($ID);
		$seq->Drop(true);

		$obIBlockRights = new CIBlockRights($ID);
		$obIBlockRights->DeleteAllRights();

		CIBlockSectionPropertyLink::DeleteByIBlock($ID);

		$DB->Query("delete from b_iblock_offers_tmp where PRODUCT_IBLOCK_ID=".$ID, false, $err_mess.__LINE__);
		$DB->Query("delete from b_iblock_offers_tmp where OFFERS_IBLOCK_ID=".$ID, false, $err_mess.__LINE__);

		if(!$DB->Query("DELETE FROM b_iblock_messages WHERE IBLOCK_ID = ".$ID, false, $err_mess.__LINE__))
			return false;

		if(!$DB->Query("DELETE FROM b_iblock_fields WHERE IBLOCK_ID = ".$ID, false, $err_mess.__LINE__))
			return false;

		$USER_FIELD_MANAGER->OnEntityDelete("IBLOCK_".$ID."_SECTION");

		if(!$DB->Query("DELETE FROM b_iblock_group WHERE IBLOCK_ID=".$ID, false, $err_mess.__LINE__))
			return false;
		if(!$DB->Query("DELETE FROM b_iblock_rss WHERE IBLOCK_ID=".$ID, false, $err_mess.__LINE__))
			return false;
		if(!$DB->Query("DELETE FROM b_iblock_site WHERE IBLOCK_ID=".$ID, false, $err_mess.__LINE__))
			return false;
		if(!$DB->Query("DELETE FROM b_iblock WHERE ID=".$ID, false, $err_mess.__LINE__))
			return false;

		$DB->Query("DROP TABLE b_iblock_element_prop_s".$ID, true, $err_mess.__LINE__);
		$DB->Query("DROP TABLE b_iblock_element_prop_m".$ID, true, $err_mess.__LINE__);
		$DB->Query("DROP SEQUENCE sq_b_iblock_element_prop_m".$ID, true, $err_mess.__LINE__);

		CIBlock::CleanCache($ID);

		if(defined("BX_COMP_MANAGED_CACHE"))
			$CACHE_MANAGER->ClearByTag("iblock_id_".$ID);

		$_SESSION["SESS_RECOUNT_DB"] = "Y";
		return true;
	}

	///////////////////////////////////////////////////////////////////
	// Check function called from Add and Update
	///////////////////////////////////////////////////////////////////
	function CheckFields(&$arFields, $ID=false)
	{
		global $APPLICATION, $DB, $USER;
		$this->LAST_ERROR = "";

		$NAME = isset($arFields["NAME"])? $arFields["NAME"]: "";
		if(
			($ID===false || array_key_exists("NAME", $arFields))
			&& strlen($NAME) <= 0
		)
			$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_NAME")."<br>";

		if($ID===false && !is_set($arFields, "IBLOCK_TYPE_ID"))
			$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_BLOCK_TYPE")."<br>";

		if($ID===false)
		{
			//For new record take default values
			$WORKFLOW = array_key_exists("WORKFLOW", $arFields)? $arFields["WORKFLOW"]: "Y";
			$BIZPROC  = array_key_exists("BIZPROC",  $arFields)? $arFields["BIZPROC"]:  "N";
		}
		else
		{
			//For existing one read old values
			$arIBlock = CIBlock::GetArrayByID($ID);
			$WORKFLOW = array_key_exists("WORKFLOW", $arFields)? $arFields["WORKFLOW"]: $arIBlock["WORKFLOW"];
			$BIZPROC  = array_key_exists("BIZPROC",  $arFields)? $arFields["BIZPROC"]:  $arIBlock["BIZPROC"];
			if($BIZPROC != "Y") $BIZPROC = "N";//This is cache compatibility issue
		}

		if($WORKFLOW == "Y" && $BIZPROC == "Y")
			$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_WORKFLOW_AND_BIZPROC")."<br>";

		if(is_set($arFields, "IBLOCK_TYPE_ID"))
		{
			$r = CIBlockType::GetByID($arFields["IBLOCK_TYPE_ID"]);
			if(!$r->Fetch())
				$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_BLOCK_TYPE_ID")."<br>";
		}

		if(
			is_array($arFields["PICTURE"])
			&& array_key_exists("bucket", $arFields["PICTURE"])
			&& is_object($arFields["PICTURE"]["bucket"])
		)
		{
			//This is trusted image from xml import
		}
		elseif(
			isset($arFields["PICTURE"])
			&& is_array($arFields["PICTURE"])
			&& isset($arFields["PICTURE"]["name"])
		)
		{
			$error = CFile::CheckImageFile($arFields["PICTURE"]);
			if (strlen($error) > 0)
				$this->LAST_ERROR .= $error."<br>";
		}

		if(
			($ID===false && !is_set($arFields, "LID")) ||
			(is_set($arFields, "LID")
			&& (
				(is_array($arFields["LID"]) && count($arFields["LID"])<=0)
				||
				(!is_array($arFields["LID"]) && strlen($arFields["LID"])<=0)
				)
			)
		)
		{
			$this->LAST_ERROR .= GetMessage("IBLOCK_BAD_SITE_ID_NA")."<br>";
		}
		elseif(is_set($arFields, "LID"))
		{
			if(!is_array($arFields["LID"]))
				$arFields["LID"] = Array($arFields["LID"]);

			foreach($arFields["LID"] as $v)
			{
				$r = CSite::GetByID($v);
				if(!$r->Fetch())
					$this->LAST_ERROR .= "'".$v."' - ".GetMessage("IBLOCK_BAD_SITE_ID")."<br>";
			}
		}

		$APPLICATION->ResetException();
		if($ID===false)
			$db_events = GetModuleEvents("iblock", "OnBeforeIBlockAdd");
		else
		{
			$arFields["ID"] = $ID;
			$db_events = GetModuleEvents("iblock", "OnBeforeIBlockUpdate");
		}

		while($arEvent = $db_events->Fetch())
		{
			$bEventRes = ExecuteModuleEventEx($arEvent, array(&$arFields));
			if($bEventRes===false)
			{
				if($err = $APPLICATION->GetException())
					$this->LAST_ERROR .= $err->GetString()."<br>";
				else
				{
					$APPLICATION->ThrowException("Unknown error");
					$this->LAST_ERROR .= "Unknown error.<br>";
				}
				break;
			}
		}

		/****************************** QUOTA ******************************/
		if(empty($this->LAST_ERROR) && (COption::GetOptionInt("main", "disk_space") > 0))
		{
			$quota = new CDiskQuota();
			if(!$quota->checkDiskQuota($arFields))
				$this->LAST_ERROR = $quota->LAST_ERROR;
		}
		/****************************** QUOTA ******************************/

		if(strlen($this->LAST_ERROR)>0)
			return false;

		return true;
	}

	function SetPermission($IBLOCK_ID, $arGROUP_ID)
	{
		global $DB;
		$IBLOCK_ID = intval($IBLOCK_ID);
		static $letters = "RUWX";

		$arToDelete = array();
		$arToInsert = array();

		if(is_array($arGROUP_ID))
		{
			foreach($arGROUP_ID as $group_id => $perm)
			{
				$group_id = intval($group_id);
				if($group_id > 0 && strlen($perm) == 1 && strpos($letters, $perm) !== false)
				{
					$arToInsert[$group_id] = $perm;
				}
			}
		}

		$rs = $DB->Query("
			SELECT GROUP_ID, PERMISSION
			FROM b_iblock_group
			WHERE IBLOCK_ID = ".$IBLOCK_ID."
		", false, "File: ".__FILE__."<br>Line: ".__LINE__);
		while($ar = $rs->Fetch())
		{
			$group_id = intval($ar["GROUP_ID"]);

			if(isset($arToInsert[$group_id]) && $arToInsert[$group_id] === $ar["PERMISSION"])
			{
				unset($arToInsert[$group_id]); //This already in DB
			}
			else
			{
				$arToDelete[] = $group_id;
			}
		}

		if(!empty($arToDelete))
		{
			$DB->Query("
				DELETE FROM b_iblock_group
				WHERE IBLOCK_ID = ".$IBLOCK_ID."
				AND GROUP_ID in (".implode(", ", $arToDelete).")
			", false, "File: ".__FILE__."<br>Line: ".__LINE__); //And this should be deleted
		}

		if(!empty($arToInsert))
		{
			foreach($arToInsert as $group_id => $perm)
			{
				$DB->Query("
					INSERT INTO b_iblock_group(IBLOCK_ID, GROUP_ID, PERMISSION)
					SELECT ".$IBLOCK_ID.", ID, '".$perm."'
					FROM b_group
					WHERE ID = ".$group_id."
				");
			}
		}

		if(!empty($arToDelete) || !empty($arToInsert))
		{
			if(CModule::IncludeModule("search"))
			{
				$arGroups = CIBlock::GetGroupPermissions($IBLOCK_ID);
				if(array_key_exists(2, $arGroups))
					CSearch::ChangePermission("iblock", array(2), false, false, $IBLOCK_ID);
				else
					CSearch::ChangePermission("iblock", $arGroups, false, false, $IBLOCK_ID);
			}
		}
	}

	function SetMessages($ID, $arFields)
	{
		global $DB;
		$ID = intval($ID);
		if($ID > 0)
		{
			$arMessages = array(
				"ELEMENT_NAME",
				"ELEMENTS_NAME",
				"ELEMENT_ADD",
				"ELEMENT_EDIT",
				"ELEMENT_DELETE",
				"SECTION_NAME",
				"SECTIONS_NAME",
				"SECTION_ADD",
				"SECTION_EDIT",
				"SECTION_DELETE",
			);
			$arUpdate = array();
			foreach($arMessages as $MESSAGE_ID)
			{
				if(array_key_exists($MESSAGE_ID, $arFields))
					$arUpdate[] = $MESSAGE_ID;
			}
			if(count($arUpdate) > 0)
			{
				$res = $DB->Query("
					DELETE FROM b_iblock_messages
					WHERE IBLOCK_ID = ".$ID."
					AND MESSAGE_ID in ('".implode("', '", $arUpdate)."')
				");
				if($res)
				{
					foreach($arUpdate as $MESSAGE_ID)
					{
						$MESSAGE_TEXT = trim($arFields[$MESSAGE_ID]);
						if(strlen($MESSAGE_TEXT) > 0)
							$DB->Add("b_iblock_messages", array(
								"ID" => 1, //FAKE field for not use sequence
								"IBLOCK_ID" => $ID,
								"MESSAGE_ID" => $MESSAGE_ID,
								"MESSAGE_TEXT" => $MESSAGE_TEXT,
							));
					}
				}
			}
		}
	}

	function GetMessages($ID, $type="")
	{
		global $DB;
		$ID = intval($ID);
		$arMessages = array(
			"ELEMENT_NAME" => GetMessage("IBLOCK_MESS_ELEMENT_NAME"),
			"ELEMENTS_NAME" => "",
			"ELEMENT_ADD" => GetMessage("IBLOCK_MESS_ELEMENT_ADD"),
			"ELEMENT_EDIT" => GetMessage("IBLOCK_MESS_ELEMENT_EDIT"),
			"ELEMENT_DELETE" => GetMessage("IBLOCK_MESS_ELEMENT_DELETE"),
			"SECTION_NAME" => GetMessage("IBLOCK_MESS_SECTION_NAME"),
			"SECTIONS_NAME" => "",
			"SECTION_ADD" => GetMessage("IBLOCK_MESS_SECTION_ADD"),
			"SECTION_EDIT" => GetMessage("IBLOCK_MESS_SECTION_EDIT"),
			"SECTION_DELETE" => GetMessage("IBLOCK_MESS_SECTION_DELETE"),
		);
		$res = $DB->Query("
			SELECT
				B.IBLOCK_TYPE_ID
				,M.IBLOCK_ID
				,M.MESSAGE_ID
				,M.MESSAGE_TEXT
			FROM
				b_iblock B
				LEFT JOIN b_iblock_messages M ON B.ID = M.IBLOCK_ID
			WHERE
				B.ID = ".$ID."
		");

		while($ar = $res->Fetch())
		{
			$type = $ar["IBLOCK_TYPE_ID"];
			if($ar["MESSAGE_ID"])
				$arMessages[$ar["MESSAGE_ID"]] = $ar["MESSAGE_TEXT"];
		}
		if((strlen($arMessages["ELEMENTS_NAME"]) <= 0) || (strlen($arMessages["SECTIONS_NAME"]) <= 0))
		{
			if($type)
			{
				$arType = CIBlockType::GetByIDLang($type, LANGUAGE_ID);
				if($arType)
				{
					if(strlen($arMessages["ELEMENTS_NAME"]) <= 0)
						$arMessages["ELEMENTS_NAME"] = $arType["ELEMENT_NAME"];
					if(strlen($arMessages["SECTIONS_NAME"]) <= 0)
						$arMessages["SECTIONS_NAME"] = $arType["SECTION_NAME"];
				}
			}
		}
		if(strlen($arMessages["ELEMENTS_NAME"]) <= 0)
			$arMessages["ELEMENTS_NAME"] = GetMessage("IBLOCK_MESS_ELEMENTS_NAME");
		if(strlen($arMessages["SECTIONS_NAME"]) <= 0)
			$arMessages["SECTIONS_NAME"] = GetMessage("IBLOCK_MESS_SECTIONS_NAME");
		return $arMessages;
	}

	function GetFieldsDefaults()
	{
/*************
REQ
+	IBLOCK_SECTION_ID 	int(11),
	ACTIVE 			char(1) 	not null 	default 'Y',
+	ACTIVE_FROM 		datetime,
+	ACTIVE_TO 		datetime,
	SORT 			int(11) 	not null 	default '500',
	NAME 			varchar(255)	not null,
+	PREVIEW_PICTURE 	int(18),
+	PREVIEW_TEXT 		text,
	PREVIEW_TEXT_TYPE	varchar(4) 	not null 	default 'text',
+	DETAIL_PICTURE 		int(18),
+	DETAIL_TEXT 		longtext,
	DETAIL_TEXT_TYPE 	varchar(4) 	not null 	default 'text',
+	XML_ID 			varchar(255),
+	CODE 			varchar(255),
+	TAGS 			varchar(255),
**************/
		$jpgQuality = intval(COption::GetOptionString('main', 'image_resize_quality', '95'));
		if($jpgQuality <= 0 || $jpgQuality > 100)
			$jpgQuality = 95;

		static $res = false;
		if(!$res)
		$res = array(
			"IBLOCK_SECTION" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_SECTIONS"),
				"IS_REQUIRED" => false,
			),
			"ACTIVE" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_ACTIVE"),
				"IS_REQUIRED" => "Y",
			),
			"ACTIVE_FROM" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_ACTIVE_PERIOD_FROM"),
				"IS_REQUIRED" => false,
			),
			"ACTIVE_TO" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_ACTIVE_PERIOD_TO"),
				"IS_REQUIRED" => false,
			),
			"SORT" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_SORT"),
				"IS_REQUIRED" => false,
			),
			"NAME" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_NAME"),
				"IS_REQUIRED" => "Y",
			),
			"PREVIEW_PICTURE" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_PREVIEW_PICTURE"),
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => serialize(array(
					"METHOD" => "resample",
					"COMPRESSION" => $jpgQuality,
				)),
			),
			"PREVIEW_TEXT_TYPE" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_PREVIEW_TEXT_TYPE"),
				"IS_REQUIRED" => "Y",
			),
			"PREVIEW_TEXT" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_PREVIEW_TEXT"),
				"IS_REQUIRED" => false,
			),
			"DETAIL_PICTURE" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_DETAIL_PICTURE"),
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => serialize(array(
					"METHOD" => "resample",
					"COMPRESSION" => $jpgQuality,
				)),
			),
			"DETAIL_TEXT_TYPE" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_DETAIL_TEXT_TYPE"),
				"IS_REQUIRED" => "Y",
			),
			"DETAIL_TEXT" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_DETAIL_TEXT"),
				"IS_REQUIRED" => false,
			),
			"XML_ID" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_XML_ID"),
				"IS_REQUIRED" => false,
			),
			"CODE" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_CODE"),
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => serialize(array(
					"UNIQUE" => "N",
					"TRANSLITERATION" => "N",
					"TRANS_LEN" => 100,
					"TRANS_CASE" => "L",
					"TRANS_SPACE" => "-",
					"TRANS_OTHER" => "-",
					"TRANS_EAT" => "Y",
					"USE_GOOGLE" => "N",
				)),
			),
			"TAGS" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_TAGS"),
				"IS_REQUIRED" => false,
			),

			"SECTION_NAME" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_NAME"),
				"IS_REQUIRED" => "Y",
			),
			"SECTION_PICTURE" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_PREVIEW_PICTURE"),
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => serialize(array(
					"METHOD" => "resample",
					"COMPRESSION" => $jpgQuality,
				)),
			),
			"SECTION_DESCRIPTION_TYPE" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_SECTION_DESCRIPTION_TYPE"),
				"IS_REQUIRED" => "Y",
			),
			"SECTION_DESCRIPTION" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_SECTION_DESCRIPTION"),
				"IS_REQUIRED" => false,
			),
			"SECTION_DETAIL_PICTURE" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_DETAIL_PICTURE"),
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => serialize(array(
					"METHOD" => "resample",
					"COMPRESSION" => $jpgQuality,
				)),
			),
			"SECTION_XML_ID" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_XML_ID"),
				"IS_REQUIRED" => false,
			),
			"SECTION_CODE" => array(
				"NAME" => GetMessage("IBLOCK_FIELD_CODE"),
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => serialize(array(
					"UNIQUE" => "N",
					"TRANSLITERATION" => "N",
					"TRANS_LEN" => 100,
					"TRANS_CASE" => "L",
					"TRANS_SPACE" => "-",
					"TRANS_OTHER" => "-",
					"TRANS_EAT" => "Y",
					"USE_GOOGLE" => "N",
				)),
			),
			"LOG_SECTION_ADD" => array(
				"NAME" => "LOG_SECTION_ADD",
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => false,
			),
			"LOG_SECTION_EDIT" => array(
				"NAME" => "LOG_SECTION_EDIT",
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => false,
			),
			"LOG_SECTION_DELETE" => array(
				"NAME" => "LOG_SECTION_DELETE",
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => false,
			),
			"LOG_ELEMENT_ADD" => array(
				"NAME" => "LOG_ELEMENT_ADD",
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => false,
			),
			"LOG_ELEMENT_EDIT" => array(
				"NAME" => "LOG_ELEMENT_EDIT",
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => false,
			),
			"LOG_ELEMENT_DELETE" => array(
				"NAME" => "LOG_ELEMENT_DELETE",
				"IS_REQUIRED" => false,
				"DEFAULT_VALUE" => false,
			),
		);
		return $res;
	}

	function SetFields($ID, $arFields)
	{
		global $DB;
		$ID = intval($ID);
		if($ID > 0)
		{
			$arDefFields = CIBlock::GetFieldsDefaults();
			$res = $DB->Query("
				SELECT * FROM b_iblock_fields
				WHERE IBLOCK_ID = ".$ID."
			");
			if(array_key_exists("PREVIEW_PICTURE", $arFields))
			{
				$arDef = &$arFields["PREVIEW_PICTURE"]["DEFAULT_VALUE"];
				if(is_array($arDef))
				{
					$arDef = serialize(array(
						"FROM_DETAIL" => $arDef["FROM_DETAIL"] === "Y"? "Y": "N",
						"SCALE" => $arDef["SCALE"] === "Y"? "Y": "N",
						"WIDTH" => intval($arDef["WIDTH"]) > 0? intval($arDef["WIDTH"]): "",
						"HEIGHT" => intval($arDef["HEIGHT"]) > 0? intval($arDef["HEIGHT"]): "",
						"IGNORE_ERRORS" => $arDef["IGNORE_ERRORS"] === "Y"? "Y": "N",
						"METHOD" => $arDef["METHOD"] === "resample"? "resample": "",
						"COMPRESSION" => intval($arDef["COMPRESSION"]) > 100? 100: (intval($arDef["COMPRESSION"]) > 0? intval($arDef["COMPRESSION"]): ""),
						"DELETE_WITH_DETAIL" => $arDef["DELETE_WITH_DETAIL"] === "Y"? "Y": "N",
						"UPDATE_WITH_DETAIL" => $arDef["UPDATE_WITH_DETAIL"] === "Y"? "Y": "N",
						"USE_WATERMARK_TEXT" => $arDef["USE_WATERMARK_TEXT"] === "Y"? "Y": "N",
						"WATERMARK_TEXT" => $arDef["WATERMARK_TEXT"],
						"WATERMARK_TEXT_FONT" => $arDef["WATERMARK_TEXT_FONT"],
						"WATERMARK_TEXT_COLOR" => $arDef["WATERMARK_TEXT_COLOR"],
						"WATERMARK_TEXT_SIZE" => intval($arDef["WATERMARK_TEXT_SIZE"]) > 0? intval($arDef["WATERMARK_TEXT_SIZE"]): "",
						"WATERMARK_TEXT_POSITION" => $arDef["WATERMARK_TEXT_POSITION"],
						"USE_WATERMARK_FILE" => $arDef["USE_WATERMARK_FILE"] === "Y"? "Y": "N",
						"WATERMARK_FILE" => $arDef["WATERMARK_FILE"],
						"WATERMARK_FILE_ALPHA" => intval($arDef["WATERMARK_FILE_ALPHA"]) > 0? intval($arDef["WATERMARK_FILE_ALPHA"]): "",
						"WATERMARK_FILE_POSITION" => $arDef["WATERMARK_FILE_POSITION"],
						"WATERMARK_FILE_ORDER" => $arDef["WATERMARK_FILE_ORDER"],
					));
				}
				else
				{
					$arDef = "";
				}
			}
			if(array_key_exists("DETAIL_PICTURE", $arFields))
			{
				$arDef = &$arFields["DETAIL_PICTURE"]["DEFAULT_VALUE"];
				if(is_array($arDef))
				{
					$arDef = serialize(array(
						"SCALE" => $arDef["SCALE"] === "Y"? "Y": "N",
						"WIDTH" => intval($arDef["WIDTH"]) > 0? intval($arDef["WIDTH"]): "",
						"HEIGHT" => intval($arDef["HEIGHT"]) > 0? intval($arDef["HEIGHT"]): "",
						"IGNORE_ERRORS" => $arDef["IGNORE_ERRORS"] === "Y"? "Y": "N",
						"METHOD" => $arDef["METHOD"] === "resample"? "resample": "",
						"COMPRESSION" => intval($arDef["COMPRESSION"]) > 100? 100: (intval($arDef["COMPRESSION"]) > 0? intval($arDef["COMPRESSION"]): ""),
						"USE_WATERMARK_TEXT" => $arDef["USE_WATERMARK_TEXT"] === "Y"? "Y": "N",
						"WATERMARK_TEXT" => $arDef["WATERMARK_TEXT"],
						"WATERMARK_TEXT_FONT" => $arDef["WATERMARK_TEXT_FONT"],
						"WATERMARK_TEXT_COLOR" => $arDef["WATERMARK_TEXT_COLOR"],
						"WATERMARK_TEXT_SIZE" => intval($arDef["WATERMARK_TEXT_SIZE"]) > 0? intval($arDef["WATERMARK_TEXT_SIZE"]): "",
						"WATERMARK_TEXT_POSITION" => $arDef["WATERMARK_TEXT_POSITION"],
						"USE_WATERMARK_FILE" => $arDef["USE_WATERMARK_FILE"] === "Y"? "Y": "N",
						"WATERMARK_FILE" => $arDef["WATERMARK_FILE"],
						"WATERMARK_FILE_ALPHA" => intval($arDef["WATERMARK_FILE_ALPHA"]) > 0? intval($arDef["WATERMARK_FILE_ALPHA"]): "",
						"WATERMARK_FILE_POSITION" => $arDef["WATERMARK_FILE_POSITION"],
						"WATERMARK_FILE_ORDER" => $arDef["WATERMARK_FILE_ORDER"],
					));
				}
				else
				{
					$arDef = "";
				}
			}
			if(array_key_exists("CODE", $arFields))
			{
				$arDef = &$arFields["CODE"]["DEFAULT_VALUE"];
				if(is_array($arDef))
				{
					$trans_len = intval($arDef["TRANS_LEN"]);
					if($trans_len > 255)
						$trans_len = 255;
					elseif($trans_len < 1)
						$trans_len = 100;

					$arDef = serialize(array(
						"UNIQUE" => $arDef["UNIQUE"] === "Y"? "Y": "N",
						"TRANSLITERATION" => $arDef["TRANSLITERATION"] === "Y"? "Y": "N",
						"TRANS_LEN" =>  $trans_len,
						"TRANS_CASE" => $arDef["TRANS_CASE"] == "U"? "U": ($arDef["TRANS_CASE"] == ""? "": "L"),
						"TRANS_SPACE" => substr($arDef["TRANS_SPACE"], 0, 1),
						"TRANS_OTHER" => substr($arDef["TRANS_OTHER"], 0, 1),
						"TRANS_EAT" => $arDef["TRANS_EAT"] === "N"? "N": "Y",
						"USE_GOOGLE" => $arDef["USE_GOOGLE"] === "Y"? "Y": "N",
					));
				}
				else
				{
					$arDef = "";
				}
			}
			if(array_key_exists("SECTION_PICTURE", $arFields))
			{
				$arDef = &$arFields["SECTION_PICTURE"]["DEFAULT_VALUE"];
				if(is_array($arDef))
				{
					$arDef = serialize(array(
						"FROM_DETAIL" => $arDef["FROM_DETAIL"] === "Y"? "Y": "N",
						"SCALE" => $arDef["SCALE"] === "Y"? "Y": "N",
						"WIDTH" => intval($arDef["WIDTH"]) > 0? intval($arDef["WIDTH"]): "",
						"HEIGHT" => intval($arDef["HEIGHT"]) > 0? intval($arDef["HEIGHT"]): "",
						"IGNORE_ERRORS" => $arDef["IGNORE_ERRORS"] === "Y"? "Y": "N",
						"METHOD" => $arDef["METHOD"] === "resample"? "resample": "",
						"COMPRESSION" => intval($arDef["COMPRESSION"]) > 100? 100: (intval($arDef["COMPRESSION"]) > 0? intval($arDef["COMPRESSION"]): ""),
						"DELETE_WITH_DETAIL" => $arDef["DELETE_WITH_DETAIL"] === "Y"? "Y": "N",
						"UPDATE_WITH_DETAIL" => $arDef["UPDATE_WITH_DETAIL"] === "Y"? "Y": "N",
						"USE_WATERMARK_TEXT" => $arDef["USE_WATERMARK_TEXT"] === "Y"? "Y": "N",
						"WATERMARK_TEXT" => $arDef["WATERMARK_TEXT"],
						"WATERMARK_TEXT_FONT" => $arDef["WATERMARK_TEXT_FONT"],
						"WATERMARK_TEXT_COLOR" => $arDef["WATERMARK_TEXT_COLOR"],
						"WATERMARK_TEXT_SIZE" => intval($arDef["WATERMARK_TEXT_SIZE"]) > 0? intval($arDef["WATERMARK_TEXT_SIZE"]): "",
						"WATERMARK_TEXT_POSITION" => $arDef["WATERMARK_TEXT_POSITION"],
						"USE_WATERMARK_FILE" => $arDef["USE_WATERMARK_FILE"] === "Y"? "Y": "N",
						"WATERMARK_FILE" => $arDef["WATERMARK_FILE"],
						"WATERMARK_FILE_ALPHA" => intval($arDef["WATERMARK_FILE_ALPHA"]) > 0? intval($arDef["WATERMARK_FILE_ALPHA"]): "",
						"WATERMARK_FILE_POSITION" => $arDef["WATERMARK_FILE_POSITION"],
						"WATERMARK_FILE_ORDER" => $arDef["WATERMARK_FILE_ORDER"],
					));
				}
				else
				{
					$arDef = "";
				}
			}
			if(array_key_exists("SECTION_DETAIL_PICTURE", $arFields))
			{
				$arDef = &$arFields["SECTION_DETAIL_PICTURE"]["DEFAULT_VALUE"];
				if(is_array($arDef))
				{
					$arDef = serialize(array(
						"SCALE" => $arDef["SCALE"] === "Y"? "Y": "N",
						"WIDTH" => intval($arDef["WIDTH"]) > 0? intval($arDef["WIDTH"]): "",
						"HEIGHT" => intval($arDef["HEIGHT"]) > 0? intval($arDef["HEIGHT"]): "",
						"IGNORE_ERRORS" => $arDef["IGNORE_ERRORS"] === "Y"? "Y": "N",
						"METHOD" => $arDef["METHOD"] === "resample"? "resample": "",
						"COMPRESSION" => intval($arDef["COMPRESSION"]) > 100? 100: (intval($arDef["COMPRESSION"]) > 0? intval($arDef["COMPRESSION"]): ""),
						"USE_WATERMARK_TEXT" => $arDef["USE_WATERMARK_TEXT"] === "Y"? "Y": "N",
						"WATERMARK_TEXT" => $arDef["WATERMARK_TEXT"],
						"WATERMARK_TEXT_FONT" => $arDef["WATERMARK_TEXT_FONT"],
						"WATERMARK_TEXT_COLOR" => $arDef["WATERMARK_TEXT_COLOR"],
						"WATERMARK_TEXT_SIZE" => intval($arDef["WATERMARK_TEXT_SIZE"]) > 0? intval($arDef["WATERMARK_TEXT_SIZE"]): "",
						"WATERMARK_TEXT_POSITION" => $arDef["WATERMARK_TEXT_POSITION"],
						"USE_WATERMARK_FILE" => $arDef["USE_WATERMARK_FILE"] === "Y"? "Y": "N",
						"WATERMARK_FILE" => $arDef["WATERMARK_FILE"],
						"WATERMARK_FILE_ALPHA" => intval($arDef["WATERMARK_FILE_ALPHA"]) > 0? intval($arDef["WATERMARK_FILE_ALPHA"]): "",
						"WATERMARK_FILE_POSITION" => $arDef["WATERMARK_FILE_POSITION"],
						"WATERMARK_FILE_ORDER" => $arDef["WATERMARK_FILE_ORDER"],
					));
				}
				else
				{
					$arDef = "";
				}
			}
			if(array_key_exists("SECTION_CODE", $arFields))
			{
				$arDef = &$arFields["SECTION_CODE"]["DEFAULT_VALUE"];
				if(is_array($arDef))
				{

					$trans_len = intval($arDef["TRANS_LEN"]);
					if($trans_len > 255)
						$trans_len = 255;
					elseif($trans_len < 1)
						$trans_len = 100;

					$arDef = serialize(array(
						"UNIQUE" => $arDef["UNIQUE"] === "Y"? "Y": "N",
						"TRANSLITERATION" => $arDef["TRANSLITERATION"] === "Y"? "Y": "N",
						"TRANS_LEN" => $trans_len,
						"TRANS_CASE" => $arDef["TRANS_CASE"] == "U"? "U": ($arDef["TRANS_CASE"] == ""? "": "L"),
						"TRANS_SPACE" => substr($arDef["TRANS_SPACE"], 0, 1),
						"TRANS_OTHER" => substr($arDef["TRANS_OTHER"], 0, 1),
						"TRANS_EAT" => $arDef["TRANS_EAT"] === "N"? "N": "Y",
						"USE_GOOGLE" => $arDef["USE_GOOGLE"] === "Y"? "Y": "N",
					));
				}
				else
				{
					$arDef = "";
				}
			}
			if(array_key_exists("SORT", $arFields))
			{
				$arFields["SORT"]["DEFAULT_VALUE"] = intval($arFields["SORT"]["DEFAULT_VALUE"]);
			}

			while($ar = $res->Fetch())
			{
				if(array_key_exists($ar["FIELD_ID"], $arFields) && array_key_exists($ar["FIELD_ID"], $arDefFields))
				{
					if($arDefFields[$ar["FIELD_ID"]]["IS_REQUIRED"] === false)
						$IS_REQUIRED = $arFields[$ar["FIELD_ID"]]["IS_REQUIRED"];
					else
						$IS_REQUIRED = $arDefFields[$ar["FIELD_ID"]]["IS_REQUIRED"];
					$IS_REQUIRED = ($IS_REQUIRED === "Y"? "Y": "N");
					if(
						$ar["IS_REQUIRED"] !== $IS_REQUIRED
						|| $ar["DEFAULT_VALUE"] !== $arFields[$ar["FIELD_ID"]]["DEFAULT_VALUE"]
					)
					{
						$arUpdate = array(
							"IS_REQUIRED" => $IS_REQUIRED,
							"DEFAULT_VALUE" => $arFields[$ar["FIELD_ID"]]["DEFAULT_VALUE"],
						);
					}
					else
					{
						$arUpdate = array(
						);
					}
					unset($arDefFields[$ar["FIELD_ID"]]);
				}
				elseif(array_key_exists($ar["FIELD_ID"], $arDefFields))
				{
					$IS_REQUIRED = $arDefFields[$ar["FIELD_ID"]]["IS_REQUIRED"];
					$IS_REQUIRED = ($IS_REQUIRED === "Y"? "Y": "N");
					if($ar["IS_REQUIRED"] !== $IS_REQUIRED)
					{
						$arUpdate = array(
							"IS_REQUIRED" => $IS_REQUIRED,
							"DEFAULT_VALUE" => "",
						);
					}
					else
					{
						$arUpdate = array(
						);
					}
					unset($arDefFields[$ar["FIELD_ID"]]);
				}
				else
				{
					$DB->Query("DELETE FROM b_iblock_fields WHERE IBLOCK_ID = ".$ID." AND FIELD_ID = '".$DB->ForSQL($ar["FIELD_ID"])."'");
					$arUpdate = array(
					);
				}

				$strUpdate = $DB->PrepareUpdate("b_iblock_fields", $arUpdate);
				if($strUpdate != "")
				{
					$strSql = "UPDATE b_iblock_fields SET ".$strUpdate." WHERE IBLOCK_ID = ".$ID." AND FIELD_ID = '".$ar["FIELD_ID"]."'";
					$arBinds = array(
						"DEFAULT_VALUE" => $arUpdate["DEFAULT_VALUE"],
					);
					$DB->QueryBind($strSql, $arBinds);
				}
			}
			foreach($arDefFields as $FIELD_ID => $arDefaults)
			{
				if(array_key_exists($FIELD_ID, $arFields))
				{
					if($arDefaults["IS_REQUIRED"] === false)
						$IS_REQUIRED = $arFields[$FIELD_ID]["IS_REQUIRED"];
					else
						$IS_REQUIRED = $arDefaults["IS_REQUIRED"];
					$DEFAULT_VALUE = $arFields[$FIELD_ID]["DEFAULT_VALUE"];
				}
				else
				{
					$IS_REQUIRED = $arDefaults["IS_REQUIRED"];
					$DEFAULT_VALUE = false;
				}
				$IS_REQUIRED = ($IS_REQUIRED === "Y"? "Y": "N");
				$arAdd = array(
					"ID" => 1,
					"IBLOCK_ID" => $ID,
					"FIELD_ID" => $FIELD_ID,
					"IS_REQUIRED" => $IS_REQUIRED,
					"DEFAULT_VALUE" => $DEFAULT_VALUE,
				);
				$DB->Add("b_iblock_fields", $arAdd, array("DEFAULT_VALUE"));
			}

			CIBlock::CleanCache($ID);
		}
	}

	function GetFields($ID)
	{
		global $DB;
		$ID = intval($ID);
		$arDefFields = CIBlock::GetFieldsDefaults();
		$res = $DB->Query("
			SELECT
				F.*
			FROM
				b_iblock B
				LEFT JOIN b_iblock_fields F ON B.ID = F.IBLOCK_ID
			WHERE
				B.ID = ".$ID."
		");
		while($ar = $res->Fetch())
		{
			if(array_key_exists($ar["FIELD_ID"], $arDefFields))
			{
				if($arDefFields[$ar["FIELD_ID"]]["IS_REQUIRED"] === false)
					$arDefFields[$ar["FIELD_ID"]]["IS_REQUIRED"] = $ar["IS_REQUIRED"] === "Y"? "Y": "N";
				$arDefFields[$ar["FIELD_ID"]]["DEFAULT_VALUE"] = $ar["DEFAULT_VALUE"];
			}
		}
		foreach($arDefFields as $FIELD_ID => $default)
		{
			if($default["IS_REQUIRED"] === false)
				$arDefFields[$FIELD_ID]["IS_REQUIRED"] = "N";

			if(
				$FIELD_ID == "DETAIL_PICTURE"
				|| $FIELD_ID == "PREVIEW_PICTURE"
				|| $FIELD_ID == "CODE"
				|| $FIELD_ID == "SECTION_PICTURE"
				|| $FIELD_ID == "SECTION_DETAIL_PICTURE"
				|| $FIELD_ID == "SECTION_CODE"
			)
			{
				$a = &$arDefFields[$FIELD_ID]["DEFAULT_VALUE"];

				$a = strlen($a)? unserialize($a): array();

				if(array_key_exists("TRANS_LEN", $a))
				{
					$trans_len = intval($a["TRANS_LEN"]);
					if($trans_len > 255)
						$trans_len = 255;
					elseif($trans_len < 1)
						$trans_len = 100;
					$a["TRANS_LEN"] = $trans_len;
				}
			}
		}
		return $arDefFields;
	}

	function GetProperties($ID, $arOrder=Array(), $arFilter=Array())
	{
		$props = new CIBlockProperty();
		$arFilter["IBLOCK_ID"] = $ID;
		return $props->GetList($arOrder, $arFilter);
	}

	function GetGroupPermissions($ID)
	{
		global $DB;
		$arRes = array();

		$dbres = $DB->Query("
			SELECT GROUP_ID, PERMISSION
			FROM b_iblock_group
			WHERE IBLOCK_ID = ".intval($ID)."
		");
		while($res = $dbres->Fetch())
			$arRes[$res["GROUP_ID"]] = $res["PERMISSION"];

		return $arRes;
	}

	function GetPermission($IBLOCK_ID, $FOR_USER_ID = false)
	{
		global $DB, $USER;
		static $CACHE = array();
		$USER_ID = is_object($USER)? intval($USER->GetID()): 0;

		if($FOR_USER_ID > 0 && $FOR_USER_ID != $USER_ID)
		{
			$arGroups = CUser::GetUserGroup($FOR_USER_ID);
			if(
				in_array(1, $arGroups)
				&& COption::GetOptionString("main", "controller_member", "N") != "Y"
				&& COption::GetOptionString("main", "~controller_limited_admin", "N") != "Y"
			)
				return "X";
			$USER_GROUPS = implode(",", $arGroups);
		}
		elseif(is_object($USER))
		{
			if($USER->IsAdmin())
				return "X";
			$USER_GROUPS = $USER->GetGroups();
		}
		else
		{
			$USER_GROUPS = "2";
		}

		$IBLOCK_ID = intval($IBLOCK_ID);
		$CACHE_KEY = $IBLOCK_ID."|".$USER_GROUPS;

		if(!array_key_exists($CACHE_KEY, $CACHE))
		{
			//Deny by default
			$CACHE[$CACHE_KEY] = "D";
			//Now check database
			$strSql = "
				SELECT MAX(IBG.PERMISSION) as P
				FROM b_iblock_group IBG
				WHERE IBG.IBLOCK_ID=".$IBLOCK_ID."
				AND IBG.GROUP_ID IN (".$USER_GROUPS.")
			";
			$res = $DB->Query($strSql);
			if($r = $res->Fetch())
			{
				if(strlen($r['P']) > 0)
				{
					//Overwrite default value
					$CACHE[$CACHE_KEY] = $r["P"];
				}
			}
		}

		return $CACHE[$CACHE_KEY];
	}

	function OnBeforeLangDelete($lang)
	{
		global $APPLICATION, $DB;
		$r = $DB->Query("
			SELECT IBLOCK_ID
			FROM b_iblock_site
			WHERE SITE_ID='".$DB->ForSQL($lang, 2)."'
			ORDER BY IBLOCK_ID
		");
		$arIBlocks = array();
		while($a = $r->Fetch())
			$arIBlocks[] = $a["IBLOCK_ID"];
		if(count($arIBlocks) > 0)
		{
			$APPLICATION->ThrowException(GetMessage("IBLOCK_SITE_LINKS_EXISTS", array("#ID_LIST#" => implode(", ", $arIBlocks))));
			return false;
		}
		else
		{
			return true;
		}
	}

	function OnLangDelete($lang)
	{
		global $DB;
		return true;
	}

	function OnGroupDelete($group_id)
	{
		global $DB;
		return $DB->Query("DELETE FROM b_iblock_group WHERE GROUP_ID=".IntVal($group_id), true);
	}

	function MkOperationFilter($key)
	{
		static $triple_char = array(
			"!><"=>"NB", //not between
		);
		static $double_char = array(
			"!="=>"NI", //not Identical
			"!%"=>"NS", //not substring
			"><"=>"B",  //between
			">="=>"GE", //greater or equal
			"<="=>"LE", //less or equal
		);
		static $single_char = array(
			"="=>"I", //Identical
			"%"=>"S", //substring
			"?"=>"?", //logical
			">"=>"G", //greater
			"<"=>"L", //less
			"!"=>"N", // not field LIKE val
		);
		if(array_key_exists($op = substr($key,0,3), $triple_char))
			return Array("FIELD"=>substr($key,3), "OPERATION"=>$triple_char[$op]);
		elseif(array_key_exists($op = substr($key,0,2), $double_char))
			return Array("FIELD"=>substr($key,2), "OPERATION"=>$double_char[$op]);
		elseif(array_key_exists($op = substr($key,0,1), $single_char))
			return Array("FIELD"=>substr($key,1), "OPERATION"=>$single_char[$op]);
		else
			return Array("FIELD"=>$key, "OPERATION"=>"E"); // field LIKE val
	}

	function FilterCreate($fname, $vals, $type, $cOperationType=false, $bSkipEmpty = true)
	{
		return CIBlock::FilterCreateEx($fname, $vals, $type, $bFullJoin, $cOperationType, $bSkipEmpty);
	}

	function ForLIKE($str)
	{
		global $DB;
		return str_replace("%", "\\%", str_replace("_", "\\_", $DB->ForSQL($str)));
	}

	function FilterCreateEx($fname, $vals, $type, &$bFullJoin, $cOperationType=false, $bSkipEmpty = true)
	{
		global $DB;

		if(!is_array($vals))
			$vals=Array($vals);

		if(count($vals)<1)
			return "";

		if(is_bool($cOperationType))
		{
			if($cOperationType===true)
				$cOperationType = "N";
			else
				$cOperationType = "E";
		}

		if($cOperationType=="E") // most req operation
			$strOperation = "=";
		elseif($cOperationType=="G")
			$strOperation = ">";
		elseif($cOperationType=="GE")
			$strOperation = ">=";
		elseif($cOperationType=="LE")
			$strOperation = "<=";
		elseif($cOperationType=="L")
			$strOperation = "<";
		elseif($cOperationType=='B')
			$strOperation = array('BETWEEN', 'AND');
		elseif($cOperationType=='NB')
			$strOperation = array('BETWEEN', 'AND');
		else
			$strOperation = "=";

		if($cOperationType=='B' || $cOperationType=='NB')
		{
			if(count($vals)==2 && !is_array($vals[0]))
				$vals = array($vals);
		}

		$bNegative = substr($cOperationType, 0, 1)=="N";
		$bFullJoin = false;
		$bWasLeftJoin = false;

		$arIn = Array(); //This will gather equality number conditions
		$bWasNull = false;
		$res = Array();
		foreach($vals as $val)
		{
			if(
				!$bSkipEmpty
				|| (is_array($strOperation) && is_array($val))
				|| (is_bool($val) && $val===false)
				|| strlen($val)>0
			)
			{
				switch ($type)
				{
				case "string_equal":
					if($cOperationType=="?")
					{
						if(strlen($val)>0)
							$res[] = GetFilterQuery($fname, $val, "N");
					}
					elseif($cOperationType=="S" || $cOperationType=="NS")
						$res[] = ($cOperationType=="NS"?" ".$fname." IS NULL OR NOT ":"")."(".CIBlock::_Upper($fname)." LIKE ".CIBlock::_Upper("'%".CIBlock::ForLIKE($val)."%'").")";
					elseif(($cOperationType=="B" || $cOperationType=="NB") && is_array($val) && count($val)==2)
						$res[] = ($cOperationType=="NB"?" ".$fname." IS NULL OR NOT ":"")."(".CIBlock::_Upper($fname)." ".$strOperation[0]." '".CIBlock::_Upper($DB->ForSql($val[0]))."' ".$strOperation[1]." '".CIBlock::_Upper($DB->ForSql($val[1]))."')";
					else
					{
						if(strlen($val)<=0)
							$res[] = ($cOperationType=="N"?"NOT":"")."(".$fname." IS NULL OR ".$DB->Length($fname)."<=0)";
						else
							$res[] = ($cOperationType=="N"?" ".$fname." IS NULL OR NOT ":"")."(".CIBlock::_Upper($fname).$strOperation.CIBlock::_Upper("'".$DB->ForSql($val)."'").")";
					}
					break;
				case "string":
					if($cOperationType=="?")
					{
						if(strlen($val)>0)
						{
							$sr = GetFilterQuery($fname, $val, "Y", array(), ($fname=="BE.SEARCHABLE_CONTENT" || $fname=="BE.DETAIL_TEXT" ? "Y" : "N"));
							if($sr != "0")
								$res[] = $sr;
						}
					}
					elseif(($cOperationType=="B" || $cOperationType=="NB") && is_array($val) && count($val)==2)
					{
						$res[] = ($cOperationType=="NB"?" ".$fname." IS NULL OR NOT ":"")."(".CIBlock::_Upper($fname)." ".$strOperation[0]." '".CIBlock::_Upper($DB->ForSql($val[0]))."' ".$strOperation[1]." '".CIBlock::_Upper($DB->ForSql($val[1]))."')";
					}
					elseif($cOperationType=="S" || $cOperationType=="NS")
						$res[] = ($cOperationType=="NS"?" ".$fname." IS NULL OR NOT ":"")."(".CIBlock::_Upper($fname)." LIKE ".CIBlock::_Upper("'%".CIBlock::ForLIKE($val)."%'").")";
					else
					{
						if(strlen($val)<=0)
							$res[] = ($bNegative? "NOT": "")."(".$fname." IS NULL OR ".$DB->Length($fname)."<=0)";
						else
							if($strOperation=="=" && $cOperationType!="I" && $cOperationType!="NI")
								$res[] = ($cOperationType=="N"?" ".$fname." IS NULL OR NOT ":"")."(".($DB->type=="ORACLE"?CIBlock::_Upper($fname)." LIKE ".CIBlock::_Upper("'".$DB->ForSqlLike($val)."'")." ESCAPE '\\'" : $fname." LIKE '".$DB->ForSqlLike($val)."'").")";
							else
								$res[] = ($bNegative? " ".$fname." IS NULL OR NOT ": "")."(".($DB->type=="ORACLE"?CIBlock::_Upper($fname)." ".$strOperation." ".CIBlock::_Upper("'".$DB->ForSql($val)."'")." " : $fname." ".$strOperation." '".$DB->ForSql($val)."'").")";
					}
					break;
				case "date":
					if(!is_array($val) && strlen($val)<=0)
						$res[] = ($cOperationType=="N"?"NOT":"")."(".$fname." IS NULL)";
					elseif(($cOperationType=="B" || $cOperationType=="NB") && is_array($val) && count($val)==2)
						$res[] = ($cOperationType=='NB'?' '.$fname.' IS NULL OR NOT ':'').'('.$fname.' '.$strOperation[0].' '.$DB->CharToDateFunction($DB->ForSql($val[0]), "FULL").' '.$strOperation[1].' '.$DB->CharToDateFunction($DB->ForSql($val[1]), "FULL").')';
					else
						$res[] = ($bNegative? " ".$fname." IS NULL OR NOT ": "")."(".$fname." ".$strOperation." ".$DB->CharToDateFunction($DB->ForSql($val), "FULL").")";
					break;
				case "number":
					if(!is_array($val) && strlen($val)<=0)
					{
						$res[] = $fname." IS ".($bNegative? "NOT NULL": " NULL");
						$bWasNull = true;
					}
					elseif($cOperationType=="B" || $cOperationType=="NB")
					{
						if(is_array($val))
						{
							if(count($val)==2)
								$res[] = ($cOperationType=='NB'?' '.$fname.' IS NULL OR NOT ':'').'('.$fname.' '.$strOperation[0].' \''.DoubleVal($val[0]).'\' '.$strOperation[1].' \''.DoubleVal($val[1]).'\')';
							else
								$res[] = ($cOperationType=='NB'?' '.$fname.' IS NULL OR NOT ':'').'('.$fname.' = \''.DoubleVal(array_pop($val[0])).'\')';
						}
						else
						{
							$res[] = ($cOperationType=='NB'?' '.$fname.' IS NULL OR NOT ':'').'('.$fname.' = \''.DoubleVal($val).'\')';
						}
					}
					elseif($bNegative)
					{
						$res[] = " ".$fname." IS NULL OR NOT (".$fname." ".$strOperation." '".DoubleVal($val)."')";
						if($strOperation == '=')
							$arIn[] = DoubleVal($val);
					}
					else
					{
						$res[] = "(".$fname." ".$strOperation." '".DoubleVal($val)."')";
						if($strOperation == '=')
							$arIn[] = DoubleVal($val);
					}
					break;
				case "number_above":
					if(strlen($val)<=0)
						$res[] = ($cOperationType=="N"?"NOT":"")."(".$fname." IS NULL)";
					else
						$res[] = ($cOperationType=="N"?" ".$fname." IS NULL OR NOT ":"")."(".$fname." ".$strOperation." '".$DB->ForSql($val)."')";
					break;
				}

				if((is_array($val) || strlen($val) > 0) && !$bNegative)
					$bFullJoin = true;
				else
					$bWasLeftJoin = true;
			}
		}

		$strResult = "";

		$cntIn = count($arIn);
		if(
			!$bWasNull
			&& $cntIn > 1
			&& (
				$cntIn < 2000
				|| $DB->type == "MYSQL"
			)
		)
		{
			if($bNegative)
				$res = array($fname." IS NULL OR NOT (".$fname." IN ('".implode("', '", $arIn)."'))");
			else
				$res = array($fname." IN ('".implode("', '", $arIn)."')");
		}

		foreach($res as $i=>$val)
		{
			if($i>0)
				$strResult .= ($bNegative? " AND ": " OR ");
			$strResult .= "(".$val.")";
		}

		if($strResult!="")
			$strResult = "(".$strResult.")";

		if($bFullJoin && $bWasLeftJoin && !$bNegative)
			$bFullJoin = false;

		return $strResult;
	}

	function _MergeIBArrays($iblock_id, $iblock_code = false, $iblock_id2 = false, $iblock_code2 = false)
	{
		if(!is_array($iblock_id))
		{
			if(is_numeric($iblock_id) || strlen($iblock_id) > 0)
				$iblock_id = Array($iblock_id);
			elseif(is_array($iblock_id2))
				$iblock_id = $iblock_id2;
			elseif(is_numeric($iblock_id2) || strlen($iblock_id2) > 0)
				$iblock_id = Array($iblock_id2);
		}

		if(!is_array($iblock_code))
		{
			if(is_numeric($iblock_code) || strlen($iblock_code) > 0)
				$iblock_code = Array($iblock_code);
			elseif(is_array($iblock_code2))
				$iblock_code = $iblock_code2;
			elseif(is_numeric($iblock_code2) || strlen($iblock_code2) > 0)
				$iblock_code = Array($iblock_code2);
		}

		if(is_array($iblock_code) && is_array($iblock_id))
			return array_merge($iblock_code, $iblock_id);

		if(is_array($iblock_code))
			return $iblock_code;

		if(is_array($iblock_id))
			return $iblock_id;

		return array();
	}

	function OnSearchGetURL($arFields)
	{
		global $DB;
		static $arIBlockCache = array();

		if($arFields["MODULE_ID"] !== "iblock" || substr($arFields["URL"], 0, 1) !== "=")
			return $arFields["URL"];

		$IBLOCK_ID = IntVal($arFields["PARAM2"]);

		if(!array_key_exists($IBLOCK_ID, $arIBlockCache))
		{
			$res = $DB->Query("
				SELECT
					DETAIL_PAGE_URL,
					SECTION_PAGE_URL,
					CODE as IBLOCK_CODE,
					XML_ID as IBLOCK_EXTERNAL_ID,
					IBLOCK_TYPE_ID
				FROM
					b_iblock
				WHERE ID = ".$IBLOCK_ID."
			");
			$arIBlockCache[$IBLOCK_ID] = $res->Fetch();
		}

		if(!is_array($arIBlockCache[$IBLOCK_ID]))
			return "";

		$arFields["URL"] = LTrim($arFields["URL"], " =");
		parse_str($arFields["URL"], $arr);
		$arr = $arIBlockCache[$IBLOCK_ID] + $arr;
		$arr["LANG_DIR"] = $arFields["DIR"];

		if(substr($arFields["ITEM_ID"], 0, 1) !== 'S')
			return CIBlock::ReplaceDetailUrl($arIBlockCache[$IBLOCK_ID]["DETAIL_PAGE_URL"], $arr, false, "E");
		else
			return CIBlock::ReplaceDetailUrl($arIBlockCache[$IBLOCK_ID]["SECTION_PAGE_URL"], $arr, false, "S");
	}

	function ReplaceSectionUrl($url, $arr, $server_name = false, $arrType = false)
	{
		$url = str_replace("#ID#", "#SECTION_ID#", $url);
		$url = str_replace("#CODE#", "#SECTION_CODE#", $url);
		return CIBlock::ReplaceDetailUrl($url, $arr, $server_name, $arrType);
	}

	function _GetProductUrl($OF_ELEMENT_ID, $OF_IBLOCK_ID, $server_name = false, $arrType = false)
	{
		static $arIBlockCache = array();
		static $arElementCache = array();

		$product_url = "";
		$OF_ELEMENT_ID = intval($OF_ELEMENT_ID);
		$OF_IBLOCK_ID = intval($OF_IBLOCK_ID);

		if(
			$arrType === "E"
			&& $OF_IBLOCK_ID > 0
			&& $OF_ELEMENT_ID > 0
			&& CModule::IncludeModule("catalog")
		)
		{
			if(!array_key_exists($OF_IBLOCK_ID, $arIBlockCache))
			{
				$rsProducts = CCatalog::GetList(
					array(),
					array('IBLOCK_ID' => $OF_IBLOCK_ID),
					false, false,
					array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID', 'SKU_PROPERTY_ID')
				);
				$arIBlockCache[$OF_IBLOCK_ID] = $rsProducts->Fetch();
				if(is_array($arIBlockCache[$OF_IBLOCK_ID]))
					$arIBlockCache[$OF_IBLOCK_ID]["PRODUCT_IBLOCK"] = CIBlock::GetArrayByID($arIBlockCache[$OF_IBLOCK_ID]["PRODUCT_IBLOCK_ID"]);
			}

			if(is_array($arIBlockCache[$OF_IBLOCK_ID]))
			{
				if(!array_key_exists($OF_ELEMENT_ID, $arElementCache))
				{
					$OF_PROP_ID = $arIBlockCache[$OF_IBLOCK_ID]["SKU_PROPERTY_ID"];
					$rsOffer = CIBlockElement::GetList(
						array(),
						array(
							"IBLOCK_ID" => $arIBlockCache[$OF_IBLOCK_ID]["IBLOCK_ID"],
							"=ID" => $OF_ELEMENT_ID,
						),
						false, false,
						array(
							"LANG_DIR",
							"PROPERTY_".$OF_PROP_ID.".ID",
							"PROPERTY_".$OF_PROP_ID.".CODE",
							"PROPERTY_".$OF_PROP_ID.".XML_ID",
							"PROPERTY_".$OF_PROP_ID.".IBLOCK_ID",
							"PROPERTY_".$OF_PROP_ID.".IBLOCK_SECTION_ID",
						)
					);
					if($arOffer = $rsOffer->Fetch())
					{
						$arOffer["PROPERTY_".$OF_PROP_ID."_IBLOCK_SECTION_CODE"] = '';
						if (intval($arOffer["PROPERTY_".$OF_PROP_ID."_IBLOCK_SECTION_ID"]) > 0)
						{
							$rsSections = CIBlockSection::GetByID($arOffer["PROPERTY_".$OF_PROP_ID."_IBLOCK_SECTION_ID"]);
							if ($arSection = $rsSections->Fetch())
							{
								$arOffer["PROPERTY_".$OF_PROP_ID."_IBLOCK_SECTION_CODE"] = $arSection['CODE'];
							}
						}

						$arElementCache[$OF_ELEMENT_ID] = array(
							"LANG_DIR" => $arOffer["LANG_DIR"],
							"ID" => $arOffer["PROPERTY_".$OF_PROP_ID."_ID"],
							"ELEMENT_ID" => $arOffer["PROPERTY_".$OF_PROP_ID."_ID"],
							"CODE" => $arOffer["PROPERTY_".$OF_PROP_ID."_CODE"],
							"ELEMENT_CODE" => $arOffer["PROPERTY_".$OF_PROP_ID."_CODE"],
							"EXTERNAL_ID" => $arOffer["PROPERTY_".$OF_PROP_ID."_XML_ID"],
							"IBLOCK_TYPE_ID" => $arIBlockCache[$OF_IBLOCK_ID]["PRODUCT_IBLOCK"]["IBLOCK_TYPE_ID"],
							"IBLOCK_ID" => $arOffer["PROPERTY_".$OF_PROP_ID."_IBLOCK_ID"],
							"IBLOCK_CODE" => $arIBlockCache[$OF_IBLOCK_ID]["PRODUCT_IBLOCK"]["CODE"],
							"IBLOCK_EXTERNAL_ID" => $arIBlockCache[$OF_IBLOCK_ID]["PRODUCT_IBLOCK"]["XML_ID"],
							"IBLOCK_SECTION_ID" => $arOffer["PROPERTY_".$OF_PROP_ID."_IBLOCK_SECTION_ID"],
							"SECTION_CODE" => $arOffer["PROPERTY_".$OF_PROP_ID."_IBLOCK_SECTION_CODE"],
						);
					}
				}

				if(is_array($arElementCache[$OF_ELEMENT_ID]))
				{
					$product_url = CIBlock::ReplaceDetailUrl($arIBlockCache[$OF_IBLOCK_ID]["PRODUCT_IBLOCK"]["DETAIL_PAGE_URL"], $arElementCache[$OF_ELEMENT_ID], $server_name);
				}
			}
		}

		return $product_url;
	}

	function ReplaceDetailUrl($url, $arr, $server_name = false, $arrType = false)
	{
		global $DB;
		static $arSectionCache = array();

		if($server_name)
		{
			$url = str_replace("#LANG#", $arr["LANG_DIR"], $url);
			if((defined("ADMIN_SECTION") && ADMIN_SECTION===true) || !defined("BX_STARTED"))
			{
				static $lcache;
				if(!is_array($lcache))
					$lcache = array();
				if(!is_set($lcache, $arr["LID"]))
				{
					$db_lang = CLang::GetByID($arr["LID"]);
					$arLang = $db_lang->Fetch();
					$lcache[$arr["LID"]] = $arLang;
				}
				$arLang = $lcache[$arr["LID"]];
				$url = str_replace("#SITE_DIR#", $arLang["DIR"], $url);
				$url = str_replace("#SERVER_NAME#", $arLang["SERVER_NAME"], $url);
			}
			else
			{
				$url = str_replace("#SITE_DIR#", SITE_DIR, $url);
				$url = str_replace("#SERVER_NAME#", SITE_SERVER_NAME, $url);
			}
		}

		if(strpos($url, "#PRODUCT_URL#") !== false)
			$url = str_replace("#PRODUCT_URL#", CIBlock::_GetProductUrl($arr["ID"], $arr["IBLOCK_ID"], $server_name, $arrType), $url);

		static $arSearch = array(
			/*Theese come from GetNext*/
			"#SITE_DIR#",
			"#ID#",
			"#CODE#",
			"#EXTERNAL_ID#",
			"#IBLOCK_TYPE_ID#",
			"#IBLOCK_ID#",
			"#IBLOCK_CODE#",
			"#IBLOCK_EXTERNAL_ID#",
			/*And theese was born during components 2 development*/
			"#ELEMENT_ID#",
			"#ELEMENT_CODE#",
			"#SECTION_ID#",
			"#SECTION_CODE#",
		);
		$arReplace = array(
			$arr["LANG_DIR"],
			intval($arr["ID"]) > 0? intval($arr["ID"]): "",
			urlencode(isset($arr["~CODE"])? $arr["~CODE"]: $arr["CODE"]),
			urlencode(isset($arr["~EXTERNAL_ID"])? $arr["~EXTERNAL_ID"]: $arr["EXTERNAL_ID"]),
			urlencode(isset($arr["~IBLOCK_TYPE_ID"])? $arr["~IBLOCK_TYPE_ID"]: $arr["IBLOCK_TYPE_ID"]),
			intval($arr["IBLOCK_ID"]) > 0? intval($arr["IBLOCK_ID"]): "",
			urlencode(isset($arr["~IBLOCK_CODE"])? $arr["~IBLOCK_CODE"]: $arr["IBLOCK_CODE"]),
			urlencode(isset($arr["~IBLOCK_EXTERNAL_ID"])? $arr["~IBLOCK_EXTERNAL_ID"]: $arr["IBLOCK_EXTERNAL_ID"]),
		);

		if($arrType === "E")
		{
			$arReplace[] = intval($arr["ID"]) > 0? intval($arr["ID"]): "";
			$arReplace[] = urlencode(isset($arr["~CODE"])? $arr["~CODE"]: $arr["CODE"]);
			#Deal with symbol codes
			$SECTION_CODE = "";
			$SECTION_ID = intval($arr["IBLOCK_SECTION_ID"]);
			if(strpos($url, "#SECTION_CODE#") !== false && $SECTION_ID > 0)
			{
				if(!array_key_exists($SECTION_ID, $arSectionCache))
				{
					$res = $DB->Query("SELECT CODE FROM b_iblock_section WHERE ID = ".$SECTION_ID);
					$arSectionCache[$SECTION_ID] = $res->Fetch();
				}
				if(is_array($arSectionCache[$SECTION_ID]))
					$SECTION_CODE = $arSectionCache[$SECTION_ID]["CODE"];
			}
			$arReplace[] = $SECTION_ID > 0? $SECTION_ID: "";
			$arReplace[] = urlencode($SECTION_CODE);
		}
		elseif($arrType === "S")
		{
			$arReplace[] = "";
			$arReplace[] = "";
			$arReplace[] = intval($arr["ID"]) > 0? intval($arr["ID"]): "";
			$arReplace[] = urlencode(isset($arr["~CODE"])? $arr["~CODE"]: $arr["CODE"]);
		}
		else
		{
			$arReplace[] = intval($arr["ELEMENT_ID"]) > 0? intval($arr["ELEMENT_ID"]): "";
			$arReplace[] = urlencode(isset($arr["~ELEMENT_CODE"])? $arr["~ELEMENT_CODE"]: $arr["ELEMENT_CODE"]);
			$arReplace[] = intval($arr["IBLOCK_SECTION_ID"]) > 0? intval($arr["IBLOCK_SECTION_ID"]): "";
			$arReplace[] = urlencode(isset($arr["~SECTION_CODE"])? $arr["~SECTION_CODE"]: $arr["SECTION_CODE"]);
		}

		$url = str_replace($arSearch, $arReplace, $url);

		return preg_replace("'(?<!:)/+'s", "/", $url);
	}


	function OnSearchReindex($NS=Array(), $oCallback=NULL, $callback_method="")
	{
		global $DB;

		$strNSJoin1 = "";
		$strNSFilter1 = "";
		$strNSFilter2 = "";
		$strNSFilter3 = "";
		$arResult = Array();
		if($NS["MODULE"]=="iblock" && strlen($NS["ID"])>0)
		{
			$arrTmp = explode(".", $NS["ID"]);
			$strNSFilter1 = " AND B.ID>=".IntVal($arrTmp[0])." ";
			if(substr($arrTmp[1], 0, 1)!='S')
			{
				$strNSFilter2 = " AND BE.ID>".IntVal($arrTmp[1])." ";
			}
			else
			{
				$strNSFilter2 = false;
				$strNSFilter3 = " AND BS.ID>".IntVal(substr($arrTmp[1], 1))." ";
			}
		}
		if($NS["SITE_ID"]!="")
		{
			$strNSJoin1 .= " INNER JOIN b_iblock_site BS ON BS.IBLOCK_ID=B.ID ";
			$strNSFilter1 .= " AND BS.SITE_ID='".$DB->ForSQL($NS["SITE_ID"])."' ";
		}
		$strSql = "
			SELECT B.ID, B.IBLOCK_TYPE_ID, B.INDEX_ELEMENT, B.INDEX_SECTION, B.RIGHTS_MODE,
				B.IBLOCK_TYPE_ID, B.CODE as IBLOCK_CODE, B.XML_ID as IBLOCK_EXTERNAL_ID
			FROM b_iblock B
			".$strNSJoin1."
			WHERE B.ACTIVE = 'Y'
				AND (B.INDEX_ELEMENT='Y' OR B.INDEX_SECTION='Y')
				".$strNSFilter1."
			ORDER BY B.ID
		";

		$dbrIBlock = $DB->Query($strSql);
		while($arIBlock = $dbrIBlock->Fetch())
		{
			$IBLOCK_ID = $arIBlock["ID"];

			$arGroups = Array();

			$strSql =
				"SELECT GROUP_ID ".
				"FROM b_iblock_group ".
				"WHERE IBLOCK_ID= ".$IBLOCK_ID." ".
				"	AND PERMISSION>='R' ".
				"	AND GROUP_ID>1 ".
				"ORDER BY GROUP_ID";

			$dbrIBlockGroup = $DB->Query($strSql);
			while($arIBlockGroup = $dbrIBlockGroup->Fetch())
			{
				$arGroups[] = $arIBlockGroup["GROUP_ID"];
				if($arIBlockGroup["GROUP_ID"]==2) break;
			}

			$arSITE = Array();
			$strSql =
				"SELECT SITE_ID ".
				"FROM b_iblock_site ".
				"WHERE IBLOCK_ID= ".$IBLOCK_ID;

			$dbrIBlockSite = $DB->Query($strSql);
			while($arIBlockSite = $dbrIBlockSite->Fetch())
				$arSITE[] = $arIBlockSite["SITE_ID"];

			if($arIBlock["INDEX_ELEMENT"]=='Y' && ($strNSFilter2 !== false))
			{
				$strSql =
					"SELECT BE.ID, BE.NAME, BE.TAGS, ".
					"	".$DB->DateToCharFunction("BE.ACTIVE_FROM")." as DATE_FROM, ".
					"	".$DB->DateToCharFunction("BE.ACTIVE_TO")." as DATE_TO, ".
					"	".$DB->DateToCharFunction("BE.TIMESTAMP_X")." as LAST_MODIFIED, ".
					"	BE.PREVIEW_TEXT_TYPE, BE.PREVIEW_TEXT, ".
					"	BE.DETAIL_TEXT_TYPE, BE.DETAIL_TEXT, ".
					"	BE.XML_ID as EXTERNAL_ID, BE.CODE, ".
					"	BE.IBLOCK_SECTION_ID ".
					"FROM b_iblock_element BE ".
					"WHERE BE.IBLOCK_ID=".$IBLOCK_ID." ".
					"	AND BE.ACTIVE='Y' ".
					CIBlockElement::WF_GetSqlLimit("BE.", "N").
					$strNSFilter2.
					"ORDER BY BE.ID ";

				//For MySQL we have to solve client out of memory
				//problem by limiting the query
				if($DB->type=="MYSQL")
				{
					$limit = 1000;
					$strSql .= " LIMIT ".$limit;
				}
				else
				{
					$limit = false;
				}

				$dbrIBlockElement = $DB->Query($strSql);
				while($arIBlockElement = $dbrIBlockElement->Fetch())
				{
					$DETAIL_URL =
							"=ID=".$arIBlockElement["ID"].
							"&EXTERNAL_ID=".$arIBlockElement["EXTERNAL_ID"].
							"&CODE=".$arIBlockElement["CODE"].
							"&IBLOCK_SECTION_ID=".$arIBlockElement["IBLOCK_SECTION_ID"].
							"&IBLOCK_TYPE_ID=".$arIBlock["IBLOCK_TYPE_ID"].
							"&IBLOCK_ID=".$IBLOCK_ID.
							"&IBLOCK_CODE=".$arIBlock["IBLOCK_CODE"].
							"&IBLOCK_EXTERNAL_ID=".$arIBlock["IBLOCK_EXTERNAL_ID"];

					$BODY =
						($arIBlockElement["PREVIEW_TEXT_TYPE"]=="html" ?
							CSearch::KillTags($arIBlockElement["PREVIEW_TEXT"]) :
							$arIBlockElement["PREVIEW_TEXT"]
						)."\r\n".
						($arIBlockElement["DETAIL_TEXT_TYPE"]=="html" ?
							CSearch::KillTags($arIBlockElement["DETAIL_TEXT"]) :
							$arIBlockElement["DETAIL_TEXT"]
						);

					$dbrProperties = CIBlockElement::GetProperty($IBLOCK_ID, $arIBlockElement["ID"], "sort", "asc", array("ACTIVE"=>"Y", "SEARCHABLE"=>"Y"));
					while($arProperties = $dbrProperties->Fetch())
					{
						$BODY .= "\r\n";

						if(strlen($arProperties["USER_TYPE"]) > 0)
							$UserType = CIBlockProperty::GetUserType($arProperties["USER_TYPE"]);
						else
							$UserType = array();

						if(array_key_exists("GetSearchContent", $UserType))
						{
							$BODY .= CSearch::KillTags(
								call_user_func_array($UserType["GetSearchContent"],
									array(
										$arProperties['ID'],
										array("VALUE" => $arProperties["VALUE"]),
										array(),
									)
								)
							);
						}
						elseif(array_key_exists("GetPublicViewHTML", $UserType))
						{
							$BODY .= CSearch::KillTags(
								call_user_func_array($UserType["GetPublicViewHTML"],
									array(
										$arProperties['ID'],
										array("VALUE" => $arProperties["VALUE"]),
										array(),
									)
								)
							);
						}
						elseif($arProperties["PROPERTY_TYPE"]=='L')
						{
							$BODY .= $arProperties["VALUE_ENUM"];
						}
						elseif($arProperties["PROPERTY_TYPE"]=='F')
						{
							$arFile = CIBlockElement::__GetFileContent($arProperties["VALUE"]);
							if(is_array($arFile))
							{
								$BODY .= $arFile["CONTENT"];
								$arIBlockElement["TAGS"] .= ",".$arFile["PROPERTIES"][COption::GetOptionString("search", "page_tag_property")];
							}
						}
						else
						{
							$BODY .= $arProperties["VALUE"];
						}
					}

					if($arIBlock["RIGHTS_MODE"] !== "E")
						$arPermissions = $arGroups;
					else
					{
						$obElementRights = new CIBlockElementRights($IBLOCK_ID, $arIBlockElement["ID"]);
						$arPermissions = $obElementRights->GetGroups(array("element_read"));
					}

					$Result = array(
						"ID" => $arIBlockElement["ID"],
						"LAST_MODIFIED" => (strlen($arIBlockElement["DATE_FROM"])>0? $arIBlockElement["DATE_FROM"]: $arIBlockElement["LAST_MODIFIED"]),
						"TITLE" => $arIBlockElement["NAME"],
						"BODY" => $BODY,
						"TAGS" => $arIBlockElement["TAGS"],
						"SITE_ID" => $arSITE,
						"PARAM1" => $arIBlock["IBLOCK_TYPE_ID"],
						"PARAM2" => $IBLOCK_ID,
						"DATE_FROM" => (strlen($arIBlockElement["DATE_FROM"])>0? $arIBlockElement["DATE_FROM"] : false),
						"DATE_TO" => (strlen($arIBlockElement["DATE_TO"])>0? $arIBlockElement["DATE_TO"] : false),
						"SITE_ID" => $arSITE,
						"PERMISSIONS" => $arPermissions,
						"URL" => $DETAIL_URL
					);

					if($oCallback)
					{
						$res = call_user_func(array($oCallback, $callback_method), $Result);
						if(!$res)
							return $IBLOCK_ID.".".$arIBlockElement["ID"];
					}
					else
					{
						$arResult[] = $Result;
					}

					if($limit !== false)
					{
						$limit--;
						if($limit <= 0)
							return $IBLOCK_ID.".".$arIBlockElement["ID"];
					}
				}
			}

			if($arIBlock["INDEX_SECTION"]=='Y')
			{
				$strSql =
					"SELECT BS.ID, BS.NAME, ".
					"	".$DB->DateToCharFunction("BS.TIMESTAMP_X")." as LAST_MODIFIED, ".
					"	BS.DESCRIPTION_TYPE, BS.DESCRIPTION, BS.XML_ID as EXTERNAL_ID, BS.CODE, ".
					"	BS.IBLOCK_ID ".
					"FROM b_iblock_section BS ".
					"WHERE BS.IBLOCK_ID=".$IBLOCK_ID." ".
					"	AND BS.GLOBAL_ACTIVE='Y' ".
					$strNSFilter3.
					"ORDER BY BS.ID ";

				$dbrIBlockSection = $DB->Query($strSql);
				while($arIBlockSection = $dbrIBlockSection->Fetch())
				{
					$DETAIL_URL =
							"=ID=".$arIBlockSection["ID"].
							"&EXTERNAL_ID=".$arIBlockSection["EXTERNAL_ID"].
							"&CODE=".$arIBlockSection["CODE"].
							"&IBLOCK_TYPE_ID=".$arIBlock["IBLOCK_TYPE_ID"].
							"&IBLOCK_ID=".$arIBlockSection["IBLOCK_ID"].
							"&IBLOCK_CODE=".$arIBlock["IBLOCK_CODE"].
							"&IBLOCK_EXTERNAL_ID=".$arIBlock["IBLOCK_EXTERNAL_ID"];
					$BODY =
						($arIBlockSection["DESCRIPTION_TYPE"]=="html" ?
							CSearch::KillTags($arIBlockSection["DESCRIPTION"])
						:
							$arIBlockSection["DESCRIPTION"]
						);
					$BODY .= $GLOBALS["USER_FIELD_MANAGER"]->OnSearchIndex("IBLOCK_".$arIBlockSection["IBLOCK_ID"]."_SECTION", $arIBlockSection["ID"]);

					if($arIBlock["RIGHTS_MODE"] !== "E")
						$arPermissions = $arGroups;
					else
					{
						$obSectionRights = new CIBlockSectionRights($IBLOCK_ID, $arIBlockSection["ID"]);
						$arPermissions = $obSectionRights->GetGroups(array("section_read"));
					}

					$Result = Array(
						"ID" => "S".$arIBlockSection["ID"],
						"LAST_MODIFIED" => $arIBlockSection["LAST_MODIFIED"],
						"TITLE" => $arIBlockSection["NAME"],
						"BODY" => $BODY,
						"SITE_ID" => $arSITE,
						"PARAM1" => $arIBlock["IBLOCK_TYPE_ID"],
						"PARAM2" => $IBLOCK_ID,
						"SITE_ID" => $arSITE,
						"PERMISSIONS" => $arPermissions,
						"URL" => $DETAIL_URL,
						);

					if($oCallback)
					{
						$res = call_user_func(array($oCallback, $callback_method), $Result);
						if(!$res)
							return $IBLOCK_ID.".S".$arIBlockSection["ID"];
					}
					else
					{
						$arResult[] = $Result;
					}
				}
			}
			$strNSFilter2="";
			$strNSFilter3="";
		}

		if($oCallback)
			return false;

		return $arResult;
	}

	function GetElementCount($iblock_id)
	{
		global $DB;
		$res = $DB->Query("
			SELECT COUNT('x') as C
			FROM b_iblock_element BE
			WHERE BE.IBLOCK_ID=".intval($iblock_id)."
			AND (
				(BE.WF_STATUS_ID=1 AND BE.WF_PARENT_ELEMENT_ID IS NULL)
				OR BE.WF_NEW='Y'
			)
		");
		$ar = $res->Fetch();
		return intval($ar["C"]);
	}

	function ResizePicture($arFile, $arResize)
	{
		if(strlen($arFile["tmp_name"]) <= 0)
			return $arFile;

		if(array_key_exists("error", $arFile) && $arFile["error"] !== 0)
			return GetMessage("IBLOCK_BAD_FILE_ERROR");

		$file = $arFile["tmp_name"];

		if(!file_exists($file) && !is_file($file))
			return GetMessage("IBLOCK_BAD_FILE_NOT_FOUND");

		$width = intval($arResize["WIDTH"]);
		$height = intval($arResize["HEIGHT"]);

		if($width <= 0 && $height <= 0)
			return $arFile;

		$orig = CFile::GetImageSize($file);
		if(!is_array($orig))
			return GetMessage("IBLOCK_BAD_FILE_NOT_PICTURE");

		if(($width > 0 && $orig[0] > $width) || ($height > 0 && $orig[1] > $height))
		{
			if($arFile["COPY_FILE"] == "Y")
			{
				$new_file = CTempFile::GetFileName(basename($file));
				CheckDirPath($new_file);
				$arFile["copy"] = true;

				if(copy($file, $new_file))
					$file = $new_file;
				else
					return GetMessage("IBLOCK_BAD_FILE_NOT_FOUND");
			}

			$width_orig = $orig[0];
			$height_orig = $orig[1];

			if($width <= 0)
				$width = $width_orig;

			if($height <= 0)
				$height = $height_orig;

			$height_new = $height_orig;
			if($width_orig > $width)
				$height_new = ($width / $width_orig) * $height_orig;

			if($height_new > $height)
				$width = ($height / $height_orig) * $width_orig;
			else
				$height = $height_new;

			$image_type = $orig[2];
			if($image_type == IMAGETYPE_JPEG)
				$image = imagecreatefromjpeg($file);
			elseif($image_type == IMAGETYPE_GIF)
				$image = imagecreatefromgif($file);
			elseif($image_type == IMAGETYPE_PNG)
				$image = imagecreatefrompng($file);
			else
				return GetMessage("IBLOCK_BAD_FILE_UNSUPPORTED");

			$image_p = imagecreatetruecolor($width, $height);
			if($image_type == IMAGETYPE_JPEG)
			{
				if($arResize["METHOD"] === "resample")
					imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
				else
					imagecopyresized($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

				if($arResize["COMPRESSION"] > 0)
					imagejpeg($image_p, $file, $arResize["COMPRESSION"]);
				else
					imagejpeg($image_p, $file);
			}
			elseif($image_type == IMAGETYPE_GIF && function_exists("imagegif"))
			{
				imagetruecolortopalette($image_p, true, imagecolorstotal($image));
				imagepalettecopy($image_p, $image);

				//Save transparency for GIFs
				$transparentcolor = imagecolortransparent($image);
				if($transparentcolor >= 0 && $transparentcolor < imagecolorstotal($image))
				{
					$transparentcolor = imagecolortransparent($image_p, $transparentcolor);
					imagefilledrectangle($image_p, 0, 0, $width, $height, $transparentcolor);
				}

				if($arResize["METHOD"] === "resample")
					imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
				else
					imagecopyresized($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
				imagegif($image_p, $file);
			}
			else
			{
				//Save transparency for PNG
				$transparentcolor = imagecolorallocatealpha($image_p, 0, 0, 0, 127);
				imagefilledrectangle($image_p, 0, 0, $width, $height, $transparentcolor);
				$transparentcolor = imagecolortransparent($image_p, $transparentcolor);

				imagealphablending($image_p, false);
				if($arResize["METHOD"] === "resample")
					imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
				else
					imagecopyresized($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

				imagesavealpha($image_p, true);
				imagepng($image_p, $file);
			}

			imagedestroy($image);
			imagedestroy($image_p);

			$arFile["size"] = filesize($file);
			$arFile["tmp_name"] = $file;
			return $arFile;
		}
		else
		{
			return $arFile;
		}
	}

	function FilterPicture($filePath, $arFilter)
	{
		$io = CBXVirtualIo::GetInstance();
		if (!$io->FileExists($filePath))
			return false;

		$arFileSize = CFile::GetImageSize($filePath);
		if(!is_array($arFileSize))
			return false;

		switch ($arFileSize[2])
		{
		case IMAGETYPE_GIF:
			$picture = imagecreatefromgif($io->GetPhysicalName($filePath));
			$bHasAlpha = true;
			break;

		case IMAGETYPE_PNG:
			$picture = imagecreatefrompng($io->GetPhysicalName($filePath));
			$bHasAlpha = true;
			break;

		case IMAGETYPE_JPEG:
			$picture = imagecreatefromjpeg($io->GetPhysicalName($filePath));
			$bHasAlpha = false;
			break;

		default:
			$picture = false;
			$bHasAlpha = false;
			break;
		}
		if (!is_resource($picture))
			return false;

		$bNeedCreatePicture = CFile::ApplyImageFilter($picture, $arFilter, $bHasAlpha);
		if ($bNeedCreatePicture)
		{
			switch ($arFileSize[2])
			{
			case IMAGETYPE_GIF:
				imagegif($picture, $io->GetPhysicalName($filePath));
				break;

			case IMAGETYPE_PNG:
				imagealphablending($picture, false);
				imagesavealpha($picture, true);
				imagepng($picture, $io->GetPhysicalName($filePath));
				break;

			case IMAGETYPE_JPEG:
				$jpgQuality = intval(COption::GetOptionString('main', 'image_resize_quality', '95'));
				if ($jpgQuality <= 0 || $jpgQuality > 100)
					$jpgQuality = 95;

				imagejpeg($picture, $io->GetPhysicalName($filePath), $jpgQuality);
				break;
			}
		}
		imagedestroy($picture);
		return true;
	}

	function NumberFormat($num)
	{
		if (strlen($num) > 0)
		{
			$res = preg_replace("#\\.([0-9]*?)(0+)\$#", ".\\1", $num);
			return rtrim($res, ".");
		}
		else
		{
			return "";
		}
	}

	function _Order($by, $order, $default_order, $nullable = true)
	{
		static $arOrder = array(
			"nulls,asc"  => array(true,  "asc" ),
			"asc,nulls"  => array(false, "asc" ),
			"nulls,desc" => array(true,  "desc"),
			"desc,nulls" => array(false, "desc"),
			"asc"        => array(true,  "asc" ),
			"desc"       => array(false, "desc"),
		);
		$order = strtolower(trim($order));
		if(array_key_exists($order, $arOrder))
			$o = $arOrder[$order];
		elseif(array_key_exists($default_order, $arOrder))
			$o = $arOrder[$default_order];
		else
			$o = $arOrder["desc,nulls"];

		//There is no need to "reverse" nulls order when
		//column can not contain nulls
		if(!$nullable)
		{
			if($o[1] == "asc")
				$o[0] = true;
			else
				$o[0] = false;
		}

		return $o;
	}

	function GetAdminSectionEditLink($IBLOCK_ID, $SECTION_ID, $arParams = array(), $strAdd = "")
	{
		if (
			(defined("CATALOG_PRODUCT") || $arParams["force_catalog"])
			&& !array_key_exists("menu", $arParams)
		)
			$url = "cat_section_edit.php";
		else
			$url = "iblock_section_edit.php";

		$url.= "?IBLOCK_ID=".intval($IBLOCK_ID);
		$url.= "&type=".urlencode(CIBlock::GetArrayByID($IBLOCK_ID, "IBLOCK_TYPE_ID"));
		if($SECTION_ID !== null)
			$url.= "&ID=".intval($SECTION_ID);
		$url.= "&lang=".urlencode(LANGUAGE_ID);
		foreach ($arParams as $name => $value)
			if (isset($value))
				$url.= "&".urlencode($name)."=".urlencode($value);

		return $url.$strAdd;
	}

	function GetAdminElementEditLink($IBLOCK_ID, $ELEMENT_ID, $arParams = array(), $strAdd = "")
	{
		if (
			(defined("CATALOG_PRODUCT") || $arParams["force_catalog"])
			&& !array_key_exists("menu", $arParams)
		)
			$url = "cat_product_edit.php";
		else
			$url = "iblock_element_edit.php";

		$url.= "?IBLOCK_ID=".intval($IBLOCK_ID);
		$url.= "&type=".urlencode(CIBlock::GetArrayByID($IBLOCK_ID, "IBLOCK_TYPE_ID"));
		if($ELEMENT_ID !== null)
			$url.= "&ID=".intval($ELEMENT_ID);
		$url.= "&lang=".urlencode(LANGUAGE_ID);
		foreach ($arParams as $name => $value)
			if (isset($value))
				$url.= "&".urlencode($name)."=".urlencode($value);

		return $url.$strAdd;
	}

	function GetAdminElementListLink($IBLOCK_ID, $arParams = array(), $strAdd = "")
	{
		if (defined("CATALOG_PRODUCT") && !array_key_exists("menu", $arParams))
			$url = "cat_product_admin.php";
		elseif (CIBlock::GetAdminListMode($IBLOCK_ID) == 'C')
			$url = "iblock_list_admin.php";
		else
			$url = "iblock_element_admin.php";

		$url.= "?IBLOCK_ID=".intval($IBLOCK_ID);
		$url.= "&type=".urlencode(CIBlock::GetArrayByID($IBLOCK_ID, "IBLOCK_TYPE_ID"));
		$url.= "&lang=".urlencode(LANGUAGE_ID);
		foreach ($arParams as $name => $value)
			if (isset($value))
				$url.= "&".urlencode($name)."=".urlencode($value);

		return $url.$strAdd;
	}

	function GetAdminSectionListLink($IBLOCK_ID, $arParams = array(), $strAdd = "")
	{
		if (defined("CATALOG_PRODUCT") && !array_key_exists("menu", $arParams))
			$url = "cat_section_admin.php";
		elseif (CIBlock::GetAdminListMode($IBLOCK_ID) == 'C')
			$url = "iblock_list_admin.php";
		else
			$url = "iblock_section_admin.php";

		$url.= "?IBLOCK_ID=".intval($IBLOCK_ID);
		$url.= "&type=".urlencode(CIBlock::GetArrayByID($IBLOCK_ID, "IBLOCK_TYPE_ID"));
		$url.= "&lang=".urlencode(LANGUAGE_ID);
		foreach ($arParams as $name => $value)
			if (isset($value))
				$url.= "&".urlencode($name)."=".urlencode($value);

		return $url.$strAdd;
	}

	function GetAdminListMode($IBLOCK_ID)
	{
		$list_mode = CIBlock::GetArrayByID($IBLOCK_ID, "LIST_MODE");

		if($list_mode == 'S' || $list_mode == 'C')
			return $list_mode;
		elseif(COption::GetOptionString("iblock","combined_list_mode")=="Y")
			return 'C';
		else
			return 'S';
	}

	function CheckForIndexes($IBLOCK_ID)
	{
		global $DB;
		$arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);

		$ar = $arIBlock["FIELDS"]["CODE"]["DEFAULT_VALUE"];
		if (
			is_array($ar)
			&& $ar["UNIQUE"] == "Y"
			&& !$DB->IndexExists("b_iblock_element", array("IBLOCK_ID", "CODE"))
		)
			$DB->Query("create index ix_iblock_element_code on b_iblock_element (IBLOCK_ID, CODE)");

		$ar = $arIBlock["FIELDS"]["SECTION_CODE"]["DEFAULT_VALUE"];
		if (
			is_array($ar)
			&& $ar["UNIQUE"] == "Y"
			&& !$DB->IndexExists("b_iblock_section", array("IBLOCK_ID", "CODE"))
		)
			$DB->Query("create index ix_iblock_section_code on b_iblock_section (IBLOCK_ID, CODE)");
	}


	function GetAuditTypes()
	{
		return array(
			"IBLOCK_SECTION_ADD" => "[IBLOCK_SECTION_ADD] ".GetMessage("IBLOCK_SECTION_ADD"),
			"IBLOCK_SECTION_EDIT" => "[IBLOCK_SECTION_EDIT] ".GetMessage("IBLOCK_SECTION_EDIT"),
			"IBLOCK_SECTION_DELETE" => "[IBLOCK_SECTION_DELETE] ".GetMessage("IBLOCK_SECTION_DELETE"),
			"IBLOCK_ELEMENT_ADD" => "[IBLOCK_ELEMENT_ADD] ".GetMessage("IBLOCK_ELEMENT_ADD"),
			"IBLOCK_ELEMENT_EDIT" => "[IBLOCK_ELEMENT_EDIT] ".GetMessage("IBLOCK_ELEMENT_EDIT"),
			"IBLOCK_ELEMENT_DELETE" => "[IBLOCK_ELEMENT_DELETE] ".GetMessage("IBLOCK_ELEMENT_DELETE"),
			"IBLOCK_ADD" => "[IBLOCK_ADD] ".GetMessage("IBLOCK_ADD"),
			"IBLOCK_EDIT" => "[IBLOCK_EDIT] ".GetMessage("IBLOCK_EDIT"),
			"IBLOCK_DELETE" => "[IBLOCK_DELETE] ".GetMessage("IBLOCK_DELETE"),
		);
	}

	function roundDB($value)
	{
		$len = 18;
		$dec = 4;
		$eps = 1.00 / pow(10, $len + 4);
		$rounded = round(doubleval($value) + $eps, $len);
		if (is_nan($rounded) || is_infinite($rounded))
			$rounded = 0;

		$result = sprintf("%01.".$dec."f", $rounded);
		if (strlen($result) > ($len - $dec))
			$result = trim(substr($result, 0, $len - $dec), ".");

		return $result;
	}

	function _transaction_lock($IBLOCK_ID)
	{
		global $DB;
		$DB->Query("UPDATE b_iblock set TMP_ID = '".md5(mt_rand())."' WHERE ID = ".$IBLOCK_ID);
	}
}
?>
