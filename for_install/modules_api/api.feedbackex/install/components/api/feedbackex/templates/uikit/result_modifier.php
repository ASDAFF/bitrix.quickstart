<?

use \Bitrix\Main\Loader;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

$templateFolder = $this->getFolder();
$formId         = ToLower($arParams['API_FEX_FORM_ID']);
$cssFormId      = '#API_FEX_' . $arParams['API_FEX_FORM_ID'];

$arParams['COLOR'] = (isset($arParams['COLOR']) ? trim($arParams['COLOR']) : 'default');
$arParams['THEME'] = (isset($arParams['THEME']) ? trim($arParams['THEME']) : 'gradient');

$arResult['FORM_TITLE'] = '';
if($arParams['TITLE_DISPLAY'] && $arParams['FORM_TITLE'])
	$arResult['FORM_TITLE'] = '<div class="api-title api-h' . $arParams['FORM_TITLE_LEVEL'] . '">' . $arParams['FORM_TITLE'] . '</div>';



//==============================================================================
// Refresh tmp css & js after refresh arparams
//==============================================================================
if($arParams['REFRESH_PARAMS']) {
	@unlink($_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/tmp/' . $formId . '-fn.js');
	@unlink($_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/tmp/' . $formId . '-' . $arParams['THEME'] . '.css');
}


//==============================================================================
// $tmpCss & $tmpJs
//==============================================================================

// $tmpCss
$tmpCss = "/* automatically generated from result_modifier.php */\n";
if($arParams['FORM_LABEL_TEXT_ALIGN']) {
	$tmpCss .= $cssFormId . ' .uk-form-label{text-align:' . $arParams['FORM_LABEL_TEXT_ALIGN'] . '}' . "\n";
}

if($arParams['FORM_LABEL_WIDTH'] || $arParams['FORM_FIELD_WIDTH'] || $arParams['FORM_WIDTH']) {
	$tmpCss .= '@media (min-width:960px){' . "\n";

	if($arParams['FORM_WIDTH']) {
		$tmpCss .= "\t" . $cssFormId . '{width:' . $arParams['FORM_WIDTH'] . '}' . "\n";
	}

	if($arParams['FORM_LABEL_WIDTH']) {
		$FORM_LABEL_WIDTH = ($arParams['FORM_LABEL_WIDTH']) ? $arParams['FORM_LABEL_WIDTH'] : '200px';
		$tmpCss           .= "\t" . $cssFormId . ' .uk-form-horizontal .uk-form-label{width:' . $FORM_LABEL_WIDTH . '}' . "\n";
		$tmpCss           .= "\t" . $cssFormId . ' .uk-form-horizontal .uk-form-controls{margin-left:' . $FORM_LABEL_WIDTH . '}' . "\n";
	}

	if($arParams['FORM_FIELD_WIDTH'])
		$tmpCss .= "\t" . $cssFormId . ' .uk-form-horizontal .uk-form-controls{width:' . $arParams['FORM_FIELD_WIDTH'] . '}' . "\n";

	$tmpCss .= '}' . "\n";
}


// $tmpJs
$tmpJs = "";
if($arParams['USE_PLACEHOLDER'])
	$tmpJs .= "\t" . '$("' . $cssFormId . ' input, ' . $cssFormId . ' textarea").placeholder();' . "\n";

if($arParams['USE_AUTOSIZE'])
	$tmpJs .= "\t" . 'autosize($("' . $cssFormId . ' textarea"));' . "\n";



//==============================================================================
// Include JS & CSS
//==============================================================================
if($arParams['USE_JQUERY'])
	CUtil::InitJSCore('jquery');

//Bitrix v15.5.1
if($arParams['USE_AUTOSIZE'])
	$this->addExternalJs($templateFolder . '/js/autosize/jquery.autosize.min.js');

if($arParams['USE_PLACEHOLDER'])
	$this->addExternalJs($templateFolder . '/js/placeholder/jquery.placeholder.min.js');

if($arParams['USE_MODAL']) {
	CUtil::InitJSCore(array('api_modal', 'api_button'));
}

if($arParams['USE_FLATPICKR']) {
	CUtil::InitJSCore(array('api_flatpickr'));
}


//==============================================================================
// Work with TMP Css & JS
//==============================================================================
$tmpFolder = $_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/tmp';
if(!is_dir($tmpFolder))
	mkdir($tmpFolder, 0755, true);


//$tmpCss
if($arParams['THEME']) {
	$tmpFile = $templateFolder . '/tmp/' . $formId . '-' . $arParams['THEME'] . '.css';

	if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $tmpFile)) {
		$sourceFile = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/css/' . ($arParams['THEME']) . '.css');
		$sourceFile = preg_replace('/#form_id/', trim($cssFormId), $sourceFile);

		file_put_contents($_SERVER['DOCUMENT_ROOT'] . $tmpFile, $sourceFile);

		if($tmpCss)
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . $tmpFile, $tmpCss, FILE_APPEND);

		unset($sourceFile);
	}

	$this->addExternalCss($tmpFile);
}

//$tmpJs
if($tmpJs) {
	$tmpFile = $templateFolder . '/tmp/' . $formId . '-fn.js';

	if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $tmpFile)) {
		$sourceFile = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $templateFolder . '/js/fn.js');
		$sourceFile = preg_replace('/script/', $tmpJs, $sourceFile);

		file_put_contents($_SERVER['DOCUMENT_ROOT'] . $tmpFile, $sourceFile);

		unset($sourceFile);
	}

	$this->addExternalJs($tmpFile);
}
