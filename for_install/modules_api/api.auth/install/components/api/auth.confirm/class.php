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

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.auth')) {
	ShowError(Loc::getMessage('API_QA_MODULE_ERROR'));
	return;
}


class ApiAuthConfirmComponent extends \CBitrixComponent
{
	public function onPrepareComponentParams($params)
	{
		//$request = &$this->request;

		//$arParams['HTTP_HOST'] = ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost();
		//$arParams['SITE_ID']    = SITE_ID;

		return $params;
	}

	public function executeComponent()
	{
		global $USER, $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		$server  = $context->getServer();

		if($this->initComponentTemplate()) {
			Loc::loadMessages($server->getDocumentRoot() . $this->getTemplate()->GetFile());
		}

		$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		$arResult['FORM_ID'] = $this->getEditAreaId($this->randString());

		//---------- $request ----------//
		$userId      = intval($request->get('uid') ? $request->get('uid') : $request->get('confirm_user_id'));
		$confirmCode = trim($request->get('code') ? $request->get('code') : $request->get('confirm_code'));

		$arMessage = array();

		if($USER->IsAuthorized()) {
			$arMessage = array(
				 'TYPE' => 'success',
				 'TEXT' => Loc::GetMessage('CC_BSAC_MESSAGE_E02'),
			);
		}
		else {

			$arUser = UserTable::getRow(array(
				 'select' => array('ID', 'ACTIVE', 'CONFIRM_CODE'),
				 'filter' => array('=ID' => $userId),
			));

			if($arUser) {
				if($arUser['ACTIVE'] == 'Y') {
					$arMessage = array(
						 'TYPE' => 'warning',
						 'TEXT' => Loc::GetMessage('CC_BSAC_MESSAGE_E03'),
					);
				}
				else {
					if(strlen($confirmCode) <= 0) {
						$arMessage = array(
							 'TYPE' => 'warning',
							 'TEXT' => Loc::GetMessage('CC_BSAC_MESSAGE_E04'),
						);
					}
					elseif($confirmCode !== $arUser['CONFIRM_CODE']) {
						$arMessage = array(
							 'TYPE' => 'danger',
							 'TEXT' => Loc::GetMessage('CC_BSAC_MESSAGE_E05'),
						);
					}
					else {

						$obUser = new CUser;
						$result = $obUser->Update($arUser['ID'], array('ACTIVE' => 'Y', 'CONFIRM_CODE' => ""));

						if($result) {
							$arMessage = array(
								 'TYPE' => 'success',
								 'TEXT' => Loc::GetMessage('CC_BSAC_MESSAGE_E06'),
							);
						}
						else {
							$arMessage = array(
								 'TYPE' => 'danger',
								 'TEXT' => Loc::GetMessage('CC_BSAC_MESSAGE_E07'),
							);
						}
					}
				}
			}
			else {
				$arMessage = array(
					 'TYPE' => 'warning',
					 'TEXT' => Loc::GetMessage('CC_BSAC_MESSAGE_E01'),
				);
			}
		}

		$arResult['MESSAGE'] = $arMessage;


		$this->includeComponentTemplate();

		if($arParams['SET_TITLE'] == 'Y') {
			$pageTitle = Loc::getMessage('API_AUTH_CONFIRM_PAGE_TITLE');

			$APPLICATION->SetTitle($pageTitle);
			$APPLICATION->SetPageProperty('title', $pageTitle);
			$APPLICATION->AddChainItem($pageTitle);
		}
	}
}