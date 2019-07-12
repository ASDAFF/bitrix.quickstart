<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$templateData = array(
    'BLOG_USE' => ($arResult['BLOG_USE'] ? 'Y' : 'N'),
    'BLOG' => array(
		'BLOG_FROM_AJAX' => $arResult['BLOG_FROM_AJAX'],
	),
);

if (!$templateData['BLOG']['BLOG_FROM_AJAX']):

    if (!empty($arResult['ERRORS'])) {
		echo '<div class="alert alert-danger">';
            echo implode('<br>', $arResult['ERRORS']);
        echo '</div>';
		return;
	}


    $arData = array();
    $arJSParams = array(
        'serviceList' => array(),
    	'settings' => array()
    );

    if ($arResult['BLOG_USE']) {
        $templateData['BLOG']['AJAX_PARAMS'] = array(
    		'IBLOCK_ID' => $arResult['ELEMENT']['IBLOCK_ID'],
    		'ELEMENT_ID' => $arResult['ELEMENT']['ID'],
    		'URL_TO_COMMENT' => $arParams['~URL_TO_COMMENT'],
    		'WIDTH' => $arParams['WIDTH'],
    		'COMMENTS_COUNT' => $arParams['COMMENTS_COUNT'],
    		'BLOG_USE' => 'Y',
    		'BLOG_FROM_AJAX' => 'Y',
    		'FB_USE' => 'N',
    		'VK_USE' => 'N',
    		'BLOG_TITLE' => $arParams['~BLOG_TITLE'],
    		'BLOG_URL' => $arParams['~BLOG_URL'],
    		'PATH_TO_SMILE' => $arParams['~PATH_TO_SMILE'],
    		'EMAIL_NOTIFY' => $arParams['EMAIL_NOTIFY'],
    		'AJAX_POST' => $arParams['AJAX_POST'],
    		'SHOW_SPAM' => $arParams['SHOW_SPAM'],
    		'SHOW_RATING' => $arParams['SHOW_RATING'],
    		'RATING_TYPE' => $arParams['~RATING_TYPE'],
    		'CACHE_TYPE' => 'N',
    		'CACHE_TIME' => '0',
    		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
    		'TEMPLATE_THEME' => $arParams['~TEMPLATE_THEME'],
    		'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
    	);

        $arJSParams['serviceList']['blog'] = true;

        $arJSParams['settings']['blog'] = array(
    		'ajaxUrl' => $templateFolder.'/ajax.php?IBLOCK_ID='.$arResult['ELEMENT']['IBLOCK_ID'].'&ELEMENT_ID='.$arResult['ELEMENT']['ID'].'&SITE_ID='.SITE_ID,
    		'ajaxParams' => array(),
    		'contID' => 'bx-cat-soc-comments-blg_'.$arResult['ELEMENT']['ID']
    	);
        $arData["BLOG"] =  array(
    		"NAME" => ($arParams['BLOG_TITLE'] != '' ? $arParams['BLOG_TITLE'] : GetMessage('IBLOCK_CSC_TAB_COMMENTS')),
    		"ACTIVE" => "Y",
    		"CONTENT" => '<div id="bx-cat-soc-comments-blg_'.$arResult['ELEMENT']['ID'].'">'.GetMessage("IBLOCK_CSC_COMMENTS_LOADING").'</div>'
    	);
    }

    if(!empty($arData)):
    ?>
    <div id="<?='bx-cat-soc-comments-blg_'.$arResult['ELEMENT']['ID']?>"></div>
    <script>var obCatalogComments_<? echo $arResult['ELEMENT']['ID']; ?> = new JCCatalogSocnetsComments(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);</script>
    <?php else: ?>
    <div class="alert alert-danger"><?=Loc::getMessage("IBLOCK_CSC_NO_DATA")?></div>
    <?php endif; ?>
<?php endif; ?>
