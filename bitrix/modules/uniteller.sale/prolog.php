<?php

// Подключает языковой файл.
global $MESS;
$strPath2Lang = str_replace('\\', '/', __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen('/prolog.php'));
include(GetLangFileName($strPath2Lang . '/lang/', '/all.php'));

?>