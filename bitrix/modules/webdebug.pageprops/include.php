<?
IncludeModuleLangFile(__FILE__);

class CWD_Pageprops {
	
	const ModuleID = 'webdebug.pageprops';
	const TableName = 'b_webdebug_pageprops';
	
	/**
	 *	Add
	 */
	function Add($arFields) {
		global $DB;
		if ($DB->Add(self::TableName, $arFields, array(), '', true)) {
			return true;
		}
		return false;
	}
	
	/*
	 *	Update
	 */
	function Update($ID, $arFields) {
		global $DB;
		$arSQL = array();
		foreach ($arFields as $Key => $Field) {
			$Key = $DB->ForSQL($Key);
			#$Field = $DB->ForSQL($Field);
			$arSQL[] = "`{$Key}`='{$Field}'";
		}
		$strSQL = implode(',',$arSQL);
		$TableName = self::TableName;
		$SQL = "UPDATE `{$TableName}` SET {$strSQL} WHERE `ID`='{$ID}' LIMIT 1;";
		if ($DB->Query($SQL, true)) {
			return true;
		}
		return false;
	}
	
	/**
	 *	Get list
	 */
	function GetList($arSort=false, $arFilter=false) {
		global $DB;
		if (!is_array($arSort)) {$arSort = array("PROPERTY"=>"ASC");}
		foreach ($arSort as $Key => $Value) {
			$Value = strtolower($Value);
			if ($Value!="asc" && $Value!="desc") {
				unset($arSort[$Key]);
			}
		}
		$TableName = self::TableName;
		$SQL = "SELECT * FROM `{$TableName}`";
		if (is_array($arFilter) && !empty($arFilter)) {
			foreach ($arFilter as $arFilterKey => $arFilterVal) {
				if (trim($arFilterVal)=="") {unset($arFilter[$arFilterKey]);}
			}
			$arWhere = array();
			foreach ($arFilter as $Key => $arFilterItem) {
				$SubStr2 = substr($Key, 0, 2);
				$SubStr1 = substr($Key, 0, 1);
				$Key = $DB->ForSQL($Key);
				$arFilterItem = $DB->ForSQL($arFilterItem);
				if ($SubStr2==">=" || $SubStr2=="<=") {
					$Val = substr($Key, 2);
					if ($SubStr2 == ">=") {$arWhere[] = "`{$Val}` >= '{$arFilterItem}'";}
					if ($SubStr2 == "<=") {$arWhere[] = "`{$Val}` <= '{$arFilterItem}'";}
				} elseif ($SubStr1==">" || $SubStr1=="<") {
					$Val = substr($Key, 1);
					if ($SubStr1 == ">") {$arWhere[] = "`{$Val}` > '{$arFilterItem}'";}
					if ($SubStr1 == "<") {$arWhere[] = "`{$Val}` < '{$arFilterItem}'";}
					if ($SubStr1 == "!") {$arWhere[] = "`{$Val}` <> '{$arFilterItem}'";}
				} elseif ($SubStr1=="%") {
					$Val = substr($Key, 1);
					$arWhere[] = "upper(`{$Val}`) like upper ('%{$arFilterItem}%') and `{$Val}` is not null";
				} else {
					$arWhere[] = "`{$Key}` = '{$arFilterItem}'";
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
					$SortBy = "`{$arSortKey}`";
					if (trim($arSortItem)!="") {
						$SortBy .= " {$arSortItem}";
					}
					$arSortBy[] = $SortBy;
				}
			}
			$SQL .= implode(", ", $arSortBy);
		}
		return $DB->Query($SQL, true);
	}
	
	/**
	 *	Get by ID
	 */
	function GetByID($ID) {
		return self::GetList(false,array("ID"=>$ID));
	}
	
	/**
	 *	Delete
	 */
	function Delete($ID) {
		global $DB;
		$TableName = self::TableName;
		$SQL = "DELETE FROM `{$TableName}` WHERE `ID`='{$ID}';";
		if ($DB->Query($SQL, true)) {
			return true;
		}
		return false;
	}
	
	/**
	 *	Handler for OnEndBufferContent
	 */
	function OnEndBufferContent(&$Content) {
		global $APPLICATION;
		$CurPage = $APPLICATION->GetCurPage();
		// Handling public page props edit
		if (in_array($CurPage, array('/bitrix/admin/public_file_property.php','/bitrix/admin/public_folder_edit.php'))) {
			self::GetTypes();
			$Content = preg_replace_callback('#<tr style.*?>[\s]{0,}<td class="bx-popup-label bx-width30">(.*?):</td>[\s]{0,}<td>(.*?)</td>[\s]{0,}</tr>#is'.(self::IsUtf8()?'u':''),create_function(
					'$matches',
					'return CWD_Pageprops::WD_ModifyPagePropsDialogReplace($matches);'
			),$Content);
		}
		// Handling settings for module 'fileman'
		if ($CurPage=='/bitrix/admin/settings.php' && $_GET['mid']=='fileman') {
			$JS = '
			var WD_Prop_Mess = {
				"POPUP_TITLE": "'.GetMessage('WD_PAGEPROPS_POPUP_TITLE').'",
				"POPUP_HEAD_1": "'.GetMessage('WD_PAGEPROPS_POPUP_HEAD_1').'",
				"POPUP_HEAD_2": "'.GetMessage('WD_PAGEPROPS_POPUP_HEAD_2').'",
				"POPUP_SAVE": "'.GetMessage('WD_PAGEPROPS_POPUP_SAVE').'",
				"POPUP_CANCEL": "'.GetMessage('WD_PAGEPROPS_POPUP_CANCEL').'",
				"EDIT_LINK_TITLE": "'.GetMessage('WD_PAGEPROPS_EDIT_LINK_TITLE').'",
				"SAVE_ERROR": "'.GetMessage('WD_PAGEPROPS_SAVE_ERROR').'",
			};
			';
			$JS .= @file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.self::ModuleID.'/wb_pageprops_fileman.js');
			$CSS = '
			.wd_pageprops_customize a {background:url(\'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH1gMaDhES2knn6wAAAZtJREFUOMvFk79LW1EUxz/J9WnjEiGCoHMxb3Ko4KKTi7NQUPHXaNyCYKeCIKKEiJqhgg4pKkTx5Q/oVK1VsohaatISB6Voh5rEJEOIL77rEF98QasZBA8cuPdwv9/7Ped+L7x22MzF0vLiYTKVbKkEpCiKNuYdf19W9Pmnb2SFMeObkiauylxIWayl01dP3u501lFz8JG5XrsEBkoENlupG2Kx2KNgVVWZ7xMMLXznbGeTLW1h9YEC8+BjEfS4GA7s8md7lR+xfzeAqFhBJNB+D44nSEbDAmitSEHQ42JwdofTrysc/U6QimoArd6Qsf+sgkignUH/N+Jfgvw6z5KKalw3j/Bh4tM+YLP/b1iqquJ2uzmzt3G8t0f8Ik3qeJOeyQiGo7HkIbu1BSkNrPvPo/Wc/E2woWlc/gzTMRLmjestuq6bFpBWBUahUCgj8IYMOhtO6Ooe5p1nC1nbRDabRdcLAKLMSEWLVuN0VpcIcrkcmUyGfD6PEAJFUe6yCsAoIxBCrPv80/3W1yi2VUzrkB0Oxxqgv8hvvAX0qNjurJSGMwAAAABJRU5ErkJggg==\'); display:inline-block; height:16px; outline:0; text-decoration:none; width:16px;}
			';
			$Content = str_ireplace('</head>','<script>'.$JS.'</script><style>'.$CSS.'</style>',$Content);
		}
	}
	
	/**
	 *	Handler for OnPageStart
	 */
	function OnPageStart() {
		global $APPLICATION;
		$CurPage = $APPLICATION->GetCurPage();
		if ($CurPage=='/bitrix/admin/settings.php' && $_GET['mid']=='fileman') {
			CAjax::Init();
		}
	}
	
	/**
	 *	Замены в окне редактирования значений свойств
	 */
	function WD_ModifyPagePropsDialogReplace($Matches){
		$strData = $Matches[2];
		$PropertyID = false;
		$PropertyCode = false;
		$PropertyValue = false;
		if (preg_match('#<input type="text" name="PROPERTY\[([\d]+)\]\[VALUE\]" value="(.*?)".*?>#',$strData,$M)) {
			$PropertyID = $M[1];
			$PropertyValue = $M[2];
		}
		if (preg_match('#<input.*?name="PROPERTY\[[\d]+\]\[CODE\]" value="(.*?)".*?>#',$strData,$M)) {
			$PropertyCode = $M[1];
		}
		if (preg_match('#<div.*?id="bx_view_property_([\d])+".*?>(.*?)</div>#',$strData,$M)) {
			// If inherited props
			$PropertyID = $M[1];
			$PropertyValue = $M[2];
		}
		$bPropFound = false;
		$NewHTML = self::ShowControls($PropertyCode, $PropertyID, $PropertyValue, $_GET['site']);
		if ($NewHTML===false) {
			return $Matches[0];
		} else {
			$NewHTML .= "<input type=\"hidden\" name=\"PROPERTY[{$PropertyID}][CODE]\" value=\"{$PropertyCode}\" data-value=\"{$PropertyValue}\" />";
		}
		$NewHTML = "<tr><td class=\"bx-popup-label bx-width30\">{$Matches[1]}</td><td>{$NewHTML}</td></tr>";
		return $NewHTML;
	}
	
	/**
	 *	Получение списка классов для реализации типов свойств
	 */
	function GetTypes($Force=false) {
		$arResult = array();
		if (!$Force && isset($GLOBALS['WD_PAGEPROPS_TYPES']) && is_array($GLOBALS['WD_PAGEPROPS_TYPES'])) {
			return $GLOBALS['WD_PAGEPROPS_TYPES'];
		}
		$ProvidersPath = BX_ROOT.'/modules/'.self::ModuleID.'/types/';
		if (is_dir($_SERVER['DOCUMENT_ROOT'].$ProvidersPath)) {
			$Handle = opendir($_SERVER['DOCUMENT_ROOT'].$ProvidersPath);
			while (($File = readdir($Handle))!==false) {
				if ($File != '.' && $File != '..') {
					if (is_file($_SERVER['DOCUMENT_ROOT'].$ProvidersPath.$File)) {
						$arPathInfo = pathinfo($File);
						if (ToUpper($arPathInfo['extension'])=='PHP') {
							require_once($_SERVER['DOCUMENT_ROOT'].$ProvidersPath.$File);
						}
					}
				}
			}
			closedir($Handle);
		}
		foreach(GetModuleEvents(self::ModuleID, 'OnGetTypes', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent);
		}
		$arDeclaredClasses = get_declared_classes();
		foreach($arDeclaredClasses as $ClassName) {
			if (stripos($ClassName,'CWD_Pageprop_')===0) {
				$Code = $ClassName::GetCode();
				$Name = $ClassName::GetName();
				$arResult[$Code] = array(
					'NAME' => $Name,
					'CODE' => $Code,
					'CLASS' => $ClassName,
				);
			}
		}
		$GLOBALS['WD_PAGEPROPS_TYPES'] = $arResult;
		return $arResult;
	}
	
	/**
	 *	Показ настроек
	 */
	function ShowSettings($PropertyCode, $PropertyType=false) {
		$PropertyType = trim($PropertyType);
		$arTypes = self::GetTypes();
		if ($PropertyType!==false && is_array($arTypes[$PropertyType])) {
			$ClassName = $arTypes[$PropertyType]['CLASS'];
			if (class_exists($ClassName) && method_exists($ClassName, 'ShowSettings')) {}
			return $ClassName::ShowSettings($PropertyCode);
		}
	}
	
	/**
	 *	Показ настроек
	 */
	function SaveSettings($PropertyCode, $PropertySite, $PropertyType, $arPost) {
		$PropertyType = trim($PropertyType);
		$arTypes = self::GetTypes();
		if ($PropertyType!==false && is_array($arTypes[$PropertyType])) {
			$ClassName = $arTypes[$PropertyType]['CLASS'];
			if (class_exists($ClassName) && method_exists($ClassName, 'SaveSettings')) {
				return $ClassName::SaveSettings($PropertyCode, $PropertySite, $arPost);
			}
		} elseif ($PropertyType == 'DEFAULT') {
			$arFilter = self::GetFilter($PropertyCode, $SiteID);
			$resCurrentProp = self::GetList(false,$arFilter);
			if ($arCurrentItem = $resCurrentProp->GetNext()) {
				self::Delete($arCurrentItem['ID']);
			}
			return true;
		}
		return false;
	}
	
	/**
	 *	Получение фильтр для поиска нужного элемента
	 */
	function GetFilter($PropertyCode, $SiteID=false) {
		$arResult = array();
		$arResult['PROPERTY'] = $PropertyCode;
		$bDifferentSet = COption::GetOptionString('fileman', 'different_set', 'Y')=='Y';
		if ($bDifferentSet && $SiteID!==false) {
			$arResult['SITE'] = $SiteID;
		}
		return $arResult;
	}
	
	/**
	 *	Замена полей в форме
	 */
	function ShowControls($PropertyCode, $PropertyID, $PropertyValue, $SiteID) {
		global $DB;
		$arFilter = self::GetFilter($PropertyCode, $SiteID);
		$resCurrentProp = self::GetList(false,$arFilter);
		if ($arCurrentItem = $resCurrentProp->GetNext()) {
			$PropertyType = trim($arCurrentItem['TYPE']);
			$arTypes = self::GetTypes();
			if ($PropertyType!==false && is_array($arTypes[$PropertyType])) {
				$ClassName = $arTypes[$PropertyType]['CLASS'];
				if (class_exists($ClassName) && method_exists($ClassName, 'ShowControls')) {
					return $ClassName::ShowControls($arCurrentItem, $PropertyCode, $PropertyID, $PropertyValue, $SiteID);
				}
			}
		}
		return false;
		
	}
	
	function IsUtf8() {
		return defined('BX_UTF') && BX_UTF===true;
	}
	
	function GetSites() {
		$arResult = array();
		$resSites = CSite::GetList($by='sort',$order='asc');
		while ($arSite = $resSites->GetNext()) {
			$arResult[] = $arSite;
		}
		return $arResult;
	}

}

abstract class CWD_PagepropsAll {
	abstract function GetName();
	abstract function GetCode();
	abstract function ShowSettings($PropertyType);
	abstract function SaveSettings($PropertyCode, $SiteID, $arPost);
	abstract function ShowControls($arItem, $PropertyCode, $PropertyID, $PropertyValue, $SiteID);
}







?>