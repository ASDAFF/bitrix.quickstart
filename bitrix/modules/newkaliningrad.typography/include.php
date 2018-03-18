<?php
global $MESS, $DOCUMENT_ROOT;
CModule::AddAutoloadClasses(
	'newkaliningrad.typography',
	array(
		'newkaliningrad_typography'=>'classes/general/newkaliningrad_typography.php',
		'newkaliningrad_EMTypograph'=>'classes/general/EMT.php',
	)
);

CJSCore::RegisterExt('newkaliningrad_typography', array(
	'rel' => array("ajax")
));
