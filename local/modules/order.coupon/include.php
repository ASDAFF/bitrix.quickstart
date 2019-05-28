<?php

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;

$sModule = basename(dirname(__FILE__));
Loader::registerAutoLoadClasses($sModule, array(
      'OrderCoupon\Event'               => 'classes/Event.php',
));