<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\ModuleManager;
//use  Bitrix\Main\Web\Uri;

Loc::loadMessages(__FILE__);

$request = Application::getInstance()->getContext()->getRequest();

// init devfunc
if (Loader::includeModule('redsign.devfunc')) {
    RSDevFunc::Init(array('jsfunc'));
} else {
    die('<span style="color:red;">'.Loc::getMessage('RS_SLINE.HEADER.ERROR_DEVFUNC').'</span>');
}

// is main page
$IS_MAIN = 'N';
if ($APPLICATION->GetCurPage(true)==SITE_DIR.'index.php') {
    $IS_MAIN = 'Y';
}

// is catalog page
$IS_CATALOG = 'N';
if (
    strpos($APPLICATION->GetCurPage(true), SITE_DIR.'catalog/') !== false  ||
    strpos($APPLICATION->GetCurPage(true), SITE_DIR.'brands/') !== false
) {
    $IS_CATALOG = 'Y';
}

// is personal page
$IS_PERSONAL = 'Y';
if (strpos($APPLICATION->GetCurPage(true), SITE_DIR.'personal/') === false) {
    $IS_PERSONAL = 'N';
}

// get site data
$cacheTime = 86400;
$cacheId = 'CSiteGetByID'.SITE_ID;
$cacheDir = '/siteData/'.SITE_ID.'/';

$cache = Bitrix\Main\Data\Cache::createInstance();
if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
    $arSiteData = $cache->getVars();
} elseif ($cache->startDataCache()) {

    $arSiteData = array();

    $rsSites = CSite::GetByID(SITE_ID);
    if ($arSite = $rsSites->Fetch()) {
        $arSiteData['SITE_NAME'] = $arSite['SITE_NAME'];
    }

    if (Loader::includeModule('sale')) {
        $arLocationPath = array();
                            
        $location_zip = COption::GetOptionString('sale', 'location_zip', '', SITE_ID);
        if ($location_zip != ''){
            $arLocationPath[] = $location_zip;
        }
       
       $location = COption::GetOptionString('sale', 'location', '', SITE_ID);
       
        if ($location != '') {
            $dbLocations = \Bitrix\Sale\Location\LocationTable::getPathToNodeByCode(
                $location,
                array(
                    'select' => array(
                        'LNAME' => 'NAME.NAME',
                        'SHORT_NAME' => 'NAME.SHORT_NAME',
                        'LEFT_MARGIN',
                        'RIGHT_MARGIN',
                    ),
                    'filter' => array(
                        'NAME.LANGUAGE_ID' => LANGUAGE_ID
                    )
                )
            );
            
            while ($arLocation = $dbLocations->Fetch()) {
                $arLocationPath[] = $arLocation['LNAME'];
            }
        }
        
        if (is_array($arLocationPath) && count($arLocationPath) > 0) {
            $arSiteData['SITE_ADDRESS_PATH'] = $arLocationPath;
        }
    }
    
    if (empty($arSiteData)) {
        $cache->abortDataCache();
    }

    $cache->endDataCache($arSiteData);
}

$Asset = Asset::getInstance();

$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/jquery/jquery-1.12.3.js');

$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/bootstrap/transition.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/bootstrap/dropdown.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/bootstrap/collapse.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/bootstrap/tab.js');
//$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/bootstrap/bootstrap.js');

$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/mousewheel/jquery.mousewheel.js');

$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/jquery.scrollbar/jquery.scrollbar.js');
$Asset->addCss(SITE_TEMPLATE_PATH.'/assets/lib/jquery.scrollbar/jquery.scrollbar.css');

$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/fancybox/jquery.fancybox.js');
$Asset->addCss(SITE_TEMPLATE_PATH.'/assets/lib/fancybox/jquery.fancybox.css');

$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/lib/owlcarousel2/owl.carousel.js');
$Asset->addCss(SITE_TEMPLATE_PATH.'/assets/lib/owlcarousel2/assets/owl.carousel.css', true);


$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/js/glass/glass.js');
$Asset->addCss(SITE_TEMPLATE_PATH.'/assets/js/glass/glass.css');

$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/js/script.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/js/offers.js');
$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/js/iefix.js');

$Asset->addCss(SITE_TEMPLATE_PATH.'/assets/css/custom.css', true);
$Asset->addJs(SITE_TEMPLATE_PATH.'/assets/js/custom.js', true);
$Asset->addCss(SITE_DIR.'assets/css/custom.css', true);
$Asset->addJs(SITE_DIR.'assets/js/custom.js', true);

$Asset->addCss(SITE_TEMPLATE_PATH.'/assets/css/template.css', true);

$Asset->addString('<script src="https://yastatic.net/share2/share.js" async="async" charset="utf-8"></script>');
$Asset->addString('<!--[if lte IE 8]><script src="'.SITE_TEMPLATE_PATH.'/assets/lib/html5shiv.min.js" async="async" data-skip-moving="true"></script><![endif]-->');
$Asset->addString('<!--[if lte IE 8]><script src="'.SITE_TEMPLATE_PATH.'/assets/lib/respond.min.js" async="async" data-skip-moving="true"></script><![endif]-->');

$protocol = \Bitrix\Main\Context::getCurrent()->getRequest()->isHttps() ? "https://" : "http://";
?>
<!DOCTYPE html>
<html xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>" itemscope itemtype="http://schema.org/WebSite">
<head>
    <?$APPLICATION->IncludeFile(SITE_DIR."include/template/head_start.php",array(),array("MODE"=>"html"))?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?=$protocol.SITE_SERVER_NAME;?>/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="<?=$protocol.SITE_SERVER_NAME;?>/favicon.ico" type="image/x-icon">
    <?$APPLICATION->ShowHead();?>
    <title itemprop="name">
    <?php
    $APPLICATION->ShowTitle();
    if ($arSiteData['SITE_NAME'] != ''):
        echo ' | '. $arSiteData['SITE_NAME'];
    endif;
    ?>
    </title>
    <?php
    CAjax::Init();
    ?>
<script type="text/javascript">
appSLine.SITE_TEMPLATE_PATH = '<?=SITE_TEMPLATE_PATH?>';
BX.message({"RSAL_JS_TO_MACH_CLICK_LIKES":"<?=CUtil::JSEscape(Loc::getMessage('RS_SLINE.HEADER.ERROR_CLICKER'))?>"});
</script>
<?$APPLICATION->IncludeFile(SITE_DIR."include/template/head_end.php",array(),array("MODE"=>"html"))?>
</head>
<body <?=$APPLICATION->ShowProperty("backgroundImage")?>>
	<?$APPLICATION->IncludeFile(SITE_DIR."include/template/body_start.php",array(),array("MODE"=>"html"))?>
    <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/favorite.php'); ?>
    <div id="svg-icons"></div>
    <div id="panel"><?$APPLICATION->ShowPanel();?></div>
    <div id="webpage" class="activelife body <?=LANGUAGE_ID;?> adapt" itemscope itemtype="http://schema.org/WebPage">
        <header class="l-header header">
            <div class="l-header__top">
                <div class="container clearfix">
                    <div class="auth_top">
                        <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/compare.php'); ?>
                        <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/menu_toppersonal.php'); ?>
                        <div class="auth_top__item" id="auth_top__exit">
                        <?php
                        $frame = new \Bitrix\Main\Page\FrameBuffered('auth_top__exit', false);
                        $frame->setBrowserStorage(true);
                        $frame->begin();
                        //$frame->beginStub();
                        ?>
                            <?php if ($USER->IsAuthorized()): ?>
                                <a class="auth_top__link" href="?logout=yes"><?=Loc::getMessage('RS_SLINE.HEADER.EXIT')?></a>
                            <?php else: ?>
                                <a class="auth_top__link js-ajax_link" href="<?=SITE_DIR?>personal/sing-in/" title="<?=Loc::getMessage('RS_SLINE.HEADER.AUTH_TITLE')?>">
                                    <svg class="icon icon-locked icon-svg"><use xlink:href="#svg-locked"></use></svg><span class="auth_top__text"><?=Loc::getMessage('RS_SLINE.HEADER.AUTH')?></span>
                                </a>
                            <?php endif; ?>
                        <?php $frame->beginStub(); ?>
                            <a class="auth_top__link js-ajax_link" href="<?=SITE_DIR?>personal/auth/" title="<?=Loc::getMessage('RS_SLINE.HEADER.AUTH_TITLE')?>">
                                <svg class="icon icon-locked icon-svg"><use xlink:href="#svg-locked"></use></svg><?=getMessage('RS_SLINE.HEADER.AUTH')?>
                            </a>
                        <?php $frame->end(); ?>
                        </div>
                    </div>
                    <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/menu_top.php'); ?>
                    <?php
                    /*
                    <div id="top_line" class="hidden-mobile">
                        <?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath(SITE_DIR.'include/template/top_line.php'),
                            Array(),
                            Array("MODE"=>"html")
                        );?>
                    </div>
                    */?>
                </div>
            </div>

            <div class="container l-header__infowrap clearfix">

                <div class="l-header__info clearfix">
                    <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/cart.php'); ?>
                    <div class="l-header__logo logo" itemscope itemtype="http://schema.org/Organization">
                        <?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath(SITE_DIR.'include/company_logo.php'),
                            Array(),
                            Array('MODE'=>'html')
                        );?>
                        <a href="<?=SITE_DIR?>" class="logo__link" itemprop="url"><?=$siteData['SITE_NAME']?></a>
                        <?
                        
                        ?>
                        <?php if (is_array($siteData['SITE_ADDRESS_PATH']) && count($siteData['SITE_ADDRESS_PATH']) > 0): ?>
                            <meta itemprop="address" content="<?=implode(', ', $siteData['SITE_ADDRESS_PATH'])?>">
                        <?php endif; ?>
                        
                        <?php
                        $sCompanyPhone = $APPLICATION->GetFileContent($_SERVER["DOCUMENT_ROOT"].SITE_DIR.'include/telephone1.php');
                        
                        if ($sCompanyPhone):
                        ?>
                        <?php endif; ?>
                        <meta itemprop="name" content="<?=$siteData['SITE_NAME']?>">
                    </div>
                    <div class="l-header__adds">
                        <div class="l-header__phone adds recall">
                            <a class="js-ajax_link" href="<?=SITE_DIR?>recall/" rel="nofollow" title="<?=Loc::getMessage('RS_SLINE.HEADER.RECALL')?>">
                                <svg class="icon icon-phone icon-svg"><use xlink:href="#svg-phone"></use></svg><?=Loc::getMessage('RS_SLINE.HEADER.RECALL')?>
                            </a>
                            <div class="adds__phone">
                            <?$APPLICATION->IncludeFile(
                                $APPLICATION->GetTemplatePath(SITE_DIR.'include/telephone1.php'),
                                Array(),
                                Array("MODE"=>"html")
                            );?>
                            </div>
                        </div>
                        <div class="l-header__phone adds feedback">
                            <a class="js-ajax_link" href="<?=SITE_DIR?>feedback/" rel="nofollow" title="<?=Loc::getMessage('RS_SLINE.HEADER.FEEDBACK_TITLE')?>">
                                <svg class="icon icon-dialog icon-svg"><use xlink:href="#svg-dialog"></use></svg><?=Loc::getMessage('RS_SLINE.HEADER.FEEDBACK')?>
                            </a>
                            <div class="adds__phone">
                            <?$APPLICATION->IncludeFile(
                                $APPLICATION->GetTemplatePath(SITE_DIR.'include/telephone2.php'),
                                Array(),
                                Array("MODE"=>"html")
                            );?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="l-header__line clearfix">
                    <div class="l-header__search">
                        <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/search.php'); ?>
                    </div>
                    <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/menu_catalog.php'); ?>
                </div>
            </div>
			
			<?php
			if ($IS_MAIN == 'Y') {
				$APPLICATION->ShowViewContent('mainbanners');
			}
			?>
			
        </header>
        <main class="l-main clearfix">
            <div class="container">
            <?php
            include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/breadcrumb.php');
/*
            if ($IS_PERSONAL == 'Y') {
                include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/header/menu_personal.php');
            }
*/
            if ($IS_MAIN != 'Y' && $IS_CATALOG != 'Y'): ?>
                <h1 class="webpage__title"><?$APPLICATION->ShowTitle(false)?></h1>
            <?php endif; ?>
            
            <?php
            if (
                $request->isAjaxRequest() ||
                $request->get('AJAX_CALL') == 'Y' ||
                $request->get('rs_ajax__page') == 'Y'
            ) {
                $APPLICATION->restartBuffer();
            }
            ?>
