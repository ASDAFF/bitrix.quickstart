<?php
error_reporting(E_ERROR);
ini_set('display_errors',1);
set_time_limit(30);
class TestSheeplaInstall{
    var $currentFolder;
    var $content = '';
    var $console = true;
    public function __construct($cons = true,$folder=''){
        if($folder!=''){
            $this->currentFolder = $folder;
        }else{
            $this->currentFolder = getcwd().'/';   
        }        
        $this->content = '';
        $this->console = $cons;    
         
    }
    public function CheckRedableWritable($fold = '',$nbsp = ''){        
        /**
         * Checking if folder is readable/writable
        */
        if($fold!=''){
            $localCurrentFolder = '/'.$fold;
        }else{
            $localCurrentFolder = '';
        }
        
        $folder = opendir($this->currentFolder.$localCurrentFolder);
        if($fold=='') $this->content .= '<table>';
        while(false !== ($file = readdir($folder))){
               if(($file != 'index.php')&&($file != '.')&&($file != '..')) {
                    if($fold=='')
                        $this->content .= '<tr><td>'.$file.'</td>';
                    else
                        $this->content .= '<tr><td>'.$nbsp.'-'.$file.'</td>';
                    
                    
                    if(is_readable($this->currentFolder.$localCurrentFolder.'/'.$file)){
                        $this->content .= '<td style="color:#23BD17;">Readable</td>';                
                    }else{
                        if($this->console){
                               return $this->currentFolder.$localCurrentFolder.'/'.$file. ' is not readable';
                               exit();  
                        }
                        $this->content .= '<td style="color:#BD1717;">Is not readable</td>';
                    }
                    
                    if(is_writable($this->currentFolder.$localCurrentFolder.'/'.$file)){
                        $this->content .= '<td style="color:#23BD17;">Writable</td>';                
                    }else{
                        if($this->console){
                               return $this->currentFolder.$localCurrentFolder.'/'.$file. ' is not writable';
                               exit(); 
                        }
                        $this->content .= '<td style="color:#BD1717;">Is not writable</td>';
                    }
                    $this->content .= '<td style="color:#010101;">'.round(filesize($this->currentFolder.$localCurrentFolder.'/'.$file)/1024,2).' KB</td>';
                    $stat = stat($this->currentFolder.$localCurrentFolder.'/'.$file);
                    $this->content .= '<td style="color:#1768BD;">'.date('d.m.Y H:i:s', $stat['mtime']).' </td>';
                    $this->content .= '</tr>
                    ';
                    
                    }
                    if(($file != 'index.php')&&($file != '.')&&($file != '..')) {
                        if(is_dir($this->currentFolder.'/'.$file.'/')&&($localCurrentFolder=='')){
                            $nbsp .= '&nbsp;&nbsp;&nbsp;';
                            $this->CheckRedableWritable($localCurrentFolder.'/'.$file);                        
                        }elseif(is_dir($this->currentFolder.'/'.$localCurrentFolder.'/'.$file.'/')&&($localCurrentFolder!='')){
                            $nbsp .= '&nbsp;&nbsp;&nbsp;';
                            $this->CheckRedableWritable($localCurrentFolder.'/'.$file,$nbsp);
                        }
                   }
                     
                
        }
        closedir($folder);
        if($fold=='') $this->content .= '</table>';
            
    }
    public function CheckCURL(){
        /**
         * Checking if CURL Installed
        */
        $res = function_exists('curl_version') ? 'Enabled' : 'Disabled';
        if($this->console){
            if($res=='Enabled'){ return true; }else{
                
                if($this->CheckFGC() !== true)
                    return 'CURL is disabled '.$this->CheckFGC(); 
                }
        }
        if($res=='Enabled'){ $color = '#23BD17'; }else{ $color = '#BD1717'; }
        $this->content .= '<table><tr><td>CURL enabled</td><td style="color:'.$color.';">'.$res.'</td></tr></table>';
    }
    public function CheckFGC(){
        /**
         * Checking if file_get_contents() can read remote files
        */
        $res = ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled';
        if($this->console){
            if($res=='Enabled'){ return true; }else{ return 'Get remote files is disabled.'; }
        }
        if($res=='Enabled'){ $color = '#23BD17'; }else{ $color = '#BD1717'; }
        $this->content .= '<table><tr><td>Get remote files</td><td style="color:'.$color.';">'.$res.'</td></tr></table>';

    }
    public function Styling(){
        $this->content .= '
        <style>
            table{
                width:600px;
                }
            td{
                border:1px solid #ccc;
                padding-left:5px;
                padding-right:5px;
                font-size:13px;
                }
        </style>
        ';
    }
    public function CheckINI(){
        /**
         * Checking if file_get_contents() can read remote files
        */
        $res = ini_get('short_open_tag') ? 'Enabled' : 'Disabled';
        if($this->console){
            if($res=='Enabled'){ return true; }else{ return 'Get remote files is disabled.'; }
        }
        if($res=='Enabled'){ $color = '#23BD17'; }else{ $color = '#BD1717'; }
        $this->content .= '<table><tr><td>Short_open_tag</td><td style="color:'.$color.';">'.$res.'</td></tr></table>';

    }
    public function RunTest(){
        if($this->console){            
            $result = $this->CheckCURL();                      
                if(strlen($result)>1){ $res['error'] = $result; unset($result); return $res;}            
            $result = $this->CheckINI();                            
                if(strlen($result)>1){ $res['error'] = $result; unset($result); return $res;}
            $result = $this->CheckRedableWritable();                            
                if(strlen($result)>1){ $res['error'] = $result; unset($result); return $res;}                
            return true;
        }else{
            $this->Styling();
            $this->CheckCURL();
            //$this->CheckFGC();
            $this->CheckINI();
            $this->CheckRedableWritable();    
        }
           
            
    }
    public function __destruct(){
        unset($this->content);
        unset($this->console);
        unset($this->currentFolder);
    }
    
}
/* Running in normal mode */    
if(strpos($_SERVER['REQUEST_URI'],'test.php')>0){
    //set_error_handler();
    $test = new TestSheeplaInstall(false);
    $test->RunTest();
    echo $test->content;    
}



?>