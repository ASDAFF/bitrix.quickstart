<?php

$arServiceTypes = array(
    0 => 'STD',
    1 => 'STDCOD',
    2 => 'PRIO',
    3 => 'PRIOCOD',
);
$arServiceTypesCodes = array(
    0 => 10001,
    1 => 10003,
    2 => 10002,
    3 => 10004,
);
$arEnclosingTypes = array(
    0 => 'CUR',
    1 => 'WIN',
    2 => 'APTCON',
    3 => 'APT',
);
$arEnclosingTypesCodes = array(
    0 => 101, //"CUR",
    1 => 102, //"WIN",
    2 => 103, //"APTCON",
    3 => 104, //"APT"
);

$arPayedServiceTypes = array(1, 3); //Available types to pay thorough PickPoint

$arSizes = array(
    'S' => array('NAME' => 'S', 'SIZE_X' => 15, 'SIZE_Y' => 36, 'SIZE_Z' => 60),
    'M' => array('NAME' => 'M', 'SIZE_X' => 20, 'SIZE_Y' => 36, 'SIZE_Z' => 60),
    'L' => array('NAME' => 'L', 'SIZE_X' => 36, 'SIZE_Y' => 36, 'SIZE_Z' => 60),
);

$arOptionDefaults = array(
    'FIO' => array(
        'TYPE' => 'USER',
        'VALUE' => 'USER_FIO',
    ),
    'ADDITIONAL_PHONES' => array(
        'TYPE' => 'USER',
        'VALUE' => 'PERSONAL_MOBILE',
    ),
    'NUMBER_P' => array(
        'TYPE' => 'ORDER',
        'VALUE' => 'ID',
    ),
    'EMAIL' => array(
        'TYPE' => 'USER',
        'VALUE' => 'EMAIL',
    ),
);
