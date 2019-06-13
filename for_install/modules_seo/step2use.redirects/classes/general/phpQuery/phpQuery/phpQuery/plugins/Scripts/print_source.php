<?php
/**
 * Script outputs document markup and changes HTML special chars to entities.
 *
 * @author Tobiasz Cudnik <tobiasz.cudnik/gmail.com>
 */
 
 require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';
 
/** @var phpQueryObject */
$self = $self;
$return = htmlspecialcharsbx($self);