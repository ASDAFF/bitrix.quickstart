<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Главная");
$APPLICATION->SetPageProperty("title", "Главная");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/main.css");
//$USER->Authorize(1);
use Bitrix\Main\Application;
$request = Application::getInstance()->getContext()->getRequest();

global $arHomeCatalogFilter;
$arHomeCatalogFilter = array();

include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/super_banners.php'); 

global $bRSHomeTabShow;
$bRSHomeTabShow = false;
?>
<ul class="nav-tabs text-center" role="tablist">
<?php
$APPLICATION->ShowViewContent('main_section_tab');
$APPLICATION->ShowViewContent('main_brands_tab');
?>
</ul>
<div class="tab-content">
    <?php
    $APPLICATION->ShowViewContent('main_section_start_div');
        include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/section_list.php'); 
    ?>
    </div>
    <?php $APPLICATION->ShowViewContent('main_brands_start_div');
        include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/brands.php'); 
    ?>
    </div>
</div>
</div>
<hr class="v_separate">
<div class="container">
    <div class="row clearfix">
        <div class="l-base col-xs-12 col-md-9 col-lg-9d6">
            <?php
            if (CModule::IncludeModule('advertising')):
                include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/adv_owl_top.php');
            else:
                include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/noadv_owl_top.php'); 
            endif;
            ?>
            <ul role="tablist" class="nav-tabs js-catalog_refresh" data-ajax-id="catalog_home">
                <?$APPLICATION->ShowViewContent('catalog_home_tab');?>
            </ul>
            <?php
            $sAjaxTabId = $request->get('ajax_filter');
            
            if ($sAjaxTabId == 'VIEWED_PRODUCT'):
                include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/viewed.php');
            elseif ($sAjaxTabId == 'BESTSELLER_PRODUCT'):
                include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/bestsellers.php');
            elseif ($sAjaxTabId == 'BIGDATA_PRODUCT'):
                include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/bigdata.php');

            else:
                if ($sAjaxTabId == 'FAVORITE_PRODUCT') {
                    global $rsFavoriteElements;
                    if (is_array($rsFavoriteElements) && count($rsFavoriteElements) > 0) {
                        $arHomeCatalogFilter['ID'] = $rsFavoriteElements;
                    } else {
                        $arHomeCatalogFilter['ID'] = array('0');
                    }
                } else if (isset($sAjaxTabId) && strlen($sAjaxTabId) > 0) {
                    $arHomeCatalogFilter['!'.$sAjaxTabId] = false;
                }
                include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/catalog_section.php');
            endif;
            ?>
        </div>
        
        <div class="l-side col-xs-12 col-md-3 col-lg-2d4">
            <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/newsline.php'); ?>
        </div>
    </div>
    <?php
    if (CModule::IncludeModule('advertising')):
        include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/adv_owl_bottom.php'); 

    else:
        include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/index/noadv_owl_bottom.php'); 
    endif;
    ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>