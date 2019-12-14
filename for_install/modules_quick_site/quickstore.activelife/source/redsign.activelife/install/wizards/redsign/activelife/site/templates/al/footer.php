<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$request = Application::getInstance()->getContext()->getRequest();

        if (
            $request->isAjaxRequest() ||
            $request->get('AJAX_CALL') == 'Y' ||
            $request->get('rs_ajax__page') == 'Y'
        ) {
            die();
        }
?>
            </div>
        </main>
        <footer id="footer" class="l-footer">
            <div class="container">

                <div class="l-footer__inner row clearfix">
                    <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="l-footer__logo logo" itemscope itemtype="http://schema.org/Organization">
                            <?$APPLICATION->IncludeFile(
                                $APPLICATION->GetTemplatePath(SITE_DIR.'include/company_logo.php'),
                                Array(),
                                Array('MODE'=>'html')
                            );?>
                            <a href="<?=SITE_DIR?>" class="logo__link" itemprop="url"><?=$siteData['SITE_NAME']?></a>
                            <meta itemprop="name" content="<?=$siteData['SITE_NAME']?>">
                        </div>

                        <div class="l-footer__adds">
                            <div class="l-footer__phone adds recall">
                                <a rel="nofollow" class="js-ajax_link" href="<?=SITE_DIR?>recall/" title="<?=Loc::getMessage('RS_SLINE.FOOTER.RECALL')?>">
									<svg class="icon icon-phone icon-svg"><use xlink:href="#svg-phone"></use></svg>
                                    <?=Loc::getMessage('RS_SLINE.FOOTER.RECALL')?>
                                </a>
                                <div class="adds__phone">
                                <?$APPLICATION->IncludeFile(
                                    $APPLICATION->GetTemplatePath(SITE_DIR.'include/telephone1.php'),
                                    Array(),
                                    Array("MODE"=>"html")
                                );?>
                                </div>
                            </div>
                            <div class="l-footer__phone adds feedback">
                                <a rel="nofollow" class="js-ajax_link" href="<?=SITE_DIR?>feedback/" title="<?=Loc::getMessage('RS_SLINE.FOOTER.FEEDBACK_TITLE')?>">
									<svg class="icon icon-dialog icon-svg"><use xlink:href="#svg-dialog"></use></svg>
                                    <?=Loc::getMessage('RS_SLINE.FOOTER.FEEDBACK')?>
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

                    <div class="l-footer__catalog col-xs-6 col-md-3 col-lg-6">
                        <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/footer/menu_catalog.php'); ?>
                    </div>

                    <div class="l-footer__menu col-xs-6 col-sm-4 col-md-3 col-lg-2">
                        <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/footer/menu_footer.php'); ?>
                    </div>

                    <?php
                        $sSocServ = $APPLICATION->GetFileContent($_SERVER["DOCUMENT_ROOT"].SITE_DIR.'include/footer/socservice.php');
                    ?>

                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                        <div class="l-footer__soc">
                            <?php if ($sSocServ): ?>
                                <div class="l-footer__title"><?=Loc::getMessage('RS_SLINE.FOOTER.JOIN_NOW')?></div>
                                <?$APPLICATION->IncludeFile(
                                    $APPLICATION->GetTemplatePath(SITE_DIR.'include/footer/socservice.php'),
                                    Array(),
                                    Array("MODE"=>"html")
                                );?>
                            <?php endif; ?>
                        </div>
                        <?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/footer/subscribe.php'); ?>
                    </div>
                </div>

            </div>
            <div class="l-footer__bottom">
                <div class="container">
                    <div class="row">
                        <div class="l-footer__copy col-sm-8">
                            <?$APPLICATION->IncludeFile(
                                $APPLICATION->GetTemplatePath(SITE_DIR.'include/copyright.php'),
                                Array(),
                                Array("MODE"=>"html")
                            );?>
                        </div>
                        <div class="l-footer__dev col-sm-4">
                            <?php // #REDSIGN_COPYRIGHT# ?>
                            Powered by <a href="http://redsign.ru/" target="_blank"><b>ALFA Systems</b></a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <div style="display:none;">alfaSystems sline activelife AL91TG62</div>
    <script>$('#svg-icons').setHtmlByUrl({url:appSLine.SITE_TEMPLATE_PATH+'/assets/img/icons.svg'});</script>
    <?$APPLICATION->IncludeFile(SITE_DIR."include/template/body_end.php", array(), array("MODE" => "html"))?>
</body>
</html>