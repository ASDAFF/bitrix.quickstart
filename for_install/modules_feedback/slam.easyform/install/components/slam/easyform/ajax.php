<?
define('STOP_STATISTICS', true);
define('NOT_CHECK_PERMISSIONS', true);
$_POST['AJAX'] = 'Y';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$APPLICATION->RestartBuffer();

if (!\Bitrix\Main\Application::isUtfMode()) {
    $context = \Bitrix\Main\Application::getInstance()->getContext();
    $_POST['arParams']['templateName'] = \Bitrix\Main\Text\Encoding::convertEncoding($_POST['arParams']['templateName'], 'UTF-8', $context->getCulture()->getCharset());
    $_POST['arParams'] = \Bitrix\Main\Text\Encoding::convertEncoding($_POST['arParams'], 'UTF-8', $context->getCulture()->getCharset());
}

header('Content-Type: text/html; charset='.LANG_CHARSET);

foreach ($_POST['arParams'] as $key => $val){
    if(strpos($val, '-array-') !== false){
        $_POST['arParams'][$key] = explode('-array-', $val);
        TrimArr($_POST['arParams'][$key]);
    }
}


$APPLICATION->IncludeComponent('slam:easyform', $_POST['arParams']['templateName'], $_POST['arParams']);
?>