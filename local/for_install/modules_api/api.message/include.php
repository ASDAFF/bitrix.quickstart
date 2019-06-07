<?
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;

class CApiMessage
{

	/**
	 * Format user name
	 *
	 * @param      $userId
	 * @param bool $bEnableId
	 * @param bool $createEditLink
	 *
	 * @return array|string
	 */
	public static function getFormatedUserName($userId, $bEnableId = true, $createEditLink = true)
	{
		static $formattedUsersName = array();
		static $siteNameFormat = '';

		$result   = (!is_array($userId)) ? '' : array();
		$newUsers = array();

		if(is_array($userId)) {
			foreach($userId as $id) {
				if(!isset($formattedUsersName[ $id ]))
					$newUsers[] = $id;
			}
		}
		else if(!isset($formattedUsersName[ $userId ])) {
			$newUsers[] = $userId;
		}

		if(count($newUsers) > 0) {
			$resUsers = \Bitrix\Main\UserTable::getList(
				 array(
						'select' => array('ID', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL'),
						'filter' => array('ID' => $newUsers),
				 )
			);
			while($arUser = $resUsers->Fetch()) {
				if(strlen($siteNameFormat) == 0)
					$siteNameFormat = CSite::GetNameFormat(false);
				$formattedUsersName[ $arUser['ID'] ] = CUser::FormatName($siteNameFormat, $arUser, true, true);
			}
		}

		if(is_array($userId)) {
			foreach($userId as $uId) {
				$formatted = '';
				if($bEnableId)
					$formatted = '[<a href="/bitrix/admin/user_edit.php?ID=' . $uId . '&lang=' . LANGUAGE_ID . '">' . $uId . '</a>] ';

				if(CBXFeatures::IsFeatureEnabled('SaleAccounts') && !$createEditLink)
					$formatted .= '<a href="/bitrix/admin/sale_buyers_profile.php?USER_ID=' . $uId . '&lang=' . LANGUAGE_ID . '">';
				else
					$formatted .= '<a href="/bitrix/admin/user_edit.php?ID=' . $uId . '&lang=' . LANGUAGE_ID . '">';
				$formatted .= $formattedUsersName[ $uId ];

				$formatted .= '</a>';

				$result[ $uId ] = $formatted;
			}
		}
		else {
			if($bEnableId)
				$result .= '[<a href="/bitrix/admin/user_edit.php?ID=' . $userId . '&lang=' . LANGUAGE_ID . '">' . $userId . '</a>] ';

			if(CBXFeatures::IsFeatureEnabled('SaleAccounts') && !$createEditLink)
				$result .= '<a href="/bitrix/admin/sale_buyers_profile.php?USER_ID=' . $userId . '&lang=' . LANGUAGE_ID . '">';
			else
				$result .= '<a href="/bitrix/admin/user_edit.php?ID=' . $userId . '&lang=' . LANGUAGE_ID . '">';

			$result .= $formattedUsersName[ $userId ];

			$result .= '</a>';
		}

		return $result;
	}

	//---------- Events ----------//

	public static function onProlog()
	{
		if(defined("ADMIN_SECTION") || ADMIN_SECTION === true)
			return;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		//$server  = $context->getServer();

		//Заблокирует многократный вызов jQuery каким-нибудь аяксом в каталоге, который использует прямой вызов
		//require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
		if($request->isPost() || $request->isAjaxRequest()) //|| ($server->get('HTTP_ACCEPT') == '*/*') - это прилетает в ajax-запросах и композите
			return;

		$arConfig = \Api\Message\ConfigTable::getData(SITE_ID);

		$cookiePrefix  = $arConfig['COOKIE_NAME'];
		//Время жизни куки 1 год по умолчанию
		$expires = (date('L') + 365)*24;

		if($arConfig['USE_JQUERY'])
			CJSCore::Init(array($arConfig['USE_JQUERY']));

		if($arMessages = \Api\Message\MessageTable::getData(SITE_ID, $arConfig)) {
			$GLOBALS["APPLICATION"]->SetAdditionalCSS('/bitrix/css/api.message/init.css');

			$useButton = false;
			$html      = '';
			foreach($arMessages as $mId => $arMess) {

				if($arMess['CLOSE_CLASS']) {
					$useButton = true;
				}

				$inner_style = ($arMess['TYPE'] == 'custom' && $arMess['COLOR'] ? 'background: ' . trim($arMess['COLOR']) : '');


				//---------- CONDITIONS ----------//
				$bShow = true;

				if($arMess['PAGE_URL']) {
					if(!self::getPageUrl($arMess['PAGE_URL']))
						$bShow = false;
				}

				if($arMess['GROUP_ID']) {
					$arGroups = explode(',', $arMess['GROUP_ID']);
					if(!CSite::InGroup($arGroups))
						$bShow = false;
				}

				if($arMess['USER_ID']) {
					$arUserId = explode(',', trim($arMess['USER_ID']));
					$userId = intval($GLOBALS["USER"]->GetId());
					if($arUserId){
						if(!in_array($userId,$arUserId))
							$bShow = false;
					}
				}


				//---------- VIEW ----------//
				if(intval($arMess['EXPIRES'])>0)
					$expires = intval($arMess['EXPIRES']);

				$id   = 'ASM_' . ToUpper($mId);
				$type = $arMess['TYPE'];

				if($bShow && !$GLOBALS["APPLICATION"]->get_cookie($id, $cookiePrefix)) {
					$html .= '<div id="' . $id . '" class="asm-alert asm-alert-' . $type . '">';

					$html .= '<div class="asm-inner" style="' . $inner_style . '">';
					$html .= '<div class="asm-content">';

					$html .= '<div class="asm-row">' . $arMess['MESSAGE'] . '</div>' . "\n";

					//Close button
					if($arMess['BLOCK'] != 'Y') {
						$closeHtml = '<span class="asm-close-icon"></span>';
						if(strlen($arMess['CLOSE_TEXT']) > 0)
							$closeHtml = '<span class="' . trim($arMess['CLOSE_CLASS']) . '">' . trim($arMess['CLOSE_TEXT']) . '</span>';

						$html .= '<div class="asm-close" data-expires="'. $expires .'">' . $closeHtml . '</div>';
					}

					$html .= '</div></div></div>';
				}
			}

			if($useButton && Loader::includeModule('api.core')) {
				CUtil::InitJSCore(array('api_button'));
			}

			if(strlen($html) > 0) {

				$js = "
				<script type='text/javascript'>
					jQuery(function ($) {
					
						$('body').prepend('" . CUtil::JSEscape($html) . "');
						$('.asm-alert').show();

						$('.asm-alert .asm-close').on('click',function(){
							var ASM_ID = $(this).closest('.asm-alert').attr('id');
							$('#'+ASM_ID).slideUp(100);
			            //set cookie
			            var exdate = new Date();
			            var expires = ". $expires .";
			            
			            if($(this).data('expires')){
			              expires = $(this).data('expires');
			            }
			            exdate.setHours(exdate.getHours() + expires);
			            //exdate.setDate(exdate.getDate() + 1);
			            document.cookie=\"" . $cookiePrefix . "_\"+ASM_ID+\"=1; path=/; expires=\" + exdate.toUTCString();
						});
					});
				</script>
				";

				$GLOBALS["APPLICATION"]->AddHeadString($js, true);
			}
		}
	}

	protected static function getPageUrl($urlList)
	{
		if(!is_array($urlList))
			$urlList = explode("\n", $urlList);

		if($urlList)
			$urlList = array_diff((array)$urlList, array(''));

		if($urlList) {
			foreach($urlList as $url) {
				if($url = trim($url)){
					if(strpos($url,'*') !== false){
						if(preg_match('#' . $url . '#i' . BX_UTF_PCRE_MODIFIER, $GLOBALS["APPLICATION"]->GetCurUri()))
							return true;
					}
					else{
						if($GLOBALS["APPLICATION"]->GetCurPageParam('',self::getSystemParameters()) == $url)
							return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Returns the array with predefined query parameters.
	 * @return array
	 */
	protected static function getSystemParameters()
	{
		static $params = array(
			 "login",
			 "login_form",
			 "logout",
			 "register",
			 "forgot_password",
			 "change_password",
			 "confirm_registration",
			 "confirm_code",
			 "confirm_user_id",
			 "bitrix_include_areas",
			 "clear_cache",
			 "show_page_exec_time",
			 "show_include_exec_time",
			 "show_sql_stat",
			 "show_cache_stat",
			 "show_link_stat",
			 "sessid",
		);
		return $params;
	}

}

?>