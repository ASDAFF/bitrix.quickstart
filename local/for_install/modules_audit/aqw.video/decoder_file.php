<?php
/*
Script name: Decoder
Script URI: http://www.verstaem.com/examples/decoder/decoder.php
Description: Script for changing files charset
Version: 1.1 
Author: Alorian  
Author URI: http://www.verstaem.com/ 
*/


/*************************************************************
* 
* HELP 
* README
* 
* All settings located at one string:
* $decoder = new CDecoder(string $FROM, string $TO, array $EXTENSIONS);
* 
* $FROM value is encoding which support iconv() php function
* Look at http://www.php.net/manual/en/function.iconv.php for more information about iconv()
* --> For example 'UTF-8'
* 
* $TO value is encoding which support iconv() php function
* Look at http://www.php.net/manual/en/function.iconv.php for more information about iconv()
* --> For example 'windows-1251'
* 
* $EXTENSIONS is array of file extensions which will be converted.
* --> For example array('txt', 'php')
* 
**************************************************************/

//SETTINGS STRING
$FROM = 'windows-1251';
$TO = 'utf-8';
$EXTENSIONS = array(
    'php',
);
$decoder = new CDecoder($FROM, $TO, $EXTENSIONS);

class CDecoder
{
    var $inCharset = '';
    var $outCharset = '';
    var $extenstions = array();

    function needToConvert($str){
        if(preg_match('#.#u', $str) ){
            $utf = true;
        }else{
            $utf = false;
        }
        
        if($utf == false && stripos($this->outCharset, 'utf') !== false)
        {
            return true;
        }
        elseif($utf == true && stripos($this->outCharset, 'utf') === false)
        {
            return true;
        }
        
        return false;
    }
    
    function fileDecode($path){

        if(!is_file($path)){
            print "No such file ".$path." <br />";
            return false;
        }

        
        $size = filesize($path);
        if($size == 0){
            print "File ".$path." is EMPTY<br />";
            return false;
        }
        
        $file = fopen($path, 'r+');
        $content = fread($file, $size);
        
        if($this->needToConvert($content))
        {
            rewind($file);
            ftruncate($file, 0);
            fwrite($file, iconv($this->inCharset, $this->outCharset, $content));
            
            print "Decoded <span style='font-weight:bold; color:#FF0000;'>".$this->inCharset."</span> to <span style='font-weight:bold; color:#0000FF;'>".$this->outCharset.'</span><br />'.$path.'<br /><br />';
        }else{
            print "File ".$path." already at ".$this->outCharset."<br>";
        }

        fclose($file);
    }
    
    function getSubDirs($dir){
        if(is_dir($dir)){
            $path = opendir($dir);
            $subDirs = array();
            while(($file = readdir($path)) !== false){
                $filePath = $dir.'/'.$file;
                if($file != '.' && $file != '..' && $filePath != $_SERVER['SCRIPT_FILENAME']){
                    $info = pathinfo($filePath);
                    if(is_dir($filePath)){
                        $subDirs[] = $filePath;
                    }elseif(in_array($info["extension"], $this->extensions)){
                        $this->fileDecode($filePath);
                    }
                }
            }
            closedir($path);
            return $subDirs;
        }
    }
    
    public function CDecoder($inCharset, $outCharset, $extensions){
        if(!empty($inCharset))
            $this->inCharset = $inCharset;
        else{
            echo "No <span style='color:#F00;'>FROM</span> encoding given.<br>";
            return;
        }
        
        if(!empty($outCharset))
            $this->outCharset = $outCharset;
        else{
            echo "No <span style='color:#F00;'>TO</span> encoding given.<br>";
            return;
        }
        
        if(!empty($extensions))
            $this->extensions = $extensions;
        else{
            echo "No file extensions given <br>";
            return;
        }
        
        $startDir = getcwd();
        $dirs = $this->getSubDirs($startDir);
        while($dirs){
            $subDirs = $this->getSubDirs(reset($dirs));
            $dirs = array_merge($dirs, $subDirs);
            unset($dirs[0]);
            echo '<pre>'; print_r($dirs); echo '</pre><hr />';
        }
        echo "<span style='font-weight:bold; font-size:3em; color:#00FF00;'>All operations done</span>";
    }
}