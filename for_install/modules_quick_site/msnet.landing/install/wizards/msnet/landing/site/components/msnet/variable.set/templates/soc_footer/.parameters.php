<?php

$set = array(
    'LINK_VK' => '������ �� Vkontakte',
    'LINK_INSTAGRAM' => '������ �� Instagram',
    'LINK_YOUTUBE' => '������ �� YouTube',
);


$arTemplateParameters = array();
foreach ($set as $k => $val) {
	$arTemplateParameters[$k] = array(
		'NAME' => $val,
		'COLS' => 35,
		'ROWS' => 3
	);
}
