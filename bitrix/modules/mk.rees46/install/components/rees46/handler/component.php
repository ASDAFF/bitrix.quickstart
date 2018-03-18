<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

CModule::IncludeModule('mk.rees46');
\Rees46\Functions::includeJs(); // include required js and handle all events
