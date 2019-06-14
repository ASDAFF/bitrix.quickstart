<?

use Bitrix\Main\Loader,
	 Bitrix\Main\Application,
	 Bitrix\Main\UserTable,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var string           $templateFile
 * @var string           $templateFolder
 *
 * @var string           $parentComponentPath
 * @var string           $parentComponentName
 * @var string           $parentComponentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 */

//ID компонента
//$cpId = $this->getEditAreaId($this->__currentCounter);

//Объект родительского компонента
//$parent = $this->getParent();
//$parentPath = $parent->getPath();

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.reviews')) {
	ShowError(Loc::getMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

use Api\Reviews\ReviewsTable,
	 Api\Reviews\Converter,
	 Api\Reviews\VideoTable,
	 Api\Reviews\Component,
	 Api\Reviews\Tools;

//if(Loader::includeModule('api.core')){
//	CUtil::InitJSCore(array('api_easypiechart','api_button'));
//}

class ApiReviewsUserComponent extends Component
{
	public function onPrepareComponentParams($arParams)
	{
		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		$server  = $context->getServer();

		//Inc template lang
		if($this->initComponentTemplate()) {
			Loc::loadMessages($server->getDocumentRoot() . $this->getTemplate()->GetFile());
		}

		$arParams['HTTP_HOST'] = ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost();
		$arParams['SITE_ID']   = SITE_ID;

		$arParams['ITEMS_LIMIT']    = ($arParams['ITEMS_LIMIT'] ? $arParams['ITEMS_LIMIT'] : 10);
		$arParams['DISPLAY_FIELDS'] = $arParams['~FORM_DISPLAY_FIELDS'];

		$arParams['SHOW_THUMBS'] = $arParams['LIST_SHOW_THUMBS'] == 'Y';

		//MULTILANGUAGE PHRASES REPLACE
		$arParams['SHOP_NAME']           = $arParams['~SHOP_NAME'] ? $arParams['~SHOP_NAME'] : Loc::getMessage('API_REVIEWS_LIST_SHOP_NAME');
		$arParams['SHOP_NAME_REPLY']     = $arParams['~LIST_SHOP_NAME_REPLY'] ? $arParams['~LIST_SHOP_NAME_REPLY'] : Loc::getMessage('API_REVIEWS_LIST_SHOP_NAME_REPLY');
		$arParams['MESS_TRUE_BUYER']     = $arParams['~LIST_MESS_TRUE_BUYER'] ? $arParams['~LIST_MESS_TRUE_BUYER'] : Loc::getMessage('API_REVIEWS_LIST_MESS_TRUE_BUYER');
		$arParams['MESS_HELPFUL_REVIEW'] = $arParams['~LIST_MESS_HELPFUL_REVIEW'] ? $arParams['~LIST_MESS_HELPFUL_REVIEW'] : Loc::getMessage('API_REVIEWS_LIST_MESS_HELPFUL_REVIEW');

		//ADDITIONAL PARAMS
		$arParams['SET_TITLE']     = $arParams['LIST_SET_TITLE'] == 'Y';
		$arParams['BROWSER_TITLE'] = Loc::getMessage('API_REVIEWS_USER_BROWSER_TITLE');
		$arParams['INCLUDE_CSS']   = $arParams['INCLUDE_CSS'] == 'Y';
		$arParams['THEME']         = ($arParams['THEME'] ? $arParams['THEME'] : 'flat');

		return $arParams;
	}

	public function executeComponent()
	{
		global $USER, $APPLICATION;

		$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		$userId = $arParams['ID'];

		$arUser = UserTable::getRow(array(
			 'filter' => array('=ID' => $userId),
			 'select' => array('ID', 'TITLE', 'NAME', 'LAST_NAME', 'EMAIL', 'SECOND_NAME', 'LOGIN', 'PERSONAL_PHOTO', 'PERSONAL_NOTES'),
		));

		$arResult['USER'] = array();
		if($arUser) {

			$siteNameFormat        = \CSite::GetNameFormat(false);
			$arUser['FORMAT_NAME'] = \CUser::FormatName($siteNameFormat, $arUser, true, true);

			if($arUser['PERSONAL_PHOTO']) {

				$bigPic = \CFile::GetFileArray($arUser['PERSONAL_PHOTO']);

				/*if($bigPic['SRC']) {
					$bigPic['SRC'] = \CUtil::GetAdditionalFileURL($bigPic['SRC'], true);
				}*/

				$arFileTmp = \CFile::ResizeImageGet(
					 $bigPic,
					 array("width" => 300, "height" => 300)
				);

				if($arFileTmp['src'])
					$arFileTmp['src'] = \CUtil::GetAdditionalFileURL($arFileTmp['src'], true);

				$smallPic = array_change_key_case($arFileTmp, CASE_UPPER);

				//getGravatar
				if(!$bigPic) {
					$bigPic = array(
						 'SRC' => '/bitrix/images/api.reviews/userpic.png?v=1',
					);
				}
				if(!$smallPic) {
					$smallPic = array(
						 'SRC' => '/bitrix/images/api.reviews/userpic.png?v=1',
					);
				}

				$arUser['BIG_PICTURE']   = $bigPic;
				$arUser['SMALL_PICTURE'] = $smallPic;
			}

			$arResult['USER'] = $arUser;

			$parameters = array(
				 'order'  => array('ACTIVE_FROM' => 'DESC', 'ID' => 'DESC'),
				 'select' => array('*'),
				 'filter' => array(
						'=ACTIVE'  => 'Y',
						'=USER_ID' => $userId,
						'=SITE_ID' => SITE_ID,
				 ),
			);

			$this->getReviewsList($parameters);

			$this->includeComponentTemplate();

			if($arParams['SET_TITLE']) {
				$arParams['PAGE_TITLE']    = $arUser['FORMAT_NAME'];
				$arParams['BROWSER_TITLE'] = str_replace(
					 array('#FORMAT_NAME#', '#SHOP_NAME#'),
					 array($arUser['FORMAT_NAME'], $arParams['SHOP_NAME']),
					 $arParams['BROWSER_TITLE']
				);

				$APPLICATION->SetTitle($arParams['PAGE_TITLE']);
				$APPLICATION->SetPageProperty("title", $arParams['BROWSER_TITLE']);
				$APPLICATION->AddChainItem($arParams['PAGE_TITLE']);
			}
		}
		else {
			//Выводим 404 страницу
			Tools::send404(
				 trim($arParams["MESSAGE_404"]) ?: Loc::getMessage('API_REVIEWS_STATUS_404')
				 , true
				 , $arParams["SET_STATUS_404"] === "Y"
				 , $arParams["SHOW_404"] === "Y"
				 , $arParams["FILE_404"]
			);
		}
	}
}