<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);
$this->setFrameMode(true);
?>
<? if (sizeof($arResult['ITEMS'])):?>
    <div class="team" id="about">
        <div class="container">
            <div class="row">
                <div class="col-md-6 team-col">
                    <div class="info">
                        <? if ($arParams['TITLE']):?>
                            <h2><?=$arParams['TITLE'];?></h2>
                        <? endif;?>
                        <? if ($arParams['TEXT']):?>
                            <p><?=$arParams['TEXT'];?></p>
                        <? endif;?>
                        <div class="team__buttons"><a class="button style_red size_large js-expert-popup" href="<?=SITE_DIR;?>personal/?register=yes&become=expert"><span class="button__text">Текст</span></a><a class="button style_border-red size_large" href="<?=SITE_DIR;?>personal/"><span class="button__text">Ещё текст</span></a></div>
                    </div>
                </div>
                <div class="col-md-6 discuss-col hidden-xs">
                    <div class="discuss__img"><img src="<?= MARKUP_PATH ?>/images/expert.jpg" alt=""></div>
                </div>
            </div>
        </div>
    </div>
<? endif;?>
