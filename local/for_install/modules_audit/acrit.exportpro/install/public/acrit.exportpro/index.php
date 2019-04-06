<?
$documentRoot = $_SERVER["DOCUMENT_ROOT"];

$filePath = $_SERVER["REQUEST_URI"];
if( stripos( $filePath, "xml" ) !== false ){
    if( stripos( $documentRoot.$filePath, "?" ) !== false ){
        $arRequest = explode( "?", $filePath );
        $filePath = $arRequest[0];
    }
}

$document = false;
if( file_exists( $documentRoot.$filePath ) ){
    $document = $documentRoot.$filePath;
}
elseif( file_exists( $documentRoot."/upload/acrit.exportpro/".$filePath ) ){
    $document = $documentRoot."/upload/acrit.exportpro/".$filePath;
}
elseif( file_exists( $documentRoot."/upload/".$filePath ) ){
    $document = $documentRoot."/upload/".$filePath;
}

$inputEncoding = $_REQUEST["encoding"];

if( $document ){
    if( stripos( $filePath, "xml" ) !== false ){
        header( "Expires: Thu, 19 Feb 1998 13:24:18 GMT");
        header( "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header( "Cache-Control: no-cache, must-revalidate");
        header( "Cache-Control: post-check=0,pre-check=0");
        header( "Pragma: no-cache");
        header( "Content-Type: application/xml; charset=".$inputEncoding );
        echo file_get_contents( $document );    
    }
    elseif( stripos( $filePath, "zip" ) !== false ){
        header( "Pragma: public" );
        header( "Expires: 0" );
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Cache-Control: public" );
        header( "Content-Type: application/zip" );
        header( "Content-Transfer-Encoding: Binary" );
        header( "Content-Length: ".filesize( $document ) );
        header( "Content-Disposition: attachment; filename=\"".basename( $document )."\"" );
        readfile( $document );
        exit;
    }
    else{
        header( "Pragma: public" );
        header( "Expires: 0" );
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Cache-Control: private", false );
        header( "Content-Type: text/csv" );
        header( "Content-Disposition: attachment;filename=".$filePath ); 
        
        echo file_get_contents( $document );    
    }
    die();
}
?>