<?
class OZON{
    private $appID;
    private $signKey;
    private $token = null; 
    private $expiration = 0;
    
    private $services = array(
        "get_types" => "merchants/products/types",
        "get_all_types" => "merchants/products/types/all",
        "get_all_products" => "merchants/products/all",
        "get_description" => "merchants/xsd/description/#ID#",
    );
    
    public function __construct( $appID, $signKey ){
        $this->appID = $appID;
        $this->signKey = $signKey;
    }
    
    public function GetToken(){
        global $USER;
        if( is_array( $_SESSION[$USER->GetSessionHash()."-".$USER->GetID()]["OZON"] ) ){
            $this->token = $_SESSION[$USER->GetSessionHash()."-".$USER->GetID()]["OZON"]["TOKEN"];
            $this->expiration = $_SESSION[$USER->GetSessionHash()."-".$USER->GetID()]["OZON"]["EXPIRATION"];
        }
                                                           
        if( ( $this->token != null ) && ( $this->expiration > time() ) ){
            return $this->token;     
        }
        else{
            $s = curl_init(); 
            curl_setopt( $s, CURLOPT_URL, "https://api.ozon.ru/auth/token/merchants?applicationid={$this->appID}&sign=".hash_hmac( "sha1", "/auth/token/merchants?applicationid={$this->appID}", $this->signKey ) );
            curl_setopt( $s, CURLOPT_HTTPHEADER, array( "X-ApiVersion: 0.1" ) );
            curl_setopt( $s, CURLOPT_RETURNTRANSFER, true );
            $reply = curl_exec( $s );
            $reply = json_decode( $reply, true );
            if( isset( $reply["token"] ) ){
                $this->token = $reply["token"];
                $this->expiration = intval( $reply["expiration"] ) + time();
                $_SESSION[$USER->GetSessionHash()."-".$USER->GetID()]["OZON"]["TOKEN"] = $this->token;
                $_SESSION[$USER->GetSessionHash()."-".$USER->GetID()]["OZON"]["EXPIRATION"] = $this->expiration;
            }
            //curl_close($s);
            return $reply["responseStatus"];
        }
        return $this->token;
    }
    
    private function Process( $service, $id = false, $jsdecode = true ){
        $token = $this->GetToken();
        
        $s = curl_init();
        if( $id !== false )
            $service = str_replace( "#ID#", $id, $service );
                    
        curl_setopt( $s, CURLOPT_URL, "https://api.ozon.ru/$service" );//?applicationid={$this->appID}&token=$token
        curl_setopt( $s, CURLOPT_HTTPHEADER, array(
            "x-ApiVersion: 0.1",
            "accept:application/json",
            "x-applicationid:{$this->appID}",
            "x-token:{$token}",
        ));
        curl_setopt( $s, CURLOPT_RETURNTRANSFER, true );
        $reply = curl_exec( $s );
        curl_close( $s );
        
        if( $jsdecode ){
            $reply = mb_convert_encoding( $reply, "utf8", "cp1251" );
            try{
                $reply = \Bitrix\Main\Web\Json::decode( $reply );
            }
            catch( Exception $e ){
                $reply = array();
            }
        }
        
        return $reply;
    }
    
    public function GetTypes(){
        $data = $this->Process( $this->services["get_types"] );
        return is_array( $data ) ? $data["ProductTypes"] : array();
    }
    
    public function GetAllTypes(){
        $data = $this->Process( $this->services["get_all_types"] );
        return is_array( $data ) ? $data["ProductTypes"] : array();
    }
    public function GetAllProducts(){
        return $this->Process( $this->services["get_all_products"] );
    }
    
    public function GetDescription( $id )
    {
        $data = $this->Process( $this->services["get_description"] );
        return is_array( $data ) ? $data["Xsd"] : "";
    }
}