<?php
//Добавление группы(есть подозрение):
use Bitrix\Sale\Delivery\Services;
$res = Services\Manager::add($fields);

     if ($res->isSuccess())
     {
      $ID = $res->getId();
     }
     else
     {
      $srvStrError = $res->getErrorMessages(); 
     }
?>
