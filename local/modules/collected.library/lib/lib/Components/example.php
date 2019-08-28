<?php
/**
 * Copyright (c) 29/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

return ;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Collected\Common\Config,
    Collected\Components;
?>

<? /*include component*/ ?>
<?$APPLICATION->IncludeComponent('bitrix:main.include', '', Config::getComponentParams('IncludeArea', array(
    'PATH' => SITE_DIR . 'include/header/address.php',
)),
    false
);?>
<?$APPLICATION->IncludeComponent('bitrix:menu', 'top_menu', Config::getComponentParams('Menu', array(
    'USE_EXT' => 'N',
    'MAX_LEVEL' => 2
)),
    false
);?>
<?/* or */
//doesn't work copy template / show component params ( bitrix o_O preg_match $APPLICATION->IncludeComponent(template...) O_o )
Components\IncludeArea::inc('template', array('IBLOCK_TYPE' => 'content', 'IBLOCK_ID' => 2));