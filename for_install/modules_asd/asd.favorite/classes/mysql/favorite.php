<?
IncludeModuleLangFile(__FILE__);

class CASDfavorite {

	const mysqlError = 'MySQL Error in CASDfavorite on line: ';

	public static function AddFolder($arFields) {
		$codeOrig = trim($arFields['CODE']);
		if ($arFields['USER_ID'] <= 0)
			$arFields['USER_ID'] = $GLOBALS['USER']->GetID();
		$arFields = array(
			'NAME' => strlen(trim($arFields['NAME'])) > 0 ? '"' . $GLOBALS['DB']->ForSQL(trim($arFields['NAME']), 255) . '"' : '',
			'CODE' => strlen(trim($arFields['CODE'])) > 0 ? '"' . $GLOBALS['DB']->ForSQL(trim($arFields['CODE']), 50) . '"' : '"unknown"',
			'USER_ID' => intval($arFields['USER_ID']),
			'DEFAULT' => $arFields['DEFAULT'] == 'Y' ? "'Y'" : "'N'",
		);
		if (!strlen($arFields['NAME']) || !$arFields['USER_ID'])
			return false;
		else {
			if (!self::GetType($codeOrig)->Fetch())
				self::AddType(array('CODE' => $codeOrig, 'NAME' => $codeOrig));
			$ID = $GLOBALS['DB']->Insert('b_asd_favorite_folders', $arFields, self::mysqlError.__LINE__);
			return $ID;
		}
	}

	public static function UpdateFolder($ID, $arFields, $checkRights = true) {
		if ($checkRights && !$GLOBALS['USER']->IsAuthorized())
			return false;
		if ($checkRights && $GLOBALS['USER']->IsAdmin())
			$checkRights = false;
		if ($ID <= 0)
			return false;
		$arFields = array(
			'NAME' => strlen(trim($arFields['NAME'])) > 0 ? '"' . $GLOBALS['DB']->ForSQL(trim($arFields['NAME']), 255) . '"' : '',
		);
		if (!strlen($arFields['NAME']))
			return false;
		$GLOBALS['DB']->Update('b_asd_favorite_folders', $arFields, 'WHERE ID=' . intval($ID) .
				($checkRights ? " AND USER_ID=" . intval($GLOBALS['USER']->GetID()) : ""), self::mysqlError.__LINE__);
	}

	public static function DeleteFolder($ID, $checkRights = true) {
		if ($checkRights && !$GLOBALS['USER']->IsAuthorized())
			return false;
		if ($checkRights && $GLOBALS['USER']->IsAdmin())
			$checkRights = false;
		$ID = intval($ID);
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_folders WHERE ID=$ID" . ($checkRights ? " AND USER_ID=" . intval($GLOBALS['USER']->GetID()) : ""));
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_likes WHERE FOLDER_ID=$ID;");
	}

	public static function SetFolderDefault($UID, $CODE, $ID) {
		$UID = intval($UID);
		$ID = intval($ID);
		$CODE = $GLOBALS['DB']->ForSQL(trim($CODE));
		$GLOBALS['DB']->Query("UPDATE b_asd_favorite_folders SET `DEFAULT`='N' WHERE USER_ID=$UID AND CODE='$CODE';");
		$GLOBALS['DB']->Query("UPDATE b_asd_favorite_folders SET `DEFAULT`='Y' WHERE ID=$ID AND CODE='$CODE';");
	}

	public static function GetFolders($CODE, $UID = false) {
		static $arFolders = array();
		if (!empty($arFolders))
			return $arFolders;

		if ($UID === false)
			$UID = $GLOBALS['USER']->GetID();
		$UID = intval($UID);
		$CODE = $GLOBALS['DB']->ForSQL(trim($CODE));

		$rs = $GLOBALS['DB']->Query("SELECT * FROM b_asd_favorite_folders WHERE USER_ID=$UID AND CODE='$CODE' ORDER BY ID ASC;");
		while ($ar = $rs->GetNext())
			$arFolders[$ar['ID']] = $ar;

		return $arFolders;
	}

	public static function AddType($arFields) {
		$arRef = $arFields['REF'];
		$CODE = $arFields['CODE'];
		$arFields['MODULE'] = trim($arFields['MODULE']);
		if ($arFields['MODULE'] != 'forum' && $arFields['MODULE'] != 'blog')
			$arFields['MODULE'] = 'iblock';
		$arFields = array(
			'NAME' => strlen(trim($arFields['NAME'])) > 0 ? '"' . $GLOBALS['DB']->ForSQL(trim($arFields['NAME']), 255) . '"' : '',
			'CODE' => strlen(trim($arFields['CODE'])) > 0 ? '"' . $GLOBALS['DB']->ForSQL(trim($arFields['CODE']), 50) . '"' : '',
			'MODULE' => "'" . $arFields['MODULE'] . "'",
		);
		if (!strlen($arFields['NAME']) || !strlen($arFields['CODE']))
			return false;
		else {
			$ID = $GLOBALS['DB']->Insert('b_asd_favorite_types', $arFields, self::mysqlError.__LINE__);
			self::MakeRefType($CODE, $arRef);
			return $ID;
		}
	}

	public static function UpdateType($CODE, $arFields) {
		$CODE = $GLOBALS['DB']->ForSQL(trim($CODE));
		$arRef = $arFields['REF'];
		$arFields = array(
			'NAME' => strlen(trim($arFields['NAME'])) > 0 ? '"' . $GLOBALS['DB']->ForSQL(trim($arFields['NAME']), 255) . '"' : '',
		);
		if (!strlen($CODE) || !strlen($arFields['NAME']))
			return false;
		else {
			$GLOBALS['DB']->Update('b_asd_favorite_types', $arFields, "WHERE CODE='" . $CODE . "'", self::mysqlError.__LINE__);
			self::MakeRefType($CODE, $arRef);
		}
	}

	public static function MakeRefType($CODE, $arRef) {
		if (strlen(trim($CODE)) && is_array($arRef)) {
			$CODE = $GLOBALS['DB']->ForSQL(trim($CODE));
			$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_types_ref WHERE CODE='$CODE';");
			foreach ($arRef as $ref) {
				$GLOBALS['DB']->Insert('b_asd_favorite_types_ref', array(
																'CODE' => '"'.$CODE.'"',
																'REF' => '"'.$GLOBALS['DB']->ForSQL(trim($ref)).'"'), self::mysqlError.__LINE__);
			}
		}
	}

	public static function GetRefType($CODE) {
		if (strlen(trim($CODE))) {
			$CODE = $GLOBALS['DB']->ForSQL(trim($CODE));
			$arRefs = array();
			$rsRef = $GLOBALS['DB']->Query("SELECT * FROM b_asd_favorite_types_ref WHERE CODE='$CODE';");
			while ($arRef = $rsRef->Fetch()) {
				$arRefs[] = $arRef['REF'];
			}
			return $arRefs;
		}
	}

	public static function GetTypesByRef($REF) {
		if (strlen(trim($REF))) {
			return $GLOBALS['DB']->Query("SELECT * FROM b_asd_favorite_types_ref WHERE REF='".$GLOBALS['DB']->ForSQL(trim($REF))."';");
		}
	}

	public static function DeleteType($CODE) {
		$CODE = $GLOBALS['DB']->ForSQL(trim($CODE));
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_types WHERE CODE='$CODE';");
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_folders WHERE CODE='$CODE';");
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_likes WHERE CODE='$CODE';");
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_types_ref WHERE CODE='$CODE';");
	}

	public static function GetTypes($arSort = array('CODE' => 'ASC'), $arFilter = array()) {
		list($by, $order) = each($arSort);
		$by = strtoupper($by);
		$order = strtoupper($order);
		if ($by != 'CODE' && $by != 'MODULE')
			$by = 'NAME';
		if ($order != 'DESC')
			$order = 'ASC';
		$strWhere = "";
		if (isset($arFilter['CODE']))
			$strWhere .= " AND CODE='" . $GLOBALS['DB']->ForSQL(trim($arFilter['CODE'])) . "'";
		if (strlen($strWhere) > 0)
			$strWhere = "WHERE 1=1 " . $strWhere;
		return $GLOBALS['DB']->Query("SELECT * FROM b_asd_favorite_types $strWhere ORDER BY $by $order;");
	}

	public static function GetType($CODE) {
		return self::GetTypes(array('CODE' => 'ASC'), array('CODE' => $CODE));
	}

	public static function GetLikes($arFilter = array(), $groupBy = false) {
		$strSelect = "*";
		$strWhere = "";
		if (isset($arFilter['ELEMENT_ID'])) {
			if (is_array($arFilter['ELEMENT_ID'])) {
				$strWhere .= " AND ELEMENT_ID IN (" . $GLOBALS['DB']->ForSQL(implode(', ', $arFilter['ELEMENT_ID'])) . ")";
			} else {
				$strWhere .= " AND ELEMENT_ID='" . intval($arFilter['ELEMENT_ID']) . "'";
			}
		}
		if (isset($arFilter['FOLDER_ID']) && is_array($arFilter['FOLDER_ID']) && !empty($arFilter['FOLDER_ID']))
			$strWhere .= " AND FOLDER_ID IN (" . $GLOBALS['DB']->ForSQL(implode(',', $arFilter['FOLDER_ID'])) . ")";
		elseif (isset($arFilter['FOLDER_ID']))
			$strWhere .= " AND FOLDER_ID='" . intval($arFilter['FOLDER_ID']) . "'";
		if (isset($arFilter['USER_ID']))
			$strWhere .= " AND USER_ID='" . intval($arFilter['USER_ID']) . "'";
		if (isset($arFilter['CODE']))
			$strWhere .= " AND CODE='" . $GLOBALS['DB']->ForSQL(trim($arFilter['CODE'])) . "'";
		if (strlen($strWhere) > 0)
			$strWhere = "WHERE 1=1 " . $strWhere;
		$strGroup = "";
		if ($groupBy !== false) {
			$groupBy = $GLOBALS['DB']->ForSQL($groupBy);
			$strGroup = " GROUP BY " . $groupBy;
			$strSelect = "$groupBy, COUNT($groupBy) as CNT";
		}
		return $GLOBALS['DB']->Query("SELECT $strSelect FROM b_asd_favorite_likes $strWhere $strGroup ORDER BY ID DESC ;");
	}

	public static function GetLikesAndFavedByElementID($arIDs = array()) {
		if (empty($arIDs) || !is_array($arIDs)) {
			return array();
		}

		$arResult = array();
		$rsList = self::GetLikes(array('ELEMENT_ID' => $arIDs), 'ELEMENT_ID');

		while ($arItem = $rsList->GetNext()) {
			$arResult[$arItem['ELEMENT_ID']] = array(
				'COUNT' => $arItem['CNT'],
				'FAVED' => 'N'
			);
		}

		if ($GLOBALS['USER']->IsAuthorized()) {
			$rsList = self::GetLikes(array('ELEMENT_ID' => $arIDs, 'USER_ID' => $GLOBALS['USER']->GetID()));
			while ($arLike = $rsList->GetNext()) {
				$arResult[$arLike['ELEMENT_ID']]['FAVED'] = 'Y';
			}
		}

		return $arResult;
	}

	public static function Like($ID, $CODE, $UID = false) {
		$ID = intval($ID);
		$CODE = trim($CODE);
		if ($UID === false)
			$UID = $GLOBALS['USER']->GetID();
		$UID = intval($UID);
		if ($ID <= 0 || !strlen($CODE) || $UID <= 0)
			return false;
		if (self::GetLikes(array('ELEMENT_ID' => $ID, 'CODE' => $CODE, 'USER_ID' => $UID))->Fetch())
			return false;

		if ($arDef = $GLOBALS['DB']->Query("SELECT ID FROM b_asd_favorite_folders WHERE USER_ID=$UID AND CODE='$CODE' AND `DEFAULT`='Y';")->Fetch())
			$FOLDER_ID = $arDef['ID'];
		else
			$FOLDER_ID = self::AddFolder(array('NAME' => GetMessage('ASD_CORE_FAVORITE'), 'CODE' => $CODE, 'USER_ID' => $UID, 'DEFAULT' => 'Y'));

		if (!self::GetType($CODE)->Fetch())
			self::AddType(array('CODE' => $CODE, 'NAME' => $CODE));

		$arFields = array(
			'ELEMENT_ID' => $ID,
			'FOLDER_ID' => intval($FOLDER_ID),
			'USER_ID' => $UID,
			'CODE' => '"' . $GLOBALS['DB']->ForSQL($CODE) . '"',
		);
		$GLOBALS['DB']->Insert('b_asd_favorite_likes', $arFields, self::mysqlError.__LINE__);
	}

	public static function UnLike($ID, $CODE, $UID = false) {
		$ID = intval($ID);
		$CODE = trim($CODE);
		if ($UID === false)
			$UID = $GLOBALS['USER']->GetID();
		$UID = intval($UID);
		if ($ID <= 0 || !strlen($CODE) || $UID <= 0)
			return false;
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_likes WHERE ELEMENT_ID=$ID AND USER_ID=$UID AND CODE='" . $GLOBALS['DB']->ForSQL($CODE) . "';");
	}

	public static function MoveLike($ID, $CODE, $NEW_FOLDER_ID) {
		$ID = intval($ID);
		$CODE = trim($CODE);
		$UID = intval($GLOBALS['USER']->GetID());
		$NEW_FOLDER_ID = intval($NEW_FOLDER_ID);
		if ($ID <= 0 || !strlen($CODE) || $UID <= 0 || $NEW_FOLDER_ID <= 0)
			return false;
		$GLOBALS['DB']->Query("UPDATE b_asd_favorite_likes SET FOLDER_ID=$NEW_FOLDER_ID
							WHERE ELEMENT_ID=$ID AND USER_ID=$UID AND CODE='" . $GLOBALS['DB']->ForSQL($CODE) . "';");
	}

	public static function OnUserDeleteHandler($USER_ID) {
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_folders WHERE USER_ID=" . intval($USER_ID));
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_likes WHERE USER_ID=" . intval($USER_ID));
	}

	public static function OnBlogPostDeleteHandler($ID, &$result) {
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_likes
								WHERE CODE IN (SELECT CODE FROM b_asd_favorite_types WHERE MODULE='blog')
								AND ELEMENT_ID=" . intval($ID));
	}

	public static function OnIBlockElementDeleteHandler($ID) {
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_likes
								WHERE CODE IN (SELECT CODE FROM b_asd_favorite_types WHERE MODULE='iblock')
								AND ELEMENT_ID=" . intval($ID));
	}

	public static function OnAfterTopicDeleteHandler($ID, $arTopic) {
		$GLOBALS['DB']->Query("DELETE FROM b_asd_favorite_likes
								WHERE CODE IN (SELECT CODE FROM b_asd_favorite_types WHERE MODULE='forum')
								AND ELEMENT_ID=" . intval($ID));
	}

	public static function OnAddRatingVoteHandler($ID, $arParam) {
		if ($arParam['VALUE'] < 0)
			return;
		if ($arParam['ENTITY_TYPE_ID']=='IBLOCK_ELEMENT' && CModule::IncludeModule('iblock')) {
			if ($arElement = CIBlockElement::GetByID($arParam['ENTITY_ID'])->Fetch()) {
				$rsTypes = self::GetTypesByRef('iblock_'.$arElement['IBLOCK_ID']);
				while ($arTypes = $rsTypes->Fetch())
					self::Like($arParam['ENTITY_ID'], $arTypes['CODE']);
			}
		}
		if ($arParam['ENTITY_TYPE_ID']=='BLOG_POST') {
			$rsTypes = self::GetTypesByRef('blog');
			while ($arTypes = $rsTypes->Fetch())
				self::Like($arParam['ENTITY_ID'], $arTypes['CODE']);
		}
	}

}
?>