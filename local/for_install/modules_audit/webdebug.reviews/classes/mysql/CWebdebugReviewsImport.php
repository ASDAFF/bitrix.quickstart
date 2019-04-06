<?
class CWebdebugReviewsImport {
	const TableName = 'b_webdebug_reviews';
	
	/**
	 *	Get microtime
	 */
	function GetMicroTime() {
		list($usec, $sec) = explode(' ', microtime()); 
		return ((float)$usec + (float)$sec); 
	}
	
	/**
	 *	Get reviews count with conditions
	 */
	function GetReviewsCount($arData) {
		global $DB;
		$Mode = $arData['MODE'];
		$IBlockID = $arData['IBLOCK_ID'];
		$strFilter = '';
		if ($Mode=='IBLOCK_ONLY') {
			$strFilter = " WHERE `IBLOCK_ID`='{$IBlockID}'";
		} elseif ($Mode=='IBLOCK_SKIP') {
			$strFilter = " WHERE `IBLOCK_ID`<>'{$IBlockID}'";
		} elseif ($Mode=='ALL') {
			$strFilter = '';
		} else {
			return false;
		}
		$TableName = self::TableName;
		$SQL = "SELECT COUNT(`ID`) as `COUNT` FROM `{$TableName}`{$strFilter}";
		$resCount = $DB->Query($SQL);
		if ($arCount = $resCount->GetNext(false,false)) {
			return $arCount['COUNT'];
		}
		return false;
	}
	
	/**
	 *	Get next import element
	 */
	function GetNext($LastID, $arData) {
		$strFilter = '';
		$Mode = $arData['MODE'];
		$IBlockID = $arData['IBLOCK_ID'];
		if ($Mode=='IBLOCK_ONLY') {
			$strFilter = " AND `IBLOCK_ID`='{$IBlockID}'";
		} elseif ($Mode=='IBLOCK_SKIP') {
			$strFilter = " AND `IBLOCK_ID`<>'{$IBlockID}'";
		} elseif ($Mode=='ALL') {
			$strFilter = '';
		} else {
			return false;
		}
		global $DB;
		$LastID = IntVal($LastID);
		$TableName = self::TableName;
		$DateTime = $DB->DateToCharFunction('`DATETIME`').' `DATETIME`';
		$SQL = "SELECT *,{$DateTime}  FROM `{$TableName}` WHERE `ID`>'{$LastID}'{$strFilter} ORDER BY `ID` ASC LIMIT 1;";
		$resItem = $DB->Query($SQL);
		if ($arItem = $resItem->GetNext(false,false)) {
			return $arItem;
		}
		return false;
	}
	
	/**
	 *	Replace &nbsp; to quotes (")
	 */
	function ReplaceQuotes($Text) {
		$Text = str_replace('&nbsp;','"',$Text);
		return $Text;
	}
	
	/**
	 *	Build array for review add
	 */
	function BuildArray($arItem, $arData) {
		$arResult = array();
		$arResult['MODERATED'] = $arItem['MODERATED'];
		$arResult['DATE_CREATED'] = $arItem['DATETIME'];
		$arResult['TARGET'] = 'E_'.$arItem['ELEMENT_ID'];
		$arResult['USER_ID'] = $arItem['USER_ID'];
		$arResult['INTERFACE_ID'] = $arData['INTERFACE_ID'];
		$arResult['EXTERNAL_ID'] = $arItem['ID'];
		$arResult['FIELDS'] = array();
		if (is_array($arData['FIELDS'])) {
			foreach($arData['FIELDS'] as $Key => $Value) {
				$arResult['FIELDS'][$Key] = self::ReplaceQuotes($arItem[$Value]);
			}
		}
		if (is_array($arData['RATINGS'])) {
			foreach($arData['RATINGS'] as $Key => $Value) {
				$arResult['RATINGS'][$Key] = $arItem['VOTE_'.$Value];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Save review
	 */
	function AddReview($arFields) {
		if (!is_object($GLOBALS['WD_REVIEWS2_REVIEWS_OBJECT'])) {
			$GLOBALS['WD_REVIEWS2_REVIEWS_OBJECT'] = new CWD_Reviews2_Reviews;
		}
		if (self::ReviewAlreadyImported($arFields['EXTERNAL_ID'])) {
			return true;
		}
		return $GLOBALS['WD_REVIEWS2_REVIEWS_OBJECT']->Add($arFields);
	}
	
	/**
	 *	Check if review already imported
	 */
	function ReviewAlreadyImported($OldItemID) {
		global $DB;
		$TableName = 'b_wd_reviews2_reviews';
		$SQL = "SELECT `ID` FROM `{$TableName}` WHERE `EXTERNAL_ID`='{$OldItemID}' LIMIT 1;";
		$resExists = $DB->Query($SQL);
		if ($arExists = $resExists->GetNext(false,false)) {
			return true;
		}
		return false;
	}
	
}
?>