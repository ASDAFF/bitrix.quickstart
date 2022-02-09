<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/lib/loader.php");

/** @var $server \Bitrix\Main\Server */
$server = \Bitrix\Main\Server::start($_SERVER);

/** @var $application \Bitrix\Main\Application */
$application = $server->getApplication();

$application->initialize();
$application->processRequest();


// Predefined variables

/** @var $page \Bitrix\Main\Page */
$page = $application->getPage();
/** @var $request \Bitrix\Main\Request */
$request = $page->getRequest();
