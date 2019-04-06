<?php

CModule::AddAutoloadClasses(
	'asd.mailtpl',
	$a = array(
		'CASDMailTpl' => 'classes/general/mailtpl.php',
		'CASDMailTplDB' => 'classes/'.$GLOBALS['DBType'].'/mailtpl.php',
	)
);
