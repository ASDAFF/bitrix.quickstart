<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity; 
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\SystemException; 

Loader::includeModule('highloadblock');

$data = array(
		'NAME' => 'TestName',
		'TABLE_NAME' => 'test_name'
	);


$result = Bitrix\Highloadblock\HighloadBlockTable::add($data);
$ID = $result->getId();






require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
