<?php
$module_id = S2uRedirects::MODULE_ID;
global $MESS;
IncludeModuleLangFile(__FILE__);
include_once($GLOBALS['DOCUMENT_ROOT'].BX_ROOT. '/modules/' . $module_id . '/include.php');

$redirectIsActive = COption::GetOptionString($module_id, 'REDIRECTS_IS_ACTIVE', 'Y');
$_404IsActive = COption::GetOptionString($module_id, '404_IS_ACTIVE', 'Y');

if($redirectIsActive!='Y') {
    $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('S2U_NO_REDIRECTS'),
        	'TYPE' => 'ERROR',
        	'DETAILS' => GetMessage('S2U_NO_REDIRECTS_DESC'),
        	'HTML' => true
        ));
    echo $message->Show();
}

if($_404IsActive!='Y') {
    $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('S2U_NO_404'),
        	'TYPE' => 'ERROR',
        	'DETAILS' => GetMessage('S2U_NO_404_DESC'),
        	'HTML' => true
        ));
    echo $message->Show();
}
?>
