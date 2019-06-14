<?
IncludeModuleLangFile(__FILE__);

class CWD_Reviews2_Interface {
	const TableName = 'b_wd_reviews2_interface';
	const EventTypePrefix = 'WD_REVIEWS2';
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
	function Add($arFields) {
		global $DB;
		self::CheckRequiredField(false, $arFields, true);
		$arFields = self::RemoveWrongFields($arFields);
		$Now =  date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
		$arFields['DATE_CREATED'] = $Now;
		$arFields['DATE_MODIFIED'] = $Now;
		if (empty($this->arLastErrors)) {
			$ID = $DB->Add(self::TableName, $arFields, array(), '', true);
			if ($ID>0) {
				self::HandleAdd($ID, $arFields);
				return $ID;
			}
		}
		return false;
	}
	
	// Update
	function Update($ID, $arFields) {
		global $DB;
		self::CheckRequiredField(false, $arFields, false);
		$arFields = self::RemoveWrongFields($arFields);
		$Now =  date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
		$arFields['DATE_MODIFIED'] = self::DateFormatToMySQL($Now);
		if (strlen($arFields['DATE_LAST_REVIEW'])) {
			$arFields['DATE_LAST_REVIEW'] = self::DateFormatToMySQL($arFields['DATE_LAST_REVIEW']);
			
		}
		$arSQL = array();
		foreach ($arFields as $Key => $Field) {
			$Key = $DB->ForSQL($Key);
			$Field = $DB->ForSQL($Field);
			$arSQL[] = "`{$Key}`='{$Field}'";
		}
		$strSQL = implode(',',$arSQL);
		$TableName = self::TableName;
		if (empty($this->arLastErrors)) {
			$SQL = "UPDATE `{$TableName}` SET {$strSQL} WHERE `ID`='{$ID}' LIMIT 1;";
			if ($DB->Query($SQL, false)) {
				self::HandleUpdate($ID, $arFields);
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Check required fields on interface save
	 */
	function CheckRequiredField($ID, $arFields, $Add=true) {
		$this->arLastErrors = array();
		if (($Add && !isset($arFields['NAME'])) || (isset($arFields['NAME']) && strlen($arFields['NAME'])===0)) {
			$this->arLastErrors[] = GetMessage('WD_REVIEWS2_INTERFACE_ERROR_NO_NAME');
		}
	}
	
	/**
	 *	Delete
	 */
	function Delete($ID) {
		global $DB;
		$this->arLastErrors = array();
		$TableName = self::TableName;
		$ReviewsCount = CWD_Reviews2_Reviews::GetInterfaceReviewsCount($ID);
		if ($ReviewsCount>0) {
			$this->arLastErrors[] = GetMessage('WD_REVIEWS2_INTERFACE_ERROR_DELETE_REVIEWS_EXISTS');
			return false;
		}
		$SQL = "DELETE FROM `{$TableName}` WHERE `ID`='{$ID}';";
		if ($DB->Query($SQL, false)) {
			self::HandleDelete($ID);
			return true;
		}
		return false;
	}
	
	/**
	 *	Get list
	 */
	function GetList($arSort=false, $arFilter=false) {
		global $DB;
		$arDateTimeFields = array('DATE_CREATED','DATE_MODIFIED','DATE_LAST_REVIEW');
		$strSelectFields = self::GetSelectFields($arDateTimeFields);
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
		$SQL = "SELECT {$strSelectFields}{$strDateTimeSelect} FROM `{$TableName}`";
		if (is_array($arFilter) && !empty($arFilter)) {
			$arWhere = array();
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
		// Sort
		if (is_array($arSort) && !empty($arSort)) {
			$SQL .= " ORDER BY ";
			$arSortBy = array();
			foreach ($arSort as $arSortKey => $arSortItem) {
				$arSortKey = $DB->ForSQL($arSortKey);
				$arSortItem = $DB->ForSQL($arSortItem);
				if (trim($arSortKey)!="") {
					if (in_array($arSortKey,array('DATE_CREATED','DATE_MODIFIED','DATE_LAST_REVIEW'))) {
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
		return $DB->Query($SQL, false);
	}
	
	/**
	 *	Wrapper for GetList(), returns array
	 */
	function GetListArray($arSort=false, $arFilter=false) {
		$arResult = array();
		$resInterfaces = self::GetList($arSort, $arFilter);
		while ($arInterfaces = $resInterfaces->GetNext()) {
			$arResult[] = $arInterfaces;
		}
		return $arResult;
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
	 *	Get fields for MySQL SELECT command
	 */
	function GetSelectFields($Exclude=false) {
		$strResult = '';
		$arExistsFields = self::GetTableFields();
		if (is_array($arExistsFields)) {
			$arFields = array();
			foreach($arExistsFields as $arExistsField) {
				if (is_array($Exclude) && in_array($arExistsField,$Exclude)) {
					continue;
				}
				$arFields[] = "`{$arExistsField}`";
			}
			$strResult = implode(',',$arFields);
		} else {
			$strResult = '*';
		}
		return $strResult;
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
	 *	Get macroses for event
	 */
	function GetEventMacroses($ID, $arFields) {
		$arResult = array();
		$resFields = CWD_Reviews2_Fields::GetList(array('SORT'=>'ASC'),array('INTERFACE_ID'=>$ID));
		while ($arFields = $resFields->GetNext()) {
			$arFields['PARAMS'] = unserialize($arFields['~PARAMS']);
			$arResult['F_'.$arFields['CODE']] = $arFields['NAME'];
		}
		$arResult['USER_REVIEW'] = GetMessage('WD_REVIEWS2_MACROS_REVIEW');
		$arResult['USER_NAME'] = GetMessage('WD_REVIEWS2_MACROS_NAME');
		$arResult['USER_EMAIL'] = GetMessage('WD_REVIEWS2_MACROS_EMAIL');
		$arResult['USER_ID'] = GetMessage('WD_REVIEWS2_MACROS_USER_ID');
		$arResult['DATETIME'] = GetMessage('WD_REVIEWS2_MACROS_DATETIME');
		$arResult['REVIEW_ID'] = GetMessage('WD_REVIEWS2_MACROS_REVIEW_ID');
		$arResult['INTERFACE_ID'] = GetMessage('WD_REVIEWS2_MACROS_INTERFACE_ID');
		$arResult['TARGET'] = GetMessage('WD_REVIEWS2_MACROS_TARGET');
		$arResult['TARGET_URL'] = GetMessage('WD_REVIEWS2_MACROS_TARGET_URL');
		
		$arMacroses = array();
		foreach($arResult as $Key => $Value) {
			$arMacroses[] = "#{$Key}# - {$Value}";
		}
		$strResult = implode("\n", $arMacroses);
		
		return $strResult;
	}
	
	/**
	 *	Create event type and messages
	 */
	function CreateEventType($ID, $arEvents=false) {
		if (!is_numeric($ID) || $ID<=0) {
			return false;
		}
		if ($arEvents===false) {
			$arEvents = array('REVIEW_GENERAL','REVIEW_MODERATE','REVIEW_ANSWER','REVIEW_MODERATED');
		}
		$resInterface = self::GetByID($ID);
		if ($arInterface = $resInterface->GetNext()) {
			$arFields = $arInterface;
		} else {
			return false;
		}
		// Prepare
		$EventType = new CEventType;
		$EventMessage = new CEventMessage;
		$strMacroses = self::GetEventMacroses($ID, $arFields);
		$arSites = CWD_Reviews2::GetSitesList(true);
		$arEventMessagesFielsDef = array(
			'ACTIVE' => 'Y',
			'LID' => $arSites,
			'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
			'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
			'BODY_TYPE' => 'html',
		);
		
		// Create event type for review add event
		if (in_array('REVIEW_GENERAL', $arEvents)) {
			$EventTypeCode = self::EventTypePrefix.'_N_'.$ID;
			// Create event type
			$arEventTypeFields = array(
				'LID' => LANGUAGE_ID,
				'EVENT_NAME' => $EventTypeCode,
				'NAME' => sprintf(GetMessage('WD_REVIEWS2_EVENT_TYPE_GENERAL'), $arFields['NAME']),
				'DESCRIPTION' => $strMacroses,
			);
			$resEventTypes = CEventType::GetList(array('TYPE_ID'=>$EventTypeCode,'LID'=>LANGUAGE_ID));
			if ($arEventType = $resEventTypes->GetNext(false,false)) {
				$EventType->Update(array('ID'=>$arEventType['ID']), $arEventTypeFields);
			} else {
				$EventType->Add($arEventTypeFields);
				// Create event messages for user
				$arEventMessageFields = array(
					'EVENT_NAME' => $EventTypeCode,
					'EMAIL_TO' => '#USER_EMAIL#',
					'SUBJECT' => GetMessage('WD_REVIEWS2_EVENT_MESSAGE_GENERAL_USER_SUBJECT'),
					'MESSAGE' => self::GetEventMessageTemplate('general_user'),
				);
				$EventMessage->Add(array_merge($arEventMessagesFielsDef, $arEventMessageFields));
				// Create event messages for admin
				$arEventMessageFields = array(
					'EVENT_NAME' => $EventTypeCode,
					'SUBJECT' => GetMessage('WD_REVIEWS2_EVENT_MESSAGE_GENERAL_ADMIN_SUBJECT'),
					'MESSAGE' => self::GetEventMessageTemplate('general_admin'),
				);
				$EventMessage->Add(array_merge($arEventMessagesFielsDef, $arEventMessageFields));
			}
		}
		
		// Create event type for review add event (with moderate option)
		if (in_array('REVIEW_MODERATE', $arEvents)) {
			$EventTypeCode = self::EventTypePrefix.'_M_'.$ID;
			$arEventTypeFields = array(
				'LID' => LANGUAGE_ID,
				'EVENT_NAME' => $EventTypeCode,
				'NAME' => sprintf(GetMessage('WD_REVIEWS2_EVENT_TYPE_MODERATE'), $arFields['NAME']),
				'DESCRIPTION' => $strMacroses,
			);
			$resEventTypes = CEventType::GetList(array('TYPE_ID'=>$EventTypeCode,'LID'=>LANGUAGE_ID));
			if ($arEventType = $resEventTypes->GetNext(false,false)) {
				$EventType->Update(array('ID'=>$arEventType['ID']), $arEventTypeFields);
			} else {
				$EventType->Add($arEventTypeFields);
				// Create event messages for user
				$arEventMessageFields = array(
					'EVENT_NAME' => $EventTypeCode,
					'EMAIL_TO' => '#USER_EMAIL#',
					'SUBJECT' => GetMessage('WD_REVIEWS2_EVENT_MESSAGE_MODERATE_USER_SUBJECT'),
					'MESSAGE' => self::GetEventMessageTemplate('moderate_user'),
				);
				$EventMessage->Add(array_merge($arEventMessagesFielsDef, $arEventMessageFields));
				// Create event messages for admin
				$arEventMessageFields = array(
					'EVENT_NAME' => $EventTypeCode,
					'SUBJECT' => GetMessage('WD_REVIEWS2_EVENT_MESSAGE_MODERATE_ADMIN_SUBJECT'),
					'MESSAGE' => self::GetEventMessageTemplate('moderate_admin'),
				);
				$EventMessage->Add(array_merge($arEventMessagesFielsDef, $arEventMessageFields));
			}
		}
		
		// Create event type for answer notice
		if (in_array('REVIEW_ANSWER', $arEvents)) {
			$EventTypeCode = self::EventTypePrefix.'_A_'.$ID;
			$arEventTypeFields = array(
				'LID' => LANGUAGE_ID,
				'EVENT_NAME' => $EventTypeCode,
				'NAME' => sprintf(GetMessage('WD_REVIEWS2_EVENT_TYPE_ANSWER'), $arFields['NAME']),
				'DESCRIPTION' => $strMacroses,
			);
			$resEventTypes = CEventType::GetList(array('TYPE_ID'=>$EventTypeCode,'LID'=>LANGUAGE_ID));
			if ($arEventType = $resEventTypes->GetNext(false,false)) {
				$EventType->Update(array('ID'=>$arEventType['ID']), $arEventTypeFields);
			} else {
				$EventType->Add($arEventTypeFields);
				// Create event messages for user
				$arEventMessageFields = array(
					'EVENT_NAME' => $EventTypeCode,
					'EMAIL_TO' => '#USER_EMAIL#',
					'SUBJECT' => GetMessage('WD_REVIEWS2_EVENT_MESSAGE_ANSWER_USER_SUBJECT'),
					'MESSAGE' => self::GetEventMessageTemplate('answer'),
				);
				$EventMessage->Add(array_merge($arEventMessagesFielsDef, $arEventMessageFields));
			}
		}
		
		// Create event type for moderated notice
		if (in_array('REVIEW_MODERATED', $arEvents)) {
			$EventTypeCode = self::EventTypePrefix.'_Y_'.$ID;
			$arEventTypeFields = array(
				'LID' => LANGUAGE_ID,
				'EVENT_NAME' => $EventTypeCode,
				'NAME' => sprintf(GetMessage('WD_REVIEWS2_EVENT_TYPE_MODERATED'), $arFields['NAME']),
				'DESCRIPTION' => $strMacroses,
			);
			$resEventTypes = CEventType::GetList(array('TYPE_ID'=>$EventTypeCode,'LID'=>LANGUAGE_ID));
			if ($arEventType = $resEventTypes->GetNext(false,false)) {
				$EventType->Update(array('ID'=>$arEventType['ID']), $arEventTypeFields);
			} else {
				$EventType->Add($arEventTypeFields);
				// Create event messages for user
				$arEventMessageFields = array(
					'EVENT_NAME' => $EventTypeCode,
					'EMAIL_TO' => '#USER_EMAIL#',
					'SUBJECT' => GetMessage('WD_REVIEWS2_EVENT_MESSAGE_MODERATED_USER_SUBJECT'),
					'MESSAGE' => self::GetEventMessageTemplate('moderated'),
				);
				$EventMessage->Add(array_merge($arEventMessagesFielsDef, $arEventMessageFields));
			}
		}
		
	}
	
	/**
	 *	Get contents of message template in '/bitrix/modules/webdebug.reviews/install/message_templates/';
	 */
	function GetEventMessageTemplate($TemplateName) {
		global $APPLICATION;
		$strResult = '';
		$TemplateName = ToLower($TemplateName);
		$TemplatesPath = BX_ROOT.'/modules/'.CWD_Reviews2::ModuleID.'/install/message_templates/';
		$TemplatePath = $TemplatesPath.$TemplateName.'.php';
		if (is_file($_SERVER['DOCUMENT_ROOT'].$TemplatePath)) {
			ob_start();
			require_once($_SERVER['DOCUMENT_ROOT'].$TemplatePath);
			$strResult = ob_get_clean();
		}
		if (CWD_Reviews2::IsUtf8()) {
			$strResult = $APPLICATION->ConvertCharset($strResult, 'CP1251', 'UTF-8');
		}
		return $strResult;
	}
	
	/**
	 *	Handle add
	 */
	function HandleAdd($ID, $arFields) {
		self::CreateEventType($ID);
		self::CheckAutoJQuery();
	}
	
	/**
	 *	Handle update
	 */
	function HandleUpdate($ID, $arFields) {
		self::CreateEventType($ID);
		self::CheckAutoJQuery();
	}
	
	/**
	 *	Delete event type
	 */
	function DeleteEventType($ID) {
		$EventType = self::EventTypePrefix.$ID;
		$resMessage = CEventMessage::GetList($By='ID',$Order='DESC',array('TYPE'=>$EventType));
		while ($arMessage = $resMessage->GetNext(false,false)) {
			CEventMessage::Delete($arMessage['ID']);
		}
		CEventType::Delete($EventType);
	}
	
	/**
	 *	Delete fields for deleteing interface
	 */
	function DeleteFields($ID) {
		CWD_Reviews2_Fields::DeleteByInterface($ID);
	}
	
	/**
	 *	Delete ratings for deleteing interface
	 */
	function DeleteRatings($ID) {
		CWD_Reviews2_Ratings::DeleteByInterface($ID);
	}
	
	/**
	 *	Delete reviews for deleteing interface
	 */
	function DeleteReviews($ID) {
		CWD_Reviews2_Reviews::DeleteByInterface($ID);
	}
	
	/**
	 *	Handle delete
	 */
	function HandleDelete($ID) {
		self::DeleteEventType($ID);
		self::DeleteRatings($ID);
		self::DeleteFields($ID);
		self::DeleteReviews($ID);
		self::CheckAutoJQuery();
	}
	
	/**
	 *	Update
	 */
	function UpdateStatus($InterfaceID) {
		$arInterfaceUpdate = array(
			'DATE_LAST_REVIEW' => CWD_Reviews2_Reviews::GetInterfaceReviewsLastDate($InterfaceID),
			'REVIEWS_COUNT' => CWD_Reviews2_Reviews::GetInterfaceReviewsCount($InterfaceID),
		);
		$this->Update($InterfaceID,$arInterfaceUpdate);
	}
	
	function CheckAutoJQuery() {
		$bAutoJQuery = false;
		$GLOBALS['WD_TEST'] = true;
		$resInterfaces = self::GetList(array(),array('!JQUERY_INIT_URL'=>''));
		if ($resInterfaces->GetNext(false,false)) {
			$bAutoJQuery = true;
		}
		COption::SetOptionString(CWD_Reviews2::ModuleID,'auto_jquery',$bAutoJQuery?'Y':'N');
	}
	
}

?>