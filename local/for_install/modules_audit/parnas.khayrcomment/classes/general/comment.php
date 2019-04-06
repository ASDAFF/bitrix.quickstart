<?
if (!class_exists("KhayRComment"))
{
	class KhayRComment
	{
		const MODULE_ID = 'parnas.khayrcomment';
		
		public static function GetCount($ID = 0, $active = true, $SITE_ID = SITE_ID)
		{
			if (!CModule::IncludeModule("iblock")) die();
			
			$arFilter = array(
				"IBLOCK_ID" => self::GetIBlock($SITE_ID)
			);
			if ($ID)
				$arFilter["PROPERTY_OBJECT"] = $ID;
			if ($active)
			{
				$arFilter["ACTIVE"] = "Y";
				$arFilter["PROPERTY_PARENT"] = false;
			}
			else
			{
				$arFilter["ACTIVE"] = "N";
			}
			$arResult = CIBlockElement::GetList(array(), $arFilter, array());
			return $arResult;
		}
		
		public static function CheckFields($arFields = array())
		{
			if (isset($arFields["object"]))
			{
				$arFields["object"] = intval($arFields["object"]);
				if ($arFields["object"] < 0)
					$arFields["object"] = 0;
			}
			else
				$arFields["object"] = 0;
			
			if (isset($arFields["parent"]))
			{
				$arFields["parent"] = intval($arFields["parent"]);
				if ($arFields["parent"] <= 0)
					$arFields["parent"] = false;
			}
			else
				$arFields["parent"] = false;
			
			if (isset($arFields["level"]))
			{
				$arFields["level"] = intval($arFields["level"]);
				if ($arFields["level"] < 1)
					$arFields["level"] = 1;
			}
			else
				$arFields["level"] = 1;
			
			if (isset($arFields["text"]))
				$arFields["text"] = trim($arFields["text"]);
			else
				$arFields["text"] = "";
			
			if (isset($arFields["mark"]))
			{
				$arFields["mark"] = intval($arFields["mark"]);
				if ($arFields["mark"] < 0)
					$arFields["mark"] = 0;
			}
			else
				$arFields["mark"] = 0;
			
			if (isset($arFields["dignity"]))
			{
				$arFields["dignity"] = trim($arFields["dignity"]);
				if (!$arFields["dignity"])
					$arFields["dignity"] = false;
			}
			else
				$arFields["dignity"] = false;
			
			if (isset($arFields["fault"]))
			{
				$arFields["fault"] = trim($arFields["fault"]);
				if (!$arFields["fault"])
					$arFields["fault"] = false;
			}
			else
				$arFields["fault"] = false;
			
			if (isset($arFields["additional"]))
			{
				$arFields["additional"] = trim($arFields["additional"]);
				if (!$arFields["additional"])
					$arFields["additional"] = false;
			}
			else
				$arFields["additional"] = false;
			
			if (isset($arFields["author"]))
			{
				$arFields["author"] = intval($arFields["author"]);
				if ($arFields["author"] <= 0)
					$arFields["author"] = false;
			}
			else
				$arFields["author"] = false;
			
			if (isset($arFields["nonuser"]))
			{
				$arFields["nonuser"] = trim($arFields["nonuser"]);
				if (!$arFields["nonuser"])
					$arFields["nonuser"] = false;
			}
			else
				$arFields["nonuser"] = false;
			
			if (isset($arFields["email"]))
			{
				$arFields["email"] = trim($arFields["email"]);
				if (!$arFields["email"])
					$arFields["email"] = false;
			}
			else
				$arFields["email"] = false;
			
			if (isset($arFields["avatar"]))
			{
				if (file_exists($arFields["avatar"]))
					$arFields["avatar"] = CFile::MakeFileArray($arFields["avatar"]);
				else
					$arFields["avatar"] = false;
			}
			else
				$arFields["avatar"] = false;
			
			if (isset($arFields["active"]))
			{
				if (!$arFields["active"] || $arFields["active"] == "N")
					$arFields["active"] = "N";
				else
					$arFields["active"] = "Y";
			}
			else
				$arFields["active"] = "Y";
			
			return $arFields;
		}
		
		public static function Add($arFields = array())
		{
			if (!CModule::IncludeModule("iblock")) die();
			
			$arFields = self::CheckFields($arFields);
			
			$arF = array(
				"IBLOCK_ID" => self::GetIBlock(),
				"IBLOCK_SECTION_ID" => false,
				"ACTIVE" => $arFields["active"],
				"NAME" => $arFields["nonuser"].($arFields["nonuser"] && $arFields["email"] ? " " : "").($arFields["email"] ? "(".$arFields["email"].")" : ""),
				"PREVIEW_TEXT" => $arFields["text"],
				"PREVIEW_TEXT_TYPE" => "html",
				"PREVIEW_PICTURE" => $arFields["avatar"],
				"PROPERTY_VALUES" => array(
					"OBJECT" => $arFields["object"],
					"PARENT" => $arFields["parent"],
					"DEPTH" => $arFields["level"],
					"USER" => $arFields["author"],
					"NONUSER" => $arFields["nonuser"],
					"EMAIL" => $arFields["email"],
					"MARK" => $arFields["mark"],
					"DIGNITY" => $arFields["dignity"],
					"FAULT" => $arFields["fault"],
					"ADDITIONAL" => $arFields["additional"],
				),
			);
			$el = new CIBlockElement();
			if ($id = $el->Add($arF))
				return $id;
			else
			{
				$GLOBALS["KHAYR_MAIN_COMMENT_COMMENT_ERROR"] = $el->LAST_ERROR;
				return false;
			}
		}
		
		public static function Update($id, $text)
		{
			if (!CModule::IncludeModule("iblock")) die();
			
			global $USER;
			
			//Ignore empty id and not authorized users
			if (intval($id) <= 0 || !$USER->IsAuthorized())
				return false;
			
			// Check own and iblock element
			// Filter for check
			$arFilter = Array(
				'ID' => $id,
				'IBLOCK_ID' => self::GetIBlock()
			);
			// Only own objects
			if (!$USER->IsAdmin())
				$arFilter['PROPERTY_USER_VALUE'] = $USER->GetID();
			
			// Check
			$check = CIBlockElement::GetList(Array(), $arFilter, Array());
			if (intval($check) <= 0)
				return false;
			
			//Update item
			$el = new CIBlockElement();
			if ($el->Update($id, Array("PREVIEW_TEXT" => $text)))
			{
				return true;
			}
			else
			{
				$GLOBALS["KHAYR_MAIN_COMMENT_COMMENT_ERROR"] = $el->LAST_ERROR;
				return false;
			}
		}
		
		/**
		 * Delete comment(and children) by id
		 * @global CUser $USER
		 * @param $id Comment id
		 * @return bool
		 */
		public static function Delete($id)
		{
			if (!CModule::IncludeModule("iblock")) die();
			
			global $USER;
			
			//Ignore empty id and not authorized users
			if (intval($id) <= 0 || !$USER->IsAuthorized())
				return false;
			
			// Check own and iblock element
			// Filter for check
			$arFilter = Array(
				'ID' => $id,
				'IBLOCK_ID' => self::GetIBlock()
			);
			// Only own objects
			if (!$USER->IsAdmin())
				$arFilter['PROPERTY_USER_VALUE'] = $USER->GetID();
			
			// Check
			$check = CIBlockElement::GetList(Array(), $arFilter, Array());
			if (intval($check) <= 0)
				return false;
			
			//Delete item and children
			$arElem = CIBlockElement::GetList(Array(), Array("IBLOCK_ID" => self::GetIBlock(), "PROPERTY_PARENT" => $id), false, false, Array("ID"));
			CIBlockElement::Delete($id);
			while ($resElem = $arElem->GetNext())
			{
				self::Delete($resElem["ID"]);
			}
			
			return true;
		}
		
		// only for component - get comments and childs
		public static function Show($arParams, $arSelect = array(), $sort = array(), $arNavParams = false)
		{
			if (!CModule::IncludeModule("iblock")) die();
			
			$arResult["ITEMS"] = array();
			$arResult["ELEMENTS"] = array();
			$arFilter = array(
				"IBLOCK_ID" => self::GetIBlock(),
				"PROPERTY_OBJECT" => $arParams["OBJECT_ID"],
				"PROPERTY_PARENT" => false,
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
			);
			foreach (Array("DATE_CREATE", "RATING_TOTAL_VALUE", "RATING_TOTAL_VOTES", "RATING_TOTAL_POSITIVE_VOTES", "RATING_TOTAL_NEGATIVE_VOTES", "RATING_USER_VOTE_VALUE") as $s)
			{
				if (!in_array($s, $arSelect))
					$arSelect[] = $s;
			}
			$rsElement = CIBlockElement::GetList($sort, $arFilter, false, $arNavParams, $arSelect);
			$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx($arParams["PAGER_DESC_NUMBERING"], $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"], $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
			$arResult["NAV_RESULT"] = $rsElement;
			while ($obElement = $rsElement->GetNextElement())
			{
				$arItem = $obElement->GetFields();
				$arItem["PUBLISH_DATE"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["DATE_CREATE"], CSite::GetDateFormat()));
				$arItem["PROPERTIES"] = $obElement->GetProperties();
				$arItem["AUTHOR"] = self::GetAuthor($arItem["PROPERTIES"]["USER"]["VALUE"], $arItem);
				$arItem["PUBLISH_TEXT"] = self::GetText($arItem["~PREVIEW_TEXT"]);
				$arItem["MARK"] = intval($arItem["PROPERTIES"]["MARK"]["VALUE"]);
				$arItem["DIGNITY"] = self::GetText($arItem["PROPERTIES"]["DIGNITY"]["VALUE"]);
				$arItem["FAULT"] = self::GetText($arItem["PROPERTIES"]["FAULT"]["VALUE"]);
				$arItem["ADDITIONAL"] = unserialize(htmlspecialcharsBack($arItem["PROPERTIES"]["ADDITIONAL"]["VALUE"]));
				if (!is_array($arItem["ADDITIONAL"]))
					$arItem["ADDITIONAL"] = array();
				if ($arParams["MAX_DEPTH"] && ($arParams["MAX_DEPTH"] > $arItem["PROPERTIES"]["DEPTH"]["VALUE"]))
				{
					$arItem["CHILDS"] = self::GetTree($arItem["ID"], $arParams, $arSelect, $sort, false);
				}
				$arItem['RATING'] = Array(
					"USER_HAS_VOTED" => (floatval($arItem['RATING_USER_VOTE_VALUE']) > 0 ? 'Y' : 'N'),
					"TOTAL_VOTES" => $arItem['RATING_TOTAL_VOTES'],
					"TOTAL_POSITIVE_VOTES" => $arItem['RATING_TOTAL_POSITIVE_VOTES'],
					"TOTAL_NEGATIVE_VOTES" => $arItem['RATING_TOTAL_NEGATIVE_VOTES'],
					"TOTAL_VALUE" => $arItem['RATING_TOTAL_VALUE']
				);
				$arItem["CAN_COMMENT"] = ((($arParams["NON_AUTHORIZED_USER_CAN_COMMENT"] == "Y") || $GLOBALS["USER"]->IsAuthorized()) && ($arItem["PROPERTIES"]["DEPTH"]["VALUE"] < $arParams["MAX_DEPTH"]));
				$arItem["CAN_MODIFY"] = ((($arParams["CAN_MODIFY"] == "Y") && ($arItem["PROPERTIES"]["USER"]["VALUE"] == $GLOBALS["USER"]->GetID()) && $GLOBALS["USER"]->IsAuthorized()) || $GLOBALS["USER"]->IsAdmin());
				$arItem["CAN_DELETE"] = ((($arItem["PROPERTIES"]["USER"]["VALUE"] == $GLOBALS["USER"]->GetID()) && $GLOBALS["USER"]->IsAuthorized()) || ($GLOBALS["USER"]->isAdmin()));
				$arItem["SHOW_RATING"] = ($arParams["ALLOW_RATING"] == "Y");
				$arResult["ITEMS"][] = $arItem;
				$arResult["ELEMENTS"][] = $arItem["ID"];
			}
			return $arResult;
		}
		
		// only for component - get childs as tree
		public static function GetTree($id, $arParams, $arSelect = array(), $sort = array(), $arNavParams = false)
		{
			if (!CModule::IncludeModule("iblock")) die();
			
			$arResult = array();
			$arFilter = array(
				"IBLOCK_ID" => self::GetIBlock(),
				"PROPERTY_OBJECT" => $arParams["OBJECT_ID"],
				"PROPERTY_PARENT" => $id,
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "Y",
			);
			foreach (Array("DATE_CREATE", "RATING_TOTAL_VALUE", "RATING_TOTAL_VOTES", "RATING_TOTAL_POSITIVE_VOTES", "RATING_TOTAL_NEGATIVE_VOTES", "RATING_USER_VOTE_VALUE") as $s)
			{
				if (!in_array($s, $arSelect))
					$arSelect[] = $s;
			}
			$rsElement = CIBlockElement::GetList($sort, $arFilter, false, $arNavParams, $arSelect);
			while ($obElement = $rsElement->GetNextElement())
			{
				$arItem = $obElement->GetFields();
				$arItem["PUBLISH_DATE"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["DATE_CREATE"], CSite::GetDateFormat()));
				$arItem["PROPERTIES"] = $obElement->GetProperties();
				$arItem["AUTHOR"] = self::GetAuthor($arItem["PROPERTIES"]["USER"]["VALUE"], $arItem);
				$arItem["PUBLISH_TEXT"] = self::GetText($arItem["~PREVIEW_TEXT"]);
				$arItem["MARK"] = intval($arItem["PROPERTIES"]["MARK"]["VALUE"]);
				$arItem["DIGNITY"] = self::GetText($arItem["PROPERTIES"]["DIGNITY"]["VALUE"]);
				$arItem["FAULT"] = self::GetText($arItem["PROPERTIES"]["FAULT"]["VALUE"]);
				$arItem["ADDITIONAL"] = unserialize(htmlspecialcharsBack($arItem["PROPERTIES"]["ADDITIONAL"]["VALUE"]));
				if (!is_array($arItem["ADDITIONAL"]))
					$arItem["ADDITIONAL"] = array();
				if ($arParams["MAX_DEPTH"] && ($arParams["MAX_DEPTH"] > $arItem["PROPERTIES"]["DEPTH"]["VALUE"]))
				{
					$arItem["CHILDS"] = self::GetTree($arItem["ID"], $arParams, $arSelect, $sort, $arNavParams);
				}
				$arItem['RATING'] = Array(
					"USER_HAS_VOTED" => (floatval($arItem['RATING_USER_VOTE_VALUE']) > 0 ? 'Y' : 'N'),
					"TOTAL_VOTES" => $arItem['RATING_TOTAL_VOTES'],
					"TOTAL_POSITIVE_VOTES" => $arItem['RATING_TOTAL_POSITIVE_VOTES'],
					"TOTAL_NEGATIVE_VOTES" => $arItem['RATING_TOTAL_NEGATIVE_VOTES'],
					"TOTAL_VALUE" => $arItem['RATING_TOTAL_VALUE']
				);
				$arItem["CAN_COMMENT"] = ((($arParams["NON_AUTHORIZED_USER_CAN_COMMENT"] == "Y") || $GLOBALS["USER"]->IsAuthorized()) && ($arItem["PROPERTIES"]["DEPTH"]["VALUE"] < $arParams["MAX_DEPTH"]));
				$arItem["CAN_MODIFY"] = ((($arParams["CAN_MODIFY"] == "Y") && ($arItem["PROPERTIES"]["USER"]["VALUE"] == $GLOBALS["USER"]->GetID()) && $GLOBALS["USER"]->IsAuthorized()) || $GLOBALS["USER"]->IsAdmin());
				$arItem["CAN_DELETE"] = ((($arItem["PROPERTIES"]["USER"]["VALUE"] == $GLOBALS["USER"]->GetID()) && $GLOBALS["USER"]->IsAuthorized()) || ($GLOBALS["USER"]->isAdmin()));
				$arItem["SHOW_RATING"] = ($arParams["ALLOW_RATING"] == "Y");
				$arResult[] = $arItem;
			}
			return $arResult;
		}
		
		public static function GetAuthor($user_id = 0, $arItem = array())
		{
			$result = array();
			if ($user_id <= 0 && $arItem["PROPERTIES"]["USER"]["VALUE"])
				$user_id = $arItem["PROPERTIES"]["USER"]["VALUE"];
			if ($user_id > 0)
			{
				$user = CUser::GetByID($user_id)->Fetch();
				if ($user)
				{
					$result["ID"] = $user_id;
					$result["AVATAR"] = $user["PERSONAL_PHOTO"];
					if (!$result["AVATAR"] && $arItem["PREVIEW_PICTURE"])
						$result["AVATAR"] = $arItem["PREVIEW_PICTURE"];
					if ($result["AVATAR"])
						$result["AVATAR"] = CFile::GetFileArray($result["AVATAR"]);
					$result["EMAIL"] = $user["EMAIL"];
					if (!$result["EMAIL"] && $arItem["PROPERTIES"]["EMAIL"]["VALUE"])
						$result["EMAIL"] = $arItem["PROPERTIES"]["EMAIL"]["VALUE"];
					$result["LOGIN"] = $user["LOGIN"];
					$result["NAME"] = ($user["NAME"] ? $user["NAME"] : $user["LOGIN"]);
					$result["FULL_NAME"] = ($user["NAME"] || $user["LAST_NAME"] ? $user["NAME"].($user["NAME"] && $user["LAST_NAME"] ? " " : "").$user["LAST_NAME"] : $user["LOGIN"]);
					$result["ALL"] = $user;
				}
			}
			if ($user_id <= 0)
				$user_id = 0;
			if (!$result)
			{
				$result["ID"] = $user_id;
				$result["AVATAR"] = $arItem["PREVIEW_PICTURE"];
				if ($result["AVATAR"])
					$result["AVATAR"] = CFile::GetFileArray($result["AVATAR"]);
				$result["EMAIL"] = $arItem["PROPERTIES"]["EMAIL"]["VALUE"];
				$result["LOGIN"] = $arItem["PROPERTIES"]["NONUSER"]["VALUE"];
				$result["NAME"] = $arItem["PROPERTIES"]["NONUSER"]["VALUE"];
				$result["FULL_NAME"] = $arItem["PROPERTIES"]["NONUSER"]["VALUE"];
				$result["ALL"] = array();
			}
			return $result;
		}
		
		public static function GetSiteID($SITE_ID = SITE_ID)
		{
			if (!$SITE_ID)
				$SITE_ID = SITE_ID;
			if ($SITE_ID)
			{
				$sids = array();
				$arQuery = CSite::GetList($sort = "sort", $order = "desc", Array());
				while ($res = $arQuery->Fetch())
				{
					$sids[] = $res["ID"];
				}
				
				if (!in_array($SITE_ID, $sids))
					$SITE_ID = $sids[0];
			}
			return $SITE_ID;
		}
		
		public static function GetIBlock($SITE_ID = SITE_ID)
		{
			global $APPLICATION;
			
			$SITE_ID = self::GetSiteID($SITE_ID);
			
			$ib = false;
			$use_site = COption::GetOptionString(self::MODULE_ID, "use_on_sites_".$SITE_ID, "");
			if ($use_site)
			{
				$ib = COption::GetOptionString(self::MODULE_ID, "IBLOCK_".$SITE_ID, "");
			}
			if (!$ib)
			{
				$ib = COption::GetOptionString(self::MODULE_ID, "IBLOCK");
			}
			if (!$ib)
			{
				$APPLICATION->ThrowException(GetMessage('KHAYR_COMMENT_CLASS_ERRORS_IBLOCK')); 
				return false;
			}
			else
				return $ib;
		}
		
		public static function GetIBlocks()
		{
			$r = array();
			
			$sites = array();
			$arQuery = CSite::GetList($sort = "sort", $order = "desc", Array());
			while ($res = $arQuery->Fetch())
			{
				$sites[$res["ID"]] = $res;
			}
			
			foreach ($sites as $sid => $site)
			{
				$r[$sid] = array();
				$r[$sid]["SITE_NAME"] = $site["NAME"];
				$r[$sid]["IBLOCK_ID"] = self::GetIBlock($sid);
				$r[$sid]["RIGHTS"] = self::GetRights($r[$sid]["IBLOCK_ID"]);
			}
			
			return $r;
		}
		
		public static function CheckIBlock($ib)
		{
			if (!CModule::IncludeModule("iblock")) die();
			$r = true;
			
			$iblock = CIBlock::GetByID($ib)->GetNext();
			if (!$iblock)
				$r = false;
			
			if ($r)
			{
				$arPropsIB = array();
				$res = CIBlock::GetProperties($ib, Array(), Array());
				while ($res_arr = $res->Fetch())
					$arPropsIB[] = $res_arr["CODE"];
				
				$arProps = Array();
				$arProps[] = Array(
					"NAME" => GetMessage("KHAYR_COMMENT_PR_OBJECT"),
					"ACTIVE" => "Y",
					"SORT" => "100",
					"CODE" => "OBJECT",
					"PROPERTY_TYPE" => "E",
					"IBLOCK_ID" => $ib,
					"WITH_DESCRIPTION" => "N",
				);
				$arProps[] = Array(
					"NAME" => GetMessage("KHAYR_COMMENT_PR_AUTHOR"),
					"ACTIVE" => "Y",
					"SORT" => "200",
					"CODE" => "USER",
					"PROPERTY_TYPE" => "S",
					"USER_TYPE" => "UserID",
					"IBLOCK_ID" => $ib,
					"WITH_DESCRIPTION" => "N",
				);
				$arProps[] = Array(
					"NAME" => GetMessage("KHAYR_COMMENT_PR_EMAIL"),
					"ACTIVE" => "Y",
					"SORT" => "300",
					"CODE" => "EMAIL",
					"PROPERTY_TYPE" => "S",
					"IBLOCK_ID" => $ib,
					"WITH_DESCRIPTION" =>"N",
				);
				$arProps[] = Array(
					"NAME" => GetMessage("KHAYR_COMMENT_PR_NONUSER"),
					"ACTIVE" => "Y",
					"SORT" => "400",
					"CODE" => "NONUSER",
					"PROPERTY_TYPE" =>"S",
					"IBLOCK_ID" => $ib,
					"WITH_DESCRIPTION" => "N",
				);
				$arProps[] = Array(
					"NAME" => GetMessage("KHAYR_COMMENT_PR_DEPTH"),
					"ACTIVE" => "Y",
					"SORT" => "500",
					"CODE" => "DEPTH",
					"PROPERTY_TYPE" => "S",
					"IBLOCK_ID" => $ib,
					"WITH_DESCRIPTION" => "N",
				);
				$arProps[] = Array(
					"NAME" => GetMessage("KHAYR_COMMENT_PR_PARENT"),
					"ACTIVE" => "Y",
					"SORT" => "600",
					"CODE" => "PARENT",
					"PROPERTY_TYPE" => "E",
					"IBLOCK_ID" => $ib,
					"WITH_DESCRIPTION" => "N",
				);
				$arProps[] = Array(
					"NAME" => GetMessage("KHAYR_COMMENT_PR_MARK"),
					"ACTIVE" => "Y",
					"SORT" => "700",
					"CODE" => "MARK",
					"PROPERTY_TYPE" =>"S",
					"IBLOCK_ID" => $ib,
					"WITH_DESCRIPTION" => "N",
				);
				$arProps[] = Array(
					"NAME" => GetMessage("KHAYR_COMMENT_PR_DIGNITY"),
					"ACTIVE" => "Y",
					"SORT" => "800",
					"CODE" => "DIGNITY",
					"PROPERTY_TYPE" =>"S",
					"IBLOCK_ID" => $ib,
					"WITH_DESCRIPTION" => "N",
				);
				$arProps[] = Array(
					"NAME" => GetMessage("KHAYR_COMMENT_PR_FAULT"),
					"ACTIVE" => "Y",
					"SORT" => "900",
					"CODE" => "FAULT",
					"PROPERTY_TYPE" =>"S",
					"IBLOCK_ID" => $ib,
					"WITH_DESCRIPTION" => "N",
				);
				$arProps[] = Array(
					"NAME" => GetMessage("KHAYR_COMMENT_PR_ADDITIONAL"),
					"ACTIVE" => "Y",
					"SORT" => "1000",
					"CODE" => "ADDITIONAL",
					"PROPERTY_TYPE" =>"S",
					"IBLOCK_ID" => $ib,
					"WITH_DESCRIPTION" => "N",
				);
				
				$iblockproperty = new CIBlockProperty;
				foreach ($arProps as $pr)
				{
					if (!in_array($pr["CODE"], $arPropsIB))
						$PropertyID = $iblockproperty->Add($pr);
				}
			}
			
			return $r;
		}
		
		public static function CreateIBlock($type = "khayr")
		{
			global $DB;
			if (!CModule::IncludeModule("iblock")) die();
			$r = false;
			
			$sids = array();
			$arQuery = CSite::GetList($sort = "sort", $order = "desc", Array());
			while ($res = $arQuery->Fetch())
			{
				$sids[] = $res["ID"];
			}
			
			$arTypes = CIBlockType::GetByID($type);
			if (!$arType = $arTypes->Fetch())
			{
				$arFields = Array(
					'ID' => $type,
					'SECTIONS' => 'Y',
					'IN_RSS' => 'N',
					'SORT' => 100,
					'LANG' => Array(
						'ru' => Array(
							'NAME' => GetMessage("KHAYR_COMMENT_CLASS_TYPE_NAME"),
							'SECTION_NAME' => GetMessage("KHAYR_COMMENT_CLASS_SEC_NAME"),
							'ELEMENT_NAME' => GetMessage("KHAYR_COMMENT_CLASS_EL_NAME")
						)
					)
				);
				$obBlocktype = new CIBlockType;
				$DB->StartTransaction();
				$res = $obBlocktype->Add($arFields);
				if (!$res)
				{
					$DB->Rollback();
					echo 'Error: '.$obBlocktype->LAST_ERROR.'<br />';
					die("TYPE_ERROR");
				}
				else
					$DB->Commit();
			}
			
			$arFields = Array(
				"ACTIVE" => "Y",
				"NAME" => GetMessage("KHAYR_COMMENT_CLASS_IB_NAME"),
				"IBLOCK_TYPE_ID" => $type,
				"SITE_ID" => $sids,
				"GROUP_ID" => Array("2" => "R", "1" => "X"),
				"INDEX_ELEMENT" => "N",
				"INDEX_SECTION" => "N"
			);
			$ib = new CIBlock();
			if ($r = $ib->Add($arFields))
			{
				self::CheckIBlock($r);
			}
			else
			{
				echo 'Error: '.$ib->LAST_ERROR.'<br />';
			}
			
			return $r;
		}
		
		public static function GetRights($ib = false)
		{
			global $USER;
			if (!CModule::IncludeModule("iblock")) die();
			
			if ($USER->IsAdmin())
				return "X";
			else
			{
				if (!$ib)
					$ib = self::GetIBlock();
				
				if ($ib)
					return CIBlock::GetPermission($ib);
				else
					return "D";
			}
		}
		
		// only for admin section
		public static function GetRightsMax()
		{
			global $USER;
			if ($USER->IsAdmin())
				$r = "X";
			else
			{
				$r = "D";
				$ibs = self::GetIBlocks();
				foreach ($ibs as $ib)
					if ($ib["RIGHTS"] > $r)
						$r = $ib["RIGHTS"];
			}
			return $r;
		}
		
		public static function GetText($text)
		{
			return self::SetSmiles(self::ParseText($text));
		}
		
		//convert BBCode
		public static function ParseText($text = "", $arParams = array())
		{
			while (preg_match("#\[quote\](.*?)\[/quote\]#si", $text))
				$text = preg_replace("#\[quote\](.*?)\[/quote\]#si", '<table class="quote"><thead><tr><th></th></tr></thead><tbody><tr><td>\1</td></tr></tbody></table>', $text);
		
			$text = preg_replace("#\[code\](.*?)\[/code\]#si", '<div class="code">\1</div>', $text);
			preg_match_all('#<div class="code">(.*?)</div>#si', $text, $code);
		
			$items = $code[0];
		
			$values = array();
			foreach ($items as $key => $val)
				$values[] = "#$".$key."#";
		
			$text = str_replace($items, $values, $text);
		
			// Parse BB
			$search[] = "#\[b\](.*?)\[/b\]#si";
			$search[] = "#\[i\](.*?)\[/i\]#si";
			$search[] = "#\[s\](.*?)\[/s\]#si";
			$search[] = "#\[u\](.*?)\[/u\]#si";
			$search[] = "#\[IMG\](.*?)\[/IMG\]#si";
			$search[] = "#\[url=(.+?)\](.+?)\[\/url\]#is";
			$search[] = "#\[url\](.+?)\[\/url\]#is";
			$replace[] = '<strong>\1</strong>';
			$replace[] = '<i>\1</i>';
			$replace[] = '<strike>\1</strike>';
			$replace[] = '<u>\1</u>';
			$replace[] = '<div><img style="max-width: 275px; max-height: 275px; padding: 5px 0 5px 0; clear: both;" src="\1"></div>';
			$replace[] = "<a href='\\1'>\\2</a>";
			$replace[] = "<a href='\\1'>\\1</a>";
			$text = preg_replace($search, $replace, $text);
		
			//$text = preg_replace('#\[url=(https?|ftp)://(\S+[^\s.,>!?])\](.*?)\[\/url\]#si', '<a '.$arParams["NO_FOLLOW"].' href="http://$2">$3</a>', $text);
		
			//if ($arParams["SHOW_FILEMAN"] == 0)
				//$text = preg_replace("#(https?|ftp)://\S+[^\s.,>)\];'\"!?]#",'<a '.$arParams["NO_FOLLOW"].' href="\\0">\\0</a>',$text);
		
			$text = str_replace($values, $items, $text);
		
			return $text;
		}
		
		// Replace icons with pathnames
		public static function SetSmiles($text = "")
		{
			if (strlen($text) == 0)
				return false;
			$iconArr = self::GetSmiles("/bitrix/components/khayr/main.comment/images/smile/");
			if (is_array($iconArr))
			{
				foreach ($iconArr as $icon => $path)
				{
					$text = str_replace($path["code"], '<img alt="'.$path["name"].'" title="'.$path["name"].'" src="'.$path["path"].'" />', $text);
				}
			}
			return $text;
		}
		
		// Get smiles
		public static function GetSmiles($path)
		{
			$arSmiles = Array(
				Array(
					"name" => "Smile",
					"path" => $path."icon_smile.gif",
					"code" =>":)"
				),
				Array(
					"name" => "Smile",
					"path" => $path."icon_smile.gif",
					"code" =>":-)"
				),
				Array(
					"name" => "Joke",
					"path" => $path."icon_wink.gif",
					"code" => ";)"
				),
				Array(
					"name" => "wide smile",
					"path" => $path."icon_biggrin.gif",
					"code" => ":D"
				),
				Array(
					"name" => "Health",
					"path" => $path."icon_cool.gif",
					"code" => "8)"
				),
				Array(
					"name" => "Pity",
					"path" => $path."icon_sad.gif",
					"code" => ":("
				),
				Array(
					"name" => "Pity",
					"path" => $path."icon_sad.gif",
					"code" => ":-("
				),
				Array(
					"name" => "Spectic",
					"path" => $path."icon_neutral.gif",
					"code" => ":|"
				),
				Array(
					"name" => "Very sad",
					"path" => $path."icon_cry.gif",
					"code" => ":cry:"
				),
				Array(
					"name" => "angry",
					"path" => $path."icon_evil.gif",
					"code" => ":evil:"
				),
				Array(
					"name" =>"Wonder",
					"path" => $path."icon_eek.gif",
					"code" =>":o"
				),
				Array(
					"name" => "redface",
					"path" => $path."icon_redface.gif",
					"code" => ":oops:"
				),
				Array(
					"name" => "Kiss",
					"path" => $path."icon_kiss.gif",
					"code" => ":{}",
				),
				Array(
					"name" => "Question",
					"path" => $path."icon_question.gif",
					"code" => ":?:"
				),
				Array(
					"name" => "Exclaim",
					"path" => $path."icon_exclaim.gif",
					"code" => ":!:"
				),
				Array(
					"name" => "Idea",
					"path" => $path."icon_idea.gif",
					"code" => ":idea:"
				),
			);
			return $arSmiles;
		}
		
		public static function CheckEncode($str)
		{
			$a = CSite::GetByID(SITE_ID)->GetNext();
			if (strtolower($a["CHARSET"]) !== "utf-8")
			{
				$str = mb_convert_encoding($str, "windows-1251", "UTF-8");
			}
			return $str;
		}
	}
}
?>