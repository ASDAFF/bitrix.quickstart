<?
class CWD_Reviews2 {
	const ModuleID = 'webdebug.reviews';
	
	/**
	 *	Get bitrix site array
	 */
	function GetSitesList($OnlyID=false) {
		$arResult = array();
		$resSites = CSite::GetList($SiteBy='SORT',$SiteOrder='ASC');
		while ($arSite = $resSites->GetNext()) {
			$arResult[] = $OnlyID ? $arSite['ID'] : $arSite;
		}
		return $arResult;
	}
	
	/**
	 *	Check if bitrix works in UTF-8 mode
	 */
	function IsUtf8() {
		return defined('BX_UTF') && BX_UTF===true;
	}
	
	/**
	 *	Sort callback for SORT key
	 */
	function uasort($a,$b) {
		return $a['SORT']==$b['SORT'] ? 0 : ($a['SORT']<$b['SORT'] ? -1 : 1);
	}

	/**
	 *	Show settings in field edit page
	 */
	function ShowSettings($PropertyType, $arSavedValues) {
		$PropertyType = trim($PropertyType);
		$arTypes = WDR2_GetFieldTypes();
		if ($PropertyType!==false && is_array($arTypes[$PropertyType])) {
			$ClassName = $arTypes[$PropertyType]['CLASS'];
			if (class_exists($ClassName) && method_exists($ClassName, 'ShowSettings')) {
				return $ClassName::ShowSettings($arSavedValues);
			}
		}
	}
	
	/**
	 *	Show input
	 */
	function ShowField($Value, $arParams, $InputName=false) {
		if (!is_array($arParams) || !isset($arParams['TYPE'])) {
			return false;
		}
		$PropertyType = trim($arParams['TYPE']);
		$arTypes = WDR2_GetFieldTypes();
		if ($PropertyType!==false && is_array($arTypes[$PropertyType])) {
			$ClassName = $arTypes[$PropertyType]['CLASS'];
			if (class_exists($ClassName) && method_exists($ClassName, 'ShowSettings')) {
				return $ClassName::Show($Value, $arParams, $InputName);
			}
		}
	}
	
	/**
	 *	Show rating
	 */
	function ShowRating($Value, $arParams, $Template='') {
		++$GLOBALS['WD_REVIEWS2_RATING_INDEX'];
		ob_start();
		$GLOBALS['APPLICATION']->IncludeComponent(
			'webdebug:reviews2.stars',
			trim($Template),
			array(
				'INTERFACE_ID' => $arParams['INTERFACE_ID'],
				'INPUT_NAME' => $arParams['INPUT_NAME'],
				'VALUE' => $Value>0 ? $Value : ($Value===0 || $Value==='0' ? 0 : ''),
				'READ_ONLY' => $arParams['READ_ONLY'],
				'UNIQ_ID' => ToLower(MD5($GLOBALS['APPLICATION']->GetCurPageParam().'_'.$GLOBALS['WD_REVIEWS2_RATING_INDEX'])),
				'SCHEMA_ORG' => $arParams['SCHEMA_ORG']=='Y'?'Y':'N',
				'COUNT' => IntVal($arParams['COUNT']),
			),
			false,
			array('HIDE_ICONS'=>'Y')
		);
		$HTML = ob_get_clean();
		$HTML = str_replace(array("\r","\n","\t"),'',$HTML);
		$HTML = str_replace('//<![CDATA[','//<![CDATA['."\n",$HTML);
		$HTML = str_replace('//]]>',"\n".'//]]>',$HTML);
		return $HTML;
	}
	
	/**
	 *	@returns error text, if error,
	 *	@returns false, if no error
	 */
	function CheckFieldError($arFields, $Value) {
		$Type = $arFields['TYPE'];
		$arTypes = WDR2_GetFieldTypes();
		if (strlen($Type) && is_array($arTypes[$Type])) {
			$ClassName = $arTypes[$Type]['CLASS'];
			if (class_exists($ClassName) && method_exists($ClassName, 'CheckFieldError')) {
				return $ClassName::CheckFieldError($arFields, $Value);
			}
		}
		return false;
	}
	
	/**
	 *	Show error via CAdminMessage
	 */
	function ShowError($Message) {
		if (defined('ADMIN_SECTION')&&ADMIN_SECTION===true) {
			$Message = new CAdminMessage(array(
				'MESSAGE' => $Message,
				'TYPE' => 'ERROR',
			));
			print $Message->Show();
		} else {
			print '<div class="wdr2_error_text" style="color:red;">'.$Message.'</div>';
		}
	}
	
	/**
	 *	Get right form of word depending of value
	 */
	function WordForm($Value, $arWord) {
		$Value = trim($Value);
		$LastSymbol = substr($Value,-1);
		$SubLastSymbol = substr($Value,-2,1);
		if (strlen($Value)>=2 && $SubLastSymbol == '1') {
			return $arWord['5'];
		} else {
			if ($LastSymbol=='1')
				return $arWord['1'];
			elseif ($LastSymbol >= 2 && $LastSymbol <= 4)
				return $arWord['2'];
			else
				return $arWord['5'];
		}
	}
	
	/**
	 *	Process text for security purposes
	 */
	function ProtectText($Text) {
		$u = defined('BX_UTF') && BX_UTF===true ? 'u' : '';
		if (!function_exists('wd_reviews2_func_protect_text_helper1')) {
			function wd_reviews2_func_protect_text_helper1($Match) {
				$Text = trim(substr($Match[1],1));
				if (preg_match('#rel([\s\t\n]{0,})=([\s\t\n"\']{0,})nofollow#is'.$u, $Text)) {
					return '<a '.$Text.'>';
				} else {
					return '<a rel="nofollow" '.$Text.'>';
				}
			}
		}
		if (!function_exists('wd_reviews2_func_protect_text_helper2')) {
			function wd_reviews2_func_protect_text_helper2($Match) {
				$Text = trim(substr($Match[1],1));
				if (preg_match('#target([\s\t\n]{0,})=([\s\t\n"\']{0,})_blank#is'.$u, $Text)) {
					return '<a '.$Text.'>';
				} else {
					return '<a target="_blank" '.$Text.'>';
				}
			}
		}
		$Text = preg_replace_callback('#<(a.*?</a)>#is'.$u, 'wd_reviews2_func_protect_text_helper1', $Text);
		$Text = preg_replace_callback('#<(a.*?</a)>#is'.$u, 'wd_reviews2_func_protect_text_helper2', $Text);
		$Text = strip_tags($Text, '<a><blockquote><div><ol><ul><li><b><strong><strike><i><u><font><span><sup><sub><hr>');
		return $Text;
	}
	
	/**
	 *	Check, if need to include jquery
	 */
	function CheckInitJQuery() {
		$u = defined('BX_UTF') && BX_UTF===true ? 'u' : '';
		if (defined('ADMIN_SECTION') && ADMIN_SECTION===true) {
			return false;
		}
		$bAutoJQuery = COption::GetOptionString(CWD_Reviews2::ModuleID,'auto_jquery')=='Y';
		if (!$bAutoJQuery) {
			return false;
		}
		$resInterfaces = CWD_Reviews2_Interface::GetList(array('SORT'=>'ASC','ID'=>'ASC'),array('!JQUERY_INIT_URL'=>''));
		while ($arInterface = $resInterfaces->GetNext()) {
			$arInterface['JQUERY_INIT_URL'] = trim($arInterface['JQUERY_INIT_URL']);
			if ($arInterface['JQUERY_INIT_URL']!='') {
				$arUrls = explode("\n", $arInterface['JQUERY_INIT_URL']);
				foreach($arUrls as $arUrl) {
					if (trim($arUrl)=='') {
						continue;
					}
					if (preg_match('#'.$arUrl.'#is'.$u,$_SERVER['REQUEST_URI'])) {
						self::InitJQuery();
						break 2;
					}
				}
			}
		}
	}
	
	/**
	 *	Include jQuery and Raty on OnEpilog event
	 */
	function InitJQueryEpilog() {
		if ($GLOBALS['WD_REVIEWS2_INCLUDE_JQUERY_CORE']) {
			CJSCore::Init(array('jquery'));
		}
		$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/js/webdebug.reviews/jquery-raty-2.7.0.min.js', true);
	}
	
	/**
	 *	Include jQuery and Raty
	 */
	function InitJQuery($IncludeJQueryCore=true) {
		if ($GLOBALS['WD_REVIEWS2_INCLUDED_JQUERY']) {
			return false;
		}
		$GLOBALS['WD_REVIEWS2_INCLUDE_JQUERY_CORE'] = $IncludeJQueryCore;
		$UseEpilog = COption::GetOptionString(self::ModuleID,'use_epilog_for_scripts_include','N')=='Y';
		if ($UseEpilog) {
			AddEventHandler('main','OnEpilog',array('CWD_Reviews2','InitJQueryEpilog'));
		} else {
			self::InitJQueryEpilog();
		}
		$GLOBALS['WD_REVIEWS2_INCLUDED_JQUERY'] = true;
	}
	
	/**
	 *	Get empty date with multiple zero
	 */
	function GetZeroDate() {
		$CheckDate = date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
		$CheckDate = preg_replace('#(\d)#','0',$CheckDate);
		return $CheckDate;
	}
	
	/**
	 *	Analog to json_encode
	 */
	function JsonEncode( $data ) {            
		if( is_array($data) || is_object($data) ) { 
			$islist = is_array($data) && ( empty($data) || array_keys($data) === range(0,count($data)-1) );
			if( $islist ) { 
				$json = '[' . implode(',', array_map('self::JsonEncode', $data) ) . ']'; 
			} else { 
				$items = Array(); 
				foreach( $data as $key => $value ) { 
					$items[] = self::JsonEncode($key) . ':' . self::JsonEncode($value); 
				} 
				$json = '{' . implode(',', $items) . '}'; 
			} 
		} elseif( is_string($data) ) {
			$string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"'; 
			$json  = ''; 
			$len = strlen($string); 
			for( $i = 0; $i < $len; $i++ ) {
				$char = $string[$i]; 
				$c1 = ord($char); 
				if( $c1 <128 ) { 
					$json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1); 
					continue; 
				}
				$c2 = ord($string[++$i]); 
				if ( ($c1 & 32) === 0 ) { 
					$json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128); 
					continue; 
				}
				$c3 = ord($string[++$i]); 
				if( ($c1 & 16) === 0 ) { 
					$json .= sprintf("\\u%04x", (($c1 - 224) <<12) + (($c2 - 128) << 6) + ($c3 - 128)); 
					continue; 
				}
				$c4 = ord($string[++$i]); 
				if( ($c1 & 8 ) === 0 ) { 
					$u = (($c1 & 15) << 2) + (($c2>>4) & 3) - 1; 
					$w1 = (54<<10) + ($u<<6) + (($c2 & 15) << 2) + (($c3>>4) & 3); 
					$w2 = (55<<10) + (($c3 & 15)<<6) + ($c4-128); 
					$json .= sprintf("\\u%04x\\u%04x", $w1, $w2); 
				} 
			} 
		} else { 
			$json = strtolower(var_export( $data, true )); 
		} 
		return $json; 
	}
	
	/**
	 * Funtions get event templates with right email
	 */
	function GetEventTemplatesWithNonemptyReceiver($EventType, $arFields, $SiteID=false) {
		$arResult = array();
		$resEventTemplates = CEventMessage::GetList($by='ID',$order='ASC',array('TYPE_ID'=>$EventType));
		if (defined('ADMIN_SECTION') && ADMIN_SECTION===true) {
			$arFields['DEFAULT_EMAIL_FROM'] = COption::GetOptionString('main','email_from');
		} elseif($SiteID!==false) {
			$arSites = self::GetSitesList();
			if (is_array($arSites)) {
				foreach($arSites as $arSite) {
					if ($arSite['LID']==$SiteID) {
						$arFields['DEFAULT_EMAIL_FROM'] = $arSite['EMAIL'];
					}
				}
			}
		}
		while ($arEventTemplate = $resEventTemplates->GetNext(false,false)) {
			$EmailTo = $arEventTemplate['EMAIL_TO'];
			foreach($arFields as $Key => $Value) {
				$EmailTo = str_replace('#'.$Key.'#', $Value, $EmailTo);
			}
			if (check_email($EmailTo)) {
				$arResult[] = $arEventTemplate['ID'];
			}
		}
		return $arResult;
	}
	
}

?>