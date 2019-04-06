<?php
/**
* Sheepla XML API client class is a class made for sending requests from the shop in to the sheepla application via XML API
* and to support basic configuration screens of the shop
* @author Orba (biuro{at}orba.pl)
* @requirements:
* 	- stream_context_create() 	(http://php.net/manual/en/function.stream-context-create.php) \
* 	- file_get_contents()  		(http://php.net/manual/en/function.file-get-contents.php)
*  - DOMDocument 				(http://php.net/manual/en/class.domdocument.php)
*  - simplexml_load_string 	(http://php.net/manual/en/function.simplexml-load-string.php)
*  - json_encode 				(http://php.net/manual/en/function.json-encode.php)
*  - json_decode 				(http://php.net/manual/en/function.json-decode.php)
*  - hash (with 'sha256' algorithm) (http://php.net/manual/en/function.hash.php)
*/
class SheeplaClient
{
	/**
	* If this variable is set to true proxy will display all important information to screen
	* @var bool $debug
	*/
	protected $debugFlag = false;
	
	/**
	 * Array whit configuration variables for correct class work
	 * Structure array(
	 * 	'url' 	=> 'http://sheepla.pl:8080/',
	 * 	'key' 	=> '38xcjni8e9v9wjnc'
	 * )
	 * @var array $config
	 */
	protected $config = null;
	
	/**
	 * Protected variable used to create XML document
	 * @var DOMDocument $document
	 */
	protected $document = null;
	
	/**
	 * Class constructor method
	 * @param boolean $debug if true sets object to debug mode
	 * @param mixed $config if given as array (see class $config) will set object config
	 */
	public function __construct($config = null, $debug = false)
	{	  
	  
		if (is_bool($debug))
		{
			$this->setDebug($debug);
		}
		if (!is_null($config) && is_array($config))
		{
			$this->setConfig($config);
		}
	}
	
	/**
	* If object is in debug mode it will return all debug notifications to standard output
	* @param string $data
	*/
	protected function debug()
	{
		if ($this->debugFlag === true)
		{
			$arg_list = func_get_args();
			foreach ($arg_list as $v) 
			{
				echo date('Y-m-d H:i:s') . ' : <pre>' . htmlentities(print_r($v, true)) ."</pre> <br />\n\r";
			}
		}
	}
	
	/**
	* Method sends XML request to sheepla API
	* @param string $method name of sheepla API method
	* @param string $request XML string to be send
	* @return string XML response string from the sheeplaAPI
	*/
	protected function send ($method, $request)
	{  
		$this->debug('Preparing send method.');
		
		if(!$this->isValidConfig($this->config))
		{
			throw new Exception('Invalid config: '.var_export($this->config,true));
		}
		
		$result = false;
		
		if(function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec'))
		{
			$result = $this->sendCurl($method, $request);
		}
		else
		{
			$function_sel = (function_exists('ini_get') ? 'ini_get' : (function_exists('get_cfg_var') ? 'get_cfg_var' : false));
			if ($function_sel !== false)
			{
				if (call_user_func($function_sel, 'allow_url_fopen'))
				{
					$result = $this->sendStandard($method, $request);
				}
				else
				{
					throw new Exception('There is no function allowed to send data please add CURL or allow file_get_contents to use remote URL');
				}
			}
			else
			{
				$this->debug('I can\'t check the PHP configuration trying to send any way');
				$result = $this->sendStandard($method, $request);
				if ($result === false)
				{
					throw new Exception('No data recived from Sheepla please allow file_get_contents to use remote URL');
				}
			}
			
		}
        
		$this->debug('received response:', $result);
		
		return $result;
	}
	
	/**
	* Method sends XML request to sheepla API via file_get_contents function
	* @param string $method name of sheepla API method
	* @param string $request XML string to be send
	* @return string XML response string from the sheeplaAPI
	 */
	protected function sendStandard($method, $request)
	{         
		$this->debug('Using file_get_contents to send');
		$result = false;
		$context = stream_context_create(array('http' => array(
					'method' => "POST",
					'header' => "Content-Type: text/xml; charset=utf-8",
					'content' => $request,
					'content-length' => strlen($request)
		)));
		$this->debug('stream_context_create done', 'sending request to this url: ', $this->config['apiUrl'].$method, 'the request:', $request);
		if (strlen($request) > 0)
		{
			$result = file_get_contents($this->config['apiUrl'].$method, false, $context);
		}
		else
		{
			$result = file_get_contents($this->config['apiUrl'].$method);
		}

		return $result;
	}
	
	/**
	* Method sends XML request to sheepla API via CURL library
	* @param string $method name of sheepla API method
	* @param string $request XML string to be send
	* @return string XML response string from the sheeplaAPI
	*/
	protected function sendCurl($method, $request)
	{       
        
		$this->debug('Using CURL to send');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->config['apiUrl'].$method);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		$this->debug('sending request to this url: ', $this->config['apiUrl'].$method, 'the request:', $request);
		$result = curl_exec($ch);
		
		return $result;
	}
	
	/**
	 * Creates xml DOMDocument object whit basic structure and returns it
	 * @param string $requestName name of sheepla API method
	 * @param boolean $includeAuth if set to true the authentication element will be attached to DOMDocument
	 * @return DOMDocument
	 */
	protected function createRequestDom($requestName, $includeAuth = false)
	{
		$dom = new DOMDocument('1.0','utf-8');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
	
		$root = $dom->createElementNS("http://www.sheepla.pl/webapi/1_0",$requestName);
		$dom->appendChild($root);
	
		if($includeAuth)
		{
			$authNode = $dom->createElement("authentication");
			$authNode->appendChild($dom->createElement("apiKey", $this->config['adminApiKey']));
			$root->appendChild($authNode);
		}
		$this->document = $dom;
		return $dom;
	}
	
	/**
	 * Converts string to object
	 * @param string $xmlstring
	 * @return SimpleXMLElement
	 */
	protected function toSimpleXMLElement($xmlstring) {
		if ($xmlstring === false)
		{
			return array('errors' => array('Connection Error'));
		}
		$xml = new SimpleXMLElement($xmlstring);
		return $xml;
	}
	
	/**
	 * Validates order 
	 * @param array $order
	 * @return boolean
	 * @throws Exception
	 */
	protected function validateOrder($order)
	{
	    if($this->config['syncAll']==1){
	       $first = array('orderValue', 'orderValueCurrency', 'externalDeliveryTypeId', 'externalDeliveryTypeName', 'externalPaymentTypeId',
							'externalPaymentTypeName', 'externalBuyerId', 'externalOrderId', 'createdOn',
							'deliveryPrice', 'deliveryPriceCurrency');
	    }else{
	       $first = array('orderValue', 'orderValueCurrency', 'externalDeliveryTypeId', 'externalDeliveryTypeName', 'externalPaymentTypeId',
							'externalPaymentTypeName', 'externalBuyerId', 'externalOrderId', 'shipmentTemplate', 'createdOn',
							'deliveryPrice', 'deliveryPriceCurrency');   
	    }
		
		foreach ($first as $e)
		{
			if (!isset($order[$e]) || (empty($order[$e]) && !is_numeric($order[$e]) ))
			{
				throw new Exception('Order structure is wrong, the ' . $e . ' key is missing.');
			}
		}
		return true;
	}
	
	/**
	* Transforms order to XML DOMElement structure
	* the $order structure is (new - please change your code to use it)
	* (the old structure is still accepted but will be deprecated in the future)
	* array(
	* 	'orderValue' 				=> '100.00', 						// total order price in float
	* 	'orderValueCurrency' 		=> 'PLN', 							// ISO formated currency type
	* 	'externalDeliveryTypeId' 	=> '1', 							// shipment id from the shop
	* 	'externalDeliveryTypeName' 	=> 'Carrier', 						// shipment name from the shop
	* 	'externalPaymentTypeId' 	=> '2', 							// payment type id from the shop
	* 	'externalPaymentTypeName' 	=> 'PayPal', 						// payment type name from the shop
	* 	'externalCountryId' 		=> '1', 							// country id from the shop
	* 	'externalBuyerId' 			=> '25', 							// client id from the shop
	* 	'externalOrderId' 			=> '1829', 							// order id from the shop
	* 	'shipmentTemplate' 			=> '11', 							// shipment template id from the sheepla application
	* 	'comments' 					=> 'this is primary order', 		// additional comments about the order
	* 	'createdOn' 				=> '2004-02-12T15:19:21+00:00', 	// ISO 8601 date (added in PHP 5) (see http://php.net/manual/en/function.date.php format character 'c')
	*  'deliveryPrice'				=> 20.10,							// the delivery cost
	*  'deliveryPriceCurrency'		=> 'PLN',							// the delivery currency
	* 	'deliveryOptions' 			=> array (
	* 		'cod' 		=> '1', 										// is it cash on delivery, can be 0 or 1
	* 		'insurance' => '0', 										// is there insurance on this orders packages, can be 0 or 1
	* 		'plInPost'  => array (
	* 			'popId'		=> '12', 									// InPost's "paczkomat" id
	* 			'popName' 	=> 'WAW115', 								// InPost's "paczkomat" code name
	* 		),
	* 		'ruShopLogistics' => array(
	* 			'metroStationId' => 3									// information for ShopLogistics about metro station
	* 		)
	* 	),
	* 	'deliveryAddress' 			=> array (
	* 		'street' 		=> 'Plac Defilad 1', 						// delivery address street
	* 		'zipCode' 		=> '00-001', 								// delivery address street
	* 		'city' 			=> 'Warszawa', 								// delivery address city
	* 		'countryAlpha2Code' 	=> 'PL', 							// the country 2 letters
	* 		'firstName' 	=> 'Adam', 									// Recipient first name
	* 		'lastName' 		=> 'Kowalski', 								// Recipient last name
	* 		'phone' 		=> '600123123', 							// Recipient phone number
	* 		'email' 		=> 'adam.kowalski@polska.pl' 				// Recipient e-mail adders
	* 	)
	* )
	* the $order structure is (old still accepted)
	* array(
	* 	'orderValue' 				=> '100.00', 						// total order price in float
	* 	'orderValueCurrency' 		=> 'PLN', 							// ISO formated currency type
	* 	'externalDeliveryTypeId' 	=> '1', 							// shipment id from the shop
	* 	'externalDeliveryTypeName' 	=> 'Carrier', 						// shipment name from the shop
	* 	'externalPaymentTypeId' 	=> '2', 							// payment type id from the shop
	* 	'externalPaymentTypeName' 	=> 'PayPal', 						// payment type name from the shop
	* 	'externalCountryId' 		=> '1', 							// country id from the shop
	* 	'externalBuyerId' 			=> '25', 							// client id from the shop
	* 	'externalOrderId' 			=> '1829', 							// order id from the shop
	* 	'shipmentTemplate' 			=> '11', 							// shipment template id from the sheepla application
	* 	'comments' 					=> 'this is primary order', 		// additional comments about the order
	* 	'createdOn' 				=> '2004-02-12T15:19:21+00:00', 	// ISO 8601 date (added in PHP 5) (see http://php.net/manual/en/function.date.php format character 'c')
	*  'deliveryPrice'				=> 20.10,							// the delivery cost
	*  'deliveryPriceCurrency'		=> 'PLN',							// the delivery currency
	* 	'deliveryOptions' 			=> array (
	* 		'cod' 		=> '1', 										// is it cash on delivery, can be 0 or 1
	* 		'insurance' => '0', 										// is there insurance on this orders packages, can be 0 or 1
	* 		'popId'		=> '12', 										// InPost's "paczkomat" id
	* 		'popName' 	=> 'WAW115', 									// InPost's "paczkomat" code name
	* 		'metroStationId' => 3										// information for ShopLogistics about metro station
	* 	),
	* 	'deliveryAddress' 			=> array (
	* 		'street' 		=> 'Plac Defilad 1', 						// delivery address street
	* 		'zipCode' 		=> '00-001', 								// delivery address street
	* 		'city' 			=> 'Warszawa', 								// delivery address city
	* 		'countryAlpha2Code' 	=> 'PL', 							// the country 2 letters
	* 		'firstName' 	=> 'Adam', 									// Recipient first name
	* 		'lastName' 		=> 'Kowalski', 								// Recipient last name
	* 		'phone' 		=> '600123123', 							// Recipient phone number
	* 		'email' 		=> 'adam.kowalski@polska.pl' 				// Recipient e-mail adders
	* 	)
	* )
	* @param array $order array in specific structure
	* @param integer $iteration
	* @return DOMElement
	*/
	protected function orderToXml($order)
	{
		$this->debug('transforming order to xml, received order:', $order);
		
		$this->validateOrder($order);
		
		$_order = $this->document;
		$order_element = $_order->createElement('order');
		
		return $this->arrayToXML($order, $order_element);
	}
	
	/**
	 * Helps transform array to xml
	 * @param array $array
	 * @param DOMElement $parent
	 * @return DOMElement
	 */
	protected function arrayToXML($array, $parent, $branchToProduct = false)
	{        
		$elements = array();
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				if (is_numeric($key) && $parent->nodeName == 'orderItems')
				{
					$tmp = $this->document->createElement('orderItem');
				}
				else
				{                
                    if($branchToProduct){
                        $tmp = $this->document->createElement('product');
                    }else{
                        $tmp = $this->document->createElement($key);
                    }
				}
				$elements[] = $this->arrayToXML($value, $tmp);
			}
			else
			{
				if (is_numeric($value) || !empty($value))
				{
        			$elements[] = $this->document->createElement($key, CSheepla::CustomIconv($value));
				}
			}
		}
		foreach($elements as $element)
		{
			$parent->appendChild($element);
		}
		return $parent;
	}
	
    
    protected function arrayToXMLAttribute($array,&$domDocument,&$parent){  
		foreach ($array as $key => $value)
		{
            if ( !empty($value) && !is_array($value) ){
                $domElement = $domDocument->createElement('attribute','');
                $domAttribute = $domDocument->createAttribute('name');
                $domAttribute->value = $key;
                $domElement->appendChild($domAttribute);
                $domAttribute = $domDocument->createAttribute('value');
                $domAttribute->value = htmlspecialchars(CSheepla::CustomIconv($value));
                $domElement->appendChild($domAttribute);
                
                $parent->appendChild($domElement);
			}
		}       
		return $parent;
    }
	/**
	 * Checks is given config correct
	 * @param array $config
	 * @return boolean
	 */
	protected function isValidConfig($config)
	{
		if (is_array($config) 
			&& isset($config['adminApiKey']) 
			&& isset($config['publicApiKey'])
            && isset($config['apiUrl'])
            && isset($config['jsUrl'])
            && isset($config['cssUrl'])
            && (sizeof($config)>5)
            ){
				return true;
			}
		
		return false;
	}
	
	/**
	 * Helps in array validation
	 * @param array $row
	 * @param array $required_keys
	 * @return boolean
	 */
	protected function isArrayValid($row, $required_keys)
	{
		foreach($required_keys as $key)
		{
			if(!isset($row[$key]) || $row[$key] === null) {
				return false;
			}
		}
		return true;
	}

	/**
	* Helps mapping xml to php array
	* @param SimpleXMLElement $row
	* @param array $kes_map
	*/
	protected function mapXML($row, $kes_map)
	{
		$result = array();
		foreach($kes_map as $xml_key => $array_key)
		{
			if($xml_key == 'errors' && ($row instanceof SimpleXMLElement)) {
				$a = json_decode(json_encode($row->errors), true);
				$result[$array_key] = (empty($a) ? null : $a);
			}
			elseif (isset($row->{$xml_key})) {
				$result[$array_key] = (string)$row->{$xml_key};
			}
			elseif(!isset($row[$xml_key])) {
				$result[$array_key] = null;
			}
			else {
				$result[$array_key] = (string)$row[$xml_key];
			}

			// if site encoding is windows-1251 they need to convert result array to windows-1251
			if (!strstr(strtolower(LANG_CHARSET), "utf"))
                        	$result[$array_key] = iconv('utf-8', LANG_CHARSET, $result[$array_key]);
		}
		return $result;
	}
	
	
	/**
	 * Changes Sheepla API key to its public form needed by WidgetAPI
	 * @param string $key
	 * @return string
	 */
	public function generateWidgetApiKey($key)
	{
		return strtolower(hash('sha256', $key));
	}
	
	/**
	* Sets debug mode
	* @param boolean $value
	*/
	public function setDebug($value)
	{
		$this->debugFlag = (bool)$value;
		$this->debug('Debug mode ON');
		return $this->debugFlag;
	}	
	/**
	 * Sets config
	 * @param array $value
	 * @return boolean
	 */
	public function setConfig($value)
	{
		if ($this->isValidConfig($value))
		{
			$this->config = $value;
			$this->debug('Config from array:', $value);
			return true;
		}

		return false;
	}
	
	/**
	 * Returns current configuration
	 * @return mixed
	 */
	public function getConfig()
	{
		return $this->config;
	}
	
	/**
	* Checks is config given in the __construct method are valid whit sheepla
	* @return boolean
	*/
	public function validAccount()
	{
		try {
			$response = $this->getShipmentsList(0, 1);
			if (isset($response['shipmentsFound']))
			{
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * Gets available shipment templates for account given in the config
	 * @return array
	 * @throws Exception
	 */
	public function getShipmentTemplates ()
	{
		$dom = $this->createRequestDom('getShipmentTemplatesRequest', true);
		$xml = $dom->saveXML();
		
		$result = array();
		
		$data = $this->toSimpleXMLElement($this->send('getShipmentTemplates',$xml));
		
		if (isset($data->templates) && isset($data->templates->template))
		{
			$keys = array(
				'id' => 'id',
				'name' => 'name',
				'carrierName' => 'carrierName',
				'isCod' => 'isCod',
				'baseServiceCode' => 'baseServiceCode'
			);
			foreach($data->templates->template as $t) {
				$result[] = $this->mapXML($t, $keys);
				if(!$this->isArrayValid($t, $keys)) {
					throw new Exception('The response XML structure is wrong pleas update Client');
				}
			}
			return $result;
		}
		throw new Exception('The response XML structure is wrong');
	}
	
	/**
	* Sends given order to Sheepla system and returns request result
	* the $order structure is (new - please change yore code to use it)
	* (the old structure is still accepted but will be deprecated in the future)
	* array(
	* 	'orderValue' 				=> '100.00', 						// total order price in float
	* 	'orderValueCurrency' 		=> 'PLN', 							// ISO formated currency type
	* 	'externalDeliveryTypeId' 	=> '1', 							// shipment id from the shop
	* 	'externalDeliveryTypeName' 	=> 'Carrier', 						// shipment name from the shop
	* 	'externalPaymentTypeId' 	=> '2', 							// payment type id from the shop
	* 	'externalPaymentTypeName' 	=> 'PayPal', 						// payment type name from the shop
	* 	'externalCountryId' 		=> '1', 							// country id from the shop
	* 	'externalBuyerId' 			=> '25', 							// client id from the shop
	* 	'externalOrderId' 			=> '1829', 							// order id from the shop
	* 	'shipmentTemplate' 			=> '11', 							// shipment template id from the sheepla application
	* 	'comments' 					=> 'this is primary order', 		// additional comments about the order
	* 	'createdOn' 				=> '2004-02-12T15:19:21+00:00', 	// ISO 8601 date (added in PHP 5) (see http://php.net/manual/en/function.date.php format character 'c')
	*  'deliveryPrice'				=> 20.10,							// the delivery cost
	*  'deliveryPriceCurrency'		=> 'PLN',							// the delivery currency
	* 	'deliveryOptions' 			=> array (
	* 		'cod' 		=> '1', 										// is it cash on delivery, can be 0 or 1
	* 		'insurance' => '0', 										// is there insurance on this orders packages, can be 0 or 1
	* 		'plInPost'  => array (
	* 			'popId'		=> '12', 									// InPost's "paczkomat" id
	* 			'popName' 	=> 'WAW115', 								// InPost's "paczkomat" code name
	* 		),
	* 		'ruShopLogistics' => array(
	* 			'metroStationId' => 3									// information for ShopLogistics about metro station
	* 		)
	* 	),
	* 	'deliveryAddress' 			=> array (
	* 		'street' 		=> 'Plac Defilad 1', 						// delivery address street
	* 		'zipCode' 		=> '00-001', 								// delivery address street
	* 		'city' 			=> 'Warszawa', 								// delivery address city
	* 		'countryAlpha2Code' 	=> 'PL', 							// the country 2 letters
	* 		'firstName' 	=> 'Adam', 									// Recipient first name
	* 		'lastName' 		=> 'Kowalski', 								// Recipient last name
	* 		'phone' 		=> '600123123', 							// Recipient phone number
	* 		'email' 		=> 'adam.kowalski@polska.pl' 				// Recipient e-mail adders
	* 	)
	* )
	* the $order structure is (old still accepted)
	* array(
	* 	'orderValue' 				=> '100.00', 						// total order price in float
	* 	'orderValueCurrency' 		=> 'PLN', 							// ISO formated currency type
	* 	'externalDeliveryTypeId' 	=> '1', 							// shipment id from the shop
	* 	'externalDeliveryTypeName' 	=> 'Carrier', 						// shipment name from the shop
	* 	'externalPaymentTypeId' 	=> '2', 							// payment type id from the shop
	* 	'externalPaymentTypeName' 	=> 'PayPal', 						// payment type name from the shop
	* 	'externalCountryId' 		=> '1', 							// country id from the shop
	* 	'externalBuyerId' 			=> '25', 							// client id from the shop
	* 	'externalOrderId' 			=> '1829', 							// order id from the shop
	* 	'shipmentTemplate' 			=> '11', 							// shipment template id from the sheepla application
	* 	'comments' 					=> 'this is primary order', 		// additional comments about the order
	* 	'createdOn' 				=> '2004-02-12T15:19:21+00:00', 	// ISO 8601 date (added in PHP 5) (see http://php.net/manual/en/function.date.php format character 'c')
	*  'deliveryPrice'				=> 20.10,							// the delivery cost
	*  'deliveryPriceCurrency'		=> 'PLN',							// the delivery currency
	* 	'deliveryOptions' 			=> array (
	* 		'cod' 		=> '1', 										// is it cash on delivery, can be 0 or 1
	* 		'insurance' => '0', 										// is there insurance on this orders packages, can be 0 or 1
	* 		'popId'		=> '12', 										// InPost's "paczkomat" id
	* 		'popName' 	=> 'WAW115', 									// InPost's "paczkomat" code name
	* 		'metroStationId' => 3										// information for ShopLogistics about metro station
	* 	),
	* 	'deliveryAddress' 			=> array (
	* 		'street' 		=> 'Plac Defilad 1', 						// delivery address street
	* 		'zipCode' 		=> '00-001', 								// delivery address street
	* 		'city' 			=> 'Warszawa', 								// delivery address city
	* 		'countryAlpha2Code' 	=> 'PL', 							// the country 2 letters
	* 		'firstName' 	=> 'Adam', 									// Recipient first name
	* 		'lastName' 		=> 'Kowalski', 								// Recipient last name
	* 		'phone' 		=> '600123123', 							// Recipient phone number
	* 		'email' 		=> 'adam.kowalski@polska.pl' 				// Recipient e-mail adders
	* 	)
	* )
	* @param array $data
	*/
	public function createOrder($data)
	{
		$r = $this->createOrders(array(0 => $data));
		if (is_array($r) && isset($r[0])) {
			return $r[0];
		}
		return $r;
	}
	
	/**
	 * Sends given order list to Sheepla system and returns request result
	 * the $order structure is (new - please change yore code to use it)
	* (the old structure is still accepted but will be deprecated in the future)
	* array( 0 => array(
	* 	'orderValue' 				=> '100.00', 						// total order price in float
	* 	'orderValueCurrency' 		=> 'PLN', 							// ISO formated currency type
	* 	'externalDeliveryTypeId' 	=> '1', 							// shipment id from the shop
	* 	'externalDeliveryTypeName' 	=> 'Carrier', 						// shipment name from the shop
	* 	'externalPaymentTypeId' 	=> '2', 							// payment type id from the shop
	* 	'externalPaymentTypeName' 	=> 'PayPal', 						// payment type name from the shop
	* 	'externalCountryId' 		=> '1', 							// country id from the shop
	* 	'externalBuyerId' 			=> '25', 							// client id from the shop
	* 	'externalOrderId' 			=> '1829', 							// order id from the shop
	* 	'shipmentTemplate' 			=> '11', 							// shipment template id from the sheepla application
	* 	'comments' 					=> 'this is primary order', 		// additional comments about the order
	* 	'createdOn' 				=> '2004-02-12T15:19:21+00:00', 	// ISO 8601 date (added in PHP 5) (see http://php.net/manual/en/function.date.php format character 'c')
	*  'deliveryPrice'				=> 20.10,							// the delivery cost
	*  'deliveryPriceCurrency'		=> 'PLN',							// the delivery currency
	* 	'deliveryOptions' 			=> array (
	* 		'cod' 		=> '1', 										// is it cash on delivery, can be 0 or 1
	* 		'insurance' => '0', 										// is there insurance on this orders packages, can be 0 or 1
	* 		'plInPost'  => array (
	* 			'popId'		=> '12', 									// InPost's "paczkomat" id
	* 			'popName' 	=> 'WAW115', 								// InPost's "paczkomat" code name
	* 		),
	* 		'ruShopLogistics' => array(
	* 			'metroStationId' => 3									// information for ShopLogistics about metro station
	* 		)
	* 	),
	* 	'deliveryAddress' 			=> array (
	* 		'street' 		=> 'Plac Defilad 1', 						// delivery address street
	* 		'zipCode' 		=> '00-001', 								// delivery address street
	* 		'city' 			=> 'Warszawa', 								// delivery address city
	* 		'countryAlpha2Code' 	=> 'PL', 							// the country 2 letters
	* 		'firstName' 	=> 'Adam', 									// Recipient first name
	* 		'lastName' 		=> 'Kowalski', 								// Recipient last name
	* 		'phone' 		=> '600123123', 							// Recipient phone number
	* 		'email' 		=> 'adam.kowalski@polska.pl' 				// Recipient e-mail adders
	* 	)
	* ),
	* 	1 => array( ...
	* 	)
	* )
	* the $order structure is (old still accepted)
	* array(
	* 	'orderValue' 				=> '100.00', 						// total order price in float
	* 	'orderValueCurrency' 		=> 'PLN', 							// ISO formated currency type
	* 	'externalDeliveryTypeId' 	=> '1', 							// shipment id from the shop
	* 	'externalDeliveryTypeName' 	=> 'Carrier', 						// shipment name from the shop
	* 	'externalPaymentTypeId' 	=> '2', 							// payment type id from the shop
	* 	'externalPaymentTypeName' 	=> 'PayPal', 						// payment type name from the shop
	* 	'externalCountryId' 		=> '1', 							// country id from the shop
	* 	'externalBuyerId' 			=> '25', 							// client id from the shop
	* 	'externalOrderId' 			=> '1829', 							// order id from the shop
	* 	'shipmentTemplate' 			=> '11', 							// shipment template id from the sheepla application
	* 	'comments' 					=> 'this is primary order', 		// additional comments about the order
	* 	'createdOn' 				=> '2004-02-12T15:19:21+00:00', 	// ISO 8601 date (added in PHP 5) (see http://php.net/manual/en/function.date.php format character 'c')
	*  'deliveryPrice'				=> 20.10,							// the delivery cost
	*  'deliveryPriceCurrency'		=> 'PLN',							// the delivery currency
	* 	'deliveryOptions' 			=> array (
	* 		'cod' 		=> '1', 										// is it cash on delivery, can be 0 or 1
	* 		'insurance' => '0', 										// is there insurance on this orders packages, can be 0 or 1
	* 		'popId'		=> '12', 										// InPost's "paczkomat" id
	* 		'popName' 	=> 'WAW115', 									// InPost's "paczkomat" code name
	* 		'metroStationId' => 3										// information for ShopLogistics about metro station
	* 	),
	* 	'deliveryAddress' 			=> array (
	* 		'street' 		=> 'Plac Defilad 1', 						// delivery address street
	* 		'zipCode' 		=> '00-001', 								// delivery address street
	* 		'city' 			=> 'Warszawa', 								// delivery address city
	* 		'countryAlpha2Code' 	=> 'PL', 							// the country 2 letters
	* 		'firstName' 	=> 'Adam', 									// Recipient first name
	* 		'lastName' 		=> 'Kowalski', 								// Recipient last name
	* 		'phone' 		=> '600123123', 							// Recipient phone number
	* 		'email' 		=> 'adam.kowalski@polska.pl' 				// Recipient e-mail adders
	* 	)
	* )
	 * @param array $data
	 */
	public function createOrders ($data)
	{
		$dom = $this->createRequestDom('createOrderRequest', true);
		$root = $dom->documentElement;
		$orders_element = $dom->createElement('orders');	
		foreach ($data as $order)
		{
			if(!isset($order['deliveryAddress']['zipCode']) || empty($order['deliveryAddress']['zipCode']))
				$order['deliveryAddress']['zipCode'] = '000000';
			if(isset($order['orderValueCurrency']) && strtolower($order['orderValueCurrency']) == 'rur')
				$order['orderValueCurrency'] = 'RUB';
			if(isset($order['deliveryPriceCurrency']) && strtolower($order['deliveryPriceCurrency']) == 'rur')
				$order['deliveryPriceCurrency'] = 'RUB';
			if(isset($order['deliveryAddress']['city'])){
				if ($_city = $this->correctCity($order['deliveryAddress']['city']))
						$order['deliveryAddress']['city'] = $_city;
			}

			foreach ($order['orderItems'] as $key => $order_item)
			{
				if(isset($order_item['VOLUME'])){
				    $order['orderItems'][$key]['volume'] = $order['orderItems'][$key]['VOLUME'];
					unset($order['orderItems'][$key]['VOLUME']);
				}
				if(isset($order_item['WEIGHT'])){
					$order['orderItems'][$key]['weight'] = $order['orderItems'][$key]['WEIGHT'];
					unset($order['orderItems'][$key]['WEIGHT']);
				}
			}

			$order_element = $this->orderToXml($order);
			$orders_element->appendChild($order_element);
		}	
		$root->appendChild($orders_element);
		$xml = $dom->saveXML();
		$data = $this->toSimpleXMLElement( $this->send('createOrder', $xml) );
		$result = array();
		if (isset($data->orders) && isset($data->orders->order))
		{
			$keys = array(
				'errors' => 'errors',
				'externalOrderId' => 'externalOrderId',
				'orderId' => 'orderId'
			);
			foreach($data->orders->order as $t) {
				$result[] = $this->mapXML($t, $keys);
			}
			return $result;
		}
		throw new Exception('The response XML structure is wrong');
	}

	/**
	* Return the correct city
	* @param string $city
	* @return string
	*/
	private function correctCity($city)
	{
		$pattern = '/^(?:[A-Za-zА-Яа-я\-\s]*)|(?:[A-Za-zА-Яа-я\-\s]*)\s\(.*\)$/u';
		if (preg_match($pattern, $city, $m))
			return trim($m[0]);
		else
			return false;
	}
	
	/**
	 * Add's shipment by shop's order ID
	 * @param int $order
	 * @return Ambigous <multitype:, multitype:multitype:string  , mixed>
	 */
	public function addShipmentToOrder($order)
	{
		$orders = $this->addShipmentsToOrders(array(0 => $order));
		if (is_array($orders) && isset($orders[0])) {
			return $orders[0];
		}
		return $orders;
	}
	
	/**
	* Add's shipment by shop's order ID for orders in the list
	* list structure
	* array(
	* 	0 => 1,
	* 	1 => 2133,
	* 	2 => [..]
	* )
	* @param array $order
	* @return Ambigous <multitype:, multitype:multitype:string  , mixed>
	*/
	public function addShipmentsToOrders($order)
	{
		$result = array();
		$dom = $this->createRequestDom('addShipmentToOrderRequest', true);
		$root = $dom->documentElement;
		$element = $dom->createElement('orders');
		foreach ($order as $v)
		{
			$selement = $dom->createElement('order');
			$selement->appendChild($dom->createElement('externalOrderId', $v));
			$element->appendChild($selement);
		}
		$root->appendChild($element);
		$xml = $dom->saveXML();
		
		$d = $this->toSimpleXMLElement($this->send('addShipmentToOrder', $xml));
		if (isset($d->errors) || !isset($d->orders)) {
			throw new Exception('The response XML structure is wrong');
		}
		$keys = array(
			'externalOrderId' => 'externalOrderId',
			'shipmentEDTN' => 'shipmentEDTN'
		);
		foreach($d->orders->order as $t) {
			$result[] = $this->mapXML($t, $keys);
			if(!$this->isArrayValid($t, $keys)) {
				throw new Exception('The response XML structure is wrong pleas update Client');
			}
		}
		return $result;
	}
	
	/**
	 * Get's shipment's list
	 * the filter structure is:
	 * array(
	 * 	'status' => 'InPreparation', 	//Possible values (InPreparation, CreatingDocuments, InfoRejected, AwaitingManifest, CreatingManifestDocument, ManifestRejected, ReadyToSend, InTransit, ReturnToSender, Delivered, Cancelled, Claimed, ClaimProcessed, Exception)
	 * 	'refNumber' => '123',
	 * 	'createDate' => array (		`	// not need
	 * 		'startDate' => '2004-02-12T15:19:21+00:00',			// the date from in ISO 8601 format
	 * 		'endDate' => '2004-02-13T15:19:21+00:00'			// the date to in ISO 8601 format
	 * 	),
	 * 	'lastModificationDate' => array ( // not need
	 * 		'startDate' => '',									// the date from in ISO 8601 format
	 * 		'endDate' => ''										// the date to in ISO 8601 format
	 * 	)
	 * )
	 * @param int $page
	 * @param int $limit
	 * @param array $filter
	 * @return Ambigous <multitype:, multitype:multitype:string  , mixed>
	 */
	public function getShipmentsList($page = 0, $limit = 25, $filter = null)
	{
		$dom = $this->createRequestDom('getShipmentsListRequest', true);
		$root = $dom->documentElement;
		
		$element = $dom->createElement('page');
		$element->appendChild($dom->createElement('pageNumber', (int)$page));
		$element->appendChild($dom->createElement('pageSize', (int)$limit));
		
		$root->appendChild($element);
		
		if (is_array($filter))
		{
			$add_filter = false;
			$filter_element = $dom->createElement('filters');
			if (isset($filter['status']) && $filter['status'] != '')
			{
				$add_filter = true;
				$filter_element->appendChild($dom->createElement('status', $filter['status']));
			}
			if (isset($filter['refNumber']) && $filter['refNumber'] != '')
			{
				$add_filter = true;
				$filter_element->appendChild($dom->createElement('refNumber', $filter['refNumber']));
			}
			if (isset($filter['createDate']) && is_array($filter['createDate']) && !empty($filter['createDate']))
			{
				$add_filter_cd = false;
				$filter_element_cd = $dom->createElement('createDate');
				if (isset($filter['createDate']['startDate']) && $filter['createDate']['startDate'] != '')
				{
					$add_filter = true;
					$add_filter_cd = true;
					$filter['createDate']['startDate'] = date('c', strtotime($filter['createDate']['startDate']));
					$filter_element_cd->appendChild($dom->createElement('startDate', $filter['createDate']['startDate']));
				}
				if (isset($filter['createDate']['endDate']) && $filter['createDate']['endDate'] != '')
				{
					$add_filter = true;
					$add_filter_cd = true;
					$filter['createDate']['endDate'] = date('c', strtotime($filter['createDate']['endDate']));
					$filter_element_cd->appendChild($dom->createElement('endDate', $filter['createDate']['endDate']));
				}
				if ($add_filter_cd)
				{
					$filter_element->appendChild($filter_element_cd);
				}
			}
			if (isset($filter['lastModificationDate']) && is_array($filter['lastModificationDate']) && !empty($filter['lastModificationDate']))
			{
				$add_filter_cd = false;
				$filter_element_cd = $dom->createElement('lastModificationDate');
				if (isset($filter['lastModificationDate']['startDate']) && $filter['lastModificationDate']['startDate'] != '')
				{
					$add_filter = true;
					$add_filter_cd = true;
					$filter['lastModificationDate']['startDate'] = date('c', strtotime($filter['lastModificationDate']['startDate']));
					$filter_element_cd->appendChild($dom->createElement('startDate', $filter['lastModificationDate']['startDate']));
				}
				if (isset($filter['lastModificationDate']['endDate']) && $filter['lastModificationDate']['endDate'] != '')
				{
					$add_filter = true;
					$add_filter_cd = true;
					$filter['lastModificationDate']['endDate'] = date('c', strtotime($filter['lastModificationDate']['endDate']));
					$filter_element_cd->appendChild($dom->createElement('endDate', $filter['lastModificationDate']['endDate']));
				}
				if ($add_filter_cd)
				{
					$filter_element->appendChild($filter_element_cd);
				}
			}
			
			if ($add_filter)
			{
				$root->appendChild($filter_element);
			}
		}
		
		
		$xml = $dom->saveXML();
		$r = $this->toSimpleXMLElement( $this->send('getShipmentsList', $xml) );
		
		if(!isset($r->shipmentsFound)) {
			throw new Exception('There can be error on connection or withe account');
		}
		
		$result = array(
			'shipmentsFound' => (int)$r->shipmentsFound,
			'page' => $this->mapXML($r->page, array(
				'currentPageSize' => 'currentPageSize' , 'pageNumber' => 'pageNumber', 'pageSize' => 'pageSize'
			)),
			'shipments' => array()
		);
		
		$keys = array(
			'edtn' => 'edtn',
			'ctn' => 'ctn',
			'status' => 'status',
			'lastStatusChangeDate' => 'lastStatusChangeDate',
			'subStatus' => 'subStatus',
			'lastSubStatusChangeDate' => 'lastSubStatusChangeDate',
			'returnReason' => 'returnReason',
			'source' => 'source',
			'createDate' => 'createDate',
			'createdBy' => 'createdBy',
			'lastModificationDate' => 'lastModificationDate',
			'lastModifiedBy' => 'lastModifiedBy',
			'isLabelCreated' => 'isLabelCreated',
			'isManifestCreated' => 'isManifestCreated',
			'manifestId' => 'manifestId'
		);
		
		$createdBy_keys = array(
			'firstName' => 'firstName',
			'lastName' => 'lastName',
			'email' => 'email'
		);
		
		$n = 0;
		foreach($r->shipments->shipment as $t) {
			$result['shipments'][$n] = $this->mapXML($t, $keys);
			$result['shipments'][$n]['createdBy'] = $this->mapXML($t->createdBy, $createdBy_keys);
			$result['shipments'][$n]['lastModifiedBy'] = $this->mapXML($t->lastModifiedBy, $createdBy_keys);
			if(!$this->isArrayValid($result['shipments'][$n], $keys)) {
				throw new Exception('The response XML structure i invalid');
			}
			$n++;
		}
		return $result;
	}
	
	/**
	* Get's shipment details
	* @param int $edtn
	* @return Ambigous <multitype:, multitype:multitype:string  , mixed>
	*/
	public function getShipmentDetails($edtn)
	{
		$result = $this->getShipmentsDetails(array(0 => $edtn));
	
		return $result;
	}
	
	/**
	 * Get's shipment's details by list
	 * list structure
	 * array(
	 * 	0 => 1,
	 * 	1 => 2133,
	 * 	2 => [..]
	 * )
	 * @param array $edtn
	 * @return Ambigous <multitype:, multitype:multitype:string  , mixed>
	 */
	public function getShipmentsDetails($edtns)
	{
		$dom = $this->createRequestDom('getShipmentDetailsRequest', true);
		$root = $dom->documentElement;
		$element = $dom->createElement('shipments');
		foreach ($edtns as $edtn)
		{
			$element->appendChild($dom->createElement('shipmentEDTN', $edtn));
		}
		$root->appendChild($element);
		$xml = $dom->saveXML();
		$d = $this->toSimpleXMLElement($this->send('getShipmentDetails', $xml));
		if (isset($d->errors)) {
			throw new Exception('The response XML structure is wrong');
		}
		
		$keys = array(
			'edtn' => 'edtn',
			'ctn' => 'ctn',
			'status' => 'status',
			'lastStatusChangeDate' => 'lastStatusChangeDate',
			'subStatus' => 'subStatus',
			'lastSubStatusChangeDate' => 'lastSubStatusChangeDate',
			'returnReason' => 'returnReason',
			'source' => 'source',
			'createdDate' => 'createdDate',
			'createdBy' => 'createdBy',
			'lastModificationDate' => 'lastModificationDate',
			'lastModifiedBy' => 'lastModifiedBy',
			'isLabelCreated' => 'isLabelCreated',
			'isManifestCreated' => 'isManifestCreated',
			'manifestId' => 'manifestId',
			'sender' => 'sender',
			'returnAddress' => 'returnAddress',
			'recipient' => 'recipient',
			'payer' => 'payer',
			'carrierAccount' => 'carrierAccount',
			'carrier' => 'carrier',
			'owner' => 'owner',
			'noteForRecipient' => 'noteForRecipient',
			'noteForSender' => 'noteForSender',
			'senderNotificationEmail' => 'senderNotificationEmail',
			'senderNotificationSMS' => 'senderNotificationSMS',
			'recipientNotificationEmail' => 'recipientNotificationEmail',
			'recipientNotificationSMS' => 'recipientNotificationSMS',
			'packageType' => 'packageType',
			'weight' => 'weight',
			'description' => 'description',
			'refNumbers' => 'refNumbers',
			'service' => 'service',
			'packages' => 'packages'
		);
		
		$fle_keys = array('firstName' => 'firstName', 'lastName' => 'lastName', 'email' => 'email');
		
		$sender_keys = array(
			'contractorId' => 'contractorId',
			'isCompany' => 'isCompany',
			'companyName' => 'companyName',
			'taxId' => 'taxId',
			'firstName' => 'firstName',
			'lastName' => 'lastName',
			'street' => 'street',
			'homeNumber' => 'homeNumber',
			'zipCode' => 'zipCode',
			'city' => 'city',
			'countryCode' => 'countryCode',
			'contact' => 'contact'
		);
		
		$pakage_keys = array(
			'weight' => 'weight',
			'length' => 'length',
			'width' => 'width',
			'height' => 'height',
			'nonStandard' => 'nonStandard',
			'refNumbers' => 'refNumbers'
		);
		
		$result = array();
		$n = 0;
		foreach($d->shipments->shipment as $t) {
			$result[$n] = $this->mapXML($t, $keys);
			$result[$n]['createdBy'] = (isset($t->createdBy) ? $this->mapXML($t->createdBy, $fle_keys) : null);
			$result[$n]['lastModifiedBy'] = (isset($t->lastModifiedBy) ? $this->mapXML($t->lastModifiedBy, $fle_keys) : null);
			$result[$n]['sender'] = (isset($t->sender) ? $this->mapXML($t->sender, $sender_keys) : null);
			if (isset($result[$n]['sender']['contact'])) {
				$result[$n]['sender']['contact'] = (isset($t->sender->contact) ? $this->mapXML($t->sender->contact, $fle_keys) : null);
			}
			$result[$n]['returnAddress'] = (isset($t->returnAddress) ? $this->mapXML($t->returnAddress, $sender_keys) : null);
			if (isset($result[$n]['returnAddress']['contact'])) {
				$result[$n]['returnAddress']['contact'] = (isset($t->returnAddress->contact) ? $this->mapXML($t->returnAddress->contact, $fle_keys) : null);
			}
			$result[$n]['recipient'] = (isset($t->recipient) ? $this->mapXML($t->recipient, $sender_keys) : null);
			if (isset($result[$n]['recipient']['contact'])) {
				$result[$n]['recipient']['contact'] = (isset($t->recipient->contact) ? $this->mapXML($t->recipient->contact, $fle_keys) : null);
			}
			$result[$n]['payer'] = (isset($t->payer) ? $this->mapXML($t->payer, $sender_keys) : null);
			if (isset($result[$n]['payer']['contact'])) {
				$result[$n]['payer']['contact'] = (isset($t->payer->contact) ? $this->mapXML($t->payer->contact, $fle_keys) : null);
			}
			$result[$n]['owner'] = (isset($t->owner) ? $this->mapXML($t->owner, $fle_keys) : null);
			
			if(isset($t->packages) && isset($t->packages->package)) {
				if(!is_array($result[$n]['packages'])) {
					$result[$n]['packages'] = array();
				}
				foreach($t->packages->package as $p) {
					$result[$n]['packages'][] = $this->mapXML($p, $pakage_keys);
				}
			} else {
				$result[$n]['packages'] = array();
			}
		}
		return $result;
	}
	
    
    
	/**
	 * Helps in configuration validation
	 * @param string $key
	 * @return boolean
	 */
	public function sayHello($key)
	{
		$dom = $this->createRequestDom('sayHelloRequest', false);
		$root = $dom->documentElement;
		$element = $dom->createElement('authentication');
		$element->appendChild($dom->createElement('apiKey', $key));
		$root->appendChild($element);
		
		$element2 = $dom->createElement('message');
		$element2->appendChild($dom->createElement('messageText', 'HELLO'));
		$root->appendChild($element2);
		
		$xml = $dom->saveXML();

		$data = $this->toSimpleXMLElement($this->send('sayHello',$xml));
		if (isset($data->message) && isset($data->message->messageText) && $data->message->messageText == 'HELLO')
		{
			return true;
		}
		return false;
	}
    
    public function syncDynamicPricing($data,$settings=array(),$carriers=array()){
        
        $dom = $this->createRequestDom('checkoutPricingRequest', false);
	    $root = $dom->documentElement;
        $html = array();
		
	$authentication = $dom->createElement('authentication');
	$authentication->appendChild($dom->createElement('apiKey', $this->config['adminApiKey']));
	$root->appendChild($authentication);

        $orderDate = $dom->createElement('orderDate',date('c'));
        $root->appendChild($orderDate);
  
        $deliveryAddress = $dom->createElement('deliveryAddress');
    	$deliveryAddress->appendChild($dom->createElement('city', $this->correctCity(iconv(LANG_CHARSET,'utf-8', $settings['CITY_NAME']))));
        $deliveryAddress->appendChild($dom->createElement('zipCode', iconv(LANG_CHARSET,'utf-8', $settings['ZIP'])));
        $root->appendChild($deliveryAddress);
        
        
        $products = $dom->createElement('products');                        
        foreach($data as $arr){
            $product = $dom->createElement('product');                
            $arr['size'] = (!empty($arr['DIMENSIONS']['LENGTH']) ? $arr['DIMENSIONS']['LENGTH'] : null) . 'x' . (!empty($arr['DIMENSIONS']['LENGTH']) ? $arr['DIMENSIONS']['LENGTH'] : null) . 'x' . (!empty($arr['DIMENSIONS']['LENGTH']) ? $arr['DIMENSIONS']['LENGTH'] : null);                        
            $attribute = $this->arrayToXMLAttribute($arr,$dom,$product);		
            $products->appendChild($product);
        }
     
    
    
	$root->appendChild($products);
	$xml = $dom->saveXML();
	$data = $this->toSimpleXMLElement($this->send('CheckoutPricing',$xml));    
        if((isset($data->errors) && !empty($data->errors)) || (isset($data->error) && !empty($data->error)) || (isset($data['errors'])&&!empty($data['errors'])) ){
            return null;
        }else{              
            foreach($data->deliveryMethods as $methods){
                foreach($methods as $method){   
                    $html[] = (array)$method;
                }
            }
        }
        return $html;
    }
}