<?
IncludeModuleLangFile(__FILE__);

class CWD_Reviews2_Reviews {
	const TableName = 'b_wd_reviews2_reviews';
	public $arLastErrors = array();
	
	/**
	 *	Format date from site format to MySQL format
	 */
	function DateFormatToMySQL($Date) {
		$mResult = CDatabase::FormatDate($Date, FORMAT_DATETIME, 'YYYY-MM-DD HH:MI:SS');
		if ($mResult===false) {
			$mResult = '0000-00-00 00:00:00';
		}
		return $mResult;
	}
	
	/**
	 *	Add
	 */
	function Add($arFields, $SkipNotice=false) {
		global $DB, $USER;
		$SkipCheck = $arFields['SKIP_CHECK']=='Y';
		if (!$SkipCheck) {
			self::CheckRequiredField(false, $arFields, true);
		}
		$arFields = self::Serialize($arFields);
		$arFields = self::RemoveWrongFields($arFields);
		$Now =  date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
		if (!isset($arFields['DATE_CREATED']) || trim($arFields['DATE_CREATED'])=='') {
			$arFields['DATE_CREATED'] = $Now;
		}
		if (defined('WDR2_IMPORTING') && IntVal($arFields['USER_ID'])<=0) {
			$arFields['USER_ID'] = '0';
		}
		if (!defined('WDR2_IMPORTING') && (!isset($arFields['USER_ID']) || !is_numeric($arFields['USER_ID']) || $arFields['USER_ID']<=0)) {
			$arFields['USER_ID'] = IntVal($USER->GetID());
		}
		$arFields['DATE_MODIFIED'] = $Now;
		$GLOBALS['WDR2_REVIEW_ADD_SKIP_NOTICE'] = false;
		if ($SkipNotice) {
			$GLOBALS['WDR2_REVIEW_ADD_SKIP_NOTICE'] = true;
		}
		if (empty($this->arLastErrors)) {
			
			foreach(GetModuleEvents(CWD_Reviews2::ModuleID, 'OnBeforeAddReview', true) as $arEvent) {
				if (ExecuteModuleEventEx($arEvent, array(&$this->arLastErrors, &$arFields, $SkipNotice))===false) {
					return false;
				}
			}
			
			$arFields = self::HandleBeforeSave(false, $arFields, 'Add');
			$ID = $DB->Add(self::TableName, $arFields, array(), '', false);
			if ($ID>0) {
				self::HandleSave($ID, $arFields, 'Add');
				return $ID;
			}
		}
		return false;
	}
	
	/**
	 *	Check required fields on save
	 */
	function CheckRequiredField($ID, $arFields, $Add=true) {
		$this->arLastErrors = array();
		if ($Add) {
			if (!isset($arFields['INTERFACE_ID']) || !is_numeric($arFields['INTERFACE_ID'])) {
				$this->arLastErrors[] = GetMessage('WD_REVIEWS2_REVIEW_ERROR_NO_INTERFACE');
			}
			if (!isset($arFields['TARGET']) || trim($arFields['TARGET'])=='') {
				$this->arLastErrors[] = GetMessage('WD_REVIEWS2_REVIEW_ERROR_NO_TARGET');
			}
		}
		
		// Check required addtional fields
		$WD_Reviews2_InterfaceID = false;
		$arReview = false;
		if ($arFields['INTERFACE_ID']>0) {
			$WD_Reviews2_InterfaceID = $arFields['INTERFACE_ID'];
		} elseif ($ID>0) {
			$resReview = self::GetByID($ID);
			if ($arReview = $resReview->GetNext(false,false)) {
				$WD_Reviews2_InterfaceID = $arReview['INTERFACE_ID'];
			}
		}
		if ($WD_Reviews2_InterfaceID>0) {
			$arRequiredFields = self::GetFields($WD_Reviews2_InterfaceID);
			foreach($arRequiredFields as $arRequiredField) {
				if (!$Add && !isset($arFields['FIELDS'][$arRequiredField['CODE']])) {
					continue;
				}
				$CheckFieldError = CWD_Reviews2::CheckFieldError($arRequiredField, $arFields['FIELDS'][$arRequiredField['CODE']]);
				if ($CheckFieldError!==false) {
					$this->arLastErrors[] = $CheckFieldError;
				}
			}
		} elseif(!is_numeric($WD_Reviews2_InterfaceID) || $WD_Reviews2_InterfaceID==0) {
			$this->arLastErrors[] = GetMessage('WD_REVIEWS2_REVIEW_ERROR_NO_INTERFACE_GET');
		}
		
		// Check required ratings
		if ($WD_Reviews2_InterfaceID>0) {
			$resInterface = CWD_Reviews2_Interface::GetByID($WD_Reviews2_InterfaceID);
			if ($arInterface = $resInterface->GetNext(false,false)) {
				if ($arInterface['RATING_IS_REQUIRED']=='Y') {
					$bEmptyRating = false;
					if ($Add) {
						if (is_array($arFields['RATINGS'])) {
							foreach($arFields['RATINGS'] as $arRating) {
								if (!is_numeric($arRating) || $arRating<=0) {
									$bEmptyRating = true;
									break;
								}
							}
						} else {
							$bEmptyRating = true;
						}
					} else {
						if (is_array($arFields['RATINGS'])) {
							foreach($arFields['RATINGS'] as $arRating) {
								if (!is_numeric($arRating) || $arRating<=0) {
									$bEmptyRating = true;
									break;
								}
							}
						} else {
							if ($arReview===false && $ID>0) {
								$resReview = self::GetByID($ID);
								$arReview = $resReview->GetNext();
							}
							$bRatingsExists = true;
							if (strlen($arReview['DATA_RATINGS'])) {
								$arReview['DATA_RATINGS'] = unserialize($arReview['DATA_RATINGS']);
								$bEmptyRating = false;
								foreach($arFields['RATINGS'] as $arRating) {
									if (!is_numeric($arRating) || $arRating<=0) {
										$bEmptyRating = true;
										break;
									}
								}
								if (!$bRatingsExists) {
									$bEmptyRating = false;
								}
							}
						}
					}
					if ($bEmptyRating) {
						$this->arLastErrors[] = GetMessage('WD_REVIEWS2_REVIEW_ERROR_NO_RATINGS');
					}
				}
			}
		}
		
	}
	
	/**
	 *	Get additional fields for interface
	 */
	function GetFields($InterfaceID) {
		$arResult = array();
		$arReviewFields = self::ReviewGetFields(false, $InterfaceID);
		if (is_array($arReviewFields)) {
			foreach($arReviewFields as $arReviewField) {
				$arResult[] = $arReviewField;
			}
		}
		return $arResult;
	}
	
	// Update
	function Update($ID, $arFields) {
		global $DB;
		$SkipCheck = $arFields['SKIP_CHECK']=='Y';
		if (!$SkipCheck) {
			self::CheckRequiredField($ID, $arFields, false);
		}
		$arFields = self::Serialize($arFields);
		$arFields = self::RemoveWrongFields($arFields);
		$Now =  date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
		if (strlen($arFields['DATE_CREATED'])) {
			$arFields['DATE_CREATED'] = self::DateFormatToMySQL($arFields['DATE_CREATED']);
		}
		if (strlen($arFields['DATE_ANSWER'])) {
			$arFields['DATE_ANSWER'] = self::DateFormatToMySQL($arFields['DATE_ANSWER']);
		}
		if (strlen($arFields['DATE_VOTING'])) {
			$arFields['DATE_VOTING'] = self::DateFormatToMySQL($arFields['DATE_VOTING']);
		}
		$arFields['DATE_MODIFIED'] = self::DateFormatToMySQL($Now);
		if (empty($this->arLastErrors)) {
			
			foreach(GetModuleEvents(CWD_Reviews2::ModuleID, 'OnBeforeUpdateReview', true) as $arEvent) {
				if (ExecuteModuleEventEx($arEvent, array(&$this->arLastErrors, $ID, &$arFields))===false) {
					return false;
				}
			}
			if (!$SkipCheck) {
				$arFields = self::HandleBeforeSave($ID, $arFields, 'Update');
			}
			$arSQL = array();
			foreach ($arFields as $Key => $Field) {
				$Key = $DB->ForSQL($Key);
				$Field = $DB->ForSQL($Field);
				$arSQL[] = "`{$Key}`='{$Field}'";
			}
			$strSQL = implode(',',$arSQL);
			$TableName = self::TableName;
			$SQL = "UPDATE `{$TableName}` SET {$strSQL} WHERE `ID`='{$ID}' LIMIT 1;";
			if ($DB->Query($SQL, false)) {
				self::HandleSave($ID,$arFields,'Update');
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Delete
	 */
	function Delete($ID) {
		global $DB;
		$TableName = self::TableName;
		$SQL = "SELECT `ID`,`TARGET`,`INTERFACE_ID`,`DATA_FIELDS` FROM `{$TableName}` WHERE `ID`='{$ID}';";
		if ($resItem = $DB->Query($SQL, false)) {
			if ($arItem = $resItem->GetNext()) {
				$SQL = "DELETE FROM `{$TableName}` WHERE `ID`='{$ID}';";
				if ($DB->Query($SQL, false)) {
					self::HandleDelete($arItem);
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 *	Handler for delete action
	 */
	function HandleDelete($arFields) {
		global $DB;
		// Update interface info
		$obInterface = new CWD_Reviews2_Interface;
		$obInterface->UpdateStatus($arFields['INTERFACE_ID']);
		// Delete values (especially FILE)
		$arFields['DATA_FIELDS'] = unserialize($arFields['~DATA_FIELDS']);
		$arInterfaceFields = self::ReviewGetFields(false, $arFields['INTERFACE_ID']);
		$arTypes = WDR2_GetFieldTypes();
		foreach($arInterfaceFields as $Code => $arReviewField) {
			$Code = $arReviewField['CODE'];
			$Type = $arReviewField['TYPE'];
			$Value = $arFields['DATA_FIELDS'][$Code];
			if ($Type!==false && is_array($arTypes[$Type])) {
				$ClassName = $arTypes[$Type]['CLASS'];
				if (class_exists($ClassName) && method_exists($ClassName, 'DeleteValue')) {
					$ClassName::DeleteValue($Code, $Value);
				}
			}
		}
		// Delete ratings (for 2.0.19)
		$ReviewID = $arFields['ID'];
		if ($ReviewID>0) {
			$SQL = "DELETE FROM `b_wd_reviews2_ratingsvalues` WHERE `REVIEW_ID`='{$ReviewID}';";
			$DB->Query($SQL);
			if (preg_match('#^E_(\d+)$#',$arFields['TARGET'],$M)) {
				$ElementID = $M[1];
				self::UpdateIBlockElementRating($ElementID, $arFields['INTERFACE_ID']);
			}
		}
	}
	
	/**
	 *	Delete by interface id
	 */
	function DeleteByInterface($InterfaceID) {
		global $DB;
		$TableName = self::TableName;
		$SQL = "DELETE FROM `{$TableName}` WHERE `INTERFACE_ID`='{$InterfaceID}';";
		if ($DB->Query($SQL, false)) {
			return true;
		}
		return false;
	}
	
	/**
	 *	Get list
	 */
	function GetList($arSort=false, $arFilter=false, $arGroupBy=false, $arNavParams=false) {
		global $DB;
		$arDateTimeFields = array('DATE_CREATED','DATE_MODIFIED','DATE_ANSWER');
		if (!is_array($arSort)) {
			$arSort = array('ID'=>'DESC');
		}
		foreach ($arSort as $Key => $Value) {
			if (!in_array(ToUpper($Value),array('ASC','DESC'))) {
				unset($arSort[$Key]);
			}
		}
		$arSort = self::RemoveWrongFields($arSort);
		$arFilter = self::RemoveWrongFields($arFilter);
		$arDateTimeSelect = array();
		foreach($arDateTimeFields as $strDateTimeField) {
			$arDateTimeSelect[] = $DB->DateToCharFunction('`'.$strDateTimeField.'`').' `'.$strDateTimeField.'`';
		}
		$strDateTimeSelect = implode(',',$arDateTimeSelect);
		if (!empty($strDateTimeSelect)) {
			$strDateTimeSelect = ','.$strDateTimeSelect;
		}
		$TableName = self::TableName;
		$SQL = "SELECT *{$strDateTimeSelect} FROM `{$TableName}`";
		$arWhere = array();
		// WHERE
		if (is_array($arFilter) && !empty($arFilter)) {
			foreach ($arFilter as $Key => $FilterItem) {
				$Key = trim($Key," \r\n\t");
				$FilterKey = trim($Key,"<>=%!");
				if (!empty($FilterItem) && in_array(ToUpper($FilterKey),$arDateTimeFields)) {
					$FilterItem = self::DateFormatToMySQL($FilterItem);
				}
				$Operation = substr($Key,0,strlen($Key)-strlen($FilterKey));
				$FilterKey = $DB->ForSQL($FilterKey);
				switch($Operation) {
					case '>=':
					case '<=':
					case '<':
					case '>':
						$FilterItem = $DB->ForSQL($FilterItem);
						$arWhere[] = "`{$FilterKey}` {$Operation} '{$FilterItem}'";
						break;
						break;
					case '%':
						if (is_array($FilterItem)) {
							$arSubWhere = array();
							foreach($FilterItem as $Value) {
								$Value = $DB->ForSQL($Value);
								$arSubWhere[] = "(UPPER(`{$FilterKey}`) LIKE UPPER ('%{$Value}%') AND `{$FilterKey}` IS NOT NULL)";
							}
							$strSubWhere = implode(' OR ', $arSubWhere);
							$arWhere[] = "({$strSubWhere})";
						} else {
							$FilterItem = $DB->ForSQL($FilterItem);
							$arWhere[] = "(UPPER(`{$FilterKey}`) LIKE UPPER ('%{$FilterItem}%') AND `{$FilterKey}` IS NOT NULL)";
						}
						break;
					case '<>':
					case '!':
						if ($FilterItem===false || $FilterItem===null) {
							$arWhere[] = "`{$FilterKey}` is not null";
						} elseif(is_array($FilterItem)) {
							$arSubWhere = array();
							foreach($FilterItem as $Value) {
								$Value = $DB->ForSQL($Value);
								$arSubWhere[] = "`{$FilterKey}` {$Operation} '{$Value}'";
							}
							$strSubWhere = implode(' OR ', $arSubWhere);
							$arWhere[] = "({$strSubWhere})";
						}  else {
							$FilterItem = $DB->ForSQL($FilterItem);
							$arWhere[] = "`{$FilterKey}` <> '{$FilterItem}'";
						}
						break;
					case '=':
					default:
						if (is_array($FilterItem)) {
							$arSubWhere = array();
							foreach($FilterItem as $Value) {
								$Value = $DB->ForSQL($Value);
								$arSubWhere[] = "`{$FilterKey}` = '{$Value}'";
							}
							$strSubWhere = implode(' OR ', $arSubWhere);
							$arWhere[] = "({$strSubWhere})";
						} else {
							$FilterItem = $DB->ForSQL($FilterItem);
							$arWhere[] = "`{$FilterKey}` = '{$FilterItem}'";
						}
						break;
				}
			}
			if (count($arWhere)>0) {
				$SQL .= " WHERE ".implode(" AND ", $arWhere);
			}
		}
		// SORT
		if (is_array($arSort) && !empty($arSort)) {
			$SQL .= " ORDER BY ";
			$arSortBy = array();
			foreach ($arSort as $arSortKey => $arSortItem) {
				$arSortKey = $DB->ForSQL($arSortKey);
				$arSortItem = $DB->ForSQL($arSortItem);
				if (trim($arSortKey)!="") {
					if (in_array($arSortKey,array('DATE_CREATED','DATE_MODIFIED','DATE_VOTING'))) {
						$arSortKey = "{$TableName}`.`{$arSortKey}";
					}
					$SortBy = "`{$arSortKey}`";
					if (trim($arSortItem)!="") {
						$SortBy .= " {$arSortItem}";
					}
					$arSortBy[] = $SortBy;
				}
			}
			$SQL .= implode(", ", $arSortBy);
		}
		
		$SQL_Count = "SELECT COUNT(`ID`) as `CNT` FROM `{$TableName}`";
		if (count($arWhere)>0) {
			$SQL_Count .= " WHERE ".implode(" AND ", $arWhere);
		}
		if (!is_array($arNavParams)) {
			$arNavParams = array();
		}
		$resCount = $DB->Query($SQL_Count);
		$arCount = $resCount->GetNext(false,false);
		$res = new CDBResult();
		$res->NavQuery($SQL, $arCount['CNT'], $arNavParams, false);
		return $res;

	}
	
	/**
	 *	Get by ID
	 */
	function GetByID($ID) {
		return self::GetList(false,array('ID'=>$ID));
	}
	
	/**
	 *	Get fields in table
	 */
	function GetTableFields() {
		global $DB;
		$arResult = array();
		$Table = self::TableName;
		$SQL = "SHOW COLUMNS FROM `{$Table}`";
		$resColumns = $DB->Query($SQL);
		while ($arColumn = $resColumns->GetNext(false,false)) {
			$arResult[] = $arColumn['Field'];
		}
		return $arResult;
	}
	
	/**
	 *	Serialize FIELDS and RATINGS
	 */
	function Serialize($arFields) {
		if (isset($arFields['FIELDS'])) {
			$arFields['DATA_FIELDS'] = serialize($arFields['FIELDS']);
			unset($arFields['FIELDS']);
		}
		if (isset($arFields['RATINGS'])) {
			$arFields['DATA_RATINGS'] = serialize($arFields['RATINGS']);
			unset($arFields['RATINGS']);
		}
		return $arFields;
	}
	
	/**
	 *	Remove not existance fields
	 */
	function RemoveWrongFields($arFields) {
		$arResult = array();
		if (!is_array($arFields)) {
			$arFields = array();
		}
		$arExistsFields = self::GetTableFields();
		foreach($arFields as $Key => $Value) {
			$KeyName = trim($Key,"\r\n\t<>=%!");
			if (in_array($KeyName,$arExistsFields)) {
				$arResult[$Key] = $Value;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get review interface
	 */
	function ReviewGetInterface($InterfaceID) {
		$resInterface = CWD_Reviews2_Interface::GetByID($InterfaceID);
		if ($arInterface = $resInterface->GetNext()) {
			return $arInterface;
		}
		return false;
	}
	
	/**
	 *	Get fields for review
	 */
	function ReviewGetFields($ReviewID, $InterfaceID=false, $CodeAsKey=false) {
		$arResult = array();
		if ($InterfaceID==false) {
			$resReview = self::GetByID($ReviewID);
			if ($arReview = $resReview->GetNext(false,false)) {
				$InterfaceID = $arReview['INTERFACE_ID'];
			}
		}
		if ($InterfaceID!=false) {
			$resFields = CWD_Reviews2_Fields::GetList(array('SORT'=>'ASC'),array('INTERFACE_ID'=>$InterfaceID));
			while ($arFields = $resFields->GetNext()) {
				$arFields['PARAMS'] = unserialize($arFields['~PARAMS']);
				if ($CodeAsKey) {
					$arResult[$arFields['CODE']] = $arFields;
				} else {
					$arResult[] = $arFields;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get ratings for review
	 */
	function ReviewGetRatings($ReviewID, $InterfaceID=false, $IdAsKey=false) {
		$arResult = array();
		if ($InterfaceID==false) {
			$resReview = self::GetByID($ReviewID);
			if ($arReview = $resReview->GetNext(false,false)) {
				$InterfaceID = $arReview['INTERFACE_ID'];
			}
		}
		if ($InterfaceID!=false) {
			$resRatings = CWD_Reviews2_Ratings::GetList(array('SORT'=>'ASC'),array('INTERFACE_ID'=>$InterfaceID));
			while ($arRating = $resRatings->GetNext()) {
				$arRating['PARAMS'] = unserialize($arRating['~PARAMS']);
				if ($IdAsKey) {
					$arResult[$arRating['ID']] = $arRating;
				} else {
					$arResult[] = $arRating;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Handler for before save action
	 */
	function HandleBeforeSave($ID, $arNewFields, $Operation='Add') {
		if (isset($arNewFields['DATA_FIELDS'])) {
			$arNewFields['DATA_FIELDS'] = unserialize($arNewFields['DATA_FIELDS']);
			if (!is_array($arNewFields['DATA_FIELDS'])) {
				$arNewFields['DATA_FIELDS'] = array();
			}
		}
		
		// Set MODERATED
		if (!defined('WDR2_IMPORTING') && $Operation=='Add' && ($arNewFields['MODERATED'] || !in_array($arNewFields['MODERATED'],array('Y','N')))) {
			$resInterface = CWD_Reviews2_Interface::GetByID($arNewFields['INTERFACE_ID']);
			if ($arInterface = $resInterface->GetNext(false,false)) {
				$arNewFields['MODERATED'] = $arInterface['PRE_MODERATION']=='Y' ? 'N' : 'Y';
			}
		}
		
		// Get old fields
		$arOldFields = false;
		if ($ID>0) {
			$resOldFields = self::GetByID($ID);
			if($arOldFields = $resOldFields->GetNext()) {
				$arOldFields['DATA_FIELDS'] = unserialize($arOldFields['~DATA_FIELDS']);
			}
		}
		// Get additional fields
		$arReviewFields = self::ReviewGetFields($ID, $arNewFields['INTERFACE_ID']); // first - if UPDATE, second - if ADD
		
		// Execute events
		foreach(GetModuleEvents(CWD_Reviews2::ModuleID, 'OnBeforeReviewSave', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, array($ID, &$arNewFields, $Operation, $arReviewFields, $arOldFields));
		}
		
		// Save each field
		if (isset($arNewFields['DATA_FIELDS'])) {
			$arTypes = WDR2_GetFieldTypes();
			foreach($arReviewFields as $arReviewField) {
				$Code = $arReviewField['CODE'];
				$Type = $arReviewField['TYPE'];
				$NewValue = $arNewFields['DATA_FIELDS'][$Code];
				$OldValue = $arOldFields['DATA_FIELDS'][$Code];
				if ($Operation=='Update' && !isset($arNewFields['DATA_FIELDS'][$Code])) {
					continue;
				}
				if ($Type!==false && is_array($arTypes[$Type])) {
					$ClassName = $arTypes[$Type]['CLASS'];
					if (class_exists($ClassName) && method_exists($ClassName, 'SaveValue')) {
						$NewValue = $ClassName::SaveValue($Code, $OldValue, $NewValue, $Operation);
						if ($NewValue!==NULL) {
							$arNewFields['DATA_FIELDS'][$Code] = $NewValue;
						}
					}
				}
			}
		}
		
		//
		if ( ($Operation=='Add' && strlen($arNewFields['ANSWER'])==0) || ($Operation='Update' && isset($arNewFields['ANSWER']) && (strlen($arNewFields['ANSWER'])==0 || trim($arNewFields['ANSWER'])=='<br>')) ) {
			if ($Operation=='Add') {
				unset($arNewFields['ANSWER']);
				unset($arNewFields['DATE_ANSWER']);
				unset($arNewFields['ANSWER_USER_ID']);
			} else {
				$arNewFields['ANSWER'] = '';
				$arNewFields['DATE_ANSWER'] = '';
				$arNewFields['ANSWER_USER_ID'] = '';
			}
		}
		if (!defined('WDR2_IMPORTING') && strlen($arNewFields['ANSWER']) && $arOldFields['USER_ANSWER_NOTIFIED']!='Y') {
			if (!isset($arNewFields['DATE_ANSWER']) || strlen($arNewFields['DATE_ANSWER'])<=0) {
				$arNewFields['DATE_ANSWER'] = self::DateFormatToMySQL(date(CDatabase::DateFormatToPHP(FORMAT_DATETIME)));
			}
			if (strlen($arNewFields['ANSWER_USER_ID'])<=0) {
				$arNewFields['ANSWER_USER_ID'] = $GLOBALS['USER']->GetID();
			}
			define('WD_REVIEWS2_NEED_SEND_ANSWER_NOTICE_TO_USER',true);
		}
		// Check if moderated
		if (!defined('WDR2_IMPORTING') && $arOldFields['MODERATED']=='N' && $arNewFields['MODERATED']=='Y' && $arOldFields['USER_MODERATED_NOTIFIED']!='Y') {
			define('WD_REVIEWS2_NEED_SEND_MODERATED_NOTICE_TO_USER',true);
		}
		// Return all collected data
		if (isset($arNewFields['DATA_FIELDS'])) {
			$arNewFields['DATA_FIELDS'] = serialize($arNewFields['DATA_FIELDS']);
		}
		return $arNewFields;
	}
	
	/**
	 *	Handler for save action
	 */
	function HandleSave($ID, $arFields, $Operation=false) {
		$resReview = self::GetByID($ID);
		if ($arReview = $resReview->GetNext()) {
			$arReview['DATA_FIELDS'] = unserialize($arReview['~DATA_FIELDS']);
			$arReview['DATA_RATINGS'] = unserialize($arReview['~DATA_RATINGS']);
			// Execute events
			foreach(GetModuleEvents(CWD_Reviews2::ModuleID, 'OnReviewSave', true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array($ID, &$arFields, $Operation));
			}
			// Save rating to another db table
			self::SaveRating($arReview);
			// Update iblock properties
			self::UpdateIBlockElementFromTarget($arReview);
			if ($GLOBALS['WDR2_REVIEW_ADD_SKIP_NOTICE']!==true) {
				// Send notice if got answer
				if (defined('WD_REVIEWS2_NEED_SEND_ANSWER_NOTICE_TO_USER') && WD_REVIEWS2_NEED_SEND_ANSWER_NOTICE_TO_USER===true) {
					self::SendAnswerNotice($arReview);
				}
				// Send moderated notice if got moderated flag
				if (defined('WD_REVIEWS2_NEED_SEND_MODERATED_NOTICE_TO_USER') && WD_REVIEWS2_NEED_SEND_MODERATED_NOTICE_TO_USER===true) {
					self::SendModeratedNotice($arReview);
				}
			}
			$arInterfaceUpdate = array(
				'DATE_LAST_REVIEW' => date(CDatabase::DateFormatToPHP(FORMAT_DATETIME)),
			);
			if ($Operation=='Add') {
				$arInterfaceUpdate['REVIEWS_COUNT'] = self::GetInterfaceReviewsCount($arReview['INTERFACE_ID']);
			}
			$obInterface = new CWD_Reviews2_Interface;
			$obInterface->Update($arReview['INTERFACE_ID'],$arInterfaceUpdate);
		}
	}
	
	/**
	 *	Send e-mail notice to user if administrator gave an answer
	 */
	function SendAnswerNotice($arFields) {
		$bCanSendAnswerNotice = false;
		if (is_numeric($arFields['INTERFACE_ID']) && $arFields['INTERFACE_ID']>0) {
			$resInterface = CWD_Reviews2_Interface::GetByID($arFields['INTERFACE_ID']);
			if ($arInterface = $resInterface->GetNext(false,false)) {
				$bCanSendAnswerNotice = $arInterface['EMAIL_ON_ANSWER']=='Y';
			}
		}
		if(!$bCanSendAnswerNotice) {
			return false;
		}
		$ID = $arFields['ID'];
		if (!is_numeric($ID) || $ID<=0) {
			return false;
		}
		if (isset($arFields['DATA_FIELDS']) && !is_array($arFields['DATA_FIELDS'])) {
			$arFields['DATA_FIELDS'] = unserialize($arFields['DATA_FIELDS']);
		}
		if (!is_array($arFields['DATA_FIELDS'])) {
			$arFields['DATA_FIELDS'] = array();
		}
		$arTypes = WDR2_GetFieldTypes();
		$arReviewFields = $arFields['DATA_FIELDS'];
		$arFieldsSettings = self::ReviewGetFields($ID, $arFields['INTERFACE_ID']);
		$Review = false;
		$Name = false;
		$Email = false;
		if (is_array($arFieldsSettings)) {
			foreach($arFieldsSettings as $arFieldSettings) {
				$Code = $arFieldSettings['CODE'];
				if ($arFieldSettings['PARAMS']['is_review']=='Y') {
					$Review = $arReviewFields[$Code];
				}
				if ($arFieldSettings['PARAMS']['is_name']=='Y') {
					$Name = $arReviewFields[$Code];
				}
				if ($arFieldSettings['PARAMS']['is_email']=='Y') {
					$Email = $arReviewFields[$Code];
				}
				if (isset($arFields['DATA_FIELDS'][$Code])) {
					$Type = $arFieldSettings['TYPE'];
					$Value = $arFields['DATA_FIELDS'][$Code];
					if (is_array($arTypes[$Type])) {
						$ClassName = $arTypes[$Type]['CLASS'];
						if (method_exists($ClassName,'GetNotifyValue')) {
							$arFields['F_'.$Code] = $ClassName::GetNotifyValue($Value, $arFieldSettings);
						} elseif(method_exists($ClassName,'GetDisplayValue')) {
							$arFields['F_'.$Code] = $ClassName::GetDisplayValue($Value, $arFieldSettings);
						} else {
							$arFields['F_'.$Code] = $Value;
						}
					}
				}
			}
			if (check_email($Email)) {
				$arFields['USER_REVIEW'] = $Review;
				$arFields['USER_NAME'] = $Name;
				$arFields['USER_EMAIL'] = $Email;
				$arFields['TARGET_URL'] = self::GetReviewURL($arFields);
				$arSites = CWD_Reviews2::GetSitesList(true);
				if (is_array($arSites) && !empty($arSites)) {
					unset($arFields['DATA_FIELDS']);
					unset($arFields['DATA_RATINGS']);
					$EventType = CWD_Reviews2_Interface::EventTypePrefix.'_A_'.$arFields['INTERFACE_ID'];
					$arFields['_ANSWER'] = $arFields['ANSWER'];
					$arFields['ANSWER'] = $arFields['~ANSWER'];
					foreach($arFields as $Key => $Value) {
						if (strpos($Key,'~')===0) {
							unset($arFields[$Key]);
						}
					}
					if (CEvent::Send($EventType, $arSites, $arFields)) {
						CEvent::CheckEvents();
						global $DB;
						$TableName = self::TableName;
						$SQL = "UPDATE `{$TableName}` SET `USER_ANSWER_NOTIFIED`='Y' WHERE `ID`='{$ID}' LIMIT 1;";
						$DB->Query($SQL);
					}
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 *	Send e-mail notice to user if review moderated
	 */
	function SendModeratedNotice($arFields) {
		$bCanSendModeratedNotice = false;
		if (is_numeric($arFields['INTERFACE_ID']) && $arFields['INTERFACE_ID']>0) {
			$resInterface = CWD_Reviews2_Interface::GetByID($arFields['INTERFACE_ID']);
			if ($arInterface = $resInterface->GetNext(false,false)) {
				$bCanSendModeratedNotice = $arInterface['EMAIL_ON_MODERATED']=='Y';
			}
		}
		if(!$bCanSendModeratedNotice) {
			return false;
		}
		$ID = $arFields['ID'];
		if (!is_numeric($ID) || $ID<=0) {
			return false;
		}
		if (isset($arFields['DATA_FIELDS']) && !is_array($arFields['DATA_FIELDS'])) {
			$arFields['DATA_FIELDS'] = unserialize($arFields['DATA_FIELDS']);
		}
		if (!is_array($arFields['DATA_FIELDS'])) {
			$arFields['DATA_FIELDS'] = array();
		}
		$arTypes = WDR2_GetFieldTypes();
		$arReviewFields = $arFields['DATA_FIELDS'];
		$arFieldsSettings = self::ReviewGetFields($ID, $arFields['INTERFACE_ID']);
		$Review = false;
		$Name = false;
		$Email = false;
		if (is_array($arFieldsSettings)) {
			foreach($arFieldsSettings as $arFieldSettings) {
				$Code = $arFieldSettings['CODE'];
				if ($arFieldSettings['PARAMS']['is_review']=='Y') {
					$Review = $arReviewFields[$Code];
				}
				if ($arFieldSettings['PARAMS']['is_name']=='Y') {
					$Name = $arReviewFields[$Code];
				}
				if ($arFieldSettings['PARAMS']['is_email']=='Y') {
					$Email = $arReviewFields[$Code];
				}
				if (isset($arFields['DATA_FIELDS'][$Code])) {
					$Type = $arFieldSettings['TYPE'];
					$Value = $arFields['DATA_FIELDS'][$Code];
					if (is_array($arTypes[$Type])) {
						$ClassName = $arTypes[$Type]['CLASS'];
						if (method_exists($ClassName,'GetNotifyValue')) {
							$arFields['F_'.$Code] = $ClassName::GetNotifyValue($Value, $arFieldSettings);
						} elseif(method_exists($ClassName,'GetDisplayValue')) {
							$arFields['F_'.$Code] = $ClassName::GetDisplayValue($Value, $arFieldSettings);
						} else {
							$arFields['F_'.$Code] = $Value;
						}
					}
				}
			}
			if (check_email($Email)) {
				$arFields['USER_REVIEW'] = $Review;
				$arFields['USER_NAME'] = $Name;
				$arFields['USER_EMAIL'] = $Email;
				$arFields['TARGET_URL'] = self::GetReviewURL($arFields);
				$arSites = CWD_Reviews2::GetSitesList(true);
				if (is_array($arSites) && !empty($arSites)) {
					unset($arFields['DATA_FIELDS']);
					unset($arFields['DATA_RATINGS']);
					$EventType = CWD_Reviews2_Interface::EventTypePrefix.'_Y_'.$arFields['INTERFACE_ID'];
					$arFields['_ANSWER'] = $arFields['ANSWER'];
					$arFields['ANSWER'] = $arFields['~ANSWER'];
					foreach($arFields as $Key => $Value) {
						if (strpos($Key,'~')===0) {
							unset($arFields[$Key]);
						}
					}
					if (CEvent::Send($EventType, $arSites, $arFields)) {
						CEvent::CheckEvents();
						global $DB;
						$TableName = self::TableName;
						$SQL = "UPDATE `{$TableName}` SET `USER_MODERATED_NOTIFIED`='Y' WHERE `ID`='{$ID}' LIMIT 1;";
						$DB->Query($SQL);
					}
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 *	Get url for target
	 */
	function GetReviewURL($arFields) {
		$ID = $arFields['ID'];
		$URL = false;
		if ($arFields['INTERFACE_ID']>0) {
			$resInterface = CWD_Reviews2_Interface::GetByID($arFields['INTERFACE_ID']);
			if ($arInterface = $resInterface->GetNext(false,false)) {
				$URL = $arInterface['URL'];
				$u = defined('BX_UTF') && BX_UTF===true ? 'u' : '';
				if (preg_match('#^E_([\d]+)$#is'.$u,$arFields['TARGET'],$M)) {
					$ID = $M[1];
					if ($ID>0 && CModule::IncludeModule('iblock')) {
						$resItem = CIBlockElement::GetList(array(),array('ID'=>$ID),false,array('nTopCount'=>'1'),array('DETAIL_PAGE_URL'));
						if ($URL!='') {
							$resItem->SetUrlTemplates($URL);
						}
						if ($arItem = $resItem->GetNext(false,false)) {
							$URL = $arItem['DETAIL_PAGE_URL'];
						}
					}
				}
			}
		}
		return $URL;
	}
	
	/**
	 *	Creating iblock properties
	 */
	function AddIBlockProperties($IBlockID) {
		if (CModule::IncludeModule('iblock')) {
			$arProps = array(
				'WD_REVIEWS2_COUNT' => array(
					'NAME' => GetMessage('WD_REVIEWS2_IBLOCK_PROP_COUNT'),
					'PROPERTY_TYPE' => 'N',
					'SORT' => '1001',
				),
				'WD_REVIEWS2_RATING' => array(
					'NAME' => GetMessage('WD_REVIEWS2_IBLOCK_PROP_RATING'),
					'PROPERTY_TYPE' => 'N',
					'SORT' => '1002',
				),
				'WD_REVIEWS2_LAST' => array(
					'NAME' => GetMessage('WD_REVIEWS2_IBLOCK_PROP_LAST'),
					'PROPERTY_TYPE' => 'S',
					'USER_TYPE' => 'DateTime',
					'SORT' => '1004',
				),
			);
			$IBlockProperty = new CIBlockProperty;
			foreach($arProps as $PropCode => $arProp) {
				$resProp = CIBlockProperty::GetList(array(),array('IBLOCK_ID'=>$IBlockID,'CODE'=>$PropCode));
				if (false==$resProp->GetNext(false,false)) {
					$arProp['ACTIVE'] = 'Y';
					$arProp['CODE'] = $PropCode;
					$arProp['IBLOCK_ID'] = $IBlockID;
					$IBlockProperty->Add($arProp);
				}
			}
		}
	}
	
	/**
	 *	Save rating values to another table
	 */
	function SaveRating($arFields) {
		global $DB;
		$TableName = 'b_wd_reviews2_ratingsvalues';
		$ReviewID = $arFields['ID'];
		if (is_array($arFields['DATA_RATINGS']) && !empty($arFields['DATA_RATINGS'])) {
			// Delete old values
			$SQL = "DELETE FROM `{$TableName}` WHERE `REVIEW_ID`='{$ReviewID}';";
			if ($DB->Query($SQL,false)) {
				// Insert new values
				$arRatingValue = array();
				foreach($arFields['DATA_RATINGS'] as $RatingID => $RatingValue) {
					$arRatingValue[] = "('{$ReviewID}','{$RatingID}','{$RatingValue}')";
				}
				$strValues = implode(', ', $arRatingValue);
				$SQL = "INSERT INTO `{$TableName}` (`REVIEW_ID`,`RATING_ID`,`VALUE`) VALUES {$strValues};";
				if ($DB->Query($SQL,false)!==false) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 *	Get average rating for review element
	 */
	function GetRatingResult($ElementID, $InterfaceID) {
		global $DB;
		$SQL = "
			SELECT 
				AVG(`WD_RV`.`VALUE`) as RATING
			FROM 
				`b_wd_reviews2_ratingsvalues` `WD_RV`
			LEFT JOIN `b_wd_reviews2_ratings` `WD_RAT`
				ON `WD_RAT`.`ID`=`WD_RV`.`RATING_ID`
			LEFT JOIN `b_wd_reviews2_reviews` `WD_REV`
				ON `WD_REV`.`ID`=`WD_RV`.`REVIEW_ID`
			LEFT JOIN `b_wd_reviews2_interface` `WD_I`
				ON `WD_I`.`ID`=`WD_REV`.`INTERFACE_ID`
			WHERE 
				`WD_RV`.`VALUE`>0 AND 
				`WD_RAT`.`PARTICIPATES`='Y' AND 
				`WD_REV`.`ID`=`WD_RV`.`REVIEW_ID` AND 
				`WD_REV`.`TARGET`='E_{$ElementID}' AND 
				`WD_REV`.`MODERATED`='Y' AND 
				`WD_I`.`ID`='{$InterfaceID}';
		";
		$resQuery = $DB->Query($SQL, false);
		if ($arItem = $resQuery->GetNext(false,false)) {
			return FloatVal($arItem['RATING']);
		}
		return false;
	}
	
	/**
	 *	Get reviews count for review element
	 */
	function GetReviewsCount($ElementID, $InterfaceID) {
		global $DB;
		$TableName = self::TableName;
		$SQL = "
			SELECT 
				COUNT(`WD_REV`.`ID`) as COUNT
			FROM 
				`{$TableName}` `WD_REV`
			LEFT JOIN `b_wd_reviews2_interface` `WD_I`
				ON `WD_I`.`ID`=`WD_REV`.`INTERFACE_ID`
			WHERE 
				`WD_REV`.`TARGET`='E_{$ElementID}' AND 
				`WD_REV`.`MODERATED`='Y' AND 
				`WD_I`.`ID`='{$InterfaceID}';
		";
		$resQuery = $DB->Query($SQL, false);
		if ($arItem = $resQuery->GetNext(false,false)) {
			return IntVal($arItem['COUNT']);
		}
		return false;
	}
	
	/**
	 *	Update IBlock element properties after rating save
	 */
	function UpdateIBlockElementRating($ElementID, $InterfaceID) {
		if (CModule::IncludeModule('iblock')) {
			$resItem = CIBlockElement::GetList(array(),array('ID'=>$ElementID),false,array('nTopCount'=>'1'),array('IBLOCK_ID','NAME'));
			if ($arItem = $resItem->GetNext(false,false)) {
				$IBlockID = $arItem['IBLOCK_ID'];
				if ($IBlockID>0) {
					self::AddIBlockProperties($IBlockID);
					$arValues = array(
						'WD_REVIEWS2_LAST' => date(CDatabase::DateFormatToPHP(FORMAT_DATETIME)),
						'WD_REVIEWS2_RATING' => self::GetRatingResult($ElementID, $InterfaceID),
						'WD_REVIEWS2_COUNT' => self::GetReviewsCount($ElementID, $InterfaceID),
					);
					CIBlockElement::SetPropertyValuesEx($ElementID, $IBlockID, $arValues);
					// Anticache
					$IBlockElement = new CIBlockElement;
					$IBlockElement->Update($ElementID, array());
				}
			}
		}
	}
	
	/**
	 *	Update IBlock element
	 */
	function UpdateIBlockElementFromTarget($arFields) {
		if (COption::GetOptionString(CWD_Reviews2::ModuleID, 'auto_create_iblock_props')=='Y') {
			$ID = $arFields['ID'];
			$u = defined('BX_UTF') && BX_UTF===true ? 'u' : '';
			if (preg_match('#^E_([\d]+)$#is'.$u,$arFields['TARGET'],$M)) {
				$ID = $M[1];
				if ($ID>0 && CModule::IncludeModule('iblock')) {
					if (COption::GetOptionString(CWD_Reviews2::ModuleID, 'auto_create_iblock_props')!='N') {
						self::UpdateIBlockElementRating($ID, $arFields['INTERFACE_ID']);
					}
				}
			}
		}
	}
	
	/**
	 *	Get full reviews count for interface
	 */
	function GetInterfaceReviewsCount($InterfaceID) {
		global $DB;
		if ($InterfaceID>0) {
			$TableName = self::TableName;
			$SQL = "SELECT COUNT(`ID`) as `COUNT` FROM `{$TableName}` WHERE `INTERFACE_ID`='{$InterfaceID}';";
			$resResult = $DB->Query($SQL, false);
			if ($arResult = $resResult->GetNext(false,false)) {
				return IntVal($arResult['COUNT']);
			}
		}
		return false;
	}
	
	/**
	 *	Get last date of created or modified reviews within an interface
	 */
	function GetInterfaceReviewsLastDate($InterfaceID) {
		global $DB;
		if ($InterfaceID>0) {
			$TableName = self::TableName;
			$DateSelect = $DB->DateToCharFunction('`DATE_CREATED`').' `DATE_CREATED`';
			$SQL = "SELECT {$DateSelect} FROM `{$TableName}` WHERE `INTERFACE_ID`='{$InterfaceID}' ORDER BY `DATE_CREATED` DESC LIMIT 1;";
			$resResult = $DB->Query($SQL, false);
			if ($arResult = $resResult->GetNext(false,false)) {
				return $arResult['DATE_CREATED'];
			}
		}
		return false;
	}
	
	/**
	 *	Get last voting date for target
	 */
	function GetTargetLastVotingDate($Target) {
		global $DB;
		$TableName = self::TableName;
		$TableVoting = 'b_wd_reviews2_voting';
		$Datetime = $DB->DateToCharFunction("`{$TableVoting}`.`DATE_CREATED`").' `LAST_VOTING`';
		$SQL = "
			SELECT {$Datetime} FROM `{$TableName}`
			LEFT JOIN `{$TableVoting}` ON `{$TableName}`.`ID`=`{$TableVoting}`.`REVIEW_ID`
			WHERE `{$TableName}`.`TARGET`='{$Target}'
			ORDER BY `LAST_VOTING` DESC
			LIMIT 0,1;";
		$resItem = $DB->Query($SQL, false);
		if ($arItem = $resItem->GetNext(false,false)) {
			return $arItem['LAST_VOTING'];
		}
		return false;
	}
	
}

?>