<?php
if(
    $_REQUEST['AJAX_CALL'] == "Y" &&
    $_REQUEST['action'] == "UPDATE_ITEMS"
) {
    $APPLICATION->RestartBuffer();
    $arJson = array(
        $arParams['TEMPLATE_AJAX_ID'] => $templateData,
        $arParams['TEMPLATE_AJAX_ID'].'_sorter' => 
            $APPLICATION->GetViewContent($arParams['TEMPLATE_AJAX_ID'].'_sorter')
    );
    echo CUtil::PhpToJSObject($arJson);
    die();
}