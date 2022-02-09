<?
ini_set( 'default_charset', 'UTF-8' ); 
require('./api.php');

 
?> 
<form>
    <label>modelid</label>
    <input type="text" name="modelID" value="<?=$_REQUEST['modelID']?>" size="15">
    <input type="submit" value="ok" /></form><hr size="1">
    <br>
<style>
    table tr td{
        font-size: 13px;
        font-family: Verdana;
        padding: 3px 20px 3px 0px;
    }
</style>    
<?php
 
        set_time_limit(0);


 if($modelID = $_REQUEST['modelID'])
 {
     
     
     $parser = new yandexParser(array('offersCount'=>20,
                                      'minRating' => 4));
     
     $r = $parser->parse($modelID);
     
     var_dump($r);
     
 }
        
        
  