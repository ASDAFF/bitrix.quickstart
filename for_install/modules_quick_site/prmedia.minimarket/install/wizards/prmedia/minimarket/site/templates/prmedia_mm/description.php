<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

// localization messages
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arTemplate = array(
  'NAME' => Loc::getMessage('TEMPLATE_NAME'),
  'DESCRIPTION' => Loc::getMessage('TEMPLATE_DESC')
);