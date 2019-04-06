<?
IncludeModuleLangFile(__FILE__);

class CWD_Reviews2_Tools {
	const ModuleID = 'webdebug.reviews';
	
	/**
	 *	Saving review
	 */
	function SaveReview($InterfaceID, $Target) {
		$arErrors = array();
		$bResult = false;
		$Authorized = $GLOBALS['USER']->IsAuthorized();
		// Simple antibot
		$AntibotFieldName = COption::GetOptionString('webdebug.reviews','antibot_field_name');
		if (strlen($AntibotFieldName)>0 && strlen($_POST[$AntibotFieldName])>0) {
			$bResult = true;
		}
		// Get captcha params
		$arInterfaceFields = false;
		if ($InterfaceID>0) {
			$resInterface = CWD_Reviews2_Interface::GetByID($InterfaceID);
			if ($arInterface = $resInterface->GetNext(false,false)) {
				$arInterfaceFields = $arInterface;
				$bUseCaptcha = $arInterface['CAPTCHA_MODE']=='Y' || ($arInterface['CAPTCHA_MODE']=='U' && !$Authorized);
				if ($bUseCaptcha) {
					if (trim($_POST['captcha_word'])=='') {
						$arErrors[] = GetMessage('WD_REVIEWS2_ERROR_NO_CAPTCHA');
					} else {
						if (!$GLOBALS['APPLICATION']->CaptchaCheckCode(htmlspecialchars($_POST['captcha_word']),htmlspecialchars($_POST['captcha_sid']))) {
							$arErrors[] = GetMessage('WD_REVIEWS2_ERROR_WRONG_CAPTCHA');
						}
					}
				}
				$bAllowUnregistered = $arInterface['ALLOW_UNREGISTERED']=='Y';
				if (!$bAllowUnregistered && !$Authorized) {
					$arErrors[] = GetMessage('WD_REVIEWS2_ERROR_DENIED_UNREGISTERED');
				}
			}
		}
		$SkipSessidCheck = COption::GetOptionString(self::ModuleID,'skip_sessid_check')=='Y';
		if (!$bResult) {
			if (!$SkipSessidCheck && !check_bitrix_sessid('wd_reviews2_review_sessid')) {
				$arErrors[] = GetMessage('WD_REVIEWS2_ERROR_SESSID_EXPIRED');
			}
		}
		if (!$bResult && empty($arErrors)) {
			$FormField = $_POST[$arResult['FORM_FIELD']];
			if (!is_string($FormField) || strlen($FormField)<=0) {
				$FormField = 'review';
			}
			$arFields = $_POST[$FormField];
			$arFields['INTERFACE_ID'] = $InterfaceID;
			$arFields['TARGET'] = $Target;
			$obReviews = new CWD_Reviews2_Reviews;
			$ID = $obReviews->Add($arFields);
			$bResult = $ID>0;
			if ($bResult) {
				$arReview['ID'] = $ID;
				self::SendEmailOnReviewAdd($arReview, $arInterfaceFields);
			}
			if (is_array($obReviews->arLastErrors) && !empty($obReviews->arLastErrors)) {
				$arErrors = array_merge($arErrors, $obReviews->arLastErrors);
			}
		}
		if ($bResult) {
			print '<div id="WD_REVIEWS2_REVIEW_SAVED_SUCCESS" style="display:none"></div>';
			return true;
		}
		if (!empty($arErrors)) {
			?>
			<div class="wdr2_errors">
				<ul>
					<?foreach($arErrors as $strError):?>
						<li><?=$strError;?></li>
					<?endforeach?>
				</ul>
			</div>
			<?
		}
		return false;
	}
	
	/**
	 * Show CAPTCHA, according with the settings 
	 */
	function ShowCaptcha($InterfaceID) {
		if ($InterfaceID>0) {
			$resInterface = CWD_Reviews2_Interface::GetByID($InterfaceID);
			if ($arInterface = $resInterface->GetNext(false,false)) {
				$bUseCaptcha = $arInterface['CAPTCHA_MODE']=='Y' || ($arInterface['CAPTCHA_MODE']=='U' && !$GLOBALS['USER']->IsAuthorized());
				if ($bUseCaptcha) {
					$CaptchaCode = $GLOBALS['APPLICATION']->CaptchaGetCode();
					?><input type="hidden" name="captcha_sid" value="<?=$CaptchaCode;?>" /><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$CaptchaCode;?>" alt="CAPTCHA" height="40" width="180" /><?
				}
			}
		}
	}
	
	/**
	 *	Send e-mail notice on review add
	 */
	function SendEmailOnReviewAdd($arReview, $arInterface) {
		if ($arInterface['EMAIL_ON_ADD']!='Y') {
			return false;
		}
		$ReviewID = $arReview['ID'];
		$Target = $arReview['TARGET'];
		if ($ReviewID>0) {
			$resReview = CWD_Reviews2_Reviews::GetByID($ReviewID);
			if ($arReview = $resReview->GetNext()) {
				$arFields = unserialize($arReview['~DATA_FIELDS']);
				if (is_array($arFields)) {
					$ReviewField = false;
					$EmailField = false;
					$NameField = false;
					$resFields2 = CWD_Reviews2_Fields::GetList(array('SORT'=>'ASC'),array('INTERFACE_ID'=>$arReview['INTERFACE_ID']));
					$Review = false;
					$Name = false;
					$Email = false;
					while ($arFields2 = $resFields2->GetNext()) {
						$arFields2['PARAMS'] = unserialize($arFields2['~PARAMS']);
						if ($arFields2['PARAMS']['is_review']=='Y') {
							$ReviewField = $arFields2['CODE'];
						}
						if ($arFields2['PARAMS']['is_name']=='Y') {
							$NameField = $arFields2['CODE'];
						}
						if ($arFields2['PARAMS']['is_email']=='Y') {
							$EmailField = $arFields2['CODE'];
						}
					}
					$arEventFields = array();
					$Email = false;
					foreach($arFields as $Key => $Value) {
						$arEventFields['F_'.$Key] = $Value;
						if ($ReviewField==$Key) {
							$Review = $Value;
						}
						if ($NameField==$Key) {
							$Name = $Value;
						}
						if ($EmailField==$Key) {
							$Email = $Value;
						}
					}
					$arEventFields['USER_REVIEW'] = $Review;
					$arEventFields['USER_NAME'] = $Name;
					$arEventFields['USER_EMAIL'] = $Email;
					$arEventFields['USER_ID'] = $arFields['USER_ID'];
					$arEventFields['DATETIME'] = date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
					$arEventFields['REVIEW_ID'] = $ReviewID;
					$arEventFields['INTERFACE_ID'] = $arReview['INTERFACE_ID'];
					$arEventFields['TARGET_URL'] = CWD_Reviews2_Reviews::GetReviewURL($arReview);
					//
					$EventTypeSuffix = $arInterface['PRE_MODERATION']=='Y' ? '_M_' : '_N_';
					$EventType = CWD_Reviews2_Interface::EventTypePrefix.$EventTypeSuffix.$arReview['INTERFACE_ID'];
					//
					$arEventTemplates = CWD_Reviews2::GetEventTemplatesWithNonemptyReceiver($EventType,$arEventFields,SITE_ID);
					if (is_array($arEventTemplates)) {
						foreach($arEventTemplates as $intEventTemplate) {
							CEvent::Send($EventType, SITE_ID, $arEventFields, 'Y', $intEventTemplate);
						}
						CEvent::CheckEvents();
					}
				}
			}
		}
	}
	
	/**
	 *	Voting for the review in public
	 */
	function VoteReview($InterfaceID, $Target, $ReviewID, $Amount){
		global $USER;
		$arResult = array();
		$arResult['review'] = $ReviewID;
		$arResult['success'] = false;
		$bAllowUnregVoting = COption::GetOptionString(self::ModuleID, 'allow_unreg_voting')=='Y';
		if (!$bAllowUnregVoting && !$USER->IsAuthorized()) {
			$arResult['error_message'] = 'AUTH_ERROR';
			return $arResult;
		}
		if (!CWD_Reviews2_Vote::UserCanVote($ReviewID, $bAllowUnregVoting)) {
			$arResult['error_message'] = 'YOU_CANNOT_VOTE';
			return $arResult;
		}
		$resReview = CWD_Reviews2_Reviews::GetList(false,array('ID'=>$ReviewID));
		if ($arReview = $resReview->GetNext(false,false)) {
			if ($arReview['INTERFACE_ID']==$InterfaceID && $arReview['TARGET']==$Target) {
				$arResult['flag'] = 'n';
				if ($Amount!='-1') {
					$arResult['flag'] = 'y';
				}
				if (!CWD_Reviews2_Vote::SaveVote($ReviewID, $arResult['flag']=='n'?false:true, $bAllowUnregVoting)) {
					$arResult['error_message'] = 'VOTE_ERROR';
					return $arResult;
				}
				$resReview = CWD_Reviews2_Reviews::GetList(false,array('ID'=>$ReviewID));
				if ($arReview = $resReview->GetNext(false,false)) {
					$arResult['value'] = $arResult['flag']=='n'?$arReview['VOTES_N']:$arReview['VOTES_Y'];
				}
				$arResult['success'] = true;
				return $arResult;
			}
		}
		return false;
	}
	
	/**
	 *	Delete review (at the moment only for admins in public)
	 */
	function DeleteReview($InterfaceID, $Target, $ReviewID) {
		global $USER;
		if (!$USER->IsAdmin()) {
			return false;
		}
		$resReview = CWD_Reviews2_Reviews::GetList(false,array('ID'=>$ReviewID));
		if ($arReview = $resReview->GetNext(false,false)) {
			if ($arReview['INTERFACE_ID']==$InterfaceID && $arReview['TARGET']==$Target) {
				$obReviews = new CWD_Reviews2_Reviews;
				if ($obReviews->Delete($ReviewID)) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 *	Show reviews list (autoloading)
	 */
	function ShowReviewsList() {
		$arParams = $_GET;
		$TemplateName = $_GET['template'];
		unset($arParams['interface'],$arParams['action'],$_GET['template']);
		$arParams['CACHE_TYPE'] = 'N';
		$arParams['AUTO_LOADING'] = 'N';
		$GLOBALS['WD_REVIEWS2_ACTION'] = 'GET_ACTUAL_LIST';
		// Save original values...
		$arGet = $_GET;
		$RequestUri = $_SERVER['REQUEST_URI'];
		$ScriptName = $_SERVER['SCRIPT_NAME'];
		// ... and replace its by own values (for correct page navigation)
		$_SERVER['REQUEST_URI'] = urldecode($_GET['path1']);
		$_SERVER['SCRIPT_NAME'] = urldecode($_GET['path2']);
		$UrlParams = $_SERVER['REQUEST_URI'];
		$Pos = strpos($UrlParams, '?');
		if ($Pos!==false) {
			$UrlParams = substr($UrlParams,$Pos+1);
		}
		$arGetNew = array();
		parse_str($UrlParams,$arGetNew);
		$_GET = $arGetNew;
		// IncludeComponent
		global $APPLICATION;
		$APPLICATION->IncludeComponent('webdebug:reviews2.list',$TemplateName,$arParams,false,array('HIDE_ICONS'=>'Y'));
		// Restore original values
		$_GET = $arGet;
		$_SERVER['REQUEST_URI'] = $RequestUri;
		$_SERVER['SCRIPT_NAME'] = $ScriptName;
	}
	
}
?>