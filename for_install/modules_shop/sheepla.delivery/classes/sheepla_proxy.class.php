<?php

/**
* Sheepla proxy class is a class made for sending orders from the shop in to the sheepla application
* and to support basic configuration screens of the shop
* @author Orba (biuro{at}orba.pl)
* @requirements:
* 	- stream_context_create() 	(http://php.net/manual/en/function.stream-context-create.php) \
* 	- file_get_contents()  		(http://php.net/manual/en/function.file-get-contents.php)
*  - DOMDocument 				(http://php.net/manual/en/class.domdocument.php)
*  - simplexml_load_string 	(http://php.net/manual/en/function.simplexml-load-string.php)
*  - json_encode 				(http://php.net/manual/en/function.json-encode.php)
*  - json_decode 				(http://php.net/manual/en/function.json-decode.php)
*/
class SheeplaProxy
{
	/**
	* If this variable is set to true proxy displays all important information to screen
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
	* Variable for ISheeplaProxyDataModel instance
	* @var ISheeplaProxyDataModel
	*/
	protected $model = null;
	
	/**
	 * Variable for SheeplaClient instance
	 * @var SheeplaClient 
	 */
	protected $client = null;
	
	/**
	 * Class constructor
	 * @param array $config
	 * @param object $model instance of ISheeplaProxyDataModel
	 * @param object $client instance of SheeplaClient
	 * @param bool $debug
	 */
	public function __construct($config = null, $model = null, $client = null, $debug = false)
	{
		$this->setDebug($debug);
		if (!is_null($config))
		{
			$this->setConfig($config);
		}
		if (!is_null($model))
		{
			$this->setModel($model);
		}
		if (!is_null($client))
		{
			$this->setClient($client);
		}
	}
	
	/**
	* If object is in debug mode it returns all debug notifications to standard output
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
	 * Checks is given configuration correct
	 * @param array $config
	 * @return boolean
	 */
	protected function isValidConfig($config)
	{
		if (is_array($config) && isset($config['apiUrl']) && !empty($config['apiUrl']) && isset($config['adminApiKey']) && !empty($config['adminApiKey']))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Sets debug mode
	 * @param boolean $value
	 * @return booleam returns actual debug mode
	 */
	public function setDebug($value)
	{
		$this->debugFlag = (bool)$value;
		$this->debug('Debug mode ON');
		if (!is_null($this->client))
		{
			$this->client->setDebug($value);
		}
		return $this->debugFlag;
	}
	
	/**
	 * Sets client
	 * @param SheeplaClient $value SheeplaClient instance
	 * @throws Exception
	 */
	public function setClient($value)
	{
		if($value instanceof SheeplaClient)
		{
			$this->client = $value;
			$this->client->setDebug($this->debugFlag);
			if (!is_null($this->config))
			{
				$this->client->setConfig($this->config);
			}
		}
		else
		{
			throw new Exception("Client must be an SheeplaClient class instance");
		}
	}
	
	/**
	 * Returns SheeplaClient instance
	 * @return SheeplaClient
	 */
	public function getClient()
	{
		return $this->client;
	}
	
	/**
	 * Sets model
	 * @param object $value ISheeplaProxyDataModel instance
	 * @throws Exception
	 */
	public function setModel($value)
	{
		if($value instanceof ISheeplaProxyDataModel)
		{
			$this->model = $value;
			$this->setConfig($this->model->getConfig());
		} 
		else 
		{
			throw new Exception("Model must implement ISheeplaProxyDataModel");
		}
	}
	
	/**
	 * Sets configuration
	 * @param array $value
	 * @return booleand
	 */
	public function setConfig($value)
	{
	   
		if ($this->isValidConfig($value))
		{
			$this->config = $value;
			$this->debug('Config from array:', $value);
			
			$rc = false;
			
			if (!is_null($this->client))
			{
				$rc = $this->client->setConfig($value);
			}
			return (true && $rc);
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
	 * Initializes sync orders whit shop and sheepla
	 * @throws Exception
	 * @return array
	 */
	public function syncOrders()
	{
		$response = array();
		if (is_null($this->client) || is_null($this->config) || is_null($this->model))
		{
			throw new Exception("Not all required values are set: " . (is_null($this->client) ? 'pleas use setClient method ' : ''). (is_null($this->model) ? 'pleas use setModel method ' : ''). (is_null($this->config) ? 'pleas use setConfig method ' : ''));
		}
		$orders = $this->model->getOrders();
		$this->debug('Orders data to be synced: ', $orders);
		
		if (!empty($orders))
		{
			$re = $this->client->createOrders($orders);
			if (isset($re['errors']))
			{
				throw new Exception(print_r($re,true));
			}
			
			if (is_array($re))
			{
				foreach ($re as $or)
				{
					if (isset($or['errors']) && !empty($or['errors']))
					{
						$response[] = array(
							'orderId' =>$or['externalOrderId'],
							'sheeplaOrderId' => 0,
							'status' => 'error',
							'errors' => $or['errors']
						);
					}
					else
					{
						$response[] = array(
							'orderId' => $or['externalOrderId'],
							'sheeplaOrderId' => $or['orderId'],
							'status' => 'ok',
							'errors' => null
						);
					}
				}
			}
			else
			{
				throw new Exception('Invalid response from sheepla' . print_r($re, true));
			}
		}
		else
		{
			$this->debug('No orders to be synced');
		}
		return $response;
	}
	
	protected function isValidPaymentMethod($pm)
	{
		return true;
	}
	
	protected function isValidShippingMethod($pm)
	{
		return true;
	}
	
	public function syncShippingMethods()
	{
		;
	}
	
	public function syncPaymentMethods()
	{
		;
	}
	
	public function proccessCmd()
	{
		if (isset($_REQUEST) && isset($_REQUEST['cmd'])) {
            if (is_null($this->client)){
						throw new Exception('No client set');
			}
			if(is_null($this->model)){
                throw new Exception('No model set');
            }			
                    
			switch ($_REQUEST['cmd'])
			{                
				case 'Hello':					
                    if (!isset($_GET['key'])){
                        throw new Exception('Key given by sheepla');
                    }
					$resp = $this->client->sayHello($_GET['key']);
					if ($resp)
					{
						if (@ob_start())
						{
							@ob_end_clean();
						}
						die('HELLO;'.$this->model->getCountAllOrders().';'.$_GET['key']);
					}
				break;               
			}
		}
	}
}
