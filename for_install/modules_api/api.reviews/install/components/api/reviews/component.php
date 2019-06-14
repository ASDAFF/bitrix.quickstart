<?php
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
 * @var string           $parentComponentPath
 * @var string           $parentComponentName
 * @var string           $parentComponentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 */

use \Bitrix\Main\Application;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.reviews')) {
	ShowError(Loc::getMessage('API_REVIEWS_MODULE_ERROR'));
	return;
}

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$docRoot = Application::getDocumentRoot();


///////////////////////////////////////////////////////////////////////////////
/// AJAX
///////////////////////////////////////////////////////////////////////////////
if($request->isPost() && $request->get('API_REVIEWS_AJAX') == 'Y' && $request->get('API_REVIEWS_ACTION')) {
	$action = $request->get('API_REVIEWS_ACTION');
	$APPLICATION->RestartBuffer();
	if($action == 'FILE_DOWNLOAD') {
		$url    = strtok($request->get('API_REVIEWS_FILE'), '?');
		$folder = trim($arParams['UPLOAD_FOLDER']);
		if($url && $folder) {
			if(preg_match('#' . preg_quote($folder) . '#' . BX_UTF_PCRE_MODIFIER, $url)) {
				$file = $docRoot . $url;
				if(file_exists($file)) {
					header("Content-Type: " . filetype($file));
					header("Content-Length: " . filesize($file));
					header('Content-Disposition: attachment; filename="' . GetFileName($file) . '"');
					readfile($file);
				}
			}
		}
	}
	die();
}


///////////////////////////////////////////////////////////////////////////////
/// COMPONENT
///////////////////////////////////////////////////////////////////////////////

$arParams['THEME']    = $arParams['THEME'] ? $arParams['THEME'] : 'flat';
$arParams['USE_STAT'] = (!isset($arParams['USE_STAT']) || $arParams['USE_STAT'] == 'Y');


if($arParams['INCLUDE_JQUERY'] && $arParams['INCLUDE_JQUERY'] != 'N') {
	CJSCore::Init($arParams['INCLUDE_JQUERY']);
	$arParams['INCLUDE_JQUERY'] = 'N';
}

CJSCore::Init(array('core', 'session', 'ls'));//array('ajax', 'json', 'ls', 'session', 'jquery', 'popup', 'pull')

$arDefaultUrlTemplates404    = array(
	 'list'   => "",
	 'search' => 'search/',
	 'rss'    => 'rss/',
	 'detail' => 'review#review_id#/',
	 'user'   => 'user#user_id#/',
);
$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases    = array();
$arComponentVariables        = array(
	 'review_id',
	 'user_id',
	 'nav-reviews',
);


if($arParams['SEF_MODE'] == 'Y') {
	$arVariables = array();

	$arUrlTemplates    = CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams['SEF_URL_TEMPLATES']);
	$arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $arParams['VARIABLE_ALIASES']);

	$engine        = new CComponentEngine($this);
	$componentPage = $engine->guessComponentPath(
		 $arParams['SEF_FOLDER'],
		 $arUrlTemplates,
		 $arVariables
	);

	//if($arVariables['review_id'] && $arParams['USE_LIST'])
	//$componentPage = 'list';

	$b404 = false;
	if(!$componentPage) {
		$componentPage = 'list';
	}

	if($arParams['USE_USER'] != 'Y'){
		if($componentPage == 'user')
			$b404 = true;
	}

	if($b404){
		//Выводим 404 страницу
		\Api\Reviews\Tools::send404(
			 trim($arParams["MESSAGE_404"]) ?: Loc::getMessage('API_REVIEWS_STATUS_404')
			 , true
			 , $arParams["SET_STATUS_404"] === "Y"
			 , $arParams["SHOW_404"] === "Y"
			 , $arParams["FILE_404"]
		);
	}

	/*if($b404 && CModule::IncludeModule('iblock'))
	{
		$folder404 = str_replace('\\', '/', $arParams['SEF_FOLDER']);
		if($folder404 != '/')
			$folder404 = '/' . trim($folder404, '/ \t\n\r\0\x0B') . '/';
		if(substr($folder404, -1) == '/')
			$folder404 .= 'index.php';

		if($folder404 != $APPLICATION->GetCurPage(true))
		{
			\Bitrix\Iblock\Component\Tools::process404(
				 ""
				 , ($arParams['SET_STATUS_404'] === 'Y')
				 , ($arParams['SET_STATUS_404'] === 'Y')
				 , ($arParams['SHOW_404'] === 'Y')
				 , $arParams['FILE_404']
			);
		}
	}*/

	CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

	//Ищем в строковых адресах ID-ки и передаем в шаблоны
	if($arVariables) {
		foreach($arVariables as $key => $val) {
			if(preg_match('/[\d]+/', $val, $match))
				$arVariables[ $key ] = intval($match[0]);
		}
	}

	$arResult = array(
		 'FOLDER'        => $arParams['SEF_FOLDER'],
		 'URL_TEMPLATES' => $arUrlTemplates,
		 'VARIABLES'     => $arVariables,
		 'ALIASES'       => $arVariableAliases,
	);
}
else {
	$arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases, $arParams['VARIABLE_ALIASES']);
	CComponentEngine::initComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

	$componentPage = "";

	/*if($arVariables['review_id'] && $arParams['USE_LIST'])
		$componentPage = 'list';
	else*/
	if(isset($arVariables['review_id']) && intval($arVariables['review_id']) > 0)
		$componentPage = 'detail';
	elseif(isset($arVariables['user_id']) && intval($arVariables['user_id']) > 0)
		$componentPage = 'user';
	else
		$componentPage = 'list';

	if($arParams['USE_USER'] != 'Y'){
		if($componentPage == 'user')
			$b404 = true;
	}

	if($b404){
		//Выводим 404 страницу
		\Api\Reviews\Tools::send404(
			 trim($arParams["MESSAGE_404"]) ?: Loc::getMessage('API_REVIEWS_STATUS_404')
			 , true
			 , $arParams["SET_STATUS_404"] === "Y"
			 , $arParams["SHOW_404"] === "Y"
			 , $arParams["FILE_404"]
		);
	}


	$arResult = array(
		 'FOLDER'        => "",
		 'URL_TEMPLATES' => Array(
				'list'   => htmlspecialcharsbx($APPLICATION->GetCurPage()),
				'detail' => htmlspecialcharsbx($APPLICATION->GetCurPage() . '?' . $arVariableAliases['review_id'] . '=#review_id#'),
				'user'   => htmlspecialcharsbx($APPLICATION->GetCurPage() . '?' . $arVariableAliases['user_id'] . '=#user_id#'),
				//'search' => htmlspecialcharsbx($APPLICATION->GetCurPage()),
				//'rss'    => htmlspecialcharsbx($APPLICATION->GetCurPage() . '?rss=y'),
		 ),
		 'VARIABLES'     => $arVariables,
		 'ALIASES'       => $arVariableAliases,
	);
}


$this->includeComponentTemplate($componentPage);