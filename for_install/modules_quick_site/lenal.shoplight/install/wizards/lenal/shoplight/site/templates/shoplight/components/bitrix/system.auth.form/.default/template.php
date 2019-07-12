<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
CJSCore::Init(array("popup"));
?>

<?
if ($arResult["FORM_TYPE"] == "login") {
    ?>
    <li class="b-main-menu__item b-main-menu__item_type_login">
        <a class="b-main-menu__item-link" href="<?= $arResult["AUTH_URL"] ?>"><?= GetMessage("AUTH_LOGIN") ?></a>
    </li>
    <?
} else {
    $name = trim($USER->GetFullName());
    if (strlen($name) <= 0)
        $name = $USER->GetLogin();
    ?>
    <li class="b-main-menu__item b-main-menu__item_type_account">
        <a class="b-main-menu__item-link b-main-menu__item__account-link" href="<?= $arResult['PROFILE_URL'] ?>">
            <img class="b-main-menu__item__account-link__icon" src="<?= SITE_TEMPLATE_PATH ?>/images/user-icon16.png">
        </a>
        <div class="b-main-menu__submenu__account__border_stub"></div>
        <ul class="b-main-menu__submenu b-main-menu__submenu__account">
            <li class="b-main-menu__submenu__item b-main-menu__submenu__account__item b-main-menu__submenu__account__item_username"><?= htmlspecialcharsEx($name); ?></li>
            <li class="b-main-menu__submenu__item b-main-menu__submenu__account__item"><a class="b-main-menu__submenu__account__item-link" href="<?=SITE_DIR?>personal/profile/"><?= GetMessage("AUTH_PROFILE") ?></a></li>
            <li class="b-main-menu__submenu__item b-main-menu__submenu__account__item"><a class="b-main-menu__submenu__account__item-link" href="<?=SITE_DIR?>personal/order/"><?= GetMessage("AUTH_ZAKAZ") ?></a></li>
            <li class="b-main-menu__submenu__item b-main-menu__submenu__account__item"><a class="b-main-menu__submenu__account__item-link" href="<?=SITE_DIR?>?logout=yes"><?= GetMessage("AUTH_LOGOUT") ?></a></li>
        </ul>
    </li>
    <?
}
?>