<?php
@CModule::IncludeModule("gsa.modul");
//global $APPLICATION;
global $DBType;

$arClasses=array(
    'cGsa'=>'classes/general/cGsa.php'
);

CModule::AddAutoloadClasses("gsa.modul",$arClasses);
