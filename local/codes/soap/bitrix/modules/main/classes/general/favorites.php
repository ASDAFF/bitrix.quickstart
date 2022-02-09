<?
IncludeModuleLangFile(__FILE__);

class CAllFavorites extends CDBResult
{
	function err_mess()
	{
		return "<br>Class: CFavorites<br>File: ".__FILE__;
	}

	function GetIDByUrl($url)
	{
		if($url == "")
			return 0;

		$paresedUrl = CBXFavUrls::ParseDetail($url);
		$pathInfo = pathinfo($paresedUrl["path"]);

		$dbFav = CFavorites::GetList(array(),array(
			"URL" => "'%".$pathInfo["basename"]."%'",
			"MENU_FOR_USER" => $GLOBALS["USER"]->GetID(),
			"LANGUAGE_ID" => LANGUAGE_ID,
		));
		while($arFav = $dbFav->Fetch())
			if(CBXFavUrls::Compare($paresedUrl, $arFav["URL"]))
				return $arFav["ID"];

		return 0;
	}

	function GetByID($ID)
	{
		global $DB;
		$ID = intval($ID);
		if($ID<=0)
			return false;
		return ($DB->Query("
			SELECT F.*,
				".$DB->DateToCharFunction("F.TIMESTAMP_X")." as TIMESTAMP_X,
				".$DB->DateToCharFunction("F.DATE_CREATE")." as	DATE_CREATE
			FROM b_favorite F
			WHERE ID=".$ID,
			false, "File: ".__FILE__."<br>Line: ".__LINE__));
	}

	function CheckFields($arFields)
	{
		$aMsg = array();
		if(is_set($arFields, "NAME") && trim($arFields["NAME"])=="")
			$aMsg[] = array("id"=>"NAME", "text"=>GetMessage("fav_general_err_name"));
		if(is_set($arFields, "URL") && trim($arFields["URL"])=="")
			$aMsg[] = array("id"=>"URL", "text"=>GetMessage("fav_general_err_url"));
		if(is_set($arFields, "USER_ID"))
		{
			if(intval($arFields["USER_ID"]) > 0)
			{
				$res = CUser::GetByID(intval($arFields["USER_ID"]));
				if(!$res->Fetch())
					$aMsg[] = array("id"=>"USER_ID", "text"=>GetMessage("fav_general_err_user"));
			}
			elseif($arFields["COMMON"] == "N")
				$aMsg[] = array("id"=>"USER_ID", "text"=>GetMessage("fav_general_err_user1"));
		}
		if(is_set($arFields, "LANGUAGE_ID"))
		{
			if($arFields["LANGUAGE_ID"] <> "")
			{
				$res = CLanguage::GetByID($arFields["LANGUAGE_ID"]);
				if(!$res->Fetch())
					$aMsg[] = array("id"=>"LANGUAGE_ID", "text"=>GetMessage("fav_general_err_lang"));
			}
			else
				$aMsg[] = array("id"=>"LANGUAGE_ID", "text"=>GetMessage("fav_general_err_lang1"));
		}

		if(!empty($aMsg))
		{
			$e = new CAdminException($aMsg);
			$GLOBALS["APPLICATION"]->ThrowException($e);
			return false;
		}
		return true;
	}

	function IsExistDuplicate($arFields)
	{
		if(!isset($arFields["MENU_ID"]) && !isset($arFields["URL"]) && !isset($arFields["NAME"]))
			return false;

		global $USER, $DB;

		$uid = $USER->GetID();


		$strSql ="SELECT MENU_ID, URL, ID FROM b_favorite  WHERE ( ";

		if(isset($arFields["MENU_ID"]))
			$strSql .= "MENU_ID = '".$DB->ForSql($arFields["MENU_ID"])."' AND ";

		if(isset($arFields["URL"]))
			$strSql .= "URL = '".$DB->ForSql($arFields["URL"])."' AND ";

		if(isset($arFields["NAME"]))
			$strSql .= "NAME = '".$DB->ForSql($arFields["NAME"])."' AND ";

			$strSql .="( USER_ID=".$uid." OR COMMON='Y' ))";

		$dbFav = $DB->Query($strSql);

		while ($arFav = $dbFav->GetNext())
			if($arFields["MENU_ID"] == $arFav["MENU_ID"] || $arFields["URL"] == $arFav["URL"] || $arFields["NAME"] == $arFav["NAME"])
				return $arFav["ID"];

		return false;
	}

	//Addition
	function Add($arFields, $checkDuplicate = false)
	{
		global $DB;

		if(!CFavorites::CheckFields($arFields))
			return false;

		if($checkDuplicate)
		{
			$duplicate = CFavorites::IsExistDuplicate($arFields);

			if($duplicate)
				return $duplicate;
		}

		$codes = new CHotKeysCode;
		$codeID=$codes->Add(array(
									"CODE"=>"location.href='".$arFields["URL"]."';",
									"NAME"=>$arFields["NAME"],
									"COMMENTS"=>"FAVORITES",
									));

		$codes->Update($codeID,array(
									"CLASS_NAME"=>"FAV-".$codeID,
									"TITLE_OBJ"=>"FAV-".$codeID,
									));

		$arFields["CODE_ID"]=intval($codeID);

		$ID = $DB->Add("b_favorite", $arFields);
		return $ID;
	}

	//Update
	function Update($ID, $arFields)
	{
		global $DB;
		$ID = intval($ID);

		if(!CFavorites::CheckFields($arFields))
			return false;

		$strUpdate = $DB->PrepareUpdate("b_favorite", $arFields);
		if($strUpdate!="")
		{
			$strSql = "UPDATE b_favorite SET ".$strUpdate." WHERE ID=".$ID;
			if(!$DB->Query($strSql))
				return false;
		}
		return true;
	}

	// delete by ID
	function Delete($ID)
	{
		global $DB;
		$codes = new CHotKeysCode;

		$res = CFavorites::GetByID($ID);

		while($arFav = $res->Fetch())
			$codes->Delete($arFav["CODE_ID"]);

		return ($DB->Query("DELETE FROM b_favorite WHERE ID='".intval($ID)."'", false, "File: ".__FILE__."<br>Line: ".__LINE__));
	}

	//*****************************
	// Events
	//*****************************

	//user deletion event
	function OnUserDelete($user_id)
	{
		global $DB;
		return ($DB->Query("DELETE FROM b_favorite WHERE USER_ID=". intval($user_id), false, "File: ".__FILE__."<br>Line: ".__LINE__));
	}

	//interface language delete event
	function OnLanguageDelete($language_id)
	{
		global $DB;
		return ($DB->Query("DELETE FROM b_favorite WHERE LANGUAGE_ID='".$DB->ForSQL($language_id, 2)."'", false, "File: ".__FILE__."<br>Line: ".__LINE__));
	}
}

class CUserOptions
{
	protected static $__USER_OPTIONS_DB;
	protected static $__USER_OPTIONS_MC;
	protected static $__USER_OPTIONS_CACHE;

	public static function GetList($arOrder = array("ID" => "ASC"), $arFilter = array())
	{
		global $DB;

		$arSqlSearch = array();
		foreach ($arFilter as $key => $val)
		{
			$key = strtoupper($key);
			switch ($key)
			{
			case "ID":
				$arSqlSearch[] = "UO.ID = ".intval($val);
				break;

			case "USER_ID":
				$arSqlSearch[] = "UO.USER_ID = ".intval($val);
				break;

			case "USER_ID_EXT":
				$arSqlSearch[] = "(UO.USER_ID = ".intval($val)." OR UO.COMMON='Y')";
				break;

			case "CATEGORY":
				$arSqlSearch[] = "UO.CATEGORY = '".$DB->ForSql($val)."'";
				break;

			case "NAME":
				$arSqlSearch[] = "UO.NAME = '".$DB->ForSql($val)."'";
				break;

			case "NAME_MASK":
				$arSqlSearch[] = GetFilterQuery("UO.NAME", $val);
				break;

			case "COMMON":
				$arSqlSearch[] = "UO.COMMON = '".$DB->ForSql($val)."'";
				break;
			}
		}

		$strSqlSearch = "";
		foreach ($arSqlSearch as $condition)
			if (strlen($condition) > 0)
				$strSqlSearch.= " AND  (".$condition.") ";

		$strSql = "
			SELECT UO.ID, UO.USER_ID, UO.CATEGORY, UO.NAME, UO.COMMON, UO.VALUE
			FROM b_user_option UO
			WHERE 1 = 1
			".$strSqlSearch."
		";

		$arSqlOrder = array();
		if (is_array($arOrder))
		{
			foreach ($arOrder as $by => $order)
			{
				$by = strtoupper($by);
				$order = strtoupper($order);
				if ($order != "ASC")
					$order = "DESC";

				if ($by == "ID")
					$arSqlOrder[$by] = " UO.ID ".$order." ";
				elseif ($by == "USER_ID")
					$arSqlOrder[$by] = " UO.USER_ID ".$order." ";
				elseif ($by == "CATEGORY")
					$arSqlOrder[$by] = " UO.CATEGORY ".$order." ";
				elseif ($by == "NAME")
					$arSqlOrder[$by] = " UO.NAME ".$order." ";
				elseif ($by == "COMMON")
					$arSqlOrder[$by] = " UO.COMMON ".$order." ";
			}
		}

		if (!empty($arSqlOrder))
			$strSqlOrder = "ORDER BY ".implode(", ", $arSqlOrder);
		else
			$strSqlOrder = "";

		$res = $DB->Query($strSql.$strSqlOrder, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		return $res;
	}

	public static function GetOption($category, $name, $default_value = false, $user_id = false)
	{
		global $DB, $USER, $CACHE_MANAGER;
		if (!isset(self::$__USER_OPTIONS_DB))
			self::_clear_cache();

		if ($user_id === false)
			$user_id = $USER->GetID();

		$user_id = intval($user_id);
		$cache_key = $category.".".$name;

		if ($category !== 'admin_menu' && $category !== 'favorite')
		{
			if (!isset(self::$__USER_OPTIONS_MC[$user_id]))
			{
				if ($CACHE_MANAGER->read(3600, $mcache_id = "user_option:$user_id", "user_option"))
				{
					self::$__USER_OPTIONS_MC[$user_id] = $CACHE_MANAGER->get($mcache_id);
				}
				else
				{
					$strSql = "
						SELECT CATEGORY, NAME, VALUE, COMMON
						FROM b_user_option
						WHERE (USER_ID=".$user_id." OR USER_ID IS NULL AND COMMON='Y')
						AND CATEGORY not in ('admin_menu', 'favorite')
					";

					$res = $DB->Query($strSql);
					while ($res_array = $res->Fetch())
					{
						$row_cache_key = $res_array["CATEGORY"].".".$res_array["NAME"];
						if (!isset(self::$__USER_OPTIONS_MC[$user_id][$row_cache_key]) || $res_array["COMMON"] <> 'Y')
							self::$__USER_OPTIONS_MC[$user_id][$row_cache_key] = $res_array["VALUE"];
					}

					$CACHE_MANAGER->Set($mcache_id, self::$__USER_OPTIONS_MC[$user_id]);
				}
			}

			if (!isset(self::$__USER_OPTIONS_MC[$user_id][$cache_key]))
				return $default_value;
		}
		else
		{
			if (!isset(self::$__USER_OPTIONS_DB[$user_id]))
			{
				//user (or default) options
				$strSql = "
					SELECT CATEGORY, NAME, VALUE, COMMON
					FROM b_user_option
					WHERE (USER_ID=".$user_id." OR USER_ID IS NULL AND COMMON='Y')
					AND CATEGORY in ('admin_menu', 'favorite')
				";

				$res = $DB->Query($strSql);
				while ($res_array = $res->Fetch())
				{
					$row_cache_key = $res_array["CATEGORY"].".".$res_array["NAME"];
					if (!isset(self::$__USER_OPTIONS_DB[$user_id][$row_cache_key]) || $res_array["COMMON"] <> 'Y')
						self::$__USER_OPTIONS_DB[$user_id][$row_cache_key] = $res_array["VALUE"];
				}
			}

			if (!isset(self::$__USER_OPTIONS_DB[$user_id][$cache_key]))
				return $default_value;
		}

		if (!isset(self::$__USER_OPTIONS_CACHE[$user_id][$cache_key]))
		{
			if (isset(self::$__USER_OPTIONS_MC[$user_id][$cache_key]))
				self::$__USER_OPTIONS_CACHE[$user_id][$cache_key] = unserialize(self::$__USER_OPTIONS_MC[$user_id][$cache_key]);
			else
				self::$__USER_OPTIONS_CACHE[$user_id][$cache_key] = unserialize(self::$__USER_OPTIONS_DB[$user_id][$cache_key]);
		}

		return self::$__USER_OPTIONS_CACHE[$user_id][$cache_key];
	}

	public static function SetOption($category, $name, $value, $bCommon = false, $user_id = false)
	{
		global $DB, $USER, $CACHE_MANAGER;
		if ($user_id === false && $bCommon === false)
			$user_id = $USER->GetID();

		$user_id = intval($user_id);
		$arFields = array(
			"USER_ID" => ($bCommon ? false : $user_id),
			"CATEGORY" => $category,
			"NAME" => $name,
			"VALUE" => serialize($value),
			"COMMON" => ($bCommon ? "Y" : "N"),
		);
		$res = $DB->Query("
			SELECT ID FROM b_user_option
			WHERE
			".($bCommon ? "USER_ID IS NULL AND COMMON='Y' " : "USER_ID=".$user_id)."
			AND CATEGORY='".$DB->ForSql($category, 50)."'
			AND NAME='".$DB->ForSql($name, 255)."'
		");

		if ($res_array = $res->Fetch())
		{
			$strUpdate = $DB->PrepareUpdate("b_user_option", $arFields);
			if ($strUpdate != "")
			{
				$strSql = "UPDATE b_user_option SET ".$strUpdate." WHERE ID=".$res_array["ID"];
				if (!$DB->QueryBind($strSql, array("VALUE" => $arFields["VALUE"])))
					return false;
			}
		}
		else
		{
			if (!$DB->Add("b_user_option", $arFields, array("VALUE")))
				return false;
		}

		self::_clear_cache($category, $bCommon, $user_id);
		return true;
	}

	public static function SetOptionsFromArray($aOptions)
	{
		global $USER;

		foreach ($aOptions as $opt)
		{
			if ($opt["c"] <> "" && $opt["n"] <> "")
			{
				$val = $opt["v"];
				if (is_array($opt["v"]))
				{
					$val = CUserOptions::GetOption($opt["c"], $opt["n"], array());
					foreach ($opt["v"] as $k => $v)
						$val[$k] = $v;
				}
				CUserOptions::SetOption($opt["c"], $opt["n"], $val);
				if ($opt["d"] == "Y" && $USER->CanDoOperation('edit_other_settings'))
					CUserOptions::SetOption($opt["c"], $opt["n"], $val, true);
			}
		}
	}

	public static function DeleteOption($category, $name, $bCommon = false, $user_id = false)
	{
		global $DB, $USER;
		if ($user_id === false)
			$user_id = $USER->GetID();

		$user_id = intval($user_id);
		$strSql = "
			DELETE FROM b_user_option
			WHERE ".($bCommon ? "USER_ID IS NULL AND COMMON='Y' " : "USER_ID=".$user_id)."
			AND CATEGORY='".$DB->ForSql($category, 50)."'
			AND NAME='".$DB->ForSql($name, 255)."'
		";
		if ($DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__))
		{
			self::_clear_cache($category, $bCommon, $user_id);
			return true;
		}
		return false;
	}

	public static function DeleteCommonOptions()
	{
		global $DB;
		if ($DB->Query("DELETE FROM b_user_option WHERE COMMON='Y' AND NAME NOT LIKE '~%'", false, "File: ".__FILE__."<br>Line: ".__LINE__))
		{
			self::_clear_cache(true, true);
			return true;
		}
		return false;
	}

	public static function DeleteUsersOptions($user_id=false)
	{
		global $DB;
		if ($DB->Query("DELETE FROM b_user_option WHERE USER_ID IS NOT NULL AND NAME NOT LIKE '~%'  ".($user_id <> false? " AND USER_ID=".intval($user_id):""), false, "File: ".__FILE__."<br>Line: ".__LINE__))
		{
			self::_clear_cache(true, ($user_id <> false? true: false), $user_id);
			return true;
		}
		return false;
	}

	public static function SetCookieOptions($cookieName)
	{
		//last user setting
		$varCookie = array();
		parse_str($_COOKIE[$cookieName], $varCookie);
		setcookie($cookieName, false, false, "/");
		if (is_array($varCookie["p"]) && $varCookie["sessid"] == bitrix_sessid())
		{
			$arOptions = $varCookie["p"];
			CUtil::decodeURIComponent($arOptions);
			CUserOptions::SetOptionsFromArray($arOptions);
		}
	}

	//*****************************
	// Events
	//*****************************

	//user deletion event
	public static function OnUserDelete($user_id)
	{
		global $DB;
		$user_id = intval($user_id);

		if ($DB->Query("DELETE FROM b_user_option WHERE USER_ID=". $user_id, false, "File: ".__FILE__."<br>Line: ".__LINE__))
		{
			self::_clear_cache(true, false, $user_id);
			return true;
		}
		return false;
	}

	protected static function _clear_cache($category = false, $bCommon = false, $user_id = 0)
	{
		global $CACHE_MANAGER;

		self::$__USER_OPTIONS_CACHE = array();
		self::$__USER_OPTIONS_DB = array();
		self::$__USER_OPTIONS_MC = array();

		if ($category !== false)
		{
			if ($category !== 'admin_menu' && $category !== 'favorite')
			{
				if ($bCommon)
					$CACHE_MANAGER->cleanDir("user_option");
				else
					$CACHE_MANAGER->clean("user_option:$user_id", "user_option");
			}
		}
	}
}

class CBXFavAdmMenu
{
	private $arItems;

	public function __construct()
	{
		$this->Init();
	}

	private function Init()
	{
		global $USER,$adminPage,$adminMenu;

		//for ajax requests, and menu autoupdates
		$adminPage->Init();
		$adminMenu->Init($adminPage->aModules);

		$dbFav = CFavorites::GetList(
			array(
				"COMMON" => "ASC",
				"SORT" => "ASC",
				"NAME" => "ASC",
			), array(
				"MENU_FOR_USER" => $GLOBALS["USER"]->GetID(),
				"LANGUAGE_ID" => LANGUAGE_ID,
			)
		);

		while ($arFav = $dbFav->GetNext())
				$this->arItems[] = $arFav;

		return true;
	}

	public function GetMenuItem($itemsID, $arMenu)
	{
		if(!is_array($arMenu))
			return;

		foreach ($arMenu as $arItem)
		{
			if( isset($arItem["items_id"]) && $arItem["items_id"] == $itemsID)
				return $arItem;

			else
				if(is_array($arItem) && !empty($arItem))
				{
					$arFindItem = $this->GetMenuItem($itemsID, $arItem);

					if(is_array($arFindItem) && !empty($arFindItem))
						return $arFindItem;
				}
		}

		return false;
	}

	public function GenerateItems()
	{
		global $adminMenu,$APPLICATION;

		$favOptions = CUserOptions::GetOption('favorite', 'favorite_menu', array("stick" => "N"));

		$aMenu = array();

		if(!empty($this->arItems))
			foreach ($this->arItems as $arItem)
			{
				$tmpMenu = array();

				if($arItem["MENU_ID"])
					$tmpMenu = $this->GetMenuItem($arItem["MENU_ID"], $adminMenu->aGlobalMenu);

				if(!$arItem["MENU_ID"] || !is_array($tmpMenu) || empty($tmpMenu))
				{
					$tmpMenu =
						array(
							"text" => $arItem["NAME"],
							"url" => $arItem["URL"],
							"dynamic" => false,
							"items_id" => "menu_favorite_".$arItem["ID"],
							"title" => $arItem["NAME"],
							"icon" => "fav_menu_icon",
							"page_icon" => "fav_page_icon"
						);
				}

				if(is_array($tmpMenu))
				{
					$tmpMenu["fav_id"] = $arItem["ID"];
					$tmpMenu["parent_menu"] = "global_menu_desktop";

					if (!isset($tmpMenu['icon']) || strlen($tmpMenu['icon']) <= 0)
						$tmpMenu['icon'] = 'fav_menu_icon';

					//if(isset($GLOBALS["BX_FAVORITE_MENU_ACTIVE_ID"]) && $tmpMenu["_active"] == true)
					//	unset($tmpMenu["_active"]);

					if($this->CheckItemActivity($tmpMenu))
						$tmpMenu["_active"] = true;

					if(($tmpMenu["_active"] || $this->CheckSubItemActivity($tmpMenu)) && $favOptions["stick"] == "Y")
						$GLOBALS["BX_FAVORITE_MENU_ACTIVE_ID"] = true;

					$aMenu[] = $tmpMenu;
				}
			}

		return $aMenu;
	}

	private function CheckSubItemActivity($arMenu)
	{
		if(!isset($arMenu["items"]) || !is_array($arMenu["items"]))
			return false;

		foreach ($arMenu["items"] as $menu)
		{
			if(isset($menu["_active"]) && isset($menu["_active"]) == true)
				return true;

			if($this->CheckSubItemActivity($menu))
				return true;
		}

		return false;
	}

	private function CheckItemActivity($arMenu)
	{
		//if(isset($GLOBALS["BX_FAVORITE_MENU_ACTIVE_ID"]))
		//	return false;

		if($arMenu["_active"] == true )
			return true;

		global $adminMenu, $APPLICATION;

		if(empty($adminMenu->aActiveSections))
			return false;

		$currentUrl = $APPLICATION->GetCurPageParam();
		$menuUrl = htmlspecialcharsback($arMenu["url"]);

		if(CBXFavUrls::Compare($menuUrl, $currentUrl))
			return true;

		$activeSectUrl = htmlspecialcharsback($adminMenu->aActiveSections["_active"]["url"]);

		if(CBXFavUrls::Compare($menuUrl, $activeSectUrl))
			return true;

		return $this->CheckFilterActivity($currentUrl, $menuUrl, $activeSectUrl);
	}

	private function CheckFilterActivity($currentUrl, $menuUrl, $activeSectUrl)
	{
		if(!CBXFavUrls::Compare($menuUrl, $activeSectUrl))
			return false;

		$curUrlFilterId = CBXFavUrls::GetFilterId($currentUrl);

		if($curUrlFilterId == CBXFavUrls::GetFilterId($menuUrl))
			return true;

		if($curUrlFilterId && $curUrlFilterId == CBXFavUrls::GetPresetId($menuUrl))
			return true;

		if(CBXFavUrls::GetPresetId($currentUrl) && CBXFavUrls::GetFilterId($menuUrl) == CBXFavUrls::GetPresetId($currentUrl))
			return true;

		return false;
	}

	public function GenerateMenuHTML($id = 0)
	{
		global $adminMenu;
		$buff = "";

		$menuItems = $this->GenerateItems();

		if(empty($menuItems))
			$buff.= self::GetEmptyMenuHTML();
		else
		{
			ob_start();

			echo '<script type="text/javascript" bxrunfirst="true">BX.adminFav.setLastId('.intval($id).');</script>';

			$menuScripts = '';
			foreach ($menuItems as $arItem)
				$menuScripts .= $adminMenu->Show($arItem);

			echo '<script type="text/javascript">'.$menuScripts.'</script>';

			$buff .= ob_get_contents();
			ob_end_clean();
		}

		$buff.= self::GetMenuHintHTML(empty($menuItems));

		return $buff;
	}

	public static function GetEmptyMenuHTML()
	{
		return '
<div class="adm-favorites-cap-text">
	'.GetMessage("fav_main_menu_nothing").'
</div>';
	}

	public static function GetMenuHintHTML($IsMenuEmpty)
	{
		$favHintOptions = CUserOptions::GetOption('favorites_menu', "hint", array("hide" => "N"));

		if(!$IsMenuEmpty && $favHintOptions["hide"] == "Y")
			return false;

		$retHtml = '
<div id="adm-favorites-cap-hint-block" class="adm-favorites-cap-hint-block">
	<div class="adm-favorites-cap-hint-icon icon-1"></div>
	<div class="adm-favorites-cap-hint-text">
		'.GetMessage("fav_main_menu_add_icon").'
	</div>
	<div class="adm-favorites-cap-hint-icon icon-2"></div>
	<div class="adm-favorites-cap-hint-text">
		'.GetMessage("fav_main_menu_add_dd").'
	</div>';


		if(!$IsMenuEmpty)
			$retHtml .='
	<a class="adm-favorites-cap-remove" href="javascript:void(0);" onclick="BX.adminFav.closeHint(this);">'.GetMessage("fav_main_menu_close_hint").'</a>';

		$retHtml .= '
</div>';

		return $retHtml;

	}
}

class CBXFavUrls
{
	const FILTER_ID_VALUE = "adm_filter_applied";
	const PRESET_ID_VALUE = "adm_filter_preset";

	public function Compare($url1, $url2, $arReqVals=array(), $arSkipVals=array())
	{
		if($url1=='' && $url2 == '')
			return false;

		if(is_array($url1))
			$arUrl1 = $url1;
		elseif(is_string($url1))
			$arUrl1 = self::ParseDetail($url1);
		else
			return false;

		$arUrl2 = self::ParseDetail($url2);

		if(isset($arUrl1["path"]) && isset($arUrl2["path"]) && $arUrl1["path"] != $arUrl2["path"])
		{
			$urlPath1 = pathinfo($arUrl1["path"]);
			$urlPath2 = pathinfo($arUrl2["path"]);

			if(
				isset($urlPath1["dirname"])
				&& $urlPath1["dirname"] != '.'
				&& isset($urlPath2["dirname"])
				&& $urlPath2["dirname"] != '.'
				&& $urlPath1["dirname"] != $urlPath2["dirname"]
			)
				return false;

			if(isset($urlPath1["basename"]) && isset($urlPath2["basename"]) && $urlPath1["basename"] != $urlPath2["basename"])
				return false;
		}

		if(isset($arUrl1["host"]) && isset($arUrl2["host"]) && $arUrl1["host"]!=$arUrl2["host"])
			return false;

		if(isset($arUrl1["query"]) && isset($arUrl2["query"]) && $arUrl1["query"] == $arUrl2["query"])
			return true;

		if(is_array($arUrl1["ar_query"]) && is_array($arUrl2["ar_query"]))
		{
			foreach ($arUrl1["ar_query"] as $valName => $value)
			{
				if($arUrl1["ar_query"][$valName] != $arUrl2["ar_query"][$valName])
				{
					if(!empty($arReqVals))
					{
						if(in_array($valName,$arReqVals))
							return false;

						continue;
					}
					if(!empty($arSkipVals))
					{
						if(in_array($valName,$arSkipVals))
							continue;

						return false;
					}

					return false;
				}
			}

			if(!empty($arReqVals))
			{
				foreach ($arReqVals as $valName => $value)
				{
					if(isset($arUrl2["ar_query"][$valName]))
					{
						if(!isset($arUrl1["ar_query"][$valName]))
							return false;

						if($arUrl1["ar_query"][$valName] != $arUrl2["ar_query"][$valName])
							return false;
					}
				}

			}
		}

		return true;
	}

	public function ParseDetail($url)
	{
		$parts = parse_url($url);

		if(isset($parts['query']))
			parse_str(urldecode($parts['query']), $parts['ar_query']);

		return $parts;
	}

	public function GetFilterId($url)
	{
		$urlParams = self::ParseDetail($url);

		if(isset($urlParams["ar_query"][self::FILTER_ID_VALUE]) && $urlParams["ar_query"][self::FILTER_ID_VALUE]!="")
			return $urlParams["ar_query"][self::FILTER_ID_VALUE];

		return false;
	}

	public function GetPresetId($url)
	{
		$urlParams = self::ParseDetail($url);

		if(isset($urlParams["ar_query"][self::PRESET_ID_VALUE]) && $urlParams["ar_query"][self::PRESET_ID_VALUE]!="")
			return $urlParams["ar_query"][self::PRESET_ID_VALUE];

		return false;
	}
}
?>