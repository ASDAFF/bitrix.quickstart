<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CRficbPayment {
    static $module_id = "rficb.payment";

    static function GetKey($sSiteID="")
	{
		$sKey = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_key", "");
		if(!strlen($sKey)) $sKey = COption::GetOptionString(CRficbPayment::$module_id, "key", ""); // Old version
        return $sKey;
    }

    static function GetSecretKey($sSiteID="")
	{
		$sSecretKey = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_secret_key", "");
		if(!strlen($sSecretKey)) $sSecretKey = COption::GetOptionString(CRficbPayment::$module_id, "_secret_key", ""); // Old version
        return $sSecretKey;
    }

    static function GetWidget($sSiteID="")
	{
		$sWidget = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_widget", "");
		if(!strlen($sWidget)) $sWidget = COption::GetOptionString(CRficbPayment::$module_id, "_widget", ""); // Old version
        return $sWidget;
    }
	static function GetWidgetType($sSiteID="")
	{
		$sWidgetType = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_widgettype", "");
		if(!strlen($sWidgetType)) $sWidgetType = COption::GetOptionString(CRficbPayment::$module_id, "_widgettype", ""); // Old version
        return $sWidgetType;
    }
	static function GetHoldMail($sSiteID="")
	{
		$sHoldMail = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_holdemail", "");
		if(!strlen($sHoldMail)) $sHoldMail = COption::GetOptionString(CRficbPayment::$module_id, "holdemail", ""); // Old version
        return $sHoldMail;
    }
	static function GetHoldStatus($sSiteID="")
	{
		$sHoldStatus = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_holdstatus", "");
		if(!strlen($sHoldStatus)) $sHoldStatus = COption::GetOptionString(CRficbPayment::$module_id, "holdstatus", ""); // Old version
        return $sHoldStatus;
    }
	static function GetCommission($sSiteID="")
	{
		$sCommission = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_commission", "");
		if(!strlen($sCommission)) $sCommission = COption::GetOptionString(CRficbPayment::$module_id, "commission", ""); // Old version
        return $sCommission;
    }
	static function GetPayType($sSiteID="")
	{
		$sPayType = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_paytype", "");
		if(!strlen($sPayType)) $sPayType = COption::GetOptionString(CRficbPayment::$module_id, "paytype", ""); // Old version
        return $sPayType;
    }
	static function GetPayCart($sSiteID="")
	{
		$sPayCart = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_paycart", "");
		if(!strlen($sPayCart)) $sPayCart = COption::GetOptionString(CRficbPayment::$module_id, "paycart", ""); // Old version
        return $sPayCart;
    }
	static function GetPayWM($sSiteID="")
	{
		$sPayWM = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_paywm", "");
		if(!strlen($sPayWM)) $sPayWM = COption::GetOptionString(CRficbPayment::$module_id, "paywm", ""); // Old version
        return $sPayWM;
    }
	static function GetPayYM($sSiteID="")
	{
		$sPayYM = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_payym", "");
		if(!strlen($sPayYM)) $sPayYM = COption::GetOptionString(CRficbPayment::$module_id, "payym", ""); // Old version
        return $sPayYM;
    }
	static function GetPayMC($sSiteID="")
	{
		$sPayMC = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_paymc", "");
		if(!strlen($sPayMC)) $sPayMC = COption::GetOptionString(CRficbPayment::$module_id, "paymc", ""); // Old version
        return $sPayMC;
    }
	static function GetPayQiwi($sSiteID="")
	{
		$sPayQiwi = COption::GetOptionString(CRficbPayment::$module_id, "{$sSiteID}_payqiwi", "");
		if(!strlen($sPayQiwi)) $sPayQiwi = COption::GetOptionString(CRficbPayment::$module_id, "payqiwi", ""); // Old version
        return $sPayQiwi;
    }

    function VerifyCheck($data,$sSiteID="")
	{
        $parameters = array ($data["tid"], $data["name"], $data["comment"], 
            $data["partner_id"], $data["service_id"], $data["order_id"], $data["type"],
            $data["partner_income"], $data["system_income"],$data["test"],CRficbPayment::GetSecretKey($sSiteID));
        $given_check = $data["check"];
        $generated_check = md5(join('',$parameters));
        return ($given_check === $generated_check);
    }   
	function VerifyCheckHold($param,$sSiteID="")
	{
        $check = $param['tid'].$param['name'].$param['comment'].$param['partner_id'].$param['service_id'].$param['order_id'].$param['type'].$param['cost'].$param['income_total'].$param['income'].$param['partner_income'].$param['system_income'].$param['command'].$param['phone_number'].$param['email'].$param['result'].$param['resultStr'].$param['date_created'].$param['version'].CRficbPayment::GetSecretKey($sSiteID);
        $given_check = $param["check"];
        $generated_check = md5($check);
        return ($given_check === $generated_check);
    } 
}
?>
