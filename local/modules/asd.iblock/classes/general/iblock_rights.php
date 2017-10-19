<?php

class CASDIblockRights {

	static protected $boolCheck = false;
	static protected $boolExtRights = false;

	public static function GetCheck() {
		return self::$boolCheck;
	}

	public static function GetExtRights() {
		return self::$boolExtRights;
	}

	public static function CheckExtRights() {
		if (!self::$boolCheck) {
			$iblockVersion = CASDiblockVersion::getIblockVersion();
			if (!empty($iblockVersion)) {
				self::$boolExtRights = version_compare($iblockVersion, '11.0.5', '>=');
				self::$boolCheck = true;
			}
		}
	}

	public static function CheckIBlockOperation($intIBlockID, $strOperation, $strAccess) {
		$intIBlockID = intval($intIBlockID);
		if ($intIBlockID <= 0) {
			return false;
		}
		if (!self::$boolCheck) {
			self::CheckExtRights();
		}
		if (self::$boolExtRights) {
			return CIBlockRights::UserHasRightTo($intIBlockID, $intIBlockID, $strOperation);
		} else {
			return (CIBlock::GetPermission($intIBlockID) >= $strAccess);
		}
	}

	public static function CheckSectionOperation($intIBlockID, $intSectionID, $strOperation, $strAccess) {
		$intIBlockID = intval($intIBlockID);
		if ($intIBlockID <= 0) {
			return false;
		}
		$intSectionID = intval($intSectionID);
		if ($intSectionID < 0) {
			return false;
		}
		if (!self::$boolCheck) {
			self::CheckExtRights();
		}
		if (self::$boolExtRights) {
			return CIBlockSectionRights::UserHasRightTo($intIBlockID, $intSectionID, $strOperation);
		} else {
			return (CIBlock::GetPermission($intIBlockID) >= $strAccess);
		}
	}

	public static function CheckElementOperation($intIBlockID, $intElementID, $strOperation, $strAccess) {
		$intIBlockID = intval($intIBlockID);
		if ($intIBlockID <= 0) {
			return false;
		}
		$intElementID = intval($intElementID);
		if ($intElementID <= 0) {
			return false;
		}
		if (!self::$boolCheck) {
			self::CheckExtRights();
		}
		if (self::$boolExtRights) {
			return CIBlockElementRights::UserHasRightTo($intIBlockID, $intElementID, $strOperation);
		} else {
			return (CIBlock::GetPermission($intIBlockID) >= $strAccess);
		}
	}

	public static function IsIBlockDisplay($intIBlockID) {
		return self::CheckIBlockOperation($intIBlockID, 'iblock_admin_display', 'W');
	}

	public static function IsIBlockEdit($intIBlockID) {
		return self::CheckIBlockOperation($intIBlockID, 'iblock_edit', 'X');
	}

	public static function IsIBlockElementCreate($intIBlockID) {
		return self::CheckIBlockOperation($intIBlockID, 'section_element_bind', 'W');
	}

	public static function IsIBlockElementDelete($intIBlockID) {
		return self::CheckIBlockOperation($intIBlockID, 'element_delete', 'W');
	}

	public static function IsIBlockElementEdit($intIBlockID) {
		return self::CheckIBlockOperation($intIBlockID, 'element_edit', 'W');
	}

	public static function IsIBlockSectionCreate($intIBlockID) {
		return self::CheckIBlockOperation($intIBlockID, 'section_section_bind', 'W');
	}

	public static function IsIBlockSectionDelete($intIBlockID) {
		return self::CheckIBlockOperation($intIBlockID, 'section_delete', 'W');
	}

	public static function IsSectionElementCreate($intIBlockID, $intSectionID) {
		return self::CheckSectionOperation($intIBlockID, $intSectionID, 'section_element_bind', 'W');
	}

	public static function IsSectionElementDelete($intIBlockID, $intSectionID) {
		return self::CheckSectionOperation($intIBlockID, $intSectionID, 'element_delete', 'W');
	}

	public static function IsSectionElementEdit($intIBlockID, $intSectionID) {
		return self::CheckSectionOperation($intIBlockID, $intSectionID, 'element_edit', 'W');
	}

	public static function IsSectionSectionCreate($intIBlockID, $intSectionID) {
		return self::CheckSectionOperation($intIBlockID, $intSectionID, 'section_section_bind', 'W');
	}

	public static function IsElementEdit($intIBlockID, $intElementID) {
		return self::CheckElementOperation($intIBlockID, $intElementID, 'element_edit', 'W');
	}

	public static function IsElementDelete($intIBlockID, $intElementID) {
		return self::CheckElementOperation($intIBlockID, $intElementID, 'element_delete', 'W');
	}

	public static function IsElementEditPrice($intIBlockID, $intElementID) {
		return self::CheckElementOperation($intIBlockID, $intElementID, 'element_edit_price', 'W');
	}

}