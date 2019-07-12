<?php
/**
 * ������ ������ � Loginza API (http://loginza.ru/api-overview).
 * 
 * ������ ����� - ��� ������� ������, ������� ����� ������������ ��� ����, 
 * � ��� �� ������������ � ����������� ���� ��� ��������� ������� ������ ��� ���� ������.
 * 
 * ��������� PHP 5, � ��� �� CURL ��� ���������� ������ c ��������� http:// ��� file_get_contents.
 * 
 * @link http://loginza.ru/api-overview
 * @author Sergey Arsenichev, PRO-Technologies Ltd.
 * @version 1.0
 */
class LoginzaAPI {
	/**
	 * ������ ������
	 *
	 */
	const VERSION = '1.0';
	/**
	 * URL ��� �������������� � API loginza
	 *
	 */
	const API_URL = 'http://loginza.ru/api/%method%';
	/**
	 * URL ������� Loginza
	 *
	 */
	const WIDGET_URL = 'https://loginza.ru/api/widget';
	
	/**
	 * �������� ���������� ������� ��������������� ������������
	 *
	 * @param string $token ����� ���� ��������������� ������������
	 * @return mixed
	 */
public function getAuthInfo ($token, $id, $sig) {
	$url_parametrs['token'] = $token;
	$current_mode = COption::GetOptionString('infospice.loginza', 'mode', '');
	if($current_mode == "Y"){
		$url_parametrs['id'] = $id;
		$url_parametrs['sig'] = $sig;	
	}
	return $this->apiRequert('authinfo', $url_parametrs);	
}
	
	/**
	 * �������� ����� ������ ������� Loginza
	 *
	 * @param string $return_url ������ ��������, ���� ����� ��������� ������������ ����� �����������
	 * @param string $provider ��������� �� ��������� �� ������: google, yandex, mailru, vkontakte, facebook, twitter, loginza, myopenid, webmoney, rambler, mailruapi:, flickr, verisign, aol
	 * @param string $overlay ��� ����������� �������: true, wp_plugin, loginza
	 * @return string
	 */
	public function getWidgetUrl ($return_url=null, $provider=null, $overlay='') {
		$params = array();
		
		if (!$return_url) {
			$params['token_url'] = $this->currentUrl();
		} else {
			$params['token_url'] = $return_url;
		}
		
		if ($provider) {
			$params['provider'] = $provider;
		}
		
		if ($overlay) {
			$params['overlay'] = $overlay;
		}
		
		return self::WIDGET_URL.'?'.http_build_query($params);
	}
	
	/**
	 * ���������� ������ �� ������� ��������
	 *
	 * @return string
	 */
	private function currentUrl () {
		$url = array();
		// �������� https
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
			$url['sheme'] = "https";
			$url['port'] = '443';
		} else {
			$url['sheme'] = 'http';
			$url['port'] = '80';
		}
		// ����
		$url['host'] = $_SERVER['HTTP_HOST'];
		// ���� �� ����������� ����
		if (strpos($url['host'], ':') === false && $_SERVER['SERVER_PORT'] != $url['port']) {
			$url['host'] .= ':'.$_SERVER['SERVER_PORT'];
		}
		// ������ �������
		if (isset($_SERVER['REQUEST_URI'])) {
			$url['request'] = $_SERVER['REQUEST_URI'];
		} else {
			$url['request'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
			$query = $_SERVER['QUERY_STRING'];
			if (isset($query)) {
			  $url['request'] .= '?'.$query;
			}
		}
		
		return $url['sheme'].'://'.$url['host'].$url['request'];
	}
	
	/**
	 * ������ ������ �� API loginza
	 *
	 * @param string $method
	 * @param array $params
	 * @return string
	 */
	private function apiRequert($method, $params) {
		// url ������
		$url = str_replace('%method%', $method, self::API_URL).'?'.http_build_query($params);
		
		if ( function_exists('curl_init') ) {
			$curl = curl_init($url);
			$user_agent = 'LoginzaAPI'.self::VERSION.'/php'.phpversion();
			
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$raw_data = curl_exec($curl);
			curl_close($curl);
			$responce = $raw_data;
		} else {
			$responce = file_get_contents($url);
		}
		
		// ��������� JSON ������ API
		return $this->decodeJSON($responce);
	}
	
	/**
	 * ������ JSON ������
	 *
	 * @param string $data
	 * @return object
	 */
	private function decodeJSON ($data) {
		if ( function_exists('json_decode') ) {
			return json_decode ($data);
		}
		
		// ��������� ���������� ������ � JSON ���� ��� ����������
		if (!class_exists('Services_JSON')) {
			require_once( dirname( __FILE__ ) . '/JSON.php' );
		}
		
		$json = new Services_JSON();	
		return $json->decode($data);
	}
	
	public function debugPrint ($responceData, $recursive=false) {
		if (!$recursive){
			echo "<h3>Debug print:</h3>";
		}
		echo "<table border>";
		foreach ($responceData as $key => $value) {
			if (!is_array($value) && !is_object($value)) {
				echo "<tr><td>$key</td> <td><b>$value</b></td></tr>";
			} else {
				echo "<tr><td>$key</td> <td>";
				$this->debugPrint($value, true);
				echo "</td></tr>";
			}
		}
		echo "</table>";
	}
}

?>