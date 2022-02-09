<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if(!$USER->isAdmin()) 
    die();
 
$id = intval($_REQUEST['id']);

if($id && CModule::IncludeModule('remains')){
    $matching = new matching();
    $matching->RemoveByID($id);
    
    $availability = new availability();
    $r = $availability->GetList(array(), array('MATCHING_ID'=>$id));
    while($res = $r->Fetch())
        $availability->RemoveByID($res['ID']);
}    