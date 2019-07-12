<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="pull-right">
    <?if ($arResult['FORM_TYPE'] == 'logout'){?>
        <div class="lc">
            <div><?=GetMessage('AUTH_PERSONAL')?></div>
            <ul>
                <li><a href="<?=$arParams['PROFILE_URL']?>"><?=GetMessage('AUTH_PROFILE')?></a></li>
                <li><a href="<?=SITE_DIR?>personal/cart/"><?=GetMessage('AUTH_CART')?> <span></span></a></li>
                <li><a href="<?=SITE_DIR?>personal/order/"><?=GetMessage('AUTH_ORDER')?></a></li>
                <li><a href="<?=SITE_DIR?>personal/subscribe/"><?=GetMessage('AUTH_SUBSCRIBE')?></a></li>
                <li><a href="?logout=yes"><?=GetMessage('AUTH_LOGOUT_BUTTON')?></a></li>
            </ul>
        </div>
    <?} else {?>
        <div class="login_block">
            <a href="<?=SITE_DIR?>auth/" class="login_link"><img src="<?=SITE_TEMPLATE_PATH?>/images/login_ico.jpg"/><?=GetMessage('AUTH_LOGIN_BUTTON')?></a>
            <a href="<?=$arParams['REGISTER_URL']?>" class="register_link"><?=GetMessage('AUTH_REGISTER')?></a>
        </div>
    <?}?>
</div>