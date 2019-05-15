<?php
include_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule("altasib.starterkit");

$email = "";
if(isset($_REQUEST["email"])){
    $email = $_REQUEST["email"];
}
\Altasib\Starterkit\Debug\Functions::checkSendMail($email);