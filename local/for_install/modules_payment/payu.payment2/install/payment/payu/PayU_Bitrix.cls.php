<?php

class PayU_Bitrix
{ 
	var $luUrl = "https://secure.payu.ru/order/lu.php", $button = "<input type='submit' class='goPayment' value='Перейти к оплате'>", $debug = 0, $showinputs = "hidden", $isWinEncode = false, $encoding = 'windows-1251';
	private static $Inst = false, $merchant, $key;
	private $isEncode = false;

	private $data = array(), $dataArr = array(), $answer = "", $userFormContent;
	private $LUcell = array(
		'MERCHANT'        => 1,
		'ORDER_REF'       => 0,
		'ORDER_DATE'      => 1,
		'ORDER_PNAME'     => 1,
		'ORDER_PGROUP'    => 0,
		'ORDER_PCODE'     => 1,
		'ORDER_PINFO'     => 0, 
		'ORDER_PRICE'     => 1,
		'ORDER_QTY'       => 1,
		'ORDER_VAT'       => 1,
		'ORDER_SHIPPING'  => 1,
		'PRICES_CURRENCY' => 1,
        'ORDER_PRICE_TYPE'=> 1,
	);

	private $IPNcell = array( "IPN_PID", "IPN_PNAME", "IPN_DATE", "ORDERSTATUS" );
    public $checkHash;

    public function getIPNAnswer() {
        return $this->answer;
    }

	private function __construct()
	{
	}

	private function __clone()
	{ 
	}

	public function __toString()
	{
		return ( $this->answer == "" ) ? "<!-- Answer are not exists -->" : $this->answer;
	}

	public static function getInst()
	{
		if ( self::$Inst === false )
		{
			self::$Inst = new PayU_Bitrix();
		}

		return self::$Inst;
	}

	public static function getKey() 
	{
		return [ self::$key , self::$merchant ];
	}

	#---------------------------------------------
	# Add all options for PayU_Bitrix object.
	# Can change all public variables;
	# $opt = array( merchant, secretkey, [ luUrl, debug, button ] );
	#---------------------------------------------
	function setOptions( $opt = array() )
	{
		if ( !isset( $opt['merchant'] ) || !isset( $opt['secretkey'] ) )
		{
			die( "No params" );
		}
		self::$merchant = $opt['merchant'];
		self::$key      = $opt['secretkey'];
		unset( $opt['merchant'], $opt['secretkey'] );
		if ( count($opt) === 0 )
		{
			return $this;
		}
		foreach ( $opt as $k => $v )
		{
			$this->$k = $v;
		}

		return $this;
	}

	function setData( $array = NULL )
	{
		if ( $array === NULL )
		{
			die( "No data" );
		}
		$this->dataArr = $array;

		return $this;
	}

	#--------------------------------------------------------
	#	Generate HASH
	#--------------------------------------------------------
	function Signature( $data = NULL )
	{
		$str = "";

		foreach ( $data as $v )
		{
			$str .= $this->convData($v);
		}

		if( strtoupper($this->encoding) != 'UTF-8' )
		{
			$str = iconv(strtoupper($this->encoding),'UTF-8',$str);
		}
		if(function_exists('hash_hmac'))
		{
			return hash_hmac("md5", $str, self::$key);
		}

		return $this->hash_hmac("md5", self::$key,$str);
	}

	function SignatureFromStr($str) 
	{
		if( strtoupper($this->encoding) != 'UTF-8' ) 
		{
			$str = iconv(strtoupper($this->encoding),'UTF-8',$str);
		}
		if(function_exists('hash_hmac'))
		{
			return hash_hmac("md5", $str, self::$key);
		}

		return $this->hash_hmac("md5", self::$key,$str);
	}
	
	function hash_hmac($algo, $data, $key, $raw_output = false)
	{
		$algo = strtolower($algo);
		$pack = 'H'.strlen($algo($key));
		$size = 64;
		$opad = str_repeat(chr(0x5C), $size);
		$ipad = str_repeat(chr(0x36), $size);

		if (strlen($key) > $size) {
			$key = str_pad(pack($pack, $algo($key)), $size, chr(0x00));
		} else {
			$key = str_pad($key, $size, chr(0x00));
		}

		for ($i = 0; $i < strlen($key) - 1; $i++) {
			$opad[$i] = $opad[$i] ^ $key[$i];
			$ipad[$i] = $ipad[$i] ^ $key[$i];
		}

		$output = $algo($opad.pack($pack, $algo($ipad.$data)));

		return ($raw_output) ? pack($pack, $output) : $output;
	}

	private function convString( $string )
	{
		return $this->utf8_strlen($string) . $this->makeValidStr($string);
	}

	private function convArray( $array )
	{
		$return = '';
		foreach ( $array as $v )
		{
			$return .= $this->convString($v);
		}

		return $return;
	}

	private function convData( $val )
	{
		return ( is_array($val) ) ? $this->convArray($val) : $this->convString($val);
	}
	#----------------------------

	#====================== LU GENERETE FORM =================================================

	public function LU()
	{
		$arr = & $this->dataArr;
		if ( $this->isWinEncode )
		{
			$this->isEncode = true;
		}
		$arr['MERCHANT'] = self::$merchant;
		if ( !isset( $arr['ORDER_DATE'] ) )
		{
			$arr['ORDER_DATE'] = date("Y-m-d H:i:s");
		}
		$arr['TESTORDER']  = ( $this->debug == 1 ) ? "TRUE" : "FALSE";
		$arr['DEBUG']      = $this->debug;

		$arr['ORDER_HASH'] = $this->Signature($this->checkArray($arr));
		$this->data = $arr;

		return $this;
	}

	#-----------------------------
	# Check array for correct data
	#-----------------------------
	private function checkArray( $data )
	{
		$this->cells = array();
		$ret         = array();
		$LUcell = $this->LUcell;
		foreach ( $LUcell as $k => $v )
		{
			if ( !empty( $data[$k] ) )
			{
				$ret[$k] = $data[$k];
			}
			elseif ( $v == 1 )
			{
				die( "$k is not set" );
			}
		}
		return $ret;
	}

	#-----------------------------
	# Method which create a form
	#-----------------------------
	public function getForm( $arExtFields = array() )
	{
		$form = '<form id="PayU_Form" method="post" action="' . $this->luUrl . '" accept-charset="UTF-8">';
		foreach ( $this->data as $k => $v )
		{
			$form .= $this->makeString($k, $v);
		}

		if(!empty($arExtFields))
		{
			$form .='<div id="PayU_Fields_Automode">';

			foreach($arExtFields as $field)
			{
				if(!empty($field['0']) && is_array($field))
				{
					foreach ( $field as $v )
					{
						if ( !empty($field['name']) && ( !empty($field['label']['name']) || $field['type']=='hidden') )
						{
							$val = !empty($field['value'])?$this->makeValidStr($field['value']):'';
							$form .= '
								<label '.(!empty($field['label']['attributes'])?$field['label']['attributes']:'').'>
									'. $field['label']['name'] .'&nbsp;&nbsp; <input '.(!empty($field['attributes'])?$field['attributes']:'').' type="' . (!empty($field['type'])?$field['type']:$this->showinputs) . '" name="' . strtoupper($field['name']) . '" value="' . htmlspecialchars($val,ENT_COMPAT,$this->encoding) . '">
								</label>';
						}
					}
				}

				if ( !empty($field['name']) && ( !empty($field['label']['name']) || $field['type']=='hidden' ) )
				{
					$val = !empty($field['value'])?$this->makeValidStr($field['value']):'';
					$form .= '
						<label '.(!empty($field['label']['attributes'])?$field['label']['attributes']:'').'>
							'. $field['label']['name'] .'&nbsp;&nbsp; <input '.(!empty($field['attributes'])?$field['attributes']:'').' type="' . (!empty($field['type'])?$field['type']:$this->showinputs) . '" name="' . strtoupper($field['name']) . '" value="' . htmlspecialchars($val,ENT_COMPAT,$this->encoding) . '">
						</label>';
				}
			}
			$form .= '</div>';
		}

		$form .= implode('<br/>',$this->userFormContent);

		return $form . $this->button . "</form>";
	}

	public function addFormContent($html)
	{
		$this->userFormContent[md5($html)] = $html;
		return $this;
	}

	#-----------------------------
	# Make inputs for form
	#-----------------------------
	private function makeString( $name, $val )
	{
		$str = "";
		foreach ( $val as $v )
		{
			$str .= $this->makeString($name . '[]', $v);
		}

		if ( !is_array($val) )
		{
			$val = $this->makeValidStr($val);
			return '<input type="' . $this->showinputs . '" name="' . strtoupper($name) . '" value="' . htmlspecialchars($val,ENT_COMPAT,$this->encoding) . '">' . "\n";
		}

		return $str;
	}

	#======================= END LU =====================================

	#======================= IPN READ ANSWER ============================

	public function IPN()
	{
		$arr = & $this->dataArr;
		$arr = $_POST;

		foreach ( $this->IPNcell as $name )
		{
			if ( !isset( $arr[$name] ) )
			{
				die( "Incorrect data" );
			}
		}
		$this->cells = $this->IPNcell;
		$hash        = $arr["HASH"];
		unset( $arr["HASH"] );
		$sign = $this->Signature($arr);

		
		if ( $hash != $sign )
		{
            $this->checkHash = false;
			return $this;
		}
        $this->checkHash = true;

		$datetime = date("YmdHis");
		$sign     = $this->Signature(array(
			"IPN_PID"   => $arr["IPN_PID"][0],
			"IPN_PNAME" => $arr["IPN_PNAME"][0],
			"IPN_DATE"  => $arr["IPN_DATE"],
			"DATE"      => $datetime
		));

		$this->answer = "<!-- <EPAYMENT>$datetime|$sign</EPAYMENT> -->";

		return $this;
	}

	#======================= END IPN ============================

	#======================= Check BACK_REF =====================
	function checkBackRef( $type = "http" )
	{
		$path   = $type . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		$tmp    = explode("?", $path);
		$url    = $tmp[0] . '?';
		$params = array();
		foreach ( $_GET as $k => $v )
		{
			if ( $k != "ctrl" )
			{
				$params[] = $k . '=' . rawurlencode($v);
			}
		}
		$url          = $url . implode("&", $params);
		$arr          = array( $url );
		$sign         = $this->Signature($arr);
		$this->answer = ( $sign === $_GET['ctrl'] ) ? true : false;

		return $this->answer;
	}

	#======================= END Check BACK_REF =================

	private function makeValidStr( $str )
	{
		return $str;
	}
	private function utf8_strlen( $str )
	{
		global $APPLICATION;

		if ( strtolower($this->encoding) != 'utf-8' )
		{
			$str = $APPLICATION->ConvertCharset( $str, $this->encoding, 'utf-8' );
		}
		return mb_strlen($str,'8bit');
	}
}

?>
