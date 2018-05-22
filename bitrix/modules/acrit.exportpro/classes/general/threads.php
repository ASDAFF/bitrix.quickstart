<?php
class Threads{
    public $phpPath = "php";
    
    private $lastId = 0;
    private $descriptorSpec = array(
        0 => array( "pipe", "r" ),
        1 => array( "pipe", "w" )
    );
    private $handles = array();
    private $streams = array();
    private $results = array();
    private $pipes = array();
    private $timeout = 10;
    
    public function newThread( $filename, $params = array() ){
        if( !file_exists( $filename ) ){
            throw new ThreadsException( "FILE_NOT_FOUND" );
        }
        
        $params = addcslashes( serialize( $params ), '"' );
        $command = $this->phpPath." -q ".$filename.' --params "'.$params.'"';
        ++$this->lastId;
        
        $this->handles[$this->lastId] = proc_open( $command, $this->descriptorSpec, $pipes );
        $this->streams[$this->lastId] = $pipes[1];
        $this->pipes[$this->lastId] = $pipes;
        
        return $this->lastId;
    }
    
    public function iteration(){
        $result = array();
        if( !count( $this->streams ) ){
            return false;
        }
        $read = $this->streams;
        if( false ===stream_select( $read, $write = null, $except = null, $this->timeout ) ){
            return true;
        }
        elseif( count( $read ) ){
            foreach( $read as $stream ){
                $id = array_search( $stream, $this->streams );
                $content = stream_get_contents( $this->pipes[$id][1] );
     
                if( feof( $stream ) ){
                    fclose( $this->pipes[$id][0] );
                    fclose( $this->pipes[$id][1] );
                    proc_close( $this->handles[$id] );
                    unset( $this->handles[$id] );
                    unset( $this->streams[$id] );
                    unset( $this->pipes[$id] );
                }
                $result[] = $content;
            }
        }
        else{
            return true;
        }

        if( count( $result ) )
            return $result;
        
        return true;
    }

 
    public static function getParams(){
        foreach( $_SERVER["argv"] as $key => $argv ){
            if( $argv == "--params" && isset( $_SERVER["argv"][$key + 1] ) ){
                return unserialize( $_SERVER["argv"][$key + 1] );
            }
        }
        return false;
    }
}

class ThreadsException extends Exception {
}

class ThreadsSession{
    private $file = "";
    public $sCnt;
    
    public function Init( $name, $dir = "" ){
        if( empty( $dir ) )
            $dir .= __DIR__;    
        
        $this->file = $dir."/".$name;
    }
    
    public function Save( $key, $value, $part = 0 ){
        if( !empty( $this->file ) ){
            $sessionData = $this->Get( false, $part );
            if( !is_array( $sessionData ) )
                $sessionData = array();
            
            $sessionDataOrig = &$sessionData;
            $arKey = explode( "|", $key );
            $keyCnt = count( $arKey );
            for( $i = 0; $i < $keyCnt; $i++ ){
                if( $i < ( $keyCnt - 1 ) ){
                    if( !is_array( $sessionData[$arKey[$i]] ) )
                        $sessionData = array( $arKey[$i] => array() );
                    
                    $sessionData = &$sessionData[$arKey[$i]];
                }
                else{
                    if( !is_array( $sessionData ) )
                        $sessionData = array();
                    
                    $sessionData[$arKey[$i]] = $value;
                }
            }
            $file_tmp .= ( intval( $part ) > 0 ) ? $this->file."_".intval( $part ) : $this->file."";
            $file_tmp .= ".tss";
            @file_put_contents( $file_tmp, serialize( $sessionDataOrig ) );
        }
    }
    
    public function Get( $key = false, $part = false, $all = false ){
        if( !empty( $this->file ) ){
            if( $part === false ){
                $arFile = scandir( dirname( $this->file ) );
                $regStr = "#".basename( $this->file )."#";
                foreach( $arFile as $file ){
                    if( ( $file == "." ) || ( $file == ".." ) )
                        continue;
                        
                    if( preg_match( $regStr, $file ) ){
                        $sessionDataTmp = file_get_contents( dirname( $this->file)."/".$file );
                        $sessionData[] = unserialize( $sessionDataTmp );
                    }
                }
                $sessionDataTmp = $this->_Merge( $sessionData );
                unset( $sessionData );
                $sessionData = $sessionDataTmp;
            }
            else{
                $file_tmp .= ( intval( $part ) > 0 ) ? $this->file."_".intval( $part ) : $this->file."";
                $file_tmp .= ".tss";
                if( file_exists( $file_tmp ) )
                    $sessionData = file_get_contents( $file_tmp );
                
                $sessionData = unserialize( $sessionData );
                if( !is_array( $sessionData ) )
                    $sessionData = array();
            }
            
            if( $key ){
                $arKey = explode( "|", $key );
                $keyCnt = count( $arKey );
                for( $i = 0; $i < $keyCnt; $i++ ){
                    if( $i < ( $keyCnt - 1 ) ){
                        if( !is_array( $sessionData[$arKey[$i]] ) )
                            return null;
                        
                        $sessionData = $sessionData[$arKey[$i]];
                    }
                    else
                        $sessionData = $sessionData[$arKey[$i]];
                }
            }
            return $sessionData;
        }
    }
    
    public function Delete(){
        $arFile = scandir( dirname( $this->file ) );
        $regStr = "#".basename( $this->file )."#";
        foreach( $arFile as $file ){
            if( ( $file == "." ) || ( $file == ".." ) )
                continue;
            
            if( preg_match( $regStr, $file ) ){
                unlink( $file );
            }
        }
    }
    
    private function _Merge( $arr ){
        $data = count( $arr ) > 0 ? $arr[0] : array();
        $dataTmp = &$data;
        if( count( $arr ) > 1 ){
            unset( $arr[0] );
            foreach( $arr as $ar ){
                $this->_MergeRecursive( $ar, $dataTmp );
            }
        }
        return $data;
    }
    
    private function _MergeRecursive( $arr, &$data ){
        $this->sCnt++;
        foreach( $arr as $key => $value ){
            if( !isset( $data[$key] ) )
                $data[$key] = array();
            
            $notArray = false;
            $valueClone = array();
            if( is_array( $value ) ){
                $valueClone = $value;
                foreach( $value as $k => $val ){
                    if( is_array( $val ) ){
                        unset( $valueClone[$k] );
                    }
                    else
                        $notArray = true;
                }
            }
            
            if( is_array( $value ) && ( ( count( $value ) - count( $valueClone ) ) > 0 ) ){
                if( !empty( $valueClone ) ){
                    $data[$key] = array_unique( array_merge( $data[$key], $valueClone ) );
                }
                $this->_MergeRecursive( $value, $data[$key] );
            }
            elseif( is_array( $value ) ){
                $data[$key] = array_unique( array_merge( $data[$key], $value ) );
            }
            else{
                if( gettype($key) == "string" ){
                    $data[$key] = $value;
                }
            }
        }
    }
}