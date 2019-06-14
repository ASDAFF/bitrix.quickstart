<?php
/**
 * Created by PhpStorm.
 * User: rp
 * Date: 17.08.15
 * Time: 14:47
 */

require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

\CModule::IncludeModule("multiline.ml2webforms");

$wfrc = new \Ml2WebForms\WebFormsRequestController($_REQUEST['webform_id']);
$wfrc->processForm();
$wfrc->outputProcessResultScript();