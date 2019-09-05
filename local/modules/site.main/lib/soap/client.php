<?
/**
 *  module
 * 
 * @category	
 * @package		Soap
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Site\Main\Soap;

use Site\Main as Main;

/**
 * Абстрактный soap-клиент
 * 
 * @category	
 * @package		Soap
 */
class Client extends \SoapClient
{
	/**
	 * Singleton экземпляры
	 *
	 * @var array
	 */
	protected static $instances = array();
	
	/**
	* WSDL документ клиента
	*
	* @var string
	*/
	protected $wsdl = '';
	
	/**
	* Параметры SOAP клиента
	*
	* @var array
	*/
	protected $wsdlParams = array();

	/**
	* Конструктор
	*
	* @param string $wsdl URI WSDL
	* @param array $options массив параметров клиента
	* @return void
	*/
	public function __construct($wsdl, $options = '')
	{
		if (strlen($wsdl) <= 0) {
			$arParams = $this->getWsdlParams();
			$wsdl = $arParams['WSDL'];
			$options = $arParams['WSDL_PARAMS'];
			$this->wsdl = $wsdl;
			$this->wsdlParams = $options;
		} elseif (!is_array($options)) {
			$arParams = $this->getWsdlParams($wsdl);
			$wsdl = $arParams['WSDL'];
			$options = $arParams['WSDL_PARAMS'];
			$this->wsdl = $wsdl;
			$this->wsdlParams = $options;
		} else {
			$wsdl = $this->wsdl;
			$options = $this->wsdlParams;
		}
		
		try {
			parent::__construct($wsdl, $options);
		} catch (\Exception $e) {
			Main\Util::log(array(
				'Exception' => $e,
				'Client' => $this,
			));
		}
	}
	
	/**
	 * Возвращает экземпляр клиента по его коду
	 * Симбиоз шаблонов Singleton + Factory
	 *
	 * @param string $code Код клиента
	 * @return Client
	 */
	public static function getFactory($code = '')
	{
		if (array_key_exists($code, self::$instances)) {
			return self::$instances[$code];
		}
		
		if (!self::$instances) {
			ini_set('soap.wsdl_cache_enabled', ('Y' == \COption::GetOptionString('site.main', 'site_soapclient_wsdl_cache_enabled', 'N') ? 1 : 0));
			ini_set('soap.wsdl_cache', intval(\COption::GetOptionInt('site.main', 'site_soapclient_wsdl_cache', 2)));
			ini_set('soap.wsdl_cache_ttl', intval(\COption::GetOptionInt('site.main', 'site_soapclient_wsdl_cache_ttl', 86400)));
			ini_set('soap.wsdl_cache_dir', \COption::GetOptionString('site.main', 'site_soapclient_wsdl_cache_dir', '/tmp'));
			ini_set('soap.wsdl_cache_limit', intval(\COption::GetOptionInt('site.main', 'site_soapclient_wsdl_cache_limit', 10)));
			ini_set('default_socket_timeout', intval(\COption::GetOptionInt('site.main', 'site_soapclient_socket_timeout', 60)));
		}
		
		$className = __CLASS__ . ucfirst($code);
		
		if (class_exists($className)) {
			self::$instances[$code] = new $className();
			return self::$instances[$code];
		} else {
			$className = __CLASS__;
			self::$instances[$code] = new $className($code);
			return self::$instances[$code];
		}
		
		return false;
	}
	
	/**
	* Вызов методов сервисов
	*
	* @param string $function_name наименование метода
	* @param mixed $arguments параметры метода
	* @return mixed
	*/
	public function __call($function_name, $arguments)
	{
		$result = null;
		try {
			$result = parent::__call($function_name, $arguments);
		} catch(\Exception $e) {
			Main\Util::log(array(
				'Exception' => $e,
				'Client' => $this,
				'function_name' => $function_name,
				'arguments' => $arguments, 
			));
		}
		
		return $result;
	}
	
	/**
	* Получение настроек клиента
	*
	* @param string $code код клиента
	* @return array
	*/
	public function getWsdlParams($code)
	{
		$code = trim($code);
		if (strlen($code) <= 0) {
			return array(
				'WSDL'=> $this->wsdl,
				'WSDL_PARAMS' => $this->wsdlParams,
			);
		}
		
		$optstr = \COption::GetOptionString('site.main', 'site_soapclients[' . $code . ']');
		$arOpt = unserialize($optstr);
		$arResult = array(
			'WSDL' => $arOpt['wsdl'],
			'WSDL_PARAMS' => array(),
		);
		if (is_array($arOpt['params'])) {
			foreach ($arOpt['params'] as $k => $v) {
				$k = trim($k);
				if (strlen($k) <= 0) {
					continue;
				}
				if (!is_array($v)) {
					$v = trim($v);
					if (strlen($v) <= 0) {
						continue;
					}
					if (in_array($k, array('trace', 'features', 'cache_wsdl', 'connection_timeout'))) {
						$v = intval($v);
					} elseif (in_array($k, array('keep_alive'))) {
						$v = intval($v) > 0;
					} elseif(in_array($k, array('local_cert'))) {
						if (file_exists($v) && is_file($v)) {
							try {
								$v = @file_get_contents($v);
							} catch(Exception $e) {
							}
							if (strlen($v) <= 0) {
								continue;
							}
						}
					}
					$arResult['WSDL_PARAMS'][$k] = $v;
				}
			}
		}
		
		return $arResult;
	}
}