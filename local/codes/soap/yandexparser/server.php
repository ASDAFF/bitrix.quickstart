<?php 
//error_reporting(E_ALL); 
set_time_limit(0);

require('./api.php');
  
function sendItems($symbol) { 
 
    $items = unserialize($symbol);
    
    $parser = new yandexParser(array('minRating'   =>  4,    
                                     'offersCount' =>  20)); 
    
    foreach($items as $id => $modelID) 
        $result[$id] = $parser->parse($modelID);
  
    return serialize($result); 
} 
    

ini_set("soap.wsdl_cache_enabled", "0");  
$server = new SoapServer("stockquote.wsdl"); 
$server->addFunction("sendItems"); 
$server->handle(); 