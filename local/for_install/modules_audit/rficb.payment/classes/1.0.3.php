<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CRficbPayment {
    static $module_id = "rficb.payment";

    static function GetKey($key="") {
        return $key ? $key : COption::GetOptionString(CRficbPayment::$module_id, "key", "");
    }

    static function GetSecretKey($secret_key="") {
        return $secret_key ? $secret_key : COption::GetOptionString(CRficbPayment::$module_id, "secret_key", "");
    }

    function VerifyCheck($data){
        $parameters = array ($data["tid"], $data["name"], $data["comment"], 
            $data["partner_id"], $data["service_id"], $data["order_id"], $data["type"],
            $data["partner_income"], $data["system_income"],$data["test"],CRficbPayment::GetSecretKey());
        $given_check = $data["check"];
        $generated_check = md5(join('',$parameters));
        return ($given_check === $generated_check);
    }   
}
?>
