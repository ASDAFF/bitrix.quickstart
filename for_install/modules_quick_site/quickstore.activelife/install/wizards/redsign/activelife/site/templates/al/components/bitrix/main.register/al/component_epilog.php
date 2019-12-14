<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

$bIsAjax = (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    $request->get('rs_ajax') == 'Y' ||
    $request->get('rs_ajax__page') == 'Y'
);

if ($bIsAjax) {
    if (
        $USER->getId() > 0 &&
        (strlen($request->get('backurl')) > 0 || $arParams["SUCCESS_PAGE"] <> '')
    ) {
        ?>
        <div><script>
        if (window.jQuery) {
			setTimeout(function(){
				$.fancybox.close(true);
				<?php if (strlen($request->get('backurl')) > 0): ?>
					window.top.location.href = <?=CUtil::PhpToJSObject($request->get('backurl'))?>;
                <?php elseif ($arParams["SUCCESS_PAGE"] <> ''): ?>
                    window.top.location.href = <?=CUtil::PhpToJSObject($arParams["SUCCESS_PAGE"])?>;
				<?php endif; ?>
			}, appSLine.fancyTimeout);
		}
        </script></div>
        <?
    }
//	$APPLICATION->RestartBuffer();
//	die();
}