<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\ModuleManager;

$moduleVersion = (int) str_replace(".", "", ModuleManager::getVersion('redsign.flyaway'));

if($moduleVersion < 320) {    
    $arMenu = array(
        array(
            Loc::getMessage('RS.FLYAWAY.MENU_PROFILE'),
            SITE_DIR.'personal/profile/',
        ),
        array(
            Loc::getMessage('RS.FLYAWAY.MENU_BASKET'),
            SITE_DIR.'personal/cart/',
        ),
        array(
            Loc::getMessage('RS.FLYAWAY.MENU_DELIVERY_PROFIL'),
            SITE_DIR.'personal/delivery/',
        ),
        array(
            Loc::getMessage('RS.FLYAWAY.MENU_ORDERS'),
            SITE_DIR.'personal/order/',
        ),
        array(
            Loc::getMessage('RS.FLYAWAY.MENU_FAVORITE'),
            SITE_DIR.'personal/favorite/',
        ),
        array(
            Loc::getMessage('RS.FLYAWAY.MENU_VIEWED'),
            SITE_DIR.'personal/viewed/',
        ),
    );
} else {
    include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'/personal/.topsub.menu.php');
    $arMenu = $aMenuLinks;
}
?>

<span class="authinhead dropdown" id="authinhead">
    <?php
    $frame = $this->createFrame('authinhead',false)->begin();
    $frame->setBrowserStorage(true);

    if($arResult['FORM_TYPE'] == 'login'):
    ?>
    <a href="<?=SITE_DIR?>auth/"><?=Loc::getMessage('RS.FLYAWAY.AUTH')?></a><span class="prom"><?=Loc::getMessage('FLYAWAY.ILI')?></span><a href="<?=SITE_DIR?>auth/?register=yes"><?=Loc::getMessage('RS.FLYAWAY.REGISTRATION')?></a>
    <?php else: ?>
    <a class="dropdown-toggle" id="ddPersonalMenu" data-toggle="dropdown" href="<?=SITE_DIR?>personal/"><?=Loc::getMessage('RS.FLYAWAY.PERSONAL_PAGE')?></a>
    <span class="prom"><?=Loc::getMessage('FLYAWAY.ILI')?></span>
    <a class = "hidden-xs" href="?logout=yes"><?=Loc::getMessage('RS.FLYAWAY.EXIT')?></a>
    <ul class="dropdown-menu dropdown-menu-right list-unstyled" aria-labelledby="ddPersonalMenu">
        <?php foreach($arMenu as $menu): ?>
        <li><a href="<?=$menu[1]?>"><?=$menu[0]?></a></li>        
        <?php endforeach; ?>
        <li class = "visible-xs"><a href="?logout=yes"><?=Loc::getMessage('RS.FLYAWAY.EXIT')?></a></li>
    </ul>
    <?php
    endif;
    $frame->beginStub();
    ?>

    <?php $this->SetViewTarget("system_auth_for_mobile"); ?>
        <?php if($arResult['FORM_TYPE'] == 'login'): ?>
        <a href="<?=SITE_DIR?>auth/"><?=Loc::getMessage('RS.FLYAWAY.AUTH')?></a>&nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;<a href="<?=SITE_DIR?>auth/?register=yes"><?=Loc::getMessage('RS.FLYAWAY.REGISTRATION')?></a>
        <?php else: ?>
        <a href="<?=SITE_DIR?>personal/" class="mobile-menu__userlogin js-userlogin-toggle"><span class="text-fadeout"><?=$arResult['USER_LOGIN']?></span> <span class="fa fa-angle-down"></span></a>
        <div class="mobile-menu__userpersonal js-mobile-userpersonal">
            <ul class="mobile-menu__userpersonal-list list-unstyled">
            <?php foreach($arMenu as $menu): ?>
            <li><a href="<?=$menu[1]?>"><?=$menu[0]?></a></li>        
            <?php endforeach; ?>
            <li><a href="?logout=yes"><?=Loc::getMessage('RS.FLYAWAY.EXIT')?></a></li>
            </ul>
        </div>
        <?php endif; ?>
    <?php $this->EndViewTarget(); ?>

    <a href="<?=SITE_DIR?>auth/"><?=Loc::getMessage('RS.FLYAWAY.AUTH')?></a><a href="<?=SITE_DIR?>auth/?register=yes"><?=Loc::getMessage('RS.FLYAWAY.REGISTRATION')?></a>
    <?php $frame->end(); ?>
</span>
